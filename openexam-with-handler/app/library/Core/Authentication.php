<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    Authentication.php
// Created: 2014-08-25 13:44:23
// 
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
// 

namespace OpenExam\Library\Core;

use UUP\Authentication\Authenticator,
    UUP\Authentication\Library\Authenticator\AuthenticatorBase;

/**
 * Authentication handler
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Authentication implements Authenticator
{

        private $chains;
        private $authenticator;
        private $service;

        public function __construct($chains = array())
        {
                $this->chains = $chains;
                $this->authenticator = new NullAuthenticator();
        }

        /**
         * Add authenticator to stack.
         * 
         * The $name parameter is a string used to identified the authenticator 
         * plugin, e.g. 'cas' or 'kerberos'. The $auth parameter is the
         * authenticator object itself. 
         * 
         * The $service is a key used to associate a group of auth plugins 
         * for a service, e.g. 'soap' or 'web'. If $service parameter is
         * missing, then the authenticator is placed in the common group.
         * 
         * @param string $name The identifier for the authenticator plugin.
         * @param AuthenticatorBase $auth The authentication plugin.
         * @param string $service The service group associated with this plugin.
         * @return AuthenticatorBase The authenticator plugin.
         */
        public function add($name, $auth, $service = '*')
        {
                $this->chains[$service][$name] = $auth;
                return $auth;
        }

        /**
         * Activate this authentication plugin for next call to login().
         * @param string $name The identifier for the authenticator plugin.
         * @param string $service The service group associated with this plugin.
         */
        public function activate($name, $service = '*')
        {
                $this->authenticator = $this->chains[$service][$name];
                $this->service = $service;
        }

        public function authenticated()
        {
                if (!$this->authenticator->authenticated()) {
                        $this->authenticate('*');
                        $this->authenticate($this->service);
                }
                return $this->authenticator->authenticated();
        }

        public function getUser()
        {
                $this->authenticator->getUser();
        }

        public function login()
        {
                $this->authenticator->login();
        }

        public function logout()
        {
                $this->authenticator->logout();
        }

        private function authenticate($service)
        {
                foreach ($this->chains[$service] as $authenticator) {
                        if ($authenticator->control === Authenticator::required) {
                                if (!$authenticator->authenticated()) {
                                        throw new AuthenticatorRequiredException($authenticator->authenticator);
                                }
                        }
                }
                foreach ($this->chains[$service] as $authenticator) {
                        if ($authenticator->control === Authenticator::sufficient &&
                            $authenticator->authenticated()) {
                                $this->authenticator = $authenticator;
                                break;
                        }
                }
        }

}
