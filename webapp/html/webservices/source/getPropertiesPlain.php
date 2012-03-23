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
$labelsSinceDate = array("<convert>#label=8<convert>","<convert>#label=9<convert>","<convert>#label=10<convert>","<convert>#label=11<convert>","<convert>#label=12<convert>","<convert>#label=13<convert>","<convert>#label=14<convert>");
include("../../func/loader_func.php");  	
include("../properties_".$_SESSION['language'].".php");

$frame = "details";
header("Content-type: text/plain");

//Capture the action type :
$type = (isset($_GET['type'])) ? $_GET['type'] : '';

switch ($type) {
  case "details":
    $id = (isset($_GET['id'])) ? $_GET['id'] : '';
    $category = (isset($_GET['category'])) ? $_GET['category'] : '';
    $labelsBlank = array("","","","","","","");
    $isConnected = USER_IS_CONNECTED;
    $geodesic = (isset($_GET['geodesic'])) ? $_GET['geodesic'] : '';
    $length = (isset($_GET['length'])) ? $_GET['length'] : '';
    $temperature = (isset($_GET['temperature'])) ? $_GET['temperature'] : '';
    $systemArray = array("geodesic" => urldecode(stripslashes($geodesic)),"length" => urldecode(stripslashes($length)),"temperature" => urldecode(stripslashes($temperature)));
    $innerHTML = getProperties($category,$id,$isConnected,$labelsBlank,$labelsSinceDate,true,$systemArray,false,__FILE__);
	break;
  case "list_caver":
    $id = (isset($_GET['id'])) ? $_GET['id'] : '';
    $category = (isset($_GET['category'])) ? $_GET['category'] : '';
    
//    $sql = "SELECT ca.Id AS objectId, 'caver' AS category, Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, CONCAT(if(ca.Name is null AND ca.Surname is null,'',CONCAT(if(ca.Name is null,'',CONCAT(ca.Name, ' ')),if(ca.Surname is null,'',CONCAT(ca.Surname , ' ')),'<convert>#label=34<convert> ')), ca.Nickname) As NName ";
    $sql = "SELECT ca.Id AS objectId, 'caver' AS category, Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, ca.Nickname As NName ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ca ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` co ON ca.Country = co.Iso ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_".$category."_caver` ec ON ca.Id = ec.Id_caver ";
    $sql .= "WHERE ec.Id_".$category." = ".$id." ";
    $sql .= "ORDER BY country, NName ";
    $comparedCol = "value";
    $countryCol = "country";
    $idCol = "objectId";
    $categoryCol = "category";
    $textCol = "NName";
    $selected = "";
    
    $Obj_name_sql = "SELECT * ";
    $Obj_name_sql .= "FROM `".$_SESSION['Application_host']."`.`T_".$category."` ";
    $Obj_name_sql .= "WHERE Id = ".$id;
    $object_source_name = getDataFromSQL($Obj_name_sql, __FILE__, $frame, __FUNCTION__);
    $object_source_name = $object_source_name[0]['Name'];
    
    $rnd_div_id = "list_caver_div_".rand();
    $innerHTML = "<div id=\"".$rnd_div_id."\" class=\"menu\">";
    $innerHTML .= getTopMenu(getCloseBtn("JavaScript:resetListDiv('".$rnd_div_id."');","<convert>#label=371<convert>")."<div class=\"frame_title\">".setTitle("JavaScript:openMe(".$id.", '".$category."', false);", "list", "<convert>#label=385<convert> <convert>#label=540<convert> ".$object_source_name, 1)."</div>");//Fermer
    $innerHTML .= "<select onclick=\"JavaScript:selectOnClick(this);\" size=\"5\" class=\"caver\">\n";
    $innerHTML .= groupOptions(getOptions($sql, "", $selected, $comparedCol, $countryCol, $idCol, $categoryCol, $textCol),$countryCol)."\n";
    $innerHTML .= "</select>".getBotMenu()."</div>\n";
	break;
  case "list_grotto":
    $id = (isset($_GET['id'])) ? $_GET['id'] : '';
    $category = (isset($_GET['category'])) ? $_GET['category'] : '';
    
    $sql = "SELECT ca.Id AS objectId, 'grotto' AS category, Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, Name As NName ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_grotto` ca ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` co ON ca.Country = co.Iso ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_grotto_".$category."` ec ON ca.Id = ec.Id_grotto ";
    $sql .= "WHERE ec.Id_".$category." = ".$id." ";
    $sql .= "ORDER BY country, NName ";
    $comparedCol = "value";
    $countryCol = "country";
    $idCol = "objectId";
    $categoryCol = "category";
    $textCol = "NName";
    $selected = "";
    
    $Obj_name_sql = "SELECT * ";
    $Obj_name_sql .= "FROM `".$_SESSION['Application_host']."`.`T_".$category."` ";
    $Obj_name_sql .= "WHERE Id = ".$id;
    $object_source_name = getDataFromSQL($Obj_name_sql, __FILE__, $frame, __FUNCTION__);
    if ($category == "caver") {
      $object_source_name = $object_source_name[0]['Nickname'];
    } else {
      $object_source_name = $object_source_name[0]['Name'];
    }
    
    $rnd_div_id = "list_grotto_div_".rand();
    $innerHTML = "<div id=\"".$rnd_div_id."\" class=\"menu\">";
    $innerHTML .= getTopMenu(getCloseBtn("JavaScript:resetListDiv('".$rnd_div_id."');","<convert>#label=371<convert>")."<div class=\"frame_title\">".setTitle("JavaScript:openMe(".$id.", '".$category."', false);", "list", "<convert>#label=386<convert> <convert>#label=540<convert> ".$object_source_name, 1)."</div>");//Fermer//Clubs en lien avec
    $innerHTML .= "<select onclick=\"JavaScript:selectOnClick(this);\" size=\"5\" class=\"grotto\">\n";
    $innerHTML .= groupOptions(getOptions($sql, "", $selected, $comparedCol, $countryCol, $idCol, $categoryCol, $textCol),$countryCol)."\n";
    $innerHTML .= "</select>".getBotMenu()."</div>\n";
	break;
  case "list_entry":
    $id = (isset($_GET['id'])) ? $_GET['id'] : '';
    $category = (isset($_GET['category'])) ? $_GET['category'] : '';
    if ($category == "grotto") {
      $j_table_name = "J_grotto_entry";
    } else {
      $j_table_name = "J_entry_caver";
    }
    
    
    $sql = "SELECT ca.Id AS objectId, 'entry' AS category, Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, Name As NName ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ca ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` co ON ca.Country = co.Iso ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`".$j_table_name."` ec ON ca.Id = ec.Id_entry ";
    $sql .= "WHERE ec.Id_".$category." = ".$id." ";
    $sql .= "ORDER BY country, NName ";
    $comparedCol = "value";
    $countryCol = "country";
    $idCol = "objectId";
    $categoryCol = "category";
    $textCol = "NName";
    $selected = "";
    
    $Obj_name_sql = "SELECT * ";
    $Obj_name_sql .= "FROM `".$_SESSION['Application_host']."`.`T_".$category."` ";
    $Obj_name_sql .= "WHERE Id = ".$id;
    $object_source_name = getDataFromSQL($Obj_name_sql, __FILE__, $frame, __FUNCTION__);
    if ($category == "caver") {
      $object_source_name = $object_source_name[0]['Nickname'];
    } else {
      $object_source_name = $object_source_name[0]['Name'];
    }
    
    $rnd_div_id = "list_entry_div_".rand();
    $innerHTML = "<div id=\"".$rnd_div_id."\" class=\"menu\">";
    $innerHTML .= getTopMenu(getCloseBtn("JavaScript:resetListDiv('".$rnd_div_id."');","<convert>#label=371<convert>")."<div class=\"frame_title\">".setTitle("JavaScript:openMe(".$id.", '".$category."', false);", "list", "<convert>#label=384<convert> <convert>#label=540<convert> ".$object_source_name, 1)."</div>");//EntrÃ©es en lien avec//Fermer
    $innerHTML .= "<select onclick=\"JavaScript:selectOnClick(this);\" size=\"5\" class=\"entry\">\n";
    $innerHTML .= groupOptions(getOptions($sql, "", $selected, $comparedCol, $countryCol, $idCol, $categoryCol, $textCol),$countryCol)."\n";
    $innerHTML .= "</select>".getBotMenu()."</div>\n";
	break;
  case "list_contribution":
    $id = (isset($_GET['id'])) ? $_GET['id'] : '';
    $category = (isset($_GET['category'])) ? $_GET['category'] : '';
		$sql = "SELECT DISTINCT IF(cat.Category = 'T_entry',cat.Id,cat.Id_entry) AS objectId, 'entry' AS category, IF(cat.Category = 'T_entry',cat.Id,cat.Id_entry) AS value, cat.Title AS NName ";
		$sql .= "FROM `".$_SESSION['Application_host']."`.`V_contributions` cat ";
		$sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_caver` usr ON (usr.Id = cat.Id_author OR usr.Id = cat.Id_reviewer) ";
		$sql .= "WHERE usr.Id = ".$id." AND NOT cat.Id_entry = 0 AND cat.Id_entry IS NOT NULL ";
		if (USER_IS_CONNECTED){
		} else {
			$sql .= "AND cat.Is_public = 'YES' ";
		}
		$sql .= "ORDER BY NName ";
    $comparedCol = "value";
    $idCol = "objectId";
    $categoryCol = "category";
    $textCol = "NName";
    $selected = "";
    
    $Obj_name_sql = "SELECT * ";
    $Obj_name_sql .= "FROM `".$_SESSION['Application_host']."`.`T_".$category."` ";
    $Obj_name_sql .= "WHERE Id = ".$id;
    $object_source_name = getDataFromSQL($Obj_name_sql, __FILE__, $frame, __FUNCTION__);
    if ($category == "caver") {
      $object_source_name = $object_source_name[0]['Nickname'];
    } else {
      $object_source_name = $object_source_name[0]['Name'];
    }
    
    $rnd_div_id = "list_contribution_div_".rand();
    $innerHTML = "<div id=\"".$rnd_div_id."\" class=\"menu\">";
    $innerHTML .= getTopMenu(getCloseBtn("JavaScript:resetListDiv('".$rnd_div_id."');","<convert>#label=371<convert>")."<div class=\"frame_title\">".setTitle("JavaScript:openMe(".$id.", '".$category."', false);", "list", "<convert>#label=887<convert> <convert>#label=540<convert> ".$object_source_name, 1)."</div>");//Contributions en lien avec//Fermer
    $innerHTML .= "<select onclick=\"JavaScript:selectOnClick(this);\" size=\"5\" class=\"entry\">\n";
    $innerHTML .= getOptions($sql, "", $selected, $comparedCol, $countryCol, $idCol, $categoryCol, $textCol)."\n";
    $innerHTML .= "</select>".getBotMenu()."</div>\n";
	break;
	default:
  break;
}
echo $innerHTML;
?>