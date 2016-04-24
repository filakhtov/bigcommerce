<?php namespace BigCommerce\Infrastructure\Controller;

use \BigCommerce\Domain\Entity\User;
use \BigCommerce\Infrastructure\Form\CsrfTokenVerificationException;
use \InvalidArgumentException;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class RegistrationController extends \BigCommerce\Infrastructure\Routing\Controller
{

    public function register(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            return $this->createAccount($request);
        } else {
            return $this->showRegistrationForm($request);
        }
    }

    private function showRegistrationForm(Request $request, array $data = [])
    {
        $data['csrfToken'] = $this->service('csrf')->generate($request, 'csrf_register');
        $data['menu'] = 'register';

        return new Response(
            $this->render('register.html.twig', $data)
        );
    }

    private function createAccount(Request $request)
    {
        try {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $rpassword = $request->request->get('rpassword');

            $this->service('csrf')->verify($request, 'csrf_register');

            $this->verifyPassword($password, $rpassword);
            $this->createUser($username, $password);
            $this->saveAuthenticationIntoSession(
                $request->getSession(), $this->service('auth')->authenticate($username, $password)
            );

            return new RedirectResponse('/');
        } catch (CsrfTokenVerificationException $e) {
            return $this->showRegistrationForm($request, ['message' => 'Unexpected security error. Please, try again.', 'username' => $username]);
        } catch (InvalidArgumentException $e) {
            return $this->showRegistrationForm($request, ['message' => $e->getMessage(), 'username' => $username]);
        }
    }

    private function createUser($username, $password)
    {
        $user = new User();
        $user->setUsername($username)
            ->setPassword(
                $this->service('password')->encrypt($password)
            );

        $this->service('user')->persist($user);
    }

    private function verifyPassword($password, $repeat) {
        if(strlen($password) < 6) {
            throw new InvalidArgumentException('Password is too short. Minimal length is 6 characters.');
        }

        if($password !== $repeat) {
            throw new InvalidArgumentException('Passwords do not match.');
        }
    }

}
