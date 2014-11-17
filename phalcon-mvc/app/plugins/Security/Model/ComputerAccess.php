<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    ComputerAccess.php
// Created: 2014-09-30 14:48:52
// 
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
// 

namespace OpenExam\Plugins\Security\Model;

use OpenExam\Library\Security\Exception;
use OpenExam\Library\Security\Roles;
use OpenExam\Library\Security\User;
use OpenExam\Models\Computer;
use OpenExam\Models\Question;

/**
 * Access control for the Computer model.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ComputerAccess extends ObjectAccess
{

        /**
         * Check object role.
         * 
         * @param string $action The model action.
         * @param Computer $model The model object.
         * @param User $user The peer object.
         * @return boolean
         */
        public function checkObjectRole($action, $model, $user)
        {
                if ($this->logger->debug) {
                        $this->logger->debug->log(sprintf(
                                "%s(action=%s, model=%s, user=%s)", __METHOD__, $action, $model->getResourceName(), $user->getPrincipalName()
                        ));
                }

                // 
                // Perform access control in a trusted context:
                // 
                return $this->trustedContextCall(function($role) use($action, $model, $user) {
                            // 
                            // Check role on exam, question or global:
                            // 
                            if ($role == Roles::CONTRIBUTOR ||
                                $role == Roles::CREATOR ||
                                $role == Roles::DECODER ||
                                $role == Roles::INVIGILATOR ||
                                $role == Roles::STUDENT) {
                                    foreach ($model->locks as $lock) {
                                            if ($user->roles->aquire($role, $lock->exam_id)) {
                                                    return true;
                                            }
                                    }
                            } elseif ($role == Roles::CORRECTOR) {
                                    foreach ($model->locks as $lock) {
                                            if (($questions = Question::find("exam_id='$lock->exam_id'"))) {
                                                    foreach ($questions as $question) {
                                                            if ($user->roles->aquire($role, $question->id)) {
                                                                    return true;
                                                            }
                                                    }
                                            }
                                    }
                            } elseif (isset($role)) {
                                    if ($user->roles->aquire($role)) {
                                            return true;
                                    }
                            }

                            if ($this->logger->debug) {
                                    $this->logger->debug->log(sprintf(
                                            "%s(role=%s)", __METHOD__, $role
                                    ));
                            }

                            if (isset($role)) {
                                    throw new Exception('role');
                            } else {
                                    return true;
                            }
                    });
        }

}
