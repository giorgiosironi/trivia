<?php
function echoln($string) {
  echo $string."\n";
}

class Game {
    private $players;
    private $places;
    private $purses ;
    private $inPenaltyBox ;

    private $popQuestions;
    private $scienceQuestions;
    private $sportsQuestions;
    private $rockQuestions;

    private $currentPlayer = 0;
    private $isGettingOutOfPenaltyBox;

    private $noWinner = true;

    public function  __construct($outputChannel = null)
    {
        if ($outputChannel === null) {
            $outputChannel = function($string) {
                return echoln($string);
            };
        }
        $this->outputChannel = $outputChannel;
        $this->players = array();
        $this->places = array(0);
        $this->purses  = array(0);
        $this->inPenaltyBox  = array(0);

        $this->popQuestions = array();
        $this->scienceQuestions = array();
        $this->sportsQuestions = array();
        $this->rockQuestions = array();

        for ($i = 0; $i < 50; $i++) {
			array_push($this->popQuestions, "Pop Question " . $i);
			array_push($this->scienceQuestions, ("Science Question " . $i));
			array_push($this->sportsQuestions, ("Sports Question " . $i));
			array_push($this->rockQuestions, $this->createRockQuestion($i));
    	}
    }

    private function output($string) {
        $channel = $this->outputChannel;
        return $channel($string);
    }

	protected function createRockQuestion($index){
		return "Rock Question " . $index;
	}

	protected function isPlayable() {
		return ($this->howManyPlayers() >= 2);
	}

	public function add($playerName) {
	   array_push($this->players, $playerName);
	   $this->places[$this->howManyPlayers()] = 0;
	   $this->purses[$this->howManyPlayers()] = 0;
	   $this->inPenaltyBox[$this->howManyPlayers()] = false;

	    $this->output($playerName . " was added");
	    $this->output("They are player number " . count($this->players));
		return true;
	}

	protected function howManyPlayers() {
		return count($this->players);
	}

	public function roll($roll) {
        $this->outputRoll($roll);

        $this->canPlay = true;
		if ($this->inPenaltyBox[$this->currentPlayer]) {
			if ($roll % 2 != 0) {
                $this->outputGettingOutOfPenaltyBox();
				$this->isGettingOutOfPenaltyBox = true;

			} else {
                $this->outputNotGettingOutOfPenaltyBox();
				$this->isGettingOutOfPenaltyBox = false;
                $this->canPlay = false;
                return;
            }
		}

        $this->playRound($roll);
	}

	public function wasCorrectlyAnswered() {
        if (!$this->canPlay) {
            return $this->nextPlayer();
        }
        $this->correctAnswer();
        return $this->nextPlayer();
	}

	public function wrongAnswer(){
        $this->outputIncorrectAnswer();
        $this->inPenaltyBox[$this->currentPlayer] = true;

        return $this->nextPlayer();
	}
    
    public function isFinished()
    {
        return $this->noWinner !== true;
    }

    protected function playRound($roll)
    {
        $this->moveForward($roll);
        $this->outputPlayerPosition();
        $this->outputCategory();
        $this->askQuestion();
    }

    protected function correctAnswer()
    {
        $this->outputCorrectAnswer();
        $this->newGoldCoin();
        $this->outputGoldCoins();
        $this->noWinner = $this->didPlayerWin();
    }

	protected function askQuestion() {
		if ($this->currentCategory() == "Pop")
			$this->output(array_shift($this->popQuestions));
		if ($this->currentCategory() == "Science")
			$this->output(array_shift($this->scienceQuestions));
		if ($this->currentCategory() == "Sports")
			$this->output(array_shift($this->sportsQuestions));
		if ($this->currentCategory() == "Rock")
			$this->output(array_shift($this->rockQuestions));
	}


	protected function currentCategory() {
		if ($this->places[$this->currentPlayer] == 0) return "Pop";
		if ($this->places[$this->currentPlayer] == 4) return "Pop";
		if ($this->places[$this->currentPlayer] == 8) return "Pop";
		if ($this->places[$this->currentPlayer] == 1) return "Science";
		if ($this->places[$this->currentPlayer] == 5) return "Science";
		if ($this->places[$this->currentPlayer] == 9) return "Science";
		if ($this->places[$this->currentPlayer] == 2) return "Sports";
		if ($this->places[$this->currentPlayer] == 6) return "Sports";
		if ($this->places[$this->currentPlayer] == 10) return "Sports";
		return "Rock";
	}

    protected function moveForward($roll)
    {
		$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
			if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;
    }

    protected function newGoldCoin()
    {
        $this->purses[$this->currentPlayer]++;
    }

    protected function nextPlayer()
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
        return $this->noWinner;
    }


	protected function didPlayerWin() {
		return !($this->purses[$this->currentPlayer] == 6);
	}

    protected function outputPlayerPosition()
    {
        $this->output($this->players[$this->currentPlayer]
            . "'s new location is "
            .$this->places[$this->currentPlayer]);
    }

    protected function outputCategory()
    {
        $this->output("The category is " . $this->currentCategory());
    }

    protected function outputGoldCoins()
    {
        $this->output($this->players[$this->currentPlayer]
                . " now has "
                .$this->purses[$this->currentPlayer]
                . " Gold Coins.");
    }


    protected function outputCorrectAnswer()
    {
        $this->output("Answer was correct!!!!");
    }

    protected function outputIncorrectAnswer()
    {
		$this->output("Question was incorrectly answered");
		$this->output($this->players[$this->currentPlayer] . " was sent to the penalty box");
    }

    protected function outputGettingOutOfPenaltyBox()
    {
        $this->output($this->players[$this->currentPlayer] . " is getting out of the penalty box");
    }

    protected function outputNotGettingOutOfPenaltyBox()
    {
        $this->output($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
    }

    protected function outputRoll($roll)
    {
		$this->output($this->players[$this->currentPlayer] . " is the current player");
		$this->output("They have rolled a " . $roll);
    }
}
