<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Flickr\FlickrApiRepository;
use \Exception;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class FlickrController extends \BigCommerce\Infrastructure\Routing\Controller
{
    public function search(Request $request) {
        if(false === $this->isAuthenticated($request)) {
            return new RedirectResponse('/login');
        }

        return new Response(
            $this->service('twig')->render('search.html.twig')
        );
    }

    public function gallery(Request $request) {
        if(false === $this->isAuthenticated($request)) {
            return new JsonResponse(['message' => 'Please, authenticate before proceeding.'], 403);
        }

        $query = $request->query->get('query', null);
        if(is_null($query) || strlen($query) < 3) {
            throw new Exception("Bad request received.");
        }

        $page = $request->query->filter('page', null, FILTER_VALIDATE_INT);
        if(is_null($page) || $page < 1) {
            $page = 1;
        }

        $flickrRepo = $this->service('flickr.repository'); /* @var $flickrRepo FlickrApiRepository */
        $gallery = $flickrRepo->findGallery($query, $page);

        return new JsonResponse($gallery);
    }
}
