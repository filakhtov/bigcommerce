<?php namespace BigCommerce\Infrastructure\Twig;

use \Twig_SimpleFilter;

class CopyrightExtension extends \Twig_Extension
{

    /** @return Twig_SimpleFilter[] */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('copyrightYears', [$this, 'copyrightYears'])
        ];
    }

    /** @return string */
    public function getName()
    {
        return __METHOD__;
    }

    /**
     * @param int $yearOfEstablishment
     * @return string
     */
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
