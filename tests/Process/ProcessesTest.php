<?php
namespace Process;

use Puppy\Process\Processes;

/**
 * Class ProcessesTest
 * @package Process
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ProcessesTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $arg = 'param';

        $processes = new Processes([
            $this->provideProcess($arg),
            $this->provideProcess($arg),
        ]);

        $processes->execute($arg);
    }

    private function provideProcess($arg)
    {
        return \Closure::bind(
            function($param) use($arg){
                $this->assertSame($arg, $param);
            },
            $this
        );
    }
}
