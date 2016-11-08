<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    simulate.php
// Created: 2016-01-13 15:23:18
// 
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
// 

define('TASKS_PHP', __DIR__ . '/../app/config/system/tasks.php');
include(TASKS_PHP);

use OpenExam\Library\Console\Application;

try {
        $console = new Application($di);
        $console->process(array('task' => 'simulate'));
} catch (\Exception $exception) {
        $di->get('flash')->error($exception->getMessage());
        exit(255);
}