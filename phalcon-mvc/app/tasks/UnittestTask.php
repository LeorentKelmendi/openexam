<?php

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
// File:    UnitTestTask.php
// Created: 2014-09-15 12:56:46
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Console\Tasks;

use OpenExam\Library\Security\User;

//
// Bypass uniqueness validation checking for unit test:
//
define('VALIDATION_SKIP_UNIQUENESS_CHECK', true);

/**
 * Unit test task.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class UnittestTask extends MainTask implements TaskInterface {

  /**
   * The sample data location.
   * @var string
   */
  private $_sample;
  /**
   * Backup copy of existing file.
   * @var string
   */
  private $_backup;

  /**
   * Initializer hook.
   */
  public function initialize() {
    $this->_sample = sprintf("%s/unittest/sample.dat", $this->config->application->cacheDir);
    $this->_backup = sprintf("%s_%s", $this->_sample, time());
  }

  public static function getUsage() {
    return array(
      'header' => 'Prepare database for running unit test by inserting some data.',
      'action' => '--unittest',
      'usage' => array(
        '--setup',
        '--clean',
      ),
      'options' => array(
        '--setup' => 'Add sample data for running unit tests.',
        '--user=name' => 'Set username for sample data. Defaults to process owner (calling user).',
        '--count=num' => 'Number of samples to add.',
        '--clean[=sample]' => 'Remove sample data (optional specifying sample file).',
        '--verbose' => 'Be more verbose.',
      ),
      'examples' => array(
        array(
          'descr' => 'Insert sample data',
          'command' => '--setup --verbose',
        ),
        array(
          'descr' => 'Cleanup sample data using last sample',
          'command' => '--clean --verbose',
        ),
        array(
          'descr' => 'Cleanup sample data using distinguished sample file',
          'command' => '--clean=phalcon-mvc/cache/unittest/sample1.dat',
        ),
      ),
    );
  }

  /**
   * Display usage information.
   */
  public function helpAction() {
    parent::showUsage(self::getUsage());
  }

  /**
   * Cleanup sample data.
   * @param array $params Task action parameters.
   */
  public function cleanAction($params = array()) {
    $options = array(
      'verbose' => false,
    );

    for ($i = 0; $i < count($params); ++$i) {
      if ($params[$i] == 'verbose') {
        $options['verbose'] = true;
      } else {
        $this->_sample = $params[$i];
      }
    }

    if ($options['verbose']) {
      $this->flash->notice("Using sample data " . $this->_sample);
    }
    if (!file_exists($this->_sample)) {
      $this->flash->error("Sample data is missing.\n");
      return;
    }

    if ($options['verbose']) {
      $this->flash->notice("Removing sample data from database:");
      $this->flash->notice("-------------------------------");
    }

    $data = array_reverse(unserialize(file_get_contents($this->_sample)));
    foreach ($data as $m => $a) {
      $class = sprintf("OpenExam\Models\%s", ucfirst($m));
      $model = $class::findFirst(array('id' => $a['id']));

      if ($options['verbose']) {
        $this->flash->notice(sprintf("Model %s:\n", $class));
        $this->flash->notice(print_r($model->toArray(), true));
        $this->flash->write();
      }

      if ($model->delete() == false) {
        $this->flash->warning("Failed remove sample data from " . $model->getSource());
      }
    }
  }

  /**
   * Setup sample data.
   * @param array $params Task action parameters.
   */
  public function setupAction($params = array()) {
    $options = array(
      'verbose' => false,
      'user' => $this->getDI()->getUser()->getPrincipalName(),
      'count' => 1,
    );

    for ($i = 0; $i < count($params); ++$i) {
      if ($params[$i] == 'verbose') {
        $options['verbose'] = true;
      }
      if ($params[$i] == 'user') {
        $options['user'] = $params[++$i];
      }
      if ($params[$i] == 'count') {
        $options['count'] = $params[++$i];
      }
    }

    $this->getDI()->setUser(new User($options['user']));

    if ($options['verbose']) {
      $this->flash->notice("Adding sample data in database:");
      $this->flash->notice("-------------------------------");
    }
    for ($i = 0; $i < $options['count']; ++$i) {
      $data = array(
        'exam' => array(
          'name' => 'Exam 1',
          'creator' => $options['user'],
          'orgunit' => 'orgunit1',
          'grades' => json_encode(array('data' => array('U' => 0, 'G' => 20, 'VG' => 30))),
        ),
        'contributor' => array(
          'exam_id' => 0,
          'user' => $options['user'],
        ),
        'decoder' => array(
          'exam_id' => 0,
          'user' => $options['user'],
        ),
        'invigilator' => array(
          'exam_id' => 0,
          'user' => $options['user'],
        ),
        'student' => array(
          'exam_id' => 0,
          'user' => $options['user'],
          'code' => '1234ABCD',
        ),
        'resource' => array(
          'exam_id' => 0,
          'name' => 'Name1',
          'path' => '/tmp/path',
          'type' => 'video',
          'subtype' => 'mp4',
          'user' => $options['user'],
        ),
        'access' => array(
          'exam_id' => 0,
          'name' => 'BMC;CBE;A3:234a',
          'addr' => '10.5.6.0/22',
        ),
        'room' => array(
          'name' => 'Room 1',
        ),
        'computer' => array(
          'room_id' => 0,
          'ipaddr' => '127.0.0.1',
          'port' => 666,
          'password' => 'password1',
        ),
        'lock' => array(
          'student_id' => 0,
          'computer_id' => 0,
          'exam_id' => 0,
        ),
        'topic' => array(
          'exam_id' => 0,
          'name' => 'Topic 1',
          'randomize' => 0,
        ),
        'question' => array(
          'exam_id' => 0,
          'topic_id' => 0,
          'score' => 1.0,
          'name' => 'Question 1',
          'quest' => 'Question text',
          'user' => $options['user'],
        ),
        'corrector' => array(
          'question_id' => 0,
          'user' => $options['user'],
        ),
        'answer' => array(
          'question_id' => 0,
          'student_id' => 0,
        ),
        'result' => array(
          'answer_id' => 0,
          'corrector_id' => 0,
          'question_id' => 0,
          'score' => 1.5,
        ),
        'file' => array(
          'answer_id' => 0,
          'name' => 'Name1',
          'path' => '/tmp/path',
          'type' => 'video',
          'subtype' => 'mp4',
        ),
        'admin' => array(
          'user' => $options['user'],
        ),
        'teacher' => array(
          'user' => $options['user'],
        ),
        'session' => array(
          'session_id' => md5(time()),
          'data' => serialize(array('auth' => array('user' => $options['user'], 'type' => 'cas'))),
          'created' => time(),
        ),
        'setting' => array(
          'data' => array('key1' => 'val1', 'key2' => array('key3' => 'val3', 'key4' => 'val4')),
          'user' => $options['user'],
        ),
      );

      foreach ($data as $m => $a) {
        foreach (array(
          'exam' => 'exam_id',
          'room' => 'room_id',
          'computer' => 'computer_id',
          'topic' => 'topic_id',
          'question' => 'question_id',
          'student' => 'student_id',
          'answer' => 'answer_id',
          'corrector' => 'corrector_id') as $n => $fk) {
          if (isset($a[$fk]) && $a[$fk] == 0) {
            $a[$fk] = $data[$n]['id'];
          }
        }
        $class = sprintf("OpenExam\Models\%s", ucfirst($m));
        $model = self::fetch($class, $a);
        $model->setDI($this->getDI());
        if (!$model->save($a)) {
          $this->flash->error(sprintf("Failed save model for %s", $model->getSource()));
          foreach ($model->getMessages() as $message) {
            $this->flash->error($message);
          }
          return false;
        }
        $data[$m] = $model->toArray();
        if ($options['verbose']) {
          $this->flash->notice(sprintf("Model %s:\n", $class));
          $this->flash->notice(print_r($data[$m], true));
          $this->flash->write();
        }
      }

      if ($options['count'] != 1) {
        $this->flash->notice("Created exam (id=" . $data['exam']['id'] . ") [" . 100 * $i / $options['count'] . "%]");
      }
    }

    if (!file_exists(dirname($this->_sample))) {
      if ($options['verbose']) {
        $this->flash->notice("Creating sample data directory " . dirname($this->_sample));
      }
      if (!mkdir(dirname($this->_sample), 0755, true)) {
        $this->flash->error("Failed create sample directory.");
      }
    }

    if (file_exists($this->_sample)) {
      copy($this->_sample, $this->_backup);
    }

    if (!file_put_contents($this->_sample, serialize($data))) {
      $this->flash->error("Failed save sample data");
    } elseif ($options['verbose']) {
      $this->flash->success("Wrote sample data to " . $this->_sample);
    }
  }

  /**
   * Fetch existing class or create new model.
   *
   * @param string $class The model class.
   * @param array $data The properties.
   */
  private static function fetch($class, $data) {
    $conditions = array();

    foreach ($data as $key => $val) {
      if (strpos($key, '_') != false && $val == 0) {
        continue;
      } elseif (is_string($val)) {
        $conditions[] = sprintf("%s = '%s'", $key, $val);
      } elseif (is_float($val)) {
        $conditions[] = sprintf("%s = %f", $key, $val);
      } else {
        $conditions[] = sprintf("%s = %d", $key, $val);
      }
    }

    if (count($conditions) == 0) {
      $model = new $class();
    } elseif (!($model = $class::findFirst(implode(" AND ", $conditions)))) {
      $model = new $class();
    }

    return $model;
  }

}
