<?php namespace BigCommerce\Infrastructure\Registry;

use \InvalidArgumentException;

class ServiceRegistry
{

    private $services = [];

    public function __construct(array $services)
    {
        foreach ($services as $name => $service) {
            $this->addService($name, $service);
        }
    }

    public function addService($name, $service)
    {
        if (false === is_object($service)) {
            throw new InvalidArgumentException("Invalid service {$name}: object expected.");
        }

        if (array_key_exists($name, $this->services)) {
            throw new InvalidArgumentException("Service {$name} already registered.");
        }

        $this->services[$name] = $service;

        return $this;
    }

    public function service($name)
    {
        if(false === array_key_exists($name, $this->services)) {
            throw new InvalidArgumentException("Service {$name} war not registered.");
        }

        return $this->services[$name];
   }

}
