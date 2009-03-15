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
                'arguments' => array('YadifBaz')
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
                'arguments' => array('YadifBaz', 'YadifBar'),
            ),
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array('YadifBaz')
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
                'methods' => array(
                    array(
                        'method' => 'setA',
                        'arguments' => array('stdClass'),
                    ),
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

    public function testInvalidArgumentToGetComponentReturnsThatValueWithArray()
    {
        $yadif = new Yadif_Container();
        $this->assertEquals(array(), $yadif->getComponent(array()));
    }

    public function testInvalidArgumentToGetComponentReturnsThatValueWithInteger()
    {
        $yadif = new Yadif_Container();
        $this->assertEquals(1234, $yadif->getComponent(1234));
    }

    public function testGetNotRegisteredComponentThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->getComponent("asdf");
    }

    public function testCallMethodWithoutArguments()
    {
        $config = array(
            'YadifNoArguments' => array(
                'methods' => array(
                    array('method' => 'init')
                 ),
            ),
        );

        $yadif = new Yadif_Container($config);
        $component = $yadif->getComponent('YadifNoArguments');

        $this->assertTrue($component->called);
    }

    public function testUsingConstructorNameInMethodsListThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $config = array(
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array('YadifBaz'),
                'methods' => array(
                    array('method' => '__construct'),
                )
            ),
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
            )
        );

        $yadif = new Yadif_Container($config);
        $component = $yadif->getComponent('YadifBar');
    }

    public function testNotSpecifyingMethodNameInConfigurationThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $config = array(
            'YadifNoArguments' => array(
                'methods' => array(
                    array('arguments' => array())
                 ),
            ),
        );

        $yadif = new Yadif_Container($config);
        $component = $yadif->getComponent('YadifNoArguments');
    }

    public function testSpecifyingMethodArgumentsNotArrayThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $config = array(
            'YadifNoArguments' => array(
                'methods' => array(
                    array('method' => 'init', 'arguments' => "invalidString")
                 ),
            ),
        );

        $yadif = new Yadif_Container($config);
        $component = $yadif->getComponent('YadifNoArguments');
    }

    public function testSpecifiyingInvalidCallbackForFactoryThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $config = array(
            'YadifFoo' => array(
                'class'     => 'YadifFoo',
                'factory'   => array('YadifFactory', 'invalidUnknownMethod'),
            ),
        );

        $yadif = new Yadif_Container($config);
        $foo = $yadif->getComponent('YadifFoo');
    }

    public function testSpecifyingArgumentsComponentsToCallbackFactoryRetrievesThroughContainer()
    {
        YadifFactory::$factoryCalled = false;
        $config = array(
            'YadifBaz' => array('class' => 'YadifBaz'),
            'YadifFoo' => array(
                'class'     => 'YadifFoo',
                'factory'   => array('YadifFactory', 'createFoo'),
                'arguments' => array('YadifBaz', 'YadifBaz'),
            ),
        );

        $yadif = new Yadif_Container($config);
        $foo = $yadif->getComponent('YadifFoo');

        $this->assertTrue($foo->a instanceof YadifBaz);
        $this->assertTrue($foo->b instanceof YadifBaz);
    }
}