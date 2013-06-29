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

	public function  roll($roll) {
		$this->output($this->players[$this->currentPlayer] . " is the current player");
		$this->output("They have rolled a " . $roll);

		if ($this->inPenaltyBox[$this->currentPlayer]) {
			if ($roll % 2 != 0) {
				$this->isGettingOutOfPenaltyBox = true;

				$this->output($this->players[$this->currentPlayer] . " is getting out of the penalty box");
			$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
				if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;

				$this->output($this->players[$this->currentPlayer]
						. "'s new location is "
						.$this->places[$this->currentPlayer]);
				$this->output("The category is " . $this->currentCategory());
				$this->askQuestion();
			} else {
				$this->output($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
				$this->isGettingOutOfPenaltyBox = false;
				}

		} else {

		$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
			if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;

			$this->output($this->players[$this->currentPlayer]
					. "'s new location is "
					.$this->places[$this->currentPlayer]);
			$this->output("The category is " . $this->currentCategory());
			$this->askQuestion();
		}

	}

	protected function  askQuestion() {
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

	public function wasCorrectlyAnswered() {
		if ($this->inPenaltyBox[$this->currentPlayer]){
			if ($this->isGettingOutOfPenaltyBox) {
				$this->output("Answer was correct!!!!");
			$this->purses[$this->currentPlayer]++;
				$this->output($this->players[$this->currentPlayer]
						. " now has "
						.$this->purses[$this->currentPlayer]
						. " Gold Coins.");

				$winner = $this->didPlayerWin();
				$this->currentPlayer++;
				if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;

				return $winner;
			} else {
				$this->currentPlayer++;
				if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
				return true;
			}



		} else {

			$this->output("Answer was corrent!!!!");
		$this->purses[$this->currentPlayer]++;
			$this->output($this->players[$this->currentPlayer]
					. " now has "
					.$this->purses[$this->currentPlayer]
					. " Gold Coins.");

			$winner = $this->didPlayerWin();
			$this->currentPlayer++;
			if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;

			return $winner;
		}
	}

	public function wrongAnswer(){
		$this->output("Question was incorrectly answered");
		$this->output($this->players[$this->currentPlayer] . " was sent to the penalty box");
	$this->inPenaltyBox[$this->currentPlayer] = true;

		$this->currentPlayer++;
		if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
		return true;
	}


	protected function didPlayerWin() {
		return !($this->purses[$this->currentPlayer] == 6);
	}
}
