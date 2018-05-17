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
// File:    ModelAccessListener.php
// Created: 2014-09-26 10:32:13
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Plugins\Security;

use OpenExam\Library\Security\Exception;
use OpenExam\Library\Security\Roles;
use OpenExam\Models\Role;
use OpenExam\Plugins\Security\Model\ObjectAccess;
use Phalcon\Events\Event;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\User\Plugin;

/**
 * Centralized model access handler.
 *
 * This class ensures that:
 *
 * 1. The system services are present (acl and user).
 * 2. User is authenticated (if primary role is set).
 * 3. The requested primary role has been global acquired.
 * 4. The ACL allows access to the requested resource/action for role.
 *
 * Object specific access control is provided by classes derived from
 * Model\ObjectAccess.
 *
 * All granted model access actions are by default cached. Using cache can
 * be disabled by setting lifetime to 0.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ModelAccessListener extends Plugin implements EventsAwareInterface {

  /**
   * The object access callable.
   * @var callable
   */
  private $_callable;
  /**
   * Register of object access callable.
   * @var array
   */
  private $_register;
  /**
   * Lifetime of cached model access.
   * @var long
   */
  private $_lifetime;

  /**
   * Constructor.
   * @param callable $callable
   * @param array $register
   */
  public function __construct($callable, $register = array(), $lifetime = 60) {
    $this->_callable = $callable;
    $this->_register = $register;
    $this->_lifetime = $lifetime;
  }

  /**
   * Set lifetime of cached model access.
   * @param long $lifetime Lifetime of each cache entry.
   */
  public function setGrantLifetime($lifetime) {
    $this->_lifetime = $lifetime;
  }

  /**
   * Get model access cache lifetime.
   * @return long
   */
  public function getGrantLifetime() {
    return $this->_lifetime;
  }

  /**
   * Check if access has been granted.
   *
   * @param string $user User principal name.
   * @param Model $model The affected model.
   * @param string $action The requested action.
   * @return boolean
   */
  private function getGrantAccess($user, $model, $action) {
    return $this->cache->exists(
      self::createCacheKey($user, $model, $action), $this->_lifetime
    );
  }

  /**
   * Record grant access and return true.
   *
   * @param string $user User principal name.
   * @param Model $model The affected model.
   * @param string $action The requested action.
   * @return boolean
   */
  private function setGrantAccess($user, $model, $action) {
    $this->cache->save(
      self::createCacheKey($user, $model, $action), true, $this->_lifetime
    );

    return true;
  }

  /**
   * Get object access check object.
   * @param string $name The model name.
   * @return ObjectAccess
   */
  private function getObjectAccess($name) {
    if (!isset($this->_register[$name])) {
      $loader = $this->_callable;
      $this->_register[$name] = $loader($name);
    }
    return $this->_register[$name];
  }

  /**
   * Create cache key from parameters.
   *
   * @param string $user User principal name.
   * @param Model $model The affected model.
   * @param string $action The requested action.
   * @return string
   */
  private static function createCacheKey($user, $model, $action) {
    return sprintf("model-access-%s-%s-%s-%d", $user, $action, $model->getResourceName(), $model->id);
  }

  /**
   * Called before a model is created.
   * @param Event $event
   * @param Model $model
   */
  public function beforeCreate($event, $model) {
    return $this->checkAccess($event, $model, ObjectAccess::CREATE);
  }

  /**
   * Called before a model is updated.
   * @param Event $event
   * @param Model $model
   */
  public function beforeUpdate($event, $model) {
    return $this->checkAccess($event, $model, ObjectAccess::UPDATE);
  }

  /**
   * Called before a model is deleted.
   * @param Event $event
   * @param Model $model
   */
  public function beforeDelete($event, $model) {
    return $this->checkAccess($event, $model, ObjectAccess::DELETE);
  }

  /**
   * Called after a model is fetched (read).
   * @param Event $event
   * @param Model $model
   */
  public function afterFetch($event, $model) {
    return $this->checkAccess($event, $model, ObjectAccess::READ);
  }

  /**
   * Check that caller has access to resource.
   * @param Event $event The event to check.
   * @param Model $model The model to check.
   * @param string $action The action to perform.
   * @return boolean
   * @throws Exception message=(acl|user|auth|access|role)
   */
  public function checkAccess($event, $model, $action) {
    $type = $event->getType();
    $name = $model->getResourceName();

    if ($this->logger->debug) {
      $this->logger->debug->log(sprintf(
        "%s(event=%s, model=%s, action=%s)", __METHOD__, $type, $name, $action
      ));
    }

    //
    // Check system services:
    //
    if (($acl = $this->getDI()->get('acl')) == false) {
      $this->logger->system->critical("The ACL service ('acl') is missing.");
      throw new Exception("System configuration error", Exception::ACL);
    }
    if (($user = $this->getDI()->get('user')) == false) {
      $this->logger->system->critical("The User service ('user') is missing.");
      throw new Exception("System configuration error", Exception::USER);
    }

    //
    // No primary role means unrestricted access. Make sure that
    // peer is authenticated if primary role is set.
    //
    if ($user->hasPrimaryRole() == false) {
      $this->logger->access->info(sprintf(
        "Granted %s access on %s(id=%d) to user (primary role unset)", $action, $name, $model->id
      ));
      return true; // unrestricted access
    } elseif (($role = $user->getPrimaryRole()) === Roles::TRUSTED) {
      $this->logger->access->info(sprintf(
        "Granted %s access on %s(id=%d) for %s (trusted role)", $action, $name, $model->id, $role
      ));
      return true; // Control ACL System
    } elseif (($role = $user->getPrimaryRole()) === Roles::SYSTEM) {
      $this->logger->access->info(sprintf(
        "Granted %s access on %s(id=%d) for %s (trusted role)", $action, $name, $model->id, $role
      ));
      return true; // System internal
    } elseif ($user->getUser() == null) {
      $this->logger->access->error(sprintf(
        "Denied %s access on %s(id=%d) (unauthenticated user)", $action, $name, $model->id
      ));
      throw new Exception("Authentication required", Exception::AUTH);
    }

    //
    // Reading own role should always be permitted:
    //
    if ($model instanceof Role && $model->user == $user->getPrincipalName()) {
      return true;
    }

    //
    // Check that ACL permits access for this role:
    //
    if ($acl->isAllowed($role, $name, $action) == false) {
      $this->logger->access->error(sprintf(
        "Denied %s access on %s(id=%d) for caller using role %s (blocked by ACL)", $action, $name, $model->id, $role
      ));
      throw new Exception("Access denied by ACL", Exception::ACCESS);
    }

    //
    // Check if permission has already been granted:
    //
    if ($this->getGrantAccess($user->getPrincipalName(), $model, $action)) {
      $this->logger->access->info(sprintf(
        "Granted %s access on %s(id=%d) for %s (already granted)", $action, $name, $model->id, $role
      ));
      return true;
    }

    //
    // Verify that caller has requested role (global), if so,
    // trigger object specific role verification.
    //
    if ($user->roles->acquire($role) == false) {
      $this->logger->access->error(sprintf(
        "Denied %s access on %s(id=%d) for caller using role %s (failed acquire role)", $action, $name, $model->id, $role
      ));
      throw new Exception(sprintf("Failed acquire role %s", $role), Exception::ROLE);
    } elseif (Roles::isCustom($role)) { // Custom roles are global
      $this->logger->access->info(sprintf(
        "Granted %s access on %s(id=%d) for caller using role %s (custom role)", $action, $name, $model->id, $role
      ));
      return $this->setGrantAccess($user->getPrincipalName(), $model, $action);
    } elseif (($access = $this->getObjectAccess($name))) {
      if ($this->logger->debug) {
        $this->logger->debug->log(sprintf(
          "%s(event=%s, model=%s, user=%s) [calling]", get_class($access), $type, $name, $user->getPrincipalName()
        ));
      }
      if ($access->notify($type, $model, $user)) {
        $this->logger->access->info(sprintf(
          "Granted %s access on %s(id=%d) for caller using role %s (object access)", $action, $name, $model->id, $role
        ));
        return $this->setGrantAccess($user->getPrincipalName(), $model, $action);
      }
    } else {
      return false;
    }
  }

}
