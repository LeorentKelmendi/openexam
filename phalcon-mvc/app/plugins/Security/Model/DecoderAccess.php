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
// File:    Decoder.php
// Created: 2014-09-29 14:09:58
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Plugins\Security\Model;

use OpenExam\Library\Security\Exception;
use OpenExam\Library\Security\Roles;
use OpenExam\Library\Security\User;
use OpenExam\Models\Decoder;
use OpenExam\Plugins\Security\Model\ObjectAccess;

/**
 * Access control for the Decoder model.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class DecoderAccess extends ObjectAccess {

  /**
   * Check object access.
   *
   * @param string $action The model action.
   * @param Decoder $model The model object.
   * @param User $user The peer object.
   * @return boolean
   */
  public function checkObjectRole($action, $model, $user) {
    if ($this->logger->debug) {
      $this->logger->debug->log(sprintf(
        "%s(action=%s, model=%s, user=%s)", __METHOD__, $action, $model->getResourceName(), $user->getPrincipalName()
      ));
    }

    //
    // Perform access control in a trusted context:
    //
    return $this->trustedContextCall(function ($role) use ($action, $model, $user) {
      //
      // Check role on exam, question or global:
      //
      if ($role == Roles::CONTRIBUTOR ||
        $role == Roles::CREATOR ||
        $role == Roles::DECODER ||
        $role == Roles::INVIGILATOR ||
        $role == Roles::STUDENT) {
        if ($user->roles->acquire($role, $model->exam_id)) {
          return true;
        }
      } elseif ($role == Roles::CORRECTOR) {
        foreach ($model->exam->questions as $question) {
          if ($user->roles->acquire($role, $question->id)) {
            return true;
          }
        }
      } elseif (isset($role)) {
        if ($user->roles->acquire($role)) {
          return true;
        }
      }

      if (isset($role)) {
        throw new Exception(sprintf("Failed acquire role %s", $role), Exception::ROLE);
      } else {
        return true;
      }
    });
  }

}
