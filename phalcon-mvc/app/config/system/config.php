<?php

use Phalcon\Config;
use Phalcon\Logger;

/*
 * Copyright (C) 2014-2018 The OpenExam Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

//
// File:    config.php
// Created: 2014-08-20 01:44:49
//
// Author:  Anders Lövgren (QNET/BMC CompDept)
//

if (!defined('CONFIG_SYS')) {
  /**
   * System configuration directory.
   */
  define('CONFIG_SYS', __DIR__);
}
if (!defined('CONFIG_DIR')) {
  /**
   * User settings directory.
   */
  define('CONFIG_DIR', dirname(CONFIG_SYS));
}
if (!defined('APP_DIR')) {
  /**
   * Application directory.
   */
  define('APP_DIR', dirname(CONFIG_DIR));
}
if (!defined('BASE_DIR')) {
  /**
   * Base directory (the Phalcon MVC app).
   */
  define('BASE_DIR', dirname(APP_DIR));
}
if (!defined('PROJ_DIR')) {
  /**
   * Project root directory.
   */
  define('PROJ_DIR', dirname(BASE_DIR));
}
if (!defined('EXTERN_DIR')) {
  /**
   * External source directory.
   */
  define('EXTERN_DIR', APP_DIR . '/extern/');
}

/**
 * These are the system default settings. Site local configuration can
 * be done in app/config/config.def.
 */
$config = new Config(
  array(
    'brand' => array(
      'logo' => array(
        'link' => 'http://www.uu.se',
        'file' => 'img/uu_logo.svg',
        'seal' => 'img/seal.png',
        'name' => 'Uppsala University',
        'style' => '',
      ),
    ),
    'application' => array(
      /**
       * The application layout:
       */
      'controllersDir' => APP_DIR . '/controllers/',
      'modelsDir' => APP_DIR . '/models/',
      'viewsDir' => APP_DIR . '/views/',
      'pluginsDir' => APP_DIR . '/plugins/',
      'libraryDir' => APP_DIR . '/library/',
      'tasksDir' => APP_DIR . '/tasks',
      'schemasDir' => BASE_DIR . '/schemas/',
      'migrationsDir' => BASE_DIR . '/schemas/migrations/',
      'localeDir' => BASE_DIR . '/locale/',
      'cacheDir' => BASE_DIR . '/cache/',
      'auditDir' => BASE_DIR . '/audit',
      'logsDir' => BASE_DIR . '/logs',
      'baseDir' => BASE_DIR . '/',
      'docsDir' => PROJ_DIR . '/docs',
      'polyfillsDir' => BASE_DIR . '/public/js/polyfill',
      /**
       * These should always be user defined:
       */
      'instance' => null,
      'release' => true,
      'baseUri' => '/phalcon-mvc/',
    ),
    'phpunit' => array(
      'logging' => true, // enable output logging
      'logfile' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . "phpunit-output.log",
      'truncate' => false, // truncate instead of rotating
      'rotate' => true, // rotate logs
      'compress' => true, // compress rotated logs
      'maxsize' => 10 * 1024 * 1024, // size limit in bytes (0 == no limit)
      'maxage' => 3600 * 24, // max age in seconds (0 == no max age)
    ),
    /**
     * System locale settings.
     */
    'locale' => array(
      'request' => 'locale', // Detect from request parameter 'locale'
      'default' => 'en_US.UTF-8', // Default locale if not detected.
    ),
    /**
     * Settings for GNU gettext (setup only).
     */
    'gettext' => array(
      'program' => array(
        'xgettext' => '/usr/bin/xgettext',
        'msgmerge' => '/usr/bin/msgmerge',
        'msgfmt' => '/usr/bin/msgfmt',
        'msgcat' => '/usr/bin/msgcat',
        'msgconv' => '/usr/bin/msgconv',
        'msginit' => '/usr/bin/msginit',
      ),
      'options' => array(
        'xgettext' => '--language=PHP --package-name="@name@" --package-version="@version@" --msgid-bugs-address="@address@" --copyright-holder="@copying@" --add-comments=\'//{tr}\' --from-code=UTF-8 --no-wrap --output=@template@',
        'msgmerge' => '--no-wrap --update',
        'msgfmt' => '--strict --statistics --check --output-file=@output@',
        'msgcat' => '--strict --no-wrap --to-code=UTF-8 --output-file=@output@',
        'msgconv' => '--no-wrap',
        'msginit' => '--no-wrap --input=@template@ --output=@output@ --locale=@locale@ --no-translator',
      ),
      'package' => array(
        'name' => 'openexam-online',
        'version' => '2.0',
        'copying' => 'The authors, the Computing Department at BMC and the OpenExam project (Uppsala University)',
        'address' => 'support@bmc.uu.se',
      ),
    ),
    /**
     * Define translation modules. See docs/develop/gettext.txt
     */
    'translate' => array(
      'core' => array(
        'directories' => array(
          'phalcon-mvc/app/config',
          'phalcon-mvc/app/controllers',
          'phalcon-mvc/app/library',
          'phalcon-mvc/app/models',
          'phalcon-mvc/app/plugins',
        ),
      ),
//                'tasks'   => array(
      //                        'directories' => array('phalcon-mvc/app/tasks'),
      //                        'merge'       => array('core')
      //                ),
      //                'web'     => array(
      //                        'directories' => array(
      //                                'phalcon-mvc/app/views/auth',
      //                                'phalcon-mvc/app/views/index',
      //                                'phalcon-mvc/app/views/public',
      //                                'phalcon-mvc/app/views/layouts'
      //                        ),
      //                        'files'       => array(
      //                                'phalcon-mvc/app/views/index.phtml'
      //                        )
      //                ),
      //                'admin'   => array(
      //                        'directories' => array('phalcon-mvc/app/views/admin'),
      //                        'merge'       => array('core', 'web')
      //                ),
      //                'student' => array(
      //                        'directories' => array('phalcon-mvc/app/views/student'),
      //                        'merge'       => array('core', 'web')
      //                ),
      //                'manager' => array(
      //                        'directories' => array('phalcon-mvc/app/views/staff'),
      //                        'merge'       => array('core', 'web')
      //                )
    ),
    /**
     * System logging to file and syslog(3). See README-LOGGER.
     */
    'logging' => array(
      'debug' => array(
        'file' => 'debug.log',
        'level' => Logger::DEBUG,
      ),
      'system' => array(
        'syslog' => 'openexam',
        'level' => Logger::NOTICE,
        'option' => LOG_NDELAY,
        'facility' => LOG_LOCAL0,
      ),
      'access' => array(
        'file' => 'access.log',
        'level' => Logger::INFO,
      ),
      'cache' => array(
        'file' => 'cache.log',
        'level' => Logger::INFO,
      ),
      'auth' => array(
        'syslog' => 'openexam',
        'level' => Logger::INFO,
        'option' => LOG_NDELAY,
        'facility' => LOG_AUTH,
      ),
      'test' => array(
        'file' => 'phpunit.log',
        'level' => Logger::DEBUG,
      ),
    ),
    /**
     * Configuration for the render service.
     * See http://wkhtmltopdf.org/libwkhtmltox/pagesettings.html
     */
    'render' => array(
      //
      // Prefered render method (extension or command):
      //
      'method' => 'command',
      //
      // Security token, an absolute file path or string:
      //
      'token' => 'render.sec',
      //
      // Download content before rendering:
      //
      'local' => false,
      //
      // The fully qualified server name:
      //
      'server' => 'localhost',
      //
      // The allowed submit host (string or array):
      //
      'submit' => '127.0.0.1',
      //
      // Options for phpwkhtmltox extension:
      //
      'extension' => array(
        'image' => array(
          'fmt' => 'png',
          'imageQuality' => 95,
          'enableJavascript' => true,
          'windowStatus' => 'content-loaded',
          // 'load.cookieJar' => BASE_DIR . '/cache/cookies.jar'
        ),
        'pdf' => array(
          'produceForms' => false,
          'outline' => true,
          'outlineDepth' => 2,
          'enableJavascript' => true,
          'windowStatus' => 'content-loaded',
          // 'load.cookieJar' => BASE_DIR . '/cache/cookies.jar'
        ),
      ),
      //
      // Options for wkhtmltoxxx command:
      //
      'command' => array(
        'image' => 'wkhtmltoimage --format png --quality 95 --window-status content-loaded --enable-javascript --print-media-type',
        'pdf' => 'wkhtmltopdf --outline --outline-depth 2 --window-status content-loaded --enable-javascript --print-media-type --disable-forms',
      ),
    ),
    /**
     * Models meta data cache. Only activated if application->release is
     * true. Fallback on default memory cache during development.
     */
    'metadata' => array(
      'prefix' => 'openexam',
      'lifetime' => 86400,
    ),
    /**
     * Session configuration.
     */
    'session' => array(
      'expires' => 21600, // Expires after (seconds)
      'refresh' => 7200, // Refresh threshold (seconds)
      'cleanup' => true, // Enable garbage collection
      'startPage' => 'exam/index', // Start page (user initiated)
    ),
    /**
     * Multi level object cache.
     */
    'cache' => array(
      /**
       * Enable these extensions (if available):
       */
      'enable' => array(
        'memory' => false,
        'xcache' => true,
        'apcu' => true,
        'apc' => true,
        'memcache' => true,
        'file' => false,
      ),
      /**
       * Cached object lifetime:
       */
      'lifetime' => array(
        'ultra' => 60,
        'fast' => 3600,
        'medium' => 86400,
        'slow' => 604800,
        'model' => 1800,
      ),
      /**
       * Extension specific settings:
       */
      'backend' => array(
        'memory' => array(
          'prefix' => '',
        ),
        'xcache' => array(
          'prefix' => '',
        ),
        'apc' => array(
          'prefix' => '',
        ),
        'apcu' => array(
          'prefix' => '',
        ),
        'memcache' => array(
          'prefix' => '',
          'host' => 'localhost',
          'port' => '11211',
        ),
        'file' => array(
          'prefix' => '',
          'cacheDir' => 'app/',
        ),
      ),
      /**
       * Optional list of servers allowed to fill cache. Either an array or
       * string containing single, range or subnet of IP-addresses. The localhost
       * is always permitted.
       */
      'filler' => array(
        'remote' => null,
        'maxdays' => 31,
      ),
    ),
    /**
     * Database cache is recommended, but disabled by default.
     */
    'dbcache' => false,
    /*
     * System performance profiler config.
     */
    'profile' => false,
    /**
     * Audit is disabled by default.
     */
    'audit' => false,
    /**
     * Performance monitoring is disabled by default.
     */
    'monitor' => false,
  )
);

/**
 * Read the config protected configuration file:
 */
$config->merge(new Config(
  include (CONFIG_DIR . '/config.def')
));

/**
 * Set absolute path to cache directory:
 */
if ($config->cache->backend->file->cacheDir[0] != '/') {
  $config->cache->backend->file->cacheDir = $config->application->cacheDir . 'app/';
}
if ($config->render->token[0] != '/') {
  $config->render->token = $config->application->cacheDir . $config->render->token;
}

/**
 * Merge user defined settings with system settings:
 */
$config->merge(new Config(
  array(
    'database' => array(
      'adapter' => $config->dbwrite->config->adapter,
      'host' => $config->dbwrite->config->host,
      'username' => $config->dbwrite->config->username,
      'password' => $config->dbwrite->config->password,
      'dbname' => $config->dbwrite->config->dbname,
    ),
  )
));

return $config;
