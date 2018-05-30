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
// File:    SuperInvigilator.php
// Created: 2018-05-29 10:28:30
//
// Author:  Daniel Wirdehäll
//

namespace OpenExam\Models;

use OpenExam\Library\Model\Guard\Exam as ExamModelGuard;

/**
 * The invigilator model.
 *
 * Represents a user having the invigilator role on all exams in the system.
 *
 */
class SuperInvigilator extends Role {

  /**
   * This object ID.
   * @var integer
   */
  public $id;
  /**
   * The user principal name (e.g. user@example.com).
   * @var string
   */
  public $user;

  /**
   * Get source table name.
   * @return string
   */
  public function getSource() {
    return 'super_invigilators';
  }

  /**
   * Get table column map.
   * @return array
   */
  public function columnMap() {
    return array(
      'id' => 'id',
      'user' => 'user',
    );
  }

  // /**
  //  * Called after model is created.
  //  */
  // public function afterCreate() {
  //   parent::afterCreate();
  //   $this->exam->getStaff()->addRole($this);
  // }
  //
  // /**
  //  * Called after model is deleted.
  //  */
  // public function afterDelete() {
  //   parent::afterDelete();
  //   $this->exam->getStaff()->removeRole($this);
  // }

}
