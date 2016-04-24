<?php

use \BigCommerce\Bootstrap;
use \BigCommerce\Infrastructure\Controller\FlickrController;
use \BigCommerce\Infrastructure\Controller\LoginController;
use \BigCommerce\Infrastructure\Controller\RegistrationController;
use \BigCommerce\Infrastructure\Controller\RouterController;
use \BigCommerce\Infrastructure\Controller\SearchHistoryController;
use \BigCommerce\Infrastructure\Routing\RouterException;
use \BigCommerce\Infrastructure\Twig\CopyrightExtension;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Session\Session;

require_once implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'vendor', 'autoload.php']);

try {
    $request = Request::createFromGlobals();
    $request->setSession(new Session());

    $registry = Bootstrap::serviceRegistry();

    $routerController = new RouterController($registry);
    set_error_handler(function() use ($request, $routerController) {
        $routerController->error($request, func_get_arg(1))->send();
    }, E_RECOVERABLE_ERROR);

    $registry->service("twig")->addExtension(new CopyrightExtension());

    $router = $registry->service("router");
    $router
        ->addRoute('/', [new FlickrController($registry), 'search'])
        ->addRoute('/gallery', [new FlickrController($registry), 'gallery'])
        ->addRoute('/login', [new LoginController($registry), 'login'])
        ->addRoute('/logout', [new LoginController($registry), 'logout'])
        ->addRoute('/register', [new RegistrationController($registry), 'register'])
        ->addRoute('/history', [new SearchHistoryController($registry), 'showHistory'])
        ->addRoute('/delete', [new SearchHistoryController($registry), 'removeHistoryElement']);

    $response = $router->resolve($request);
} catch (RouterException $e) {
    $response = $routerController->pageNotFound($request);
} catch (Exception $e) {
    $response = $routerController->error($request, $e->getMessage());
}

$response->send();
