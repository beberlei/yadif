<?php

require_once __DIR__ . "/../../vendor/symfony/src/Symfony/Component/HttpFoundation/UniversalClassLoader.php";

$loader = new Symfony\Component\HttpFoundation\UniversalClassLoader();
$loader->registerNamespace('Symfony', __DIR__.'/../../vendor/symfony/src/');
$loader->register();

require_once __DIR__."/../../src/Yadif/Container.php";
require_once __DIR__."/../../src/Yadif/Builder.php";
require_once __DIR__."/../../src/Yadif/Module.php";
require_once __DIR__."/../../src/Yadif/Loader/YadifLoader.php";

require_once "Fixture.php";
require_once "ConfigComponentTest.php";
require_once "InstantiateObjectGraphTest.php";
require_once "BindParamsTest.php";
require_once "EnforceSingletonTest.php";
require_once "ZendApplicationComplianceTest.php";
require_once "BuilderTest.php";
require_once "ModuleTest.php";
require_once "SymfonyYadifLoaderTest.php";

class Yadif_Tests_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Yadif - Testsuite');
        $suite->addTestSuite('Yadif_Tests_ConfigComponentTest');
        $suite->addTestSuite('Yadif_Tests_InstantiateObjectGraphTest');
        $suite->addTestSuite('Yadif_Tests_BindParamsTest');
        $suite->addTestSuite('Yadif_Tests_EnforceSingletonTest');
        $suite->addTestSuite('Yadif_Tests_ZendApplicationComplianceTest');
        $suite->addTestSuite('Yadif_Tests_BuilderTest');
        $suite->addTestSuite('Yadif_Tests_ModuleTest');
        $suite->addTestSuite('Yadif_Tests_SymfonyYadifLoaderTest');

        return $suite;
    }
}