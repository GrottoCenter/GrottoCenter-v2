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
 * @copyright Copyright (c) 2009-1912 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../../conf/config.php");
include("../../func/function.php");
include("../../html/declaration.php");
include("../../func/upload.php");
if (USER_IS_CONNECTED){
  switch ($type) {
    case "add_avatar":
      if ($error == "") {
        if (move_uploaded_file($_FILES[$source_file_fieldname]['tmp_name'], $target_filename)) {
          $avatar_file = $target_filename;
        } else {
          $error = "Error while uploading file!";
        }
      }
    break;
    case "delete_avatar":
      $sql = "SELECT Picture_file_name FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE `Id` = ".$_SESSION['user_id'];
      $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
      $avatar_file = $data[0]['Picture_file_name'];
      if ($avatar_file != "") {
        if (@file_exists($avatar_file)) {
          if (@unlink($avatar_file)) {
            $avatar_file = "";
          } else {
            $error = "Error while deleting file!";
          }
        } else {
          $avatar_file = "";
        }
      }
  	break;
    default:
      exit();
  	break;
  }
  if ($error != "") {
    $options = "&error=".urlencode($error);
  } else {
    $options = "&avatar_changed=true&avatar_file=".$avatar_file;
  }
  header("location:".$source_manager.$options);
  exit();
}
?>