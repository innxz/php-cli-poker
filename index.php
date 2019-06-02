<?php
declare(strict_types=1);

use App\HandHandler;
use App\Input;
use App\Judge;
use App\Result;
use App\Validator;

require_once 'vendor/autoload.php';

$input = (new Validator(new Input()))->get();

$players = [];

for ($i = 1; $i <= 9; $i++) {
    $player = 'p' . $i;
    if (isset($input[$player])) {
        $players[$player] = (new HandHandler($player, $input[$player], $input['board']))->getCombination();
    }
}

$players = (new Judge($players))->getPlayersByPriority();

foreach ($players as $player) {
    $result = new Result($player);
    $result->show();
}
