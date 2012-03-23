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
function getNews($is_admin)
{
  $sql = "SELECT n.*, ";
  $sql .= "CONCAT(if(caa.Surname is null,'',CONCAT(caa.Surname, ' ')), if(caa.Name is null,'',CONCAT(caa.Name , ' alias ')), caa.Nickname) AS Author, ";
  $sql .= "CONCAT(if(cab.Surname is null,'',CONCAT(cab.Surname, ' ')), if(cab.Name is null,'',CONCAT(cab.Name , ' alias ')), cab.Nickname) AS Reviewer ";
  $sql .= "FROM `".$_SESSION['Application_host']."`.`T_news` n ";
  $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` caa ON n.Id_author = caa.Id ";
  $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` cab ON n.Id_reviewer = cab.Id ";
  if (!$is_admin) {
    $sql .= "WHERE n.Is_public = 'YES' ";
  }
  $sql .= "ORDER BY n.Id_answered, n.Date_inscription ";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  return sortByThread($data);
}
?>