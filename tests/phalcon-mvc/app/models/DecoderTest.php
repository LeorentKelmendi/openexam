<?php

namespace OpenExam\Models;

use Exception;
use OpenExam\Tests\Phalcon\TestCase;
use OpenExam\Tests\Phalcon\TestModelAccess;
use OpenExam\Tests\Phalcon\TestModelBasic;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class DecoderModel extends Decoder
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
class DecoderTest extends TestCase
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
                $decoder = Decoder::findFirst();
                self::assertNotNull($decoder);

                self::assertNotEquals(0, $decoder->exam->count());
                self::assertTrue(count($decoder->exam) == 1);
        }

        /**
         * @group model
         */
        public function testProperties()
        {
                $values = array(
                        'exam_id' => Exam::findFirst()->id,
                        'user'    => 'user1'
                );

                try {
                        $helper = new TestModelBasic(new Decoder());
                        $helper->tryPersist();
                        self::error("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected exception
                }

                try {
                        $helper = new TestModelBasic(new Decoder());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'exam_id'      => Exam::findFirst()->id,
                        'user'         => 'user1',
                        'non_existing' => 666   // ignored wihout error
                );
                try {
                        $helper = new TestModelBasic(new Decoder());
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
                $helper = new TestModelAccess(new Decoder());
                $helper->testModelAccess();
        }

        /**
         * @covers OpenExam\Models\Decoder::getSource
         * @group model
         */
        public function testGetSource()
        {
                $object = new DecoderModel();
                $expect = "decoders";
                $actual = $object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}