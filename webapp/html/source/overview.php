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
$frame = "overview";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
    <script type="text/javascript" src="../scripts/arraysLib.js"></script>
    <script type="text/javascript" src="../scripts/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../scripts/jquery.cookie.js"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
  	$advanced_filter = (isset($_SESSION['advanced_filter'])) ? $_SESSION['advanced_filter'] : 'false';
  	$entry_filter = (isset($_SESSION['entry_filter'])) ? $_SESSION['entry_filter'] : 'true';
//CRO 2011-10-12
    $grotto_filter = (isset($_SESSION['grotto_filter'])) ? $_SESSION['grotto_filter'] : 'false'; //true';
    $caver_filter = (isset($_SESSION['caver_filter'])) ? $_SESSION['caver_filter'] : 'false'; //true';
  	unset($_SESSION['advanced_filter']);
  	unset($_SESSION['entry_filter']);
  	unset($_SESSION['grotto_filter']);
  	unset($_SESSION['caver_filter']);

  	function getOverviewLayers($enabled)
    {
      $sql = "SELECT ";
      $sql .= "g.Code AS Group_code,g.Name AS Group_name,lbl.".$_SESSION['language']." AS Label,l.Code AS Layer_code,l.Url,l.Name AS Layer_name,l.Short_name,l.Layer,l.Style,l.Format,l.Version,l.Background_color ";
      $sql .= "FROM `".$_SESSION['Application_host']."`.`T_layer` l ";
      $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_group_layer` gl ON gl.Id_layer = l.Id ";
      $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_group_layer` g ON g.Id = gl.Id_group ";
      $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_label` lbl ON lbl.Id = g.Id_label ";
      if ($enabled) {
        $sql .= "WHERE l.Enabled = 'YES' ";
      }
      $sql .= "ORDER BY 1,2,3,4,5,6,7,8,9,10,11,12 ";
      $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
      $group_code = "";
      $return = "";
      for($i=0;$i<$data['Count'];$i++) {
        if ($group_code != $data[$i]['Group_code']) {
          $group_code = $data[$i]['Group_code'];
          if ($return != "") {
            $return = substr($return,0,strlen($return)-1);
            $return .= '}},'."\n";
          }
          $return .= '"'.$group_code.'":{"NAME":"'.$data[$i]['Group_name'].'",
                                          "SHORTNAME":"'.$data[$i]['Label'].'",
                                          "OPACITY":opacity,
                                          "SPECS":{';
        }
        $return .= '"'.$data[$i]['Layer_code'].'":{"URL":"'.$data[$i]['Url'].'",
                                "NAME":"'.$data[$i]['Layer_name'].'",
                                "SHORTNAME":"'.$data[$i]['Short_name'].'",
                                "LAYER":"'.$data[$i]['Layer'].'",
                                "STYLE":"'.$data[$i]['Style'].'",
                                "FORMAT":"'.$data[$i]['Format'].'",
                                "VERSION":"'.$data[$i]['Version'].'",
                                "BGCOLOR":"'.$data[$i]['Background_color'].'"}';
				if ($group_code == $data[$i+1]['Group_code']) {
					$return .= ',';
				}
				$return .= "\n";
      }
      return $return."}}";
    }
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=391<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/overview.css" />
    <link rel="stylesheet" type="text/css" href="../css/overview_p.css" media="print" />
		<link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
		<link rel="stylesheet" type="text/css" href="../css/infowindow.css?v3" />
		<link rel="stylesheet" type="text/css" href="../css/contextualMenu.css?v3" />
    <style type="txt/css">
      v\:* {behavior:url(#default#VML);}
    </style>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.26&libraries=places&key=<?php echo Gmaps_key; ?>&language=<?php echo $_SESSION['language']; ?>"></script>
    <script type="text/javascript" src="../scripts/gmap-wms.js?v3"></script>
    <!-- <script type="text/javascript" src="../scripts/dragzoom.js"></script> -->
    <script type="text/javascript" src="../scripts/GCMap.js"></script>
    <!-- <script type="text/javascript" src="../scripts/lib/ExtDraggableObject.js"></script> -->
    <!-- <script type="text/javascript" src="../scripts/lib/CustomTileOverlay.js"></script> -->
    <!-- <script type="text/javascript" src="../scripts/opacity.js"></script> -->
    <!-- <script type="text/javascript" src="../scripts/GCControls.js"></script> -->
    <script type="text/javascript" src="../scripts/ContextMenu.js?v3"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need functions: getTargetNode, convertMousePsn, copySelectedCoords, detailMarker, editMarker, deleteMarker, addMarker, xtdGetElementById,
		//											showRelationList
    var isLoaded, jg, map, geocoder, directionsService, directionsDisplay, measurementLine, is_over_measurement_line, lineStarted, measurement_handler_updated,
        measurement_handler_over, measurement_handler_out, converter_handler, drawMode, posn1, marker_user, marker_converter,
        allMarkers, allLines, idForShownLines, typeForShownLines, caversLayer, entriesLayer, grottosLayer, linksLayer, mousePosnIsFrozen,
        mouseLatLng, doResetEnvironement, userConnected, counterForAfterLoad, callBackFunction, clusteringLimit, categoryVisibility,
        existingMarkers, lockedMarkers, existingLines, doAbort, debug, mapControl, WMS, BGForWMS, LAYERS, layersOpacity, marker_elevation,
        elevation_handler, GCinfoWindow, dragstarted;//, clip;

    function loadMap() {
      var latLng, dragzoomOpts, gLatLng, basicZoom, defaultZoom, options;
      if (debug) {
          console.log("loadMap");
      }
      dragstarted = false;
      basicZoom = 4;
      //Set up BGForWMS list
      BGForWMS = [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID, google.maps.MapTypeId.TERRAIN];
        //Create the geocoder
        geocoder = new google.maps.Geocoder();
<?php if (isset($_SESSION['user_default_lat']) && isset($_SESSION['user_default_lng']) && isset($_SESSION['user_default_zoom'])) { ?>
        //Get user defined position
        gLatLng = new google.maps.LatLng(<?php echo defaultZero($_SESSION['user_default_lat']); ?>, <?php echo defaultZero($_SESSION['user_default_lng']); ?>);
        defaultZoom = '<?php echo $_SESSION['user_default_zoom']; ?>';
        defaultZoom = (defaultZoom == '')? basicZoom : parseInt(defaultZoom, 10);
<?php } else { ?>
        //Get the client location
        gLatLng = getClientLatLng();
        if (!gLatLng) {
          gLatLng = new google.maps.LatLng(47.75, 2.11);
        }
        defaultZoom = basicZoom;
<?php } ?>

        map = new google.maps.Map(document.getElementById("map"),{
            center: gLatLng,
            zoom: defaultZoom,
            scaleControl: true,
            overviewMapControl: true,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            mapTypeControlOptions: {
                mapTypeIds: BGForWMS,
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
            }
        });

        google.maps.event.addListener(map, 'mousemove', function (event) {
          mapOnMouseMove(event);
        });
        google.maps.event.addListener(map, 'click', function (event) {
            map.set('disableDoubleClickZoom', false);
            mapOnLeftClick(event);
        });
        google.maps.event.addListener(map, 'rightclick', function(e) {
            map.set('disableDoubleClickZoom', true);
        });

        google.maps.event.addListener(map, 'infowindowopen', mapOnInfowindowOpen);
        //Add layers
    		layersOpacity = 0.4;
    		loadExtraLayers(layersOpacity);
  			//Set the default position on the map

  			//Opacity control
  			//map.addControl(new OpacityControl('<convert>#label=776<convert>')); //Transparence
  			//BS createOpacityControl(map, layersOpacity);
        loadContextMenu();
  			//Refresh control
  			//BS var GCControlsContainer = document.createElement('div');
  		    //BS GCControls(GCControlsContainer, map);

  		    //BS GCControlsContainer.index = 1;
  		    //BS map.controls[google.maps.ControlPosition.TOP_RIGHT].push(GCControlsContainer);

        //BS google.maps.event.addListener(map, 'moveend', loadFromXML);
        //BS google.maps.event.addListener(map, 'viewchangeend', loadFromXML);
            initDirections();
  			GCinfoWindow = new google.maps.InfoWindow();
  			google.maps.event.addListener(GCinfoWindow, 'closeclick', function() {
  	          loadMarkers();
  	        });
  	        var searchMarkers = [];
  			var searchInput = document.getElementById('searchBox');
  		    var searchBox = new google.maps.places.SearchBox(searchInput);
  		  // [START region_getplaces]
  		  // Listen for the event fired when the user selects an item from the
  		  // pick list. Retrieve the matching places for that item.
  		  google.maps.event.addListener(searchBox, 'places_changed', function() {
  		    var places = searchBox.getPlaces();

  		    for (var i = 0, marker; marker = searchMarkers[i]; i++) {
  		      marker.setMap(null);
  		    }

  		    // For each place, get the icon, place name, and location.
  		    searchMarkers = [];
  		    var bounds = new google.maps.LatLngBounds();
  		    for (var i = 0, place; place = places[i]; i++) {
  		      var image = {
  		        url: place.icon,
  		        size: new google.maps.Size(71, 71),
  		        origin: new google.maps.Point(0, 0),
  		        anchor: new google.maps.Point(17, 34),
  		        scaledSize: new google.maps.Size(25, 25)
  		      };

  		      // Create a marker for each place.
  		      var marker = new google.maps.Marker({
  		        map: map,
  		        icon: image,
  		        title: place.name,
  		        position: place.geometry.location
  		      });

  		      searchMarkers.push(marker);

  		      bounds.extend(place.geometry.location);
  		    }

  		    map.fitBounds(bounds);
  		  });
  		  // [END region_getplaces]

  		  // Bias the SearchBox results towards places that are within the bounds of the
  		  // current map's viewport.
  		  google.maps.event.addListener(map, 'bounds_changed', function() {
  		    var bounds = map.getBounds();
  		    searchBox.setBounds(bounds);
  		  });

  		google.maps.LatLng.prototype.kmTo = function(a){
  		    var e = Math, ra = e.PI/180;
  		    var b = this.lat() * ra, c = a.lat() * ra, d = b - c;
  		    var g = this.lng() * ra - a.lng() * ra;
  		    var f = 2 * e.asin(e.sqrt(e.pow(e.sin(d/2), 2) + e.cos(b) * e.cos
  		    (c) * e.pow(e.sin(g/2), 2)));
  		    return f * 6378.137;
  		};

  		google.maps.Polyline.prototype.inM = function(n){
  		    var a = this.getPath(n), len = a.getLength(), dist = 0;
  		    for (var i=0; i < len-1; i++) {
  		       dist += a.getAt(i).kmTo(a.getAt(i+1));
  		    }
  		    return dist*1000;
  		};

    }

    function loadContextMenu() {
        var contextMenuOptions={};
    	contextMenuOptions.classNames={menu:'context_menu', menuSeparator:'context_menu_separator'};
        var menuItems = [];
        menuItems.push({className:'context_menu_item', eventName:'refreshData', label:"<convert>#label=56<convert>"});
        menuItems.push({className:'context_menu_item', eventName:'copyLatLng', label:"<convert>#label=401<convert>"});
        menuItems.push({className:'context_menu_item', eventName:'convertLatLng', label:"<convert>#label=403<convert>"});
        menuItems.push({className:'context_menu_item', eventName:'elevationHere', label:"<convert>#label=489<convert>"});
        <?php if (allowAccess(entry_edit_all)) { ?>
            menuItems.push({className:'context_menu_item', eventName:'addEntry', label:"<convert>#label=404<convert>"});
        <?php }
        if (allowAccess(grotto_edit_all)) { ?>
            menuItems.push({className:'context_menu_item', eventName:'addGrotto', label:"<convert>#label=406<convert>"});
        <?php } ?>

        contextMenuOptions.menuItems=menuItems;

        //create the ContextMenu object
    	var contextMenu=new ContextMenu(map, contextMenuOptions);

    	//	display the ContextMenu on a Map right click
    	google.maps.event.addListener(map, 'rightclick', function(mouseEvent){
    		contextMenu.show(mouseEvent.latLng);
    	});

    	//	listen for the ContextMenu 'menu_item_selected' event
    	google.maps.event.addListener(contextMenu, 'menu_item_selected', function(latLng, eventName){
    		//	latLng is the position of the ContextMenu
    		//	eventName is the eventName defined for the clicked ContextMenuItem in the ContextMenuOptions
    		switch(eventName){
    			case 'refreshData':
    			    reload();
    				break;
    			case 'copyLatLng':
    			    copySelectedCoords({'lat': latLng.lat(), 'lng': latLng.lng()});
    				break;
    			case 'convertLatLng':
    			    convertMousePsn({'lat': latLng.lat(), 'lng': latLng.lng()});
    				break;
    			case 'elevationHere':
    			    showMouseElevation({'lat': latLng.lat(), 'lng': latLng.lng()});
    				break;
    			case 'addEntry':
    			    addMarker('entry',undefined,undefined,'<?php echo $_SESSION['language']; ?>');
    				break;
    			case 'addGrotto':
    			    addMarker('grotto',undefined,undefined,'<?php echo $_SESSION['language']; ?>');
    				break;
    		}
    	});
    }

    function isInfoWindowOpen(){
        var IFmap = GCinfoWindow.getMap();
        return (IFmap !== null && typeof IFmap !== "undefined");
    }

    function setLayersOpacity(opacity) {
      var index, currentMapType;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      //layersOpacity = layersOpacity + delta;
      layersOpacity = opacity;
      index = 1;
      while (map.getCurrentMapType().getTileLayers()[index] !== undefined) {
        map.getCurrentMapType().getTileLayers()[index].getOpacity = function() {
          return layersOpacity;
        }
        index = index + 1;
      }
      currentMapType = map.getMapTypeId();
      map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
      map.setMapTypeId(currentMapType);
    }

    /*BS function setControlRelationship() {
      //Create control relationships
      mapControl = new google.maps.HierarchicalMapTypeControl();
      var bg = 0;
      for(var index in LAYERS) {
        mapControl.addRelationship(BGForWMS[bg], LAYERS[index], undefined, false);
        if (bg + 1 >= BGForWMS.length) {
          bg = 0;
        } else {
          bg = bg + 1;
        }
      }
      mapControl.addRelationship(google.maps.MapTypeId.SATELLITE, google.maps.MapTypeId.HYBRID, undefined, true);
      map.addControl(mapControl);
    }*/

    function loadExtraLayers(opacity) {
      LAYERS = new Object();
      //Set up WMS
      WMS = {<?php echo getOverviewLayers(true); ?>};
//service=WMS&version=1.1.1&request=GetCapabilities&
      var layersArray = BGForWMS;
	  for(var layers in WMS) {
        //BS var layersArray = [];
        for(var layer in WMS[layers]["SPECS"]) {
            getImageMapType(WMS[layers]["SPECS"][layer]["URL"],
                                        WMS[layers]["SPECS"][layer]["NAME"],
                                        WMS[layers]["SPECS"][layer]["SHORTNAME"],
                                        WMS[layers]["SPECS"][layer]["LAYER"],
                                        WMS[layers]["SPECS"][layer]["STYLE"],
                                        WMS[layers]["SPECS"][layer]["FORMAT"],
                                        WMS[layers]["SPECS"][layer]["VERSION"],
                                        WMS[layers]["SPECS"][layer]["BGCOLOR"],
                                        layer);
            layersArray.push(layer);
        }
        /*BS for(var bg = 0; bg < BGForWMS.length; bg = bg + 1) {
          LAYERS[layers + bg] = createWMSOverlaySpec(BGForWMS[bg],
                                              layersArray,
                                              WMS[layers]["NAME"] + " - " + bg,
                                              WMS[layers]["SHORTNAME"],
                                              WMS[layers]["OPACITY"]);
        }
      }
      for(var layer in LAYERS) {
        map.addMapType(LAYERS[layer]);
      }*/
      }
      map.setOptions({mapTypeControlOptions: {
          mapTypeIds: layersArray,
          style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
      });
    }

    function loadFromXML() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      startBusy();
      resetVars();
      loadMarkers();
    }

    function handleErrors() {
      alert("<convert>#label=392<convert>");//Aucune localité correspondant au point de départ ou d'arrivée n'a pu être trouvé.\nCela peut être dû au fait que l'une des adresses est relativement nouvelle ou incorrecte.
    }

    function mapOnMouseMove(event) {
        latlng = event.latLng;
      mouseLatLng = latlng;
      if (!mousePosnIsFrozen) {
        setMouseInputs(latlng);
        //getSrtm3(latlng.lat(), latlng.lng());
      }
      if (drawMode && lineStarted) {
        drawMeasurementLine(event, false);
      }
    }

    function showMouseElevation(latLng) {
      setupElevationMarker(latLng.lat, latLng.lng);
    }

    function mapOnLeftClick(event) {
        if (debug) {
    		console.log("Click!");
    	}
	    if (drawMode) {
	        drawMeasurementLine(event, true);
	    }
      if (elevation_handler) {
        resetElevationMarker();
      }
      if (converter_handler) {
        mySite.details.switchConverter(false, 'converter_menu');
      }
      if (isInfoWindowOpen()) {
          GCinfoWindow.close();
          loadMarkers();
      }
    }

    function lineOnClick(oLine) {
      if (mySite.openedInfoWindowId === oLine.id1 && mySite.openedInfoWindowType === oLine.cat1) {
        openMe(oLine.id2, oLine.cat2, false);
      } else {
        openMe(oLine.id1, oLine.cat1, false);
      }
    }

    function setNearMeList() {
      return;
      var oSelect = mySite.filter.xtdGetElementById('markersNearMeSelect');
      if (oSelect && allMarkers) {
        var list = getListForSelect(allMarkers, "<convert>#label=34<convert>");//Alias
        emptySelect(oSelect);
        fillSelect(list, oSelect);
        sortSelect(oSelect);
      }
    }

    function setVisibilityFilter() {
      var oForm;
      if (mySite.filter) {
        oForm = mySite.filter.document.visibilityFrom;
        if (oForm) {
          oForm.entry.checked = getCategoryVisibility("entry");
          oForm.grotto.checked = getCategoryVisibility("grotto");
          oForm.caver.checked = getCategoryVisibility("caver");
          oForm.link.checked = getCategoryVisibility("link");
          oForm.advanced.checked = getCategoryVisibility("advanced");
          //oForm.limit.value = getClusteringLimit();
          //oForm.limit.selectedIndex = strToFloat(getClusteringLimit()) - 1;
          setSelectedIndex(oForm.limit, getClusteringLimit());
        }
      }
    }

    function setCategoryVisibility(sCategory, bIsVisible) {
      categoryVisibility[sCategory] = bIsVisible;
    }

    function getCategoryVisibility(sCategory) {
      if (categoryVisibility != undefined) {
        return categoryVisibility[sCategory];
      } else {
        return false;
      }
    }

    function setClusteringLimit(iValue) {
      clusteringLimit = iValue;
      reload();
    }

    function getClusteringLimit() {
      return clusteringLimit;
    }


    function drawMeasurementLine(event, doEndMeasurement) {
      var posn, mouseLatLng;
      posn = event.latLng;

      if (overlay && overlay !== measurementLine) {
          if (overlay.getLatLng) {
          posn = overlay.getLatLng();
          } else {
          return false;
          }
          } else {
          mouseLatLng = getMouseLatLng();
          posn = new google.maps.LatLng(mouseLatLng.lat, mouseLatLng.lng);
      }

      if (!lineStarted) {
        if (measurementLine) {
          clearMeasurementLine(true);
          return false;
        }
        posn1 = posn;
        lineStarted = true;
      } else {
        clearMeasurementLine(false);
        measurementLine = new google.maps.Polyline([posn1, posn], "#0F0F0F", 2, 0.5, {"geodesic": true});
        measurementLine.setMap(map);
        if (doEndMeasurement) {
          lineStarted = false;
          measurement_handler_updated = google.maps.event.addListener(measurementLine, "lineupdated", displayMeasurement);
          measurement_handler_over = google.maps.event.addListener(measurementLine, "mouseover", measurementLineOver);
          measurement_handler_out = google.maps.event.addListener(measurementLine, "mouseout", measurementLineOut);
        }
        displayMeasurement();
      }
      return true;
    }

    function measurementLineOver() {
      is_over_measurement_line = true;
      enableEditingMeasurement();
    }

    function measurementLineOut() {
      is_over_measurement_line = false;
      setTimeout('disableEditingMeasurement()', 500);
    }

    function enableEditingMeasurement() {
      if (measurementLine) {
        measurementLine.setEditable(true);
      }
    }

    function disableEditingMeasurement() {
      if (!is_over_measurement_line && measurementLine) {
        measurementLine.setEditable(false);
      }
    }

    function clearMeasurementLine(doResetDisplay) {
      if (measurementLine) {
        if (measurement_handler_updated) {
          google.maps.event.removeListener(measurement_handler_updated);
        }
        if (measurement_handler_over) {
          google.maps.event.removeListener(measurement_handler_over);
        }
        if (measurement_handler_out) {
          google.maps.event.removeListener(measurement_handler_out);
        }
        measurementLine.setEditable(false);
        measurementLine.setMap(null);
        measurementLine = undefined;
        if (doResetDisplay) {
          resetMeasurementDisplay();
        }
      }
    }

    function displayMeasurement() {
      if (measurementLine) {
        mySite.banner.document.measurement.measure.style.color = "black";
        mySite.banner.document.measurement.measure.value = Math.round(measurementLine.inM() * 10) / 10;
      }
    }

    function setMouseInputs(latlng) {
      if (mySite.banner) {
        if (mySite.banner.document.measurement) {
          mySite.banner.document.measurement.mouseLat.value = latlng.lat();
          mySite.banner.document.measurement.mouseLng.value = latlng.lng();
        }
      }
    }

    function freezeMousePsn(doFreeze) {
      mousePosnIsFrozen = doFreeze;
    }

    function resetVars() {
      allMarkers = [];
      allMarkers.length = 0;
      allLines = [];
      allLines.length = 0;
      caversLayer = [];
      entriesLayer = [];
      grottosLayer = [];
      linksLayer = [];
      if (doResetEnvironement) {
        idForShownLines = 0;
        typeForShownLines = "";
      }
    }

    function loadMarkers() {
      loadVars();
      if (doResetEnvironement) {
        resetDetails();
        resetDirections();
        doResetEnvironement = false;
      }
    }

    function loadVars() {
			var postObj;
      counterForAfterLoad = 0;
      cancelAbortLoading();
      if (getCategoryVisibility("entry") ===  true) {
        if (debug) {
          console.log("entry - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
		postObj = getAjaxObj("entry");
		$.ajax({
		    url: postObj.url,
		    type: "POST",
		    data: postObj.data,
		    success: setupEntriesLayer,
		    dataType: "xml",
		    error: synchroAfterLoad
		});
      }
      if (getCategoryVisibility("caver") ===  true) {
        if (debug) {
          console.log("caver - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
		postObj = getAjaxObj("caver");
		$.ajax({
		    url: postObj.url,
		    type: "POST",
		    data: postObj.data,
		    success: setupCaversLayer,
		    dataType: "xml",
		    error: synchroAfterLoad
		});
      }
      if (getCategoryVisibility("grotto") ===  true) {
        if (debug) {
          console.log("grotto - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
		postObj = getAjaxObj("grotto");
		$.ajax({
		    url: postObj.url,
		    type: "POST",
		    data: postObj.data,
		    success: setupGrottosLayer,
		    dataType: "xml",
		    error: synchroAfterLoad
		});
      }
      if (getCategoryVisibility("link") ===  true) {
        if (debug) {
          console.log("link - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
		postObj = getAjaxObj("link");
		$.ajax({
		    url: postObj.url,
		    type: "POST",
		    data: postObj.data,
		    success: setupLinksLayer,
		    dataType: "xml",
		    error: synchroAfterLoad
		});
      }
      if (counterForAfterLoad === 0 || counterForAfterLoad < 0) {
        if (debug) {
          console.log("0 - loadVars");
        }
        setCallBackfunction ("");
        synchroAfterLoad(true);
      }
    }

    function getAjaxObj(sCategory) {
        var bounds, southWest, northEast, isNotMaxZoom, getVars, stdArray, i, url, data, currentMapType;
    	/* Code from http://googlemapsbook.com/chapter7/ */
        //create the boundary for the data to provide
    	//initial filtering
    	bounds = map.getBounds();
    	southWest = bounds.getSouthWest();
    	northEast = bounds.getNorthEast();
    	currentMapType = map.mapTypes.get(map.getMapTypeId());
    	isNotMaxZoom = (map.getZoom() !== currentMapType.maxZoom);
    	getVars = '&ne=' + northEast.toUrlValue() + '&sw=' + southWest.toUrlValue() + '&clust=' + isNotMaxZoom.toString() + '&limit=' + clusteringLimit.toString() + '&advanced=' + getCategoryVisibility("advanced").toString();
    	url = "";
			if (sCategory === "link") {
        getVars = getVars + '&idsl=' + idForShownLines + '&csl=' + typeForShownLines;
				data = "ff=d&category=line&sql=" + encodeURI(encodeURIComponent("SELECT * FROM `<?php echo $_SESSION['Application_host']; ?>`.`V_links` ")) + getVars;
        url = "webservices/getMarkersXML.php";
        } else {
			stdArray = ["Address", "City", "Id", "Latitude", "Longitude", "Name", "Region"];
			switch (sCategory) {
				case "caver":
					stdArray = stdArray.concat(["Login", "Nickname", "Surname"]);
					break;
				default:
					break;
			}
			for (i = 0; i < stdArray.length; i = i + 1) {
				url = url + "T_" + sCategory + "." + stdArray[i] + ", ";
			}
			data = "ff=d&category=" + sCategory + "&sql=" + encodeURI(encodeURIComponent("SELECT " + url + " ifnull(T_" + sCategory + ".Country,'<?php echo Select_default; ?>') AS Country2, 'm' AS `Marker_type` FROM `<?php echo $_SESSION['Application_host']; ?>`.`T_" + sCategory + "` ")) + getVars;
            url = "webservices/getMarkersXML.php";
        }
		t = $.ajax({type:'POST', url:url, async:false, cache:false, data:'ff=g'}).responseText;
		if(t.length<10) return alert("<?php echo MESSAGE_NOT_SENT; ?>"+t);
		$.cookie('<?php echo TOKEN_NAME; ?>',t);
		return {"url":url, "data":data};
    }

    function abortLoading() {
      if (debug) {
        console.log("Start Aborting");
      }
      setAbort("entry", true);
      setAbort("caver", true);
      setAbort("grotto", true);
      setAbort("link", true);
      //synchroAfterLoad(true);
    }

    function cancelAbortLoading() {
      if (debug) {
        console.log("Cancel Aborting");
      }
      setAbort("entry", false);
      setAbort("caver", false);
      setAbort("grotto", false);
      setAbort("link", false);
    }

    function setAbort(sCategory, bValue) {
      doAbort[sCategory] = bValue;
    }

    function getAbort(sCategory) {
      return doAbort[sCategory];
    }

    function setupEntriesLayer(xmlData, httpResponseStatusCode) {
        //BS
        entriesLayer = [];
        //BS
      if (debug) {
        console.log("Entering : setupEntriesLayer");
      }
      var xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlElement = xmlData.documentElement.getElementsByTagName("marker");
        for (i = 0; i < xmlElement.length; i = i + 1) {
          index = entriesLayer.length;
          entriesLayer[index] = {};
          entriesLayer[index].name = xmlElement[i].getAttribute("Name");
          entriesLayer[index].id = xmlElement[i].getAttribute("Id");
          entriesLayer[index].location = xmlElement[i].getAttribute("Latitude") + ", " + xmlElement[i].getAttribute("Longitude");
          entriesLayer[index].latitude = strToFloat(xmlElement[i].getAttribute("Latitude"));
          entriesLayer[index].longitude = strToFloat(xmlElement[i].getAttribute("Longitude"));
          entriesLayer[index].address = xmlElement[i].getAttribute("Address");
          entriesLayer[index].city = xmlElement[i].getAttribute("City");
          entriesLayer[index].region = xmlElement[i].getAttribute("Region");
          entriesLayer[index].country = xmlElement[i].getAttribute("Country2");
          /*entriesLayer[index].massif = xmlElement[i].getAttribute("Id_massif");
          entriesLayer[index].cave = xmlElement[i].getAttribute("Id_cave");
          entriesLayer[index].inscriptionDate = xmlElement[i].getAttribute("Date_inscription");
          entriesLayer[index].reviewedDate = xmlElement[i].getAttribute("Date_reviewed");*/
          entriesLayer[index].icon_type = xmlElement[i].getAttribute("Marker_type");
          if (getAbort("entry")) {
            if (debug) {
              console.log("Abort - entry - SetupEntriesLayer - step " + i);
            }
            //setAbort("entry", false);
            break;
          }
        }
        setupMarkers(entriesLayer,"entry");
      } else {
        synchroAfterLoad();
      }
    }

    function setupCaversLayer(xmlData, httpResponseStatusCode) {
        //BS
        caversLayer = [];
        //BS
      if (debug) {
        console.log("Entering : setupCaversLayer");
      }
      var xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlElement = xmlData.documentElement.getElementsByTagName("marker");
        for (i = 0; i < xmlElement.length; i = i + 1) {
          index = caversLayer.length || 0;
          caversLayer[index] = {};
          caversLayer[index].cavername = xmlElement[i].getAttribute("Name");
          caversLayer[index].caversurname = xmlElement[i].getAttribute("Surname");
          caversLayer[index].nickname = xmlElement[i].getAttribute("Nickname");
          caversLayer[index].login = xmlElement[i].getAttribute("Login");
          caversLayer[index].id = xmlElement[i].getAttribute("Id");
          caversLayer[index].location = xmlElement[i].getAttribute("Latitude") + ", " + xmlElement[i].getAttribute("Longitude");
          caversLayer[index].latitude = strToFloat(xmlElement[i].getAttribute("Latitude"));
          caversLayer[index].longitude = strToFloat(xmlElement[i].getAttribute("Longitude"));
          caversLayer[index].address = xmlElement[i].getAttribute("Address");
          caversLayer[index].city = xmlElement[i].getAttribute("City");
          caversLayer[index].region = xmlElement[i].getAttribute("Region");
          caversLayer[index].country = xmlElement[i].getAttribute("Country2");
          /*caversLayer[index].inscriptionDate = xmlElement[i].getAttribute("Date_inscription");
          caversLayer[index].reviewedDate = xmlElement[i].getAttribute("Date_reviewed");*/
          caversLayer[index].isConnected = xmlElement[i].getAttribute("Is_connected");
          caversLayer[index].isReferent = xmlElement[i].getAttribute("Is_referent");
          caversLayer[index].icon_type = xmlElement[i].getAttribute("Marker_type");
          if (getAbort("caver")) {
            if (debug) {
              console.log("Abort - caver - setupCaversLayer - step " + i);
            }
            //setAbort("caver", false);
            break;
          }
        }
        setupMarkers(caversLayer,"caver");
      } else {
        synchroAfterLoad();
      }
    }

    function setupGrottosLayer(xmlData, httpResponseStatusCode) {
        //BS
        grottosLayer = [];
        //BS
      if (debug) {
        console.log("Entering : setupGrottosLayer");
      }
      var xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlElement = xmlData.documentElement.getElementsByTagName("marker");
        for (i = 0; i < xmlElement.length; i = i + 1) {
          index = grottosLayer.length;
          grottosLayer[index] = {};
          grottosLayer[index].name = xmlElement[i].getAttribute("Name");
          grottosLayer[index].id = xmlElement[i].getAttribute("Id");
          grottosLayer[index].location = xmlElement[i].getAttribute("Latitude") + ", " + xmlElement[i].getAttribute("Longitude");
          grottosLayer[index].latitude = strToFloat(xmlElement[i].getAttribute("Latitude"));
          grottosLayer[index].longitude = strToFloat(xmlElement[i].getAttribute("Longitude"));
          grottosLayer[index].address = xmlElement[i].getAttribute("Address");
          grottosLayer[index].city = xmlElement[i].getAttribute("City");
          grottosLayer[index].region = xmlElement[i].getAttribute("Region");
          grottosLayer[index].country = xmlElement[i].getAttribute("Country2");
          /*grottosLayer[index].inscriptionDate = xmlElement[i].getAttribute("Date_inscription");
          grottosLayer[index].reviewedDate = xmlElement[i].getAttribute("Date_reviewed");*/
          grottosLayer[index].icon_type = xmlElement[i].getAttribute("Marker_type");
          if (getAbort("grotto")) {
            if (debug) {
              console.log("Abort - grotto - setupGrottosLayer - step " + i);
            }
            //setAbort("grotto", false);
            break;
          }
        }
        setupMarkers(grottosLayer,"grotto");
      } else {
        synchroAfterLoad();
      }
    }

    function setupLinksLayer(xmlData, httpResponseStatusCode) {
        //BS
        linksLayer = [];
        //BS
      if (debug) {
        console.log("Entering : setupLinksLayer");
      }
      var xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlElement = xmlData.documentElement.getElementsByTagName("line");
        for (i = 0; i < xmlElement.length; i = i + 1) {
          index = linksLayer.length;
          linksLayer[index] = {};
          linksLayer[index].s = {};
          linksLayer[index].s.category = xmlElement[i].getAttribute("sCategory");
          linksLayer[index].s.id = xmlElement[i].getAttribute("sId");
          linksLayer[index].s.latitude = strToFloat(xmlElement[i].getAttribute("sLat"));
          linksLayer[index].s.longitude = strToFloat(xmlElement[i].getAttribute("sLng"));
          linksLayer[index].e = {};
          linksLayer[index].e.category = xmlElement[i].getAttribute("eCategory");
          linksLayer[index].e.id = xmlElement[i].getAttribute("eId");
          linksLayer[index].e.latitude = strToFloat(xmlElement[i].getAttribute("eLat"));
          linksLayer[index].e.longitude = strToFloat(xmlElement[i].getAttribute("eLng"));
          if (getAbort("link")) {
            if (debug) {
              console.log("Abort - link - setupLinksLayer - step " + i);
            }
            //setAbort("link", false);
            break;
          }
        }
        setupLines(linksLayer);
      } else {
        synchroAfterLoad();
      }
    }

    //Prepare the array of markers
    function setupMarkers(layer, category) {
      if (debug) {
        console.log("Entering : setupMarkers");
      }
      var myMarkers, i, title, caverLogin, id, lat, lng, address, city, region, country, massifId, caveId, position, caverSurname,
          caverName, inscriptionDate, reviewedDate, isConnected, icon_type, marker;
      myMarkers = [];
      for (i = 0; i < layer.length; i = i + 1) {
        if (category === "caver") {
          title = layer[i].nickname;
        } else {
          title = layer[i].name;
        }
        caverLogin = layer[i].login;
        id = layer[i].id;
        lat = layer[i].latitude;
        lng = layer[i].longitude;
        address = layer[i].address;
        city = layer[i].city;
        region = layer[i].region;
        country = layer[i].country;
        massifId = layer[i].massif;
        caveId = layer[i].cave;
        position = new google.maps.LatLng(lat, lng);
        caverSurname = layer[i].caversurname;
        caverName = layer[i].cavername;
        inscriptionDate = layer[i].inscriptionDate;
        reviewedDate = layer[i].reviewedDate;
        isConnected = layer[i].isConnected;
        isReferent = layer[i].isReferent;
        icon_type = layer[i].icon_type;
        if (lat != undefined && lng != undefined) {
          marker = createMarker(position, category, id, address, city, region, country, massifId, caveId, title, caverName, caverSurname, caverLogin, inscriptionDate, reviewedDate, isConnected, isReferent, icon_type);
          allMarkers.push(marker);
          myMarkers.push(marker);
        }
        if (getAbort(category)) {
          if (debug) {
            console.log("Abort - " + category + " - setupMarkers - step " + i);
          }
          //setAbort(category, false);
          break;
        }
      }
      addMarkers(myMarkers, category);
    }

    function createMarker(posn, category, id, address, city, region, country, massifId, caveId, name, caverName, caverSurname, caverLogin, inscriptionDate, reviewedDate, isConnected, isReferent, icon_type) {
      var object, options, marker, connected, clustered, infoURL, infoWindow, loadingHTMLMsg;
      object = createIconOptions(category, isConnected, isReferent, icon_type);
      options = {
                    "position": posn,
                    "map": map,
                    "draggable": true,
                    "id": id,
                    "objectId":id,
                    "category": category,
                    "title": name,
                    "cavername": caverName,
                    "caversurname": caverSurname,
                    "caverlogin": caverLogin,
                    "icon": object.image,
                    "address": address,
                    "city": city,
                    "region": region,
                    "country": country,
                    "massifId": massifId,
                    "caveId": caveId,
                    "inscriptionDate": inscriptionDate,
                    "reviewedDate": reviewedDate,
                    "isConnected": isConnected,
                    "markerType": icon_type};
      marker = new google.maps.Marker(options);
      connected = "";
      if (category === "caver") {
        connected = '&connected=' + isConnected.toString();
      }
      clustered = "&clustered=" + icon_type;
      //infoWindow = '<iframe src="' + category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered + '" frameborder="no" name="infowindow" id="infowindow" class="infoFrame" scrolling="no"></iframe>';
			infoURL = category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered;
			loadingHTMLMsg = '<div style="position:absolute;top:50%;left:50%;"><div style="width:350px;height:16px;text-align:center;vertical-align:middle;position:absolute;top:-8px;left:-175px;font-weight:bold;"><convert>#label=890<convert></div></div>'; //Chargement en cours...
			infoWindow = '<div class="infoFrame" id="GC_IW_LOADING" type="' + icon_type + '" category="' + category + '" markerid="' + id + '" url="' + infoURL + '">' + loadingHTMLMsg + '</div>';
      marker.setDraggable(false);
      google.maps.event.addListener(marker, 'click', function () {
        if (!drawMode) {
            GCinfoWindow.setContent(infoWindow);
            GCinfoWindowOpen(marker);
        }
      });

      if (icon_type === "m") {
        google.maps.event.addListener(marker, 'mouseover', function () {
          overSwitchLines(id, category, true);
        });
        google.maps.event.addListener(marker, 'mouseout', function () {
          overSwitchLines(id, category, false);
        });
        google.maps.event.addListener(marker, "dragstart", function () {
            dragstarted = true;
            GCinfoWindow.close();
        });
        google.maps.event.addListener(marker, "dragend", function () {
            dragstarted = false;
            GCinfoWindow.setContent(infoWindow);
            GCinfoWindowOpen(marker);
          if (mySite.filter.recieveLocation != undefined) {
            mySite.filter.recieveLocation(marker.getPosition().lat(), marker.getPosition().lng());
          }
        });
        google.maps.event.addListener(marker, "infowindowopen", function () {
          mySite.openedInfoWindowId = id;
          mySite.openedInfoWindowType = category;
        });
      }
      return marker;
    }

    function GCinfoWindowOpen(anchor) {
        GCinfoWindow.open(map, anchor);
        mapOnInfowindowOpen();
    }

		function mapOnInfowindowOpen(opts) {
			var id, iwDOM, strOpts, sHTML;
            mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
			strOpts = "";
			if (opts != undefined && typeof(opts) == 'string' && opts != '') {
				strOpts = '&' + opts + '=true';
			}
			id = 'GC_IW_LOADING';
			iwDOM = xtdGetElementById(id);
			if (iwDOM != undefined) {
				sHTML = getResponseText(iwDOM.getAttribute('url') + strOpts);
				iwDOM.innerHTML = sHTML;
				GCinfoWindow.setContent(iwDOM);
				if (idForShownLines == iwDOM.getAttribute('markerid') && typeForShownLines == iwDOM.getAttribute('category')) {
					xtdGetElementById('GC_IW_link').checked = true;
				}
			}
		}

		function infowindowReload(oButton) {
			mapOnInfowindowOpen(oButton.getAttribute('name'));
		}

    function createIconOptions(category, isConnected, isReferent, icon_type) {
      var image = {}, shadow = {};
      if (isConnected === undefined || isConnected === "false") {
        isConnected = false;
      }
      if (isReferent === undefined || isReferent === "false") {
        isReferent = false;
      }
      switch (category) {
          case "entry":
            if (icon_type === "m") {
                image = "../images/icons/entry2.png";
            } else {
                image = "../images/icons/entry2_clust.png";
            }
            break;
          case "caver":
            if (icon_type === "m") {
              if (isConnected) {
                if (isReferent) {
                    image = "../images/icons/refcaver2_connected.png";
                } else {
                    image = "../images/icons/caver2_connected.png";
                }
              } else {
                if (isReferent) {
                    image = "../images/icons/refcaver2.png";
                } else {
                    image = "../images/icons/caver2.png";
                }
              }
            } else {
                image = "../images/icons/caver2_clust.png";
            }
            break;
          case "grotto":
            if (icon_type === "m") {
                image = "../images/icons/grotto1.png";
            } else {
                image = "../images/icons/grotto1_clust.png";
            }
            break;
      }
      return {image: image, shadow: shadow};
    }

    function setupLines(layer) {
      if (debug) {
        console.log("Entering : setupLines");
      }
      var i, cat1, cat2, id1, id2, pos1, pos2, color, line;
      for (i = 0; i < layer.length; i = i + 1) {
        cat1 = layer[i].s.category;
        cat2 = layer[i].e.category;
        id1 = layer[i].s.id;
        id2 = layer[i].e.id;
        pos1 = new google.maps.LatLng(layer[i].s.latitude, layer[i].s.longitude);
        pos2 = new google.maps.LatLng(layer[i].e.latitude, layer[i].e.longitude);
        color;
        switch (cat1 + cat2) {
        case "caverentry":
          color = "#0000ff";
          break;
        case "cavergrotto":
          color = "#00ff00";
          break;
        case "grottoentry":
          color = "#ff0000";
          break;
        }
        var line = createLine(pos1, pos2, cat1, cat2, id1, id2, color, 2, 0.5);
        allLines.push(line);
        if (getAbort("link")) {
          if (debug) {
            console.log("Abort - link - setupLines - step " + i);
          }
          //setAbort("link", false);
          break;
        }
      }
      addLines(allLines);
    }

    function createLine(posn1, posn2, cat1, cat2, id1, id2, color, weight, opacity) {
        var line;
        line = new google.maps.Polyline({
                path: [posn1, posn2],
                strokeColor: color,
                strokeOpacity: opacity,
                strokeWeight: weight,
                geodesic: true,
                cat1: cat1,
                cat2: cat2,
                id1: id1,
                id2: id2
        });
        google.maps.event.addListener(line, 'click', function () {
            lineOnClick(line);
        });
        google.maps.event.addListener(line, 'mouseover', function () {
            map.getDiv().className = "map_lines";
            map.getDiv().title = "<convert>#label=565<convert>";//Afficher l'objet lié
        });
        google.maps.event.addListener(line, 'mouseout', function () {
            map.getDiv().className = "map";
            map.getDiv().title = "";
        });
        return line;
    }

    function addLines(linesArray) {
      if (debug) {
        console.log("Entering : addLines");
      }


      //BS
      removeLines(linesArray);
      $.each(linesArray, function(index, value) {
          value.setMap(map);
          value.setVisible(false);//The synchroAfterLoad() function will show what needs to be shown considering the filter tree.
      });
      var existings = existingLines;
      existingLines = existings.concat(linesArray);
      //existingLines = existingLines.unique();
      switchLines(idForShownLines, typeForShownLines, true);
      synchroAfterLoad();
      return;
      //BS

      /*
      var i, existings, overlaysToAdd, addedOverlays;
      removeLines(linesArray);
      //Don't add all overlays! Don't add the ones that are in the intersection of the old array with the new one.
      existings = existingLines;
      overlaysToAdd = [];
      addedOverlays = [];
      overlaysToAdd = linesArray.diff(existings);
      for(i = 0; i < overlaysToAdd.length; i = i + 1) {
          overlaysToAdd[i].setMap(map);
          overlaysToAdd[i].setVisible(false); //The synchroAfterLoad() function will show what needs to be shown considering the filter tree.
          addedOverlays.push(overlaysToAdd[i]);
          if (getAbort("link")) {
              if (debug) {
                  console.log("Abort - link - addLines - step " + i);
              }
              //setAbort("link", false);
              break;
          }
      }
      switchLines(idForShownLines, typeForShownLines, true);
      existingLines = existings.concat(addedOverlays);
      existingLines = existingLines.unique(); //Line added on 10 sept. 09
      synchroAfterLoad();
      */
    }

    function removeLines(linesArray, removeAll) {
      if (debug) {
        console.log("Entering : removeLines");
      }

      //BS
      $.each(existingLines, function(index, value){
          value.setMap(null);
      });
      existingLines = [];
      return;
      //BS

      /*
      var i, existings, overlaysToRemove, removedOverlays;
      existings = existingLines;
      removedOverlays = [];
      if (removeAll) {
        overlaysToRemove = linesArray;
      } else {
        //Don't remove all the overlays! Just keep the intersection of the old array with the new one.
        overlaysToRemove = [];
        overlaysToRemove = existings.diff(linesArray);
      }
      for(i = 0; i < overlaysToRemove.length; i = i + 1) {
        overlaysToRemove[i].setMap(null);
        removedOverlays.push(overlaysToRemove[i]);
        if (getAbort("link")) {
          if (debug) {
            console.log("Abort - link - removeLines - step " + i);
          }
          //setAbort("link", false);
          break;
        }
      }
      //existingLines = existings.intersect(linesArray);
      existingLines = existings.diff(removedOverlays);
      */
    }

    function addMarkers(markersArray, category) {
      if (debug) {
        console.log("Entering : addMarkers");
      }

      //BS
      removeMarkers(markersArray, category);
      var existings = existingMarkers[category];
      existingMarkers[category] = existings.concat(markersArray);
      //existingMarkers[category] = existingMarkers[category].unique();
      synchroAfterLoad();
      return;
      //BS

      /*
      var i, existings, overlaysToAdd, addedOverlays;
      removeMarkers(markersArray, category);
      //Don't add all overlays! Don't add the ones that are in the intersection of the old array with the new one.
      existings = existingMarkers[category];
      overlaysToAdd = [];
      addedOverlays = [];
      overlaysToAdd = markersArray.diff(existings);
      for(i = 0; i < overlaysToAdd.length; i = i + 1) {
        addedOverlays.push(overlaysToAdd[i]);
        if (getAbort(category)) {
          if (debug) {
            console.log("Abort - " + category + " - addMarkers - step " + i);
          }
          //setAbort(category, false);
          break;
        }
      }
      existingMarkers[category] = existings.concat(addedOverlays);
      existingMarkers[category] = existingMarkers[category].unique(); //Line added on 10 sept. 09
      synchroAfterLoad();*/
    }

    function removeMarkers(markersArray, category, removeAll) {
      if (debug) {
        console.log("Entering : removeMarkers");
      }

      //BS
      $.each(existingMarkers[category], function(index, value){
          value.setMap(null);
      });
      existingMarkers[category] = [];
      return;
      //BS

      /*
      var i, existings, protects, overlaysToRemove, removedOverlays;
      existings = existingMarkers[category];
      protects = lockedMarkers[category];
      removedOverlays = [];
      if (removeAll) {
        overlaysToRemove = markersArray;
      } else {
        //Don't remove all the overlays! Just keep the intersection of the old array with the new one.
        overlaysToRemove = [];
        overlaysToRemove = existings.diff(markersArray);
        overlaysToRemove = overlaysToRemove.diff(protects);
      }
      for(i = 0; i < overlaysToRemove.length; i = i + 1) {
          overlaysToRemove[i].setMap(null);
        removedOverlays.push(overlaysToRemove[i]);
        if (getAbort(category)) {
          if (debug) {
            console.log("Abort - " + category + " - removeMarkers - step " + i);
          }
          //setAbort(category, false);
          break;
        }
      }
      //existingMarkers[category] = existings.intersect(markersArray);
      existingMarkers[category] = existings.diff(removedOverlays);
      existingMarkers[category] = existingMarkers[category].concat(protects);
      existingMarkers[category] = existingMarkers[category].unique();
      */
    }

    function synchroAfterLoad(passThrough) {
      if (debug) {
          console.log("Entering : synchroAfterLoad");
      }
      var callback;
      if (passThrough === undefined) {
          passThrough = false;
      }
      counterForAfterLoad = counterForAfterLoad - 1;
      /*if (getAbort("entry") || getAbort("caver") || getAbort("grotto") || getAbort("link")) {
        if (debug) {
          console.log("Abort - any - synchroAfterLoad");
        }
        counterForAfterLoad = 0;
      }*/
      if (counterForAfterLoad === 0 || passThrough || counterForAfterLoad < 0) {
        allMarkers = existingMarkers["entry"];
        allMarkers = allMarkers.concat(existingMarkers["caver"]);
        allMarkers = allMarkers.concat(existingMarkers["grotto"]);
        allLines = existingLines;
        setNearMeList();
        setVisibilityFilter();
        callback = getCallBackfunction();
        if (!strIsEmpty(callback)) {
          eval(callback);
          setCallBackfunction("");
        }
        stopBusy();
      }
    }

    function infoRadar() {
      var sMessage = "<convert>#label=326<convert>";
      popUpMsg(sMessage);
    }

    function getDirectionsForm(bFromThisPlace) {
      var innerLine, sValue, id, iwDOM;
			id = 'GC_IW_LOADING';
			iwDOM = xtdGetElementById(id);
			innerLine = "<form id=\"GC_IW_directions_form\" name=\"directions_form\" action=\"#\" onsubmit=\"mySite.details.openDirections(this.address.value, " + bFromThisPlace.toString() + ", '" + iwDOM.getAttribute('category') + "', '" + iwDOM.getAttribute('markerid') + "','<?php echo $_SESSION['language']; ?>'); return false\">";
      innerLine = innerLine + "<convert>#label=166<convert> : ";//Itinéraire
      if (bFromThisPlace) {
        innerLine = innerLine + "<a href=\"JavaScript:getDirectionsForm(false);\"><convert>#label=167<convert></a> - <b><convert>#label=168<convert></b><br />";//Vers ce lieu //Depuis ce lieu
      } else {
        innerLine = innerLine + "<b><convert>#label=167<convert></b> - <a href=\"JavaScript:getDirectionsForm(true);\"><convert>#label=168<convert></a><br />";//Vers ce lieu //Depuis ce lieu
      }
      sValue = "";
      if (document.directions_form) {
        sValue = document.directions_form.address.value;
      }
      innerLine = innerLine + "<input type=\"text\" size=\"25\" id=\"GC_IW_address\" name=\"address\" value=\"" + sValue + "\" /> <input name=\"submit\" type=\"submit\" class=\"button1\" value=\"<convert>#label=171<convert>\" />";//Ok
      innerLine = innerLine + "</form>";
      xtdGetElementById('GC_IW_directions').innerHTML = innerLine;
      xtdGetElementById('GC_IW_address').focus();
    }

    function setDirections(fromAddress, toAddress, locale) {
        var directionsRequest = {
                origin: fromAddress,
                destination: toAddress,
                provideRouteAlternatives: false,
                travelMode: google.maps.TravelMode.DRIVING,
                region: locale
        };
        directionsService.route(directionsRequest, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setMap(map);
                directionsDisplay.setDirections(result);
                $("#directions").show();
            }
        });
    }

    function clearDirections() {
      if (directionsDisplay) {
          directionsDisplay.setMap(null);
          $("#directions").hide();
      }
    }

    function initDirections() {
      if (!directionsService) {
        directionsService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer();
        directionsDisplay.setPanel(xtdGetElementById("directions"));
      }
    }

    function getMarkersByName(strName) {
      var markersArray, value, i, title;
      markersArray = [];
      value = strName.toLowerCase();
      for (i = 0; i < allMarkers.length; i = i + 1) {
        title = allMarkers[i].getTitle().toLowerCase();
        if (title.indexOf(value) !== -1) {
          markersArray.push(allMarkers[i]);
        }
      }
      return markersArray;
    }

    function getMarkersByName2(strName, forCavers, forEntries, forGrottoes) {
      var markersArray, value, i, title;
      markersArray = [];
      value = strName.toLowerCase();
      for (i = 0; i < allMarkers.length; i = i + 1) {
        title = "";
        if (allMarkers[i].category === "caver" && forCavers) {
          title = allMarkers[i].getTitle().toLowerCase();
          if (!strIsEmpty(allMarkers[i].cavername)) {
            title = title + " " + allMarkers[i].cavername.toLowerCase();
          }
          if (!strIsEmpty(allMarkers[i].caversurname)) {
            title = title + " " + allMarkers[i].caversurname.toLowerCase();
          }
        } else {
          if ((allMarkers[i].category === "entry" && forEntries) || (allMarkers[i].category === "grotto" && forGrottoes)) {
            title = allMarkers[i].getTitle().toLowerCase();
          }
        }
        if (title.indexOf(value) !== -1) {
          markersArray.push(allMarkers[i]);
        }
      }
      return markersArray;
    }

    function getMarkersOnCriteria(markers, sCaracteristic, sCriteria, sValue) {
      var markersArray, i;
      markersArray = [];
      for(i = 0; i < markers.length; i = i + 1) {
        switch (sCriteria) {
        case "==":
          if (markers[i][sCaracteristic] === sValue) {
            markersArray.push(markers[i]);
          }
          break;
        case ">=":
          if (markers[i][sCaracteristic] >= sValue) {
            markersArray.push(markers[i]);
          }
          break;
        case "<=":
          if (markers[i][sCaracteristic] <= sValue) {
            markersArray.push(markers[i]);
          }
          break;
        case ">":
          if (markers[i][sCaracteristic] > sValue) {
            markersArray.push(markers[i]);
          }
          break;
        case "<":
          if (markers[i][sCaracteristic] < sValue) {
            markersArray.push(markers[i]);
          }
          break;
        case "!=":
          if (markers[i][sCaracteristic] !== sValue) {
            markersArray.push(markers[i]);
          }
          break;
        }
        if (markers[i][sCaracteristic] === sValue) {
          markersArray.push(markers[i]);
        }
      }
      return markersArray;
    }

    function getMarkersByCategory(category) {
      var markersArray;
      markersArray = getMarkersOnCriteria(allMarkers, "category", "==", category);
      return markersArray;
    }

    function getMarkersByCountry(category, country) {
      var markersOfCategory, markersArray;
      markersOfCategory = getMarkersByCategory(category);
      markersArray = getMarkersOnCriteria(markersOfCategory, "country", "==", country);
      return markersArray;
    }

    function getMarkersByMassif(category, country, massif) {
      var markersOfCountry, markersArray;
      markersOfCountry = getMarkersByCountry(category, country);
      if (category === "entry") {
        markersArray = getMarkersOnCriteria(markersOfCountry, "massif", "==", massif);
        return markersArray;
      }
    }

    function getMarkersByCave(category, country, massif, cave) {
      var markersOfMassif, markersArray;
      markersOfMassif = getMarkersByMassif(category, country, massif);
      if (category === "entry") {
        markersArray = getMarkersOnCriteria(markersOfMassif, "cave", "==", cave);
        return markersArray;
      }
    }

    function getNewMarkers(dStartDate, dEndDate) {
      var markersArrayTemp, markersArray;
      markersArrayTemp = getMarkersOnCriteria(allMarkers, "inscriptionDate", ">=", dStartDate);
      markersArray = getMarkersOnCriteria(markersArrayTemp, "inscriptionDate", "<=", dEndDate);
      return markersArray;
    }

    function getReviewedMarkers(dStartDate, dEndDate) {
      var markersArrayTemp, markersArray;
      markersArrayTemp = getMarkersOnCriteria(allMarkers, "reviewedDate", ">=", dStartDate);
      markersArray = getMarkersOnCriteria(markersArrayTemp, "reviewedDate", "<=", dEndDate);
      return markersArray;
    }

    function showMarkers(markersArray, doSetFilter) {
      var i;
      if (doSetFilter === undefined) {
          doSetFilter = false;
      }
  		for(i = 0; i < markersArray.length; i = i + 1) {
  			if (markersArray[i]) {
                markersArray[i].setVisible(true);
    			if (doSetFilter) {
                    setFilterCheckbox(markersArray[i].category, markersArray[i].id, true);
                }
            }
  		}
    }

    function showMarker(markerId, markerCategory, doSetFilter) {
      var marker;
      if (doSetFilter === undefined) {
        doSetFilter = false;
      }
      marker = getMarker(markerId, markerCategory);
      marker.setVisible(true);
      if (doSetFilter) {
        setFilterCheckbox(marker.category, marker.id, true);
      }
    }

    function hideMarkers(markersArray, doSetFilter) {
        var i;
        if (doSetFilter === undefined) {
            doSetFilter = false;
        }
        GCinfoWindow.close();
        for (i = 0; i < markersArray.length; i = i + 1) {
        	markersArray[i].setVisible(false);
        	if (doSetFilter) {
                setFilterCheckbox(markersArray[i].category, markersArray[i].id, false);
            }
        }
    }

    function hideMarker(markerId, markerCategory, doSetFilter) {
      var marker;
      if (doSetFilter === undefined) {
        doSetFilter = false;
      }
      marker = getMarker(markerId, markerCategory);
      GCinfoWindow.close();
      marker.setVisible(false);
      if (doSetFilter) {
        setFilterCheckbox(marker.category, marker.id, false);
      }
    }

    function switchLines(id, category, status) {
      if (id !== 0) {
        if (getCategoryVisibility("link") !== true) {
          setCategoryVisibility("link", true);
          loadFromXML();
        }
    		if (status) {
    			hideLines();
    			showLines(id, category);
    			idForShownLines = id;
    			typeForShownLines = category;
    		} else {
    			hideLines();
    			idForShownLines = 0;
    			typeForShownLines = '';
    		}
      }
    }

    function overSwitchLines(id, category, status) {
      if (getCategoryVisibility("link") === true) {
        if (status) {
    		  showLines(id, category);
        } else {
    		  hideLines();
    		  switchLines(idForShownLines, typeForShownLines, true);
        }
      }
    }

    function hideLines() {
      var i;
      for (i = 0; i < allLines.length; i = i + 1) {
          allLines[i].setVisible(false);
      }
    }

    function showLines(id, category) {
      var markersArray, marker, linesArray, i;
      markersArray = [];
      linesArray = getLines(id, category);
      for (i = 0; i < linesArray.length; i = i + 1) {
          linesArray[i].setVisible(true);
          marker = getMarker(linesArray[i].id1, linesArray[i].cat1);
          markersArray.push(marker);
          marker = getMarker(linesArray[i].id2, linesArray[i].cat2);
          markersArray.push(marker);
      }
      showMarkers(markersArray, true);
    }

    function getLines(id, category) {
      var linesArray, i;
      linesArray = [];
      for (i = 0; i < allLines.length; i = i + 1) {
        if ((allLines[i].cat1 === category && parseInt(allLines[i].id1, 10) === parseInt(id, 10)) || (allLines[i].cat2 === category && parseInt(allLines[i].id2, 10) === parseInt(id, 10))) {
          linesArray.push(allLines[i]);
        }
      }
      return linesArray;
    }

    function getMarker(id, category) {
      var i;
      for (i = 0; i < allMarkers.length; i = i + 1) {
        if (allMarkers[i].objectId == id && allMarkers[i].category === category) {
          return allMarkers[i];
        }
      }
    }

    function fireInfowindow(id, category) {
      var marker, width, height, offset, connected, clustered, infoWindow;
      marker = getMarker(id, category);
      if (marker) {
        connected = "";
        if (category === "caver") {
          connected = '&connected=' + marker.isConnected.toString();
        }
        clustered = "&clustered=" + marker.markerType;
        //infoWindow = '<iframe src="' + category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered + '" frameborder="no" name="infowindow" id="infowindow" class="infoFrame" scrolling="no"></iframe>'
        infoURL = category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered;
		infoWindow = '<div class="infoFrame" id="GC_IW_LOADING" type="' + marker.markerType + '" category="' + category + '" markerid="' + id + '" url="' + infoURL + '"><p>Loading Content...</p></div>';
		GCinfoWindow.setContent(infoWindow);
		GCinfoWindowOpen(marker);
        mySite.openedInfoWindowId = id;
        mySite.openedInfoWindowType = category;
      }
    }

    function freeMarker(id, category) {
      var marker;
      marker = getMarker(id, category);
      if (marker) {
        marker.setDraggable(true);
        lockedMarkers[category].push(marker);
      } else {
        goCloseToMarker(id, category, "freeMarker(" + id + ", '" + category + "');");
      }
    }

    function blockMarker(id, category) {
      var marker;
      marker = lockedMarkers[category][lockedMarkers[category].length-1];//getMarker(id, category);
      if (marker) {
        marker.setDraggable(false);
        lockedMarkers[category].pop();
      }
    }

    function openMarker(id, category, doSetZoom, gLatLng, goClose) {
      var marker, callBack;
      isCluster = false;
      GCinfoWindow.close();
      marker = getMarker(id, category);
      if (!marker || goClose) {
        //The marker is clusterized
        //Defines the callBack operation
        callBack = "openMarker(" + id + ",'" + category + "'";
        if (doSetZoom != undefined) {
          callBack = callBack + ", " + doSetZoom.toString();
        }
        if (gLatLng != undefined) {
          callBack = callBack + ", '" + gLatLng.toString() + "'";
        }
        callBack = callBack + ");";
        //Go as close as possible to it
        goCloseToMarker(id, category, callBack);
      } else {
        if (gLatLng === undefined) {
          map.panTo(marker.getPosition());
        } else {
          map.panTo(gLatLng);
        }
        if (doSetZoom === undefined || doSetZoom === true) {
          map.setZoom(13);
        }
        fireInfowindow(id, category);
        setFilterCheckbox(category, id, true);
      }
    }

    function goCloseToMarker(id, category, callBack) {
      var sURL, latLngPlace;
      sURL = "webservices/getCoordsJson.php";
			t = $.ajax({type:'POST', url:sURL, async:false, cache:false, data:'ff=g'}).responseText;
			if(t.length<10) return alert("<?php echo MESSAGE_NOT_SENT; ?>"+t);
			$.cookie('<?php echo TOKEN_NAME; ?>',t);
			$.post(sURL, {ff: 'd', cat: category, id: id}, function(data){
				if (data.status == "OK") { //HERE
					latLngPlace = new google.maps.LatLng(data.content.wgs84[1], data.content.wgs84[0]);
					setCallBackfunction(callBack);
					setCategoryVisibility(category, true);
					setCategoryVisibility("advanced", false);

					var maxZoomService = new google.maps.MaxZoomService();
					maxZoomService.getMaxZoomAtLatLng(
					  latLngPlace,
					  function(response) {
					    if (response.status == google.maps.MaxZoomStatus.OK) {
					      map.setZoom(response.zoom);
					    } else {
					      console.log("Error in Max Zoom Service.");
					    }
					    map.panTo(latLngPlace);
					});
					//map.setCenter(latLngPlace);
				}
			}, 'json');
      //setMaxZoomCenter(latLngPlace);
    }

    /*function setMaxZoomCenter(latlng) {
      map.getCurrentMapType().getMaxZoomAtLatLng(latlng, function(response) {
        if (response && response['status'] == G_GEO_SUCCESS) {
          map.setCenter(latlng, response['zoom']);
        }
      });
    }*/

    function moveMarkerTo(id, category, lat, lng) {
      var marker;
      marker = getMarker(id, category);
      if (marker) {
        marker.setPosition(new google.maps.LatLng(lat, lng));
      }
    }

    function getCoordsByDirection(sDirection, callback) {
      if (sDirection != undefined) {
          geocoder.geocode({address: sDirection}, callback);
      }
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
          } else {
            myHtml = title + "<br />\n<b>" + addresses[0].formatted_address + "</b>";
          }
          GCinfoWindow.setContent(myHtml);
          if (marker) {
              GCinfoWindowOpen(marker);
          } else {
              GCinfoWindowOpen();
          }
        });
      }
    }

    function setMarkerUser(category, point) {
      var object, titleHere;
      object = createIconOptions(category, false, false, "m");
      titleHere = getTitleTemp();
      marker_user = new google.maps.Marker({
          position: point,
          map: map,
          title: titleHere,
          draggable: true,
          bouncy: true,
          icon:object.image
      });
      google.maps.event.addListener(marker_user, "dragstart", function () {
        GCinfoWindow.close();
      });
      google.maps.event.addListener(marker_user, "dragend", function (object) {
        openRGCInfoWindow(object.latLng, marker_user);
        if (mySite.filter.recieveLocation != undefined) {
          mySite.filter.recieveLocation(object.latLng.lat(), object.latLng.lng());
        }
      });
      google.maps.event.addListener(marker_user, "click", function (object) {
        openRGCInfoWindow(object.latLng, marker_user);
      });
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

    function removeAddress() {
      if (marker_user != undefined) {
        marker_user.setMap(null);
        marker_user = undefined;
      }
    }

    function setupConvertMarker(lat, lng) {
      var infoWindow, point, titleHere;
      point = new google.maps.LatLng(lat, lng);
      resetConvertMarker();
      titleHere = getTitleTemp();
      marker_converter = new google.maps.Marker({position: point, map: map, title: titleHere, draggable: true, bouncy: true});
      converter_handler = google.maps.event.addListener(marker_converter, "dragend", function (object) {
        convertFromWGS84(marker_converter.getPosition().lat(), marker_converter.getPosition().lng());
      });
      google.maps.event.addListener(marker_converter, 'click', function (object) {
        if (!drawMode) {
          openRGCInfoWindow(object.latLng, marker_converter);
        }
      });
      google.maps.event.addListener(marker_converter, "dragstart", function () {
          GCinfoWindow.close();
      });
      openRGCInfoWindow(marker_converter.getPosition(), marker_converter);
    }

    function resetConvertMarker() {
      if (marker_converter) {
        if (converter_handler) {
          google.maps.event.removeListener(converter_handler);
        }
        marker_converter.setMap(null);
        marker_converter = undefined;
      }
    }

    function setupElevationMarker(lat, lng) {
      var infoWindow, point, titleHere, altUrl, infoWindow;
      altUrl = "altitude_<?php echo $_SESSION['language']; ?>.php?lat=" + lat + "&lng=" + lng;
      //infoWindow = '<iframe src="' + altUrl + '" frameborder="no" name="infowindow" id="infowindow" class="infoFrame" scrolling="auto"></iframe>';
			infoWindow = '<div class="infoFrame" id="GC_IW_LOADING" url="' + altUrl + '"><p>Loading Content...</p></div>';
      point = new google.maps.LatLng(lat, lng);
      resetElevationMarker();
      titleHere = getTitleTemp();
      marker_elevation = new google.maps.Marker({position: point, map:map, title: titleHere, draggable: true, bouncy: true});
      elevation_handler = google.maps.event.addListener(marker_elevation, "dragend", function (object) {
        setupElevationMarker(object.latLng.lat(), object.latLng.lng());
      });
      google.maps.event.addListener(marker_elevation, 'click', function (object) {
        if (!drawMode) {
            GCinfoWindow.setContent(infoWindow);
            GCinfoWindowOpen(marker_elevation);
        }
      });
      google.maps.event.addListener(marker_elevation, "dragstart", function () {
          GCinfoWindow.close();
      });
      GCinfoWindow.setContent(infoWindow);
      GCinfoWindowOpen(marker_elevation);
    }

    function resetElevationMarker() {
      if (marker_elevation) {
        if (elevation_handler) {
          google.maps.event.removeListener(elevation_handler);
        }
        marker_elevation.setMap(null);
        marker_elevation = undefined;
      }
    }

		function goToDefaultPosition(lat, lng, zoom) {
		  var mapMove_handler;
      mapMove_handler = google.maps.event.addListener(map, "moveend", function () {
        google.maps.event.removeListener(mapMove_handler);
        saveDefaultPosition(); //My function
      });
  		map.panTo(new google.maps.LatLng(lat, lng));
  		map.setZoom(zoom);
    }

    function getClientLatLng() {
      if (google.loader.ClientLocation) {
        return new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
      } else {
        return undefined;
      }
    }

    function refreshOverview() {
      eval("self.location.href = 'overview_<?php echo $_SESSION['language']; ?>.php';");
    }

    function startBusy() {
      showId('waitingSign');
      /*if (map) {
        map.disableDragging();
        map.disableDoubleClickZoom();
        map.disableScrollWheelZoom();
      }*/
    }

    function stopBusy() {
      hideId('waitingSign');
      /*if (map) {
        map.enableDragging();
        map.enableDoubleClickZoom();
        map.enableScrollWheelZoom();
      }*/
    }

    function setCallBackfunction(sValue) {
      callBackFunction = sValue;
    }

    function getCallBackfunction() {
      return callBackFunction;
    }

    function containerResized() {
      //reload();
      loadFromXML();
    }

    function loadContext() {
      /* Debug var */
			debug = false;
      /*Load libs*/
      load_GCMap();
      /*Start of global vars definition*/
      isLoaded = false;
      lineStarted = false;
      drawMode = false;
      mousePosnIsFrozen = false;
      mouseLatLng = new google.maps.LatLng(0,0);
      doResetEnvironement = true;
      userConnected = false;
      counterForAfterLoad = 0;
      callBackFunction = "";
      clusteringLimit = getUserClusterLimit();
      categoryVisibility = {"entry": <?php echo $entry_filter; ?>, "caver": <?php echo $caver_filter; ?>, "grotto": <?php echo $grotto_filter; ?>, "link": doShowLinksOnHover(), "advanced": <?php echo $advanced_filter; ?>};
      existingMarkers = {"entry": [], "caver": [], "grotto": []};
      lockedMarkers = {"entry": [], "caver": [], "grotto": []};
      doAbort = {"entry": false, "caver": false, "grotto": false, "link": false};
      existingLines = [];
      /*End of global vars definition*/
      startBusy();
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      loadMap();
      resetVars();
      userConnected = isUserConnected();
      google.maps.event.addListener(map, 'idle', function() {
          if (!isInfoWindowOpen() && !dragstarted) {
              loadMarkers();
          }
       });

      isLoaded = true;
      //Hide the checkboxes
      //.blur();
    }

    document.onkeyup=manageKey;
    //BS google.setOnLoadCallback(loadContext, true);
    google.maps.event.addDomListener(window, 'load', loadContext);


    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body><!-- onload="JavaScript:toggle();" onresize="JavaScript:containerResized();"-->
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
	<div style="height:100%;width:100%;">
      <div id="map" class="map"></div>
      <div id="waitingSign"><div class="caption" style="cursor:pointer;" onclick="JavaScript:abortLoading();"><convert>#label=408<convert><!--Chargement en cours...<br />Appuyez sur ECHAPPE ou cliquez ici pour annuler.--></div></div>
    </div>
    <div id="reloadContainer" style="display:none;visibility:hidden;">
      <span title="<convert>#label=56<convert>">
        <convert>#label=56<convert><!--Rafraîchir-->
      </span>
    </div>
    <div id="printerContainer" style="display:none;visibility:hidden;">
      <a title="<convert>#label=525<convert>" class="nothing" style="display:block;float:right;text-align:right;margin-right:5px;">
        <img src="../images/icons/printer.png" alt="<convert>#label=525<convert>" style="border:0px none;height:19px" title="<convert>#label=525<convert>" />
      </a>
    </div>
    <div id="directions" style="display:none;background-color: #FFFFFF;height: 100%;overflow: scroll;position: absolute;right: 0px; top: 30px; width: 25%;">
        <?php echo getCloseBtn("JavaScript:clearDirections();","<convert>#label=371<convert>")?>
    </div>
    <div id="clip">
    </div>
    <div id="searchPanel">
      <input id="searchBox" type="text" placeholder="<convert>#label=487<convert>">
    </div>
<?php
    $virtual_page = "overview/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
