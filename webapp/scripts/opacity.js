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

var OPACITY_MAX_PIXELS = 57; // Width of opacity control image
var overlay;



function createOpacityControl(map, opacity) {
    var initialOpacity = 40;
    overlay = new CustomTileOverlay(map, initialOpacity);
    
    var sliderImageUrl = "../images/icons/opacity-slider.png";

    // Create main div to hold the control.
    var opacityDiv = document.createElement('DIV');
    opacityDiv.setAttribute("style", "margin:5px;overflow-x:hidden;overflow-y:hidden;background:url(" + sliderImageUrl + ") no-repeat;width:71px;height:21px;cursor:pointer;");

    // Create knob
    var opacityKnobDiv = document.createElement('DIV');
    opacityKnobDiv.setAttribute("style", "padding:0;margin:0;overflow-x:hidden;overflow-y:hidden;background:url(" + sliderImageUrl + ") no-repeat -71px 0;width:14px;height:21px;");
    opacityDiv.appendChild(opacityKnobDiv);

    var opacityCtrlKnob = new ExtDraggableObject(opacityKnobDiv, {
        restrictY: true,
        container: opacityDiv
    });

    google.maps.event.addListener(opacityCtrlKnob, "dragend", function () {
        setOpacity(opacityCtrlKnob.valueX());
    });

    google.maps.event.addDomListener(opacityDiv, "click", function (e) {
        var left = findPosLeft(this);
        var x = e.pageX - left - 5; // - 5 as we're using a margin of 5px on the div
        opacityCtrlKnob.setValueX(x);
        setOpacity(x);
    });

    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(opacityDiv);

    // Set initial value
    var initialValue = OPACITY_MAX_PIXELS / (100 / opacity);
    opacityCtrlKnob.setValueX(initialValue);
    setOpacity(initialValue);
}

function setOpacity(pixelX) {
    // Range = 0 to OPACITY_MAX_PIXELS
    var value = (100 / OPACITY_MAX_PIXELS) * pixelX;
    if (value < 0) value = 0;
    if (value == 0) {
            if (overlay.visible == true) {
                    overlay.hide();
            }
    }
    else {
            overlay.setOpacity(value);
            if (overlay.visible == false) {
                    overlay.show();
            }
    }
}

function findPosLeft(obj) {
    var curleft = 0;
    if (obj.offsetParent) {
            do {
                    curleft += obj.offsetLeft;
            } while (obj = obj.offsetParent);
            return curleft;
    }
    return undefined;
}
