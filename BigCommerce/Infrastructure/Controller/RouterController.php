<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Routing\Controller;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class RouterController extends Controller
{

    /** @return Response */
    public function pageNotFound(Request $request)
    {
        return new Response(
            $this->render('404.html.twig', ['url' => $request->getRequestUri()]), 404
        );
    }

    /**
     * @param string $message
     * @return Response
     */
    public function error(Request $request, $message)
    {
        if($request->headers->get('X-Api')) {
            return new JsonResponse(['message' => $message], 500);
        } else {
            return new Response(
                $this->render('error.html.twig', ['message' => $message]), 500
            );
        }
    }

}
