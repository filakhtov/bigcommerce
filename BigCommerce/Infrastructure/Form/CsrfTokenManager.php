<?php namespace BigCommerce\Infrastructure\Form;

use \BigCommerce\Infrastructure\Form\CsrfTokenVerificationException;
use \Symfony\Component\HttpFoundation\Request;

class CsrfTokenManager
{

    /**
     * @param string $tokenFieldName
     * @return string
     */
    public function generate(Request $request, $tokenFieldName)
    {
        $csrfToken = sha1(uniqid('auth'));

        $session = $request->getSession();
        $session->set($tokenFieldName, $csrfToken);

        return $csrfToken;
    }

    /**
     * @param string $tokenFieldName
     * @throws CsrfTokenVerificationException
     * @return void
     */
    public function verify(Request $request, $tokenFieldName)
    {
        if (false === $request->hasPreviousSession()) {
            throw new CsrfTokenVerificationException('No previous session found.');
        }

        $session = $request->getSession();

        if (false === $session->has($tokenFieldName)) {
            throw new CsrfTokenVerificationException('Token value is missing in the session.');
        }

        $sessionToken = $session->get($tokenFieldName);
        $session->remove($tokenFieldName);

        if (false === $request->request->has($tokenFieldName)) {
            throw new CsrfTokenVerificationException('Token value is missing in the request.');
        }

        if ($sessionToken !== $request->request->get($tokenFieldName)) {
            throw new CsrfTokenVerificationException('Tokens do not match.');
        }
    }

}
