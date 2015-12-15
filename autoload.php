<?php

function __autoload($class_name) {
    include "src/".$class_name . '.php';
}

$s = new SainsburysScrapper();