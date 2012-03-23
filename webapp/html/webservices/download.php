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
$fileName = (isset($_POST['file'])) ? $_POST['file'] : '';
$name = (isset($_POST['name'])) ? $_POST['name'] : '';
$ext = (isset($_POST['ext'])) ? $_POST['ext'] : '';
$fileWhiteList = array("getMarkersGPX.php", "getMarkersKML.php");

//$temp = explode("?", $file);
//$fileName = $temp[0];
//$arguments = explode("&", $temp[1]);
$name = ($name=="") ? "export" : $name;
$name = $name.".".$ext;
if (($fileName != "") && (file_exists("./".$fileName)) && in_array($fileName, $fileWhiteList)) {
  /*foreach($arguments as $index => $value) {
    $temp = explode("=", $value);
    $_POST[$temp[0]] = $temp[1];
  }*/ //Now $_POST
	if (isset($_POST['ff']) && $_POST['ff'] == 'g') {
		include("./".$fileName);
	} else {
		//$size = filesize("./".$fileName);
		header("Content-Type: application/force-download; name=\"".$name."\"");
		header("Content-Transfer-Encoding: binary");
		//header("Content-Length: ".$size);
		header("Content-Disposition: attachment; filename=\"".$name."\"");
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		ob_start();
		include("./".$fileName);
		ob_end_flush();
	}
} else {
  echo "Error: file not found: ".$fileName;
}
exit();
?>