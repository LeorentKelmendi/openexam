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

namespace OpenExam\Library\Monitor;

use OpenExam\Tests\Phalcon\TestCase;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ConfigTest extends TestCase {

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {

  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown() {

  }

  /**
   * @covers OpenExam\Library\Monitor\Config::hasCounters
   * @group monitor
   */
  public function testHasCounters() {
    $config = new Config(array());
    $expect = false;
    $actual = $config->hasCounters();
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);

    $config = new Config(false);
    $expect = false;
    $actual = $config->hasCounters();
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);

    $config = new Config(true);
    $expect = true;
    $actual = $config->hasCounters();
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);
  }

  /**
   * @covers OpenExam\Library\Monitor\Config::getCounters
   * @group monitor
   */
  public function testGetCounters() {
    $config = new Config(array('counter' => array('c1' => true, 'c2' => true)));
    $expect = array('c1', 'c2');
    $actual = $config->getCounters();
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);
  }

  /**
   * @covers OpenExam\Library\Monitor\Config::getParams
   * @group monitor
   */
  public function testGetParams() {
    $config = new Config(array('counter' => array('c1' => array('params' => array('p1' => 'v1', 'p2' => 23)))));
    $expect = array('p1' => 'v1', 'p2' => 23);
    $actual = $config->getParams('c1');
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);

    $expect = null;
    $actual = $config->getParams('c2');
    self::assertEquals($expect, $actual);
  }

  /**
   * @covers OpenExam\Library\Monitor\Config::getTriggers
   * @group monitor
   */
  public function testGetTriggers() {
    $config = new Config(array('trigger' => array('t1' => true), 'counter' => array('c1' => true)));
    $expect = array('t1' => true);
    $actual = $config->getTriggers('c1');
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);
  }

  /**
   * @covers OpenExam\Library\Monitor\Config::hasConfig
   * @group monitor
   */
  public function testHasConfig() {
    $config = new Config(array('counter' => array('c1' => true)));

    $expect = true;
    $actual = $config->hasConfig('c1');
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);

    $expect = false;
    $actual = $config->hasConfig('c2');
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);
  }

  /**
   * @covers OpenExam\Library\Monitor\Config::getConfig
   * @group monitor
   */
  public function testGetConfig() {
    $data = array(
      'trigger' => array(
        't1' => true,
        't2' => array(
          'k1' => 'v1',
          'k2' => 44,
        ),
      ),
      'counter' => array(
        'c1' => true,
        'c2' => array(
          'params' => array(
            'p1' => 'v2',
            'p2' => 'v3',
          ),
        ),
        'c3' => array(
          'params' => array(
            'p3' => 'v4',
          ),
          'trigger' => array(
            't1' => true,
            't2' => array(
              'k1' => 'v4',
            ),
            't3' => true,
          ),
        ),
      ),
    );
    $tree = array(
      'c1' => array(
        'params' => array(),
        'triggers' => array(
          't1' => true,
          't2' => array(
            'k1' => 'v1',
            'k2' => 44,
          ),
        ),
      ),
      'c2' => array(
        'params' => array(
          'p1' => 'v2',
          'p2' => 'v3',
        ),
        'triggers' => array(
          't1' => true,
          't2' => array(
            'k1' => 'v1',
            'k2' => 44,
          ),
        ),
      ),
      'c3' => array(
        'params' => array(
          'p3' => 'v4',
        ),
        'triggers' => array(
          't1' => true,
          't2' => array(
            'k1' => 'v4',
          ),
          't3' => true,
        ),
      ),
    );

    $config = new Config($data);

    $actual = $config->getConfig();
    $expect = $tree;
    self::assertNotNull($actual);
    self::assertEquals($expect['c1'], $actual['c1']);
    self::assertEquals($expect['c2'], $actual['c2']);
    self::assertEquals($expect['c3'], $actual['c3']);

    $actual = $config->getConfig('c1');
    $expect = $tree['c1'];
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);

    $actual = $config->getConfig('c2');
    $expect = $tree['c2'];
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);

    $actual = $config->getConfig('c3');
    $expect = $tree['c3'];
    self::assertNotNull($actual);
    self::assertEquals($expect, $actual);
  }

}
