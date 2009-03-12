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
                'class' => 'YadifFoo',
                'arguments' => array(
                    '__construct' => array('YadifBaz', 'YadifBaz'),
                ),
            ),
            'YadifBaz' => array(
                'class' => 'YadifBaz',
                'scope' => Yadif_Container::SCOPE_SINGLETON,
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
                'class' => 'YadifBaz',
                'scope' => Yadif_Container::SCOPE_SINGLETON,
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
                'class' => 'YadifFoo',
                'arguments' => array(
                    '__construct' => array('YadifBaz', 'YadifBaz'),
                ),
            ),
            'YadifBaz' => array(
                'class' => 'YadifBaz',
                'scope' => Yadif_Container::SCOPE_PROTOTYPE,
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifFoo");
        $this->assertFalse($component->a === $component->b, 'Not Enforcing singleton of object did not work!');
    }
}