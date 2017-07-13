<?php
namespace BrokenPixel\CurlerBundle\Classes;

abstract class ServerConnectionAbstract
{
    protected $serverConnection;

    /**
     * Object constructor used to open a connection
     *
     * @param $url String
     *      Web accessible address to connect to using cURL
     */
    public function __construct($url)
    {
        $this->openConnection($url);
    }

    protected abstract function openConnection();

    protected abstract function closeConnection();

    /**
     * Object destructor used to close the server connection
     */
    public function __deconstruct()
    {
        $this->closeConnection();
    }
}