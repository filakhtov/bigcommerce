<?php namespace BigCommerce\Infrastructure\Registry;

use \InvalidArgumentException;

class ServiceRegistry
{

    /** @var mixed[] */
    private $services = [];

    /** @throws InvalidArgumentException */
    public function __construct(array $services)
    {
        foreach ($services as $name => $service) {
            $this->addService($name, $service);
        }
    }

    /**
     * @param string $alias
     * @param mixed $service Must be an instance of an Object
     * @throws InvalidArgumentException
     * @return ServiceRegistry
     */
    public function addService($alias, $service)
    {
        if (false === is_object($service)) {
            throw new InvalidArgumentException("Invalid service {$alias}: object expected.");
        }

        if (array_key_exists($alias, $this->services)) {
            throw new InvalidArgumentException("Service {$alias} already registered.");
        }

        $this->services[$alias] = $service;

        return $this;
    }

    /**
     * @param string $alias
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function service($alias)
    {
        if(false === array_key_exists($alias, $this->services)) {
            throw new InvalidArgumentException("Service {$alias} war not registered.");
        }

        return $this->services[$alias];
   }

}
