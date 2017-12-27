<?php

include __DIR__.'/vendor/autoload.php';

$dir = __DIR__.'/tests/files';
array_map('unlink', glob($dir."/*.*"));
rmdir($dir);


if (! is_dir(__DIR__.'/tests/files')) {
    mkdir(__DIR__.'/tests/files', 0777, true);
}

