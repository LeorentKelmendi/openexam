<?php

/*
 * Copyright (C) 2014-2018 The OpenExam Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace OpenExam\Library\Render;

use OpenExam\Tests\Phalcon\TestCase;

class DomTreeRender {

}

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-27 at 14:39:20.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class RenderServiceTest extends TestCase {

  /**
   * @var RenderService
   */
  private $_object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {
    $this->_object = new RenderService();
    $this->_object->setMethod('command');
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown() {

  }

  /**
   * @covers OpenExam\Library\Render\RenderService::getRender
   * @group render
   */
  public function testGetRender() {
    //
    // Test that correct object is returned:
    //
    $render1 = $this->_object->getRender(Renderer::FORMAT_PDF);
    self::assertNotNull($render1);
    self::assertEquals(get_class($render1), __NAMESPACE__ . '\\Command\\RenderPdfDocument');

    //
    // Test that same object is returned:
    //
    $render2 = $this->_object->getRender(Renderer::FORMAT_PDF);
    self::assertSame($render2, $render1);

    //
    // Test that correct object is returned:
    //
    $render1 = $this->_object->getRender(Renderer::FORMAT_IMAGE);
    self::assertNotNull($render1);
    self::assertEquals(get_class($render1), __NAMESPACE__ . '\\Command\\RenderImage');

    //
    // Test that same object is returned:
    //
    $render2 = $this->_object->getRender(Renderer::FORMAT_IMAGE);
    self::assertSame($render2, $render1);
  }

  /**
   * @covers OpenExam\Library\Render\RenderService::setRender
   * @group render
   */
  public function testSetRender() {
    $this->_object->setRender('dom', new DomTreeRender());

    $render1 = $this->_object->getRender('dom');
    self::assertNotNull($render1);
    self::assertEquals(get_class($render1), __NAMESPACE__ . '\\DomTreeRender');
  }

  /**
   * Test dependency injector.
   * @group render
   */
  public function testService() {
    self::assertNotNull($this->render);
    self::assertEquals(get_class($this->render), get_class($this->_object));
  }

}
