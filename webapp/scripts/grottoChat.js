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

var handler;
var scrollEnabled = true;
var KEY_RETURN = 13;

document.onkeypress = function(event) {
  if (getKeyCode(event) == KEY_RETURN) {
    grottochat.send();
  }
}

function setScrollStatus(event) {
  if (scrollEnabled) {
    scrollEnabled = false;
  } else {
    var dialogs = getTargetNode(event);
    if (dialogs.scrollTop + dialogs.offsetHeight >= dialogs.scrollHeight) {
      scrollEnabled = true;
    }
  }
}

function chatajax() {
  this.send = function(url) {
    var xhr_object;
    url = url + '&nocache=' + Math.round(Math.random()*10000000000);
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
    xhr_object.open("GET", url, false); 
    xhr_object.send(null); 
    if (xhr_object.readyState === 4) {
      return xhr_object.responseText;
    } else {
      return;
    }
  }
}

function grottochat(usersDivId, dialogsDivId, messageInputId, buttonInputId, guest, roomId, refreshDelay, webServiceUrl) {
  var request;
	request = new chatajax();
	if (typeof(roomId) == undefined) {
    roomId = 1;
  }
	if (typeof(refreshDelay) == undefined) {
    refreshDelay = 5000;
  }
	if (typeof(webServiceUrl) == undefined) {
    webServiceUrl = 'webservices/grottoChat.php?lang=En';
  }
  
	this.refresh = function() {
    var url, answer, dialogs, users, scrollPosn;
		// Mise à jour de la liste des utilisateurs
		url = webServiceUrl + '&action=list&delay=' + refreshDelay;
		users = xtdGetElementById(usersDivId);
		dialogs = xtdGetElementById(dialogsDivId);
		answer = request.send(url);
		scrollPosn = dialogs.scrollTop;
		users.innerHTML = answer;
		// Mise à jour des messages
		url = webServiceUrl + '&action=refresh&id=' + roomId + '&delay=' + refreshDelay;
		answer = request.send(url);
		dialogs.innerHTML = answer;
		if (scrollEnabled) {
		  dialogs.scrollTop = dialogs.scrollHeight - dialogs.offsetHeight;
		} else {
      dialogs.scrollTop = scrollPosn;
    }
		// Rappel de la fonction
		if (handler != undefined) {
		  clearTimeout(handler);
    }
		handler = setTimeout('grottochat.refresh()',refreshDelay);
	}

	this.send = function(message) {
    var url, answer, button;
    scrollEnabled = true;
    if (mySite) {
      if (mySite.setSessionTimer) {
        mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      }
    }
    button = xtdGetElementById(buttonInputId);
    if (!button.disabled) {
      disableField(button, true);
  		if (message == undefined && message != "") {
  			message = xtdGetElementById(messageInputId).value;
  		  xtdGetElementById(messageInputId).value = '';
  		}
  		xtdGetElementById(messageInputId).focus();
  		url = webServiceUrl + '&action=send&id=' + roomId + '&message=' + encodeURI(encodeURIComponent(message)) + '&guest=' + encodeURI(encodeURIComponent(guest));
  		answer = request.send(url);
      disableField(button, false);
  		if (answer == -1) {
  			alert('Room N°' + roomId + ' not found');
  			return false;
  		} else if (answer == 0) {
  			alert('Your browser must accepts session cookies');
  			return false;
  		}
  		//grottochat.refresh();
  	}
	}
}