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
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @author     Thomas McKelvey (http://github.com/tsmckelvey/yadif/tree/master)
 * @author     Benjamin Eberlei (http://github.com/beberlei/yadif/tree/master)
 */
class Yadif_Builder
{
    /**
     * @var array
     */
    private $_config = array();

    /**
     * @var string
     */
    private $_lastMethodKey = null;

    /**
     * @var string
     */
    private $_lastComponentName = null;

    /**
     * @return array
     */
    public function finalize()
    {
        return $this->_config;
    }

    /**
     * Specify an Interface/Component Name which is used for querying the Container for an Implementation.
     *
     * @param  string $componentName
     * @return Yadif_Builder
     */
    public function bind($componentName)
    {
        $this->_lastMethodKey = null;
        $this->_lastComponentName = $componentName;
        $this->_config[$componentName] = array('class' => $componentName);
        return $this;
    }

    /**
     * Specify concrete implementation class name of the previously given Interface/Component Name
     *
     * @param  string $componentName
     * @return Yadif_Builder
     */
    public function to($className)
    {
        $this->_config[$this->_lastComponentName]['class'] = $className;
        return $this;
    }

    /**
     * Specifiy Callback which creates a concrete implementation of the previously given Interface/Component Name
     *
     * @param  callback $factoryCallback
     * @return Yadif_Builder
     */
    public function toProvider($factoryCallback)
    {
        $this->_config[$this->_lastComponentName]['factory'] = $factoryCallback;
        return $this;
    }

    /**
     * Specify arguments that are given to the constructor of the chosen implementation.
     *
     * Parameters are only allowed to be given in the format ':paramName' and then be specified
     * by the {@link param()} method.
     *
     * @return Yadif_Builder
     */
    public function args()
    {
        $args = func_get_args();
        if ($this->_lastMethodKey === null) {
            $this->_config[$this->_lastComponentName]['arguments'] = $args;
        } else {
            $this->_config[$this->_lastComponentName]['methods'][$this->_lastMethodKey]['arguments'] = $args;
        }
        return $this;
    }

    /**
     * Bind Parameter Name to a specific value
     *
     * @param  string $paramName
     * @param  mixed $paramValue
     * @return Yadif_Builder
     */
    public function param($paramName, $paramValue)
    {
        if($this->_lastMethodKey === null) {
            if(!isset($this->_config[$this->_lastComponentName]['params'])) {
                $this->_config[$this->_lastComponentName]['params'] = array();
            }
            $this->_config[$this->_lastComponentName]['params'][$paramName] = $paramValue;
        } else {
            if(!isset($this->_config[$this->_lastComponentName]['methods'][$this->_lastMethodKey]['params'])) {
                $this->_config[$this->_lastComponentName]['methods'][$this->_lastMethodKey]['params'] = array();
            }
            $this->_config[$this->_lastComponentName]['methods'][$this->_lastMethodKey]['params'][$paramName] = $paramValue;
        }
        return $this;
    }

    /**
     * Specifiy method to be called on the implementation instance after creation.
     *
     * @param  string $methodName
     * @return Yadif_Builder
     */
    public function method($methodName)
    {
        $this->_config[$this->_lastComponentName]['methods'][] = array('method' => $methodName);
        $this->_lastMethodKey = count($this->_config[$this->_lastComponentName]['methods'])-1;
        return $this;
    }

    /**
     * Specify the scope of the implementation 'singleton' or 'prototype'.
     *
     * @param  string $scope
     * @return Yadif_Builder
     */
    public function scope($scope)
    {
        $this->_config[$this->_lastComponentName]['scope'] = $scope;
        return $this;
    }

    /**
     *
     * @param  string $decoratorComponentName
     * @return Yadif_Builder
     */
    public function decorateWith($decoratorComponentName)
    {
        if (!isset($this->_config[$this->_lastComponentName]['decorateWith'])) {
            $this->_config[$this->_lastComponentName]['decorateWith'] = array();
        }
        $this->_config[$this->_lastComponentName]['decorateWith'][] = $decoratorComponentName;
        return $this;
    }
}