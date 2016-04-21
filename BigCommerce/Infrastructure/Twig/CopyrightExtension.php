<?php namespace BigCommerce\Infrastructure\Twig;

use \Twig_SimpleFilter;

class CopyrightExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('copyrightYears', [$this, 'copyrightYears'])
        ];
    }

    public function getName()
    {
        return __METHOD__;
    }

    public function copyrightYears($yearOfEstablishment)
    {
        $currentYear = date('Y');

        if ($currentYear > $yearOfEstablishment) {
            $copyrightYears = "{$yearOfEstablishment} - {$currentYear}";
        } else {
            $copyrightYears = $yearOfEstablishment;
        }

        return $copyrightYears;
    }

}
