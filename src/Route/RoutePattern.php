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
     * search alias
     *
     * @var array
     */
    public static $alias = [
        ':all' => '(?<all>.*?)',
        ':id' => '(?<id>[1-9]\d*)',
        ':lang' => '(?<lang>[a-z]{2}-[A-Z]{2}|[a-z]{2})',
        ':datetime' => '(?<datetime>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}|\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})',
        ':date' => '(?<date>\d{4}-\d{2}-\d{2})',
        ':time' => '(?<time>\d{2}:\d{2}:\d{2})',
    ];

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
     * @var callable[]
     */
    private $filters = [];

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
        return '#' . strtr($this->getUri(), self::$alias) . '#';
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

    /**
     * Adder of $filter
     *
     * @param callable $filter
     */
    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Getter of $filters
     *
     * @return callable[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUri()
        . ' [' . ($this->getMethod() ? : '*') . ']'
        . ' [' . ($this->getContentType() ? : '*') . ']';
    }
}
 