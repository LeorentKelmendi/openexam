<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    SessionHandler.php
// Created: 2015-02-17 10:40:06
// 
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
// 

namespace OpenExam\Plugins\Security\Dispatcher;

use OpenExam\Plugins\Security\DispatchListener;
use Phalcon\Mvc\User\Component;

/**
 * Session handling class.
 * @access private
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class SessionHandler extends Component implements DispatchHelper
{

        /**
         * Session data entry name.
         */
        const ENTRY = 'auth';

        /**
         * The dispatch listener class.
         * @var DispatchListener
         */
        private $_listener;
        /**
         * The service type (e.g. soap).
         * @var string 
         */
        private $_service;
        /**
         * The remote caller address.
         * @var string 
         */
        private $_remote;
        /**
         * The session data.
         * @var array 
         */
        private $_data;
        /**
         * Number of seconds until session expires.
         * @var int 
         */
        private $_expires;
        /**
         * Renew session if expire time is closer that this value.
         * @var int 
         */
        private $_refresh;

        /**
         * Constructor.
         * @param DispatchListener The dispatch listener.
         * @param string $service The service type.
         */
        public function __construct($listener, $service)
        {
                $this->_listener = $listener;
                $this->_service = $service;
                $this->_remote = $this->request->getClientAddress(true);
                $this->_data = $this->session->get(self::ENTRY, array());

                $this->_expires = $this->config->session->expires;
                $this->_refresh = $this->config->session->refresh;
        }

        /**
         * Validate session data.
         * @return boolean True if session is valid.
         */
        public function validate()
        {
                // 
                // Validate session data:
                // 
                if (!$this->get('expire')) {
                        $this->logger->auth->debug(sprintf(
                                "Session is missing (no auth entry)"
                        ));
                        return false;   // No session exist
                } elseif ($this->get('expire') < time()) {
                        $this->logger->auth->debug(sprintf(
                                "Session has expired (valid until %s)", strftime("%x %X", $this->get('expire'))
                        ));
                        return false;   // Session has expired.
                } elseif ($this->get('remote') != $this->_remote) {
                        $this->logger->auth->debug(sprintf(
                                "Remote address missmatch (expected %s, was %s)", $this->get('remote'), $this->_remote
                        ));
                        $this->_listener->report('Remote address missmatch', $this->_data);
                        return false;   // Session owner missmatch
                }

                // 
                // Session is validated.
                // 
                $this->logger->auth->debug(sprintf(
                        "Verified session data for %s: %s", $this->_remote, print_r($this->_data, true)
                ));
                return true;
        }

        /**
         * Register an authentication session.
         */
        public function register()
        {
                error_log("EXPIRES: " . $this->_expires);
                
                $this->set('expire', time() + $this->_expires);
                $this->set('remote', $this->_remote);

                $this->logger->auth->debug(sprintf(
                        "Register session data: %s", print_r($this->_data, true)
                ));

                $this->session->start();
                $this->session->set(self::ENTRY, $this->_data);
        }

        /**
         * Check if session refresh is required.
         * 
         * Return true if session is about to expire and need to be 
         * refreshed by calling register().
         * 
         * @return boolean
         */
        public function expiring()
        {
                if (!($expires = $this->get('expire'))) {
                        return true;
                }
                if ($expires - time() < $this->_refresh) {
                        return true;
                }
                return false;
        }

        /**
         * Return true if session has expired.
         * @return boolean
         */
        public function expired()
        {
                return $this->get('expire', 0) < time();
        }

        /**
         * Remove authentication entry.
         */
        public function remove()
        {
                $this->session->remove(self::ENTRY);
        }

        /**
         * Set session data value.
         * @param string $key The key name.
         * @param mixed $val The entry value.
         */
        public function set($key, $val)
        {
                $this->_data[$key] = $val;
        }

        /**
         * Get session data value.
         * @param string $key The key name.
         * @param mixed $default The default value if key is missing.
         * @return mixed
         */
        public function get($key = null, $default = false)
        {
                if (!isset($key)) {
                        return $this->_data;
                } elseif (isset($this->_data[$key])) {
                        return $this->_data[$key];
                } else {
                        return $default;
                }
        }

        /**
         * Clear named key or all if key is null.
         * @param string $key The key name.
         */
        public function clear($key = null)
        {
                if (!isset($key)) {
                        $this->_data = array();
                } elseif (isset($this->_data[$key])) {
                        unset($this->_data[$key]);
                }
        }

        public function getData()
        {
                return array(
                        'expires' => $this->_expires,
                        'refresh' => $this->_refresh,
                        'data'    => $this->_data,
                        'remote'  => $this->_remote,
                        'service' => $this->_service
                );
        }

        public function __toString()
        {
                return print_r($this->getData(), true);
        }

}