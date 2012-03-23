<?php
/**
 * This file is part of GrottoCenter.
 *
 * GrottoCenter is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GrottoCenter is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with GrottoCenter.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright (c) 2009-2012 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../../conf/config.php");
include("../../func/function.php");
include("../../html/declaration.php");
include("../../func/upload.php");
if (USER_IS_CONNECTED){
  $options = "";
  $attachment_file = (isset($_POST['attachment_file'])) ? $_POST['attachment_file'] : '';
  switch ($type) {
    case "add_attachment":
      if ($error == "") {
        if (move_uploaded_file($_FILES[$source_file_fieldname]['tmp_name'], $target_filename)) {
          $attachment_original_filename = (isset($_POST['attachment_filename'])) ? $_POST['attachment_filename'] : '';
          $options .= "&original_filename=".urlencode($attachment_original_filename);
          $attachment_file = $target_filename;
        } else {
          $error = "Error while uploading file!";
        }
      }
    break;
    case "delete_attachment":
      if ($attachment_file != "") {
        $file_id = (isset($_POST['file_id'])) ? $_POST['file_id'] : '';
        $options .= "&file_id=".$file_id;
        if (@file_exists($attachment_file)) {
          if (@unlink($attachment_file)) {
            $attachment_file = "";
          } else {
            $error = "Error while deleting file!";
          }
        } else {
          $attachment_file = "";
        }
      } else {
        $error = "No file found";
      }
  	break;
    default:
      exit();
  	break;
  }
  if ($error != "") {
    $options .= "&error=".urlencode($error);
  } else {
    $options .= "&attachment_changed=true&attachment_name=".urlencode($attachment_file);
  }
  header("location:".$source_manager.$options);
  exit();
}
?>