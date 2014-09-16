<?php

namespace OpenExam\Models;

use Exception;
use OpenExam\Tests\Phalcon\TestModel;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-15 at 18:28:47.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class StudentTest extends TestModel
{

        public function __construct()
        {
                parent::__construct(new Student());
        }

        /**
         * @covers OpenExam\Models\Students::properties
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
                        $helper = new TestModel(new Student());
                        $helper->tryPersist();
                        self::fail("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected exception
                }

                try {
                        $helper = new TestModel(new Student());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::fail($exception);
                }

                $values = array(
                        'exam_id'      => Exam::findFirst()->id,
                        'user'         => 'user1',
                        'code'         => '1234ABCD',
                        'non_existing' => 666   // ignored wihout error
                );
                try {
                        $helper = new TestModel(new Student());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::fail("Unexcepted constraint violation exception");
                }
        }

        /**
         * @covers OpenExam\Models\Student::getSource
         * @group model
         */
        public function testGetSource()
        {
                $expect = "students";
                $actual = $this->object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}
