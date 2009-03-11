<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifInstantiateObjectGraphTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateObjectWithoutDependencies()
    {
        $config = array('YadifBaz' => array('class' => 'YadifBaz'));
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifBaz");
        $this->assertTrue($component instanceof YadifBaz);
    }

    public function testInstantiateObjectWithSingleDependency()
    {
        $config = array(
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array(
                    '__construct' => array('YadifBaz')
                )
            ),
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent('YadifBar');
        $this->assertTrue($component->a instanceof YadifBaz);
    }

    public function testInstantiateObjectWithMultipleDependencies()
    {
        $config = array(
            'YadifFoo' => array(
                'class'     => 'YadifFoo',
                'arguments' => array(
                    '__construct' => array('YadifBaz', 'YadifBar'),
                ),
            ),
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array(
                    '__construct' => array('YadifBaz')
                )
            ),
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent('YadifFoo');
        $this->assertTrue($component->a instanceof YadifBaz);
        $this->assertTrue($component->b instanceof YadifBar);
        $this->assertTrue($component->b->a instanceof YadifBaz);
    }

    public function testInstantiateObjectWithSetterDependency()
    {
        $config = array(
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
                'arguments' => array(
                    'setA' => array('stdClass'),
                ),
            ),
            'stdClass' => array('class' => 'stdClass'),
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent('YadifBaz');
        $this->assertTrue($component->a instanceof stdClass);
    }

    public function testRecursiveDependencyIsDetectedByException()
    {
        $this->markTestIncomplete('Recursive dependency detection not implemented yet.');
    }
}