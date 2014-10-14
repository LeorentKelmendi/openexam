<?php

namespace OpenExam\Models;

use Exception;
use OpenExam\Tests\Phalcon\TestCase;
use OpenExam\Tests\Phalcon\TestModelAccess;
use OpenExam\Tests\Phalcon\TestModelBasic;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class TopicModel extends Topic
{

        public function initialize()
        {
                parent::initialize();
        }

}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-15 at 20:22:33.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class TopicTest extends TestCase
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
                $topic = Topic::findFirst();
                self::assertNotNull($topic);

                self::assertNotEquals(0, $topic->exam->count());
                self::assertTrue(count($topic->exam) == 1);

                $topic = Question::findFirst()->topic;
                self::assertNotNull($topic);

                self::assertNotEquals(0, $topic->questions->count());
                self::assertTrue(count($topic->questions) >= 0);
        }

        /**
         * @group model
         */
        public function testProperties()
        {
                $values = array(
                        'exam_id' => Exam::findFirst()->id,
                        'name'    => 'Name1'
                );

                try {
                        $helper = new TestModelBasic(new Topic());
                        $helper->tryPersist();
                        self::error("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected exception
                }

                try {
                        $helper = new TestModelBasic(new Topic());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'exam_id' => Exam::findFirst()->id,
                        'name'    => 'Name1',
                        'grades'  => '// Ooh, really missing C++ ;-)',
                        'depend'  => 'Depend1'
                );

                try {
                        $helper = new TestModelBasic(new Topic());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'exam_id'      => Exam::findFirst()->id,
                        'name'         => 'Name1',
                        'non_existing' => 666
                );
                try {
                        $helper = new TestModelBasic(new Topic());
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
                $helper = new TestModelAccess(new Topic());
                $helper->testModelAccess();
        }

        /**
         * @covers OpenExam\Models\Topic::getSource
         * @group model
         */
        public function testGetSource()
        {
                $object = new TopicModel();
                $expect = "topics";
                $actual = $object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}