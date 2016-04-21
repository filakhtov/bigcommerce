<?php namespace BigCommerce\Infrastructure\Routing;

use \BigCommerce\Infrastructure\Registry\ServiceRegistry;

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
}
