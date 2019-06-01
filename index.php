<?php
declare(strict_types=1);

use App\Input;
use App\Validator;

require_once 'vendor/autoload.php';

$input = (new Validator(new Input()))->get();
