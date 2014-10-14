<?php

namespace OpenExam\Models;

use Exception;
use OpenExam\Tests\Phalcon\TestCase;
use OpenExam\Tests\Phalcon\TestModelAccess;
use OpenExam\Tests\Phalcon\TestModelBasic;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ComputerModel extends Computer
{

        public function initialize()
        {
                parent::initialize();
        }

}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-15 at 18:04:22.
 * 
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ComputerTest extends TestCase
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
                $computer = Computer::findFirst();
                self::assertNotNull($computer);

                self::assertNotEquals(0, $computer->locks->count());
                self::assertNotEquals(0, $computer->room->count());

                self::assertTrue(count($computer->locks) > 0);
                self::assertTrue(count($computer->room) == 1);
        }

        /**
         * @group model
         */
        public function testProperties()
        {
                $values = array(
                        'room_id'  => Room::findFirst()->id,
                        'ipaddr'   => '127.0.0.1',
                        'port'     => 4092,
                        'password' => 'secret'
                );

                try {
                        $helper = new TestModelBasic(new Computer());
                        $helper->tryPersist();
                        self::error("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected exception
                }

                try {
                        $helper = new TestModelBasic(new Computer());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'room_id'  => Room::findFirst()->id,
                        'created'  => date('Y-m-d H:i:s'),
                        'updated'  => date('Y-m-d H:i:s'),
                        'hostname' => 'localhost',
                        'ipaddr'   => '127.0.0.1',
                        'port'     => 4092,
                        'password' => 'secret'
                );
                try {
                        $helper = new TestModelBasic(new Computer());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'room_id'      => Room::findFirst()->id,
                        'created'      => date('Y-m-d H:i:s'),
                        'updated'      => date('Y-m-d H:i:s'),
                        'non_existing' => 666
                );
                try {
                        $helper = new TestModelBasic(new Computer());
                        $helper->tryPersist();
                        self::error("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected excetion (user required)
                }
        }

        /**
         * @group model
         * @group security
         */
        public function testAccess()
        {
                $helper = new TestModelAccess(new Computer());
                $helper->testModelAccess();
        }

        /**
         * @covers OpenExam\Models\Computer::getSource
         * @group model
         */
        public function testGetSource()
        {
                $object = new ComputerModel();
                $expect = "computers";
                $actual = $object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}