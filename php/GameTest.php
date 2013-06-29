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

    private function assertOutputIs(array $lines)
    {
        $this->assertEquals(
            $lines,
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
}
