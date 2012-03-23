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
function getEntryName($id)
{
  if ($id != "") {
    $sql = "SELECT Name FROM `".$_SESSION['Application_host']."`.`T_entry` ";
    $sql .= "WHERE Id=".$id." ORDER BY Id ";
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]['Name'];
  } else {
    return "";
  }
}

function getMapParams($id)
{
  if ($id != "") {
    $sql = "SELECT Latitude, Longitude FROM `".$_SESSION['Application_host']."`.`T_entry` ";
    $sql .= "WHERE Id=".$id." ORDER BY Id ";
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]['Latitude'].",".$array[0]['Longitude'];
  } else {
    return "";
  }
}

function getEntryId($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT Id_entry FROM `".$_SESSION['Application_host']."`.`J_entry_".$category."` ";
    $sql .= "WHERE Id_".$category." = ".$id." ORDER BY Id_".$category." ";
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]['Id_entry'];
  } else {
    return "";
  }
}

function getLocation($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT l.*, ";
//    $sql .= "CONCAT(if(caa.Surname is null,'',CONCAT(caa.Surname, ' ')), if(caa.Name is null,'',CONCAT(caa.Name , ' alias ')), caa.Nickname) AS Author, ";
//    $sql .= "CONCAT(if(cab.Surname is null,'',CONCAT(cab.Surname, ' ')), if(cab.Name is null,'',CONCAT(cab.Name , ' alias ')), cab.Nickname) AS Reviewer ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "cab.Nickname AS Reviewer ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_location` l ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON l.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON l.Id_reviewer = cab.Id ";
    $sql .= "WHERE l.Id_entry=".$id." ";
    $sql .= "ORDER BY l.Id ";
    return getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  } else {
    return "";
  }
}

function getTopography($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT t.Id_request, f.*, ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "GROUP_CONCAT(DISTINCT a.Name ORDER BY a.Name SEPARATOR ', ') AS Authors ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_file` f ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON f.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_file` tf ON tf.Id_file = f.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_topography` t ON t.Id = tf.Id_topography ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_cave` tc ON tc.Id_topography = t.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ce.Id_cave = tc.Id_cave ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_author` ta ON ta.Id_topography = t.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_author` a ON a.Id = ta.Id_author ";
    $sql .= "WHERE (tc.Id_entry=".$id." OR ce.Id_entry=".$id.") ";
    if (!USER_IS_CONNECTED) {
      $sql .= "AND t.Is_public = 'YES' ";
    }
    $sql .= "AND t.Enabled = 'YES' ";
    $sql .= "GROUP BY f.Id ";
    $sql .= "ORDER BY t.Id_request ";
    return getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  } else {
    return "";
  }
}

function getBibliography($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT b.*, ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "cab.Nickname AS Reviewer ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_bibliography` b ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON b.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON b.Id_reviewer = cab.Id ";
    $sql .= "WHERE b.Id_entry=".$id." ";
    $sql .= "ORDER BY b.Id ";
    return getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  } else {
    return "";
  }
}

function getHistory($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT h.*, ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "cab.Nickname AS Reviewer ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_history` h ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON h.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON h.Id_reviewer = cab.Id ";
    $sql .= "WHERE h.Id_entry=".$id." ";
    $sql .= "ORDER BY h.Id ";
    return getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  } else {
    return "";
  }
}

function getDescription($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT d.*, e.Name as Exit_name, ";
//    $sql .= "CONCAT(if(caa.Surname is null,'',CONCAT(caa.Surname, ' ')), if(caa.Name is null,'',CONCAT(caa.Name , ' alias ')), caa.Nickname) AS Author, ";
//    $sql .= "CONCAT(if(cab.Surname is null,'',CONCAT(cab.Surname, ' ')), if(cab.Name is null,'',CONCAT(cab.Name , ' alias ')), cab.Nickname) AS Reviewer ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "cab.Nickname AS Reviewer ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_description` d ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_entry_description` ed ON d.Id=ed.Id_description ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON d.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON d.Id_reviewer = cab.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_entry` e ON d.Id_exit = e.Id ";
    $sql .= "WHERE ed.Id_entry = ".$id." OR d.Id_exit = ".$id." ";
    $sql .= "ORDER BY d.Id ";
    return getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  } else {
    return "";
  }
}

function getRigging($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT r.*, e.Name as Exit_name, ";
//    $sql .= "CONCAT(if(caa.Surname is null,'',CONCAT(caa.Surname, ' ')), if(caa.Name is null,'',CONCAT(caa.Name , ' alias ')), caa.Nickname) AS Author, ";
//    $sql .= "CONCAT(if(cab.Surname is null,'',CONCAT(cab.Surname, ' ')), if(cab.Name is null,'',CONCAT(cab.Name , ' alias ')), cab.Nickname) AS Reviewer ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "cab.Nickname AS Reviewer ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_rigging` r ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON r.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON r.Id_reviewer = cab.Id ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_entry_rigging` er ON r.Id=er.Id_rigging ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_entry` e ON r.Id_exit = e.Id ";
    $sql .= "WHERE er.Id_entry = ".$id." OR r.Id_exit = ".$id." ";
    $sql .= "ORDER BY r.Id ";
    return getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  } else {
    return "";
  }
}

function getComment($category, $id)
{
  if ($id != "" && $category != "") {
    $sql = "SELECT c.*, e.Name as Exit_name, ";
//    $sql .= "CONCAT(if(caa.Surname is null,'',CONCAT(caa.Surname, ' ')), if(caa.Name is null,'',CONCAT(caa.Name , ' alias ')), caa.Nickname) AS Author, ";
//    $sql .= "CONCAT(if(cab.Surname is null,'',CONCAT(cab.Surname, ' ')), if(cab.Name is null,'',CONCAT(cab.Name , ' alias ')), cab.Nickname) AS Reviewer ";
    $sql .= "caa.Nickname AS Author, ";
    $sql .= "cab.Nickname AS Reviewer ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_comment` c ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON c.Id_author = caa.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON c.Id_reviewer = cab.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_entry` e ON c.Id_exit = e.Id ";
    $sql .= "WHERE c.Id_entry=".$id." OR c.Id_exit=".$id." ";
    $sql .= "ORDER BY c.Id_answered, c.Date_inscription ";
    $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return sortByThread($data);
  } else {
    return "";
  }
}

function getSqlExits($id, $category, $selected_id="")
{
	//OLD FASHIONED WAY:
	/*
  $sql_exits = "SELECT ey.Id AS value, ey.Name AS text ";
  $sql_exits .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
  $sql_exits .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ey.Id = ce.Id_entry ";
  $sql_exits .= "WHERE ce.Id_cave IN (SELECT Id_cave FROM `".$_SESSION['Application_host']."`.`J_cave_entry` ce2 WHERE ce2.Id_entry = ".$id.") ";
  
  $sql_exits .= "AND NOT ey.Id = ".$id." ";
  
  $sql_exits .= "AND NOT ey.Id IN ( ";
  $sql_exits .= " SELECT d.Id_exit ";
  $sql_exits .= " FROM `".$_SESSION['Application_host']."`.`T_".$category."` d ";
  $sql_exits .= " LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_entry_".$category."` ed ON d.Id = ed.Id_".$category." ";
  $sql_exits .= " WHERE ed.Id_entry = ".$id." ";
  $sql_exits .= " AND d.Id_exit IS NOT NULL ";
  if ($selected_id != "") {
    $sql_exits .= " AND NOT d.Id_exit = ".$selected_id." ";
  }
  $sql_exits .= ") ";
  
  $sql_exits .= "AND NOT ey.Id IN ( ";
  $sql_exits .= " SELECT Id_entry ";
  $sql_exits .= " FROM `".$_SESSION['Application_host']."`.`J_entry_".$category."` ";
  if ($selected_id != "") {
    $sql_exits .= "WHERE NOT Id_entry = ".$selected_id." ";
  }
  $sql_exits .= ") ";
  
  $sql_exits .= "ORDER BY text";
  */
	//NEW ONE:
	
	$sql_exits = "SELECT e.Id AS value, e.Name AS text ";
	$sql_exits .= "FROM `".$_SESSION['Application_host']."`.`J_cave_entry` ce ";
	$sql_exits .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_entry` e ON e.Id = ce.Id_entry ";
	$sql_exits .= "WHERE ce.Id_cave IN (SELECT Id_cave FROM `".$_SESSION['Application_host']."`.`J_cave_entry` WHERE Id_entry = ".$id.") ";
	$sql_exits .= "AND NOT ce.Id_entry = ".$id." ";
	$sql_exits .= "ORDER BY text ";
  
  return $sql_exits;
}
?>