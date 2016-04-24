<?php namespace BigCommerce\Infrastructure\Routing;

use \BigCommerce\Infrastructure\Routing\RouterException;
use \InvalidArgumentException;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class Router
{

    private $routes = [];

    /**
     * @param string $path
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
     * @return callable
     */
    public function resolve(Request $request)
    {
        foreach ($this->routes as $route => $action) {
            if ($route === $request->getPathInfo()) {
                return $this->checkResponse($action($request));
            }
        }

        throw new RouterException("No route for: {$request}");
    }

    private function checkResponse(Response $response) {
        return $response;
    }

}
