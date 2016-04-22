<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Routing\Controller;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class RouterController extends Controller
{

    public function pageNotFound(Request $request)
    {
        return new Response(
            $this->service('twig')->loadTemplate('404.html.twig')->render(['url' => $request->getRequestUri()]), 404
        );
    }

    public function error(Request $request, $message)
    {
        if($request->headers->get('X-Api')) {
            return new JsonResponse(['message' => $message], 500);
        } else {
            return new Response(
                $this->service('twig')->loadTemplate('error.html.twig')->render(['message' => $message]), 500
            );
        }
    }

}
