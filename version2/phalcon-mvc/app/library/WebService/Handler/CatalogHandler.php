<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    CatalogHandler.php
// Created: 2015-04-03 14:31:38
// 
// Author:  Anders Lövgren (QNET/BMC CompDept)
// 

namespace OpenExam\Library\WebService\Handler;

use OpenExam\Library\Catalog\DirectoryManager;
use OpenExam\Library\Catalog\Principal;
use OpenExam\Library\Security\User;
use OpenExam\Library\WebService\Common\ServiceHandler;
use OpenExam\Library\WebService\Common\ServiceRequest;
use OpenExam\Library\WebService\Common\ServiceResponse;

/**
 * Catalog service handler.
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class CatalogHandler extends ServiceHandler
{

        /**
         * The catalog service.
         * @var DirectoryManager
         */
        private $catalog;

        /**
         * Output as obejct.
         */
        const OUTPUT_OBJECT = 'object';
        /**
         * Output as array.
         */
        const OUTPUT_ARRAY = 'array';
        /**
         * Strip null values in output.
         */
        const OUTPUT_STRIP = 'strip';
        /**
         * Compact otuput format.
         */
        const OUTPUT_COMPACT = 'compact';

        /**
         * Constructor.
         * @param ServiceRequest $request The service request.
         * @param User $user The logged in user.
         * @param string $action The dispatched action.
         * @param DirectoryManager $catalog The catalog service.
         */
        public function __construct($request, $user, $catalog)
        {
                $this->catalog = $catalog;
                parent::__construct($request, $user);
        }

        /**
         * List all domains.
         * @return ServiceResponse 
         */
        public function domains()
        {
                $this->formatRequest("domains");
                
                $result = $this->catalog->getDomains();
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Get name from user principal.
         * @return ServiceResponse 
         */
        public function name()
        {
                $this->formatRequest("name");
                
                $result = $this->catalog->getName($this->request->data['principal']);
                $result = $this->formatResult($result);
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Get mail address from user principal.
         * @return ServiceResponse 
         */
        public function mail()
        {
                $this->formatRequest("mail");
                
                $result = $this->catalog->getMail($this->request->data['principal']);
                $result = $this->formatResult($result);
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Get attribute from user principal.
         * @return ServiceResponse 
         */
        public function attribute()
        {
                $this->formatRequest("attribute");
                
                $result = $this->catalog->getAttribute($this->request->data['principal'], $this->request->data['attribute']);
                $result = $this->formatResult($result);
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Get user principal groups.
         * @param string $method The request method (GET or POST).
         * @param string $principal The user principal.
         * @param string $output The output format.
         * @return ServiceResponse 
         */
        public function groups($method = "POST", $principal = null, $output = null)
        {
                if ($method == "GET") {
                        $this->request->data['principal'] = $principal;
                        $this->request->params['output'] = $output;
                }

                $this->formatRequest("groups");
                
                $result = $this->catalog->getGroups($this->request->data['principal'], $this->request->data['attributes']);
                $result = $this->formatResult($result);
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Get group members.
         * @param string $method The request method (GET or POST).
         * @param string $group The group name.
         * @param string $output The output format.
         * @return ServiceResponse 
         */
        public function members($method = "POST", $group = null, $output = null)
        {
                if ($method == "GET") {
                        $this->request->data['group'] = $group;
                        $this->request->params['output'] = $output;
                }

                $this->formatRequest("members");
                
                $result = $this->catalog->getMembers($this->request->data['group'], $this->request->data['domain'], $this->request->data['attributes']);
                $result = $this->formatResult($result);
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Search for user principals.
         * @return ServiceResponse 
         */
        public function principal()
        {
                $this->formatRequest("principal");

                $result = $this->catalog->getPrincipal(current($this->request->data), key($this->request->data), $this->request->params);
                $result = $this->formatResult($result);
                return new ServiceResponse($this, self::SUCCESS, $result);
        }

        /**
         * Format output based on request params preferences.
         * @param array $input The result data.
         * @param ServiceRequest $request The service request.
         * @return array
         */
        private function formatResult($input)
        {
                $output = $this->request->params['output'];
                $svcref = $this->request->params['svcref'];

                // 
                // Strip service references if requested:
                // 
                if ($svcref == false) {
                        for ($i = 0; $i < count($input); $i++) {
                                if ($input[$i] instanceof Principal) {
                                        unset($input[$i]->attr['svc']);
                                } else {
                                        unset($input[$i]['svc']);
                                }
                        }
                }

                // 
                // Convert single index array to strings:
                // 
                if (is_array(current($input))) {
                        for ($i = 0; $i < count($input); $i++) {
                                foreach ($input[$i] as $key => $val) {
                                        if (is_array($val) && count($val) == 1) {
                                                $input[$i][$key] = $input[$i][$key][0];
                                        }
                                }
                        }
                }

                // 
                // Format output:
                // 
                if ($output == self::OUTPUT_OBJECT) {
                        return $input;
                } elseif ($output == self::OUTPUT_STRIP) {
                        for ($i = 0; $i < count($input); $i++) {
                                $input[$i] = array_filter((array) $input[$i]);
                        }
                        return $input;
                } elseif ($output == self::OUTPUT_COMPACT) {
                        $result = array();
                        for ($i = 0; $i < count($input); $i++) {
                                $result = array_merge($result, array_values(array_filter((array) $input[$i])));
                        }
                        return $result;
                } elseif ($output == self::OUTPUT_ARRAY) {
                        for ($i = 0; $i < count($input); $i++) {
                                $input[$i] = array_values(array_filter((array) $input[$i]));
                        }
                        return $input;
                }
        }

        private function formatRequest($action)
        {
                if ($action == "groups") {
                        if (!isset($this->request->data['attributes'])) {
                                $this->request->data['attributes'] = array(Principal::ATTR_NAME);
                        }
                }

                if ($action == "members") {
                        if (!isset($this->request->data['attributes'])) {
                                $this->request->data['attributes'] = DirectoryManager::$DEFAULT_ATTR;
                        }
                        if (!isset($this->request->data['domain'])) {
                                $this->request->data['domain'] = null;
                        }
                }

                if ($action == "attribute" ||
                    $action == "name" ||
                    $action == "mail") {
                        if (!isset($this->request->params['svcref'])) {
                                $this->request->params['svcref'] = false;
                        }
                        if (!isset($this->request->params['output'])) {
                                $this->request->params['output'] = self::OUTPUT_COMPACT;
                        }
                }

                if (!isset($this->request->data['principal'])) {
                        $this->request->data['principal'] = $this->user->getPrincipalName();
                }
                if (!isset($this->request->params['output'])) {
                        $this->request->params['output'] = self::OUTPUT_OBJECT;
                }
                if (!isset($this->request->params['svcref'])) {
                        $this->request->params['svcref'] = true;
                }
        }

}