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
include("../declaration.php");
include("../application_".$_SESSION['language'].".php");
include("../mailfunctions_".$_SESSION['language'].".php");
include("../../func/firewall.php");
header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.EXPIRATION_DATE);
header('Content-type: application/json; charset=utf-8');

if (!$passed) {
  $mail_header = 'From: contact@grottocenter.org'."\n";
  $mail_header .= 'Reply-To: contact@grottocenter.org'."\n";
  $mail_header .= 'Return-Path: contact@grottocenter.org'."\n";
  $mail_header .= 'Content-Type: text/plain; charset=UTF-8'."\n";
  $mail_header .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body = '  - URL: '.__FILE__."\n".'  - Login: '.$_SESSION['user_login']."\n".'  - Referer: '.$_SERVER['HTTP_REFERER'];
  mail('contact@grottocenter.org','wrong referer',$mail_body,$mail_header);
	die('{"status":"KO", "reason":"firewalled"}');
}

$frame = "filter";
header("Content-type: text/plain");

$category = (isset($_POST['cat'])) ? $_POST['cat'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';

$all_categories = array("caver","entry","grotto");

if (in_array($category,$all_categories) && $id != "") {
  // requÃªte sql
  $sql = "SELECT Latitude, Longitude FROM `".$_SESSION['Application_host']."`.`T_".$category."` WHERE Id = ".$id;
  $result = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  $js_var = '{"status":"OK", "content":{"wgs84":['.$result[0]['Longitude'].','.$result[0]['Latitude'].']}}';
  echo $js_var;
} else {
  echo '{"status":"KO", "reason":"Blacklisted or Empty ID"}';
}
?>