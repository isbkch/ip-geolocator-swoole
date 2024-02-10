<?php

namespace App;

use App\Library\Helper;
use OpenSwoole\Http\Server as HttpServer;

class TimerKernel
{
    /**
     * @var HttpServer
     */
    private $server;

    /**
     * @var array
     */
    private $container = [];

    /**
     * TimerKernel Constructor.
     *
     * @param HttpServer $server
     */
    public function __construct(HttpServer $server)
    {
        $this->server = $server;
    }

    /**
     * Call Timer Controller
     *
     * @param $id
     * @param array $params
     */
    public function call($id, array $params = []): void
    {
        if (is_array($id)) {
            $params = $id;
            $id = null;
        }

        [$class, $method] = explode('::', $params[0]);

        // Create AbstractController
        if (!isset($this->container[$class])) {
            $this->container[$class] = new $class($this->server, $method, $params[1], $params[2], $id);
        }

        // Call Method
        call_user_func([$this->container[$class], $method]);
    }
}
