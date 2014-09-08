<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    config.php
// Created: 2014-08-20 01:44:49
// 
// Author:  Anders Lövgren (QNET/BMC CompDept)
// 

/**
 * System configuration directory.
 */
define('CONFIG_SYS', __DIR__);
/**
 * User settings directory.
 */
define('CONFIG_DIR', dirname(CONFIG_SYS));
/**
 * Application directory.
 */
define('APP_DIR', dirname(CONFIG_DIR));
/**
 * Base directory (the Phalcon MVC app).
 */
define('BASE_DIR', dirname(APP_DIR));
/**
 * Project root directory.
 */
define('PROJ_DIR', dirname(BASE_DIR));

/**
 * Read the config protected configuration file:
 */
$config = new Phalcon\Config(include CONFIG_DIR . '/config.def');

/**
 * Merge user defined settings with system settings:
 */
$config->merge(array(
        'database'    => array(
                'adapter'  => $config->dbread->adapter,
                'host'     => $config->dbread->host,
                'username' => $config->dbread->username,
                'password' => $config->dbread->password,
                'dbname'   => $config->dbread->dbname
        ),
        'application' => array(
                'controllersDir' => APP_DIR . '/controllers/',
                'modelsDir'      => APP_DIR . '/models/',
                'viewsDir'       => APP_DIR . '/views/',
                'pluginsDir'     => APP_DIR . '/plugins/',
                'libraryDir'     => APP_DIR . '/library/',
                'schemasDir'     => BASE_DIR . '/schemas/',
                'migrationsDir'  => BASE_DIR . '/schemas/migrations/',
                'cacheDir'       => BASE_DIR . '/cache/',
                'baseDir'        => BASE_DIR . '/',
                'baseUri'        => $config->baseuri
        )
));

return $config;