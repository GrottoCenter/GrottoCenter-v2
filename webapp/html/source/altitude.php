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
 * @copyright Copyright (c) 2009-2012 Cl�ment Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../conf/config.php");
include("../func/function.php");
include("declaration.php"); 
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
$lat = (isset($_GET['lat'])) ? $_GET['lat'] : '';
$lng = (isset($_GET['lng'])) ? $_GET['lng'] : '';
$source = (isset($_GET['s'])) ? $_GET['s'] : 'gmaps'; //'geonames'; //'usgs';
$source = 'gmaps';

function getElevation($lat, $lng, $source)
{
	$msg = "";
	switch ($source) {
		case "gmaps":
			$url = "http://maps.google.com/maps/api/elevation/xml?locations=".$lat.",".$lng."&sensor=false";
			if(!$dom = @domxml_open_file($url)) {
				die("Error opening xml file");
			}
			$xpath = @xpath_new_context($dom);
			if ($_lat = @xpath_eval_expression($xpath, '//location/lat/text()')) {
				$_lat = $_lat->nodeset[0]->node_value();
			}
			if ($_lng = @xpath_eval_expression($xpath, '//location/lng/text()')) {
				$_lng = $_lng->nodeset[0]->node_value();
			}
			if ($alt = @xpath_eval_expression($xpath, '//elevation/text()')) {
				$alt = $alt->nodeset[0]->node_value();
				$msg = "";
			} else {
				if ($msg = @xpath_eval_expression($xpath, '//status/text()')) {
					$msg = $msg->nodeset[0]->node_value();
				}
			}
			$ds = '';
			break;
		case "usgs":
			$url = "http://gisdata.usgs.gov/xmlwebservices2/elevation_service.asmx/getElevation?X_Value=".$lng."&Y_Value=".$lat."&Elevation_Units=METERS&Source_Layer=-1&Elevation_Only=-1";
			//$dom = @domxml_open_file($url);
			if(!$dom = @domxml_open_file($url)) {
				die("Error opening xml file");
			}
			$xpath = @xpath_new_context($dom);
			if ($_lat = @xpath_eval_expression($xpath, '//Y_Value/text()')) {
				$_lat = $_lat->nodeset[0]->node_value();
			}
			if ($_lng = @xpath_eval_expression($xpath, '//X_Value/text()')) {
				$_lng = $_lng->nodeset[0]->node_value();
			}
			//if ($alt = @xpath_eval_expression($xpath, '//Elevation/text()')) {
			if ($alt = @xpath_eval_expression($xpath, '//Elevation_Only/text()')) {
				$alt = $alt->nodeset[0]->node_value();
				$msg = "";
			} else {
				if ($msg = @xpath_eval_expression($xpath, '//Elevation_Query/text()')) {
					$msg = $msg->nodeset[0]->node_value();
				}
			}
			//if ($ds = @xpath_eval($xpath, '//Data_Source/text()')) {
			if ($ds = @xpath_eval_expression($xpath, '//Source_Layer/text()')) {
				$ds = $ds->nodeset[0]->node_value();
			}
			break;
		case "geonames":
			$url = "http://ws.geonames.org/srtm3?lat=".$lat."&lng=".$lng."&style=full&type=XML";
			//$dom = @domxml_open_file($url);
			if(!$dom = @domxml_open_file($url)) {
				die("Error opening xml file");
			}
			$xpath = @xpath_new_context($dom);
			if ($_lat = @xpath_eval_expression($xpath, '//lat/text()')) {
				$_lat = $_lat->nodeset[0]->node_value();
			}
			if ($_lng = @xpath_eval_expression($xpath, '//lng/text()')) {
				$_lng = $_lng->nodeset[0]->node_value();
			}
			if ($alt = @xpath_eval_expression($xpath, '//srtm3/text()')) {
				$alt = $alt->nodeset[0]->node_value();
				$alt = ($alt == '-32768') ? 0 : $alt;
				$msg = "";
			}/* else {
				if ($msg = @xpath_eval_expression($xpath, '//Elevation_Query/text()')) {
					$msg = $msg->nodeset[0]->node_value();
				}
			}*/
			$ds = 'SRTM3';
			break;
		default:
			exit();
	}
	return array("lat" => $_lat, "lng" => $_lng, "alt" => $alt, "src" => $ds, "msg" => $msg, "url" => $url);
}

$ret = getElevation($lat, $lng, $source);
if ($ret['alt'] == "") {
	$source = 'geonames';
	$ret = getElevation($lat, $lng, $source);
}
$lat = $ret['lat'];
$lng = $ret['lng'];
$alt = $ret['alt'];
$ds = $ret['src'];
$msg = $ret['msg'];
$url = $ret['url'];
?>
<div>
		<b><convert>#label=103<convert><!--latitude--> = <?php echo $lat; ?> <convert>#label=290<convert><!--°--><convert>#label=293<convert><!--N-->.<br />
		<convert>#label=105<convert><!--longitude--> = <?php echo $lng; ?> <convert>#label=290<convert><!--°--><convert>#label=294<convert><!--E-->.<br />
		<convert>#label=491<convert><!--altitude--> = <?php echo $alt; ?> <convert>#label=492<convert><!--mètres au dessus du niveau de la mer-->.</b><br />
		<div class="credit">
<?php if (false) { ?>
			<b><convert>#label=493<convert><!--Précision--> :</b> 3 <convert>#label=494<convert><!--secondes d'arc--> <convert>#label=495<convert><!--(env.--> 0.09 <convert>#label=496<convert><!--kilomètre)-->.<br />
			<b><convert>#label=497<convert><!--Description--> :</b> <?php echo $ds; ?>.<br /> <convert>#label=501<convert><!--Shuttle Radar Topography Mission (SRTM) elevation data. SRTM consisted of a specially modified radar system that flew onboard the Space Shuttle Endeavour during an 11-day mission in February of 2000. The dataset covers land areas between 60 degrees north and 56 degrees south.
			This web service is using SRTM3 data with data points located every 3-arc-second (approximately 90 meters) on a latitude/longitude grid.--><br />
			<b><convert>#label=499<convert><!--Documentation--> :</b> <a href="http://gisdata.usgs.gov/XMLWebServices/TNM_Elevation_Service.php" target="_blank">USGS TNM Elevation Service</a>, <a href="http://www2.jpl.nasa.gov/srtm/" target="_blank"><convert>#label=502<convert><!--Nasa--></a>.<br />
<?php } ?>
			<?php echo $url; ?>
		</div>
</div>
<?php
$virtual_page = "overview/altitude/".$_SESSION['language'];
include_once "../func/suivianalytics.php";
?>