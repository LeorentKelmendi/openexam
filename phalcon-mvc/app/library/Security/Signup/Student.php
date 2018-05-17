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
// File:    Student.php
// Created: 2015-03-13 16:12:47
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Library\Security\Signup;

use Exception;
use OpenExam\Library\Model\Exception as ModelException;
use OpenExam\Library\Security\Exception as SecurityException;
use OpenExam\Library\Security\Roles;
use OpenExam\Models\Student as StudentModel;

/**
 * Student signup class.
 *
 * This class provides extended functionality for students. It could be used
 * from a signup page or automatic task applied when a user is logged on and
 * detected to be an student.
 *
 * Currently it is being used for register a student in the pre-configured
 * list of example exams.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Student extends SignupBase {

  /**
   * Constructor.
   * @param string $user The target user principal name.
   */
  public function __construct($user = null) {
    parent::__construct($user);

    if ($this->config->get('signup') == false) {
      $this->_enabled = false;
    } elseif ($this->config->signup->get('student') == false) {
      $this->_enabled = false;
    } else {
      $this->_enabled = true;
      $this->_exams = $this->config->signup->student->toArray();
    }
  }

  /**
   * Register all available exams on current selected student.
   * @throws Exception
   * @throws ModelException
   */
  public function register() {
    foreach ($this->_exams as $index) {
      $this->assign($index);
    }
  }

  /**
   * Assign exam to current selected user.
   * @param int $index The exam ID.
   * @throws Exception
   * @throws ModelException
   */
  public function assign($index) {
    if (!in_array($index, $this->_exams)) {
      throw new SecurityException(
        "Exam $index is not in list of available exams.", SecurityException::ACTION
      );
    }

    $role = $this->user->setPrimaryRole(Roles::SYSTEM);
    try {
      if (($found = StudentModel::count(array(
        "exam_id = :exam: AND user = :user:",
        "bind" => array(
          "exam" => $index,
          "user" => $this->_caller,
        ),
      ))) == 0) {
        $student = new StudentModel();
        $student->user = $this->_caller;
        $student->exam_id = $index;
        $student->starttime = date('Y-m-d H:i:s');
        $student->tag = 'SIGNUP';

        if ($student->save() == false) {
          throw new ModelException($student->getMessages()[0]);
        }
      }
    } catch (Exception $exception) {
      $this->user->setPrimaryRole($role);
      throw $exception;
    }

    $this->user->setPrimaryRole($role);
    return $found == 0;
  }

  /**
   * Check if signup has been applied.
   *
   * Return true if at least one signup actions has been applied. The
   * check is done by counting the number of student accounts having
   * the caller as username and with SIGNUP as tag.
   *
   * @return boolean
   */
  public function isApplied() {
    return StudentModel::count(array(
      "user = :user: AND tag = 'SIGNUP'",
      "bind" => array(
        "user" => $this->_caller,
      ),
    )) > 0;
  }

}
