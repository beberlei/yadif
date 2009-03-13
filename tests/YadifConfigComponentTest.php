<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifConfigComponentTest extends PHPUnit_Framework_TestCase
{
    public function testStaticCreateWithEmptyArray()
    {
        $yadif = new Yadif_Container();
        $this->assertEquals(array(), $this->readAttribute($yadif, '_container'));
    }

    public function testCreateNewContainerWithEmptyArray()
    {
        $yadif = new Yadif_Container();
        $this->assertEquals(array(), $this->readAttribute($yadif, '_container'));
    }

    public function testAddComponent()
    {
        $yadif = new Yadif_Container();
        $yadif->addComponent("YadifBaz", array("class" => "YadifBaz", "arguments" => array(), 'scope' => Yadif_Container::SCOPE_SINGLETON, 'methods' => array()));

        $expected = array("YadifBaz" => array("class" => "YadifBaz", "arguments" => array(), 'scope' => Yadif_Container::SCOPE_SINGLETON, 'methods' => array()));
        $this->assertEquals($expected, $this->readAttribute($yadif, '_container'));
    }

    public function testAddComponentWithoutClassAndArgumentsHasDefaultValues()
    {
        $yadif = new Yadif_Container();
        $yadif->addComponent("YadifBaz");

        $expected = array("YadifBaz" => array("class" => "YadifBaz", "arguments" => array(), 'scope' => Yadif_Container::SCOPE_SINGLETON, 'methods' => array()));
        $this->assertEquals($expected, $this->readAttribute($yadif, '_container'));
    }

    public function testAddComponentRequiresContainerOrString()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->addComponent(array());
    }

    public function testAddComponentWithNonExistantClassRaisesException()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->addComponent("YadifFoo", array("class" => "NonExistantYadifClass"));
    }

    public function testGetComponentOnEmptyContainer()
    {
        $yadif = new Yadif_Container();
        $this->assertEquals(array(), $yadif->getContainer());
    }

    public function testGetComponent()
    {
        $yadif = new Yadif_Container();
        $yadif->addComponent("stdClass");
        
        $expected = array("stdClass" => array("class" => "stdClass", "arguments" => array(), 'scope' => Yadif_Container::SCOPE_SINGLETON, 'methods' => array()));
        $this->assertEquals($expected, $yadif->getContainer());
    }
    
    public function testAddComponentWithContainerIsMerging()
    {
        $yadif1 = new Yadif_Container();
        $yadif1->addComponent('stdClass');

        $yadif2 = new Yadif_Container();
        $yadif2->addComponent($yadif1);

        $expected = array("stdClass" => array("class" => "stdClass", "arguments" => array(), 'scope' => Yadif_Container::SCOPE_SINGLETON, 'methods' => array()));
        $this->assertEquals($expected, $this->readAttribute($yadif2, '_container'));
    }

    public function testGetComponentFromStaticFactory()
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

        $this->assertTrue(YadifFactory::$factoryCalled);
    }

    public function testGetComponentMagicGet()
    {
        $config = array(
            'YadifBaz' => array('class' => 'YadifBaz'),
        );

        $yadif = new Yadif_Container($config);
        $this->assertTrue($yadif->YadifBaz instanceof YadifBaz);
    }

    public function testGetComponentMagicCall()
    {
        $config = array(
            'YadifBaz' => array('class' => 'YadifBaz'),
        );

        $yadif = new Yadif_Container($config);
        $this->assertTrue($yadif->getYadifBaz() instanceof YadifBaz);
    }

    public function testMagicCallWhichIsNotGet()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->someInvalidCall();
    }
}