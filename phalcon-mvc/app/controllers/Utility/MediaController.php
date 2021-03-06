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

//
// File:    ExamController.php
// Created: 2014-09-19 11:15:15
//
// Author:  Ahsan Shahzad (MedfarmDoIT)
//          Anders Lövgren (BMC-IT)
//

namespace OpenExam\Controllers\Utility;

use DateTime;
use Exception;
use OpenExam\Controllers\GuiController;
use OpenExam\Library\Core\Error;
use OpenExam\Library\Core\Size;
use OpenExam\Models\Resource;
use Phalcon\Mvc\View;
use RuntimeException;
use UploadHandler;

require_once EXTERN_DIR . 'UploadHandler.php';

/**
 * Controller for loading media resources
 *
 * @author Ahsan Shahzad (MedfarmDoIT)
 */
class MediaController extends GuiController {

  /**
   * Shows media library interface to upload / select lib resources
   * utility/media/library
   *
   * @param int $exam_id The exam ID (POST).
   */
  public function libraryAction() {
    //
    // Sanitize:
    //
    if (!($eid = $this->dispatcher->getParam("eid"))) {
      throw new Exception("Missing or invalid exam ID", Error::PRECONDITION_FAILED);
    }

    //
    // Check route access:
    //
    $this->checkAccess(array(
      'eid' => $eid,
    ));

    //
    // Fetch shared resources filtered on sharing levels:
    //
    if (!($sres = Resource::find(array(
      'conditions' => "
                                    (shared = :private: AND user = ?2)
                                        OR
                                    (shared = :exam: AND exam_id = ?1)
                                        OR
                                    (shared = :group: AND user = ?3)
                                        OR
                                    (shared = :global:)",
      'bind' => array(
        'private' => Resource::NOT_SHARED,
        'exam' => Resource::SHARED_EXAM,
        'group' => Resource::SHARED_GROUP,
        'global' => Resource::SHARED_GLOBAL,
        1 => $eid,
        2 => $this->user->getPrincipalName(),
        3 => $this->user->getPrincipalName(),
      ),
      'order' => 'shared,id desc',
    )))) {
      throw new Exception("Failed fetch shared resources", Error::BAD_REQUEST);
    }

    //
    // Fetch personal resource files:
    //
    if (!($pres = Resource::find(array(
      'conditions' => "user = :user: AND exam_id != :exam:",
      'bind' => array(
        'user' => $this->user->getPrincipalName(),
        'exam' => $eid,
      ),
      'order' => 'id desc',
    )))) {
      throw new Exception("Failed fetch personal resources", Error::BAD_REQUEST);
    }

    //
    // Filter shared resources on mime-type:
    //
    $simage = $sres->filter(function ($resource) {
      if ($resource->type == 'image') {
        return $resource;
      }
    });
    $svideo = $sres->filter(function ($resource) {
      if ($resource->type == 'video') {
        return $resource;
      }
    });
    $saudio = $sres->filter(function ($resource) {
      if ($resource->type == 'audio') {
        return $resource;
      }
    });
    $sother = $sres->filter(function ($resource) {
      if (!in_array($resource->type, array('image', 'video', 'audio'))) {
        return $resource;
      }
    });

    //
    // Filter personal resources on mime-type:
    //
    $pimage = $pres->filter(function ($resource) {
      if ($resource->type == 'image') {
        return $resource;
      }
    });
    $pvideo = $pres->filter(function ($resource) {
      if ($resource->type == 'video') {
        return $resource;
      }
    });
    $paudio = $pres->filter(function ($resource) {
      if ($resource->type == 'audio') {
        return $resource;
      }
    });
    $pother = $pres->filter(function ($resource) {
      if (!in_array($resource->type, array('image', 'video', 'audio'))) {
        return $resource;
      }
    });

    $this->view->setVar('sres', array(
      "image" => $simage,
      "video" => $svideo,
      "audio" => $saudio,
      "other" => $sother,
    ));

    $this->view->setVar('pres', array(
      "image" => $pimage,
      "video" => $pvideo,
      "audio" => $paudio,
      "other" => $pother,
    ));

    //
    // No views can handle exceptions, so reset primary role:
    //
    $this->user->setPrimaryRole(null);
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  /**
   * Upload media resources
   * utility/media/upload
   */
  public function uploadAction() {
    $this->view->disable();

    //
    // Check route access:
    //
    $this->checkAccess();

    //
    // Find media type of this file to set file upload directory:
    //
    $files = $this->request->getUploadedFiles(true);
    $media = array();

    //
    // Check number of uploaded files:
    //
    if (count($files) == 0) {
      $maxsize = Size::minimum(array(
        ini_get('post_max_size'),
        ini_get('upload_max_filesize'),
      ));

      throw new RuntimeException(sprintf("Failed upload file (maximum allowed filesize size is %s)", $maxsize));
    }

    //
    // Extract MIME type:
    //
    preg_match('/(.*)\/.*/', $files[0]->getRealType(), $media);
    var_dump($media);
    die();
    //
    // Set target URL and path:
    //
    $uploadDir = $this->config->application->mediaDir . $media[1];
    $uploadUrl = $this->url->get('utility/media/view/' . $media[1]);

    //
    // Upload file:
    //
    $handler = new UploadHandler(array(
      'upload_dir' => $uploadDir . "/",
      'upload_url' => $uploadUrl . "/",
    ));
  }

  /**
   * Send media file.
   *
   * @param string $type The media file type (e.g image, video).
   * @param string $file The file name.
   * @throws Exception
   */
  public function viewAction($type, $file) {
    //
    // Sanity check:
    //
    if (empty($type) || empty($file)) {
      throw new Exception('Invalid request', Error::PRECONDITION_FAILED);
    }

    //
    // Check route access:
    //
    $this->checkAccess();

    //
    // Prevent disclose of system files:
    //
    if (strpos($file, '/') !== false ||
      strpos($type, '/') !== false) {
      throw new Exception('Invalid character in filename', Error::NOT_ACCEPTABLE);
    }

    //
    // Check that file exists:
    //
    $path = sprintf("%s/%s/%s", $this->config->application->mediaDir, $type, $file);

    if (!file_exists($path)) {
      throw new Exception("Can't located requested file", Error::NOT_FOUND);
    }
    if (!is_file($path)) {
      throw new Exception("Requested resource is not a file", Error::NOT_FOUND);
    }

    //
    // Flush output buffering to get chunked mode:
    //
    while (ob_get_level()) {
      ob_end_clean();
      ob_end_flush();
    }

    //
    // Required by some browsers for actually caching:
    //
    $expires = new DateTime();
    $expires->modify("+2 months");

    //
    // Disable view:
    //
    $this->view->disable();
    $this->view->finish();

    //
    // Set headers for cache and chunked transfer mode:
    //
    $this->response->setContentType(mime_content_type($path));
    $this->response->setHeader("Cache-Control", "max-age=86400");
    $this->response->setHeader("Pragma", "public");
    $this->response->setExpires($expires);

    //
    // Send response headers and file:
    //
    $this->response->send();
    readfile($path);
  }

}
