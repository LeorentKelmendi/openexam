<?php

namespace OpenExam\Models;

use OpenExam\Models\ModelBase;
use OpenExam\Tests\Phalcon\TestModelBasic;

class MyModel extends ModelBase
{

        public function initialize()
        {
                parent::initialize();
        }

}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-09-19 at 03:15:59.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class ModelBaseTest extends TestModelBasic
{

        public function __construct()
        {
                parent::__construct(new MyModel());
        }

        /**
         * @covers OpenExam\Models\ModelBase::initialize
         * @group model
         */
        public function testInitialize()
        {
                $this->object->initialize();
        }

        /**
         * @covers OpenExam\Models\ModelBase::getName
         * @group model
         */
        public function testGetName()
        {
                $expect = "mymodel";
                $actual = $this->object->getName();
                self::assertNotNull($actual);
                self::assertEquals($expect, $actual);
        }

}