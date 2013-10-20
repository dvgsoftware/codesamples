<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '*.*.*.*',
        'username' => '******',
        'password' => '******',
        'dbname' => '******',
    ),
    'application' => array(
        'modelsDir' => __DIR__ . '/../models/',
        'viewsDir' => __DIR__ . '/../views/',
        'baseUri' => '/',
    )
));

