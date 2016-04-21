<?php namespace BigCommerce\Infrastructure\Routing;

use \BigCommerce\Infrastructure\Routing\Controller;
use \BigCommerce\Infrastructure\Routing\RouterException;
use \InvalidArgumentException;

class Router
{

    private $requestPath;
    private $queryString = [];
    private $routes = [];

    public function __construct($requestUri)
    {
        $this->requestPath = $requestUri;

        if (false !== strpos($requestUri, '?')) {
            list($this->requestPath, $queryString) = explode('?', $requestUri, 2);
            parse_str($queryString, $this->queryString);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @return Router
     */
    public function addRoute($path, callable $action)
    {
        if (in_array($path, $this->routes)) {
            throw new InvalidArgumentException("{$path}: route already registered.");
        }

        $this->routes[$path] = $action;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     * @return Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route) {
            $this->addRoute($route['route'], $route['action']);
        }

        return $this;
    }

    /**
     * @throws RouterException
     * @return Controller
     */
    public function __invoke()
    {
        foreach ($this->routes as $route => $action) {
            if ($route === $this->requestPath) {
                return $action;
            }
        }

        throw new RouterException("No route for: {$this->requestPath}");
    }

}
