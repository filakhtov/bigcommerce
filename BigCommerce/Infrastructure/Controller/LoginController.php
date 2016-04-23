<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Authentication\AuthenticationException;
use \Exception;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class LoginController extends \BigCommerce\Infrastructure\Routing\Controller
{

    public function login(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            return $this->authenticate($request);
        } else {
            return $this->showLoginForm($request);
        }
    }

    public function logout(Request $request)
    {
        if ($request->hasPreviousSession()) {
            $session = $request->getSession();
            if (false === $session->isStarted()) {
                $session->start();
            }
            $session->invalidate();
        }

        return new RedirectResponse('/login');
    }

    private function showLoginForm(Request $request, array $data = [])
    {
        $data['csrfToken'] = uniqid('auth');

        $session = $request->getSession();
        $session->set('csrfToken', $data['csrfToken']);

        return new Response(
            $this->service('twig')->render('login.html.twig', $data)
        );
    }

    private function checkCsrfToken(Request $request)
    {
        if (false === $request->hasPreviousSession()) {
            throw new Exception('Security error. Please, repeat your request.');
        }

        $session = $request->getSession();

        if (false === $session->has('csrfToken')) {
            throw new Exception('Security error. Please, repeat your request.');
        }

        if ($session->get('csrfToken') !== $request->request->get('csrf-token')) {
            throw new Exception('Security error. Please, repeat your request.');
        }
    }

    private function authenticate(Request $request)
    {
        try {
            $this->checkCsrfToken($request);

            $this->saveAuthenticationIntoSession(
                $request->getSession(),
                $this->service('auth')->authenticate(
                    $request->request->get('username'),
                    $request->request->get('password')
                )
            );

            $response = new RedirectResponse("/");
        } catch (AuthenticationException $e) {
            $response = $this->showLoginForm($request, ['message' => 'Invalid username or password. Please, try again.']);
        } catch (Exception $e) {
            $response = $this->showLoginForm($request, ['message' => $e->getMessage()]);
        }

        return $response;
    }

}
