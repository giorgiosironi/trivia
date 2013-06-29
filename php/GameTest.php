<?
include_once 'Game.php';

class GameTest extends PHPUnit_Framework_TestCase
{
    private $game;
    private $output = array();

    public function setUp()
    {
        $this->game = new Game($this->collectOutput());
    }

    public function testASinglePlayerCanBeAddedToTheGame()
    {
        $this->game->add('Giorgio');
        $this->assertOutputIs(array(
            'Giorgio was added',
            'They are player number 1',
        ));
    }

    public function testASinglePlayerCanRollTheDice()
    {
        $this->game->add('Giorgio');
        $this->clearOutput();

        $this->game->roll(1);
        $this->assertOutputIs(array(
            'Giorgio is the current player',
            'They have rolled a 1',
            'Giorgio\'s new location is 1',
            'The category is Science',
            'Science Question 0',
        ));
    }

    public function testASinglePlayerCanAnswerAQuestionCorrectly()
    {
        $this->game->add('Giorgio');
        $this->game->roll(1);

        $this->clearOutput();
        $this->game->wasCorrectlyAnswered();
        $this->assertOutputIs(array(
            'Answer was correct!!!!',
            'Giorgio now has 1 Gold Coins.',
        ));
    }

    public function testASinglePlayerCanAnswerAQuestionWronglyAndBeSentToThePenaltyBox()
    {
        $this->game->add('Giorgio');
        $this->game->roll(1);

        $this->clearOutput();
        $this->game->wrongAnswer();
        $this->assertOutputIs(array(
            'Question was incorrectly answered',
            'Giorgio was sent to the penalty box',
        ));
    }

    public function testAPlayerInThePenaltyBoxCanExitITWithAnOddRoll()
    {
        $this->game->add('Giorgio');
        $this->game->roll(1);
        $this->game->wrongAnswer();

        $this->clearOutput();
        $this->game->roll(1);
        $this->assertOutputContains(
            'Giorgio is getting out of the penalty box'
        );
    }

    public function testAPlayerInThePenaltyBoxStaysInThereWithAnEvenRoll()
    {
        $this->game->add('Giorgio');
        $this->game->roll(1);
        $this->game->wrongAnswer();

        $this->clearOutput();
        $this->game->roll(2);
        $this->assertOutputContains(
            'Giorgio is not getting out of the penalty box'
        );
    }

    public function testAnsweringAQuestionWhileExitingFromThePenaltyBox()
    {
        $this->game->add('Giorgio');
        $this->game->roll(1);
        $this->game->wrongAnswer();
        $this->game->roll(1);

        $this->clearOutput();
        $this->game->wasCorrectlyAnswered();
        $this->assertOutputIs(array(
            'Answer was correct!!!!',
            'Giorgio now has 1 Gold Coins.',
        ));
    }

    public function testQuestionsCannotBeAnsweredWhileStayingInThePenaltyBox()
    {
        $this->game->add('Giorgio');
        $this->game->roll(1);
        $this->game->wrongAnswer();
        $this->game->roll(2);

        $this->clearOutput();
        $this->game->wasCorrectlyAnswered();
        $this->assertOutputIs(array());
    }

    private function assertOutputIs(array $lines)
    {
        $this->assertEquals(
            $lines,
            $this->output
        );
    }

    private function assertOutputContains($line)
    {
        $this->assertContains(
            $line,
            $this->output
        );
    }

    private function collectOutput()
    {
        return function($string) {
            foreach (explode("\n", $string) as $line) {
                if ($line) {
                    $this->output[] = $line;
                }
            }
        };
    }

    private function clearOutput()
    {
        $this->output = array();
    }
}
