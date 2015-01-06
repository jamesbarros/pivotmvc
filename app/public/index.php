<?php

use PivotMvc\Application;

// Bootstrapping
require __DIR__ . '/../bootstrap.php';

// App config
$config = require PIVOTMVC_BASE . '/app/config/app.php';

// Application run
$application = new Application(PIVOTMVC_ENV, $config);
$application->run();
