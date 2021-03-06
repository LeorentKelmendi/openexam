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
// File:    TestModel.php
// Created: 2014-11-25 11:48:48
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Tests\Phalcon;

use OpenExam\Library\Security\Capabilities;
use OpenExam\Library\Security\User;
use Phalcon\Mvc\Model;

/**
 * Base class for model testing.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class TestModel extends TestCase {

  /**
   * Sample data object.
   * @var SampleData
   */
  protected $_sample;
  /**
   * The capabilities mapper.
   * @var Capabilities
   */
  protected $_capabilities;

  public function setUp() {
    parent::setUp();

    $this->_sample = new SampleData();
    $this->_capabilities = new Capabilities(require (CONFIG_DIR . '/access.def'));
  }

  public function tearDown() {
    $this->getDI()->get('user')->roles->clear(); // Clear cached roles
    $this->getDI()->get('user')->setPrimaryRole(null);
    parent::tearDown();
  }

  /**
   * Cleanup model.
   * @param Model $model
   */
  protected function cleanup($model) {
    $user = $this->getDI()->get('user');
    $role = $user->setPrimaryRole(null);
    $model->delete();
    $user->setPrimaryRole($role);
  }

  /**
   * Persist model.
   * @param Model $model
   */
  protected function persist(&$model) {
    $user = $this->getDI()->get('user');
    $role = $user->setPrimaryRole(null);
    $model->create();
    $user->setPrimaryRole($role);
  }

  /**
   * Test create model as user.
   * @param Model $model The model to create.
   * @param User $user The user object.
   * @param bool $expect True if call should fail.
   * @return bool
   */
  protected function create($model, $user, $expect) {
    self::info("model: %s, user: %s, role: %s, expect: %s", $model->getResourceName(), $user->getPrincipalName(), $user->getPrimaryRole(), $expect ? "success" : "failure");
    $result = $expect;

    $curr = $this->getDI()->get('user');
    $this->getDI()->set('user', $user);

    try {
      if (($result = $model->create()) == false) {
        throw new \Exception($model->getMessages()[0]);
      }
    } catch (\Exception $exception) {
      if ($expect) {
        self::error($exception);
      } else {
        self::success("Expected failure: %s", $exception->getMessage());
      }
    }

    $this->getDI()->set('user', $curr);
    return $result == $expect;
  }

  /**
   * Test update model as user.
   * @param Model $model The model to update.
   * @param User $user The user object.
   * @param bool $expect True if call should fail.
   * @return bool
   */
  protected function update($model, $user, $expect) {
    self::info("model: %s, user: %s, role: %s, expect: %s", $model->getResourceName(), $user->getPrincipalName(), $user->getPrimaryRole(), $expect ? "success" : "failure");
    $result = $expect;

    $curr = $this->getDI()->get('user');
    $this->getDI()->set('user', $user);

    try {
      if (($result = $model->update()) == false) {
        throw new \Exception($model->getMessages()[0]);
      }
    } catch (\Exception $exception) {
      if ($expect) {
        self::error($exception);
      } else {
        self::success("Expected failure: %s", $exception->getMessage());
      }
    }

    $this->getDI()->set('user', $curr);
    return $result == $expect;
  }

  /**
   * Test delete model as user.
   * @param Model $model The model to delete.
   * @param User $user The user object.
   * @param bool $expect True if call should fail.
   * @return bool
   */
  protected function delete($model, $user, $expect) {
    self::info("model: %s, user: %s, role: %s, expect: %s", $model->getResourceName(), $user->getPrincipalName(), $user->getPrimaryRole(), $expect ? "success" : "failure");
    $result = $expect;

    $curr = $this->getDI()->get('user');
    $this->getDI()->set('user', $user);

    try {
      if (($result = $model->delete()) == false) {
        throw new \Exception($model->getMessages()[0]);
      }
    } catch (\Exception $exception) {
      if ($expect) {
        self::error($exception);
      } else {
        self::success("Expected failure: %s", $exception->getMessage());
      }
    }

    $this->getDI()->set('user', $curr);
    return $result == $expect;
  }

  /**
   * Test read model as user.
   * @param Model $model The model to read.
   * @param User $user The user object.
   * @param bool $expect True if call should fail.
   * @return bool
   */
  protected function read($model, $user, $expect) {
    self::info("model: %s, user: %s, role: %s, expect: %s", $model->getResourceName(), $user->getPrincipalName(), $user->getPrimaryRole(), $expect ? "success" : "failure");
    $result = $expect;

    $curr = $this->getDI()->get('user');
    $this->getDI()->set('user', $user);

    $class = get_class($model);

    try {
      if (($result = $class::findFirstById($model->id)) == false) {
        throw new \Exception("Find model failed: id=" . $model->id);
      } else {
        $result = $expect;
      }
    } catch (\Exception $exception) {
      if ($expect) {
        self::error($exception);
      } else {
        self::success("Expected failure: %s", $exception->getMessage());
      }
    }

    $this->getDI()->set('user', $curr);
    return $result == $expect;
  }

}
