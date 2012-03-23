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

function GCControls() {};

function load_GCControls() {
  GCControls.prototype = new google.maps.Control();
  
  // This gets called by the API when addControl(new GCControls())
  GCControls.prototype.initialize = function(map) {
    var that, container, refreshDiv, refreshButton, printDiv, i;
    
    that = this;
    this.map = map;
    // Create the main container
    container = document.createElement("div");
    container.style.font = "small Arial";
    container.style.height = "19px";
    container.style.width = "90px";
    // Create the print button
    printDiv = document.createElement("div");
    printDiv.style.position = "absolute";
    printDiv.style.right = "67px";
    printDiv.style.cursor = "pointer";
    container.appendChild(printDiv);
    printDiv.innerHTML = xtdGetElementById('printerContainer').innerHTML;
    // Create the refresh button
    refreshDiv = document.createElement("div");
    this.setButtonContainerStyle_(refreshDiv);
    refreshDiv.style.right = "0px";
    refreshButton = document.createElement("div");
    this.setButtonStyle_(refreshButton);
    container.appendChild(refreshDiv);
    refreshDiv.appendChild(refreshButton);
    refreshButton.innerHTML = xtdGetElementById('reloadContainer').innerHTML;
    // Listen for the buttons clicked
    google.maps.Event.addDomListener(refreshButton, "click", function() {
      reload();
    });
    google.maps.Event.addDomListener(printDiv, "click", function() {
      window.print();
    });
    // Set the accessor
    this.container = container;
    // Attach the control to the map
    map.getContainer().appendChild(container);
    return container;
  };
  
  // Set the default position for the control
  GCControls.prototype.getDefaultPosition = function() {
    return new google.maps.ControlPosition(G_ANCHOR_TOP_RIGHT, new google.maps.Size(278, 7));
  };
  
  // Sets the proper CSS for the given button element.
  GCControls.prototype.setButtonStyle_ = function(button) {
    button.style.borderBottom = "1px solid #B0B0B0";
    button.style.borderLeft = "1px solid white";
    button.style.borderRight = "1px solid #B0B0B0";
    button.style.borderTop = "1px solid white";
    button.style.font = "12px Arial";
  };
  
  // Sets the proper CSS for the given button element.
  GCControls.prototype.setButtonContainerStyle_ = function(buttonContainer) {
    buttonContainer.style.backgroundColor = "white";
    buttonContainer.style.border = "1px solid black";
    buttonContainer.style.cursor = "pointer";
    buttonContainer.style.textalign = "center";
    buttonContainer.style.width = "65px";
    buttonContainer.style.position = "absolute";
  };
}