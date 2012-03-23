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

/* similar to equi.js FIXME proj4 uses eqc */
Proj4js.Proj.eqc = {
  init : function() {

      if(!this.x0) this.x0=0;
      if(!this.y0) this.y0=0;
      if(!this.lat0) this.lat0=0;
      if(!this.long0) this.long0=0;
      if(!this.lat_ts) this.lat_ts=0;
      if (!this.title) this.title = "Equidistant Cylindrical (Plate Carre)";

      this.rc= Math.cos(this.lat_ts);
    },


    // forward equations--mapping lat,long to x,y
    // -----------------------------------------------------------------
    forward : function(p) {

      var lon= p.x;
      var lat= p.y;

      var dlon = Proj4js.common.adjust_lon(lon - this.long0);
      var dlat = Proj4js.common.adjust_lat(lat - this.lat0 );
      p.x= this.x0 + (this.a*dlon*this.rc);
      p.y= this.y0 + (this.a*dlat        );
      return p;
    },

  // inverse equations--mapping x,y to lat/long
  // -----------------------------------------------------------------
  inverse : function(p) {

    var x= p.x;
    var y= p.y;

    p.x= Proj4js.common.adjust_lon(this.long0 + ((x - this.x0)/(this.a*this.rc)));
    p.y= Proj4js.common.adjust_lat(this.lat0  + ((y - this.y0)/(this.a        )));
    return p;
  }

};
