<?php
namespace Puppy\Module;

use Stash\Interfaces\PoolInterface;

/**
 * Class ModulesLoaderProxy
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesLoaderProxy implements IModulesLoader
{

    /**
     * @var IModulesLoader
     */
    private $modulesLoader;

    /**
     * @var PoolInterface
     */
    private $cache;

    /**
     * @param IModulesLoader $modulesLoader
     * @param \Stash\Interfaces\PoolInterface $cache
     */
    public function __construct(IModulesLoader $modulesLoader, PoolInterface $cache)
    {
        $this->setModulesLoader($modulesLoader);
        $this->setCache($cache);
    }

    /**
     * @return IModule[]
     */
    public function getModules()
    {
        $modules = $this->getCache()->getItem(__METHOD__)->get();
        if (!$modules) {
            $this->getCache()->getItem(__METHOD__)->set($this->getModulesLoader()->getModules());
            $modules = $this->getCache()->getItem(__METHOD__)->get();
        }
        return $modules;
    }

    /**
     * Setter of $cache
     *
     * @param PoolInterface $cache
     */
    private function setCache(PoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Getter of $cache
     *
     * @return PoolInterface
     */
    private function getCache()
    {
        return $this->cache;
    }

    /**
     * Setter of $modulesLoader
     *
     * @param IModulesLoader $modulesLoader
     */
    private function setModulesLoader(IModulesLoader $modulesLoader)
    {
        $this->modulesLoader = $modulesLoader;
    }

    /**
     * Getter of $modulesLoader
     *
     * @return IModulesLoader
     */
    private function getModulesLoader()
    {
        return $this->modulesLoader;
    }
}
