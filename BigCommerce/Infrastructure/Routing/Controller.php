<?php namespace BigCommerce\Infrastructure\Routing;

use \BigCommerce\Domain\Entity\User;
use \BigCommerce\Infrastructure\Authentication\Authentication;
use \BigCommerce\Infrastructure\Registry\ServiceRegistry;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class Controller
{

    /** @var ServiceRegistry */
    private $registry;

    final public function __construct(ServiceRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $alias
     * @return mixed
     */
    final protected function service($alias)
    {
        return $this->registry->service($alias);
    }

    /** @return bool */
    protected function isAuthenticated(Request $request)
    {
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

    /** @return void */
    protected function saveAuthenticationIntoSession(SessionInterface $session, Authentication $authentication)
    {
        $session->migrate(true, 0);
        $session->set('user.authentication', $authentication);

        return $this;
    }

    /**
     * @param string $template
     * @return string
     */
    protected function render($template, array $context = [])
    {
        return $this->service('twig')->render($template, $context);
    }

    /** @return User */
    protected function authenticatedUser()
    {
        return $this->service('doctrine')
            ->getRepository(User::class)
            ->findOneByUsername(
                $this->service('auth')->currentAuthentication()->username()
            );
    }

}
