<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifBindParamsTest extends PHPUnit_Framework_TestCase
{
    public function testParamNameMustBeString()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->bindParam(array(), "foo");
    }

    public function testParamNameMustStartWithAColonException()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->bindParam("foo", "bar");
    }

    public function setGetParameters()
    {
        $yadif = new Yadif_Container();
        $yadif->bindParam(":foo", "bar");

        $this->assertEquals(array(":foo" => "bar"), $yadif->getParameters());
    }

    public function testGetParam()
    {
        $yadif = new Yadif_Container();
        $yadif->bindParam(":foo", 'bar');

        $this->assertEquals("bar", $yadif->getParam(":foo"));
    }

    public function testGetComponentRetrieveBoundParam()
    {
        $yadif = new Yadif_Container();
        $yadif->bindParam(":foo", "bar");

        $this->assertEquals("bar", $yadif->getComponent(":foo"));
    }

    public function testGetComponentWithBoundParam()
    {
        $config = array(
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array(
                    '__construct' => array(':foo')
                ),
            )
        );
        $yadif = new Yadif_Container($config);
        $yadif->bindParam(":foo", "bar");

        $component = $yadif->getComponent("YadifBar");
        $this->assertEquals("bar", $component->a);
    }
}