<?php
namespace Puppy\resources;

/**
 * Class TemplateMock
 * @package Puppy\resources
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class TemplateMock
{
    /**
     * @param $template
     * @param array $vars
     * @return string
     */
    public function render($template, array $vars = [])
    {
        return 'foo';
    }
}
 