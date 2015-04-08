<?php
namespace Puppy\Module;

use Puppy\Application;
use Stash\Pool;

/**
 * Class ModuleFactory
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleFactory
{
    /**
     * @param array $modulesDirectories
     * @param array $cacheOptions
     * @return IModulesLoader
     */
    public function create(
        array $modulesDirectories = ['src', 'vendor'],
        array $cacheOptions = ['path' => 'cache', 'driver' => 'Stash\Driver\FileSystem']
    )
    {
        return new ModulesLoaderProxy(
            new ModulesLoader($modulesDirectories),
            new Pool($this->getDriver($cacheOptions))
        );
    }

    /**
     * @param Application $application
     * @return IModulesLoader
     */
    public function createFromApplication(Application $application)
    {
        return $this->create(
            $this->retrieveDirectories($application->getService('config')),
            $this->retrieveOptions($application->getService('config'))
        );
    }

    /**
     * @param array $options
     * @return \Stash\Interfaces\DriverInterface|null
     */
    private function getDriver(array $options)
    {
        if(!$options){
            return null;
        }

        /**
         * @var \Stash\Interfaces\DriverInterface $driver
         */
        $driver = new $options['driver'];
        $driver->setOptions($options);
        return $driver;
    }

    /**
     * @param \ArrayAccess $config
     * @return array
     */
    private function retrieveDirectories(\ArrayAccess $config)
    {
        return isset($config['module.directories']) ? $config['module.directories'] : ['src', 'vendor'];
    }

    /**
     * @param \ArrayAccess $config
     * @return array
     */
    private function retrieveOptions(\ArrayAccess $config)
    {
        if (!empty($config['module.cache.enable'])) {
            return isset($config['module.cache.options']) ? $config['module.cache.options'] : [
                'path' => 'cache',
                'driver' => 'Stash\Driver\FileSystem',
            ];
        }
        return [];
    }
}
