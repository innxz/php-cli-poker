<?php
declare(strict_types=1);

use App\HandHandler;
use App\Input;
use App\Validator;

require_once 'vendor/autoload.php';

$input = (new Validator(new Input()))->get();

$players = [];

for ($i = 1; $i <= 9; $i++) {
    $player = 'p'. $i;
    if (isset($input[$player])) {
        $playersCards = array_merge($input[$player], $input['board']);
        $players[$player] = (new HandHandler($playersCards))->getCombination();
    }
}
