<?php

/*
 * Copyright (C) 2015-2018 The OpenExam Project
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
// File:    AccessHandler.php
// Created: 2015-01-29 11:37:52
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Library\WebService\Handler;

use OpenExam\Library\Core\Exam\Student\Access;
use OpenExam\Library\Core\Location;
use OpenExam\Library\Security\Exception as SecurityException;
use OpenExam\Library\Security\Roles;
use OpenExam\Library\Security\User;
use OpenExam\Library\WebService\Common\ServiceHandler;
use OpenExam\Library\WebService\Common\ServiceRequest;
use OpenExam\Library\WebService\Common\ServiceResponse;
use OpenExam\Models\Exam;
use OpenExam\Models\Lock;

/**
 * Access service handler.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class AccessHandler extends ServiceHandler {

  /**
   * @var Exam
   */
  private $_exam;
  /**
   * @var Lock
   */
  private $_lock;
  /**
   * @var Access
   */
  private $_access;

  /**
   * Constructor.
   * @param ServiceRequest $request The service request.
   * @param User $user The logged in user.
   */
  public function __construct($request, $user) {
    parent::__construct($request, $user);

    if (isset($request->data['exam_id'])) {
      if (($this->_exam = Exam::findFirstById($request->data['exam_id'])) == false) {
        throw new SecurityException("No exam found!", self::UNDEFINED);
      }
    }
    if (isset($request->data['lock_id'])) {
      if (($this->_lock = Lock::findFirstById($request->data['lock_id'])) == false) {
        throw new SecurityException("No lock found!", self::UNDEFINED);
      }
    }

    $this->_access = new Access($this->_exam);
  }

  /**
   * Open exam.
   * @return ServiceResponse
   */
  public function open() {
    if (!isset($this->_exam)) {
      return new ServiceResponse($this, self::UNDEFINED, "Undefined exam object.");
    }

    $this->_user->setPrimaryRole(Roles::STUDENT);
    $status = $this->_access->open($this->_exam);

    if ($status == Access::OPEN_APPROVED) {
      return new ServiceResponse($this, self::SUCCESS, true);
    } else if ($status == Access::OPEN_PENDING) {
      return new ServiceResponse($this, self::PENDING, "The exam connection is pending approval.");
    } else if ($status == Access::OPEN_DENIED) {
      return new ServiceResponse($this, self::FORBIDDEN, "Permission denied.");
    } else {
      return new ServiceResponse($this, self::UNDEFINED, "Unhandled open mode.");
    }
  }

  /**
   * Close exam lock.
   * @return ServiceResponse
   */
  public function close() {
    if (!isset($this->_exam)) {
      return new ServiceResponse($this, self::UNDEFINED, "Undefined exam object.");
    }

    $this->_user->setPrimaryRole(Roles::STUDENT);
    $status = $this->_access->close($this->_exam);

    if ($status == true) {
      return new ServiceResponse($this, self::SUCCESS, true);
    } else {
      return new ServiceResponse($this, self::FORBIDDEN, "Permission denied.");
    }
  }

  /**
   * Approve exam lock.
   * @return ServiceResponse
   */
  public function approve() {
    if (!isset($this->_lock)) {
      return new ServiceResponse($this, self::UNDEFINED, "Undefined lock object.");
    }

    $this->_user->setPrimaryRole(Roles::INVIGILATOR);
    $this->_access->approve($this->_lock);

    return new ServiceResponse($this, self::SUCCESS, true);
  }

  /**
   * Release exam lock.
   * @return ServiceResponse
   */
  public function release() {
    if (!isset($this->_lock)) {
      return new ServiceResponse($this, self::UNDEFINED, "Undefined lock object.");
    }

    $this->_user->setPrimaryRole(Roles::INVIGILATOR);
    $this->_access->release($this->_lock);

    return new ServiceResponse($this, self::SUCCESS, true);
  }

  /**
   * Get location/access information entries.
   * @param Location $location The location and information service.
   * @param string $section The section (active, system or recent).
   * @return ServiceResponse
   */
  public function entries($location, $section = null) {
    if (!isset($this->_request->data['exam_id'])) {
      $this->_request->data['exam_id'] = 0;
    }
    if (isset($section)) {
      $this->_request->params['filter'] = array(
        $section => true,
      );
    }
    if (!isset($this->_request->params['filter'])) {
      $this->_request->params['filter'] = array(
        'system' => true,
        'recent' => true,
        'active' => true,
      );
    }
    if (!isset($this->_request->params['flat'])) {
      $this->_request->params['flat'] = false;
    }

    $result = $location->getEntries(
      $this->_request->data['exam_id'], $this->_request->params['filter'], $this->_request->params['flat']
    );
    return new ServiceResponse($this, self::SUCCESS, $result);
  }

}
