<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel;
use App\TimerKernel;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server as HttpServer;

class server
{
    /**
     * Server Parameters
     */
    public const HOST = '0.0.0.0';
    public const PORT = '9500';

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * Create HTTP Server
     */
    public function __construct()
    {
        $http = new HttpServer($this::HOST, $_ENV['SWOOLE_PORT'] ?? $this::PORT);
        $http->set(['worker_num' => $_ENV['SWOOLE_WORKER'] ?? '3']);

        // Events
        $http->on('Start', [$this, 'onStart']);
        $http->on('Request', [$this, 'onRequest']);

        // Run
        $http->start();
    }

    /**
     * Server Started Event
     *
     * @param HttpServer $server
     */
    public function onStart(HttpServer $server): void
    {
        // Create Timer Kernel
        new TimerKernel($server);

        // Debug Console
        echo sprintf("Swoole Worker => %s\n", $_ENV['SWOOLE_WORKER'] ?? '2');
        echo sprintf("Swoole HTTP server is started at http://%s:%s\n", $this::HOST, $_ENV['SWOOLE_PORT'] ?? $this::PORT);
    }

    /**
     * Server Request Event
     *
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response): void
    {
        // Create Kernel
        if (!$this->kernel) {
            $this->kernel = new Kernel();
        }

        // Process Request
        $this->kernel->boot($request, $response);
    }
}

new server();
