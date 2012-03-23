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
include("../declaration.php");
include("../application_".$_SESSION['language'].".php");
include("../mailfunctions_".$_SESSION['language'].".php");

$frame = "filter";
header("Content-type: text/plain");

$category = (isset($_GET['cat'])) ? $_GET['cat'] : '';

$all_categories = array("caver","entry","grotto","cave","massif","url","group","right","caver_nick","request","file","author");

if (in_array($category,$all_categories)) {
  switch($category) {
    case "caver":
      $column = "Login";
      break;
    case "caver_nick":
      $column = "Nickname";
      $category = "caver";
      break;
    default:
      $column = "Name";
      break;
  }
  $sql = "SELECT ".$column." AS Name FROM `".$_SESSION['Application_host']."`.`T_".$category."` ORDER BY ".$column;
  $result = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  $num = $result["Count"];
  
  $js_var = "new Array(";
  
  if ($num > 0) {
    while(list($k,$v) = each($result)) {
      if ($v['Name'] != "") {
        //$js_var .= "\"".$v['Name']."\", ";
				$js_var .= "\"".str_replace('"', '\"', $v['Name'])."\", ";
      }
    }
    $js_var = substr($js_var,0,strlen($js_var)-2);
  }
  
  $js_var .= ");";
  echo $js_var;
} else {
  echo "undefined;";
}
?>