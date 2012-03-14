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

$frame = "filter";

header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.EXPIRATION_DATE);
header('Content-type: application/json; charset=utf-8');

$iso = (isset($_GET['iso'])) ? urldecode(stripslashes($_GET['iso'])) : Select_default;
$iso = (isset($_POST['iso'])) ? urldecode(stripslashes($_POST['iso'])) : $iso;

$crs_language = (isset($_GET['lng'])) ? ucfirst(urldecode(stripslashes($_GET['lng']))) : ucfirst($_SESSION['language']);
$crs_language = (isset($_POST['lng'])) ? ucfirst(urldecode(stripslashes($_POST['lng']))) : $crs_language;

$supported_languages = array('Fr', 'En', 'Es', 'De');
$crs_language = in_array($crs_language, $supported_languages) ? $crs_language : 'En';

$sql = "SELECT DISTINCT IFNULL(co.".$crs_language."_name, '*World') AS country, crs.Code AS code, crs.Definition AS def FROM `".$_SESSION['Application_host']."`.`T_crs` crs ";
$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_country_crs` cc ON cc.Id_crs = crs.Id ";
$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` co ON co.Iso = cc.Iso ";
$sql .= "WHERE crs.Code = 'WGS84' OR (crs.Enabled = 'YES' ";
if ($iso == Select_default) {
	$sql .= ") ";
} else {
	$sql .= "AND ((cc.Iso IS NULL) OR cc.Iso = ".returnDefault($iso, 'text').")) ";
}
$sql .= "ORDER BY co.".$crs_language."_name";
$result = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
$num = $result["Count"];

$js_var = "{"."\n";
$country = '';
$started = false;
$cstart = false;
if ($num > 0) {
	while(list($k,$crs) = each($result)) {
		if ($crs['code'] != '' && $crs['def'] != '') {
			if ($country != $crs['country']) {
				$country = $crs['country'];
				if ($started) $js_var .= "},"."\n";
				$js_var .= "  \"".$crs['country']."\": {";
				$cstart = true;
			} else {
				$cstart = false;
			}
			$started = true;
			if (!$cstart) $js_var .= ",";
			$js_var .= "\n"."    \"".$crs['code']."\": \"".$crs['def']."\"";
		}
	}
	$js_var .= "\n"."  }";
}
$js_var .= "\n"."}";
echo $js_var;
?>