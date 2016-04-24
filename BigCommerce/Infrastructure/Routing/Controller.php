<?php namespace BigCommerce\Infrastructure\Routing;

use \BigCommerce\Infrastructure\Registry\ServiceRegistry;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class Controller
{
    private $registry;

    final public function __construct(ServiceRegistry $registry)
    {
        $this->registry = $registry;
    }

    final protected function service($name) {
        return $this->registry->service($name);
    }

    protected function isAuthenticated(Request $request) {
        $isAuthenticated = false;

        if($request->hasPreviousSession()) {
            $session = $request->getSession();

            if($session->has('user.authentication')) {
                $authentication = $session->get('user.authentication');
                $isAuthenticated = $this->service('auth')->isAuthenticated($authentication);
            }
        }

        return $isAuthenticated;
    }

    protected function saveAuthenticationIntoSession(SessionInterface $session, $authentication) {
        $session->migrate(true, 0);
        $session->set('user.authentication', $authentication);
    }

    protected function render($template, array $context = []) {
        return $this->service('twig')->render($template, $context);
    }

}
