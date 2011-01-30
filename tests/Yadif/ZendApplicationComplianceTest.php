<?php

class Yadif_Tests_ZendApplicationComplianceTest extends PHPUnit_Framework_TestCase
{
    public function testSetInstanceViaMagicSet()
    {
        $object = new stdClass();

        $yadif = new Yadif_Container();
        $yadif->stdClass = $object;

        $this->assertSame($object, $yadif->stdClass);
    }

    public function testIssetInstance()
    {
        $object = new stdClass();
        $yadif = new Yadif_Container();

        $this->assertFalse(isset($yadif->stdClass));
        $yadif->stdClass = $object;
        $this->assertTrue(isset($yadif->stdClass));
    }

    public function testIssetComponent()
    {
        $yadif = new Yadif_Container(array('stdClass' => array('class' => 'stdClass')));
        $this->assertTrue(isset($yadif->stdClass));
    }
}