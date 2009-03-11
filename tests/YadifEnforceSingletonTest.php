<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifEnforceSingletonTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateObjectGraphWithNestedSingleton()
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
                'singleton' => true,
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifFoo");
        $this->assertTrue($component->a === $component->b, 'Enforcing singleton of object did not work!');
    }
}