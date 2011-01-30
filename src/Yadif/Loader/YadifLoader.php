<?php

/*
 * Yadif
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

use Symfony\Component\DependencyInjection\Loader\Loader;
use Symfony\Component\DependencyInjection\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class Yadif_Loader_YadifLoader extends Loader
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * @var string
     */
    protected $namespace = '';

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    function load($yadif)
    {
        if (!($yadif instanceof Yadif_Container)) {
            throw new Yadif_Exception("Cannot run Symfony Dependency Injenction YadifLoader without passing an instance of Yadif_Container");
        }

        $definitions = $yadif->getContainer();
        $params = $yadif->getParameters();
        foreach ($params AS $param => $value) {
            $param = strtolower(substr($param, 1)); // remove the starting :
            $this->container->setParameter($this->namespace . $param, $value);
        }

        foreach ($definitions AS $serviceName => $def) {
            $serviceId = $this->convertServiceName($serviceName);
            foreach ($def[Yadif_Container::CONFIG_PARAMETERS] AS $param => $value) {
                $param = strtolower(substr($param, 1)); // remove the starting :
                $this->container->setParameter($serviceId . "." . $param, $value);
            }

            $arguments = array();
            foreach ($def[Yadif_Container::CONFIG_ARGUMENTS] AS $arg) {
                if (strpos($arg, ":") === 0) {
                    $param = strtolower(substr($arg, 1));
                    if ($this->container->hasParameter($serviceId.".".$param)) {
                        $arguments[] = '%'.$serviceId.".".$param.'%';
                    } else {
                        $arguments[] = '%'.$this->namespace.$param.'%';
                    }
                } else {
                    $arguments[] = new Reference($this->convertServiceName($arg));
                }
            }

            $this->container->setParameter($serviceId.'.class', $def[Yadif_Container::CONFIG_CLASS]);
            $definition = new Definition('%' .$serviceId.'.class%', $arguments);
            if ($def[Yadif_Container::CONFIG_SCOPE] == Yadif_Container::SCOPE_PROTOTYPE) {
                $definition->setScope('prototype');
            }

            foreach ($def[Yadif_Container::CONFIG_METHODS] AS $method) {
                $methodName = $method[Yadif_Container::CONFIG_METHOD];
                foreach ($method[Yadif_Container::CONFIG_PARAMETERS] AS $param => $value) {
                    $param = strtolower(substr($param, 1)); // remove the starting :
                    $this->container->setParameter($serviceId . ".".strtolower($methodName).".".$param, $value);
                }

                $callArguments = array();
                foreach ($def[Yadif_Container::CONFIG_ARGUMENTS] AS $arg) {
                    if (strpos($arg, ":") === 0) {
                        $param = strtolower(substr($arg, 1));
                        if ($this->container->hasParameter($serviceId . ".".strtolower($methodName).".".$param)) {
                            $callArguments[] = '%'.$serviceId . ".".strtolower($methodName).".".$param.'%';
                        } else if ($this->container->hasParameter($serviceId.".".$param)) {
                            $callArguments[] = '%'.$serviceId.".".$param.'%';
                        } else {
                            $callArguments[] = '%'.$this->namespace.$param.'%';
                        }
                    } else {
                        $callArguments[] = new Reference($this->convertServiceName($arg));
                    }
                }
                $definition->addMethodCall($methodName, $callArguments);
            }

            if (isset($def[Yadif_Container::CONFIG_FACTORY])) {
                $definition->setClass($def[Yadif_Container::CONFIG_FACTORY][0]);
                $definition->setFactoryMethod($def[Yadif_Container::CONFIG_FACTORY][1]);
            }

            $this->container->setDefinition($this->convertServiceName($serviceName), $definition);
        }
    }

    private function convertServiceName($yadifService)
    {
        return $this->namespace . str_replace(array("_", "\\"), ".", strtolower($yadifService));
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param  mixed $resource A resource
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    function supports($resource)
    {
        return false;
    }
}