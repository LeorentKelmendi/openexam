<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    SystemWideTrait.php
// Created: 2014-08-28 04:26:49
// 
// Author:  Anders Lövgren (QNET/BMC CompDept)
// 

namespace OpenExam\Models\Access\Traits;

/**
 * System wide authorization on model.
 *
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
trait SystemWideTrait
{

        private function checkRole()
        {
                if ($this->_roles->aquire($this->_role) == false) {
                        throw new Exception(_("You are not authorized to make this call."));
                }
        }

}
