<?php
declare(strict_types=1);

namespace App;

use App\Enums\CardEnum;
use App\Enums\CombinationEnum;

class Result
{
    private $player;

    private $cards;

    public function __construct(array $player)
    {
        $this->player = $player;
        $this->cards = array_merge($player['board_cards'], $player['player_cards']);
    }

    public function show(): void
    {
        echo $this->player['player_name']
            . ' '
            . CombinationEnum::LIST[$this->player['combination']]
            . ' '
            . $this->getCardsList()
            . PHP_EOL;
    }

    private function getCardsList(): string
    {
        switch ($this->player['combination']) {
            case CombinationEnum::ONE_PAIR:
                return $this->getOnePairList();
                break;
            case CombinationEnum::TWO_PAIR:
                return $this->getTwoPairList();
                break;
            case CombinationEnum::THREE_OF_KIND:
                return $this->getThreeOfKind();
                break;
            case CombinationEnum::STRAIGHT:
                return $this->getStraightList();
                break;
            case CombinationEnum::FLUSH:
                return $this->getFlushList();
                break;
            case CombinationEnum::FULL_HOUSE:
                return $this->getFullHouseList();
                break;
            case CombinationEnum::FOUR_OF_KIND:
                return $this->getFourOfKindList();
                break;
            case CombinationEnum::STRAIGHT_FLUSH:
                return $this->getStraightFlushList();
                break;
            case CombinationEnum::ROYAL_FLUSH:
                return $this->getFlushList();
                break;
            default:
                return $this->getKickerList();
                break;
        }
    }

    private function getKickerList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            $resultAllCards[] = $name;
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            $resultPlayerCards[] = $name;
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getOnePairList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['pair_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['pair_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getTwoPairList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['low_pair_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['low_pair_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['high_pair_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['high_pair_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getThreeOfKind(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['set_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['set_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getStraightList(): string
    {
        $usedCards = [];
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            foreach ($this->player['straight'] as $item) {
                if ($card['value'] === $item && !in_array($item, $usedCards, true)) {
                    $resultAllCards[] = $name;
                    $usedCards[] = $item;
                }
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            foreach ($this->player['straight'] as $item) {
                if ($card['value'] === $item && in_array($item, $usedCards, true)) {
                    $resultPlayerCards[] = $name;
                    $usedCards[] = $item;
                }
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getFlushList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            if ($card['suit'] === $this->player['flush_suit']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['suit'] === $this->player['flush_suit']) {
                $resultPlayerCards[] = $name;
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getFullHouseList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['set_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['set_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['pair_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['pair_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getFourOfKindList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            if ($card['value'] === $this->player['four_of_kind_rank']) {
                $resultAllCards[] = $name;
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            if ($card['value'] === $this->player['four_of_kind_rank']) {
                $resultPlayerCards[] = $name;
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function getStraightFlushList(): string
    {
        $resultAllCards = [];
        $resultPlayerCards = [];

        foreach ($this->cards as $name => $card) {
            foreach ($this->player['straight'] as $item) {
                if ($card['value'] === $item && $card['suit'] === $this->player['flush_suit']) {
                    $resultAllCards[] = $name;
                }
            }
        }

        foreach ($this->player['player_cards'] as $name => $card) {
            foreach ($this->player['straight'] as $item) {
                if ($card['value'] === $item && $card['suit'] === $this->player['flush_suit']) {
                    $resultPlayerCards[] = $name;
                }
            }
        }

        return $this->sortCards($resultAllCards, $resultPlayerCards);
    }

    private function sortCards(array $resultAllCards, array $resultPlayerCards): string
    {
        $resultAllCardsFull = [];
        $resultPlayerCardsFull = [];

        foreach ($resultAllCards as $card) {
            $resultAllCardsFull[$card] = CardEnum::LIST[$card];
        }

        foreach ($resultPlayerCards as $card) {
            $resultPlayerCardsFull[$card] = CardEnum::LIST[$card];
        }

        $resultAllCardsFull = $this->sortArray($resultAllCardsFull);
        $resultPlayerCardsFull = $this->sortArray($resultPlayerCardsFull);

        return '{' . implode(' ', array_keys($resultAllCardsFull)) . '} {' . implode(' ',
                array_keys($resultPlayerCardsFull)) . '}';
    }

    private function sortArray(array $cards): array
    {
        $value = array_column($cards, 'value');
        $suit = array_column($cards, 'suit');

        array_multisort($value, SORT_ASC, $suit, SORT_ASC, $cards);

        if (count($cards) > 5) {
            $cards = array_slice($cards, 0, 5, true);
        }

        return $cards;
    }
}
