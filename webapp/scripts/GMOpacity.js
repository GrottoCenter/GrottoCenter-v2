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

/*
 * Opacity GControl by Klokan Petr Pridal (based on XSlider of Mike Williams)
 * http://www.maptiler.org/google-maps-overlay-opacity-control/ *  
 */
 
function OpacityControl(title) {
  this.title = title;
};

function load_GMOpacity() {
  OpacityControl.prototype = new google.maps.Control();

  // This function positions the slider to match the specified opacity
  OpacityControl.prototype.setSlider = function(pos) {
    var left = Math.round((58*pos));
    this.slide.left = left;
    this.knob.style.left = left+"px";
    this.knob.style.top = "0px"; // correction001
  };
  
  // This function reads the slider and sets the overlay opacity level
  OpacityControl.prototype.setOpacity = function() {
    //this.overlay.getTileLayer().opacity = this.slide.left/58;
    /*this.overlay.getTileLayers()[1].opacity = this.slide.left/58;
    this.map.removeOverlay(this.overlay);
    this.map.addOverlay(this.overlay);*/
    setLayersOpacity(this.slide.left/58);
  };
  
  // This gets called by the API when addControl(new OpacityControl())
  OpacityControl.prototype.initialize = function(map) {
  
    var that = this;
    this.map = map;
  
    // Is this MSIE, if so we need to use AlphaImageLoader
    var agent = navigator.userAgent.toLowerCase();
    if ((agent.indexOf("msie") > -1) && (agent.indexOf("opera") < 1)){this.ie = true} else {this.ie = false}
  
    // create the background graphic as a <div> containing an image
    var container = document.createElement("div");
    container.style.width="70px";
    container.style.height="21px";
    container.title = this.title;
  
    // Handle transparent PNG files in MSIE
    if (this.ie) {
      var loader = "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader("+
        "src='../images/icons/opacity-slider.png', sizingMethod='crop');";
      container.innerHTML = '<div style="height:21px; width:70px; ' +loader+ '" ></div>';
    } else {
      container.innerHTML = '<div style="height:21px; width:70px; background-image:url(../images/icons/opacity-slider.png)" ></div>';
    }
  
    // create the knob as a GDraggableObject
    // Handle transparent PNG files in MSIE
    if (this.ie) {
      var loader = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../images/icons/opacity-slider.png', sizingMethod='crop');";
      this.knob = document.createElement("div");
      this.knob.style.height="21px";
      this.knob.style.width="13px";
      this.knob.style.overflow="hidden";
      this.knob_img = document.createElement("div");
      this.knob_img.style.height="21px";
      this.knob_img.style.width="83px";
      this.knob_img.style.filter=loader;
      this.knob_img.style.position="relative";
      this.knob_img.style.left="-70px";
      this.knob.appendChild(this.knob_img);
    } else {
      this.knob = document.createElement("div");
      this.knob.style.height="21px";
      this.knob.style.width="13px";
      this.knob.style.backgroundImage="url(../images/icons/opacity-slider.png)";
      this.knob.style.backgroundPosition="-70px 0px";
    }
    container.appendChild(this.knob);
    this.slide = new google.maps.DraggableObject(this.knob, {container:container});
    this.slide.setDraggableCursor('pointer');
    this.slide.setDraggingCursor('pointer');
    this.container = container;
  
    // attach the control to the map
    map.getContainer().appendChild(container);
  
    // init slider
    //this.setSlider( this.overlay.getTileLayer().opacity );
    //this.setSlider(map.getCurrentMapType().getTileLayers()[1].getOpacity());
    this.setSlider(layersOpacity);
  
    // Listen for the slider being moved and set the opacity
    google.maps.Event.addListener(this.slide, "dragend", function() {that.setOpacity()});
    //google.maps.Event.addListener(this.container, "click", function( x, y ) { alert(x, y) });
  
    return container;
  };
  
  // Set the default position for the control
  OpacityControl.prototype.getDefaultPosition = function() {
    return new google.maps.ControlPosition(G_ANCHOR_TOP_RIGHT, new google.maps.Size(371, 7));
  };
}