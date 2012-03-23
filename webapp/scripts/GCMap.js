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

function GCMarker(latlng, options) {
  this.objectId = options.id;
  this.category = options.category;
  this.address = options.address || "";
  this.city = options.city || "";
  this.region = options.region || "";
  this.caversurname = options.caversurname || "";
  this.cavername = options.cavername || "";
  this.caverlogin = options.caverlogin || "";
  this.country = options.country || "<?php echo Select_default; ?>";
  this.massif = options.massifId || "<?php echo Select_default; ?>";
  this.cave = options.caveId || "<?php echo Select_default; ?>";
  this.inscriptionDate = Date.parse(options.inscriptionDate) || undefined;
  this.reviewedDate = Date.parse(options.reviewedDate) || undefined;
  this.isConnected = options.isConnected || false;
  this.markerType = options.markerType || "m";
  google.maps.Marker.apply(this, arguments);
}


function GCPolyline(latlngs, color, weight, opacity, options) {
  this.cat1 = options.cat1;
  this.cat2 = options.cat2;
  this.id1 = options.id1;
  this.id2 = options.id2;
  GPolyline.apply(this, arguments);
}

function load_GCMap() {
  GCMarker.prototype = new google.maps.Marker(new google.maps.LatLng(0, 0));
  GCPolyline.prototype = new google.maps.Polyline([new google.maps.LatLng(0, 0), new google.maps.LatLng(0, 0)]);  
}