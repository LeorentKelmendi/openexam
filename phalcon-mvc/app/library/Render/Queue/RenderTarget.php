<?php

/*
 * Copyright (C) 2017-2018 The OpenExam Project
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

//
// File:    RenderTarget.php
// Created: 2017-12-07 18:26:17
//
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
//

namespace OpenExam\Library\Render\Queue;

use OpenExam\Library\Render\Exception as RenderException;
use OpenExam\Models\Render;
use OpenExam\Models\Student;
use Phalcon\Mvc\User\Component;

/**
 * The render target handler.
 *
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class RenderTarget extends Component {

  /**
   * The render model.
   * @var Render
   */
  private $_render;
  /**
   * The student model.
   * @var Student
   */
  private $_student;

  /**
   * Constructor.
   *
   * @param Render $render The render model.
   * @param Student $student Optional student model.
   */
  public function __construct($render, $student = null) {
    if (!isset($render->type)) {
      throw new RenderException("The render type is missing");
    }

    switch ($render->type) {
    case Render::TYPE_ARCHIVE:
      if (!isset($render->exam_id)) {
        throw new RenderException("The render exam_id is missing");
      }
      break;
    case Render::TYPE_EXPORT:
    case Render::TYPE_EXTERN:
      if (!isset($render->url)) {
        throw new RenderException("The render url is missing");
      }
      break;
    case Render::TYPE_RESULT:
      if (!isset($render->exam_id)) {
        throw new RenderException("The render exam_id is missing");
      }
      if (!isset($student->id)) {
        throw new RenderException("The student id is missing");
      }
      break;
    }

    $this->_render = $render;
    $this->_student = $student;
  }

  /**
   * Set render model path.
   */
  public function setPath() {
    $render = $this->_render;

    switch ($render->type) {
    case Render::TYPE_ARCHIVE:
      $render->path = $this->getPath(
        Render::TYPE_ARCHIVE, $render->exam_id
      );
      break;
    case Render::TYPE_EXPORT:
      $render->path = $this->getPath(
        Render::TYPE_EXPORT, md5($render->url)
      );
      break;
    case Render::TYPE_EXTERN:
      $render->path = $this->getPath(
        Render::TYPE_EXTERN, md5($render->url)
      );
      break;
    case Render::TYPE_RESULT:
      $render->path = $this->getPath(
        Render::TYPE_RESULT, sprintf("%s/%s", $render->exam_id, $this->_student->id)
      );
      break;
    }

    $this->usePath($render->path);
  }

  /**
   * Get target path.
   *
   * @param string $type The render type.
   * @param string $path The sub path.
   * @return string
   */
  private function getPath($type, $path) {
    return sprintf("%s/%s.pdf", $type, $path);
  }

  /**
   * Ensure that path exist.
   *
   * @param string $target The target path (relative).
   * @throws RenderException
   */
  private function usePath($target) {
    $file = sprintf("%s/%s", $this->config->application->cacheDir, $target);
    $path = dirname($file);

    if (!file_exists($path)) {
      if (!mkdir($path, 0755, true)) {
        throw new RenderException("Failed create target directory");
      }
      if (!is_writable($path)) {
        throw new RenderException("Target directory is not writable");
      }
    }
  }

}
