<?php
namespace Puppy\resources\src2;

use Puppy\Application;
use Puppy\Module\IModule;

/**
 * Class ModuleMock
 * @package Puppy\resources\modules
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleMock1 implements IModule
{


    /**
     * init the module.
     *
     * @param Application $application
     */
    public function init(Application $application)
    {
        $GLOBALS['module_mock_init_1'] = true;
    }
}
 