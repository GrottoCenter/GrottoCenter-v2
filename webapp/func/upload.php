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
 * @copyright Copyright (c) 2009-1912 Cl�ment Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
if (USER_IS_CONNECTED){
  include("upload_restrictions.php");
  $type = (isset($_POST['upload_type'])) ? $_POST['upload_type'] : '';
  $do_upload = false;
  $error = "";
  switch($type) {
		case "add_avatar":
      $max_size = $upload_restrictions_size_array[$type];
      $extensions = $upload_restrictions_ext_array[$type];
		  $target_folder = $_SESSION['Application_url']."/upload/avatars/";
		  $manager = "avatar.php";
		  $target_filename = $_SESSION['user_login']."-".$_SESSION['user_id'];
		  $source_file_fieldname = "filename";
		  $do_upload = true;
		case "delete_avatar":
      $source_manager = (isset($_POST['source_manager'])) ? $_POST['source_manager'] : '';
		  break;
		case "add_logo":
      $max_size = $upload_restrictions_size_array[$type];
      $extensions = $upload_restrictions_ext_array[$type];
		  $target_folder = $_SESSION['Application_url']."/upload/logos/";
		  $manager = "logo.php";
		  $target_filename = (isset($_POST['target_name'])) ? $_POST['target_name'] : '';
		  $source_file_fieldname = "filename";
		  $do_upload = true;
		case "delete_logo":
      $source_manager = (isset($_POST['source_manager'])) ? $_POST['source_manager'] : '';
		  break;
		case "add_topo":
      $max_size = $upload_restrictions_size_array[$type];
      $extensions = $upload_restrictions_ext_array[$type];
		  $target_folder = $_SESSION['Application_url']."/upload/topos/";
		  $manager = "topo.php";
		  $target_filename = (isset($_POST['target_name'])) ? $_POST['target_name'] : '';
		  $source_file_fieldname = "filename";
		  $do_upload = true;
		case "delete_topo":
      $source_manager = (isset($_POST['source_manager'])) ? $_POST['source_manager'] : '';
		  break;
		case "add_attachment":
      $max_size = $upload_restrictions_size_array[$type];
      $extensions = $upload_restrictions_ext_array[$type];
		  $target_folder = $_SESSION['Application_url']."/upload/attachments/";
		  $manager = "attachment.php";
		  $target_filename = (isset($_POST['target_name'])) ? $_POST['target_name'] : '';
		  $source_file_fieldname = "filename";
		  $do_upload = true;
		case "delete_attachment":
      $source_manager = (isset($_POST['source_manager'])) ? $_POST['source_manager'] : '';
		  break;
		default:
		  $error = "Not supported.";
		  break;
  }
  
  function is_uploaded_file_4_0_2($filename) {
    if (!$tmp_file = get_cfg_var('upload_tmp_dir')) {
      $tmp_file = dirname(tempnam('', ''));
    }
    $tmp_file .= '/' . basename($filename);
    return (ereg_replace('/+', '/', $tmp_file) == $filename);
  }
  
  function is_uploadable_file($filename, $destination) {
    switch($destination) {
		  case "add_avatar":
		  case "add_logo":
        if (!exif_imagetype($filename)) {
          return false;
        } else {
          return true;
        }
  		break;
		  case "add_topo":
		  case "add_attachment":
        return true;
  		break;
  		default:
  		  return false;
  		break;
  	}
  }
  
  if ($error == "" && $do_upload) {
    if (isset($_FILES[$source_file_fieldname])) {
      if (is_uploaded_file_4_0_2($_FILES[$source_file_fieldname]['tmp_name'])) {
        $size = filesize($_FILES[$source_file_fieldname]['tmp_name']);
        $extension = strrchr($_FILES[$source_file_fieldname]['name'], '.');
        if(!in_array(strtolower($extension), $extensions) || !is_uploadable_file($_FILES[$source_file_fieldname]['tmp_name'], $type)) {
          $error = 'Wrong file type!<br />Allowed are :<br /><ul>';
          foreach ($extensions as $allowed_ext) {
            $error .= "<li>".$allowed_ext."</li>";
          }
          $error .= "</ul>";
        }
        if($size>$max_size) {
          $error = 'File is too big! Max.: '.round($max_size/1000, 2).'Ko.';
        }
        if($error == "") {
          //$target_filename = strtr($target_filename,'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝÑàáâãäåçèéêëìíîïðòóôõöùúûüýÿñ','AAAAAACEEEEIIIIOOOOOUUUUYNaaaaaaceeeeiiiioooooouuuuyyn');
          //$target_filename = preg_replace('/([^.a-z0-9]+)/i', '-', $target_filename);
          $target_filename = cleanString($target_filename);
          //$target_filename .= rand();
          $target_filename .= $extension;
        }
      } else {
        if(empty($_FILES['fichier_source']['tmp_name'])) {
          $error = "Select a file, please!";
        } else {
          $error = "No hack, please!";
        }
      }
    } else {
      $error = "Variable _FILES not supported, please report this message to your webdesigner : _FILES['".$source_file_fieldname."'] is not set.";
    }
  }
}
?>
