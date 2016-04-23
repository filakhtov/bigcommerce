<?php namespace BigCommerce\Infrastructure\Controller;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class RegistrationController extends \BigCommerce\Infrastructure\Routing\Controller
{

    public function register(Request $request) {
        return new Response(
                $this->service('twig')->render('register.html.twig')
        );
    }
}
