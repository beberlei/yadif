<?php

class Yadif_Tests_SymfonyYadifLoaderTest extends PHPUnit_Framework_TestCase
{

    public function testLoad()
    {
        $config = array(
            'YadifFoo' => array(
                'class' => 'YadifFoo',
                'arguments' => array('YadifBaz', 'YadifBar'),
            ),
            'YadifBar' => array(
                'class' => 'YadifBar',
                'arguments' => array('YadifBaz')
            ),
            'YadifBaz' => array(
                'class' => 'YadifBaz',
            )
        );
        $yadif = new Yadif_Container($config);

        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $loader = new Yadif_Loader_YadifLoader($container);
        $loader->load($yadif);
    }

}