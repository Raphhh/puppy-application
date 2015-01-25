<?php
namespace Puppy\resources;

use Puppy\Application;
use Puppy\Module\IModule;

class ModuleMock implements IModule
{

    /**
     * init the module.
     *
     * @param Application $application
     */
    public function init(Application $application)
    {
        //nothing
    }
}
 