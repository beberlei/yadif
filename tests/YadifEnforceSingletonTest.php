<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifEnforceSingletonTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateObjectGraphWithNestedSingletonAsDefaultBehaviour()
    {
        $config = array(
            'YadifFoo' => array(
                'class'     => 'YadifFoo',
                'scope'     => Yadif_Container::SCOPE_SINGLETON,
                'arguments' => array('YadifBaz', 'YadifBaz'),
                'methods'   => array(),
            ),
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
                'scope'     => Yadif_Container::SCOPE_SINGLETON,
                'arguments' => array(),
                'methods'   => array(),
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifFoo");
        $this->assertTrue($component->a === $component->b, 'Enforcing singleton of object did not work!');
    }

    public function testMultipleFetchesOfSameSingletonObjectReturnSameReference()
    {
        $config = array(
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
                'scope'     => Yadif_Container::SCOPE_SINGLETON,
                'arguments' => array(),
                'methods'   => array(),
            )
        );
        $yadif = new Yadif_Container($config);

        $component1 = $yadif->getComponent("YadifBaz");
        $component2 = $yadif->getComponent("YadifBaz");

        $this->assertTrue($component1 === $component2, 'Enforcing singleton of object did not work!');
    }

    public function testInstantiateExplicitlyDisabledSingletonLeafes()
    {
        $config = array(
            'YadifFoo' => array(
                'class'     => 'YadifFoo',
                'scope'     => Yadif_Container::SCOPE_SINGLETON,
                'arguments' => array('YadifBaz', 'YadifBaz'),
                'methods'   => array(),
            ),
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
                'scope'     => Yadif_Container::SCOPE_PROTOTYPE,
                'arguments' => array(),
                'methods'   => array(),
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifFoo");
        $this->assertFalse($component->a === $component->b, 'Not Enforcing singleton of object did not work!');
    }
}