<?php
$basePath = dirname(__DIR__);
require "$basePath/vendor/autoload.php";

Hail\Framework::bootstrap($basePath)
    ->get('app')->listen();