<?php

class YadifFoo
{
    public $a = null;
    public $b = null;
    public $c = null;

    public function __construct($a=null, $b=null)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function setC($c)
    {
        $this->c = $c;
    }
}

class YadifBar
{
    public $a = null;

    public function __construct($a)
    {
        $this->a = $a;
    }
}

class YadifBaz
{
    public $a = null;

    public function setA($a)
    {
        $this->a = $a;
    }
}