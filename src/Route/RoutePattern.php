<?php
namespace Puppy\Route;

/**
 * Class RoutePattern
 * @package Puppy\Route
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RoutePattern
{
    const REGEX_DELIMITER = '#';

    /**
     * list of bindings
     *
     * @var string[]
     */
    public static $bindings = [
        ':all' => '(?<all>.*)',
        ':home' => '(?<home>^/?$)',
        ':slug' => '(?<home>[a-zA-Z0-9\-_]+)',
        ':id' => '(?<id>[1-9]\d*)',
        ':index' => '(?<index>\d+)',
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
     * @var string[]
     */
    private $specificBindings = [];

    /**
     * @param string $alias
     * @param string $pattern
     * @param string $delimiter
     */
    public static function addGlobalBinding($alias, $pattern, $delimiter = ':')
    {
        self::$bindings[self::formatAlias($alias, $delimiter)] = self::formatBinding($alias, $pattern);
    }

    /**
     * @param string $alias
     * @param string $pattern
     * @return string
     */
    private static function formatBinding($alias, $pattern)
    {
        return '(?<'.$alias.'>'.$pattern.')';
    }

    /**
     * @param string $delimiter
     * @param string $alias
     * @return string
     */
    private static function formatAlias($alias, $delimiter)
    {
        return $delimiter . $alias;
    }

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
        return self::REGEX_DELIMITER . '^' . $this->formatUri() . '$' . self::REGEX_DELIMITER;
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
     * Getter of $bindings
     *
     * @return string[]
     */
    public function getBindings()
    {
        return array_merge(self::$bindings, $this->specificBindings);
    }

    /**
     * Setter of $bindings
     *
     * @param string $alias
     * @param string $pattern
     * @param string $delimiter
     */
    public function addBinding($alias, $pattern = '[a-zA-Z0-9\-_]+', $delimiter = ':')
    {
        $this->specificBindings[self::formatAlias($alias, $delimiter)] =  self::formatBinding($alias, $pattern);
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

    /**
     * @return string
     */
    private function formatUri()
    {
        return trim(strtr($this->getUri(), $this->getBindings()), '/');
    }
}
 