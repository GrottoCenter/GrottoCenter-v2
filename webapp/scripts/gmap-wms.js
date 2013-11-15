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

function getImageMapType(wmsURL, gName, gShortName, wmsLayers, wmsStyles, wmsFormat, wmsVersion, wmsBgColor, layerCode){
    var mapType = new google.maps.ImageMapType({
        getTileUrl: function (coord, zoom) {
            var proj = map.getProjection();
            var zfactor = Math.pow(2, zoom);
            // get Long Lat coordinates
            var top = proj.fromPointToLatLng(new google.maps.Point(coord.x * 256 / zfactor, coord.y * 256 / zfactor));
            var bot = proj.fromPointToLatLng(new google.maps.Point((coord.x + 1) * 256 / zfactor, (coord.y + 1) * 256 / zfactor));

            //corrections for the slight shift of the SLP (mapserver)
            var deltaX = 0.0013;
            var deltaY = 0.00058;

            //create the Bounding box string
            var bbox =     (top.lng() + deltaX) + "," +
                           (bot.lat() + deltaY) + "," +
                           (bot.lng() + deltaX) + "," +
                           (top.lat() + deltaY);

            //base WMS URL
            var url = wmsURL;
            url += "&REQUEST=GetMap"; //WMS operation
            url += "&SERVICE=WMS";    //WMS service
            url += "&VERSION=" + wmsVersion;  //WMS version  
            url += "&LAYERS=" + wmsLayers; //WMS layers
            url += "&STYLES=" + wmsStyles;
            url += "&FORMAT=" + wmsFormat; //WMS format
            url += "&BGCOLOR=" + wmsBgColor;  
            url += "&TRANSPARENT=TRUE";
            url += "&SRS=EPSG:4326";     //set WGS84 
            url += "&BBOX=" + bbox;      // set bounding box
            url += "&WIDTH=256";         //tile size in google
            url += "&HEIGHT=256";
            return url;                 // return URL for the tile

        },
        tileSize: new google.maps.Size(256, 256),
        isPng: true,
        name: gShortName,
        alt: gName,
        maxZoom: 9
    });
    map.mapTypes.set(layerCode, mapType);
    return mapType;
}
