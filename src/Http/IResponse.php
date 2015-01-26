<?php
namespace Puppy\Http;

/**
 * Interface IResponse
 * @package Puppy\Http
 */
interface IResponse
{
    /**
     *
     */
    public function send();

    /**
     * @return mixed
     */
    public function getContent();
}
 