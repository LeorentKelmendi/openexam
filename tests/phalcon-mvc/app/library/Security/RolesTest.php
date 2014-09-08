<?php

namespace OpenExam\Library\Security;

use OpenExam\Models\Exam;
use OpenExam\Tests\Phalcon\TestCase;
use OpenExam\Tests\Phalcon\UniqueUser;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-08-31 at 16:13:35.
 */
class RolesTest extends TestCase
{

        /**
         * @var Roles
         */
        protected $object;

        /**
         * Sets up the fixture, for example, opens a network connection.
         * This method is called before a test is executed.
         */
        protected function setUp()
        {
                $this->object = new Roles;
        }

        /**
         * Tears down the fixture, for example, closes a network connection.
         * This method is called after a test is executed.
         */
        protected function tearDown()
        {
                
        }

        /**
         * @covers OpenExam\Library\Security\Roles::__construct
         * @group security
         */
        public function testConstruct()
        {
                $this->object = new Roles();
                self::assertTrue(count($this->object->getRoles(0)) == 0);
                self::assertTrue(count($this->object->getAllRoles()) == 0);

                // Test alternative roles array format:
                $roles = array(
                        0 => array(Roles::admin, Roles::teacher),
                        1 => array(Roles::contributor)
                );
                $this->object = new Roles($roles);
                self::assertTrue(count($this->object->getRoles(0)) == 3);
                self::assertTrue(count($this->object->getRoles(1)) == 1);
                self::assertTrue(count($this->object->getAllRoles()) == 2);

                // Test native roles array format:
                $roles = array(
                        0 => array(Roles::admin => true, Roles::teacher => true),
                        1 => array(Roles::contributor => true)
                );
                $this->object = new Roles($roles);
                self::assertTrue(count($this->object->getRoles(0)) == 3);
                self::assertTrue(count($this->object->getRoles(1)) == 1);
                self::assertTrue(count($this->object->getAllRoles()) == 2);

                // Test idempotence:
                $roles = array(
                        0 => array(Roles::admin => true, Roles::teacher => true),
                        1 => array(Roles::contributor => true),
                        1 => array(Roles::contributor => true),
                        2 => array(Roles::contributor => true)
                );
                $this->object = new Roles($roles);
                self::assertTrue(count($this->object->getRoles(0)) == 3);
                self::assertTrue(count($this->object->getRoles(1)) == 1);
                self::assertTrue(count($this->object->getAllRoles()) == 3);

                // An more complex test:
                $roles = array(
                        0 => array(Roles::admin, Roles::teacher),
                        1 => array(Roles::contributor),
                        2 => array(Roles::decoder, Roles::invigilator),
                        3 => array(Roles::contributor)
                );
                $this->object = new Roles($roles);
                self::assertTrue(count($this->object->getRoles(0)) == 5);
                self::assertTrue(count($this->object->getRoles(1)) == 1);
                self::assertTrue(count($this->object->getRoles(2)) == 2);
                self::assertTrue(count($this->object->getRoles(3)) == 1);
                self::assertTrue(count($this->object->getRoles(4)) == 0);
                self::assertTrue(count($this->object->getAllRoles()) == 4);
                self::assertTrue(count($this->object->getAllRoles()[1]) == 1);
        }

        /**
         * @covers OpenExam\Library\Security\Roles::addRole
         * @group security
         */
        public function testAddRole()
        {
                $role = 'role';

                self::assertFalse($this->object->hasRole($role));
                self::assertFalse($this->object->hasRole($role, 0));
                self::assertFalse($this->object->hasRole($role, 1));

                $this->object->addRole($role);
                self::assertTrue($this->object->hasRole($role));
                self::assertTrue($this->object->hasRole($role, 0));
                self::assertFalse($this->object->hasRole($role, 1));

                $this->object = new Roles;
                self::assertFalse($this->object->hasRole($role));
                $this->object->addRole($role, 1);
                self::assertTrue($this->object->hasRole($role));
                self::assertTrue($this->object->hasRole($role, 0));
                self::assertTrue($this->object->hasRole($role, 1));
                self::assertFalse($this->object->hasRole($role, 2));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::removeRole
         * @group security
         */
        public function testRemoveRole()
        {
                $role = 'role';

                $this->object->removeRole($role);
                self::assertFalse($this->object->hasRole($role));

                $this->object->addRole($role);
                self::assertTrue($this->object->hasRole($role));
                $this->object->removeRole($role);
                self::assertFalse($this->object->hasRole($role));

                $this->object = new Roles;
                self::assertFalse($this->object->hasRole($role, 1));
                $this->object->addRole($role, 1);
                self::assertTrue($this->object->hasRole($role, 1));
                $this->object->removeRole($role, 1);
                self::assertFalse($this->object->hasRole($role, 1));
                self::assertTrue($this->object->hasRole($role));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::hasRole
         * @group security
         */
        public function testHasRole()
        {
                $role1 = 'role1';
                $role2 = 'role2';

                self::assertFalse($this->object->hasRole($role1));
                $this->object->addRole($role1);
                self::assertTrue($this->object->hasRole($role1));
                self::assertFalse($this->object->hasRole($role2));

                $this->object->addRole($role2);
                self::assertTrue($this->object->hasRole($role2));

                self::assertFalse($this->object->hasRole($role1, 1));
                $this->object->addRole($role1, 1);
                self::assertTrue($this->object->hasRole($role1, 1));
                self::assertFalse($this->object->hasRole($role2, 1));

                $this->object->addRole($role2, 2);
                self::assertTrue($this->object->hasRole($role2, 2));

                self::assertFalse($this->object->hasRole($role1, 2));
                self::assertFalse($this->object->hasRole($role2, 1));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::getRoles
         * @group security
         */
        public function testGetRoles()
        {
                $role = 'role';

                self::assertTrue(is_array($this->object->getRoles()));
                self::assertTrue(is_array($this->object->getRoles(1)));

                self::assertTrue(count($this->object->getRoles()) == 0);
                self::assertTrue(count($this->object->getRoles(1)) == 0);

                $this->object->addRole($role);

                self::assertTrue(is_array($this->object->getRoles()));
                self::assertTrue(is_array($this->object->getRoles(1)));

                self::assertTrue(count($this->object->getRoles()) == 1);
                self::assertTrue(count($this->object->getRoles(1)) == 0);

                $this->object->addRole($role, 1);

                self::assertTrue(is_array($this->object->getRoles()));
                self::assertTrue(is_array($this->object->getRoles(1)));

                self::assertTrue(count($this->object->getRoles()) == 1);
                self::assertTrue(count($this->object->getRoles(1)) == 1);
        }

        /**
         * @covers OpenExam\Library\Security\Roles::getAllRoles
         * @group security
         */
        public function testGetAllRoles()
        {
                self::assertTrue(is_array($this->object->getAllRoles()));
                self::assertTrue(count($this->object->getAllRoles()) == 0);

                $this->object->addRole(Roles::admin);
                $this->object->addRole(Roles::teacher);

                self::assertTrue(is_array($this->object->getAllRoles()));
                self::assertTrue(count($this->object->getAllRoles()) == 1);
                self::assertTrue(count($this->object->getAllRoles()[0]) == 2);

                $this->object = new Roles;

                $this->object->addRole(Roles::contributor, 1);
                $this->object->addRole(Roles::decoder, 1);
                $this->object->addRole(Roles::invigilator, 2);

                self::assertTrue(is_array($this->object->getAllRoles()));
                self::assertTrue(count($this->object->getAllRoles()) == 3);
                self::assertTrue(count($this->object->getAllRoles()[0]) == 3);
                self::assertTrue(count($this->object->getAllRoles()[1]) == 2);
                self::assertTrue(count($this->object->getAllRoles()[2]) == 1);

                // Test idempotence:
                $this->object->addRole(Roles::contributor, 1);
                self::assertTrue(count($this->object->getAllRoles()[0]) == 3);
                self::assertTrue(count($this->object->getAllRoles()[1]) == 2);

                printf("%s: [roles: '%s']\n", __METHOD__, print_r($this->object->getAllRoles(), true));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::aquire
         * @group security
         * @todo   Implement testAquire().
         */
        public function testAquire()
        {
                // 
                // Domain for unique users:
                // 
                if (isset($this->config['user']['domain'])) {
                        $domain = $this->config['user']['domain'];
                } else {
                        $domain = 'test.com';
                }

                // 
                // User principal guarantied to not exists in any model.
                // 
                $principal = (new UniqueUser($domain))->user;

                // 
                // Fake current authenticated user:
                // 
                $user = new User($principal);
                $this->di->set('user', $user);

                printf("%s: [principal name: '%s']\n", __METHOD__, $user);
                printf("%s: [principal name: '%s']\n", __METHOD__, $this->user);
                self::assertEquals($user, $principal);
                self::assertEquals($user, $this->user);

                // 
                // Test aquire system wide roles:
                // 
                $this->checkAquireSystemRole($user, Roles::admin, '\OpenExam\Models\Admin');
                $this->checkAquireSystemRole($user, Roles::teacher, '\OpenExam\Models\Teacher');

                // 
                // Test aquire object specific roles:
                // 
                $exam = new Exam();
                $exam->name = "Name";
                $exam->starttime = date('Y-m-d H:i:s');
                $exam->starttime = date('Y-m-d H:i:s', time() + 3600 * 24);
                if ($exam->create() == false) {
                        self::fail(print_r($exam->getMessages(), true));
                }

                $this->checkAquireExamRole($user, $exam, Roles::contributor, '\OpenExam\Models\Contributor');
//                $this->checkAquireExamRole($user, $exam, Roles::decoder, '\OpenExam\Models\Decoder');
//                $this->checkAquireExamRole($user, $exam, Roles::invigilator, '\OpenExam\Models\Invigilator');
//                $this->checkAquireExamRole($user, $exam, Roles::student, '\OpenExam\Models\Student');

                $exam->delete();
        }

        private function checkAquireSystemRole($user, $role, $class)
        {
                printf("%s: [class: '%s', role: '%s']\n", __METHOD__, $class, $role);

                self::assertFalse($this->object->aquire($role));
                self::assertFalse($this->object->aquire($role, 0));
                self::assertFalse($this->object->aquire($role, 1));
                printf("%s: [roles: '%s']\n", __METHOD__, $this->object);

                $model = new $class();
                $model->user = $user->getPrincipalName();
                $model->create();
                print_r($model->dump());

                self::assertTrue($this->object->aquire($role));
                self::assertTrue($this->object->aquire($role, 0));
                self::assertTrue($this->object->aquire($role, 1));
                printf("%s: [roles: '%s']\n", __METHOD__, $this->object);
                $model->delete();       // cleanup                
        }

        private function checkAquireExamRole($user, $exam, $role, $class)
        {
                printf("%s: [class: '%s', role: '%s']\n", __METHOD__, $class, $role);

                self::assertFalse($this->object->aquire($role));
                self::assertFalse($this->object->aquire($role, 0));
                self::assertFalse($this->object->aquire($role, $exam->id));
                printf("%s: [roles: '%s']\n", __METHOD__, $this->object);

                $model = new $class();
                $model->exam_id = $exam->id;
                $model->user = $user->getPrincipalName();
                $model->create();
                print_r($model->dump());

                self::assertTrue($this->object->aquire($role));
                self::assertTrue($this->object->aquire($role, 0));
                self::assertTrue($this->object->aquire($role, $exam->id));
                self::assertFalse($this->object->aquire($role, $exam->id + 1));
                printf("%s: [roles: '%s']\n", __METHOD__, $this->object);
                $model->delete();       // cleanup                
        }

        /**
         * @covers OpenExam\Library\Security\Roles::isAdmin
         * @group security
         */
        public function testIsAdmin()
        {
                self::assertTrue(Roles::isAdmin(Roles::admin));
                self::assertFalse(Roles::isAdmin('service'));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::isStudent
         * @group security
         */
        public function testIsStudent()
        {
                self::assertTrue(Roles::isStudent(Roles::student));
                self::assertFalse(Roles::isStudent('service'));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::isStaff
         * @group security
         */
        public function testIsStaff()
        {
                self::assertTrue(Roles::isStaff(Roles::contributor));
                self::assertTrue(Roles::isStaff(Roles::corrector));
                self::assertTrue(Roles::isStaff(Roles::creator));
                self::assertTrue(Roles::isStaff(Roles::decoder));
                self::assertTrue(Roles::isStaff(Roles::invigilator));
                self::assertTrue(Roles::isStaff(Roles::teacher));

                self::assertFalse(Roles::isStaff(Roles::admin));
                self::assertFalse(Roles::isStaff(Roles::student));
                self::assertFalse(Roles::isStaff('service'));
        }

        /**
         * @covers OpenExam\Library\Security\Roles::isCustom
         * @group security
         */
        public function testIsCustom()
        {
                self::assertTrue(Roles::isCustom('service'));
                self::assertFalse(Roles::isCustom(Roles::admin));
        }

}