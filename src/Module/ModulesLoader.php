<?php
namespace Puppy\Module;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use TRex\Parser\ClassAnalyzer;

/**
 * Class ModulesLoader
 * @package Puppy\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesLoader implements IModulesLoader
{
    /**
     * @var string[]
     */
    private $modulesDirectories;

    /**
     * @param string[] $modulesDirectories
     */
    public function __construct(array $modulesDirectories = ['src', 'vendor'])
    {
        $this->setModulesDirectories($modulesDirectories);
    }

    /**
     * @return IModule[]
     */
    public function getModules()
    {
        $result = [];
        foreach ($this->getModulesDirectories() as $dir) {
            $result = array_merge($result, $this->getModulesByDir($dir));
        }
        return $result;
    }

    /**
     * @param string $modulesDirectory
     * @return IModule[]
     */
    private function getModulesByDir($modulesDirectory)
    {
        $result = [];
        foreach ($this->getFiles($modulesDirectory) as $entry) {
            if ($entry->isFile() && stripos($entry->getFilename(), 'module') !== false) {
                $result = array_merge($result, $this->extractModules($entry->getPathname()));
            }
        }
        return $result;
    }

    /**
     * @param $filePath
     * @return IModule[]
     */
    private function extractModules($filePath)
    {
        $result = [];
        foreach ($this->extractReflectionClass($filePath) as $reflectionClass) {
            if (is_subclass_of($reflectionClass->getName(), 'Puppy\Module\IModule')) {
                $result[] = $reflectionClass->newInstance();
            }
        }
        return $result;
    }

    /**
     * @param string $filePath
     * @return \ReflectionClass[]
     */
    private function extractReflectionClass($filePath)
    {
        $classAnalyzer = new ClassAnalyzer();
        return $classAnalyzer->getClassReflectionsFromFile($filePath);
    }

    /**
     * @param string $modulesDirectory
     * @return SPLFileInfo[]
     */
    private function getFiles($modulesDirectory)
    {
        if (!is_dir($modulesDirectory)) {
            return [];
        }

        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $modulesDirectory,
                RecursiveDirectoryIterator::KEY_AS_FILENAME
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * Setter of $modulesDirectories
     *
     * @param string[] $modulesDirectories
     */
    private function setModulesDirectories(array $modulesDirectories)
    {
        $this->modulesDirectories = $modulesDirectories;
    }

    /**
     * Getter of $modulesDirectories
     *
     * @return string[]
     */
    private function getModulesDirectories()
    {
        return $this->modulesDirectories;
    }
}
 