<?php
/**
 * Yadif
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tsmckelvey@gmail.com so I can send you a copy immediately.
 */

require_once 'Exception.php';

/**
 * Yadif_Container
 *
 * @category   Yadif
 * @copyright  Copyright (c) 2008 Thomas McKelvey
 */
class Yadif_Container
{
    /**
     * Identifier for singleton scope
     */
    const SCOPE_SINGLETON = "singleton";

    /**
     * Identifier for prototype scope (new object each call)
     */
    const SCOPE_PROTOTYPE = "prototype";

	/**
	 * Char we prefix string arguments with to identify as strings
	 */
	const STRING_IDENTIFIER = '"';

	/**
	 * Char to preserve index of identical method calls
	 */
	const METHOD_TRIM_CHAR = '#';

	/**
	 * Class index key of component $config
	 */
	const CONFIG_CLASS = 'class';

	/**
	 * Arguments index key of component $config
	 */
	const CONFIG_ARGUMENTS = 'arguments';

    /**
     * Singleton index key of component $config
     */
    const CONFIG_SCOPE = 'scope';

    /**
     * Setter Methods to call config index key
     */
    const CONFIG_METHODS = 'methods';

    /**
     * Method Name
     */
    const CONFIG_METHOD = 'method';

	/**
     * container of component configurations
     *
     * @var array
     */
	protected $_container = array();

	/**
     * parameters which have been set, expected to be bound
     *
     * @var array
     */
	protected $_parameters = array();

    /**
     * singletons
     *
     * @var array
     */
    protected $_singletons = array();

    /**
     * Construct Dependency Injection Container
     *
     * @param array $config
     */
	public function __construct(array $config = array())
	{
		if (isset($config) && is_array($config)) {
			foreach ($config as $componentName => $componentConfig) {
				$this->addComponent( $componentName, $componentConfig );
			}
		}
	}

	/**
	 * Create a container
	 *
	 * @param array|string A configuration array or the filename of a PHP 
	 * file that returns a configuration array
	 */
	static public function create($config = array())
	{
		if (is_string($config)) {
			$filename = (substr($config, -4, 4) === '.php') ? $config : $config . '.php';
			if (substr($filename, -13, 13) === 'Container.php') {
                $config = include($filename);
                if(!is_array($config)) {
                    throw new Yadif_Exception('Container Config File '.$filename.' does not return an array.');
                }
				return new Yadif_Container($config);
			} else {
				throw new Yadif_Exception("$filename not file");
			}
		} else if (is_array($config)) {
			return new Yadif_Container($config);
		}

		throw new Yadif_Exception('$config must be string or array, is ' . gettype($config));
	}

	/**
	 * Getter method for internal array of component configurations
	 *
	 * @return array
	 */
	public function getContainer()
	{
		return $this->_container;
	}

	/**
	 * Getter method for internal array of parameters
	 * 
	 * @return array
	 */
	public function getParameters()
	{
		return $this->_parameters;
	}
	
	/**
	 * Add a component to the container
	 *
	 * If $config is omitted, $name is assumed to be the classname.
	 *
	 * @param string|Yadif_Container $name Class-tag, class, or Yadif_Container
	 * @param array $config Array of configuration values
	 * @return Yadif_Container
	 */
	public function addComponent($name = null, array $config = null)
	{
		if ($name instanceof Yadif_Container) { // if Yadif_Container
			$foreignContainer = $name->getContainer();

			// merge containers, @TODO: duplicates
			$this->_container = array_merge($this->_container, $foreignContainer);
		} elseif(is_string($name)) {
            if (!is_array($config) || !isset($config[self::CONFIG_CLASS])) { // assume name is the class name
                $config[self::CONFIG_CLASS] = $name;
            }

            if (!isset($config[self::CONFIG_ARGUMENTS]) || !is_array($config[self::CONFIG_ARGUMENTS])) {
                $config[ self::CONFIG_ARGUMENTS ] = array();
            }

            // if class is set and doesn't exist
            if (!class_exists( $config[self::CONFIG_CLASS] )) {
                throw new Yadif_Exception( 'Class ' . $config[self::CONFIG_CLASS] . ' not found' );
            }

            // check for singleton config parameter and set it to true as default if not found.
            if(!isset($config[self::CONFIG_SCOPE])) {
                $config[self::CONFIG_SCOPE] = self::SCOPE_SINGLETON;
            }

            if(!isset($config[self::CONFIG_METHODS])) {
                $config[self::CONFIG_METHODS] = array();
            }

            $this->_container[ $name ] = $config;
        } else {
            throw new Yadif_Exception('$string not string|Yadif_Container, is ' . gettype($name));
        }

		return $this;
	}

	/**
	 * Bind a parameter
	 *
	 * @param string $param The parameter name, to be given with a leading colon ":param"
	 * @param mixed $value The value to bind to the parameter
	 * @return Yadif_Container
	 */
	public function bindParam($param, $value)
	{
		if (!is_string($param)) {
            throw new Yadif_Exception('$param not string, is ' . gettype($param));
        }

		if ($param{0} != ':') {
			throw new Yadif_Exception($param . ' must start with a colon (:)');
        }

		$this->_parameters[$param] = $value;

		return $this;
	}

	/**
	 * Retrieve a parameter by name
	 *
	 * @param mixed $param Retrieve named parameter
	 * @return mixed
	 */
	public function getParam($param)
	{
		if (isset($this->_parameters[$param])) {
			return $this->_parameters[$param];
		}

		return false;
	}

	/**
	 * Bind multiple parameters by way of array
	 * @param array $params Array of parameters, key as param to bind, value as the bound value
	 * @return Yadif_Container
	 */
	public function bindParams($params = null)
	{
		if (!is_array($params)) {
			throw new Yadif_Exception('$params must be array, is ' . gettype($params));
        }

		foreach ($params as $param => $value) {
			$this->bindParam($param, $value);
		}

		return $this;
	}

	/**
	 * Return string minus identifier
	 * @param string $string
	 * @return string
	 */
	protected function _parseString($string)
	{
		return ltrim($string, self::STRING_IDENTIFIER);
	}

	/**
	 * Get back a fully assembled component based on the configuration provided beforehand
	 *
	 * @param mixed $name The name of the component
	 * @return mixed 
	 */
	public function getComponent($name)
	{
		if (is_array($name)) {
			foreach ($name as $k =>$value) {
                $name[$k] = $this->getComponent($value);
            }
			return $name;
		}

		if (!is_string($name)) {
            throw new Yadif_Exception('$name not string, is ' . gettype($name));
        }

		// if is string
		if($name[0] === self::STRING_IDENTIFIER) {
            return $this->_parseString($name);
        }

		// if we're trying to "getParameter" (see the loop below)
		if (array_key_exists($name, $this->_parameters)) {
			return $this->getParam($name);
        }

		if (!array_key_exists($name, $this->_container)) {
			throw new Yadif_Exception("'$name' not in " . '$this->_container or $this->_parameters');
        }

		$component = $this->_container[ $name ];
        $scope = $component[self::CONFIG_SCOPE];
        if($scope == self::SCOPE_SINGLETON && isset($this->_singletons[$name])) {
            return $this->_singletons[$name];
        }

		$componentReflection = new ReflectionClass($component[ self::CONFIG_CLASS ]);

		$constructorArguments = $component[self::CONFIG_ARGUMENTS];
        $setterMethods        = $component[self::CONFIG_METHODS];

		if (empty($constructorArguments)) { // if no instructions
			$component = $componentReflection->newInstance();
        } else {
            $constructorInjection = $this->getComponent($constructorArguments);
            $component = $componentReflection->newInstanceArgs($constructorInjection);
            unset($constructorInjection);
        }

        foreach ($setterMethods as $method) {
            if(!isset($method[self::CONFIG_METHOD])) {
                throw new Yadif_Exception("No method name was set for injection via method.");
            }
            $methodName = $method[self::CONFIG_METHOD];
            
            $injection = array();
            if(isset($method[self::CONFIG_ARGUMENTS])) {
                $argsName = $method[self::CONFIG_ARGUMENTS];
                if(!is_array($argsName)) {
                    throw new Yadif_Exception("Argument names for method injection '".$methodName."' have to an array.");
                }

                $injection = $this->getComponent($argsName);
            }

            if ($componentReflection->getMethod($methodName)->isConstructor()) {
                throw new Yadif_Exception("Cannot use constructor in 'methods' setter injection list. Use 'arguments' key instead.");
            } else {
                if(count($injection) == 0) {
                    $componentReflection->getMethod($methodName)->invoke($component);
                } else {
                    $componentReflection->getMethod($methodName)->invokeArgs($component, $injection);
                }
            }
        }

        if($scope == self::SCOPE_SINGLETON) {
            $this->_singletons[$name] = $component;
        }

		return $component;
	}

    /**
     * Magic Get accesses the {@link getComponent} function and returns the object.
     *
     * @param  string $component
     * @throws Yadif_Exception
     * @return object
     */
    public function __get($name)
    {
        return $this->getComponent($name);
    }

    /**
     * Call allows to initiate requests to get[ComponetName] and links against {@link getComponent}.
     *
     * @param  string $method
     * @param  array  $arguments
     * @throws Yadif_Exception
     * @return object
     */
    public function __call($method, $args)
    {
        if(!substr($method, 0, 3) !== "get") {
            throw new Yadif_Exception("Container __call only intercepts get[ComponetName]() like calls.");
        }
        $component = substr($method, 3);
        return $this->getComponent($component);
    }
}
