<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Exception;

class Queue
{
    /**
     * middleware mapping
     *
     * @var array
     */
    public static $map = [];

    /**
     * Mapping of middlewares that will be loaded in all routes
     *
     * @var array
     */
    private static $default = [];

    /**
     * Queue of middlewares to be executed
     *
     * @var array
     */
    private $middlewares = [];

    /**
     * Controller execution function
     *
     * @var Closure
     */
    private $controller;

    /**
     * Controller function arguments
     *
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Method responsible for building the middleware queue class
     *
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Method responsible for defining the middleware mapping
     *
     * @param array $map
     */
    public static function setMap($map)
    {
        self::$map = $map;
    }

    /**
     * Method responsible for defining the default middleware mapping
     *
     * @param array $default
     */
    public static function setDefault($default)
    {
        self::$default = $default;
    }

    /**
     * Method responsible for executing the next level of the middleware queue
     *
     * @param Request $request
     * @return Response
     */
    public function next($request)
    {
        // Checks if the queue is empty
        if(empty($this->middlewares)) {
            return call_user_func_array($this->controller, $this->controllerArgs);
        }

        // Middleware
        $middleware = array_shift($this->middlewares);

        // Check the mapping
        if(!isset(self::$map[$middleware])) {
            throw new Exception('Problemas ao processar o middleware da requisição.', 500);
        }

        // Next
        $queue = $this;
        $next = function ($request) use ($queue) {
            return $queue->next($request);
        };

        // Run the middleware
        return (new self::$map[$middleware]())->handle($request, $next);
    }
}
