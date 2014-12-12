<?php

namespace OpenExam\Models;

use OpenExam\Library\Security\User;
use OpenExam\Tests\Phalcon\TestModel;
use OpenExam\Tests\Phalcon\UniqueUser;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ExamModel extends Exam
{

        public function initialize()
        {
                parent::initialize();
        }

}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-15 at 19:03:31.
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ExamTest extends TestModel
{

        /**
         * The model resource name.
         */
        const MODEL = 'exam';

        /**
         * @group model
         */
        public function testRelations()
        {
                $object = Exam::findFirstById($this->sample->getSample(self::MODEL)['id']);
                self::assertNotNull($object);

                self::assertNotEquals(0, $object->contributors->count());
                self::assertTrue(count($object->contributors) > 0);

                self::assertTrue(count($object->decoders) > 0);
                self::assertNotEquals(0, $object->decoders->count());

                self::assertNotEquals(0, $object->invigilators->count());
                self::assertTrue(count($object->invigilators) > 0);

                self::assertNotEquals(0, $object->questions->count());
                self::assertTrue(count($object->questions) > 0);

                self::assertNotEquals(0, $object->students->count());
                self::assertTrue(count($object->students) > 0);

                self::assertNotEquals(0, $object->topics->count());
                self::assertTrue(count($object->topics) > 0);

                self::assertNotEquals(0, $object->locks->count());
                self::assertTrue(count($object->locks) > 0);

                self::assertNotEquals(0, $object->resources->count());
                self::assertTrue(count($object->resources) > 0);
        }

        /**
         * @covers OpenExam\Models\Exam::getSource
         * @group model
         */
        public function testGetSource()
        {
                $object = new ExamModel();
                $expect = "exams";
                $actual = $object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

        /**
         * Test model behavior.
         * @group model
         */
        public function testBehavior()
        {
                $user = $this->getDI()->get('user');

                /**
                 * Test ownership:
                 */
                // 
                // Test caller on create:
                // 
                $expect = $user->getPrincipalName();
                $object = new Exam();
                $object->assign($this->sample->getSample('exam', false));
                $object->creator = null;

                self::assertTrue($object->create());
                self::assertNotNull($object->creator);
                self::assertTrue(is_string($object->creator));

                $actual = $object->creator;
                self::assertEquals($expect, $actual);

                $object->delete();

                // 
                // Test custom user on create:
                //                 
                $expect = 'user@example.com';
                $object = new Exam();
                $object->assign($this->sample->getSample('exam', false));
                $object->creator = $expect;

                self::assertTrue($object->create());
                self::assertNotNull($object->creator);
                self::assertTrue(is_string($object->creator));

                $actual = $object->creator;
                self::assertEquals($expect, $actual);

                // 
                // Test caller on update:
                // 
                $expect = $user->getPrincipalName();
                $object->creator = $user->getPrincipalName();

                self::assertTrue($object->update());
                self::assertNotNull($object->creator);
                self::assertTrue(is_string($object->creator));

                $actual = $object->creator;
                self::assertEquals($expect, $actual);

                // 
                // Test custom user on update:
                // 
                $expect = 'user@example.com';
                $object->creator = $expect;

                self::assertTrue($object->update());
                self::assertNotNull($object->creator);
                self::assertTrue(is_string($object->creator));

                $actual = $object->creator;
                self::assertEquals($expect, $actual);

                $object->delete();
        }

        /**
         * @covers OpenExam\Models\Exam::create
         * @group model
         */
        public function testCreate()
        {
                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ pass: primary role unset");
                foreach ($roles as $role) {
                        $user->setPrimaryRole(null);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        self::assertTrue($this->create($model, $user, true));
                        $this->cleanup($model);
                }

                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user not authenticated");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        self::assertTrue($this->create($model, $user, false));
                        $this->cleanup($model);
                }

                $user = new User((new UniqueUser())->user);
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user without roles");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        self::assertTrue($this->create($model, $user, false));
                        $this->cleanup($model);
                }

                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);

                self::info("rolemap=%s", print_r($roles, true));

                self::info("+++ pass: user has roles");
                foreach ($roles as $role => $actions) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        if (in_array('create', $actions)) {
                                self::assertTrue($this->create($model, $user, true));   // action allowed
                        } else {
                                self::assertTrue($this->create($model, $user, false));  // action denied
                        }
                        $this->cleanup($model);
                }
        }

        /**
         * @covers OpenExam\Models\Exam::update
         * @group model
         */
        public function testUpdate()
        {
                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ pass: primary role unset");
                foreach ($roles as $role) {
                        $user->setPrimaryRole(null);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        self::assertTrue($this->update($model, $user, true));
                }

                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user not authenticated");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        self::assertTrue($this->update($model, $user, false));
                }

                $user = new User((new UniqueUser())->user);
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user without roles");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        self::assertTrue($this->update($model, $user, false));
                }

                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);

                self::info("sample=%s", print_r($this->sample->getSample(self::MODEL), true));
                self::info("rolemap=%s", print_r($roles, true));

                self::info("+++ pass: user has roles");
                foreach ($roles as $role => $actions) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        if (in_array('update', $actions)) {
                                self::assertTrue($this->update($model, $user, true));   // action allowed
                        } else {
                                self::assertTrue($this->update($model, $user, false));  // action denied
                        }
                }
        }

        /**
         * @covers OpenExam\Models\Exam::delete
         * @group model
         */
        public function testDelete()
        {
                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ pass: primary role unset");
                foreach ($roles as $role) {
                        $user->setPrimaryRole(null);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        $this->persist($model);
                        self::assertTrue($this->delete($model, $user, true));
                        $this->cleanup($model);
                }

                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user not authenticated");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        $this->persist($model);
                        self::assertTrue($this->delete($model, $user, false));
                        $this->cleanup($model);
                }

                $user = new User((new UniqueUser())->user);
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user without roles");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        $this->persist($model);
                        self::assertTrue($this->delete($model, $user, false));
                        $this->cleanup($model);
                }

                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);

                self::info("sample=%s", print_r($this->sample->getSample(self::MODEL), true));
                self::info("rolemap=%s", print_r($roles, true));

                self::info("+++ pass: user has roles");
                foreach ($roles as $role => $actions) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL, false));
                        $this->persist($model);
                        if (in_array('delete', $actions)) {
                                self::assertTrue($this->delete($model, $user, true));   // action allowed
                        } else {
                                self::assertTrue($this->delete($model, $user, false));  // action denied
                        }
                        $this->cleanup($model);
                }
        }

        /**
         * @covers OpenExam\Models\Exam::find
         * @group model
         */
        public function testRead()
        {
                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ pass: primary role unset");
                foreach ($roles as $role) {
                        $user->setPrimaryRole(null);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        self::assertTrue($this->read($model, $user, true));
                }

                $user = new User();
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user not authenticated");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        self::assertTrue($this->read($model, $user, false));
                }

                $user = new User((new UniqueUser())->user);
                $roles = $this->capabilities->getRoles();

                self::info("+++ fail: user without roles");
                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        self::assertTrue($this->read($model, $user, false));
                }

                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);

                self::info("sample=%s", print_r($this->sample->getSample(self::MODEL), true));
                self::info("rolemap=%s", print_r($roles, true));

                self::info("+++ pass: user has roles");
                foreach ($roles as $role => $actions) {
                        $user->setPrimaryRole($role);
                        $model = new Exam();
                        $model->assign($this->sample->getSample(self::MODEL));
                        if (in_array('read', $actions)) {
                                self::assertTrue($this->read($model, $user, true));   // action allowed
                        } else {
                                self::assertTrue($this->read($model, $user, false));  // action denied
                        }
                }
        }

        /**
         * Specific test of Exam::query()
         * @covers OpenExam\Models\Exam::query
         * @group model
         */
        public function testQuery()
        {
                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);
                $roles = array_merge(array(null), array_keys($roles));

                $sample = $this->sample->getSample(self::MODEL);

                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);

                        $criteria = Exam::query();
                        $result = $criteria->execute();
                        self::assertNotNull($result);
                        foreach ($result as $model) {
                                self::assertNotNull($model);
                        }

                        $criteria = Exam::query();
                        $criteria
                            ->andWhere("name = :name: AND details = :details:", array(
                                    'name'    => $sample['name'],
                                    'details' => $sample['details']
                            ))
                            ->betweenWhere("created", "2014-01-01", "2015-12-31")
                            ->inWhere("id", array(1, $sample['id']))
                            ->orderBy("name desc, details");
                        $result = $criteria->execute();
                        self::assertNotNull($result);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        $criteria = Exam::query();
                        $criteria
                            ->andWhere("name = ?0 AND details = ?1", array(
                                    $sample['name'],
                                    $sample['details']
                            ))
                            ->betweenWhere("created", "2014-01-01", "2020-01-01")
                            ->inWhere("id", array(1, $sample['id']))
                            ->orderBy("name desc, details");
                        $result = $criteria->execute();
                        self::assertNotNull($result);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);
                }
        }

        /**
         * Specific test of Exam::findFirst()
         * @covers OpenExam\Models\Exam::findFirst
         * @group model
         */
        public function testFindFirst()
        {
                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);
                $roles = array_merge(array(null), array_keys($roles));

                $sample = $this->sample->getSample(self::MODEL);

                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);

                        $model = Exam::findFirst();
                        self::assertNotNull($model);

                        $model = Exam::findFirst($sample['id']);
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        $model = Exam::findFirstById($sample['id']);
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        $model = Exam::findFirst(array(
                                    "conditions" => "name = :name:",
                                    "bind"       => array(
                                            'name' => $sample['name'])
                        ));
                        self::assertNotNull($model);
                        self::assertTrue($model->name == $sample['name']);
                }
        }

        /**
         * Specific test of Exam::find()
         * @group model
         */
        public function testFind()
        {
                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);
                $roles = array_merge(array(null), array_keys($roles));

                $sample = $this->sample->getSample(self::MODEL);

                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);

                        $result = Exam::find();
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        foreach ($result as $model) {
                                self::assertNotNull($model);
                        }

                        $result = Exam::find(sprintf("id = %d", $sample['id']));
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        $result = Exam::find(sprintf("id = %d AND name = '%s'", $sample['id'], $sample['name']));
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        $result = Exam::find(array(
                                    "conditions" => "id = ?0 AND name = ?1",
                                    "bind"       => array($sample['id'], $sample['name'])
                        ));
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        $result = Exam::find(array(
                                    "conditions" => "id = :id: AND name = :name:",
                                    "bind"       => array(
                                            'id'   => $sample['id'],
                                            'name' => $sample['name'])
                        ));
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);
                }
        }

        /**
         * Specific test of PHQL over the Exam model.
         * @group model
         */
        public function testPhql()
        {
                $user = $this->getDI()->get('user');
                $roles = $this->capabilities->getRoles(self::MODEL);
                $roles = array_merge(array(null), array_keys($roles));

                $sample = $this->sample->getSample(self::MODEL);
                $modman = $this->getDI()->get('modelsManager');

                foreach ($roles as $role) {
                        $user->setPrimaryRole($role);

                        // 
                        // Equivalent to Exam::find():
                        // 
                        $result = $modman->executeQuery(
                            Exam::getQuery("SELECT Exam.* FROM Exam")
                        );
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        foreach ($result as $model) {
                                self::assertNotNull($model);
                        }

                        // 
                        // Equivalent to Exam::findFirst():
                        // 
                        $result = $modman->executeQuery(
                            Exam::getQuery("SELECT Exam.* FROM Exam LIMIT 1")
                        );
                        $model = $result->getFirst();
                        self::assertNotNull($model);

                        // 
                        // Equivalent to Exam::findFirstById($sample['id']):
                        // 
                        $result = $modman->executeQuery(
                            Exam::getQuery("SELECT Exam.* FROM Exam WHERE Exam.id = " . $sample['id'])
                        );
                        $model = $result->getFirst();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        // 
                        // Equivalent to Exam::find(sprintf("id = %d AND name = '%s'", $sample['id'], $sample['name'])):
                        // 
                        $result = $modman->executeQuery(
                            Exam::getQuery(sprintf(
                                    "SELECT Exam.* FROM Exam WHERE Exam.id = %d AND Exam.name = '%s'", $sample['id'], $sample['name']
                                )
                        ));
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        // 
                        // Equivalent to:
                        // 
                        // Exam::find(array(
                        //      "conditions" => "id = ?0 AND name = ?1",
                        //      "bind"       => array($sample['id'], $sample['name'])
                        // );
                        $result = $modman->executeQuery(
                            Exam::getQuery(
                                "SELECT Exam.* FROM Exam WHERE Exam.id = ?0 AND Exam.name = ?1"
                            ), array($sample['id'], $sample['name'])
                        );
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        // 
                        // Equivalent to:
                        // 
                        // Exam::find(array(
                        //      "conditions" => "id = :id: AND name = :name:",
                        //      "bind"       => array(
                        //              'id' => $sample['id'], 'name' => $sample['name']
                        //      )
                        // );
                        $result = $modman->executeQuery(
                            Exam::getQuery(
                                "SELECT Exam.* FROM Exam WHERE Exam.id = :id: AND Exam.name = :name:"
                            ), array('id' => $sample['id'], 'name' => $sample['name'])
                        );
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->id == $sample['id']);

                        // 
                        // Equivalent to:
                        // 
                        // Exam::find(array(
                        //      "conditions" => "name = :name: AND created BETWEEN :start: AND :end:",
                        //      "bind"       => array(
                        //              'name'  => $sample['name'],
                        //              'start' => '2014-01-01', 
                        //              'end'   => '2020-01-01'
                        //      )
                        // );
                        $result = $modman->executeQuery(
                            Exam::getQuery(
                                "SELECT Exam.* FROM Exam WHERE Exam.name = :name: AND created BETWEEN :start: AND :end:"
                            ), array('name' => $sample['name'], 'start' => '2014-01-01', 'end' => '2020-01-01')
                        );
                        self::assertNotNull($result);
                        self::assertTrue(count($result) > 0);
                        $model = $result->getLast();
                        self::assertNotNull($model);
                        self::assertTrue($model->name == $sample['name']);
                }
        }

}
