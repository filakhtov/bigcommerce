<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Routing\Controller;
use \Symfony\Component\HttpFoundation\Response;

class RouterController extends Controller
{

    public function pageNotFound()
    {
        http_response_code(404);
        return $this->service('twig')->loadTemplate('404.html.twig')->render([]);
    }

    public function error($message)
    {
        return new Response(
            $this->service('twig')->loadTemplate('error.html.twig')->render(['message' => $message]), 500
        );
    }

}
