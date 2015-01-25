<?php
namespace Puppy\Module;

use Puppy\Application;

/**
 * @package Puppy
 * @author Rapha?l Lefebvre <raphael@raphaellefebvre.be>
 */
interface IModule
{

    /**
     * init the module.
     *
     * @param Application $application
     */
    public function init(Application $application);
}
