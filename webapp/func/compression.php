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
//Check if that page is compressed: http://www.whatsmyip.org/http_compression/
//Free.fr does not provide GZip compression.
$comment = '/*';
if(!ob_start("ob_gzhandler")) {
	ob_start();
	$comment .= 'COULD NOT COMPRESS ';
} else {
	$comment .= 'Compressed ';
}
//MOVED:
$file = (isset($_GET['file'])) ? $_GET['file'] : '';
$pathParts = pathinfo($file);
$ext = $pathParts['extension'];

if (strpos($file, '/') !== false) {
	$real_path = realpath(__FILE__);
	$real_path = substr($real_path,0,strlen($real_path)-20);
	$file = $real_path.'scripts/'.$file;
}
$comment .= "file for transfert : ".$file." on ".gmdate("D, d M Y H:i:s", time())." GMT*/\n";
header("Cache-Control: no-cache, must-revalidate");
$offset = 6 * 60 * 60;
$ExpDate = gmdate("D, d M Y H:i:s", time() + $offset);
header("Expires: ".$ExpDate." GMT");

/*$file = (isset($_GET['file'])) ? $_GET['file'] : '';
$pathParts = pathinfo($file);
$ext = $pathParts['extension'];*/

function stripCSS($buffer)
{
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer); // remove tabs, spaces, newlines, etc.
	$buffer = preg_replace("! {2,}!", '',$buffer); // remove multispaces
	$buffer = str_replace('{ ', '{', $buffer); // remove unnecessary spaces.
	$buffer = str_replace(' }', '}', $buffer);
	$buffer = str_replace('; ', ';', $buffer);
	$buffer = str_replace(', ', ',', $buffer);
	$buffer = str_replace(' {', '{', $buffer);
	$buffer = str_replace('} ', '}', $buffer);
	$buffer = str_replace(': ', ':', $buffer);
	$buffer = str_replace(' ,', ',', $buffer);
	$buffer = str_replace(' ;', ';', $buffer);
	return $buffer;
}

function stripJS($buffer)
{
	//$buffer = preg_replace('#/\*.*?\*/#s', '', $buffer); // remove PHP comments (/*tralala*/)
	//$buffer = preg_replace('#//.*$#m', '', $buffer); // remove C comments (// tralala)	
	//$buffer = str_replace(array("\r\n", "\r", "\n"), ' ', $buffer); // change new lines to single spaces
	$buffer = str_replace(array("\t"), ' ', $buffer); // change tabs to single spaces
	$buffer = preg_replace("! {2,}!s", ' ',$buffer); // multispaces to single...
	return $buffer;
}

if (!($buffer=@file_get_contents($file))) {
	header("HTTP/1.0 404 Not Found");
	exit();
}

switch ($ext) {
  case "css":
    header("Content-type: text/css; charset=utf-8");
		$buffer = stripCSS($buffer);
    break;
  case "js":
    header("Content-type: text/javascript; charset=utf-8");
    $buffer = stripJS($buffer);
		break;
  default:
    header("Content-type: text/html; charset=utf-8");
    echo "/*Could not get content of file : ".$file." on ".gmdate("D, d M Y H:i:s", time())." GMT*/";
    exit();
}
echo $comment;
echo $buffer; //file_get_contents($file);
?>