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
//Test:
//http://clementronzon.free.fr/grottocenter/upload/attachments/secure.php?file=u99-a-r9889a7e4db478152e11e2b7b0725a9d5-fr-ok.zip%3Ftest%3Dok%26t%3D5&d=3

$file = (isset($_GET['file'])) ? $_GET['file'] : '';
$type = (isset($_GET['type'])) ? $_GET['type'] : '';
$temp = explode("?", urldecode($file));
$fileName = basename($temp[0]);
$arguments = explode("&", $temp[1]);
$code_err = -1;
if ($file != "") {
	if (file_exists($fileName)) {
		if (USER_IS_CONNECTED) {
			$size = filesize($fileName);
			$name = $fileName;
			//Manage file arguments:
			foreach($arguments as $index => $value) {
				$temp = explode("=", $value);
				$_GET[$temp[0]] = $temp[1];
			}
			// required for IE => dosen't work with IE 6 !!!
			if(ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}
			header('Pragma: public'); //Required
			header('Expires: 0'); //No cache
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: application/force-download');
			header('Content-Disposition: attachment; filename="'.$name.'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.$size);	//Provide file size
			readfile($fileName); //Push it out
		} else {
			$code_err = 3;
		}
	} else {
		$code_err = 2;
	}
} else {
	$code_err = 1;
}

switch ($code_err) {
  case 1:
		$msg = "Please specify the file parameter!";
    break;
  case 2:
    $msg = "File doesn't exists!";
    break;
  case 3:
    $msg = "You must be connected!";
    break;
  default:
    $msg = "Unknown error.";
    break;
}

if ($code_err != -1) {
	header("Content-type: text/plain");
	echo $msg;
}
?>
