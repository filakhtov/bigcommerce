<?php namespace BigCommerce\Infrastructure\Routing;

use \BigCommerce\Infrastructure\Routing\Controller;
use \BigCommerce\Infrastructure\Routing\RouterException;
use \InvalidArgumentException;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class Router
{

    private $request;
    private $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws InvalidArgumentException
     * @return \BigCommerce\Infrastructure\Routing\Router
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
     * @return \BigCommerce\Infrastructure\Routing\Router
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
            if ($route === $this->request->getPathInfo()) {
                return $this->checkResponse($action($this->request));
            }
        }

        throw new RouterException("No route for: {$this->request}");
    }

    private function checkResponse(Response $response) {
        return $response;
    }

}
