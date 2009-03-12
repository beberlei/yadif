<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifConfigComponentTest extends PHPUnit_Framework_TestCase
{
    public function testStaticCreateWithArray()
    {
        $config = array(
            'YadifBaz' => array('class' => 'YadifBaz', 'arguments' => array(), 'scope' => Yadif_Container::SCOPE_SINGLETON, 'methods' => array()),
        );
        $yadif = Yadif_Container::create($config);
        $this->assertEquals($config, $this->readAttribute($yadif, '_container'));
    }

    public function testStaticCreateWithEmptyArray()
    {
        $yadif = Yadif_Container::create();
        $this->assertEquals(array(), $this->readAttribute($yadif, '_container'));
    }

    public function testCreateNewContainerWithEmptyArray()
    {
        $yadif = new Yadif_Container();
        $this->assertEquals(array(), $this->readAttribute($yadif, '_container'));
    }

    public function testStaticCreateWithInvalidInput()
    {
        $this->setExpectedException('Yadif_Exception');
        $yadif = Yadif_Container::create(true);
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
}