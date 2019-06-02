<?php
declare(strict_types=1);

namespace App;

use App\Enums\CombinationEnum;

class Judge
{
    private $players;

    public function __construct(array $players)
    {
        $this->players = $players;
        $this->calculatePriority();
    }

    public function getPriority(): array
    {
        return $this->players;
    }

    private function calculatePriority(): void
    {
        $context = $this;

        uasort($this->players, static function (array $a, array $b) use ($context) {
            if ($a['combination'] === $b['combination']) {
                switch ($a['combination']) {
                    case CombinationEnum::HIGH_CARD:
                        return $context->getPriorityByKicker($a, $b);
                        break;
                    case CombinationEnum::ONE_PAIR:
                        return $context->getPriorityInOnePair($a, $b);
                        break;
                    case CombinationEnum::TWO_PAIR:
                        return $context->getPriorityInTwoPair($a, $b);
                        break;
                    case CombinationEnum::THREE_OF_KIND:
                        return $context->getPriorityInThreeOfKind($a, $b);
                        break;
                    case CombinationEnum::STRAIGHT:
                        return $context->getPriorityByHighEnd($a, $b);
                        break;
                    case CombinationEnum::FLUSH:
                        return $context->getPriorityByHighEnd($a, $b);
                        break;
                    case CombinationEnum::FULL_HOUSE:
                        return $context->getPriorityInFullHouse($a, $b);
                        break;
                    case CombinationEnum::FOUR_OF_KIND:
                        return $context->getPriorityInFourOfKind($a, $b);
                        break;
                    case CombinationEnum::STRAIGHT_FLUSH:
                        return $context->getPriorityByHighEnd($a, $b);
                        break;
                    case CombinationEnum::ROYAL_FLUSH:
                        return $context->getPriorityByPlayerName($a, $b);
                        break;
                }
            }

            return ($a['combination'] > $b['combination']) ? -1 : 1;
        });
    }

    private function getPriorityByKicker(array $a, array $b): int
    {
        if ($a['kicker'] !== $b['kicker']) {
            return ($a['kicker'] > $b['kicker']) ? -1 : 1;
        }

        return $this->getPriorityByPlayerName($a, $b);
    }

    private function getPriorityInOnePair(array $a, array $b): int
    {
        if ($a['pair_rank'] !== $b['pair_rank']) {
            return ($a['pair_rank'] > $b['pair_rank']) ? -1 : 1;
        }

        return $this->getPriorityByKicker($a, $b);
    }

    private function getPriorityInTwoPair(array $a, array $b): int
    {
        if ($a['high_rank'] !== $b['high_rank']) {
            return ($a['high_rank'] > $b['high_rank']) ? -1 : 1;
        }

        if ($a['low_rank'] !== $b['low_rank']) {
            return ($a['low_rank'] > $b['low_rank']) ? -1 : 1;
        }

        return $this->getPriorityByKicker($a, $b);
    }

    private function getPriorityInThreeOfKind(array $a, array $b): int
    {
        if ($a['set_rank'] !== $b['set_rank']) {
            return ($a['set_rank'] > $b['set_rank']) ? -1 : 1;
        }

        return $this->getPriorityByKicker($a, $b);
    }

    private function getPriorityByHighEnd(array $a, array $b): int
    {
        if ($a['high_end'] !== $b['high_end']) {
            return ($a['high_end'] > $b['high_end']) ? -1 : 1;
        }

        return $this->getPriorityByPlayerName($a, $b);
    }

    private function getPriorityInFullHouse(array $a, array $b): int
    {
        if ($a['set_rank'] !== $b['set_rank']) {
            return ($a['set_rank'] > $b['set_rank']) ? -1 : 1;
        }

        if ($a['pair_rank'] !== $b['pair_rank']) {
            return ($a['pair_rank'] > $b['pair_rank']) ? -1 : 1;
        }

        return $this->getPriorityByKicker($a, $b);
    }

    private function getPriorityInFourOfKind(array $a, array $b): int
    {
        if ($a['four_of_kind_rank'] !== $b['four_of_kind_rank']) {
            return ($a['four_of_kind_rank'] > $b['four_of_kind_rank']) ? -1 : 1;
        }

        return $this->getPriorityByKicker($a, $b);
    }

    private function getPriorityByPlayerName(array $a, array $b): int
    {
        return strnatcasecmp($a['player_name'], $b['player_name']);
    }
}
