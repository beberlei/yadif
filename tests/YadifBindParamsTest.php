<?php

require_once dirname(__FILE__)."/../Container.php";
require_once "Fixture.php";
require_once "PHPUnit/Framework.php";

class YadifBindParamsTest extends PHPUnit_Framework_TestCase
{
    public function testGetParamReturnsNullByDefault()
    {
        $yadif = new Yadif_Container();
        $this->assertNull($yadif->getParam('invalid'));
    }

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

    public function testSetGetParameters()
    {
        $yadif = new Yadif_Container();
        $yadif->bindParam(":foo", "bar");

        $this->assertEquals(array(":foo" => "bar"), $yadif->getParameters());
    }

    public function testSetMultipleParams()
    {
        $yadif = new Yadif_Container();
        $yadif->bindParams(array(":foo" => "bar", ":bar" => "baz"));

        $this->assertEquals(array(":foo" => "bar", ":bar" => "baz"), $yadif->getParameters());
    }

    public function testGetParam()
    {
        $yadif = new Yadif_Container();
        $yadif->bindParam(":foo", 'bar');

        $this->assertEquals("bar", $yadif->getParam(":foo"));
    }

    public function testGetComponentRetrieveBoundParamThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->bindParam(":foo", "bar");

        $yadif->getComponent(":foo");
    }

    public function testGetComponentWithBoundParam()
    {
        $config = array(
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array(':foo'),
            )
        );
        $yadif = new Yadif_Container($config);
        $yadif->bindParam(":foo", "bar");

        $component = $yadif->getComponent("YadifBar");
        $this->assertEquals("bar", $component->a);
    }

    public function testBindParamsNotArrayThrowsException()
    {
        $this->setExpectedException("Yadif_Exception");

        $yadif = new Yadif_Container();
        $yadif->bindParams("notArray");
    }

    public function testBindConstructorParamInConfigurationThroughParametersKey()
    {
        $config = array(
            'YadifBar' => array(
                'class'     => 'YadifBar',
                'arguments' => array(':foo'),
                'params' => array(':foo' => 'bar'),
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifBar");
        $this->assertEquals("bar", $component->a);
    }

    public function testBindMethodParamInConfigurationThroughParametersKey()
    {
        $config = array(
            'YadifBaz' => array(
                'class'     => 'YadifBaz',
                'methods' => array(
                    array(
                        'method' => 'setA',
                        'arguments' => array(':foo'),
                        'params' => array(':foo' => 'bar'),
                    ),
                ),
            )
        );
        $yadif = new Yadif_Container($config);

        $component = $yadif->getComponent("YadifBaz");
        $this->assertEquals("bar", $component->a);
    }
}