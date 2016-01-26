<?php

spl_autoload_register(function ($className) {
    $fileName = __DIR__.'/'.str_replace('\\', '/', $className).'.php';
    if (file_exists($fileName)) {
        require $fileName;
    }
});
