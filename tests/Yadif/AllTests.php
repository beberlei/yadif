<?php

require_once dirname(__FILE__)."/../Container.php";
require_once dirname(__FILE__)."/../Builder.php";
require_once dirname(__FILE__)."/../Module.php";
require_once "PHPUnit/Framework.php";

require_once "ConfigComponentTest.php";
require_once "InstantiateObjectGraphTest.php";
require_once "BindParamsTest.php";
require_once "EnforceSingletonTest.php";
require_once "ZendApplicationComplianceTest.php";
require_once "BuilderTest.php";
require_once "ModuleTest.php";

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

        return $suite;
    }
}