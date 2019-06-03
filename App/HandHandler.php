<?php
declare(strict_types=1);

namespace App;

use App\Enums\CombinationEnum;
use App\Enums\CardValueEnum;

class HandHandler
{
    private $cards;

    private $kicker;

    private $counts = [];

    private $pairs = [];

    private $sets = [];

    private $highEnd;

    private $cardValues = [];

    private $flushSuit;

    private $fourOfKindValue;

    private $playerCards;

    private $boardCards;

    private $playerName;

    private $straightValues;

    private $straightHigEnd;

    private $flushHighEnd;

    public function __construct(string $playerName, array $playerCards, array $boardCards)
    {
        $this->cards = array_merge($playerCards, $boardCards);
        $this->playerCards = $playerCards;
        $this->boardCards = $boardCards;
        $this->playerName = $playerName;
        $this->sortCards();
        $this->setKicker();
        $this->getCountOfSuitsAndValues();
        $this->setPairs();
        $this->setSets();
        $this->setCardValues();
    }

    public function getCombination(): array
    {
        $combination = [];

        if ($this->isHighCard()) {
            $combination = [
                'combination' => CombinationEnum::HIGH_CARD,
                'kicker' => $this->kicker['value'],
            ];
        }

        if ($this->isOnePair()) {
            $combination = [
                'combination' => CombinationEnum::ONE_PAIR,
                'pair_rank' => $this->pairs[0],
                'kicker' => $this->kicker['value'],
            ];
        }

        if ($this->isTwoPair()) {
            $combination = [
                'combination' => CombinationEnum::TWO_PAIR,
                'high_pair_rank' => $this->pairs[0],
                'low_pair_rank' => $this->pairs[1],
                'kicker' => $this->kicker['value'],
            ];
        }

        if ($this->isThreeOfKind()) {
            $combination = [
                'combination' => CombinationEnum::THREE_OF_KIND,
                'set_rank' => $this->sets[0],
                'kicker' => $this->kicker['value'],
            ];
        }

        if ($this->isStraight()) {
            $combination = [
                'combination' => CombinationEnum::STRAIGHT,
                'high_end' => $this->highEnd,
                'straight' => $this->straightValues,
            ];
        }

        if ($this->isFlush()) {
            $combination = [
                'combination' => CombinationEnum::FLUSH,
                'flush_suit' => $this->flushSuit,
                'high_end' => $this->highEnd,
            ];
        }

        if ($this->isFullHouse()) {
            $combination = [
                'combination' => CombinationEnum::FULL_HOUSE,
                'set_rank' => $this->sets[0],
                'pair_rank' => $this->pairs[0],
                'kicker' => $this->kicker['value'],
            ];
        }

        if ($this->isFourOfKind()) {
            $combination = [
                'combination' => CombinationEnum::FOUR_OF_KIND,
                'four_of_kind_rank' => $this->fourOfKindValue,
                'kicker' => $this->kicker['value'],
            ];
        }

        if ($this->isStraightFlush()) {
            $combination = [
                'combination' => CombinationEnum::STRAIGHT_FLUSH,
                'flush_suit' => $this->flushSuit,
                'high_end' => $this->highEnd,
                'straight' => $this->straightValues,
            ];
        }

        if ($this->isRoyalFlush()) {
            $combination = [
                'combination' => CombinationEnum::ROYAL_FLUSH,
                'flush_suit' => $this->flushSuit,
            ];
        }

        $combination['player_name'] = $this->playerName;
        $combination['player_cards'] = $this->playerCards;
        $combination['board_cards'] = $this->boardCards;

        return $combination;
    }

    private function sortCards(): void
    {
        usort($this->cards, static function ($a, $b) {
            return ($a['value'] - $b['value']);
        });
    }

    private function getCountOfSuitsAndValues(): void
    {
        foreach ($this->cards as $card) {
            if (isset($this->counts['suits'][$card['suit']])) {
                $this->counts['suits'][$card['suit']]++;
            } else {
                $this->counts['suits'][$card['suit']] = 1;
            }

            if (isset($this->counts['values'][$card['value']])) {
                $this->counts['values'][$card['value']]++;
            } else {
                $this->counts['values'][$card['value']] = 1;
            }
        }
    }

    private function isHighCard(): bool
    {
        return true;
    }

    private function setKicker(): void
    {
        $this->kicker = end($this->cards);
    }

    private function isOnePair(): bool
    {
        return count($this->pairs) === 1;
    }

    private function isTwoPair(): bool
    {
        return count($this->pairs) >= 2;
    }

    private function setPairs(): void
    {
        foreach ($this->counts['values'] as $value => $count) {
            if ($count === 2) {
                $this->pairs[] = $value;
            }
        }

        rsort($this->pairs);
    }

    private function setSets(): void
    {
        foreach ($this->counts['values'] as $value => $count) {
            if ($count === 3) {
                $this->sets[] = $value;
            }
        }

        rsort($this->sets);
    }

    private function isThreeOfKind(): bool
    {
        return count($this->sets) > 0;
    }

    private function isStraight(): bool
    {
        $count = count($this->cardValues);

        if ($count < 5) {
            return false;
        }

        if ($count === 5) {
            if ($this->findStraight($this->cardValues)) {
                $this->straightValues = $this->cardValues;
            } else {
                return false;
            }
        }

        if ($count > 5) {
            $straight = [];
            $lowerStraight = array_values(array_slice($this->cardValues, 0, 5, true));
            $midStraight = array_values(array_slice($this->cardValues, 1, 5, true));

            if ($this->findStraight($lowerStraight)) {
                $straight = $lowerStraight;
            }

            if ($this->findStraight($midStraight)) {
                $straight = $midStraight;
            }

            if ($count === 7) {
                $highStraight = array_values(array_slice($this->cardValues, 2, 5, true));
                if ($this->findStraight($highStraight)) {
                    $straight = $highStraight;
                }
            }

            if (empty($straight)) {
                return false;
            }

            $this->straightValues = $straight;
        }

        $this->highEnd = $this->straightHigEnd = end($this->straightValues);

        return true;
    }

    private function findStraight(array $values): bool
    {
        for ($i = 1; $i < 5; $i++) {
            if ($values[$i] - $values[$i - 1] !== 1) {
                return false;
            }
        }

        return true;
    }

    private function setCardValues(): void
    {
        foreach ($this->cards as $card) {
            $this->cardValues[] = $card['value'];
        }

        $this->cardValues = array_values(array_unique($this->cardValues, SORT_NUMERIC));
    }

    private function isFlush(): bool
    {
        foreach ($this->counts['suits'] as $suit => $count) {
            if ($count >= 5) {
                $this->flushSuit = $suit;
                $this->highEnd = $this->flushHighEnd = $this->getHighValueOfSuit($suit);
                return true;
            }
        }

        return false;
    }

    private function getHighValueOfSuit(string $suit): int
    {
        $values = [];

        foreach ($this->cards as $card) {
            if ($card['suit'] === $suit) {
                $values[] = $card['value'];
            }
        }

        rsort($values);

        return $values[0];
    }

    private function isFullHouse(): bool
    {
        return $this->isThreeOfKind() && $this->isOnePair();
    }

    private function isFourOfKind(): bool
    {
        foreach ($this->counts['values'] as $value => $count) {
            if ($count === 4) {
                $this->fourOfKindValue = $value;
                return true;
            }
        }

        return false;
    }

    private function isStraightFlush(): bool
    {
        $count = 0;

        if ($this->isStraight() && $this->isFlush()) {
            foreach ($this->cards as $card) {
                foreach ($this->straightValues as $value) {
                    if ($card['value'] === $value && $card['suit'] === $this->flushSuit) {
                        $count++;
                    }
                }
            }
        }

        return $count === 5;
    }

    private function isRoyalFlush(): bool
    {
        return $this->isStraightFlush() && $this->flushHighEnd === CardValueEnum::ACE && $this->straightHigEnd === CardValueEnum::ACE;
    }
}
