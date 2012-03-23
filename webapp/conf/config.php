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
ob_start("ob_gzhandler");

include("session.php");

/*header("Cache-Control: no-cache, must-revalidate");
$offset = 6 * 60 * 60;
$ExpDate = gmdate("D, d M Y H:i:s", time() + $offset);
header("Expires: ".$ExpDate." GMT");*/

function logDBToFile($content, $filename)
{
  return "";
  $path = substr(__FILE__, 0, strlen(__FILE__)-15)."log/";
  $handle = fopen($path.$filename.".".date("Y-m-d", time()).".log", "a+b");
  $date = date("r", time());
  fwrite($handle, $date.$content."\n");
  fclose($handle);
}

//on devrais tester aussi l'utilisation de $_SERVER['DOCUMENT_ROOT']
function connect()
{
  //global $host_db,$user_db,$password_db,$bdd_db;
  $host_db = ""; // nom de votre serveur
  $user_db = ""; // nom d'utilisateur de connexion à votre bdd
  $password_db = ""; // mot de passe de connexion à votre bdd
  $bdd_db = ""; // nom de votre bdd
  $connect_db = mysql_connect($host_db,$user_db,$password_db);
$filename = "mysql";
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$IP = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
} elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
	$IP = $_SERVER['HTTP_CLIENT_IP'] ;
} else {
	$IP = $_SERVER['REMOTE_ADDR'] ;
}
$content = "|".$IP."|".session_id()."|".__FUNCTION__."|".$connect_db;
logDBToFile($content, $filename);
  if (is_resource($connect_db)) {
    mysql_select_db($bdd_db,$connect_db);
    mysql_query("SET NAMES UTF8");
    return $connect_db;
  } else {
    reportError(mysql_error(),__FILE__, "function", __FUNCTION__, 'Connection Error');
    exit();
  }
}

function close($connect_db)
{
  /*@mysql_close($connect_db);
$filename = "mysql";
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$IP = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
} elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
	$IP = $_SERVER['HTTP_CLIENT_IP'] ;
} else {
	$IP = $_SERVER['REMOTE_ADDR'] ;
}
$content = "|".$IP."|".session_id()."|".__FUNCTION__."|".$connect_db;
logDBToFile($content, $filename);*/
}
?>
