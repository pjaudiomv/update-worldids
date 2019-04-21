<?php

class Foo {

    var $bar;

    function setBar($value) {
        $this->bar = $value;
    }

    function someFunction($param) {
        echo $param.$this->bar."<br/>";
    }

}

$foo = new Foo();

$foo->setBar("Charles");
$foo->someFunction("Hello, ");