<?php

use \BigCommerce\Infrastructure\Authentication\AuthenticationRepository;
use \BigCommerce\Infrastructure\Authentication\PasswordHasher;
use \BigCommerce\Infrastructure\Configuration\Configuration;
use \BigCommerce\Infrastructure\Controller\FlickrController;
use \BigCommerce\Infrastructure\Controller\LoginController;
use \BigCommerce\Infrastructure\Controller\RegistrationController;
use \BigCommerce\Infrastructure\Controller\RouterController;
use \BigCommerce\Infrastructure\Flickr\FlickrApiRepository;
use \BigCommerce\Infrastructure\Flickr\FlickrRestService;
use \BigCommerce\Infrastructure\Form\CsrfTokenManager;
use \BigCommerce\Infrastructure\Php\Curl;
use \BigCommerce\Infrastructure\Php\CurlProxy;
use \BigCommerce\Infrastructure\Registry\ServiceRegistry;
use \BigCommerce\Infrastructure\Routing\Router;
use \BigCommerce\Infrastructure\Routing\RouterException;
use \BigCommerce\Infrastructure\Twig\CopyrightExtension;
use \BigCommerce\Infrastructure\User\UserRepository;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Session\Session;
use \Symfony\Component\Yaml\Yaml;

$projectPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $projectPath . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$request = Request::createFromGlobals();

try {
    $request->setSession(new Session());

    $registry = new ServiceRegistry([
        'flickr.repository' => new FlickrApiRepository(
            new FlickrRestService(
                new Configuration(
                    Yaml::parse(file_get_contents($projectPath . 'config' . DIRECTORY_SEPARATOR . 'config.yml'))
                ),
                new Curl(
                    new CurlProxy()
                )
            )
        ),
        'twig' => new \Twig_Environment(
            new \Twig_Loader_Filesystem([$projectPath . 'templates'])
        ),
        'router' => new Router(),
        'auth' => new AuthenticationRepository(),
        'csrf' => new CsrfTokenManager(),
        'password' => new PasswordHasher(),
        'user' => new UserRepository()
    ]);

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
        ->addRoute('/register', [new RegistrationController($registry), 'register']);

    $response = $router->resolve($request);
} catch (RouterException $e) {
    $response = $routerController->pageNotFound($request);
} catch (Exception $e) {
    $response = $routerController->error($request, $e->getMessage());
}

$response->send();
