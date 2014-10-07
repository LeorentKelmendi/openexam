<?php

namespace OpenExam\Models;

use Exception;
use OpenExam\Tests\Phalcon\TestCase;
use OpenExam\Tests\Phalcon\TestModelAccess;
use OpenExam\Tests\Phalcon\TestModelBasic;

/**
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class FileModel extends File
{

        public function initialize()
        {
                parent::initialize();
        }

}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-21 at 15:11:16.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class FileTest extends TestCase
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
                $file = File::findFirst();
                self::assertNotNull($file);

                self::assertNotEquals(0, $file->answer->count());
                self::assertTrue(count($file->answer) == 1);
        }

        /**
         * @group model
         */
        public function testProperties()
        {
                $values = array(
                        'answer_id' => Answer::findFirst()->id,
                        'name'      => 'Name1',
                        'path'      => '/tmp/path',
                        'type'      => 'video',
                        'subtype'   => 'mp4'
                );

                try {
                        $helper = new TestModelBasic(new File());
                        $helper->tryPersist();
                        self::error("Excepted constraint violation exception");
                } catch (Exception $exception) {
                        // Expected exception
                }

                try {
                        $helper = new TestModelBasic(new File());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error($exception);
                }

                $values = array(
                        'answer_id'    => Answer::findFirst()->id,
                        'name'         => 'Name1',
                        'path'         => '/tmp/path',
                        'type'         => 'video',
                        'subtype'      => 'mp4',
                        'non_existing' => 666   // ignored wihout error
                );
                try {
                        $helper = new TestModelBasic(new File());
                        $helper->tryPersist($values);
                } catch (Exception $exception) {
                        self::error("Unexcepted constraint violation exception");
                }

                // 
                // Test shared property:
                // 
                $media = new File();
                $media->assign($values);
                $media->save();
        }

        /**
         * @group model
         * @group security
         */
        public function testAccess()
        {
                $helper = new TestModelAccess(new File());
                $helper->testModelAccess();
        }

        /**
         * @covers OpenExam\Models\Contributor::getSource
         * @group model
         */
        public function testGetSource()
        {
                $object = new FileModel();
                $expect = "files";
                $actual = $object->getSource();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}
