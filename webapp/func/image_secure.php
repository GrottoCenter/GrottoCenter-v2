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
$path = (isset($_GET['file'])) ? $_GET['file'] : '';
$type = (isset($_GET['type'])) ? $_GET['type'] : '';
//$path = urldecode($path);
$file_separator = '-';
$topo_usr_id_pos = 0;
$topo_usr_id_offset = 1;
$topo_id_pos = 1;
$topo_id_offset = 1;
$random_id_pos = 2;
$random_id_offset = 1;
$code_err = -1;
switch ($type) {
  case "topos":
    $file_split = explode($file_separator, getFileName(basename($path)));
    $topo_usr_id = substr($file_split[$topo_usr_id_pos], $topo_usr_id_offset);
		$topo_id = substr($file_split[$topo_id_pos], $topo_id_offset);
		$random_id = substr($file_split[$random_id_pos], $random_id_offset);
    if (!is_numeric($topo_usr_id) || $topo_usr_id == "" || !is_numeric($topo_id) || $topo_id == "" || $random_id == "") {
      $code_err = 1;
      break;
    }
    $sql = "SELECT T_topography.Is_public, T_file.Path, T_topography.Enabled ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_topo_file` ON J_topo_file.Id_topography = T_topography.Id ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_file` ON T_file.Id = J_topo_file.Id_file ";
    $sql .= "WHERE T_file.Id_author = ".$topo_usr_id." AND T_topography.Id = ".$topo_id." AND T_file.Path LIKE '%u".$topo_usr_id."_t".$topo_id."_r".$random_id."%' ";
    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
    if ($data['Count'] < 1) {
      $code_err = 2;
      break;
    }
    // MANQUE LE CAS OU LA PERSONNE CONNECTEE NE DEVRAIS PAS POUVOIR VOIR LA TOPO !!!
    if (USER_IS_CONNECTED || ($data[0]['Is_public'] == 'YES' && $data[0]['Enabled'] == 'YES')) {
      $filename = basename($data[0]['Path']);
      if (!fileExists($filename)) {
        $code_err = 3;
        break;
      }
      /*$kind = (exif_imagetype($filename)) ? 'image' : 'application';
      header('Content-Type: '.$kind.'/'.strtolower(getFileExtension($filename)));*/
      header('Content-Type: image/'.strtolower(getFileExtension($filename)));
      readfile($filename);
    } else {
      $code_err = 4;
      break;
    }
    break;
  default:
    $code_err = 100;
    break;
}
switch ($code_err) {
  case 1:
    $msg = "Unreadable file ID.";
    break;
  case 2:
    $msg = "No file found.";
    break;
  case 3:
    $msg = "File does not exists.";
    break;
  case 4:
    $msg = "Access forbidden, please log in.";
    break;
  case 100:
    $msg = "Unknown filetype.";
    break;
  default:
    $msg = "Unknown error.";
    break;
}
if ($code_err != -1) {
  header ("content-type: image/png");
  $image = createImagedText($msg, 5);
  imagepng($image);
  imagedestroy($image);
}
?>
