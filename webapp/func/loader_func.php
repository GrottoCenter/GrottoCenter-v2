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
function getAvgAestheticism($id_entry)
{
  if ($id_entry != "") {
    $sql = "SELECT AVG(Aestheticism) AS Avg FROM `".$_SESSION['Application_host']."`.`T_comment` ";
    $sql .= "WHERE Id_entry=".$id_entry." AND Aestheticism IS NOT NULL ";
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]["Avg"];
  } else {
    return false;
  }
}

function getAvgCaving($id_entry)
{
  if ($id_entry != "") {
    $sql = "SELECT AVG(Caving) AS Avg FROM `".$_SESSION['Application_host']."`.`T_comment` ";
    $sql .= "WHERE Id_entry=".$id_entry." AND Caving IS NOT NULL ";
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]["Avg"];
  } else {
    return false;
  }
}

function getAvgApproach($id_entry)
{
  if ($id_entry != "") {
    $sql = "SELECT AVG(Approach) AS Avg FROM `".$_SESSION['Application_host']."`.`T_comment` ";
    $sql .= "WHERE Id_entry=".$id_entry." AND Approach IS NOT NULL ";
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]["Avg"];
  } else {
    return false;
  }
}
?>
