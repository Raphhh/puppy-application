<?php
namespace Puppy\Module;

/**
 * Interface IModulesLoader
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
interface IModulesLoader
{
    /**
     * @return IModule[]
     */
    public function getModules();
}
 