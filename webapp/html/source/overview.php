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
 * @copyright Copyright (c) 2009-1912 Cl�ment Ronzon
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
		<link rel="stylesheet" type="text/css" href="../css/infowindow.css" />
		<link rel="stylesheet" type="text/css" href="../css/contextualMenu.css" />
    <style type="txt/css">
      v\:* {behavior:url(#default#VML);}
    </style>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>"></script>
    <script type="text/javascript" src="../scripts/gmap-wms.js"></script>
    <script type="text/javascript" src="../scripts/dragzoom.js"></script>
    <script type="text/javascript" src="../scripts/GCMap.js"></script>
    <script type="text/javascript" src="../scripts/GMOpacity.js"></script>
    <script type="text/javascript" src="../scripts/GCControls.js"></script>
    <script type="text/javascript" src="../scripts/contextmenucontrol.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need functions: getTargetNode, convertMousePsn, copySelectedCoords, detailMarker, editMarker, deleteMarker, addMarker, xtdGetElementById,
		//											showRelationList
    var isLoaded, jg, map, geocoder, gdir, measurementLine, is_over_measurement_line, lineStarted, measurement_handler_updated,
        measurement_handler_over, measurement_handler_out, converter_handler, drawMode, posn1, marker_user, marker_converter,
        allMarkers, allLines, idForShownLines, typeForShownLines, caversLayer, entriesLayer, grottosLayer, linksLayer, mousePosnIsFrozen,
        mouseLatLng, doResetEnvironement, userConnected, counterForAfterLoad, callBackFunction, clusteringLimit, categoryVisibility,
        existingMarkers, lockedMarkers, existingLines, doAbort, debug, mapControl, WMS, BGForWMS, LAYERS, layersOpacity, marker_elevation,
        elevation_handler;//, clip;
    
    function loadMap() {
      var latLng, dragzoomOpts, gLatLng, basicZoom, defaultZoom, options;
      basicZoom = 4;
      if (google.maps.BrowserIsCompatible()) {
        //Create the map
        map = new google.maps.Map2(xtdGetElementById("map"));
        //map = new google.maps.Map(xtdGetElementById("map"));
        //Create the geocoder
        geocoder = new google.maps.ClientGeocoder();
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
        google.maps.Event.addListener(map, 'mousemove', function (latlng) {
          mapOnMouseMove(latlng);
        });
        google.maps.Event.addListener(map, 'click', function (overlay, latlng) {
          mapOnLeftClick(overlay, latlng);
        });
        google.maps.Event.addListener(map, 'infowindowopen', mapOnInfowindowOpen);
        //Add layers
    		map.addMapType(G_PHYSICAL_MAP);
    		map.addMapType(G_SATELLITE_3D_MAP);
    		layersOpacity = 0.4;
    		loadExtraLayers(layersOpacity);
  			//Set the default position on the map
        map.setCenter(gLatLng, defaultZoom);
        map.setMapType(G_PHYSICAL_MAP);
        //Add controls
  			map.addControl(new google.maps.LargeMapControl3D());
  			// /!\ WARNING: GENERATES A ERROR ON 3D MAP TYPE:
//        map.addControl(new google.maps.OverviewMapControl());
        // /!\ END OF WARNING.
  			map.addControl(new google.maps.ScaleControl());
  			//Opacity control
  			map.addControl(new OpacityControl('<convert>#label=776<convert>')); //Transparence
  			//Contextual Menu Control
  			options = {dirsFrom: {enabled: false, label: "A"},
                   dirsTo: {enabled: false, label: "B"},
                   zoomIn: {enabled: false, label: "C"},
                   zoomOut: {enabled: false, label: "D"},
                   centerMap: {enabled: false, label: "E"},
                   whatsHere: {enabled: false, label: "F"},
                   refreshData: {enabled: true, label: "<convert>#label=56<convert>"},
                   copyLatLng: {enabled: true, label: "<convert>#label=401<convert>"},
                   convertLatLng: {enabled: true, label: "<convert>#label=403<convert>"},
                   elevationHere: {enabled: true, label: "<convert>#label=489<convert>"},
                   properties: {enabled: false, label: "<convert>#label=179<convert>"},
                   details: {enabled: false, label: "<convert>#label=185<convert>"},
                   editMe: {enabled: false, label: "<convert>#label=53<convert>"},
                   deleteMe: {enabled: false, label: "<convert>#label=55<convert>"},
                   addEntry: {enabled: false, label: "<convert>#label=404<convert>"},
                   addGrotto: {enabled: false, label: "<convert>#label=406<convert>"}};
<?php if (allowAccess(entry_edit_all)) { ?>
        options.addEntry.enabled = true;
<?php }
      if (allowAccess(grotto_edit_all)) { ?>
        options.addGrotto.enabled = true;
<?php } ?>
  			map.addControl(new ContextMenuControl('<?php echo $_SESSION['language']; ?>', options));
  			//Refresh control
  			map.addControl(new GCControls());
  			//bind a dragzoom control to the map
  			dragzoomOpts = {buttonStartingStyle: {background: '#FFFFFF', paddingTop: '0px', paddingLeft: '0px', border:'0px none'},
                        buttonHTML: '<img title="<convert>#label=666<convert>" src="../images/icons/zoomin.gif" alt="[+]" />',//Drag Zoom In
                        buttonStyle: {width:'23px', height:'21px'},
                        buttonZoomingHTML: '<convert>#label=667<convert>',//Drag a region on the map (click here to reset)
                        buttonZoomingStyle: {background:'#F3F3E9',width:'75px', height:'100%', color:'#34558A', border:'1px solid black'},
                        backButtonHTML: '<img title="<convert>#label=668<convert>" src="../images/icons/zoomout.gif" alt="[-]" />',//Zoom Back Out  
                        backButtonStyle: {display:'none',marginTop:'2px',width:'23px', height:'21px'},
                        backButtonEnabled: true, 
                        overlayRemoveTime: 1000};
        map.addControl(new DragZoomControl({border:'2px solid #34558A'}, dragzoomOpts, {}), new google.maps.ControlPosition(G_ANCHOR_TOP_LEFT, new google.maps.Size(73,7)));
  			// bind a search control to the map, suppress result list
        map.addControl(new google.elements.LocalSearch({"searchFormHint": "<convert>#label=487<convert>"}), new google.maps.ControlPosition(G_ANCHOR_BOTTOM_LEFT, new google.maps.Size(10, 35)));//Recherche google
        setControlRelationship();
  			map.enableDoubleClickZoom();
  			map.enableScrollWheelZoom();
  			map.enableContinuousZoom();
  			map.enableRotation();
        google.maps.Event.addListener(map, 'moveend', loadFromXML);
        google.maps.Event.addListener(map, 'viewchangeend', loadFromXML);
        setGDir();
      }
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
      currentMapType = map.getCurrentMapType();
      map.setMapType(G_NORMAL_MAP);
      map.setMapType(currentMapType);
    }
    
    function setControlRelationship() {
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
      mapControl.addRelationship(G_SATELLITE_MAP, G_HYBRID_MAP, undefined, true);
      map.addControl(mapControl);
    }
    
    function loadExtraLayers(opacity) {
      LAYERS = new Object();
      //Set up BGForWMS list
      BGForWMS = [G_NORMAL_MAP, G_SATELLITE_MAP, G_PHYSICAL_MAP];
      //Set up WMS
      WMS = {<?php echo getOverviewLayers(true); ?>};
//service=WMS&version=1.1.1&request=GetCapabilities&
			for(var layers in WMS) {
        var layersArray = [];
        for(var layer in WMS[layers]["SPECS"]) {
          layersArray.push(createWMSSpec(WMS[layers]["SPECS"][layer]["URL"],
                                        WMS[layers]["SPECS"][layer]["NAME"],
                                        WMS[layers]["SPECS"][layer]["SHORTNAME"],
                                        WMS[layers]["SPECS"][layer]["LAYER"],
                                        WMS[layers]["SPECS"][layer]["STYLE"],
                                        WMS[layers]["SPECS"][layer]["FORMAT"],
                                        WMS[layers]["SPECS"][layer]["VERSION"],
                                        WMS[layers]["SPECS"][layer]["BGCOLOR"]));
        }
        for(var bg = 0; bg < BGForWMS.length; bg = bg + 1) {
          LAYERS[layers + bg] = createWMSOverlaySpec(BGForWMS[bg],
                                              layersArray,
                                              WMS[layers]["NAME"] + " - " + bg,
                                              WMS[layers]["SHORTNAME"],
                                              WMS[layers]["OPACITY"]);
        }
      }
  		for(var layer in LAYERS) {
        map.addMapType(LAYERS[layer]);
      }
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
    
    function mapOnMouseMove(latlng) {
      mouseLatLng = latlng;
      if (!mousePosnIsFrozen) {
        setMouseInputs(latlng);
        //getSrtm3(latlng.lat(), latlng.lng());
      }
      if (drawMode && lineStarted) {
        drawMeasurementLine(undefined, latlng, false);
      }
    }
    
    function showMouseElevation(latLng) {
      setupElevationMarker(latLng.lat, latLng.lng);
    }
    
    function mapOnLeftClick(overlay, latlng) {
      if (debug) {
				google.maps.Log.write("Click!");
			}
			if (drawMode) {
        drawMeasurementLine(overlay, latlng, true);
      }
      if (elevation_handler) {
        resetElevationMarker();
      }
      if (converter_handler) {
        mySite.details.switchConverter(false, 'converter_menu');
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
    
    function pickAPlace(callBack) {
      var handler = google.maps.Event.addListener(map, 'click', function (overlay, latlng) {
        if (handler) {
          google.maps.Event.removeListener(handler);
        }
        callBack(overlay,latlng);
      });
    }
    
    function drawMeasurementLine(overlay, latlng, doEndMeasurement) {
      var posn, mouseLatLng;
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
        map.addOverlay(measurementLine);
        if (doEndMeasurement) {
          lineStarted = false;
          measurement_handler_updated = google.maps.Event.addListener(measurementLine, "lineupdated", displayMeasurement);
          measurement_handler_over = google.maps.Event.addListener(measurementLine, "mouseover", measurementLineOver);
          measurement_handler_out = google.maps.Event.addListener(measurementLine, "mouseout", measurementLineOut);
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
        measurementLine.enableEditing();
      }
    }
    
    function disableEditingMeasurement() {
      if (!is_over_measurement_line && measurementLine) {
        measurementLine.disableEditing();
      }
    }
    
    function clearMeasurementLine(doResetDisplay) {
      if (measurementLine) {
        if (measurement_handler_updated) {
          google.maps.Event.removeListener(measurement_handler_updated);
        }
        if (measurement_handler_over) {
          google.maps.Event.removeListener(measurement_handler_over);
        }
        if (measurement_handler_out) {
          google.maps.Event.removeListener(measurement_handler_out);
        }
        measurementLine.disableEditing();
        map.removeOverlay(measurementLine);
        measurementLine = undefined;
        if (doResetDisplay) {
          resetMeasurementDisplay();
        }
      }
    }
    
    function displayMeasurement() {
      if (measurementLine) {
        mySite.banner.document.measurement.measure.style.color = "black";
        mySite.banner.document.measurement.measure.value = Math.round(measurementLine.getLength() * 10) / 10;
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
          google.maps.Log.write("entry - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
				postObj = getAjaxObj("entry");
        google.maps.DownloadUrl(postObj.url, setupEntriesLayer, postObj.data);
      }
      if (getCategoryVisibility("caver") ===  true) {
        if (debug) {
          google.maps.Log.write("caver - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
				postObj = getAjaxObj("caver");
        google.maps.DownloadUrl(postObj.url, setupCaversLayer, postObj.data);
      }
      if (getCategoryVisibility("grotto") ===  true) {
        if (debug) {
          google.maps.Log.write("grotto - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
				postObj = getAjaxObj("grotto");
        google.maps.DownloadUrl(postObj.url, setupGrottosLayer, postObj.data);
      }
      if (getCategoryVisibility("link") ===  true) {
        if (debug) {
          google.maps.Log.write("link - loadVars");
        }
        counterForAfterLoad = counterForAfterLoad + 1;
				postObj = getAjaxObj("link");
        google.maps.DownloadUrl(postObj.url, setupLinksLayer, postObj.data);
      }
      if (counterForAfterLoad === 0 || counterForAfterLoad < 0) {
        if (debug) {
          google.maps.Log.write("0 - loadVars");
        }
        setCallBackfunction ("");
        synchroAfterLoad(true);
      }
    }
    
    function getAjaxObj(sCategory) {
      var bounds, southWest, northEast, isNotMaxZoom, getVars, stdArray, i, url, data;
    	/* Code from http://googlemapsbook.com/chapter7/ */          	
      //create the boundary for the data to provide
    	//initial filtering
    	bounds = map.getBounds();
    	southWest = bounds.getSouthWest();
    	northEast = bounds.getNorthEast();
    	isNotMaxZoom = (map.getZoom() !== map.getCurrentMapType().getMaximumResolution());
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
        google.maps.Log.write("Start Aborting");
      }
      setAbort("entry", true);
      setAbort("caver", true);
      setAbort("grotto", true);
      setAbort("link", true);
      //synchroAfterLoad(true);
    }
    
    function cancelAbortLoading() {
      if (debug) {
        google.maps.Log.write("Cancel Aborting");
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
      if (debug) {
        google.maps.Log.write("Entering : setupEntriesLayer");
      }
      var xmlNode, xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlNode = google.maps.Xml.parse(xmlData);
        xmlElement = xmlNode.documentElement.getElementsByTagName("marker");
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
              google.maps.Log.write("Abort - entry - SetupEntriesLayer - step " + i);
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
      if (debug) {
        google.maps.Log.write("Entering : setupCaversLayer");
      }
      var xmlNode, xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlNode = google.maps.Xml.parse(xmlData);
        xmlElement = xmlNode.documentElement.getElementsByTagName("marker");
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
              google.maps.Log.write("Abort - caver - setupCaversLayer - step " + i);
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
      if (debug) {
        google.maps.Log.write("Entering : setupGrottosLayer");
      }
      var xmlNode, xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlNode = google.maps.Xml.parse(xmlData);
        xmlElement = xmlNode.documentElement.getElementsByTagName("marker");
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
              google.maps.Log.write("Abort - grotto - setupGrottosLayer - step " + i);
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
      if (debug) {
        google.maps.Log.write("Entering : setupLinksLayer");
      }
      var xmlNode, xmlElement, index, i;
      if (httpResponseStatusCode !== -1) {
        xmlNode = google.maps.Xml.parse(xmlData);
        xmlElement = xmlNode.documentElement.getElementsByTagName("line");
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
              google.maps.Log.write("Abort - link - setupLinksLayer - step " + i);
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
        google.maps.Log.write("Entering : setupMarkers");
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
            google.maps.Log.write("Abort - " + category + " - setupMarkers - step " + i);
          }
          //setAbort(category, false);
          break;
        }
      }
      addMarkers(myMarkers, category);
    }
  
    function createMarker(posn, category, id, address, city, region, country, massifId, caveId, name, caverName, caverSurname, caverLogin, inscriptionDate, reviewedDate, isConnected, isReferent, icon_type) {
      var tinyIcon, options, marker, connected, clustered, infoURL, infoWindow, loadingHTMLMsg;
      tinyIcon = createIcon(category, isConnected, isReferent, icon_type);
      options = {
                    "draggable": true,
                    "id": id,
                    "category": category,
                    "title": name,
                    "cavername": caverName,
                    "caversurname": caverSurname,
                    "caverlogin": caverLogin,
                    "draggable": true,
                    "icon": tinyIcon,
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
      marker = new GCMarker(posn, options);
      connected = "";
      if (category === "caver") {
        connected = '&connected=' + isConnected.toString();
      }
      clustered = "&clustered=" + icon_type;
      //infoWindow = '<iframe src="' + category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered + '" frameborder="no" name="infowindow" id="infowindow" class="infoFrame" scrolling="no"></iframe>';
			infoURL = category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered;
			loadingHTMLMsg = '<div style="position:absolute;top:50%;left:50%;"><div style="width:350px;height:16px;text-align:center;vertical-align:middle;position:absolute;top:-8px;left:-175px;font-weight:bold;"><convert>#label=890<convert></div></div>'; //Chargement en cours...
			infoWindow = '<div class="infoFrame" id="GC_IW_LOADING" type="' + icon_type + '" category="' + category + '" markerid="' + id + '" url="' + infoURL + '">' + loadingHTMLMsg + '</div>';
      marker.disableDragging();
      google.maps.Event.addListener(marker, 'click', function () {
        if (!drawMode) {
					marker.openInfoWindowHtml(infoWindow);
        }
      });
			
      if (icon_type === "m") {
        google.maps.Event.addListener(marker, 'mouseover', function () {
          overSwitchLines(id, category, true);
        });
        google.maps.Event.addListener(marker, 'mouseout', function () {
          overSwitchLines(id, category, false);
        });
        google.maps.Event.addListener(marker, "dragstart", function () {
          map.closeInfoWindow();
        });
        google.maps.Event.addListener(marker, "dragend", function () {
          marker.openInfoWindowHtml(infoWindow);
          if (mySite.filter.recieveLocation != undefined) {
            mySite.filter.recieveLocation(marker.getLatLng().lat(), marker.getLatLng().lng());
          }
        });
        google.maps.Event.addListener(marker, "infowindowopen", function () {
          mySite.openedInfoWindowId = id;
          mySite.openedInfoWindowType = category;
        });
      }
      return marker;
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
				if (idForShownLines == iwDOM.getAttribute('markerid') && typeForShownLines == iwDOM.getAttribute('category')) {
					xtdGetElementById('GC_IW_link').checked = true;
				}
			}
		}
		
		function infowindowReload(oButton) {
			mapOnInfowindowOpen(oButton.getAttribute('name'));
		}
		
    function createIcon(category, isConnected, isReferent, icon_type) {
      var tinyIcon;
      if (isConnected === undefined || isConnected === "false") {
        isConnected = false;
      }
      if (isReferent === undefined || isReferent === "false") {
        isReferent = false;
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
            if (isReferent) {
              tinyIcon.image = "../images/icons/refcaver2_connected.png";
            } else {
              tinyIcon.image = "../images/icons/caver2_connected.png";
            }
          } else {
            if (isReferent) {
              tinyIcon.image = "../images/icons/refcaver2.png";
            } else {
              tinyIcon.image = "../images/icons/caver2.png";
            }
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
    
    function setupLines(layer) {
      if (debug) {
        google.maps.Log.write("Entering : setupLines");
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
            google.maps.Log.write("Abort - link - setupLines - step " + i);
          }
          //setAbort("link", false);
          break;
        }
      }
      addLines(allLines);
    }
    
    function createLine(posn1, posn2, cat1, cat2, id1, id2, color, weight, opacity) {
      var options, line;
      options = {"cat1":cat1,
                    "cat2":cat2,
                    "id1":id1,
                    "id2":id2,
                    "geodesic":true};
      line = new GCPolyline([posn1, posn2], color, weight, opacity, options);
      google.maps.Event.addListener(line, 'click', function () {
        lineOnClick(line);
      });
      google.maps.Event.addListener(line, 'mouseover', function () {
        map.getContainer().className = "map_lines";
        map.getContainer().title = "<convert>#label=565<convert>";//Afficher l'objet lié
      });
      google.maps.Event.addListener(line, 'mouseout', function () {
        map.getContainer().className = "map";
        map.getContainer().title = "";
      });
      return line;
    }
    
    function addLines(linesArray) {
      if (debug) {
        google.maps.Log.write("Entering : addLines");
      }
      var i, existings, overlaysToAdd, addedOverlays;
      removeLines(linesArray);
      //Don't add all overlays! Don't add the ones that are in the intersection of the old array with the new one.
      existings = existingLines;
      overlaysToAdd = [];
      addedOverlays = [];
      overlaysToAdd = linesArray.diff(existings);
      for(i = 0; i < overlaysToAdd.length; i = i + 1) {
        map.addOverlay(overlaysToAdd[i]);
        overlaysToAdd[i].hide(); //The synchroAfterLoad() function will show what needs to be shown considering the filter tree.
        addedOverlays.push(overlaysToAdd[i]);
        if (getAbort("link")) {
          if (debug) {
            google.maps.Log.write("Abort - link - addLines - step " + i);
          }
          //setAbort("link", false);
          break;
        }
      }
      switchLines(idForShownLines, typeForShownLines, true);
      existingLines = existings.concat(addedOverlays);
      existingLines = existingLines.unique(); //Line added on 10 sept. 09
      synchroAfterLoad();
    }
    
    function removeLines(linesArray, removeAll) {
      if (debug) {
        google.maps.Log.write("Entering : removeLines");
      }
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
        map.removeOverlay(overlaysToRemove[i]);
        removedOverlays.push(overlaysToRemove[i]);
        if (getAbort("link")) {
          if (debug) {
            google.maps.Log.write("Abort - link - removeLines - step " + i);
          }
          //setAbort("link", false);
          break;
        }
      }
      //existingLines = existings.intersect(linesArray);
      existingLines = existings.diff(removedOverlays);
    }
    
    function addMarkers(markersArray, category) {
      if (debug) {
        google.maps.Log.write("Entering : addMarkers");
      }
      var i, existings, overlaysToAdd, addedOverlays;
      removeMarkers(markersArray, category);
      //Don't add all overlays! Don't add the ones that are in the intersection of the old array with the new one.
      existings = existingMarkers[category];
      overlaysToAdd = [];
      addedOverlays = [];
      overlaysToAdd = markersArray.diff(existings);
      for(i = 0; i < overlaysToAdd.length; i = i + 1) {
        map.addOverlay(overlaysToAdd[i]);
        addedOverlays.push(overlaysToAdd[i]);
        if (getAbort(category)) {
          if (debug) {
            google.maps.Log.write("Abort - " + category + " - addMarkers - step " + i);
          }
          //setAbort(category, false);
          break;
        }
      }
      existingMarkers[category] = existings.concat(addedOverlays);
      existingMarkers[category] = existingMarkers[category].unique(); //Line added on 10 sept. 09
      synchroAfterLoad();
    }
    
    function removeMarkers(markersArray, category, removeAll) {
      if (debug) {
        google.maps.Log.write("Entering : removeMarkers");
      }
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
        map.removeOverlay(overlaysToRemove[i]);
        removedOverlays.push(overlaysToRemove[i]);
        if (getAbort(category)) {
          if (debug) {
            google.maps.Log.write("Abort - " + category + " - removeMarkers - step " + i);
          }
          //setAbort(category, false);
          break;
        }
      }
      //existingMarkers[category] = existings.intersect(markersArray);
      existingMarkers[category] = existings.diff(removedOverlays);
      existingMarkers[category] = existingMarkers[category].concat(protects);
      existingMarkers[category] = existingMarkers[category].unique();
    }
    
    function synchroAfterLoad(passThrough) {
      if (debug) {
        google.maps.Log.write("Entering : synchroAfterLoad");
      }
      var callback;
      if (passThrough === undefined) {
        passThrough = false;
      }
      counterForAfterLoad = counterForAfterLoad - 1;
      /*if (getAbort("entry") || getAbort("caver") || getAbort("grotto") || getAbort("link")) {
        if (debug) {
          google.maps.Log.write("Abort - any - synchroAfterLoad");
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
      if (google.maps.BrowserIsCompatible()) {
        gdir.load("from: " + fromAddress + " to: " + toAddress, {"locale": locale});
      }
    }
    
    function displayDirections() {
      mySite.details.xtdGetElementById("directions").innerHTML = xtdGetElementById("directions").innerHTML;
      mySite.details.showId('directions_div');
    }
    
    function clearGDir() {
      if (gdir) {
        gdir.clear();
      }
    }
    
    function setGDir() {
      var div_container;
      if (!gdir) {
        div_container = xtdGetElementById("directions");
        gdir = new google.maps.Directions(map, div_container);
        google.maps.Event.addListener(gdir, "error", handleErrors);
        google.maps.Event.addListener(gdir, "addoverlay", displayDirections);
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
          markersArray[i].show();
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
      marker.show();
      if (doSetFilter) {
        setFilterCheckbox(marker.category, marker.id, true);
      }
    }
    
    function hideMarkers(markersArray, doSetFilter) {
      var i;
      if (doSetFilter === undefined) {
        doSetFilter = false;
      }
  		for(i = 0; i < markersArray.length; i = i + 1) {
  		  markersArray[i].closeInfoWindow();
  			markersArray[i].hide();
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
      marker.closeInfoWindow();
      marker.hide();
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
        allLines[i].hide();
      }
    }
    
    function showLines(id, category) {
      var markersArray, marker, linesArray, i;
      markersArray = [];
      linesArray = getLines(id, category);
      for (i = 0; i < linesArray.length; i = i + 1) {
        linesArray[i].show();
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
        width = marker.getIcon().infoWindowAnchor.x - marker.getIcon().iconAnchor.x;
        height = marker.getIcon().infoWindowAnchor.y - marker.getIcon().iconAnchor.y;
        offset = new google.maps.Size(width, height);
        connected = "";
        if (category === "caver") {
          connected = '&connected=' + marker.isConnected.toString();
        }
        clustered = "&clustered=" + marker.markerType;
        //infoWindow = '<iframe src="' + category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered + '" frameborder="no" name="infowindow" id="infowindow" class="infoFrame" scrolling="no"></iframe>'
        infoURL = category + 'Infowindow_<?php echo $_SESSION['language']; ?>.php?id=' + id + connected + clustered;
				infoWindow = '<div class="infoFrame" id="GC_IW_LOADING" type="' + marker.markerType + '" category="' + category + '" markerid="' + id + '" url="' + infoURL + '"><p>Loading Content...</p></div>';
				map.openInfoWindowHtml(marker.getLatLng(), infoWindow, {pixelOffset:offset}); 
        mySite.openedInfoWindowId = id;
        mySite.openedInfoWindowType = category;
      } 
    }

    function freeMarker(id, category) {
      var marker;
      marker = getMarker(id, category);
      if (marker) {
        marker.enableDragging();
        lockedMarkers[category].push(marker);
      } else {
        goCloseToMarker(id, category, "freeMarker(" + id + ", '" + category + "');");
      }
    }
    
    function blockMarker(id, category) {
      var marker;
      marker = lockedMarkers[category][lockedMarkers[category].length-1];//getMarker(id, category);
      if (marker) {
        marker.disableDragging();
        lockedMarkers[category].pop();
      }
    }

    function openMarker(id, category, doSetZoom, gLatLng, goClose) {
      var marker, callBack;
      isCluster = false;
      map.closeInfoWindow();
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
          map.panTo(marker.getPoint());
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
					map.setCenter(latLngPlace, map.getCurrentMapType().getMaximumResolution());
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
        marker.setLatLng(new google.maps.LatLng(lat, lng));
      }
    }
    
    function getCoordsByDirection(sDirection, callback) {
      if (sDirection != undefined) {
        geocoder.getLatLng(sDirection, callback);
      }
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
    
    function setMarkerUser(category, point) {
      var tinyIcon, titleHere;
      tinyIcon = createIcon(category, false, false, "m");
      titleHere = getTitleTemp();
      marker_user = new google.maps.Marker(point, {title: titleHere, draggable: true, bouncy: true, icon:tinyIcon});
      google.maps.Event.addListener(marker_user, "dragstart", function () {
        map.closeInfoWindow();
      });
      google.maps.Event.addListener(marker_user, "dragend", function (latLng) {
        openRGCInfoWindow(latLng, marker_user);
        if (mySite.filter.recieveLocation != undefined) {
          mySite.filter.recieveLocation(latLng.lat(), latLng.lng());
        }
      });
      google.maps.Event.addListener(marker_user, "click", function (latLng) {
        openRGCInfoWindow(latLng, marker_user);
      });
      map.addOverlay(marker_user);
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
    
    function removeAddress() {
      if (marker_user != undefined) {
        map.removeOverlay(marker_user);
        marker_user = undefined;
      }
    }
    
    function setupConvertMarker(lat, lng) {
      var infoWindow, point, titleHere;
      point = new google.maps.LatLng(lat, lng);
      resetConvertMarker();
      titleHere = getTitleTemp();
      marker_converter = new google.maps.Marker(point, {title: titleHere, draggable: true, bouncy: true});
      map.addOverlay(marker_converter);
      converter_handler = google.maps.Event.addListener(marker_converter, "dragend", function (latLng) {
        convertFromWGS84(marker_converter.getLatLng().lat(), marker_converter.getLatLng().lng());
      });
      google.maps.Event.addListener(marker_converter, 'click', function (latLng) {
        if (!drawMode) {
          openRGCInfoWindow(latLng, marker_converter);
        }
      });
      google.maps.Event.addListener(marker_converter, "dragstart", function () {
        map.closeInfoWindow();
      });
      openRGCInfoWindow(marker_converter.getLatLng(), marker_converter);
    }
    
    function resetConvertMarker() {
      if (marker_converter) {
        if (converter_handler) {
          google.maps.Event.removeListener(converter_handler);
        }
        map.removeOverlay(marker_converter);
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
      marker_elevation = new google.maps.Marker(point, {title: titleHere, draggable: true, bouncy: true});
      map.addOverlay(marker_elevation);
      elevation_handler = google.maps.Event.addListener(marker_elevation, "dragend", function (latLng) {
        setupElevationMarker(latLng.lat(), latLng.lng());
      });
      google.maps.Event.addListener(marker_elevation, 'click', function (latLng) {
        if (!drawMode) {
          marker_elevation.openInfoWindowHtml(infoWindow);
        }
      });
      google.maps.Event.addListener(marker_elevation, "dragstart", function () {
        map.closeInfoWindow();
      });
      marker_elevation.openInfoWindowHtml(infoWindow);
    }
    
    function resetElevationMarker() {
      if (marker_elevation) {
        if (elevation_handler) {
          google.maps.Event.removeListener(elevation_handler);
        }
        map.removeOverlay(marker_elevation);
        marker_elevation = undefined;
      }
    }
    
		function goToDefaultPosition(lat, lng, zoom) {
		  var mapMove_handler;
      mapMove_handler = google.maps.Event.addListener(map, "moveend", function () {
        google.maps.Event.removeListener(mapMove_handler);
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
      load_gmap_wms();
      load_GCMap();
      load_GMOpacity();
      load_GCControls();
      load_contextmenucontrol();
      load_dragzoom();
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
      loadMarkers();
      isLoaded = true;
      //Hide the checkboxes
      //.blur();
    }
    
    function unload() {
      google.maps.Unload();
      isLoaded = false;
    }
    
    /*See http://code.google.com/p/gmaps-api-issues/wiki/JavascriptMapsAPIChangelog for Google Maps version.*/
		google.load("maps", "2", {"language" : "<?php echo $_SESSION['language']; ?>", "other_params" : "sensor=true,indexing=false"});
    //google.load("maps", "3", {"language" : "<?php echo $_SESSION['language']; ?>", "other_params":"sensor=true"});
    google.load("elements", "1", {packages : ["localsearch"], "language" : "<?php echo $_SESSION['language']; ?>"});//, "nocss" : true
    document.onkeyup=manageKey;
    google.setOnLoadCallback(loadContext, true);

    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onunload="JavaScript:unload();"><!-- onload="JavaScript:toggle();" onresize="JavaScript:containerResized();"-->
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
    <div id="directions" style="display:none;visibility:hidden;">
    </div>
    <div id="clip">
    </div>
<?php
    $virtual_page = "overview/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>