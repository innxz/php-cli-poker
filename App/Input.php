<?php
declare(strict_types=1);

namespace App;

class Input
{
    private $inputCardsByHolders;

    public function __construct()
    {
        $this->inputCardsByHolders = getopt('', [
            'board:',
            'p1:',
            'p2:',
            'p3::',
            'p4::',
            'p5::',
            'p6::',
            'p7::',
            'p8::',
            'p9::',
        ]);
    }

    public function get(): array
    {
        return $this->inputCardsByHolders;
    }
}
