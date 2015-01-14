<?php namespace Petja\Import;

class TestClass {
    protected static $instance;

    public static function testMethod()
    {


        echo 'Test ' . \Config::get('test.value');
    }
}