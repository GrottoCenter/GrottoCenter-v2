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
include("../../conf/session.php");
include("../cst_declaration.php");
include("../../func/firewall.php");

if (!$passed) {
  $mail_header = 'From: contact@grottocenter.org'."\n";
  $mail_header .= 'Reply-To: contact@grottocenter.org'."\n";
  $mail_header .= 'Return-Path: contact@grottocenter.org'."\n";
  $mail_header .= 'Content-Type: text/plain; charset=UTF-8'."\n";
  $mail_header .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body = '  - URL: '.__FILE__."\n".'  - Login: '.$_SESSION['user_login']."\n".'  - Referer: '.$_SERVER['HTTP_REFERER'];
  //mail('clem.rz@gmail.com','wrong referer',$mail_body,$mail_header);
  $_xml = '';
  $sql = '';
} else {
  //This script may require additional memory
  ini_set('memory_limit',8388608 * 10);

  include("../../conf/config.php");
  include("../../func/function.php");
  include("../declaration.php");
  include("../application_".$_SESSION['language'].".php");
  include("../mailfunctions_".$_SESSION['language'].".php");
  $frame = "overview";
  header("Content-type: text/xml");

  $sql = (isset($_POST['sql'])) ? $_POST['sql'] : '';
  $sql = stripslashes($sql);
  $category = (isset($_POST['category'])) ? $_POST['category'] : '';
  $idForShownLines = (isset($_POST['idsl'])) ? $_POST['idsl'] : '';
  $categoryForShownLines = (isset($_POST['csl'])) ? $_POST['csl'] : '';
  $do_clusterization = (isset($_POST['clust'])) ? $_POST['clust'] : 'true';
  $advanced_filter = (isset($_POST['advanced'])) ? $_POST['advanced'] : 'false';
  $do_clusterization = ($do_clusterization == "true");
  $advanced_filter = ($advanced_filter == "true");
  $marker_categories = array("caver","entry","grotto");
  $all_categories = array("caver","entry","grotto","line");

  //retrieve the variables from the GET vars
  list($nelat,$nelng) = explode(',',$_POST['ne']);
  list($swlat,$swlng) = explode(',',$_POST['sw']);

  //clean the data
  $nelng = (float)$nelng;
  $swlng = (float)$swlng;
  $nelat = (float)$nelat;
  $swlat = (float)$swlat;

  if ($swlng >= $nelng) {
    $splitFlag = true;
  } else {
    $splitFlag = false;
  }

  $sql = urldecode(stripslashes($sql));

  if (in_array($category, $all_categories)) {
    if ($category == "line") {
      $nodeType = "line";
      $sql .= " WHERE (( ";
      if ($splitFlag) {
        $sql .= " ((sLng BETWEEN ".$swlng." AND 180) OR (sLng BETWEEN -180 AND ".$nelng."))	";
      } else {
        $sql .= " (sLng BETWEEN ".$swlng." AND ".$nelng.")	";
      }
      $sql .= " AND (sLat BETWEEN ".$swlat." AND ".$nelat.")) ";
      $sql .= " OR ( ";
      if ($splitFlag) {
        $sql .= " ((eLng BETWEEN ".$swlng." AND 180) OR (eLng BETWEEN -180 AND ".$nelng."))	";
      } else {
        $sql .= " (eLng BETWEEN ".$swlng." AND ".$nelng.")	";
      }
      $sql .= " AND (eLat BETWEEN ".$swlat." AND ".$nelat.")) ";
      $sql .= " OR (eId = ".$idForShownLines."	AND eCategory = '".$categoryForShownLines."') ";
      $sql .= " OR (sId = ".$idForShownLines."	AND sCategory = '".$categoryForShownLines."')) ";
    } elseif (in_array($category, $marker_categories)) {
      $nodeType = "marker";
      if ($advanced_filter && $_SESSION[$category.'_load_conditions'] != "") {
        switch ($category) {
          case "entry":
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_entry = T_entry.Id ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` ON (J_massif_cave.Id_cave = J_cave_entry.Id_cave OR J_massif_cave.Id_entry = T_entry.Id) ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ON T_massif.Id = J_massif_cave.Id_massif ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_cave` ON T_cave.Id = J_cave_entry.Id_cave ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_type` on T_type.Id = T_entry.Id_type ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_entry.Country ";
            $sql .= "LEFT OUTER JOIN (SELECT T_topography.*, J_topo_cave.Id_cave, J_topo_cave.Id_entry ";
            $sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_cave` ON (T_topography.Id = J_topo_cave.Id_topography AND T_topography.Enabled = 'YES')) T_topography ";
            $sql .= "ON ((T_topography.Id_cave = J_cave_entry.Id_cave OR T_topography.Id_entry = T_entry.Id) ";
            if (USER_IS_CONNECTED) {
            } else {
              $sql .= "AND T_topography.Is_public = 'YES' ";
            }
            $sql .= ") ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_single_entry` ON T_single_entry.Id = T_entry.Id ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`V_entry_avg` ON T_entry.Id = V_entry_avg.Id_entry ";
          break;
          case "grotto":
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_grotto.Country ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_grotto_caver` ON J_grotto_caver.Id_grotto = T_grotto.Id ";
          break;
          case "caver":
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_caver.Country ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_grotto_caver` ON J_grotto_caver.Id_caver = T_caver.Id ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_grotto` ON T_grotto.Id = J_grotto_caver.Id_grotto ";
          break;
          default:
            exit();
          break;
        }
      }
      $sql .= " WHERE ( ";
      if ($splitFlag) {
        $sql .= " ((T_".$category.".Longitude BETWEEN ".$swlng." AND 180) OR (T_".$category.".Longitude BETWEEN -180 AND ".$nelng.")) ";
      } else {
        $sql .= " (T_".$category.".Longitude BETWEEN ".$swlng." AND ".$nelng.")	";
      }
      $sql .= " AND (T_".$category.".Latitude BETWEEN ".$swlat." AND ".$nelat.")) ";
      if ($advanced_filter && $_SESSION[$category.'_load_conditions'] != "") {
        $sql .= " AND ".$_SESSION[$category.'_load_conditions']." ";
      }
      if (USER_IS_CONNECTED) {
      } else {
        if ($category == "entry") {
          $sql .= " AND T_entry.Is_public = 'YES' ";
        }
      }
      $sql .= " GROUP BY T_".$category.".Id ";
    }

    $sql = str_replace("<plus>","+",$sql);
    $result = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
    $num = $result["Count"];

    if (in_array($category, $marker_categories)) {

      if ($category == "caver") {
//CRO 2011-10-12
      	$connectedArray = array();
      	$referentArray = array();
     	//$connectedArray = readSessionsVar("user_id");
      	//$referentArray = getReferentCavers(LEADER_GROUP_ID);
      }

      //limit by default to 30 markers
      if ($do_clusterization) {
        $limit = (isset($_POST['limit'])) ? $_POST['limit'] : 30;
        $gridSize = 0;
        $listRemove = array();
        while($num>$limit) {

        	//grid size in pixels. if the first pass fails to reduce the
        	//number of markers below the limit, the grid will increase
        	//again and redo the loop.
        	$gridSize += ($nelng-$swlng)/30;
        	$clustered = array();
        	reset($result);

        	//loop through the $result and put each one in a grid square
        	while(list($k,$v) = each($result)) {
            if ($v["Latitude"] != "" && $v["Longitude"] != "") {
            //f ($k != "Count") {
          		//calculate the y position based on the latitude: $v[0]
          		$y = floor(($v["Latitude"]-$swlat)/$gridSize);

          		//calculate the x position based on the longitude: $v[1]
          		$x = floor(($v["Longitude"]-$swlng)/$gridSize);

          		//use the x and y values as the key for the array and append
          		//the points key to the clustered array
          		$clustered["{$x},{$y}"][] = $k;
          	}
        	}

        	//check if we're below the limit and if not loop again
        	if(count($clustered)>$limit) continue;

        	//reformat the list array
        	$listRemove = array();
        	while(list($k,$v) = each($clustered)) {

        		//only merge if there is more than one marker in acell
        		if(count($v)>1) {

        			//create a list of the merged markers
        			$listRemove = array_merge($listRemove,$v);

        			//add a cluster marker to the list
        			$clusterLat = $result[$v[0]]["Latitude"];
        			$clusterLng = $result[$v[0]]["Longitude"];

              if ($category == "caver") {
                $title_key = 'Nickname';
              } else {
                $title_key = 'Name';
              }

        			$clusterName = "[".count($v)."] ";
        			$clusterId = "";
              while(list($key,$value) = each($v)) {
        			 $clusterName .= $result[$value][$title_key].", ";
        			 $clusterId .= $result[$value]['Id'].",";
              }

              $clusterName = substr($clusterName,0,strlen($clusterName)-2);
              $clusterId = substr($clusterId,0,strlen($clusterId)-1);

              //Request-URI Too Large :
              //Rework the cluster Id and the cluster Name in order to prevent too large URIs
              $limit = 256;
              if (strlen($clusterId) > $limit) {
                $temp_clusterId = substr($clusterId, 0, $limit);
                $clusterId = (strrpos($temp_clusterId, ',') === false) ? $clusterId : substr($temp_clusterId, 0, strrpos($temp_clusterId, ','));
              }
							$limit = 200;
              if (strlen($clusterName) > $limit) {
                $temp_clusterName = substr($clusterName, 0, $limit);
                $clusterName = (strrpos($temp_clusterName, ',') === false) ? $clusterName : substr($temp_clusterName, 0, strrpos($temp_clusterName, ','))."...";
              }

              //use 'c' to indicate this is a (c)luster marker
        			$result[] = array('Latitude' => $clusterLat,
                                'Longitude' => $clusterLng,
                                'Marker_type' => 'c',
                                $title_key => $clusterName,
                                'Id' => $clusterId);
        		}
        	}

        	//unset all the merged pins
        	//reverse to start with highest key
        	rsort($listRemove);
        	while(list($k,$v) = each($listRemove)) {
        		unset($result[$v]);
        	}

        	//we're done!
        	break;
        }
      }
    }

    unset($result["Count"]);
    reset($result);

    $_xml = "<".$nodeType."s>\n";
    while(list($k,$v) = each($result)) {
      $_xml .= "<".$nodeType." ";
      while(list($key,$value) = each($v)) {
        if ($category == "caver") {
          $public = $v['Contact_is_public'];
          $display_contact = ((USER_IS_CONNECTED && $public==1) || $public==2);
          if (!$display_contact && ($key == "Address" || $key == "City" || $key == "Region")) {
            $value = "";
          }
        }
        if ($key != "Password" && $key != "Custom_message") {
          //$_xml .= $key."=\"".$value."\" ";
					$_xml .= $key."=\"".htmlspecialchars($value, ENT_COMPAT, 'UTF-8')."\" ";
        }
      }
      if ($category == "caver") {
        $_xml .= "Is_connected=\"";
        if (in_array($v['Id'], $connectedArray)) {
          $_xml .= "true";
        } else {
          $_xml .= "false";
        }
        $_xml .= "\" ";
        $_xml .= "Is_referent=\"";
        if (in_array($v['Id'], $referentArray)) {
          $_xml .= "true";
        } else {
          $_xml .= "false";
        }
        $_xml .= "\" ";
      }
      $_xml .= "/>\n";
    }
    $_xml .= "</".$nodeType."s>";
  } else {
    $_xml = '';
    $sql = '';
  }
}
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">".$_xml;
?>