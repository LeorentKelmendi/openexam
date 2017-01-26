<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    UserLoginForm.php
// Created: 2016-11-18 02:47:44
// 
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
// 

namespace OpenExam\Library\Form;

use OpenExam\Models\Exam;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Form;
use UUP\Authentication\Authenticator\RequestAuthenticator;

/**
 * Anonymous code login form.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class CodeLoginForm extends Form
{

        /**
         * Form initialize method.
         * @param RequestAuthenticator $login The selected authenticator.
         */
        public function initialize($login)
        {
                // 
                // Get today exams:
                // 
                $exams = Exam::find(sprintf("DATE(starttime) = '%s'", date('Y-m-d')));

                $this->setAction($this->url->get('auth/login/' . $login->name));
                
                $this->setUserOption('description', $login->description);
                $this->setUserOption('information', "Select your exam and use your anonymous code as login. Contact the invigilator if you don't know the code.<br><br>Example code: AB-39845");
                
                $this->add(new Password('fpass', array('name' => $login->pass, 'placeholder' => 'The anonymous code')));
                $this->add(new Select('fexam', $exams, array('using' => array('id', 'name'), 'name' => $login->user)));
                $this->add(new Hidden('fcode', array('name' => 'secret', 'value' => $login->secret)));
                $this->add(new Hidden("fembed", array('value' => $this->request->get("embed"))));
                $this->add(new Submit('fsubmit', array('name' => $login->name, 'value' => 'Login', 'class' => 'btn-submit')));
        }

}
