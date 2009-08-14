<?php

class YadifModuleTest extends PHPUnit_Framework_TestCase
{
    public function testModuleReturnsBuilderOnBind()
    {
        $module = new Yadif_Module;
        $this->assertType('Yadif_Builder', $module->bind('foo'));
    }

    public function testModuleOnlyUsesOneBuilder()
    {
        $module = new Yadif_Module;
        $builderA = $module->bind('foo');
        $builderB = $module->bind('bar');

        $this->assertSame($builderA, $builderB);
    }

    public function testGetConfig_FinalizesBuilder()
    {
        $module = new Yadif_Module;

        $this->assertEquals(array(), $module->getConfig());

        $module->bind('Foo');

        $this->assertEquals(array('Foo' => array('class' => 'Foo')), $module->getConfig());
    }
}