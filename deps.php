#!/usr/bin/php
<?php

include(__DIR__ . '/src/autoload.php');

$deps = new Deps(__DIR__);
array_shift($argv);
$deps->run($argv);
