<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Routing\Controller;
use \Exception;

class RouterController extends Controller
{

    public function pageNotFound()
    {
        http_response_code(404);
        return $this->service('twig')->loadTemplate('404.html.twig')->render([]);
    }

    public function error(Exception $exception)
    {
        http_response_code(500);
        return $this->service('twig')->loadTemplate('error.html.twig')->render(['message' => $exception->getMessage()]);
    }

}
