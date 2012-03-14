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

header("Content-type: text/plain");

$param_code = (isset($_GET['p'])) ? $_GET['p'] : '';

switch ($param_code) {
  case "connected":
    if (USER_IS_CONNECTED){
      $js_var = "true;";
    } else {
      $js_var = "false;";
    }
  break;
  case "hover":
    if($_SESSION['user_hover'] == 'YES') {
      $js_var = "true;";
    } else {
      $js_var = "false;";
    }
  break;
  case "limit":
    if ($_SESSION['user_detail_level'] != "") {
      $js_var = $_SESSION['user_detail_level'].";";
    } else {
      $js_var = "20;";
    }
  break;
  default:
    $js_var = "undefined;";
  break;
}
echo $js_var;
?>