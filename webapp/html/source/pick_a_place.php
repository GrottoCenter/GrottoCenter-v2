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
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.26&libraries=places&key=<?php echo Google_key; ?>&language=<?php echo $_SESSION['language']; ?>"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need functions: getTargetNode
    var map, geocoder, marker_user, GCinfoWindow;

    function loadMap() {
      var gLatLng, basicZoom, defaultZoom;
      basicZoom = 10;
        //Create the geocoder
        geocoder = new google.maps.Geocoder();
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
        map = new google.maps.Map(xtdGetElementById("map"),{
            center: gLatLng,
            zoom: defaultZoom,
            scaleControl: true,
            overviewMapControl: true,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
        });
        GCinfoWindow = new google.maps.InfoWindow();
        google.maps.event.addListener(map, 'click', function (object) {
          mapOnLeftClick(object.latLng);
        });

  		showMarkerUser(gLatLng, "<?php echo $category; ?>");
    }

    function getCoordsByDirection(sDirection, callback) {
      if (sDirection != undefined) {
        geocoder.geocode({address: sDirection}, callback);
      }
    }

    function mapOnLeftClick(GLatLng) {
      updateDisplay(GLatLng);
    }

    function updateDisplay(geocoderResult) {
        if (geocoderResult[0]) {
            showMarkerUser(geocoderResult[0].geometry.location, "<?php echo $category; ?>");
            updateLocation(geocoderResult[0].geometry.location);
        }
    }

    function showMarkerUser(gLatLng, category, doZoomIn) {
      if (doZoomIn === undefined) {
        doZoomIn = true;
      }
      if (marker_user === undefined) {
        setMarkerUser(category, gLatLng);
      } else {
        marker_user.setPosition(gLatLng);
      }
      map.setCenter(gLatLng);
      //google.maps.event.trigger(marker_user, 'click');
      openRGCInfoWindow(gLatLng, marker_user);
      if (doZoomIn) {
        map.setZoom(13);
      }
    }

    function setMarkerUser(category, point) {
      var object, titleHere;
      object = createIcon(category, false, "m");
      titleHere = getTitleTemp();
      marker_user = new google.maps.Marker({
          position: point,
          title: titleHere,
          draggable: true,
          bouncy: true,
          icon: object.image,
          shadow: object.shadow});
      google.maps.event.addListener(marker_user, "dragstart", function () {
          GCinfoWindow.close();
      });
      google.maps.event.addListener(marker_user, "dragend", function (object) {
        openRGCInfoWindow(object.latLng, marker_user);
        updateLocation(object.latLng);
      });
      google.maps.event.addListener(marker_user, "click", function (object) {
        openRGCInfoWindow(object.latLng, marker_user);
      });
      marker_user.setMap(map);
    }

    function updateLocation(latLng) {
      if (mySite.filter.recieveLocation != undefined) {
        mySite.filter.recieveLocation(latLng.lat(), latLng.lng());
      }
    }

    function createIcon(category, isConnected, icon_type) {
        var image = {}, shadow = {};
      if (isConnected === undefined || isConnected === "false") {
        isConnected = false;
      }
      switch (category) {
      case "entry":
        if (icon_type === "m") {
            image.scaledSize = new google.maps.Size(10.67, 21.33);//16,32
            shadow.scaledSize = new google.maps.Size(18, 21.33);//27,32
          //tinyIcon.iconAnchor = new google.maps.Point(4, 21.33);//6,32
          //tinyIcon.infoWindowAnchor = new google.maps.Point(5.33, 1);//8,2
          image.url = "../images/icons/entry2.png";
          shadow.url = "../images/icons/entry2_shadow.png";
        } else {
            image.scaledSize = new google.maps.Size(17.34, 28);
          shadow.scaledSize = new google.maps.Size(26.67, 20);
          //tinyIcon.iconAnchor = new google.maps.Point(8.67, 28);
          //tinyIcon.infoWindowAnchor = new google.maps.Point(7.33, 2.5);
          image.url = "../images/icons/entry2_clust.png";
          shadow.url = "../images/icons/entry2_clust_shadow.png";
        }
        break;
      case "caver":
        if (icon_type === "m") {
            image.scaledSize = new google.maps.Size(21.33, 20.67);//32,31
          shadow.scaledSize =  new google.maps.Size(28, 21.33);//42,32
          //tinyIcon.iconAnchor = new google.maps.Point(13.33, 21.33);//20,32
          //tinyIcon.infoWindowAnchor = new google.maps.Point(18, 8);//14, 1
          if (isConnected) {
            image.url = "../images/icons/caver2_connected.png";
          } else {
            image.url = "../images/icons/caver2.png";
          }
          shadow.url = "../images/icons/caver2_shadow.png";
        } else {
            image.scaledSize = new google.maps.Size(28, 27.13);
          shadow.scaledSize = new google.maps.Size(36, 21.33);
          //tinyIcon.iconAnchor = new google.maps.Point(14, 28);
          //tinyIcon.infoWindowAnchor = new google.maps.Point(23.63, 10.5);
          image.url = "../images/icons/caver2_clust.png";
          shadow.url = "../images/icons/caver2_clust_shadow.png";
        }
        break;
      case "grotto":
        if (icon_type === "m") {
            image.scaledSize = new google.maps.Size(21.33, 21.33);//32,32
          shadow.scaledSize = new google.maps.Size(26.67, 21.33);//40,32
          //tinyIcon.iconAnchor = new google.maps.Point(10.67, 21.33);//16,32
          //tinyIcon.infoWindowAnchor = new google.maps.Point(14.67, 3.33);//22,5
          image.url = "../images/icons/grotto1.png";
          shadow.url = "../images/icons/grotto1_shadow.png";
        } else {
            image.scaledSize = new google.maps.Size(28, 28);
          shadow.scaledSize = new google.maps.Size(36, 19.33);
          //tinyIcon.iconAnchor = new google.maps.Point(14, 28);
          //tinyIcon.infoWindowAnchor = new google.maps.Point(14.67, 3.33);
          image.url = "../images/icons/grotto1_clust.png";
          shadow.url = "../images/icons/grotto1_clust_shadow.png";
        }
        break;
      }
      return {image: image, shadow: shadow};
    }

    function getTitleTemp() {
      return "<convert>#label=126<convert>"; //Faites glisser le marqueur ...
    }

    function openRGCInfoWindow(latLng, marker) {
      if (latLng) {
        geocoder.geocode({location: latLng}, function(addresses, status) {
          var myHtml, address, title;
          title = getTitleTemp();
          if (status != google.maps.GeocoderStatus.OK) {
            myHtml = title;
          }
          else {
            myHtml = title + "<br />\n<b>" + addresses[0].formatted_address + "</b>";
          }
          GCinfoWindow.setContent(myHtml);
          if (marker) {
              GCinfoWindow.open(map, marker);
          } else {
              GCinfoWindow.open(map);
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

    google.setOnLoadCallback(loadContext, true);

    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body>
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
