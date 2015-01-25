<?php
namespace Puppy\resources;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestMock
 * @package Puppy\resources
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RequestMock extends Request
{
    /**
     * @param string $uri
     */
    public function setRequestUri($uri){
        $this->requestUri = $uri;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param array $acceptableContentTypes
     */
    public function setAcceptableContentTypes(array $acceptableContentTypes)
    {
        $this->acceptableContentTypes = $acceptableContentTypes;
    }
}
 