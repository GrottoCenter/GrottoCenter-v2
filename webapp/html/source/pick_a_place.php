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
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$category = (isset($_GET['cat'])) ? $_GET['cat'] : "caver";
$latitude = (isset($_GET['lat'])) ? $_GET['lat'] : "";
$longitude = (isset($_GET['lng'])) ? $_GET['lng'] : "";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
    <script type="text/javascript" src="../scripts/arraysLib.js"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=391<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/pick_a_place.css" />
		<link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need functions: getTargetNode
    var map, geocoder, marker_user;
    
    function loadMap() {
      var gLatLng, basicZoom, defaultZoom;
      basicZoom = 10;
      if (google.maps.BrowserIsCompatible()) {
        //Create the map
        map = new google.maps.Map2(xtdGetElementById("map"));
        //Create the geocoder
        geocoder = new google.maps.ClientGeocoder();
<?php if ($latitude == "" || $longitude == "") { ?>
        //Get the client location
        gLatLng = getClientLatLng();
        if (!gLatLng) {
          gLatLng = new google.maps.LatLng(47.75, 2.11);
        }
        updateLocation(gLatLng);
<?php } else { ?>
        gLatLng = new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>);
<?php } ?>
        defaultZoom = basicZoom;
        google.maps.Event.addListener(map, 'click', function (overlay, latlng) {
          mapOnLeftClick(overlay, latlng);
        });
  			map.addMapType(G_PHYSICAL_MAP);  			
  			//Set the default position on the map
        map.setCenter(gLatLng, defaultZoom);
        //Add controls
  			map.addControl(new google.maps.LargeMapControl3D());
  			// /!\ WARNING: GENERATES A ERROR ON 3D MAP TYPE:
        map.addControl(new google.maps.OverviewMapControl());
        // /!\ END OF WARNING.
  			map.addControl(new google.maps.ScaleControl());
  			map.addControl(new google.maps.HierarchicalMapTypeControl());
  			// bind a search control to the map, suppress result list
        map.addControl(new google.elements.LocalSearch({"searchFormHint": "<convert>#label=487<convert>"}), new google.maps.ControlPosition(G_ANCHOR_BOTTOM_LEFT, new google.maps.Size(10, 35)));//Recherche google
  			map.enableDoubleClickZoom();
  			map.enableScrollWheelZoom();
  			map.enableContinuousZoom();
  			showMarkerUser(gLatLng, "<?php echo $category; ?>");
      }
    }
    
    function getCoordsByDirection(sDirection, callback) {
      if (sDirection != undefined) {
        geocoder.getLatLng(sDirection, callback);
      }
    }
    
    function mapOnLeftClick(overlay, GLatLng) {
      if(overlay) {
        GLatLng = marker_user.getLatLng();
      }
      updateDisplay(GLatLng);
    }
    
    function updateDisplay(GLatLng) {
      showMarkerUser(GLatLng, "<?php echo $category; ?>");
      updateLocation(GLatLng);
    }

    function showMarkerUser(gLatLng, category, doZoomIn) {
      if (doZoomIn === undefined) {
        doZoomIn = true;
      }
      if (marker_user === undefined) {
        setMarkerUser(category, gLatLng);
      } else {
        marker_user.setLatLng(gLatLng);
      }
      map.setCenter(gLatLng);
      //google.maps.Event.trigger(marker_user, 'click');
      openRGCInfoWindow(gLatLng, marker_user);
      if (doZoomIn) {
        map.setZoom(13);
      }
    }
    
    function setMarkerUser(category, point) {
      var tinyIcon, titleHere;
      tinyIcon = createIcon(category, false, "m");
      titleHere = getTitleTemp();
      marker_user = new google.maps.Marker(point, {title: titleHere, draggable: true, bouncy: true, icon:tinyIcon});
      google.maps.Event.addListener(marker_user, "dragstart", function () {
        map.closeInfoWindow();
      });
      google.maps.Event.addListener(marker_user, "dragend", function (latLng) {
        openRGCInfoWindow(latLng, marker_user);
        updateLocation(latLng);
      });
      google.maps.Event.addListener(marker_user, "click", function (latLng) {
        openRGCInfoWindow(latLng, marker_user);
      });
      map.addOverlay(marker_user);
    }
    
    function updateLocation(latLng) {
      if (mySite.filter.recieveLocation != undefined) {
        mySite.filter.recieveLocation(latLng.lat(), latLng.lng());
      }
    }
    
    function createIcon(category, isConnected, icon_type) {
      var tinyIcon;
      if (isConnected === undefined || isConnected === "false") {
        isConnected = false;
      }
      tinyIcon = new google.maps.Icon();
      switch (category) {
      case "entry":
        if (icon_type === "m") {
          tinyIcon.iconSize = new google.maps.Size(10.67, 21.33);//16,32
          tinyIcon.shadowSize = new google.maps.Size(18, 21.33);//27,32
          tinyIcon.iconAnchor = new google.maps.Point(4, 21.33);//6,32
          tinyIcon.infoWindowAnchor = new google.maps.Point(5.33, 1);//8,2
          tinyIcon.image = "../images/icons/entry2.png";
          tinyIcon.shadow = "../images/icons/entry2_shadow.png";
        } else {
          tinyIcon.iconSize = new google.maps.Size(17.34, 28);
          tinyIcon.shadowSize = new google.maps.Size(26.67, 20);
          tinyIcon.iconAnchor = new google.maps.Point(8.67, 28);
          tinyIcon.infoWindowAnchor = new google.maps.Point(7.33, 2.5);
          tinyIcon.image = "../images/icons/entry2_clust.png";
          tinyIcon.shadow = "../images/icons/entry2_clust_shadow.png";
        }
        break;          
      case "caver":
        if (icon_type === "m") {
          tinyIcon.iconSize = new google.maps.Size(21.33, 20.67);//32,31
          tinyIcon.shadowSize =  new google.maps.Size(28, 21.33);//42,32
          tinyIcon.iconAnchor = new google.maps.Point(13.33, 21.33);//20,32
          tinyIcon.infoWindowAnchor = new google.maps.Point(18, 8);//14, 1
          if (isConnected) {
            tinyIcon.image = "../images/icons/caver2_connected.png";
          } else {
            tinyIcon.image = "../images/icons/caver2.png";
          }
          tinyIcon.shadow = "../images/icons/caver2_shadow.png";
        } else {
          tinyIcon.iconSize = new google.maps.Size(28, 27.13);
          tinyIcon.shadowSize = new google.maps.Size(36, 21.33);
          tinyIcon.iconAnchor = new google.maps.Point(14, 28);
          tinyIcon.infoWindowAnchor = new google.maps.Point(23.63, 10.5);
          tinyIcon.image = "../images/icons/caver2_clust.png";
          tinyIcon.shadow = "../images/icons/caver2_clust_shadow.png";
        }
        break;
      case "grotto":
        if (icon_type === "m") {
          tinyIcon.iconSize = new google.maps.Size(21.33, 21.33);//32,32
          tinyIcon.shadowSize = new google.maps.Size(26.67, 21.33);//40,32
          tinyIcon.iconAnchor = new google.maps.Point(10.67, 21.33);//16,32
          tinyIcon.infoWindowAnchor = new google.maps.Point(14.67, 3.33);//22,5
          tinyIcon.image = "../images/icons/grotto1.png";
          tinyIcon.shadow = "../images/icons/grotto1_shadow.png";
        } else {
          tinyIcon.iconSize = new google.maps.Size(28, 28);
          tinyIcon.shadowSize = new google.maps.Size(36, 19.33);
          tinyIcon.iconAnchor = new google.maps.Point(14, 28);
          tinyIcon.infoWindowAnchor = new google.maps.Point(14.67, 3.33);
          tinyIcon.image = "../images/icons/grotto1_clust.png";
          tinyIcon.shadow = "../images/icons/grotto1_clust_shadow.png";
        }
        break;
      }
      return tinyIcon;
    }
    
    function getTitleTemp() {
      return "<convert>#label=126<convert>"; //Faites glisser le marqueur ...
    }
    
    function openRGCInfoWindow(latLng, marker) {
      if (latLng) {
        geocoder.getLocations(latLng, function(addresses) {
          var myHtml, address, title;
          title = getTitleTemp();
          if(addresses.Status.code != 200) {
            myHtml = title;
          }
          else {
            address = addresses.Placemark[0];
            myHtml = title + "<br />\n<b>" + address.address + "</b>";
          }
          if (marker) {
            marker.openInfoWindow(myHtml);
          } else {
            map.openInfoWindow(myHtml);
          }
        });
      }
    }
    
    function getClientLatLng() {
      if (google.loader.ClientLocation) {
        return new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
      } else {
        return undefined;
      }
    }
    
    function loadContext() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      loadMap();
    }
    
    function unload() {
      google.maps.Unload();
      isLoaded = false;
    }
    
    google.load("maps", "2", {"language" : "<?php echo $_SESSION['language']; ?>"});
    google.load("elements", "1", {packages : ["localsearch"], "language" : "<?php echo $_SESSION['language']; ?>"});
    google.setOnLoadCallback(loadContext, true);

    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onunload="JavaScript:unload();">
    <?php echo getTopFrame(true, "height:100%;"); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div id="map" class="map"></div>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "pickaplace/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
