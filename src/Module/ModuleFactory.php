<?php
namespace Puppy\Module;

use Puppy\Application;
use Stash\Driver\FileSystem;
use Stash\Pool;

/**
 * Class ModuleFactory
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleFactory
{
    /**
     * @param string $cachePath
     * @param array $modulesDirectories
     * @return IModulesLoader
     */
    public function create($cachePath = 'cache', array $modulesDirectories = ['src', 'vendor'])
    {
        if ($cachePath) {
            return new ModulesLoaderProxy(
                new ModulesLoader($modulesDirectories),
                new Pool($this->getDriver($cachePath))
            );
        }
        return new ModulesLoader($modulesDirectories);
    }

    /**
     * @param Application $application
     * @return IModulesLoader
     */
    public function createFromApplication(Application $application)
    {
        if(!empty($application->getService('config')['module.cache.enable'])){
            if (
                !empty($application->getService('config')['module.cache.path'])
                && !empty($application->getService('config')['module.directories'])
            ) {
                return $this->create(
                    $application->getService('config')['module.cache.path'],
                    $application->getService('config')['module.directories']
                );
            }

            if (!empty($application->getService('config')['module.cache.path'])) {
                return $this->create($application->getService('config')['module.cache.path']);
            }

            return $this->create();
        }

        return $this->create('');
    }

    /**
     * @param $cachePath
     * @return FileSystem
     */
    private function getDriver($cachePath)
    {
        $driver = new FileSystem();
        $driver->setOptions(['path' => $cachePath]);
        return $driver;
    }
}
