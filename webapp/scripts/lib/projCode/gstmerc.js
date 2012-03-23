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

Proj4js.Proj.gstmerc = {
  init : function() {

    // array of:  a, b, lon0, lat0, k0, x0, y0
      var temp= this.b / this.a;
      this.e= Math.sqrt(1.0 - temp*temp);
      this.lc= this.long0;
      this.rs= Math.sqrt(1.0+this.e*this.e*Math.pow(Math.cos(this.lat0),4.0)/(1.0-this.e*this.e));
      var sinz= Math.sin(this.lat0);
      var pc= Math.asin(sinz/this.rs);
      var sinzpc= Math.sin(pc);
      this.cp= Proj4js.common.latiso(0.0,pc,sinzpc)-this.rs*Proj4js.common.latiso(this.e,this.lat0,sinz);
      this.n2= this.k0*this.a*Math.sqrt(1.0-this.e*this.e)/(1.0-this.e*this.e*sinz*sinz);
      this.xs= this.x0;
      this.ys= this.y0-this.n2*pc;

      if (!this.title) this.title = "Gauss Schreiber transverse mercator";
    },


    // forward equations--mapping lat,long to x,y
    // -----------------------------------------------------------------
    forward : function(p) {

      var lon= p.x;
      var lat= p.y;

      var L= this.rs*(lon-this.lc);
      var Ls= this.cp+(this.rs*Proj4js.common.latiso(this.e,lat,Math.sin(lat)));
      var lat1= Math.asin(Math.sin(L)/Proj4js.common.cosh(Ls));
      var Ls1= Proj4js.common.latiso(0.0,lat1,Math.sin(lat1));
      p.x= this.xs+(this.n2*Ls1);
      p.y= this.ys+(this.n2*Math.atan(Proj4js.common.sinh(Ls)/Math.cos(L)));
      return p;
    },

  // inverse equations--mapping x,y to lat/long
  // -----------------------------------------------------------------
  inverse : function(p) {

    var x= p.x;
    var y= p.y;

    var L= Math.atan(Proj4js.common.sinh((x-this.xs)/this.n2)/Math.cos((y-this.ys)/this.n2));
    var lat1= Math.asin(Math.sin((y-this.ys)/this.n2)/Proj4js.common.cosh((x-this.xs)/this.n2));
    var LC= Proj4js.common.latiso(0.0,lat1,Math.sin(lat1));
    p.x= this.lc+L/this.rs;
    p.y= Proj4js.common.invlatiso(this.e,(LC-this.cp)/this.rs);
    return p;
  }

};
