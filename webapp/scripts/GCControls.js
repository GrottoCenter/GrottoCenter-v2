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

function GCControls(container, map) {
    var refreshDiv, refreshButton, printDiv;
    
    // Create the main container
    container.style.font = "small Arial";
    container.style.height = "19px";
    container.style.width = "90px";
    container.style.margin = "5px";
    // Create the print button
    printDiv = document.createElement("div");
    printDiv.style.position = "absolute";
    printDiv.style.right = "67px";
    printDiv.style.cursor = "pointer";
    container.appendChild(printDiv);
    printDiv.innerHTML = xtdGetElementById('printerContainer').innerHTML;
    // Create the refresh button
    refreshDiv = document.createElement("div");
    refreshDiv.style.backgroundColor = "white";
    refreshDiv.style.cursor = "pointer";
    refreshDiv.style.textalign = "center";
    refreshDiv.style.width = "65px";
    refreshDiv.style.position = "absolute";
    refreshDiv.style.right = "0px";
    refreshDiv.style.color = "#565656";

    refreshButton = document.createElement("div");
    
    refreshButton.style.border = "1px solid rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) -moz-use-text-color";
    refreshButton.style.padding = "1px 6px";
    refreshButton.style.WebkitBorderRadius = "2px";
    refreshButton.style.borderRadius = "2px";
    refreshButton.style.boxShadow = "0 1px 4px -1px rgba(0, 0, 0, 0.3)";
    refreshButton.style.font = "11px Roboto,Arial,sans-serif";
    
    container.appendChild(refreshDiv);
    refreshDiv.appendChild(refreshButton);
    refreshButton.innerHTML = xtdGetElementById('reloadContainer').innerHTML;
    // Listen for the buttons clicked
    google.maps.event.addListener(refreshButton, "click", function(event) {
        window.location.reload();
    });
    google.maps.event.addListener(printDiv, "click", function() {
      window.print();
    });
    
}