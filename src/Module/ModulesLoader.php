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
class ModulesLoader
{
    /**
     * @var string
     */
    private $modulesDirectory;

    /**
     * @param string $modulesDirectory
     */
    public function __construct($modulesDirectory = 'src')
    {
        $this->setModulesDirectory($modulesDirectory);
    }

    /**
     * @return IModule[]
     */
    public function getModules()
    {
        $result = [];
        foreach ($this->getFiles($this->getModulesDirectory()) as $entry) {
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
     * @param $modulesDirectory
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
     * Setter of $modulesDirectory
     *
     * @param string $modulesDirectory
     */
    private function setModulesDirectory($modulesDirectory)
    {
        $this->modulesDirectory = (string)$modulesDirectory;
    }

    /**
     * Getter of $modulesDirectory
     *
     * @return string
     */
    private function getModulesDirectory()
    {
        return $this->modulesDirectory;
    }
}
 