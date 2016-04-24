<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Infrastructure\Authentication\AuthenticationException;
use \BigCommerce\Infrastructure\Form\CsrfTokenVerificationException;
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
        $data['csrfToken'] = $this->service('csrf')->generate($request, 'csrf_login');

        return new Response(
            $this->render('login.html.twig', $data)
        );
    }

    private function authenticate(Request $request)
    {
        try {
            $this->service('csrf')->verify($request, 'csrf_login');

            $this->saveAuthenticationIntoSession(
                $request->getSession(),
                $this->service('auth')->authenticate(
                    $request->request->get('username'),
                    $request->request->get('password')
                )
            );

            $response = new RedirectResponse("/");
        } catch (CsrfTokenVerificationException $e) {
            $response = $this->showLoginForm($request, ['message' => 'Unexpected security error. Please, try again.']);
        } catch (AuthenticationException $e) {
            $response = $this->showLoginForm($request, ['message' => 'Invalid username or password. Please, try again.']);
        } catch (Exception $e) {
            $response = $this->showLoginForm($request, ['message' => $e->getMessage()]);
        }

        return $response;
    }

}
