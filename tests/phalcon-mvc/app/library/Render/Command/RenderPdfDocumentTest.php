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

namespace OpenExam\Library\Render\Command;

use OpenExam\Tests\Phalcon\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-27 at 14:30:22.
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class RenderPdfDocumentTest extends TestCase {

  /**
   * Checksum and file size properties:
   */
  private static $_output = array(
    'pdf' => array(
      'md5' => '17ee1ebfdc59a27e6c0d2c21c8626cca',
      'size' => 28840,
      'mime' => 'application/pdf',
      'type' => 'PDF document, version 1.4',
    ),
    'unlink' => false,
    'check' => false,
  );
  /**
   * @var RenderPdfDocument
   */
  private $_object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {
    $this->_object = new RenderPdfDocument('wkhtmltopdf --outline --outline-depth 2 --disable-forms');
    $this->cwd = getcwd();
    chdir(__DIR__);
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown() {
    chdir($this->cwd);
  }

  /**
   * @covers OpenExam\Library\Render\RenderPdfDocument::save
   * @group render
   */
  public function testSave() {
    $objects = array(array('page' => '../index.html'));
    $filename = sprintf("%s/render-pdf-document-test.pdf", sys_get_temp_dir());

    self::assertTrue($this->_object->save($filename, $objects));
    self::assertTrue(file_exists($filename));
    self::assertTrue(filesize($filename) != 0);

    //
    // Replace timestamp:
    //
    $content = file_get_contents($filename);
    $content = preg_replace("/CreationDate \(.*?\)/", "CreationDate (D:20141127140623+01'00')", $content);
    file_put_contents($filename, $content);

    if (self::$_output['check']) {
      self::assertEquals(self::$_output['pdf']['size'], filesize($filename));
      self::assertEquals(self::$_output['pdf']['md5'], md5_file($filename));
    }
    if (self::$_output['unlink'] && file_exists($filename)) {
      unlink($filename);
    }
  }

}
