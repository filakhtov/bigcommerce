<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Domain\Entity\SearchHistory;
use \Exception;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class SearchHistoryController extends \BigCommerce\Infrastructure\Routing\Controller
{
    public function showHistory(Request $request)
    {
        if (false === $this->isAuthenticated($request)) {
            return new RedirectResponse('/login');
        }

        return new Response(
            $this->render('history.html.twig', ['user' => $this->authenticatedUser()])
        );
    }

    public function removeHistoryElement(Request $request)
    {
        if (false === $request->headers->has('X-Api')) {
            return new RedirectResponse('/history');
        }

        if (false === $this->isAuthenticated($request)) {
            return new JsonResponse(['message' => 'Please, authenticate first.'], 403);
        }

        if ('DELETE' !== $request->getMethod()) {
            return new JsonResponse(['message' => 'Unsupported method.'], 400);
        }

        $searchHistoryItemId = $request->query->filter('id', null, FILTER_VALIDATE_INT);
        if (is_null($searchHistoryItemId)) {
            return new JsonResponse(['message' => 'Invalid id provided.'], 400);
        }

        try {
            $this->removeHistoryItemFromUsersList($searchHistoryItemId);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Unexpected system error.'], 500);
        }

        return new JsonResponse(['message' => 'OK']);
    }

    private function removeHistoryItemFromUsersList($id)
    {
        $em = $this->service('doctrine');
        $searchHistory = $em->getRepository(SearchHistory::class)->findOneById($id);

        $user = $this->authenticatedUser();
        $user->removeSearchHistoryItem($searchHistory);

        $em->persist($user);
        $em->flush();
    }
}
