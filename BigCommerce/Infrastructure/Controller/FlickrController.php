<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Domain\Entity\SearchHistory;
use \BigCommerce\Domain\Entity\User;
use \BigCommerce\Infrastructure\Flickr\FlickrApiRepository;
use \Doctrine\ORM\EntityManager;
use \Exception;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class FlickrController extends \BigCommerce\Infrastructure\Routing\Controller
{

    public function search(Request $request)
    {
        if (false === $this->isAuthenticated($request)) {
            return new RedirectResponse('/login');
        }

        return new Response(
            $this->render('search.html.twig', ['user' => $this->authenticatedUser()])
        );
    }

    public function gallery(Request $request)
    {
        if (false === $this->isAuthenticated($request)) {
            return new JsonResponse(['message' => 'Please, authenticate before proceeding.'], 403);
        }

        $query = $request->query->get('query', null);
        if (is_null($query) || strlen($query) < 3) {
            throw new Exception("Bad request received.");
        }

        $page = $request->query->filter('page', null, FILTER_VALIDATE_INT);
        if (is_null($page) || $page < 1) {
            $page = 1;
        }

        $saveToHistory = $request->query->filter('saveToHistory', null, FILTER_VALIDATE_INT);
        if (1 === $saveToHistory) {
            $this->saveSearchToHistory($query);
        }

        $flickrRepo = $this->service('flickr.repository'); /* @var $flickrRepo FlickrApiRepository */
        $gallery = $flickrRepo->findGallery($query, $page);

        return new JsonResponse($gallery);
    }

    /** @return User */
    private function authenticatedUser()
    {
        return $this->service('doctrine')
            ->getRepository(User::class)
            ->findOneByUsername(
                $this->service('auth')->currentAuthentication()->username()
            );
    }

    private function saveSearchToHistory($query)
    {
        $em = $this->service('doctrine'); /* @var $em EntityManager */

        $searchHistoryItem = $em->getRepository(SearchHistory::class)->findOneByQuery($query);
        if (is_null($searchHistoryItem)) {
            $searchHistoryItem = new SearchHistory();
            $searchHistoryItem->setQuery($query);
        }

        $user = $this->authenticatedUser();
        $user->addSearchHistoryItem($searchHistoryItem);

        $em->persist($searchHistoryItem);
        $em->persist($user);
        $em->flush();
    }

}
