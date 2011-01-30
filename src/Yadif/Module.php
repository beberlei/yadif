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
class Yadif_Module
{
    /**
     * @var bool
     */
    private $_isConfigured = false;

    /**
     * @var Yadif_Builder
     */
    private $_builder = null;

    public function __construct()
    {
        $this->_builder = new Yadif_Builder();
    }

    /**
     * Specify an Interface/Component Name which is used for querying the Container for an Implementation.
     *
     * @param  string $componentName
     * @return Yadif_Builder
     */
    public function bind($componentName)
    {
        return $this->_builder->bind($componentName);
    }

    /**
     * @return array
     */
    final public function getConfig()
    {
        if($this->_isConfigured == false) {
            $this->_isConfigured = true;
            $this->configure();
        }
        return $this->_builder->finalize();
    }

    protected function configure()
    {

    }
}