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
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
    $sql = "SELECT ca.Id, CONCAT_WS(', ',ca.Address,ca.City,ca.Region,cy.En_name) AS Address FROM T_caver ca INNER JOIN T_country cy ON (cy.Iso = ca.Country AND cy.Latitude = ca.Latitude AND cy.Longitude = ca.Longitude) WHERE ca.Region IS NOT NULL OR ca.City IS NOT NULL OR ca.Address IS NOT NULL";
    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
    $address_column = "Address";
    $ID_column = "Id";
    $addresses_array = array();
    $ID_array = array();
    for ($i = 0; $i < $data['Count']; $i = $i + 1) {
      $addresses_array[] .= $data[$i][$address_column];
      $ID_array[] .= $data[$i][$ID_column];
    }
    array_walk($addresses_array,'set_quotes',"\"");
    $addresses = implode(",", $addresses_array);
    array_walk($ID_array,'set_quotes',"\"");
    $ID = implode(",", $ID_array);
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?></title>
    <link rel="stylesheet" type="text/css" href="../css/overview.css" />
    <link rel="stylesheet" type="text/css" href="../css/overview_p.css" media="print" />
		<link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
		<link rel="stylesheet" type="text/css" href="../css/contextualMenu.css" />
    <style type="txt/css">
      v\:* {behavior:url(#default#VML);}
    </style>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    var map, geocoder, nameArray, idArray, latArray, lngArray, index, counter;
    nameArray = new Array(<?php echo $addresses; ?>);
    idArray = new Array(<?php echo $ID; ?>);
    latArray = new Array();
    lngArray = new Array();
    index = 0;
    
    function loadMap() {
      if (google.maps.BrowserIsCompatible()) {
        //Create the map
        map = new google.maps.Map2(document.getElementById("map"));
        //Create the geocoder
        geocoder = new google.maps.ClientGeocoder();
        /*//Add layers
    		map.addMapType(G_PHYSICAL_MAP);
    		map.addMapType(G_SATELLITE_3D_MAP);
  			//Set the default position on the map
        map.setCenter(new google.maps.LatLng(0, 0), 4);
        map.setMapType(G_PHYSICAL_MAP);
  			map.enableDoubleClickZoom();
  			map.enableScrollWheelZoom();
  			map.enableContinuousZoom();*/
      }
    }
    
    function loadAddresses() {
      geocoder.getLatLng(nameArray[index], addRows);
    }
    
    function checkCavers() {
    }
    
    function addRows(latLng) {
      var row, cell_id, cell_name, cell_lat, cell_lng, table;
      table = document.getElementById('table');
      row = document.createElement("tr");
      cell_name = document.createElement("td");
      cell_name.appendChild(document.createTextNode(nameArray[index]));
      cell_id = document.createElement("td");
      cell_id.appendChild(document.createTextNode(idArray[index]));
      cell_lat = document.createElement("td");
      cell_lng = document.createElement("td");
      if (latLng !== null) {
        cell_lat.appendChild(document.createTextNode(latLng.lat()));
        cell_lng.appendChild(document.createTextNode(latLng.lng()));
        latArray.push(latLng.lat());
        lngArray.push(latLng.lng());
        counter = 0;
      } else {
        if (counter > 3) {
          cell_lat.appendChild(document.createTextNode("Unk"));
          cell_lng.appendChild(document.createTextNode("Unk"));
          latArray.push(undefined);
          lngArray.push(undefined);
          counter = 0;
        } else {
          counter = counter + 1;
          setTimeout('loadAddresses()', 100);
          return '';
        }
      }
      row.appendChild(cell_id);
      row.appendChild(cell_name);
      row.appendChild(cell_lat);
      row.appendChild(cell_lng);
      table.appendChild(row);
      index = index + 1;
      if (index < nameArray.length) {
        setTimeout('loadAddresses()', 100);
      } else {
        checkCavers();
      }
    }
    
    function loadContext() {
      loadMap();
      loadAddresses();
    }
    
    function unload() {
      google.maps.Unload();
      isLoaded = false;
    }
    
    google.load("maps", "2.x", {"language" : "<?php echo $_SESSION['language']; ?>", "other_params":"sensor=true"});
    google.setOnLoadCallback(loadContext, true);

    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onunload="JavaScript:unload();"><!-- onload="JavaScript:toggle();" onresize="JavaScript:containerResized();"-->
    <?php echo getNoScript("",""); ?>
    <div style="height:50%;width:100%;">
      <div id="map" class="map"></div>
    </div>
		<table border="1" id="table">
      <tr>
        <th>ID</th>
        <th>Country</th>
        <th>Latitude</th>
        <th>Longitude</th>
      </tr>
    </table>
<?php
    $virtual_page = "overview/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
