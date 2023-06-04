<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Http;

use App\Http\Middleware\Queue as MiddlewareQueue;
use App\Utils\View;
use Closure;
use Exception;
use ReflectionFunction;

class Router
{
    /**
     * Full project URL (root)
     *
     * @var string
     */
    private $url = '';

    /**
     * Prefix of all routes
     *
     * @var string
     */
    private $prefix = '';

    /**
     * Route index
     *
     * @var array
     */
    private $routes = [];

    /**
     * Request Instance
     *
     * @var Request
     */
    private $request;

    private $contentType = 'text/html';

    /**
     * Method responsible for starting the class
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Method responsible for defining the prefix of the routes
     *
     * @return void
     */
    private function setPrefix()
    {
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';
    }

    /**
     * Method responsible for adding a route in the class
     *
     * @param string $method
     * @param string $route
     * @param array $params
     */
    private function addRoute($method, $route, $params = [])
    {
        foreach($params as $key => $value) {
            if($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];

        $params['variables'] = [];

        $patternVariable = '/{(.*?)}/';
        if(preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        $patternRoute = '/^'.str_replace('/', '\/', $route).'$/';
        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Method responsible for defining a GET route
     *
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = [])
    {
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Method responsible for defining a POST route
     *
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = [])
    {
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Method responsible for defining a PUT route
     *
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = [])
    {
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Method responsible for defining a DELETE route
     *
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);
    }

    /**
     * Method responsible for returning the URI disregarding the prefix
     *
     * @return string
     */
    private function getUri()
    {
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];
        return rtrim(end($xUri), '/');
    }

    /**
     * Method responsible for returning the current route data
     *
     * @return array
     */
    private function getRoute()
    {
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();
        foreach($this->routes as $patternRoute => $methods) {
            if(preg_match($patternRoute, $uri, $matches)) {

                if(isset($methods[$httpMethod])) {
                    unset($matches[0]);

                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }

                $content = View::render('base/405', []);
                throw new Exception($content, 405);
            }
        }

        $content =  View::render('base/404', []);
        throw new Exception($content, 404);
    }

    /**
     * Method responsible for executing the current route
     *
     * @return Response
     */
    public function run()
    {
        try {
            $route = $this->getRoute();
            if(!isset($route['controller'])) {
                throw new Exception('A URL não pôde ser processada.', 500);
            }

            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);

        } catch(Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    private function getErrorMessage($message)
    {
        switch($this->contentType) {
            case 'application/json':
                return [
                    'error' => $message
                ];

            default:
                return $message;
        }
    }

    /**
     * Method responsible for returning the current URL
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url.$this->getUri();
    }

    /**
     * Method responsible for redirecting the URL
     *
     * @param string $route
     */
    public function redirect($route)
    {
        $url = $this->url.$route;

        header('Location: '.$url);
        exit;
    }
}
