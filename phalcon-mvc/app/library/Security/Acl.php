<?php

namespace OpenExam\Library\Security;

use Phalcon\Acl as PhalconAcl;
use Phalcon\Acl\Adapter\Memory as AclAdapter;
use Phalcon\Acl\Role;
use Phalcon\Mvc\User\Component;

/**
 * Security
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class Acl extends Component
{

        /**
         * Access list configuration.
         * @var array 
         */
        private $_access;
        /**
         * The ACL object.
         * @var AclAdapter 
         */
        private $_acl;

        /**
         * Constructor.
         * @param array $access Access list configuration.
         */
        public function __construct($access = array())
        {
                $this->_access = $access;
        }

        /**
         * Get ACL object.
         * @return AclAdapter
         */
        public function getAcl()
        {
                if (!isset($this->_acl)) {
                        if ($this->cache->exists('acl-access')) {
                                $this->_acl = $this->cache->get('acl-access');
                        } else {
                                $this->_acl = $this->rebuild();
                                $this->cache->save('acl-access', $this->_acl);
                        }
                }
                return $this->_acl;
        }

        /**
         * Returns true if role is permitted to call action on resource.
         * 
         * @param string $role
         * @param string $resource
         * @param string $action
         * @return bool
         */
        public function isAllowed($role, $resource, $action)
        {
                // 
                // Check ACL for defined resources:
                // 
                if ($this->capabilities->hasResource($resource)) {
                        return $this->getAcl()->isAllowed($role, $resource, $action);
                }

                // 
                // Grant access for undefined resources:
                // 
                return true;
        }

        /**
         * Get controller access.
         * 
         * Returns a string describing the protection level for this 
         * controller/action:
         * 
         * <code>
         * <ul>private:   Enforce authentication.</ul>
         * <ul>protected: Advisory authentication.</ul>
         * <ul>public:    No authentication required.</ul>
         * </code>
         * 
         * @param string $controller The controller.
         * @param string $action The action. 
         * @return string 
         */
        public function getAccess($controller, $action)
        {
                foreach (array('public', 'protected') as $protection) {
                        if (isset($this->_access[$protection][$controller])) {
                                if (is_string($this->_access[$protection][$controller])) {
                                        $this->_access[$protection][$controller] = array($this->_access[$protection][$controller]);
                                }
                                if (in_array($action, $this->_access[$protection][$controller])) {
                                        return $protection;
                                } elseif ($this->_access[$protection][$controller][0] == "*") {
                                        return $protection;
                                }
                        }
                }

                return 'private';
        }

        private function rebuild()
        {
                $acl = new AclAdapter();
                $acl->setDefaultAction(PhalconAcl::DENY);

                // 
                // Use roles map:
                // 
                $roles = $this->_access['roles'];

                // 
                // Use permissions map:
                // 
                $permissions = $this->_access['permissions'];

                // 
                // Add roles:
                // 
                foreach (array_keys($roles) as $role) {
                        $acl->addRole(new Role($role));
                }

                // 
                // Add resources:
                // 
                $resources = array();
                foreach ($roles as $role => $rules) {
                        if (is_array($rules)) {
                                foreach (array_keys($rules) as $resource) {
                                        $resources[] = $resource;
                                }
                        }
                }
                $resources = array_unique($resources);
                foreach ($resources as $resource) {
                        $acl->addResource($resource, $permissions['full']);
                }

                // 
                // Add rules:
                // 
                foreach ($roles as $role => $resources) {
                        if (is_string($resources)) {
                                $acl->allow($role, '*', $permissions[$resources]);
                                continue;
                        }
                        foreach ($resources as $resource => $permission) {
                                $acl->allow($role, $resource, $permissions[$permission]);
                        }
                }

                return $acl;
        }

}
