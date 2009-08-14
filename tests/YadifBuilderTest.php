<?php

class YadifBuilderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new Yadif_Builder();
    }

    public function testFinalizeOfEmptyBuilder_ReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->builder->finalize());
    }

    public function testBind()
    {
        $this->builder->bind('stdClass');

        $this->assertBuilder(array('stdClass' => array('class' => 'stdClass')));
    }

    public function testBindTo()
    {
        $this->builder->bind('stdClass')->to('stdObject');

        $this->assertBuilder(array('stdClass' => array('class' => 'stdObject')));
    }

    public function testBindToArgs()
    {
        $this->builder->bind('stdClass')->to('stdObject')->args('foo', 'bar', 'baz');

        $this->assertBuilder(array('stdClass' => array('class' => 'stdObject', 'arguments' => array('foo', 'bar', 'baz'))));
    }

    public function testBindToArgsParam()
    {
        $this->builder->bind('stdClass')->to('stdObject')->args(':foo')->param(':foo', 'bar');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdObject',
                    'arguments' => array(':foo'),
                    'parameters' => array(':foo' => 'bar'),
                )
            )
        );
    }

    public function testBindMethod()
    {
        $this->builder->bind('stdClass')->method('setFoo');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'methods' => array(
                        array('method' => 'setFoo')
                     ),
                )
            )
        );
    }

    public function testBindMethodMethod()
    {
        $this->builder->bind('stdClass')->method('setFoo')->method('setFoo');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'methods' => array(
                        array('method' => 'setFoo'),
                        array('method' => 'setFoo'),
                     ),
                )
            )
        );
    }

    public function testBindMethodArgs()
    {
        $this->builder->bind('stdClass')->method('setFoo')->args('foo', 'bar');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'methods' => array(
                        array('method' => 'setFoo', 'arguments' => array('foo', 'bar')),
                     ),
                )
            )
        );
    }


    public function testBindMethodArgsMethodArgs()
    {
        $this->builder->bind('stdClass')->method('setFoo')->args('foo', 'bar')->method('setBar')->args('baz');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'methods' => array(
                        array('method' => 'setFoo', 'arguments' => array('foo', 'bar')),
                        array('method' => 'setBar', 'arguments' => array('baz')),
                     ),
                )
            )
        );
    }


    public function testBindMethodArgsParam()
    {
        $this->builder->bind('stdClass')->method('setFoo')->args(':foo')->param(':foo', 'bar');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'methods' => array(
                        array('method' => 'setFoo', 'arguments' => array(':foo'), 'parameters' => array(':foo' => 'bar')),
                     ),
                )
            )
        );
    }

    public function testBindArgsParamsMethodArgsParams()
    {
        $this->builder->bind('stdClass')
            ->args(':foo')->param(':foo', 'baz')
            ->method('setFoo')->args(':foo')->param(':foo', 'bar');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'arguments' => array(':foo'),
                    'parameters' => array(':foo' => 'baz'),
                    'methods' => array(
                        array('method' => 'setFoo', 'arguments' => array(':foo'), 'parameters' => array(':foo' => 'bar')),
                     ),
                )
            )
        );
    }

    public function testBindToProvider()
    {
        $this->builder->bind('stdClass')->toProvider(array('foo', 'bar'));

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'factory' => array('foo', 'bar'),
                )
            )
        );
    }

    public function testBindScopeSingleton()
    {
        $this->builder->bind('stdClass')->scope('singleton');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'scope' => 'singleton',
                )
            )
        );
    }

    public function testBindScopePrototype()
    {
        $this->builder->bind('stdClass')->scope('prototype');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'scope' => 'prototype',
                )
            )
        );
    }

    public function testChainBind()
    {
        $this->builder->bind('stdClass')->bind('stdObject');

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                ),
                'stdObject' => array(
                    'class' => 'stdObject',
                ),
            )
        );
    }

    public function testChainBindMethodArgs()
    {
        $this->builder->bind('stdClass')->args(1)->method('foo')->args(1, 2)
                      ->bind('stdObject')->args(1)->method('foo')->args(1, 2);

        $this->assertBuilder(
            array(
                'stdClass' => array(
                    'class' => 'stdClass',
                    'arguments' => array(1),
                    'methods' => array(
                        array('method' => 'foo', 'arguments' => array(1, 2))
                    ),
                ),
                'stdObject' => array(
                    'class' => 'stdObject',
                    'arguments' => array(1),
                    'methods' => array(
                        array('method' => 'foo', 'arguments' => array(1, 2))
                    ),
                ),
            )
        );
    }

    public function assertBuilder($expectedConfig)
    {
        $this->assertEquals($expectedConfig, $this->builder->finalize());
    }
}