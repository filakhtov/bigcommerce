<?php

use \BigCommerce\Infrastructure\Configuration\Configuration;
use \BigCommerce\Infrastructure\Controller\FlickrController;
use \BigCommerce\Infrastructure\Controller\RouterController;
use \BigCommerce\Infrastructure\Flickr\FlickrApiRepository;
use \BigCommerce\Infrastructure\Flickr\FlickrRestService;
use \BigCommerce\Infrastructure\Php\Curl;
use \BigCommerce\Infrastructure\Php\CurlProxy;
use \BigCommerce\Infrastructure\Registry\ServiceRegistry;
use \BigCommerce\Infrastructure\Routing\Router;
use \BigCommerce\Infrastructure\Routing\RouterException;
use \BigCommerce\Infrastructure\Twig\CopyrightExtension;
use \Symfony\Component\Yaml\Yaml;

$projectPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
require_once $projectPath . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

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
    )
]);

$registry->service("twig")->addExtension(new CopyrightExtension());

try {
    $requestPath = filter_input(INPUT_SERVER, 'REQUEST_URI');
    $router = new Router($requestPath);
    $router->addRoute('/search', [new FlickrController($registry), 'search']);
    $controller = $router();
    $response = $controller();
} catch (RouterException $re) {
    $routerController = new RouterController($registry);
    $response = $routerController->pageNotFound();
} catch (Exception $e) {
    $routerController = new RouterController($registry);
    $response = $routerController->error($e);
}

echo $response;
