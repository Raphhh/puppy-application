<?php
namespace Puppy\Process;

/**
 * Class Processes
 * @package Puppy\Process
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Processes extends \ArrayObject
{
    /**
     * execute the processes
     */
    public function execute()
    {
        /**
         * @var callable[] $this
         */
        foreach($this as $process){
            call_user_func_array($process, func_get_args());
        }
    }
}
