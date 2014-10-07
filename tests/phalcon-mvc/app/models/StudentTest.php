<?php

namespace OpenExam\Models;

use Exception;
use OpenExam\Tests\Phalcon\TestCase;
use OpenExam\Tests\Phalcon\TestModelAccess;
use OpenExam\Tests\Phalcon\TestModelBasic;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class StudentModel extends Student
{

        public function initialize()
        {
                parent::initialize();
        }

}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-15 at 18:28:47.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class StudentTest extends TestCase
{

        protected function setUp()
        {
                $this->getDI()->get('user')->setPrimaryRole(null);
        }

        /**
         * @group model
         */
        public function testRelations()
        {
                $student = Student::findFirst();
                self::assertNotNull($student);

                self::assertNotEquals(0, $student->exam->count());
                self::assertTrue(count($student->exam) == 1);

                $student = Answer::findFirst()->student;
                self::assertNotNull($student);

                self::assertNotEquals(0, $student->answers->count());
                self::assertTrue(count($student->answers) >= 0);
        }

        /**
         * @group model
         */
        public function testProperties()
        {
                $values = array(
                        'exam_id' => Exam::findFirst()->id,
                        'user'    => 'user1',
                        'code'    => '1234ABCD'
                );

                try {
                        $helper = new TestModelBasic(new Student());
                        $helper->tryPersist();
                        self::error("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected exception
                }

                try {
                        $helper = new TestModelBasic(new Student());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'exam_id'      => Exam::findFirst()->id,
                        'user'         => 'user1',
                        'code'         => '1234ABCD',
                        'non_existing' => 666   // ignored wihout error
                );
                try {
                        $helper = new TestModelBasic(new Student());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error("Unexcepted constraint violation exception");
                }
        }

        /**
         * @group model
         * @group security
         */
        public function testAccess()
        {
                $helper = new TestModelAccess(new Student());
                $helper->testModelAccess();
        }

        /**
         * @covers OpenExam\Models\Student::getSource
         * @group model
         */
        public function testGetSource()
        {
                $object = new StudentModel();
                $expect = "students";
                $actual = $object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}
