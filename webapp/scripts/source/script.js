/*required*/
var wrongPic, rightPic, center, zoom, alertTimerId, expirationTimerId, childWindow, mySite;
/*required*/

function strIsEmpty(str) {
  if (str === "" || str === undefined) {
    return true;
  }
  return false;
}

function getMasterWindow() {
  var getSite, site;
  if (window.opener && !window.opener.closed) {
    getWindow = window.opener.top;
  } else {
    getWindow = window.top;
  }
  if (getWindow == undefined) {
    getSite = false;
  } else {
    getSite = getWindow.site;
  }
  if (!getSite) {
    if (getWindow != undefined) {
      getSite = getWindow.mySite;
    }
    if (!getSite) {
      if (document.location.toString().indexOf('/html/file_', 0) === -1) {
        window.top.location = "../index.php";
      }
      return;
    }
  }
  return getSite;
}

function clearSessionTimer() {
  clearTimeout(alertTimerId);
  clearTimeout(expirationTimerId);
}

function sessionTimer(alertDelay, expirationTime, url, firstMsg, lastMsg) {
  //expirationTime in minutes
  //alertDelay in minutes
  clearSessionTimer();
  alertTimerId = setTimeout('alertTimer(' + alertDelay + ', "' + firstMsg + '")', (expirationTime - alertDelay) * 60 * 1000);
  expirationTimerId = setTimeout('expirationTimer("' + url + '", "' + lastMsg + '")', expirationTime * 60 * 1000);
}

function alertTimer(minutes, firstMsg) {
  var msg = firstMsg.split("|")[0] + minutes + firstMsg.split("|")[1];
  if (confirm(msg)) {
    location.reload();
  }
}

function expirationTimer(url, lastMsg) {
  alert(lastMsg);
  mySite.parent.location.href = url;
}

function setLogOff(iMinutes, sMessage) {
  setTimeout('logOff("' + sMessage + '")', iMinutes * 60 * 1000);
}

function logOff(sMessage) {
  alert(sMessage);
  window.top.location.reload();
}

/*function getMarkerText(marker, AKAText) {
  switch (marker.category) {
  case "caver":
    var text = "";
    if (!strIsEmpty(marker.cavername)) {
      text = text + marker.cavername + " ";
    }
    if (!strIsEmpty(marker.caversurname)) {
      text = text + marker.caversurname + " ";
    }
    if (!strIsEmpty(marker.cavername) || !strIsEmpty(marker.caversurname)) {
      text = text + AKAText + " ";
    }
    text = text + marker.getTitle();
    return text;
  default:
    return marker.getTitle();
  }
}*/

function ltrim(str) { 
	for(var k = 0; k < str.length && isWhitespace(str.charAt(k)); k++);
	return str.substring(k, str.length);
}

function rtrim(str) {
	for(var j=str.length-1; j>=0 && isWhitespace(str.charAt(j)) ; j--) ;
	return str.substring(0,j+1);
}

function trim(str) {
	return ltrim(rtrim(str));
}

function isWhitespace(charToCheck) {
	var whitespaceChars = " \t\n\r\f";
	return (whitespaceChars.indexOf(charToCheck) != -1);
}

function strToFloat(str) {
  return parseFloat(str.replace(new RegExp("[,]", "ig"), "."));
}

function getListForSelect(markersArray, AKAText) {
  var list, k, j, i, idsArray, namesArray, names;
  j = 0;
  list = [];
  for (i = 0; i < markersArray.length; i = i + 1) {
    if (markersArray[i].markerType == "m") {
      list[j] = {};
      list[j].Category = markersArray[i].category;
      list[j].Id = markersArray[i].id;
      list[j].Text = markersArray[i].getTitle();
      j = j + 1;
    } else { //Clustered markers
      idsArray = [];
      idsArray = markersArray[i].id.split(",");
      namesArray = [];
      names = markersArray[i].getTitle();
      names = names.substring(names.indexOf("]") + 2, names.length);
      namesArray = names.split(", ");
      for (k = 0; k < idsArray.length; k = k + 1) {
        list[j] = {};
        list[j].Category = markersArray[i].category;
        list[j].Id = idsArray[k];
        list[j].Text = namesArray[k];
        j = j + 1;
      }
    }
  }
  return list;
}

function ctxtMenu(event, doShow) {
  if (mySite.overview) {
    if (doShow) {
      if (mySite.overview.MCie5 || mySite.overview.MCns6) {
        mySite.overview.showmenuie5(event);
      }
    } else {
      if (mySite.overview.MCie5 || mySite.overview.MCns6) {
        mySite.overview.hidemenuie5(event);
      }
    }
  }
}

function hideCtxtMenu(event) {
  ctxtMenu(event, false);
}

function manageKey(event) {
  var key_pressed, escKey;
  escKey = 27;
  key_pressed = getKeyCode(event);
  if (mySite.overview) {
    if (mySite.overview.debug) {
      mySite.overview.google.map.Log.write("Key pressed : " + key_pressed);
    }
  }
  if (key_pressed === escKey) {
    if (mySite.overview) {
      if (mySite.overview.debug) {
        mySite.overview.google.map.Log.write("Abort key pressed");
      }
      mySite.overview.abortLoading();
    }
  }
}

function setNodePosition(id, x, y) {
  if (document.getElementById) {
    if (x != undefined && x != "") {
      document.getElementById(id).style.left = x + "px";
    }
    if (y != undefined && y != "") {
      document.getElementById(id).style.top = y + "px";
    }
    return true;
  } else if (document.all && !document.getElementById) {
    if (document.all[id]) {
      if (x != undefined && x != "") {
        document.all[id].style.left = x + "px";
      }
      if (y != undefined && y != "") {
        document.all[id].style.top = y + "px";
      }
      return true;
    }
  }
  return false;
}

function getMousePosition(event) {
	var posx, posy;
	event = getEvent(event);
  posx = 0;
	posy = 0;
	if (event.pageX || event.pageY) {
		posx = event.pageX;
		posy = event.pageY;
	} else if (event.clientX || event.clientY) {
		posx = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		posy = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	}
	return {"x": posx, "y": posy}
}

function getTargetNode(event) {
	var targetNode;
	event = getEvent(event);
	if (event != undefined) {
		if (event.target) {
			targetNode = event.target;
		} else if (event.srcElement) {
			targetNode = event.srcElement;
		}
		if (targetNode.nodeType == 3) {// defeat Safari bug
			targetNode = targetNode.parentNode;
		}
	}
	return targetNode;
}

function getKeyCode(event) {
	event = getEvent(event);
	if (event.keyCode) {
    return event.keyCode;
  } else {
    return event.which;
  }
}

function getEvent(event) {
	if (!event && window.event) {
		event = window.event;
	}
	return event;
}

function getURLParams() {
  var query, params, paramArray, i, pos, name, value;
  query = window.top.location.search.substring(1);
  if (query.length > 0) {
    params = query.split("&");
    paramArray = [];
    for (i = 0 ; i < params.length ; i = i + 1) {
      pos = params[i].indexOf("=");
      name = params[i].substring(0, pos);
      value = params[i].substring(pos + 1);
      paramArray[i] = {};
      paramArray[i].name = name;
      paramArray[i].value = value;
    }
    return paramArray;
  } else {
    return "";
  }
}

function getURLBase(lang) {
  var url, urlBase, base;
  url = window.top.location.toString().split("?");
  urlBase = url[0];
  base = urlBase.substring(0, urlBase.lastIndexOf("_") + 1) + lang + urlBase.substring(urlBase.lastIndexOf("."), urlBase.length);
  return base;
}

function getURLParam(name) {
  //name = name.replace(new RegExp("[\[]"), "\\\[");
  //name = name.replace(new RegExp("[\]]"), "\\\]");
  var regexS, regex, results;
  regexS = "[\\?&]" + name + "=([^&#]*)";
  regex = new RegExp(regexS);
  results = regex.exec(window.top.location.href);
  if (results === null) {
    return "";
  } else {
    return results[1];
  }
}

function changeLanguage(oSelect, doReload) {
  var lang, paramArray, params, i, flag;
  if (doReload === undefined) {
    doReload = false;
  }
  lang = oSelect.options[oSelect.selectedIndex].value;
  if (doReload) {
    //window.top.location = "../index.php?lang=" + lang;
    paramArray = getURLParams();
    params = "";
    if (!strIsEmpty(paramArray)) {
      flag = false;
			for (i = 0 ; i < paramArray.length ; i = i + 1) {
        if (paramArray[i].name === "lang") {
          paramArray[i].value = lang;
					flag = true;
        }
        params = params + paramArray[i].name + "=" + paramArray[i].value + "&";
      }
			if (!flag) {
				params = params + "lang=" + lang + "&";
			}
      params = "?" + params.substring(0, params.length - 1);
    }
    window.top.location = getURLBase(lang) + params;
  } else {
    window.top.location = "../index.php?lang=" + lang;
  }
}

function getMaxWidthInput(oForm, tagName, offset) {
  var valMax, valTemp, inputId, elementArray, i;
  if (tagName === undefined) {
    tagName = 'TR';//'SPAN';
  }
  valMax = 0;
  if (offset === undefined) {
    offset = 12;//28; //(la croix pour fermer fait 19px)
  }
  elementArray = oForm.getElementsByTagName(tagName);
  for (i = 0; i < elementArray.length; i = i + 1) {
    if (elementArray[i] != undefined &&
      elementArray[i].offsetLeft != undefined &&
      elementArray[i].offsetWidth != undefined) {
      valTemp = parseInt(elementArray[i].offsetLeft, 10) + parseInt(elementArray[i].offsetWidth, 10);
      if (valMax < valTemp) {
        valMax = valTemp;
        inputId = i;
      }
    }
  }
  valMax = valMax + offset;
  return valMax;
}

function findElementPosX(obj) {
	curleft = 0;
	if (obj.offsetParent) {
		while (obj.offsetParent) {
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	}//if offsetParent exists
	else if (obj.x)
		curleft += obj.x
	return curleft;
}

function findElementPosY(obj) {
	curtop = 0;
	if (obj.offsetParent) {
		while (obj.offsetParent) {
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}//if offsetParent exists
	else if (obj.y)
		curtop += obj.y
	return curtop;
}

function getResponseText(sURL) {
  var xhr_object;
  xhr_object = null; 
  if (window.XMLHttpRequest) { // Firefox 
    xhr_object = new XMLHttpRequest(); 
  } else {
    if (window.ActiveXObject) {// Internet Explorer 
      xhr_object = new ActiveXObject("Microsoft.XMLHTTP"); 
    } else { // XMLHttpRequest non supporté par le navigateur 
      alert("XMLHTTPRequest error !"); 
      return;
    }
  }
  xhr_object.open("GET", sURL, false); 
  xhr_object.send(null); 
  if (xhr_object.readyState === 4) {
    return xhr_object.responseText;
  }
}
/*
var timeoutId = window.setTimeout(
function() {
if ( callInProgress(xmlhttp) ) {
xmlhttp.abort();
reportTimeout();
}
},
5000 // Five seconds
);

// Callback
function handleResponse() {
if ( xmlhttp.readyState == 4 ) {

window.clearTimeout(timeoutId);

alert (xmlhttp.responseText);
}
}

function reportTimeout() {
// Fairly inexact but still something
alert('A request was timed out. Taking too long');
}*/

function getMarkersByCategory(category) {
  return mySite.overview.getMarkersByCategory(category);
}

function getMarkersByCountry(category, country) {
  return mySite.overview.getMarkersByCountry(category, country);
}

function getMarkersByMassif(category, country, massif) {
  return mySite.overview.getMarkersByMassif(category, country, massif);
}

function getMarkersByCave(category, country, massif, cave) {
  return mySite.overview.getMarkersByCave(category, country, massif, cave);
}

function showMarker(markerId, markerCategory) {
  mySite.overview.showMarker(markerId, markerCategory);
}

function showMarkers(markersArray) {
  mySite.overview.showMarkers(markersArray);
}

function hideMarker(markerId, markerCategory) {
  mySite.overview.hideMarker(markerId, markerCategory);
}

function hideMarkers(markersArray) {
  mySite.overview.hideMarkers(markersArray);
}

function isUserConnected() {
  var sURL;
  sURL = "webservices/getUserParamPlain.php?p=connected";
  return eval(getResponseText(sURL));
}

function doShowLinksOnHover() {
  var sURL;
  sURL = "webservices/getUserParamPlain.php?p=hover";
  return eval(getResponseText(sURL));
}

function reload(openInfoWindow) {
  var myOverview, categories, i;
  if (openInfoWindow === undefined) {
    openInfoWindow = true;
  }
  myOverview = mySite.overview;
  myOverview.startBusy();
  myOverview.cancelAbortLoading();
  myOverview.doResetEnvironement = true;
  categories = ["entry", "caver", "grotto"];
  for (i = 0; i < categories.length; i = i + 1) {
    myOverview.removeMarkers(myOverview.existingMarkers[categories[i]], categories[i], true);
    myOverview.existingMarkers[categories[i]] = new myOverview.Array();
  }
  myOverview.removeLines(myOverview.existingLines, true);
  myOverview.existingLines = new myOverview.Array();
  myOverview.userConnected = isUserConnected();
  myOverview.showLinksOnHover = doShowLinksOnHover();
  //myOverview.map.clearOverlays();
  myOverview.loadFromXML();
}

function setWGS84Form(lat, lng) {
  var oForm;
  oForm = mySite.details.document.coords_converter_form;
  oForm.T_Lat_W.value = lat;
  oForm.T_Long_W.value = lng;
}

function convertFromWGS84(lat, lng) {
  var oForm, sourceDiv;
  oForm = mySite.details.document.coords_converter_form;
  /*DEPRECATED ON 2.2:
	sourceDiv = 'GPS_dec';
  setWGS84Form(lat, lng);
  mySite.details.convert_coords(sourceDiv, oForm);*/
	mySite.details.setCountry(lat, lng);
	mySite.details.converterHash.transform({'x':lng,'y':lat});
  mySite.details.switchConverter(true, "coords_converter");
}

function getMouseLatLng() {
  var bannerForm;
  bannerForm = mySite.banner.document.measurement;
  return {"lat": strToFloat(bannerForm.mouseLat.value), "lng": strToFloat(bannerForm.mouseLng.value)};
}

function convertMousePsn(LatLng) {
  var lat, lng;
  if (LatLng === undefined) {
    LatLng = getMouseLatLng();
  }
  lat = LatLng.lat;
  lng = LatLng.lng;
  convertFromWGS84(lat, lng);
}

function setInputDefaultValue(oinput, sValue, bselect) {
  if (bselect) {
    if (oinput.value === sValue) {
      oinput.value = "";
      oinput.style.color = "black";
    }
  } else {
    if (strIsEmpty(oinput.value)) {
      oinput.value = sValue;
      oinput.style.color = "#808080";
    }
  }
}

function xtdGetElementById(id) {
  if (document.getElementById) { // DOM3 = IE5, NS6
    return document.getElementById(id);
  } else {
    if (document.layers) { // Netscape 4
      return document.id;
    } else { // IE 4
      if (document.all[id] != undefined) {
        return document.all[id];
      } else {
        return undefined;
      }
    }
  }
}

function xtdSetDisplay(baliseId, displayValue) {
  if (document.getElementById && document.getElementById(baliseId) != undefined) { // DOM3 = IE5, NS6
    document.getElementById(baliseId).style.display = displayValue;
  } else {
    if (document.layers) { // Netscape 4
      document.baliseId.display = displayValue;
    } else { // IE 4
      if (document.all != undefined) {
        if (document.all[baliseId] != undefined) {
          document.all[baliseId].style.display = displayValue;
        }
      }
    }
  }
}

function xtdGetDisplay(baliseId) {
  if (document.getElementById && document.getElementById(baliseId) != undefined) { // DOM3 = IE5, NS6
    return document.getElementById(baliseId).style.display;
  } else {
    if (document.layers) { // Netscape 4
      return document.baliseId.display;
    } else { // IE 4
      if (document.all != undefined) {
        if (document.all[baliseId] != undefined) {
          return document.all[baliseId].style.display;
        } else {
          return undefined;
        }
      }
    }
  }
}

function xtdSetVisibility(baliseId, visiValue) {
  if (document.getElementById && document.getElementById(baliseId) != undefined) { // DOM3 = IE5, NS6
    document.getElementById(baliseId).style.visibility = visiValue;
  } else {
    if (document.layers) { // Netscape 4
      document.baliseId.visibility = visiValue;
    } else { // IE 4
      if (document.all != undefined) {
        if (document.all[baliseId] != undefined) {
          document.all[baliseId].style.visibility = visiValue;
        }
      }
    }
  }
}

function showId(baliseId) { //safe function to show an element with a specified id
  xtdSetVisibility(baliseId, 'visible');
  xtdSetDisplay(baliseId, 'block');
}

function hideId(baliseId) { 	//safe function to hide an element with a specified id
  xtdSetVisibility(baliseId, 'hidden');
  xtdSetDisplay(baliseId, 'none');
}

/*function showId(baliseId) {
if (self.document.getElementById && self.document.getElementById(baliseId) != null) {
self.document.getElementById(baliseId).style.visibility='visible';
self.document.getElementById(baliseId).style.display='block';
}
}*/

/*function hideId(baliseId) {
if (self.document.getElementById && self.document.getElementById(baliseId) != null) {
self.document.getElementById(baliseId).style.visibility='hidden';
self.document.getElementById(baliseId).style.display='none';
}
}*/

//draw a line into the map
/*function drawLine(x_start,y_start,x_end,y_end) {
var jg = mySite.overview.jg;
jg.clear();
jg.setColor("#FFA713");
jg.setStroke(Stroke.DOTTED);
jg.setPrintable(true);
jg.drawLine(x_start,y_start,x_end,y_end);
jg.paint();
}*/

//Link to the connection part
function connectUser(lang) {
  mySite.filter.location.href = "connection_" + lang + ".php?type=login";
}

function checkPassword(oObject) { //obsolete
  if (oObject.value.length < 8) {
    oObject.style.color = 'red';
  } else {
    oObject.style.color = 'black';
  }
}

function checkPassword2(oObject, oObject2) { //obsolete
  if (oObject.value !== oObject2.value) {
    oObject.style.color = 'red';
  } else {
    oObject.style.color = 'black';
  }
}

function userIsOk(oDocument) {
  return mySite.banner.userIsOk(oDocument);
}

function loadNames(sCategory) {
  var sURL, namesArray;
  sURL = "webservices/getNamesPlain.php?cat=" + sCategory;
  namesArray = eval(getResponseText(sURL));
  return namesArray;
}

function getUserClusterLimit() {
  var sURL;
  sURL = "webservices/getUserParamPlain.php?p=limit";
  return eval(getResponseText(sURL));
}

function checkName(oObject, sTargetId, sCategory, sName, namesArray, bUnicity) {
  var reg, found, i;
  if (bUnicity === undefined) {
    bUnicity = true;
  }
  switch (sCategory) {
    case "caver":
      reg = new RegExp("^[^|/\\\\\"#&+¨\\s]{1}[^|/\\\\\"#&+¨]{1,18}[^|/\\\\\"#&+¨\\s]{1}$", "ig");
      //^[a-zA-Z0-9]{1}[a-zA-Z0-9\\s]{1,18}[a-zA-Z0-9]{1}$
      //"^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{1,18}[^|/\\\"\\s\\\\#&+¨]{1}$","g");
      break;
    case "caver_long":
      reg = new RegExp("^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{0,66}[^|/\\\"\\s\\\\#&+¨]{1}$", "ig");
      break;
    case "request":
    case "file":
    case "entry":
      reg = new RegExp("^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{0,98}[^|/\\\"\\s\\\\#&+¨]{1}$", "ig");
      break;
    case "author":
      reg = new RegExp("^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{0,68}[^|/\\\"\\s\\\\#&+¨]{1}$", "ig");
      break;
    case "url":
      reg = new RegExp("^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{0,198}[^|/\\\"\\s\\\\#&+¨]{1}$", "ig");
      break;
    case "caver2":
      reg = new RegExp("^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{0,34}[^|/\\\"\\s\\\\#&+¨]{1}$", "ig");
      break;
    default:
      reg = new RegExp("^[^|/\\\"\\s\\\\#&+¨]{1}[^|/\\\"\\\\#&+¨]{0,34}[^|/\\\"\\s\\\\#&+¨]{1}$", "ig");
      break;
  }
  found = false;
  if (bUnicity) {
    for (i = 0; i < namesArray.length; i = i + 1) {
      if (oObject.value === namesArray[i]) {
        if (strIsEmpty(sName)) {
          found = true;
          break;
        } else {
          if (namesArray[i] !== sName) {
            found = true;
            break;
          }
        }
      }
    }
  }
	if (oObject != undefined) {
		if ((found && bUnicity) || !reg.test(oObject.value)) {
			xtdGetElementById(sTargetId).src = wrongPic;
		} else {
			xtdGetElementById(sTargetId).src = rightPic;
		}
	} else {
		xtdGetElementById(sTargetId).src = wrongPic;
	}
}

function getMatchingValues(userInput, collection, ignoredValue) {
  var MAX_MATCHES, i, regMatch, regSplit, matchCollection;
  MAX_MATCHES = 15;
  userInput = userInput.toLowerCase().replace(new RegExp("([\\\\\[\\]\\(\\)\\{\\}\\-\\.\\*\\?\\|\\^\\$]{1})","gi"),"\\$1").replace(/^[ \s\f\t\n\r]+/,'').replace(/[ \s\f\t\n\r]+$/,'');
  matchCollection = [];
  regSplit = new RegExp("\\s", "ig");
  //regMatch = new RegExp("(" + implode('|', userInput.split(regSplit)) + ")", "gi");
  regMatch = new RegExp("(" + userInput + ")", "gi");
  for (i = 0; i < collection.length; i = i + 1) {
    if (matchCollection.length <= MAX_MATCHES) {
      if (collection[i].match(regMatch) && collection[i] != ignoredValue) {
        matchCollection.push(collection[i].replace(regMatch,"<b>$1</b>"));
      }
    } else {
      break;
    }
  }
  return matchCollection;
}

function displayCloseNames(oObject, namesArray, label) {
  var container, i, text, sId;
  sId = 'closeNamesDiv';
  //Get the container
  container = xtdGetElementById(sId);
  if (!container) {
    container = document.createElement("div");
    container.setAttribute('id', sId);
    container.setAttribute('class', 'warnsuggestions');
    oObject.parentNode.insertBefore(container, oObject.nextSibling);
    hideId(sId);
    //Set the listeners
    oObject.setAttribute('onfocus', "JavaScript:showId('" + sId + "');");
    oObject.setAttribute('onblur', "JavaScript:hideId('" + sId + "');");
  }
  //Set the display
  text = label;
  for (i = 0; i < namesArray.length; i++) {
    text = text + '<br />' + namesArray[i];
  }
  container.innerHTML = text;
}

/* UNUSED */
function getCloseNames(name, namesArray, ignoredName) {
  var i, closeNamesArray, limit;
  closeNamesArray = [];
  limit = 4;
  for (i = 0; i < namesArray.length; i++) {
    if (namesArray[i] != ignoredName) {
      if (levenshtein(name, namesArray[i]) <= limit) {
        closeNamesArray.push(namesArray[i]);
      }
    }
  }
  return closeNamesArray;
}

/* UNUSED */
function levenshtein(s1, s2) {
  // Calculate Levenshtein distance between two strings
  //
  // version: 909.322
  // discuss at: http://phpjs.org/functions/levenshtein
  // +            original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
  // +            bugfixed by: Onno Marsman
  // +             revised by: Andrea Giammarchi (http://webreflection.blogspot.com)
  // + reimplemented by: Brett Zamir (http://brett-zamir.me)
  // + reimplemented by: Alexander M Beedie
  // *                example 1: levenshtein('Kevin van Zonneveld', 'Kevin van Sommeveld');
  // *                returns 1: 3
  if (s1 == s2) {
      return 0;
  }

  var s1_len = s1.length;
  var s2_len = s2.length;
  if (s1_len === 0) {
      return s2_len;
  }
  if (s2_len === 0) {
      return s1_len;
  }

  // BEGIN STATIC
  var split = false;
  try{
      split=!('0')[0];
  } catch (e){
      split=true; // Earlier IE may not support access by string index
  }
  // END STATIC
  if (split){
      s1 = s1.split('');
      s2 = s2.split('');
  }

  var v0 = new Array(s1_len+1);
  var v1 = new Array(s1_len+1);

  var s1_idx=0, s2_idx=0, cost=0;
  for (s1_idx=0; s1_idx<s1_len+1; s1_idx++) {
      v0[s1_idx] = s1_idx;
  }
  var char_s1='', char_s2='';
  for (s2_idx=1; s2_idx<=s2_len; s2_idx++) {
      v1[0] = s2_idx;
      char_s2 = s2[s2_idx - 1];

      for (s1_idx=0; s1_idx<s1_len;s1_idx++) {
          char_s1 = s1[s1_idx];
          cost = (char_s1 == char_s2) ? 0 : 1;
          var m_min = v0[s1_idx+1] + 1;
          var b = v1[s1_idx] + 1;
          var c = v0[s1_idx] + cost;
          if (b < m_min) {
              m_min = b; }
          if (c < m_min) {
              m_min = c; }
          v1[s1_idx+1] = m_min;
      }
      var v_tmp = v0;
      v0 = v1;
      v1 = v_tmp;
  }
  return v0[s1_len];
}

function getRadioValue(oRadioArray) {
  var i;
  for (i = 0; i < oRadioArray.length;i = i + 1) {
    if (oRadioArray[i].checked) {
      return oRadioArray[i].value;
    }
  }
  if (oRadioArray.length === undefined && oRadioArray.checked) {
    return oRadioArray.value;
  }
}

function getRadio(oRadioArray) {
  var i;
  for (i = 0; i < oRadioArray.length;i = i + 1) {
    if (oRadioArray[i].checked) {
      return oRadioArray[i];
    }
  }
}

function setRadio(oRadio, sValue) {
  var i;
  for (i = 0; i < oRadio.length; i = i + 1) {
    if (oRadio[i].value === sValue) {
      oRadio[i].click();
      break;
    }
  }
}

function switchDOM(sId) {
  if (xtdGetDisplay(sId).indexOf("none") === -1) {
    hideId(sId);
  } else {
    showId(sId);
  }
}

function checkMail(oObject, sTargetId) {
  var regMail;
  regMail = new RegExp("^[a-zA-Z0-9\\-_]+[a-zA-Z0-9\\.\\-_]*[@]{1}[a-zA-Z0-9\\-_]+[\\.]{1}[a-zA-Z\\.\\-_]{1,}[a-zA-Z\\-_]+$", "g");//^[a-zA-Z0-9\-_]+[a-zA-Z0-9\.\-_]*[@]{1}[a-zA-Z0-9\-_]+[\.]{1}[a-zA-Z\.\-_]{1,}[a-zA-Z\-_]+$
  if (!regMail.test(oObject.value)) {
    xtdGetElementById(sTargetId).src = wrongPic;
  } else {
    xtdGetElementById(sTargetId).src = rightPic;
  }
}

function checkBirth(oObject, sTargetId) {
  var regBirth;
  regBirth = new RegExp("^[0-9]{2}[/]{1}[0-9]{2}[/]{1}[0-9]{4}$", "g");
  if (!regBirth.test(oObject.value)) {
    xtdGetElementById(sTargetId).src = wrongPic;
  } else {
    xtdGetElementById(sTargetId).src = rightPic;
  }
}

function checkPwd(oObject, sTargetId) {
  var check;
  check = new RegExp("^[^|/\\\\'\"\\s#&+¨]{8,32}$", "g");
  if (!check.test(oObject.value)) {
    xtdGetElementById(sTargetId).src = wrongPic;
  } else {
    xtdGetElementById(sTargetId).src = rightPic;
  }
}

function checkPwd2(oPwd2, oPwd, sTargetId) {
  if (oPwd2.value !== oPwd.value || strIsEmpty(oPwd2.value)) {
    xtdGetElementById(sTargetId).src = wrongPic;
  } else {
    xtdGetElementById(sTargetId).src = rightPic;
  }
}

//Create a randomized password
/*function doChallengePwd() {
var charact = "";
var pwd = "";
for (i=1; i < =10; i = i + 1) {
switch (Math.floor(Math.random()*3)) {
case 0: //UpC
charact = String.fromCharCode(Math.floor(Math.random()*25) + 65);
break;
case 1: //LoC
charact = String.fromCharCode(Math.floor(Math.random()*25) + 97);
break;
case 2: //Num
charact = String.fromCharCode(Math.floor(Math.random()*9) + 48);
break;
}
pwd = pwd + charact;
}
return pwd;
}*/
/*
var char_escaped = "%FF%FE%FD%FC%FB%FA%F9%F8%F7%F6%F5%F4%F3%F2%F1%F0%EF%EE%ED%EC%EB%EA%E9%E8%E7%E6%E5%E4%E3%E2%E1%E0%DF%DE%DD%DC%DB%DA%D9%D8%D7%D6%D5%D4%D3%D2%D1%D0%CF%CE%CD%CC%CB%CA%C9%C8%C7%C6%C5%C4%C3%C2%C1%C0%BF%BE%BD%BC%BB%BA%B9%B8%B7%B6%B5%B4%B3%B2%B1%B0%AF%AE%AD%AC%AB%AA%A9%A8%A7%A6%A5%A4%A3%A2%A1%A0%9F%9E%9D%9C%9B%9A%99%98%97%96%95%94%93%92%91%90%8F%8E%8D%8C%8B%8A%89%88%87%86%85%84%83%82%81%80%7F%7E%7D%7C%7B%7A%79%78%77%76%75%74%73%72%71%70%6F%6E%6D%6C%6B%6A%69%68%67%66%65%64%63%62%61%60%5F%5E%5D%5C%5B%5A%59%58%57%56%55%54%53%52%51%50%4F%4E%4D%4C%4B%4A%49%48%47%46%45%44%43%42%41%40%3F%3E%3D%3C%3B%3A%39%38%37%36%35%34%33%32%31%30%2F%2E%2D%2C%2B%2A%29%28%27%26%25%24%23%22%21%20%1F%1E%1D%1C%1B%1A%19%18%17%16%15%14%13%12%11%10%0F%0E%0D%0C%0B%0A%09%08%07%06%05%04%03%02%01%00";
var char_all = unescape(char_escaped);

function bound(min_val, value, max_val) {  // Revision 1.00, becd.
  if (value < min_val) {
    value = min_val;
  }
  if(value > max_val) {
    value = max_val;
  }
  return value;
}

function aton(string, index) {  // Convert a character to a number.  The range is 0x00 to 0xFF inclusive.
   // Revision 1.00, becd.
  index = bound(0, index, string.length - 1);
  return char_all.indexOf(string.charAt(index), 0);
}

function ntoa(index) {  // Convert a number to a character.  The range is 0x00 to 0xFF inclusive.
   // Revision 1.00, becd.
  index = bound(0, index, 0xFF);
  return char_all.charAt(index);
}

function xor(data, pattern) {  // Simple xor of a string with a pattern.
  // Revision 1.00, becd.
  var result = "";
  var j = 0;
  // If no pattern is supplied, then we use a simple pattern.
  if(pattern === null || pattern === "" || pattern.length <= 0) {
    pattern = "simple_xor_pattern";
  }
  // XOR every character in the data string with a character in the pattern string.
  for(var i = 0; i < data.length; i = i + 1) {
    if(j >= pattern.length) {
      j = 0;
    }
    result = result + ntoa(aton(data, i)^aton(pattern, j + 1));
  }
  return result;
}

function encode(data, pattern) {  // Encode a string.  The result will be a well behaved escape()d string.
   // Note that the encoded string may be up to three times as long as the original.
   // Revision 1.00, becd.
   return escape(xor(data, pattern));
}

function decode(data, pattern) {  // Decode a string that was encode()d from above.
   // Revision 1.00, becd.
   return xor(unescape(data), pattern);
}
*/

function decToBin(dec) {
  var temp, result, rest, bin, i;
  bin = "";
  result = dec;
  for (i = 7; i >= 0; i = i - 1) {
    temp = result;
    result = Math.floor(result / 2);
    rest = temp - result * 2;
    bin = rest + bin;
  }
  return bin; 
}

function binToDec(bin) {
  var temp, result, i;
  result = 0;
  for (i = 0; i < 8; i = i + 1) {
    result = result * 2 + parseInt(bin.charAt(i), 10);
  }
  return result;
}

function myBoolXor(boolA, boolB) {
  return (!boolA !== !boolB);
}

function myDecXor(decA, decB) {
  var binA, binB, boolA, boolB, result, i;
  binA = decToBin(decA);
  binB = decToBin(decB);
  boolA = false;
  boolB = false;
  result = "";
  for (i = 0; i < Math.max(binA.length, binB.length); i = i + 1) {
    if (binA.charAt(i) === undefined || binA.charAt(i) === "0") {
      boolA = false;
    } else {
      boolA = true;
    }
    if (binB.charAt(i) === undefined || binB.charAt(i) === "0") {
      boolB = false;
    } else {
      boolB = true;
    }
    if (myBoolXor(boolA, boolB)) {
      result = result + "1";
    } else {
      result = result + "0";
    }
  }
  return binToDec(result);
}

function c(a, b) {
  var d, i;
  d = "";
  for (i = 0; i < a.length;i = i + 1) {
    d = d + String.fromCharCode(myDecXor(b, a.charCodeAt(i)));
  }
  return d;
}

//Capture the coordinates of the user
function doChallengeCoordinates(oLat, oLng, marker) {
  if (marker != undefined) {
    oLat.value = marker.getPosition().lat();
    oLng.value = marker.getPosition().lng();
  } else {
    oLat.value = 0;
    oLng.value = 0;
  }
}

function showMe(gLatLng, category, setZoom) {
  mySite.overview.showMarkerUser(gLatLng, category, setZoom);
}

function openMe(id, category, setZoom, gLatLng, goClose) {
  if (mySite.overview) {
    mySite.overview.openMarker(id, category, setZoom, gLatLng, goClose);
  }
}

function implode(glue, pieces) {
    // Joins array elements placing glue string between items and return one string  
    // 
    // version: 909.322
    // discuss at: http://phpjs.org/functions/implode
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
    return ( ( pieces instanceof Array ) ? pieces.join( glue ) : pieces );
}

function getAddress(prefixe, category, level) {
  var nPrefixe, address_array, oDoc, address, i;
  nPrefixe = prefixe + category + "_";
  address_array = [];
  oDoc = self.document;
  if (xtdGetElementById(nPrefixe + "address") && level >= 4) {
    address_array.push(oDoc.getElementById(nPrefixe + "address").value);
  }
  if (xtdGetElementById(nPrefixe + "city") && level >= 3) {
    address_array.push(oDoc.getElementById(nPrefixe + "city").value);
  }
  if (xtdGetElementById(nPrefixe + "region") && level >= 2) {
    address_array.push(oDoc.getElementById(nPrefixe + "region").value);
  }
  if (xtdGetElementById(nPrefixe + "country") && level >= 1) {
    if (oDoc.getElementById(nPrefixe + "country").selectedIndex !== 0) {
      address_array.push(oDoc.getElementById(nPrefixe + "country").options[oDoc.getElementById(nPrefixe + "country").selectedIndex].id);
    }
  }
  address = implode(", ", address_array);
  /*for (i = 0; i < address_array.length; i = i + 1) {
    address = address + [i] + ;
  }
  address = address.substring(0, address.length - 1);*/
  return address;
}

function moveMarkerTo(id, category, lat, lng) {
  mySite.overview.moveMarkerTo(id, category, lat, lng);
}

function getCoordsByDirection(direction, callback) {
  mySite.overview.getCoordsByDirection(direction, callback);
}

function freeMe(id, category) {
  mySite.overview.freeMarker(id, category);
}

function blockMe(id, category) {
  mySite.overview.blockMarker(id, category);
}

/*function fireInfowindow(id, category) {
mySite.overview.fireInfowindow(id, category);
}*/

function stopSubmit(event) {
  event = getEvent(event);
  if (event.preventDefault) {
    event.preventDefault();
  }
  event.returnValue = false;
}

function stopUnload(event, msg) {
  event = getEvent(event);
  if (event.preventDefault) {
    event.preventDefault();
  }
  event.returnValue = msg;
}

function setFilterCheckbox(sCategory, sId, doCheck) {
  var oForm, reg, i;
  oForm = mySite.filter.document.filter_tree;
  if (oForm != undefined) {
    reg = new RegExp("[|]", "gi");
    for (i = 0; i < oForm.length;i = i + 1) {
      if (oForm[i].id.split(reg)[0] === sCategory && oForm[i].id.split(reg)[oForm[i].id.split(reg).length - 1] === sId) {
        if (doCheck) {
          if (!oForm[i].checked) {
            oForm[i].click();
          }
        } else {
          if (oForm[i].checked) {
            oForm[i].click();
          }
        }
      }
    }
  }
}

function disableField(oField, bDisable) {
  oField.disabled = bDisable;
  if (oField.className != undefined) {
    var oClassName = oField.className;
    if (oClassName.substring(0, 6) === "button") {
      if (bDisable) {
        oField.className = "buttonDis" + oClassName.charAt(oClassName.length - 1);
      } else {
        oField.className = "button" + oClassName.charAt(oClassName.length - 1);
      }
    }
  }
}

function freeFields(oForm) {
  var i;
  for (i = 0; i < oForm.length; i = i + 1) {
    disableField(oForm[i], false);
  }
}

function blockFields(oForm) {
  var i;
  for (i = 0; i < oForm.length; i = i + 1) {
    disableField(oForm[i], true);
  }
}

function saveDefaultPosition() {
  mySite.overview.map.savePosition();
}

function returnToDefaultPosition() {
  mySite.overview.map.returnToSavedPosition();
}

function getMapPosition() {
  center = mySite.overview.map.getCenter();
  zoom = mySite.overview.map.getZoom();
}

function getDefaultZoom() {
  return mySite.overview.map.getZoom();
}

function getDefaultLat() {
  return mySite.overview.map.getCenter().lat();
}

function getDefaultLng() {
  return mySite.overview.map.getCenter().lng();
}

function setMapPosition() {
  mySite.overview.map.setCenter(center, zoom);
}

function goToDefaultPosition(lat, lng, level) {
  mySite.overview.goToDefaultPosition(lat, lng, level);
}

function fillSelect(list, oList) {
  var i, j; 
  j = 0;
  for (i = 0; i < list.length; i = i + 1) {
    if (list[i].Text != undefined) {
      oList.options[j] = new Option(list[i].Text);
    } else {
      if (list[i].getTitle != undefined) {
        oList.options[j] = new Option(list[i].getTitle());
      } else {
        if (list[i].Text != undefined) {
          oList.options[j] = new Option(list[i].Text);
        } else {
          continue;
        }
      }
    }
    if (list[i].Id != undefined) {
      oList.options[j].objectId = list[i].Id;
    }
    if (list[i].id != undefined) {
      oList.options[j].objectId = list[i].id;
    }
    if (list[i].Id != undefined) {
      oList.options[j].objectId = list[i].Id;
    }
    if (list[i].Category != undefined) {
      oList.options[j].category = list[i].Category;
    }
    if (list[i].category != undefined) {
      oList.options[j].category = list[i].category;
    }
    if (list[i].Category != undefined) {
      oList.options[j].category = list[i].Category;
    }
    if (list[i].Country != undefined) {
      oList.options[j].Country = list[i].Country;
    }
    if (list[i].Massif != undefined) {
      oList.options[j].Massif = list[i].Massif;
    }
    j = j + 1;
  }
}

function emptySelect(oList) {
  while (oList.options.length > 0) {
    oList.options[0] = null;
  }
}

function filterByCountry(sISO, sourceList, sDefault) {
  var newSourceList, i;
  newSourceList = [];
  for (i = 0; i < sourceList.length; i = i + 1) {
    if (sourceList[i].Country === sISO || sISO === sDefault) {
      newSourceList.push(sourceList[i]);
    }
  }
  return newSourceList;
}

function getElement(List, Prop, propValue) {
  var i;
  for (i = 0; i < List.length; i = i + 1) {
    if (List[i][Prop] === propValue) {
      return List[i];
    }
  }
  return undefined;
}

function getMyList(list, caver_id) {
  var myList, id, i;
  myList = [];
  id = 0;
  for (i = 0; i < list.length; i = i + 1) {
    if (id !== list[i].Id && (caver_id === undefined || list[i].caverId === caver_id)) {
      myList.push(list[i]);
      id = list[i].Id;
    }
  }
  return myList;
}

/*function doChallengeList(oSource, oDest) {
var value = "";
for (i = 0; i < oSource.length; i = i + 1) {
value = value + oSource[i].objectId + "|";
}
value = value.substring(0,value.length - 1);
oDest.value = value;
}*/

function doChallengeList(oSource, oDest) {
  var value, i;
  value = "";
  for (i = 0; i < oSource.length; i = i + 1) {
    value = value + oSource[i].value + "|";
  }
  value = value.substring(0, value.length - 1);
  oDest.value = value;
}

/*function moveSelect(oFromList, oToList) {
var Text = oFromList.options[oFromList.selectedIndex].text;
var Id = oFromList.options[oFromList.selectedIndex].objectId;
var Category = oFromList.options[oFromList.selectedIndex].category;
var nextIndex = oToList.options.length;
oToList.options[nextIndex] = new Option(Text);
oToList.options[nextIndex].objectId = Id
oToList.options[nextIndex].category = Category
oFromList.options[oFromList.selectedIndex] = null;
}*/

function moveOption(oFromList, oToList, iIndex) {
  oToList.appendChild(oFromList.options[iIndex]);
}

function moveAllOptions(oFromList, oToList) {
  var index;
  for (index = 0; index < oFromList.options.length; index = index + 1) {
    moveOption(oFromList, oToList, index);
    index = index - 1;
  }
}

function moveOptionsOnCriteria(oFromList, oToList, sCriteria, sValue, sDefault) {
  var criteria, index;
  for (index = 0; index < oFromList.options.length; index = index + 1) {
    criteria = oFromList.options[index][sCriteria];
    if (criteria === sValue || sValue === sDefault) {
      moveOption(oFromList, oToList, index);
      index = index - 1;
    }
  }
}

function enableMove(oForm, oMylist, oOtherList) {
  if (oMylist.length === 0) {
    disableField(oForm.remove, true);
  } else {
    disableField(oForm.remove, false);
  }
  if (oOtherList.length === 0) {
    disableField(oForm.add, true);
  } else {
    disableField(oForm.add, false);
  }
}

function sortSelect(oSelect) {
  var arrSelect, arrTemp, reg, arrResult, i;
  arrSelect = oSelect.options;
  arrTemp = [];
  reg = new RegExp("[|]+", "gi");
  for (i = 0; i < arrSelect.length; i = i + 1) {
    arrTemp[i] = arrSelect[i].text + "|" + arrSelect[i].objectId + "|" + arrSelect[i].category + "|" + arrSelect[i].Country + "|" + arrSelect[i].Massif;
  }
  arrTemp = arrTemp.sort();
  for (i = 0; i < arrTemp.length; i = i + 1) {
    arrResult = arrTemp[i].split(reg);
    oSelect.options[i] = new Option(arrResult[0]);
    oSelect.options[i].objectId = arrResult[1];
    oSelect.options[i].category = arrResult[2];
    oSelect.options[i].Country = arrResult[3];
    oSelect.options[i].Massif = arrResult[4];
  }
}

function orderSelect(oSelect) {
  var arrSelect, arrTemp, reg, arrResult, i;
  arrSelect = oSelect.options;
  arrTemp = [];
  reg = new RegExp("[|]+", "gi");
  for (i = 0; i < arrSelect.length; i = i + 1) {
    arrTemp[i] = arrSelect[i].text + "|" + arrSelect[i].value;
  }
  arrTemp = arrTemp.sort();
  for (i = 0; i < arrTemp.length; i = i + 1) {
    arrResult = arrTemp[i].split(reg);
    oSelect.options[i] = new Option(arrResult[0], arrResult[1]);
  }
}

function getOtherList(list, myList, caver_id) {
  var otherList, allList, id, found, i, j;
  otherList = [];
  allList = [];
  id = 0;
  for (i = 0; i < list.length; i = i + 1) {
    if (id !== list[i].Id) {
      allList.push(list[i]);
      id = list[i].Id;
    }
  }
  for (i = 0; i < allList.length; i = i + 1) {
    found = false;
    for (j = 0; j < myList.length; j = j + 1) {
      if (myList[j].Id === allList[i].Id) {
        found = true;
        break;
      }
    }
    if (!found) {
      otherList.push(allList[i]);
    }
  }
  return otherList;
}

function limitLength(oObject, iMax) {
  if (oObject.value.length > iMax) {
    oObject.value = oObject.value.substring(0, iMax);
  }
}

function displayLength(oObject, sId) {
  if (xtdGetElementById(sId)) {
    xtdGetElementById(sId).innerHTML = oObject.value.length;
  }
}

function selectValue(oSelect, sName) {
  var i;
  if (oSelect && !strIsEmpty(sName)) {
    for (i = 0; i < oSelect.options.length; i = i + 1) {
      if (oSelect.options[i]) {
        if (oSelect.options[i].text === sName) {
          oSelect.selectedIndex = i;
          break;
        }
      }
    }
  }
}

function getOptions(type) {
  var options;
  options = "directories=yes,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes,toolbar=yes";
  return options;
}

function openLegalNPrivacy(sLanguage) {
  var loadLocation;
  loadLocation = "legal_and_privacy_" + sLanguage + ".php";
  //window.open(loadLocation, "", getOptions("regular"));
  openWindow(loadLocation, "", undefined, undefined, getOptions("regular"));
}

function openInBlank(e, url) {
	oNode = getTargetNode(e);
	if (oNode != undefined) {
		if (oNode.nodeName != 'A') {
			oNode = oNode.parentNode;
		}
		if (oNode.nodeName == 'A') {
			oNode.setAttribute('href', url);
			oNode.setAttribute('target', '_blank');
		}
	}
}

function detailMarker(e, sCategory, sId, sLanguage, bNewWindow, systemArray) {
  var loadLocation, innerObject, oDocument, oNode, aNode;
  if (window.opener && !window.opener.closed) {
    window.opener.focus();
  }
  if (bNewWindow === undefined) {
    bNewWindow = false;
  }
  if (bNewWindow) {
    loadLocation = "file_" + sLanguage + ".php?lang=" + sLanguage + "&check_lang_auto=false&category=" + sCategory + "&id=" + sId;
    if (systemArray != undefined) {
      loadLocation = loadLocation + "&geodesic=" + encodeURI(encodeURIComponent(systemArray.geodesic)) + "&length=" + encodeURI(encodeURIComponent(systemArray.length)) + "&temperature=" + encodeURI(encodeURIComponent(systemArray.temperature));
    }
    //openWindow(loadLocation, "", undefined, undefined, getOptions("regular"));
		openInBlank(e, loadLocation);
  } else {
    sURL = "webservices/getPropertiesPlain_" + sLanguage + ".php?type=details&id=" + sId + "&category=" + sCategory;
    if (systemArray != undefined) {
      sURL = sURL + "&geodesic=" + encodeURI(encodeURIComponent(systemArray.geodesic)) + "&length=" + encodeURI(encodeURIComponent(systemArray.length)) + "&temperature=" + encodeURI(encodeURIComponent(systemArray.temperature));
    }
    /* OLD FASHIONED WAY => loading into a frame
    mySite.loader.location = sURL;
    */
    /* NEW FASHIONED WAY => AJAX */
    innerObject = getResponseText(sURL);
    oDocument = mySite.details.document;
    oDocument.getElementById('details_div').innerHTML = innerObject;
  }
}

function showRelationList(sLanguage, sCategory, sSecCategory, sId) {
  var sURL, oDocument, innerObject;
  sURL = "webservices/getPropertiesPlain_" + sLanguage + ".php?type=list_" + sCategory + "&id=" + sId + "&category=" + sSecCategory;
  /* OLD FASHIONED WAY => loading into a frame
  mySite.loader.location = sURL;
  */
  /* NEW FASHIONED WAY => AJAX */
  innerObject = getResponseText(sURL);
  oDocument = mySite.details.document;
  oDocument.getElementById('list_div').innerHTML = oDocument.getElementById('list_div').innerHTML + innerObject;
}

//DEPRECATED
function popUpAltitude(sLang, dLat, dLng) {
  var altUrl;
  altUrl = "altitude_" + sLang + ".php?lat=" + dLat + "&lng=" + dLng;
  //window.open(altUrl, "message", "toolbar=no,scrollbars=yes,width=500,height=500");
  //window.open(altUrl, "", "toolbar=no,scrollbars=yes,width=500,height=500");
  openWindow(altUrl, "", undefined, undefined, "toolbar=no,scrollbars=yes,width=500,height=500");
}

//DEPRECATED
function showMouseAltitude(sLang, LatLng) {
  var lat, lng;
  if (LatLng === undefined) {
    LatLng = getMouseLatLng();
  }
  lat = LatLng.lat;
  lng = LatLng.lng;
  popUpAltitude(sLang, lat, lng);
}

function popUpMsg(sMessage, sTitle, sOptions) {
  var Info, header, footer;
  if (sTitle === undefined) {
    sTitle = "message";
  }
  if (sOptions === undefined) {
    sOptions = "toolbar=no,scrollbars=yes,width=500,height=500";
  }
  //Info = window.open("", sTitle, sOptions);
  Info = window.open("", "", sOptions);
  Info.document.open("text/html", "replace");
  header = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
  header = header + "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  header = header + "<head>\n";
  header = header + "<link rel=\"stylesheet\" type=\"text/css\" href=\"../css/global.css\" />\n";
  header = header + "<title>" + sTitle + "</title>\n";
  header = header + "</head>\n";
  header = header + "<body>\n";
  footer = "</body>\n";
  footer = footer + "</html>\n";
  sMessage = header + sMessage + footer;
  Info.document.write(sMessage);
  Info.document.close();
}

function closeAllChildWindows() {
  if (childWindow && childWindow.open && !childWindow.closed) {
    childWindow.close();
  }
}

function openWindow(url, windowName, width, height, options) {
  var top, left, prop;
  if (url.indexOf('http://') != -1) {
     //check if the domain is good: www?
     if (url.substring(7, document.domain.length + 7) != document.domain) {
        url = 'http://' + document.domain + url.substring(url.indexOf(document.domain) + document.domain.length);
     }
  }
  if (options == undefined) {
    prop = "status,scrollbars,resizable";
    if (width != undefined && height != undefined) {
      top = (screen.height - height) / 2;
      left = (screen.width - width) / 2;
      prop = prop + ",width=" + width + ",height=" + height + ",top=" + top + ",left=" + left;
    }
  } else {
    prop = options;
  }
  closeAllChildWindows();
  //childWindow = window.open(url, windowName, prop);
  childWindow = window.open(url, "", prop);
  //childWindow.focus();
}

function editMarker(sCategory, sId, sLanguage) {
  mySite.filter.location.href = sCategory + "_" + sLanguage + ".php?back=filter&type=edit&id=" + sId;
}

function deleteMarker(sCategory, sId, sLanguage) {
  //var marker = mySite.overview.getMarker(sId, sCategory);
  //mySite.filter.location.href = sCategory + "_" + sLanguage + ".php?type=menu&action=clickDel&aid=" + sId; // + "&aname=" + marker.getTitle();
  mySite.filter.location.href = sCategory + "_" + sLanguage + ".php?type=delete&did=" + sId;
}

function addMarker(category, lat, lng, language) {
  var mouseLatLng;
  if (lat === undefined || lng === undefined) {
    mouseLatLng = getMouseLatLng();
    lat = mouseLatLng.lat;
    lng = mouseLatLng.lng;
  }
  mySite.filter.location.href = category + "_" + language + ".php?back=filter&type=edit&nlat=" + lat + "&nlng=" + lng;
}

function copyToClipboard(sString) {
  return this.clipboardData.setData("text", sString);
}

function copySelectedCoords(mouseLatLng) {
  if (mouseLatLng === undefined) {
    mouseLatLng = getMouseLatLng();
  }
  copyToClipboard(mouseLatLng.lat + "," + mouseLatLng.lng);
  //mySite.overview.clip.setText(mouseLatLng.lat + "," + mouseLatLng.lng);
  if (mySite.filter.recieveLocation) {
    mySite.filter.recieveLocation(mouseLatLng.lat, mouseLatLng.lng);
  }
}

function startMeasurement(sValue, sStart, sStop) {
  var myOverview, myBanner, measurement_form;
  myOverview = mySite.overview;
  myBanner = mySite.banner;
  measurement_form = myBanner.document.measurement;
  if (!myOverview.drawMode) {
    myOverview.drawMode = true;
    myBanner.showId('measure_label');
    myBanner.document.getElementById('measure_label').style.display = "inline";
    measurement_form.measure_btn.value = sStop;
  } else {
    myOverview.drawMode = false;
    myBanner.hideId('measure_label');
    measurement_form.measure.value = sValue;
    measurement_form.measure.style.color = "#808080";
    measurement_form.measure_btn.value = sStart;
    myOverview.lineStarted = false;
    myOverview.clearMeasurementLine();
  }
}

function resetMeasurementDisplay() {
  mySite.banner.document.measurement.measure.value = "0";
}

function resetDetails() {
  if (mySite.details != undefined && mySite.details.document != undefined) {
    if (mySite.details.document.getElementById('details_div') != undefined) {
      mySite.details.document.getElementById('details_div').innerHTML = "";
    }
  }
}

function resetDirections() {
  if (mySite.details != undefined && mySite.details.document != undefined) {
    if (mySite.details.closeDirections) {
      mySite.details.closeDirections();
    }
  }
}

function logMeOut() {
  top.location.href = "../index.php?logout=true";
}

function addAnchorTo(oObject, sAnchor) {
  var objectContent;
  if (oObject) {
    objectContent = oObject.innerHTML;
    oObject.innerHTML = "<a name=\"" + sAnchor + "\"></a>" + objectContent;
    return true;
  } else {
    return false;
  }
}

function getTableOfContent(oContainer, iMinLevel, iMaxLevel) {
  var toc, array, tagName, level, anchor, i;
  toc = "";
  array = [];
  array = document.getElementsByTagName('*');
  for (i = 0; i < array.length; i = i + 1) {
    tagName = array[i].tagName;
    if (tagName.length === 2 && tagName.substring(0, 1) === "H") {
      level = parseInt(tagName.substring(1, 2), 10);
      if (level >= iMinLevel && level <= iMaxLevel && array[i].innerHTML.indexOf('<input') === -1) {
        anchor = "anchor_" + iMinLevel + "_" + i;
        if (trim(array[i].innerHTML) != "") {
          toc = toc + "<a href=\"#" + anchor + "\" class=\"TOC" + level + "\">" + array[i].innerHTML + "</a><br />";
        }
        addAnchorTo(array[i], anchor);
      }
    }
  }
  return toc;
}

/*function getSrtm3(lat, lng) {
  var request, aObj;
  // Create the request
  request = 'http://ws.geonames.org/srtm3JSON?formatted=true&lat=' + lat  + '&lng=' + lng  + '&style=full&callback=setAltitude';
  // Create a new script object
  aObj = new JSONscriptRequest(request);
  // Build the script tag
  aObj.buildScriptTag();
  // Execute (add) the script tag
  aObj.addScriptTag();
}*/

function setAltitude(jData) {
  var oInput;
  oInput = mySite.banner.document.measurement.mouseAlt;
  if (jData != undefined) {
    if (jData.srtm3 === -32768) {
      oInput.value = 0;
    } else {
      // Show the value
      oInput.value = jData.srtm3;
    }
  } else {
    // There was a problem parsing search results
    oInput.value = "NaN";
  }
}

function toAbsURL(s) {
  var l, h, p, f, i, regExg;
  l = location;
  if (/^\w+:/.test(s)) {
    return s;
  }
  h = l.protocol + '//' + l.host;
  if (s.indexOf('/') === 0) {
    return h + s;
  }
  p = l.pathname.replace(/\/[^\/]*$/, '');
  regExg = new RegExp("\\.\\.\\/", "g")
  f = s.match(regExg);
  if (f) {
    s = s.substring(f.length * 3);
    for (i = f.length; i > 0; i = i - 1) {
      p = p.substring(0, p.lastIndexOf('/'));
    }
  }
  return h + p + '/' + s;
}

function include(url) {
  document.write("<script type='text/javascript' src='" + url + "'></script>");
}

function getSelectedText() {
  var str;
  if (window.getSelection) {
    str = window.getSelection();
  } else if (document.getSelection) {
    str = document.getSelection();
  } else {
    str = document.selection.createRange().text;
  }
  return str;
}

function countInstances(oObject, open, closed) {
  var opening, closing;
  opening = oObject.value.split(open);
  closing = oObject.value.split(closed);
  return opening.length + closing.length - 2;
}

function insertTag(oObject, sOTag, sCTag) {
  var str, sel, instances, firstPos, secondPos, contenuScrollTop;
  if (document.selection) {
    str = document.selection.createRange().text;
    oObject.focus();
    sel = document.selection.createRange();
    if (!strIsEmpty(sCTag)) {
      if (strIsEmpty(str)) {
        instances = countInstances(oObject, sOTag, sCTag);
        if (instances % 2 !== 0) {
          sel.text = sel.text + sCTag;
        } else {
          sel.text = sel.text + sOTag;
        }
      } else {
        sel.text = sOTag + sel.text + sCTag;
      }
    } else {
      sel.text = sel.text + sOTag;
    }
  } else if (oObject.selectionStart || oObject.selectionStart === 0) {
    if (oObject.selectionEnd > oObject.value.length) {
      oObject.selectionEnd = oObject.value.length;
    }
    firstPos = oObject.selectionStart;
    secondPos = oObject.selectionEnd + sOTag.length;
    contenuScrollTop = oObject.scrollTop;
    
    oObject.value = oObject.value.slice(0, firstPos) + sOTag + oObject.value.slice(firstPos);
    oObject.value = oObject.value.slice(0, secondPos) + sCTag + oObject.value.slice(secondPos);
    
    oObject.selectionStart = firstPos + sOTag.length;
    oObject.selectionEnd = secondPos;
    oObject.focus();
    oObject.scrollTop = contenuScrollTop;
  } else { // Opera
    sel = document.hop.contenu;
    instances = countInstances(oObject, sOTag, sCTag);
    if (instances % 2 !== 0 && !strIsEmpty(sCTag)) {
      sel.value = sel.value + sCTag;
    } else {
      sel.value = sel.value + sOTag;
    }
  }
}

function replaceEMail(string) {
  var exp;
  exp = new RegExp("([a-zA-Z0-9\\.\\-_]+@[a-zA-Z0-9\\-_]+\\.[a-zA-Z0-9]{2,})", "g");
  return string.replace(exp, "<a href=\"mailto:$1\">$1</a>");
}

function replaceURL(string) {
  var exp1;
  exp1 = new RegExp("((ht|f)tps?://.*)[\\s]+", "gi");
  return string.replace(exp1, "<a href=\"$1\" target=\"_blank\">$1</a>");
}

function doChallengeLinks(string) {
  var str;
  str = "";
  str = replaceEMail(string);
  str = replaceURL(string);
  return str;
}

function resetForm(oForm) {
  var len, type, i, j;
  len = oForm.elements.length;
  for (i = 0; i < len; i = i + 1) {
    type = oForm.elements[i].type;
    name = oForm.elements[i].name;
    if (type !== "submit" && type !== "reset" && type !== "button" && name !== "records_by_page") {
      if (oForm.elements[i].tagName === "SELECT") {
        if (oForm.elements[i].multiple) {
          for (j = 0; j < oForm.elements[i].length; j = j + 1) {
            oForm.elements[i].options[j].selected = false;
          }
        } else {
          oForm.elements[i].selectedIndex = 0;
        }
      } else {
        if (type === "text") {
          oForm.elements[i].value = "";
        } else {
        }
      }
    }
  }
}

function checkAll(oForm) {
  var flag, len, i, j;
  flag = false;
  len = oForm.elements.length;
  for (i = 0; i < len; i = i + 1) {
    if (oForm.elements[i].type === "checkbox" && oForm.elements[i].checked === false) {
      flag = true;
      break;
    }
  }
  for (j = 0; j < len; j = j + 1) {
    if (oForm.elements[j].type === "checkbox") {
      oForm.elements[j].checked = flag;
    }
  }
}

function uncheckAll(oForm) {
  var len, j;
  len = oForm.elements.length;
  for (j = 0; j < len; j = j + 1) {
    if (oForm.elements[j].type === "checkbox") {
      oForm.elements[j].checked = false;
    }
  }
}

function inArray(aArray, sValue) {
  var j;
  for (j = 0; j < aArray.length; j = j + 1) {
    if (aArray[j] === sValue) {
      return true;
    }
  }
  return false;
}

function addOptionsFromSelection(fromForm, toSelect) {
  var len, i, j, flag, objId;
  len = fromForm.elements.length
  for (i = 0; i < len; i = i + 1) {
    if (fromForm.elements[i].type === "checkbox") {
      if (fromForm.elements[i].checked) {
        flag = true;
        objId = fromForm.elements[i].id.substring(1, fromForm.elements[i].id.length);
        for (j = 0; j < toSelect.options.length; j = j + 1) {
          if (toSelect.options[j].value === objId) {
            flag = false;
            break;
          }
        }
        if (flag) {
          toSelect.options[toSelect.options.length] = new Option(fromForm.elements[i].name, objId);
        }
      }
    }
  }
}

function alertObject(obj) {
  var string ="";
  for(var property in obj)
  {
      //Add the name and value of the child object
      string += property + ': '+ obj[property] + ''+ '\n';           
  }
  alert(string);
}

function setSelectedIndex(oSelect, sValue) {
  var len, i;
  if (sValue !== undefined && sValue !== null) {
    len = oSelect.options.length;
    for (i = 0; i < len; i = i + 1) {
      if (oSelect.options[i].value === sValue.toString()) {
        oSelect.selectedIndex = i;
        break;
      }
    }
  }
}

function reloadCaptcha(iLength, iSize, oButton, imgID) {
  var sURL, imgSrc;
  if (oButton !== undefined) {
    disableField(oButton, true);
  }
  sURL = "getCaptchaPlain.php?l=" + iLength + "&s=" + iSize;
  imgSrc = eval(getResponseText(sURL)) + '.gif';
  if (imgID !== undefined) {
    xtdGetElementById(imgID).setAttribute("src", imgSrc);
  }
  if (oButton !== undefined) {
    disableField(oButton, false);
  }
  return imgSrc;
}

function init() {
  /*Copy to cliboard*/
  var clipboard, flashclipboard;
  if (!window.clipboardData) {
    window.clipboardData = {
      setData : function (mode, scontent) {
        clipboard = document.body;
        flashclipboard = clipboard.flashclipboard;
        if (flashclipboard == null) {
          flashclipboard = document.createElement('div');
          clipboard.flashclipboard = flashclipboard;
          clipboard.appendChild(flashclipboard);
        }
        flashclipboard.innerHTML = '<embed src="../clipboard.swf" FlashVars="clipboard=' + encodeURIComponent(scontent) + '" width="0" height="0" type="application/x-shockwave-flash"></embed>';
      }
    };
  }
  /*Copy to cliboard*/
  wrongPic = "../images/icons/wrong.png";
  rightPic = "../images/icons/right.png";
  mySite = getMasterWindow();
}

/*required*/
init();
/*required*/

//Passer un nombre d'arguments de maniere indefinie
/*function test() {
for (i = 0; i < arguments.length; i = i + 1) {
alert(arguments[i]);
}
}*/

//Manipuler une listbox
//list1.options[j] = new Option(table[i].text1,table[i].value) ;