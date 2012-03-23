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

$frame = "details";
header("Content-type: text/plain");

$text = (isset($_GET['txt'])) ? $_GET['txt'] : '';
$text = trim($text);
$text = str_replace("*", "%", $text);
$categories = (isset($_GET['cat'])) ? $_GET['cat'] : '';
$separator = (isset($_GET['s'])) ? $_GET['s'] : '|';
$categories = explode($separator, $categories);
$all_categories = array("caver","entry","grotto","cave");
$categories_labels = array("caver" => "<convert>#label=172<convert>",
														"entry" => "<convert>#label=625<convert>",
														"grotto" => "<convert>#label=186<convert>",
														"cave" => "<convert>#label=119<convert>");
$js_var = "new Array(";
$var = "";
for($i=0;$i<count($categories);$i++) {
  $category = $categories[$i];
  if (in_array($category, $all_categories)) {
    if ($category == "caver") {
      $column = "Nickname";
      $where = "WHERE Nickname LIKE '%".$text."%' OR Name LIKE '%".$text."%' OR Surname LIKE '%".$text."%' ";
    } else {
      $column = "Name";
      $where = "WHERE ".$column." LIKE '%".$text."%' ";
    }  
    // requÃªte sql
    $sql = "SELECT Id, '".$category."' AS Category, ".$column." AS Text FROM `".$_SESSION['Application_host']."`.`T_".$category."` ".$where;
    if (USER_IS_CONNECTED) {
  
    } else {
      if ($category == "entry") {
        $sql .= "AND Is_public = 'YES' ";
      }
    }
    $result = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
    while(list($k,$v) = each($result)) {
      if ($v['Text'] != "") {
        //$var .= "{\"Id\": \"".$v['Id']."\", \"Category\": \"".$v['Category']."\", \"Text\": \"".$v['Text']." [".$categories_labels[$category]."]\"},";
				$var .= "{\"Id\": \"".$v['Id']."\", \"Category\": \"".$v['Category']."\", \"Text\": \"".str_replace('"', '\"', $v['Text'])." [".$categories_labels[$category]."]\"},";
      }
    }
  }
}
if ($var != "") {
  $var = substr($var,0,strlen($var)-1);
}
$js_var .= $var.");";
echo $js_var;
?>