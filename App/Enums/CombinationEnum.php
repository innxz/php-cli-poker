<?php
declare(strict_types=1);

namespace App\Enums;

class CombinationEnum
{
    public const ROYAL_FLUSH = 9;

    public const STRAIGHT_FLUSH = 8;

    public const FOUR_OF_KIND = 7;

    public const FULL_HOUSE = 6;

    public const FLUSH = 5;

    public const STRAIGHT = 4;

    public const THREE_OF_KIND = 3;

    public const TWO_PAIR = 2;

    public const ONE_PAIR = 1;

    public const HIGH_CARD = 0;

    public const LIST = [
        self::HIGH_CARD => 'High card',
        self::ONE_PAIR => 'One pair',
        self::TWO_PAIR => 'Two pair',
        self::THREE_OF_KIND => 'Three of a kind',
        self::STRAIGHT => 'Straight',
        self::FLUSH => 'Flush',
        self::FULL_HOUSE => 'Full house',
        self::FOUR_OF_KIND => 'Four of a kind',
        self::STRAIGHT_FLUSH => 'Straight flush',
        self::ROYAL_FLUSH => 'Royal flush',
    ];
}
