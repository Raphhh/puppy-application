<?php
namespace Puppy\Route;

/**
 * Class RoutePattern
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RoutePattern
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->setUri($uri);
    }

    /**
     * Setter of $method
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * Getter of $method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Setter of $uri
     *
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = (string)$uri;
    }

    /**
     * Getter of $uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getRegexUri()
    {
        return '#' . $this->getUri() . '#';
    }

    /**
     * Setter of $contentType
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = (string)$contentType;
    }

    /**
     * Getter of $ContentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
 