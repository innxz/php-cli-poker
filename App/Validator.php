<?php
declare(strict_types=1);

namespace App;

use App\Enums\CardEnum;
use App\Exceptions\CardAlreadyInGameException;
use App\Exceptions\CardDoesntExistsException;
use App\Exceptions\NoInputDataException;
use App\Exceptions\PlayerExistsException;
use App\Exceptions\WrongCountOfCardsException;

class Validator
{
    private $input;

    private $formatInput;

    private $usedCards = [];

    public function __construct(Input $input)
    {
        $this->input = $input->get();
        $this->validate();
        $this->formatInput();
        $this->validateCardsCount();
    }

    public function get(): array
    {
        return $this->formatInput;
    }

    private function validate(): void
    {
        try {
            if (!empty($this->input)) {
                try {
                    if ($this->isBoardExists()) {
                        if (!$this->isCountOfPlayersIsCorrect()) {
                            throw new PlayerExistsException('Players must be between two and nine');
                        }
                    } else {
                        throw new PlayerExistsException('Board is not exists');
                    }
                } catch (PlayerExistsException $exception) {
                    die($exception->getMessage() . PHP_EOL);
                }
            } else {
                throw new NoInputDataException('No input data');
            }
        } catch (NoInputDataException $exception) {
            die($exception->getMessage() . PHP_EOL);
        }
    }

    private function isBoardExists(): bool
    {
        return array_key_exists('board', $this->input);
    }

    private function isCountOfPlayersIsCorrect(): bool
    {
        $playersCount = count(array_keys($this->input));

        return $playersCount > 2 && $playersCount <= 10;
    }

    private function formatInput(): void
    {
        $this->formatInput;

        foreach ($this->input as $holder => $hand) {
            $this->formatInput[$holder] = $this->splitHandByCards($hand);
        }
    }

    private function splitHandByCards(string $hand): array
    {
        $result = [];

        foreach (CardEnum::LIST as $card => $splittedCard) {
            if (strpos($hand, $card) !== false) {
                try {
                    if (!$this->isCardInGame($card) && substr_count($hand, $card) === 1) {
                        $this->usedCards[] = $card;
                        $result[$card] = $this->splitCardBySuitAndValue($card);
                        $hand = str_replace($card, '', $hand);
                    } else {
                        throw new CardAlreadyInGameException('Card ' . $card . ' already in game');
                    }
                } catch (CardAlreadyInGameException $exception) {
                    die('error: ' . $exception->getMessage() . PHP_EOL);
                }
            }
        }

        try {
            if ($hand !== '') {
                throw new CardDoesntExistsException('Card(s) ' . $hand . ' doesn\'t exists');
            }
        } catch (CardDoesntExistsException $exception) {
            die('error: ' . $exception->getMessage() . PHP_EOL);
        }

        return $result;
    }

    private function splitCardBySuitAndValue(string $card): array
    {
        return CardEnum::LIST[$card];
    }

    private function isCardInGame(string $card): bool
    {
        return in_array($card, $this->usedCards, true);
    }

    private function validateCardsCount(): void
    {
        foreach ($this->formatInput as $player => $cards) {
            $count = count($cards);
            try {
                if ($player === 'board') {
                    if ($count < 3 || $count > 5) {
                        throw new WrongCountOfCardsException('The board must be from three to five cards');
                    }
                } else {
                    if ($count !== 2) {
                        throw new WrongCountOfCardsException('The player must have two cards');
                    }
                }
            } catch (WrongCountOfCardsException $exception) {
                die('error: ' . $exception->getMessage() . PHP_EOL);
            }
        }
    }
}
