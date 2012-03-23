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

/** ToDo:
# ajout interactif de nouveaux systèmes pour les utilisateurs identifiés (=inscrit + session ouverte)
# visualisation des limites du système choisi sur la carte (rectangle de couleur semi-transparent)
*/
/** Done:
# CSV mode
# /!\ bug CSV quand Dest n'est pas affiché /!\
# le reload ne doit pas supprimer tous les GRS, uniquement ceux qui ne sont pas dans le nouveau pays ! (mode delta)
# filtrage des systèmes en fonction de la zone géographique
# conversion des coordonnées dans la fiche détaillée des cavités et dans les propriétés des cavités
# conversion des coordonnées lors de l'enregistrement d'une nouvelle entrée
# traduction dans toutes les langues
# requetage AJAX des CRS
# pop-up d'information concernant le type de système et ses constantes utilisées
*/
/*
  converter.class.js -- Javascript reprojection library. 
  
  Authors:      Clément Ronzon
  License:      CC by-nc as per http://creativecommons.org/licenses/by-nc/3.0/deed.en
*/
/* ======================================================================
    converter.class.js
   ====================================================================== */
/**
* GeodesicConverter Class constructor
*
*/
GeodesicConverter = function(src, dest, units, labels, HTMLWrapper, options, defs, referer, nfo, callback, readOnly) {
	/*
	* FOR WRAPPER, USE class_ INSTEAD OF class (I.E. TRICK)
	*/
	this.Units = units ? units : {'dms':{'D':'°', 'M':'\'', 'S':'\'\''},
																'dd':{'x':{'DD':'°E'}, 'y':{'DD':'°N'}},
																'xy':{'XY':'m'},
																'zxy':{'XY':'m'},
																'csv':{'CSV':'', 'L':''}};
	this.Labels = labels ? labels :{'dms':{'x':'Lng = ', 'y':'Lat = '},
																	'dd':{'x':'Lng = ', 'y':'Lat = '},
																	'xy':{'x':'X = ', 'y':'Y = '},
																	'zxy':{'x':'X = ', 'y':'Y = ', 'z':'Fuseau = ', 'e':'Emisphère = '},
																	'csv':{'csv':'CSV : ', 'l':'Format :'}};
	this.Wrapper = HTMLWrapper ? HTMLWrapper : {'converter':['div', {'class_':'unit_div'}],
																							'title':['h3'],
																							'set':['table', {'border':'0', 'cellspacing':'1', 'cellpadding':'0', 'class_':'form_tbl'}],
																							'fields':['td', {'class_':'field'}],
																							'label':['td', {'class_':'label'}],
																							'container':['tr']};
	this.Options = options ? options : {'x':{'E':'Est','W':'Ouest'},
																		 	'y':{'N':'Nord','S':'Sud'},
																			'o':{'_DMS':'Deg. min. sec. ', '_DD':'Deg. décimaux'},
																			'e':{'n':'Nord ', 's':'Sud'},
																			'f':{'c':'CSV', 'm':'Manu.'}};
	this.Defs = defs;
	this.Referer = referer;
	this.idSource = src;
	this.idDest = dest;
  this.Source = xtdGetElementById('xy'+src);
  this.crsSource = xtdGetElementById('crs'+src);
	this.Dest = dest ? xtdGetElementById('xy'+dest) : undefined;
	this.crsDest = dest ? xtdGetElementById('crs'+dest) : undefined;
	this.Proj4js = Proj4js;
	this.ProjHash = {};
	this.converter = [];
	this.WGS84 = {0:{'x':undefined, 'y':undefined}};
	this.callback = callback;
  this.nfo = nfo;
	this.readOnly = readOnly ? readOnly : false;
	this.isManual = true;
	
	this.transform = function (id) {
    var crsSource, crsDest, projSource, projDest, pointInput, pointSource, pointDest, pointDestStr, idSource, idDest, fromWGS84, idx, xy;
		if (id == undefined) return;
		fromWGS84 = ((typeof(id) != 'string') && id.x != undefined);
		if (fromWGS84) {
			pointInput = id.x.toString()+','+id.y.toString();
			projSource = this.ProjHash['WGS84'];
			projDest = this.ProjHash[this.crsSource.options[this.crsSource.selectedIndex].value];
			idDest = this.crsSource.options[this.crsSource.selectedIndex].value+'_'+this.crsSource.getAttribute('id').substr(3);
			this.WGS84[0] = new this.Proj4js.Point(pointInput);
		} else {
			if (this.idSource == id) {
				crsSource = this.crsSource;
				crsDest = this.crsDest;
			} else {
        crsSource = this.crsDest;
				crsDest = this.crsSource;
			}
			if (!crsSource) return;
			idSource = crsSource.options[crsSource.selectedIndex].value+'_'+id;
			if (crsDest) idDest = crsDest.options[crsDest.selectedIndex].value+'_'+crsDest.getAttribute('id').substr(3);
			projSource = undefined;
			if (crsSource.options[crsSource.selectedIndex].value) {
				projSource = this.ProjHash[crsSource.options[crsSource.selectedIndex].value];
			} else {
				//alert("Select a source coordinate system");
				return;
			}
			projDest = undefined;
			if (crsDest) {
				if (crsDest.options[crsDest.selectedIndex].value) {
					projDest = this.ProjHash[crsDest.options[crsDest.selectedIndex].value];
				} else {
					//alert("Select a destination coordinate system");
					return;
				}
			}
			if (this.converter[idSource]) {
				pointInput = this.converter[idSource].getXY();
			} else {
				return;
			}
		}
    if (pointInput) {
			pointInput = pointInput.split('\n');
			pointDestStr = '';
			for (idx in pointInput) {
				//Check for a valid value
				xy = pointInput[idx].split(',');
				if (pointInput[idx] == undefined || pointInput[idx] == '' || isNaN(xy[0]) || isNaN(xy[1]) || xy[0] == '' || xy[1] == '' || xy.length > 2) {
					pointDestStr = pointDestStr + 'INPUT ERROR';
					if (idx < pointInput.length-1) {
						 pointDestStr = pointDestStr + "\n";
					}
					continue;
				}
				pointSource = new this.Proj4js.Point(pointInput[idx]);
				if (!fromWGS84) {
					//Prepare the definition in case of UTM
					if (this.converter[idSource].setOriginalProj == 'zxy') { //projSource.projName == 'utm') {
						projSource.zone = this.converter[idSource].getZ(idx);
						projSource.utmSouth = (this.converter[idSource].getE(idx) == 's') ? true : false;
						projSource.deriveConstants();
						projSource.loadProjCode(projSource.projName);
					}
					//Get the WGS84 value to allow switching
					this.WGS84[idx] = this.Proj4js.transform(projSource, this.ProjHash['WGS84'], pointSource.clone()); //new this.Proj4js.Point(pointInput));
				}
				if (projDest) {
					if (this.converter[idDest].setOriginalProj == 'zxy') { //projDest.projName == 'utm') {
						projDest.zone = getUTMZone(this.WGS84[idx].x);
						projDest.utmSouth = (getEmisphere(this.WGS84[idx].y) == 's') ? true : false;
						projDest.deriveConstants();
						projDest.loadProjCode(projDest.projName);
					}
					pointDest = this.Proj4js.transform(projSource, projDest, pointSource);
					pointDestStr = pointDestStr + pointDest.x.toString() + ',' + pointDest.y.toString();
					if (idx < pointInput.length-1) {
						 pointDestStr = pointDestStr + "\n";
					}
				}
			}
			if (projDest) {
				this.converter[idDest].setXY(pointDestStr, this.WGS84);
			}
			if (fromWGS84) {
				this.transform(this.idSource);
			} else {
				if (this.callback) {
					this.callback(this.WGS84[0]);
				}
			}
    } else {
      //alert("Enter source coordinates");
      return;
    }
	}; //transform
	
  function removeAllChilds(element) {
		if (element.hasChildNodes()) {
			while (element.childNodes.length >= 1) {
				element.removeChild(element.firstChild);
			}
		}
  }
	
	this.setDefSource = function (src) {
		var newDefs, crs, flag, sourceIndex, sourceValue, destValue, destIndex;
		flag = false;
		this.Defs = src;
		//Get the actual selection
		sourceIndex = this.crsSource.selectedIndex;
		if (this.crsDest) destIndex = this.crsDest.selectedIndex;
		if (sourceIndex > -1 && destIndex > -1) {
			sourceValue = this.crsSource.options[sourceIndex].value;
			if (this.crsDest) destValue = this.crsDest.options[destIndex].value;
		}
		//load the object
		if (typeof(this.Defs) == 'object') { 
			newDefs = this.Defs;
		} else if (typeof(this.Defs) == 'string') { //Defs is an URL, load the definition via AJAX
			newDefs = eval(getResponseText(this.Defs));
		} else {
			return;
		}
		//Remove the olds that are not in the news
		for (crs in this.Proj4js.defs) {
			if (newDefs[crs] == undefined) {
				this.unloadCRS(crs.toString());
			}
		}
		//Add the news that are not in the olds
		for (crs in newDefs) {
			if (this.Proj4js.defs[crs] == undefined) {
				this.Proj4js.defs[crs] = newDefs[crs];
				this.loadCRS(crs);
				flag = true;
			}
		}
		//Sort the selects and restore the selection
		if (flag) {
			orderSelect(this.crsSource);
			if (sourceIndex > -1 && destIndex > -1) {
				setSelectedIndex(this.crsSource, sourceValue);
				if (this.crsDest) {
					orderSelect(this.crsDest);
					setSelectedIndex(this.crsDest, destValue);
				}
			}
		}
	}; //setDefSource
	
	this.loadCRS = function (def) {
		var label;
		this.ProjHash[def] = new this.Proj4js.Proj(def);
		label = (this.ProjHash[def].title ? this.ProjHash[def].title : def);
		this.crsSource.options[this.crsSource.length] = new Option(label, def);
		if (this.crsDest) this.crsDest.options[this.crsDest.length] = new Option(label, def);		
	}; //loadCRS
	
	this.unloadCRS = function (crs) {
		var removedFromSource, removedFromDest, source, dest, sourceIndex, destIndex, sourceOptns, destOptns;
		removedFromSource = false;
		removedFromDest = false;
		sourceIndex = this.crsSource.selectedIndex;
		sourceOptns = this.crsSource.options;
		if (this.crsDest) {
			destOptns = this.crsDest.options;
			destIndex = this.crsDest.selectedIndex;
		}
		if (sourceOptns != undefined && sourceIndex != undefined && sourceIndex != -1) {
			source = sourceOptns[sourceIndex].value
		}
		if (destOptns != undefined && destIndex != undefined && destIndex != -1) {
			dest = destOptns[destIndex].value
		}
		if (source != crs) {
			removeOption(this.crsSource, crs);
			removedFromSource = true;
		}
		if (dest != crs) {
			removeOption(this.crsDest, crs);
			removedFromDest = true;
		}
		if (removedFromSource && removedFromDest) {
			delete this.Proj4js.defs[crs];
			delete this.ProjHash[crs];
			delete this.converter[crs+'_'+this.idSource];
			delete this.converter[crs+'_'+this.idDest];
		}
	}; //unloadCRS
	
	this.loadingSign = function (doShow) {
		if (doShow) {
			hideId('xy' + this.idSource);
			showId('loading' + this.idSource);
			if (this.Dest) hideId('xy' + this.idDest);
			if (this.Dest) showId('loading' + this.idDest);
		} else {
			hideId('loading' + this.idSource);
			showId('xy' + this.idSource);
			if (this.Dest) hideId('loading' + this.idDest);
			if (this.Dest) showId('xy' + this.idDest);
		}
	}; //loadingSign
	
	this.unload = function () {
		this.loadingSign(true);
		removeAllChilds(this.crsSource);
		removeAllChilds(this.Source);
		if (this.crsDest) removeAllChilds(this.crsDest);
		if (this.Dest) removeAllChilds(this.Dest);
		delete this.Proj4js.defs;
		this.Proj4js.defs = {};
		delete this.ProjHash;
		this.ProjHash = {};
		this.converter = [];
		this.loadingSign(false);
	}; //unload
	
	this.reload = function (src) {
		this.loadingSign(true);
		this.setDefSource(src); //this.load(src);
		this.updateCrs(this.crsSource);
		if (this.crsDest) this.updateCrs(this.crsDest);
		this.loadingSign(false);
	}; //reload
	
	this.reset = function () {
		var crsCode;
		for (crsCode in this.converter) {
			this.converter[crsCode].reset();
		}
	}; //reset
	
  this.updateCrs = function (crs) {
  	var container, proj, scriptLocation, str, desc, units, crsTitle, crsName, crsProj, crsUnit,
				HTMLTag, HTMLTitle, id, tempTag, tempFunc, crsSource;
  	id = crs.getAttribute('id').substr(3);
		if (this.idSource == id) {
			container = this.Source;
			crsSource = (this.crsDest) ? this.idDest : this.WGS84[0];
		} else {
			container = this.Dest;
			crsSource = this.idSource;
		}
		crsName = crs.options[crs.selectedIndex].value;
    if (crsName) {
      proj = this.ProjHash[crsName];
			scriptLocation = proj.defsLookupService +'/' + proj.srsAuth +'/'+ proj.srsProjNumber;
			crsTitle = (proj.title ? proj.title : crsName); // +" ("+ crsName +")";
			crsUnit = proj.units;
      if (!this.converter[crsName+'_'+id]) {
				crsProj = (this.isManual) ? transcodeCRSProj(proj.projName) : 'csv';
				this.setCRS(crsName, id, crsProj);
			}
      removeAllChilds(container);
			tempTag = new Tag(this.Wrapper.converter);
			HTMLTag = tempTag.html;
			tempTag = new Tag(this.Wrapper.title);
			HTMLTitle = tempTag.html;
			
			eval('tempFunc = function (e) { ' + this.nfo.replace('|', crsName) + ' }');
			if (HTMLTitle.addEventListener) {
				HTMLTitle.addEventListener('click', tempFunc, false);
			} else if (HTMLTitle.attachEvent) { 
				HTMLTitle.attachEvent('onclick', tempFunc);
			}
			
			HTMLTitle.appendChild(document.createTextNode(crsTitle));
			HTMLTag.appendChild(HTMLTitle);
			HTMLTag.appendChild(this.converter[crsName+'_'+id].html);
			container.appendChild(HTMLTag);
    }
    this.transform(crsSource);
  }; //updateCrs
	
	this.setCRS = function (crsName, id, crsProj) {
  	var xy, oProj;
		//Useless : 
		//xy = (this.converter[crsName+'_'+id] != undefined) ? this.converter[crsName+'_'+id].getXY() : undefined;
		if (crsProj == 'csv') {
			oProj = transcodeCRSProj(this.ProjHash[crsName].projName);
			this.Units[crsProj].L = '';
			if (this.Labels[oProj].x != undefined) this.Units[crsProj].L = this.Labels[oProj].x.replace(' = ','') + ((this.Units[oProj].x != undefined) ? '(' + this.Units[oProj].x.DD + ')' : '(' + this.Units[oProj].XY + ')');
			if (this.Labels[oProj].y != undefined) this.Units[crsProj].L = this.Units[crsProj].L + ',' + this.Labels[oProj].y.replace(' = ','') + ((this.Units[oProj].y != undefined) ? '(' + this.Units[oProj].y.DD + ')' : '(' + this.Units[oProj].XY + ')');
			if (this.Labels[oProj].z != undefined) this.Units[crsProj].L = this.Labels[oProj].z.replace(' = ','') + ',' + this.Units[crsProj].L;
			if (this.Labels[oProj].e != undefined) this.Units[crsProj].L = this.Labels[oProj].e.replace(' = ','') + ',' + this.Units[crsProj].L;
		}
		this.converter[crsName+'_'+id] = new GeodesicFieldSet(crsName,
																								xy,
																								crsProj,
																								this.Units[crsProj], //eval('this.Units.' + crsProj),
																								this.Labels[crsProj], //eval('this.Labels.' + crsProj),
																								this.Wrapper,
																								this.Options,
																								id,
																								this.Referer,
																								this.readOnly);
  }; //setCRS
  
  this.updateDisplay = function (input) {
  	var radio, crs, crsName, id, crsProj, event;
		if (typeof(input) == 'string') { //Switch between Manual and CSV
			for (crsName in this.converter) {
				crsName = crsName.split('_')[0];
				crsProj = (this.isManual) ? transcodeCRSProj(this.ProjHash[crsName].projName) : 'csv';
				this.setCRS(crsName, this.idSource, crsProj);
				if (this.crsDest) this.setCRS(crsName, this.idDest, crsProj);
				this.updateCrs(this.crsSource);
				if (this.crsDest) this.updateCrs(this.crsDest);
			}
		} else {
			event = input;
			radio = getTargetNode(event);
			id = radio.getAttribute('name').split('_')[1]; //Source | Dest
			crsProj = radio.value; //dms | dd
			if (this.idSource == id) {
				crs = this.crsSource;
			} else {
				crs = this.crsDest;
			}
			crsName = crs.options[crs.selectedIndex].value;
			this.setCRS(crsName, id, crsProj);
			this.updateCrs(crs);
		}
	}; //updateDisplay
	
	this.setManualMode = function (isManual) {
		if (this.isManual != isManual) {
			this.isManual = isManual;
			this.reset();
			this.updateDisplay(this.idSource);
		}
	}; //setManualMode
  
	this.loadingSign(true);
	this.setDefSource(this.Defs);
	this.updateCrs(this.crsSource);
	if (this.crsDest) this.updateCrs(this.crsDest);
	this.loadingSign(false);
};
function transcodeCRSProj(projName) {
	var crsProj;
	switch (projName) {
		case 'utm':
			crsProj = 'zxy';
			break;
		case 'lcc':
		case 'tmerc':
			crsProj = 'xy';
			break;
		case 'longlat':
			crsProj = 'dd';
			break;
		case 'csv':
			crsProj = 'textarea';
			break;
		default:
			/*alert('Unknown projection name!');
			return;
			break;*/
			crsProj = 'xy';
			break;
	}
	return crsProj;
}
function is_array(input) {
	return typeof(input)=='object'&&(input instanceof Array);
}
function removeOption(oSelect, optionValue) {
  var i;
  for (i = oSelect.length - 1; i>=0; i--) {
    if (oSelect.options[i].value == optionValue) {
      oSelect.remove(i);
    }
  }
}
function getUTMZone(WGS84lng) {
	return (WGS84lng >= 0) ? Math.floor((WGS84lng + 180) / 6) + 1 : Math.floor(WGS84lng / 6) + 31;
}
function getEmisphere(WGS84Lat) {
	return (WGS84Lat >= 0) ? 'n' : 's';
}
/**
* GeodesicFieldSet Class constructor
* - proj can take the values:
*    . dms (degrees minutes seconds)
*    . dd (decimal degrees)
*    . xy (cartesian)
*/
GeodesicFieldSet = function(name, values, proj, unit, labels, HTMLWrapper, options, target, referer, readOnly) {
	var a;
	a	= values ? values.split(',') : undefined;
	this.setName = name;
	this.setValues = values ? {'x':a[0], 'y':a[1], 'e':a[2], 'z':a[3]} : {'x':undefined, 'y':undefined, 'e':undefined, 'z':undefined};
	this.setOriginalProj = (proj == 'csv') ? transcodeCRSProj(eval(referer + '.ProjHash[this.setName].projName')) : proj;
	this.setProj = proj;
	this.setUnit = unit.x ? unit : {'x':unit, 'y':unit};
	this.setLabels = labels;
	this.setId = name;
	this.setWrapper = HTMLWrapper ? HTMLWrapper : {'set':['table', {'border':'0', 'cellspacing':'1', 'cellpadding':'0', 'class':'form_tbl'}],
																									'fields':['td', {'class':'field'}],
																									'label':['td', {'class':'label'}],
																									'container':['tr']};
	this.setOptions = options;
	this.setTarget = target;
	this.setReferer = referer;
	this.setReadOnly = readOnly;
	this.initialize = function() {
		var HTMLTag, tempTag;
		tempTag = new Tag(this.setWrapper.set);
		if (this.setWrapper.set[0] == 'table') {
			tempTag = new Tag(['tbody']);
		}
		HTMLTag = tempTag.html;
		switch (this.setProj) {
			case 'dd':
			case 'dms':
				this.set = {'x':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues.x, this.setProj, this.setUnit.x, this.setLabels.x, this.setId + '_X', this.setWrapper, this.setOptions.x, this.setReferer, this.setReadOnly),
										'y':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues.y, this.setProj, this.setUnit.y, this.setLabels.y, this.setId + '_Y', this.setWrapper, this.setOptions.y, this.setReferer, this.setReadOnly),
										'o':new GeodesicField(this.setName+'_'+this.setTarget, this.setProj, 'dms_dd', this.setOptions.o, '', this.setId + '_DMS_DD', this.setWrapper, undefined, this.setReferer, this.setReadOnly)};
				HTMLTag.appendChild(this.set.y.html);
				HTMLTag.appendChild(this.set.x.html);
				HTMLTag.appendChild(this.set.o.html);
				break;
			case 'xy':
				this.set = {'x':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues.x, this.setProj, this.setUnit.x, this.setLabels.x, this.setId + '_X', this.setWrapper, this.setOptions.x, this.setReferer, this.setReadOnly),
										'y':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues.y, this.setProj, this.setUnit.y, this.setLabels.y, this.setId + '_Y', this.setWrapper, this.setOptions.y, this.setReferer, this.setReadOnly)};
				HTMLTag.appendChild(this.set.x.html);
				HTMLTag.appendChild(this.set.y.html);
				break;
			case 'zxy':
				this.set = {'e':new GeodesicField(this.setName+'_'+this.setTarget, 'n', 'e', this.setUnit.e, this.setLabels.e, this.setId + '_E', this.setWrapper, this.setOptions.e, this.setReferer, this.setReadOnly),
										'z':new GeodesicField(this.setName+'_'+this.setTarget, 31, 'z', this.setUnit.z, this.setLabels.z, this.setId + '_Z', this.setWrapper, this.setOptions.z, this.setReferer, this.setReadOnly),
										'x':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues.x, this.setProj, this.setUnit.x, this.setLabels.x, this.setId + '_X', this.setWrapper, this.setOptions.x, this.setReferer, this.setReadOnly),
										'y':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues.y, this.setProj, this.setUnit.y, this.setLabels.y, this.setId + '_Y', this.setWrapper, this.setOptions.y, this.setReferer, this.setReadOnly)};
				HTMLTag.appendChild(this.set.e.html);
				HTMLTag.appendChild(this.set.z.html);
				HTMLTag.appendChild(this.set.x.html);
				HTMLTag.appendChild(this.set.y.html);
				break;
			case 'csv':
				this.setValues = values;
				this.set = {'csv':new GeodesicField(this.setName+'_'+this.setTarget, this.setValues, this.setProj, this.setUnit.x, this.setLabels.csv, this.setId + '_CSV', this.setWrapper, undefined, this.setReferer, this.setReadOnly),
										'l':new GeodesicField(this.setName+'_'+this.setTarget, undefined, 'l', this.setUnit.x, this.setLabels.l, this.setId + '_L', this.setWrapper, undefined, this.setReferer, this.setReadOnly)};
				HTMLTag.appendChild(this.set.csv.html);
				HTMLTag.appendChild(this.set.l.html);
				break;
			default:
				return;
				break;
		}
		if (this.setWrapper.set[0] == 'table') {
			tempTag = new Tag(this.setWrapper.set);
			tempTag.html.appendChild(HTMLTag);
			HTMLTag = tempTag.html;
		}
		return HTMLTag;
	}; //initialize
	this.setX = function(value) {
		this.setValues.x = value;
		this.set.x.setValue(this.setValues.x);
	}; //setX
	this.setY = function(value) {
		this.setValues.y = value;
		this.set.y.setValue(this.setValues.y);
	}; //setY
	this.setZ = function(value) {
		if (this.set.z) {
			this.setValues.z = value;
			this.set.z.setValue(this.setValues.z);
		}
	}; //setZ
	this.setE = function(value) {
		if (this.set.e) {
			this.setValues.e = value;
			this.set.e.setValue(this.setValues.e);
		}
	}; //setE
	this.setCSV = function(value) {
		if (this.set.csv) {
			this.setValues = value;
			this.set.csv.setValue(this.setValues);
		}
	}; //setCSV
	this.setXY = function(value, WGS84) {
		var a, x, y, csv, arr, idx, csvArr;
		if (this.setProj == 'csv') {
			csvArr = [];
			arr = value.split('\n');
			for (idx in arr) {
				a = arr[idx].split(',');
				x = a[0];
				y = a[1];
				if (a.length == 2) {
					csv = '';
					if (this.setOriginalProj == 'zxy') {
						csv = getEmisphere(WGS84[idx].y).toString() + ',' + getUTMZone(WGS84[idx].x).toString() + ',';
						y = Math.abs(y);
					}
					csvArr.push(csv + x.toString() + ',' + y.toString());
				} else {
					csvArr.push(x);
				}
			}
			this.setCSV(csvArr.join('\n'));
		} else {
			a = value.split(',');
			x = a[0];
			y = a[1];
			if (this.setProj == 'zxy') {
				this.setE(getEmisphere(WGS84[0].y));
				this.setZ(getUTMZone(WGS84[0].x));
				y = Math.abs(y);
			}
			this.setX(x);
			this.setY(y);
		}
	}; //setXY
	this.getX = function() {
		return getNumber(this.set.x.getValue());
	}; //getX
	this.getY = function() {
		return getNumber(this.set.y.getValue());
	}; //getY
	this.getZ = function(idx) {
		if (this.setProj == 'csv') {
			return this.getCSV().split('\n')[idx].split(',')[1];
		} else {
			return getNumber(this.set.z.getValue());
		}
	}; //getZ
	this.getE = function(idx) {
		if (this.setProj == 'csv') {
			return this.getCSV().split('\n')[idx].split(',')[0];
		} else {
			return this.set.e.getValue();
		}
	}; //getE
	this.getCSV = function() {
		return this.set.csv.getValue();
	}; //getCSV
	this.getXY = function() {
		var X, Y, CSVArr, eltArr, idx, CSVStr;
		if (this.setProj == 'csv') {
			if (this.setOriginalProj == 'zxy') {
				CSVArr = this.getCSV().split('\n');
				for (idx in CSVArr) {
					eltArr = CSVArr[idx].split(',');
					eltArr.shift();
					eltArr.shift();
					CSVArr[idx] = eltArr.join(',');
				}
				CSVStr = CSVArr.join('\n');
			} else {
				CSVStr = this.getCSV();
			}
			return CSVStr;
		} else {
			X = this.getX();
			Y = this.getY();
			if (isNaN(X) || isNaN(Y)) {
				return;
			} else {
				return X.toString() + ',' + Y.toString();
			}
		}
	}; //getXY
	this.reset = function() {
		if (this.setProj == 'csv') {
			this.setCSV('');
		} else {
			this.setX('');
			this.setY('');
			this.setE('n');
			this.setZ(31);
		}
	}; //reset
	this.html = this.initialize();
};
function getNumber(value) {
	if (isNaN(value)) {
		return;
	} else {
		return value;
	}
}
function xtdParseFloat(value) {
	return parseFloat(value.toString().replace(/\,/gi, '.'));
}
function xtdRound(value, decimals) {
	var valStr;
	decimals = (decimals != undefined) ? decimals : 0;
	valStr = value.toString();
	if (valStr == '' || valStr == 'NaN') {
		return value;
	} else {
		return Math.round(parseFloat(value) * Math.pow(10, parseFloat(decimals))) / Math.pow(10, parseFloat(decimals));
	}
}
function dmsToDd(dmsValue) {
	var value;
	if (dmsValue == undefined) {
		return;
	}
	value = Math.abs(xtdParseFloat(dmsValue.D));
	value = value + Math.abs(xtdParseFloat(dmsValue.M)) / 60;
	value = value + Math.abs(xtdParseFloat(dmsValue.S)) / 3600;
	cardinal = (xtdParseFloat(dmsValue.D) > 0) ? 1 : -1;
	cardinal = cardinal * ((dmsValue.C == 'N' || dmsValue.C == 'E') ? 1 : -1);
	return cardinal * value;
}
function ddToDms(ddValue, ddOpts) {
  var degrees, minutes_temp, minutes, seconds, cardinal;
	if (ddValue == '' || ddValue == undefined) {
		degrees = '';
		minutes = '';
		seconds = '';
		if (ddOpts) {
			cardinal = ddOpts.N ? 'N' : 'E';
		}
	} else {
		if (ddOpts) {
			cardinal = (ddValue >= 0) ? (ddOpts.N ? 'N' : 'E') : (ddOpts.S ? 'S' : 'W');
		}
		ddValue = Math.abs(ddValue);
		degrees = Math.floor(ddValue);
		minutes_temp = (ddValue - degrees) * 60;
		minutes = Math.floor(minutes_temp);
		seconds = (minutes_temp - minutes) * 60;
	}
  return {'C':cardinal,
					'D':degrees.toString(),
					'M':minutes.toString(),
					'S':seconds.toString()};
}
/**
* GeodesicField Class constructor
* - proj can take the values:
*    . dms (degrees minutes seconds)
*    . dd (decimal degrees)
*    . xy or zxy (cartesian)
*    . z (UTM Zone)
*/
GeodesicField = function(name, value, proj, unit, label, id, HTMLWrapper, options, referer, readOnly) {
	this.geodesicName = name;
	this.geodesicValue = value;
	this.geodesicProj = proj;
	this.geodesicUnit = unit;
	this.geodesicLabel = label;
	this.geodesicId = id ? id : name;
	this.geodesicWrapper = HTMLWrapper ? HTMLWrapper : {'fields':['td', {'class':'field'}],
																											'label':['td', {'class':'label'}],
																											'container':['tr']};
	this.geodesicOptions = options;
	this.geodesicReferer = referer;
	this.geodesicReadOnly = readOnly;
	this.geodesicAttributes = {'C':{'size':'1'},
														 'D':{'size':'4'},
														 'M':{'size':'4'},
														 'S':{'size':'6'},
														 'DD':{'size':'20'},
														 'XY':{'size':'20'},
														 'Z':{'size':'5'},
														 'E':{'size':'1'},
														 'CSV':{'rows':'5', 'wrap':'off'}};
	if (this.geodesicReadOnly) {
		this.geodesicAttributes.C.disabled = 'disabled';
		this.geodesicAttributes.D.readonly = 'readonly';
		this.geodesicAttributes.D.disabled = 'disabled';
		this.geodesicAttributes.M.readonly = 'readonly';
		this.geodesicAttributes.M.disabled = 'disabled';
		this.geodesicAttributes.S.readonly = 'readonly';
		this.geodesicAttributes.S.disabled = 'disabled';
		this.geodesicAttributes.DD.readonly = 'readonly';
		this.geodesicAttributes.DD.disabled = 'disabled';
		this.geodesicAttributes.XY.readonly = 'readonly';
		this.geodesicAttributes.XY.disabled = 'disabled';
		this.geodesicAttributes.Z.readonly = 'readonly';
		this.geodesicAttributes.Z.disabled = 'disabled';
		this.geodesicAttributes.E.disabled = 'disabled';
		this.geodesicAttributes.CSV.readonly = 'readonly';
		this.geodesicAttributes.CSV.disabled = 'disabled';
	}
	this.initialize = function() {
		var HTMLTag, geoName, geoField, HTMLcell, HTMLfields, HTMLLabel, tempTag, csvMsg;
		tempTag = new Tag(this.geodesicWrapper.container);
		HTMLTag = tempTag.html;
		switch (this.geodesicProj) {
			case 'dms':
				this.geodesicValue = ddToDms(this.geodesicValue, this.geodesicOptions);
				this.geodesicFields = {'C':new Field(this.geodesicName + '_C', 'option', this.geodesicValue.C ? this.geodesicValue.C : '', this.geodesicAttributes.C, this.geodesicOptions),
															'D':new Field(this.geodesicName + '_D', 'text', isNaN(this.geodesicValue.D) ? '' : xtdRound(this.geodesicValue.D, 0), this.geodesicAttributes.D),
															'M':new Field(this.geodesicName + '_M', 'text', isNaN(this.geodesicValue.M) ? '' : xtdRound(this.geodesicValue.M, 0), this.geodesicAttributes.M),
															'S':new Field(this.geodesicName + '_S', 'text', isNaN(this.geodesicValue.S) ? '' : xtdRound(this.geodesicValue.S, 12), this.geodesicAttributes.S)};
				break;
			case 'dd':
				this.geodesicFields = {'DD':new Field(this.geodesicName + '_DD', 'text', isNaN(this.geodesicValue) ? '' : xtdRound(this.geodesicValue, 15), this.geodesicAttributes.DD)};
				break;
			case 'dms_dd':
				this.geodesicFields = {'_DMS':new Field(this.geodesicName + '_DMS_DD', 'radio', 'dms', {'onclick':this.geodesicReferer+'.updateDisplay(e);'}),
															 '_DD':new Field(this.geodesicName + '_DMS_DD', 'radio', 'dd', {'onclick':this.geodesicReferer+'.updateDisplay(e);'})};
				this.geodesicFields['_'+this.geodesicValue.toUpperCase()].html.setAttribute('checked', 'checked');
				this.geodesicFields['_'+this.geodesicValue.toUpperCase()].html.checked = true;
				this.geodesicFields['_'+this.geodesicValue.toUpperCase()].html.defaultChecked = true;
				/*eval('this.geodesicFields._'+this.geodesicValue.toUpperCase()+'.html.setAttribute(\'checked\', \'checked\')');
				eval('this.geodesicFields._'+this.geodesicValue.toUpperCase()+'.html.checked = true');
				eval('this.geodesicFields._'+this.geodesicValue.toUpperCase()+'.html.defaultChecked = true');*/
				break;
			case 'zxy':
			case 'xy':
				this.geodesicFields = {'XY':new Field(this.geodesicName + '_XY', 'text', isNaN(this.geodesicValue) ? '' : xtdRound(this.geodesicValue, 3), this.geodesicAttributes.XY)};
				break;
			case 'z':
				this.geodesicFields = {'Z':new Field(this.geodesicName + '_Z', 'text', isNaN(this.geodesicValue) ? '' : xtdRound(this.geodesicValue, 0), this.geodesicAttributes.Z)};
				break;
			case 'e':
				this.geodesicFields = {'E':new Field(this.geodesicName + '_E', 'option', this.geodesicValue ? this.geodesicValue : '', this.geodesicAttributes.E, this.geodesicOptions)};
				break;
			case 'csv':
				this.geodesicFields = {'CSV':new Field(this.geodesicName + '_CSV', 'textarea', this.geodesicValue ? this.geodesicValue : '', this.geodesicAttributes.CSV)};
				break;
			case 'l':
				this.geodesicFields = {'L':new Tag(['span'])};
				break;
			default:
				return;
				break;
		}
		//Wrapp the fields
		tempTag = new Tag(this.geodesicWrapper.label);
		HTMLLabel = tempTag.html;
		HTMLLabel.appendChild(document.createTextNode(this.geodesicLabel));
		HTMLTag.appendChild(HTMLLabel);
		tempTag = new Tag(this.geodesicWrapper.fields);
		HTMLcell = tempTag.html;
		for (geoName in this.geodesicFields) {
			geoField = this.geodesicFields[geoName]; //eval('this.geodesicFields.' + geoName);
			tempTag = new Tag(['label', {'for_':this.geodesicFields[geoName].fieldId}]);//eval('this.geodesicFields.' + geoName + '.fieldId')}]);
			HTMLfields = tempTag.html;
			HTMLfields.appendChild(geoField.html);
			if (this.geodesicUnit) {
				if (this.geodesicUnit[geoName]) { //eval('this.geodesicUnit.' + geoName)) {
					HTMLfields.appendChild(document.createTextNode(this.geodesicUnit[geoName]));//eval('this.geodesicUnit.' + geoName)));
				}
			}
			HTMLfields.appendChild(document.createTextNode(' '));
			HTMLcell.appendChild(HTMLfields);
			HTMLTag.appendChild(HTMLcell);
		}
		return HTMLTag;
	}; //initialize
	this.setValue = function (value) {
		this.geodesicValue = value;
		switch (this.geodesicProj) {
			case 'dms':
				this.geodesicValue = ddToDms(this.geodesicValue, this.geodesicOptions);
				this.geodesicFields.C.fieldValue = (this.geodesicValue.C != undefined) ? this.geodesicValue.C : '';
				this.geodesicFields.C.refresh();
				this.geodesicFields.D.fieldValue = isNaN(this.geodesicValue.D) ? '' : xtdRound(this.geodesicValue.D, 0);
				this.geodesicFields.D.refresh();
				this.geodesicFields.M.fieldValue = isNaN(this.geodesicValue.M) ? '' : xtdRound(this.geodesicValue.M, 0);
				this.geodesicFields.M.refresh();
				this.geodesicFields.S.fieldValue = isNaN(this.geodesicValue.S) ? '' : xtdRound(this.geodesicValue.S, 12);
				this.geodesicFields.S.refresh();
				break;
			case 'dd':
				this.geodesicFields.DD.fieldValue = isNaN(this.geodesicValue) ? '' : xtdRound(this.geodesicValue, 15);
				this.geodesicFields.DD.refresh();
				break;
			case 'zxy':
			case 'xy':
				this.geodesicFields.XY.fieldValue = isNaN(this.geodesicValue) ? '' : xtdRound(this.geodesicValue, 3);
				this.geodesicFields.XY.refresh();
				break;
			case 'e':
				this.geodesicFields.E.fieldValue = (this.geodesicValue != undefined) ? this.geodesicValue : '';
				this.geodesicFields.E.refresh();
				break;
			case 'z':
				this.geodesicFields.Z.fieldValue = isNaN(this.geodesicValue) ? '' : xtdRound(this.geodesicValue, 0);
				this.geodesicFields.Z.refresh();
				break;
			case 'csv':
				this.geodesicFields.CSV.fieldValue = this.geodesicValue;
				this.geodesicFields.CSV.refresh();
				break;
			default:
				return;
				break;
		}
	}; //setValue
	this.getValue = function() {
		var value;
		switch (this.geodesicProj) {
			case 'dms':
				this.geodesicValue = {'C':this.geodesicFields.C.getValue(),
															'D':xtdParseFloat(this.geodesicFields.D.getValue()),
															'M':xtdParseFloat(this.geodesicFields.M.getValue()),
															'S':xtdParseFloat(this.geodesicFields.S.getValue())};
				value = dmsToDd(this.geodesicValue);
				break;
			case 'dd':
				this.geodesicValue = xtdParseFloat(this.geodesicFields.DD.getValue());
				value = this.geodesicValue;
				break;
			case 'zxy':
			case 'xy':
				this.geodesicValue = xtdParseFloat(this.geodesicFields.XY.getValue());
				value = this.geodesicValue;
				break;
			case 'e':
				this.geodesicValue = this.geodesicFields.E.getValue();
				value = this.geodesicValue;
				break;
			case 'z':
				this.geodesicValue = xtdParseFloat(this.geodesicFields.Z.getValue());
				value = this.geodesicValue;
				break;
			case 'csv':
				this.geodesicValue = this.geodesicFields.CSV.getValue();
				value = this.geodesicValue;
				break;
			default:
				return;
				break;
		}
		return value;
	}; //getValue
	this.html = this.initialize();
};
/**
* Field Class constructor
*
*/
Field = function(name, type, value, attributes, options) {
	this.fieldName = name;
	this.fieldId = name + '_id_' + Math.floor(Math.random()*10001);
	this.fieldType = type;
	this.fieldValue = value ? value : '';
	this.fieldAttributes = attributes ? attributes : {};
	this.fieldOptions = options;
	switch (this.fieldType) {
		case 'text':
		case 'radio':
			this.fieldTagName = 'input';
			break;
		case 'option':
			this.fieldTagName = 'select';
			break;
		case 'textarea':
			this.fieldTagName = 'textarea';
			break;
		default:
			this.fieldTagName = 'input';
			break;
	}
	this.initialize = function() {
		var HTMLTag;
		this.fieldAttributes.name = this.fieldName;
		switch (this.fieldType) {
			case 'option':
				HTMLTag = new Tag([this.fieldTagName, this.fieldAttributes, undefined, this.fieldOptions, this.fieldValue]);
				break;
			case 'textarea':
				HTMLTag = new Tag([this.fieldTagName, this.fieldAttributes, this.fieldValue]);
				break;
			default:
				this.fieldAttributes.type = this.fieldType;
				this.fieldAttributes.value = this.fieldValue;
				this.fieldAttributes.id = this.fieldId;
				HTMLTag = new Tag([this.fieldTagName, this.fieldAttributes]);
				break;
		}
		return HTMLTag;
	}; //initialize
	this.refresh = function() {
		this.field.tagName = this.fieldTagName;
		this.fieldAttributes.name = this.fieldName;
		switch (this.fieldType) {
			case 'option':
				this.field.tagSelected = this.fieldValue;
				break;
			case 'textarea':
				this.fieldAttributes.value = this.fieldValue;
				this.fieldAttributes.id = this.fieldId;
				break;
			default:
				this.fieldAttributes.type = this.fieldType;
				this.fieldAttributes.value = this.fieldValue;
				this.fieldAttributes.id = this.fieldId;
				break;
		}
		this.field.tagAttributes = this.fieldAttributes;
		this.field.refresh();
	}; //refresh
	this.getValue = function() {
		switch (this.fieldType) {
			case 'option':
				this.fieldValue = this.html.options[this.html.selectedIndex].value;
				break;
			default:
				this.fieldValue = this.html.value;
				break;
		}
		return this.fieldValue;
	}; //getValue
	this.field = this.initialize();
	this.html = this.field.html;
};

/**
* Tag Class constructor
* array should be: name, attributes, text
*/
Tag = function(array) {
	this.tagName = array[0]; //name;
	this.tagAttributes = array[1] ? array[1] : {}; //attributes ? attributes : {};
	this.tagText = array[2] ? array[2] : ''; //text ? text : '';
	this.tagOptions = array[3] ? array[3] : []; //options ? options : [];
	this.tagSelected = array[4] ? array[4] : ''; //selected ? selected : '';
	this.initialize = function() {
		var HTMLTag, attName, attValue, optValue, optText, opts, tempTag, tempFunc;
		if (this.tagName === '') {
			return;
		}
		HTMLTag = document.createElement(this.tagName);
		for (attName in this.tagAttributes) {
			if (attName != undefined) {
				attValue = this.tagAttributes[attName.toString()]; //eval('this.tagAttributes.' + attName.toString());
				if (attValue != undefined) {
					if (attName.toString().substring(0, 2) == 'on') {
						eval('tempFunc = function (e) { '+attValue+' }');
						if (HTMLTag.addEventListener) {
							HTMLTag.addEventListener(attName.toString().substr(2), tempFunc, false);
						} else if (HTMLTag.attachEvent) { 
							HTMLTag.attachEvent(attName.toString(), tempFunc);
						}
					} else {
						// Prenvent scientific notation due to the Number.toString() method:
						if (attName == 'value' && typeof(attValue) == 'number' && attValue.toString().split('e').length > 1) {
							attValue = xtdRound(attValue);
						}
						HTMLTag.setAttribute(attName.toString().split('_').join(''), attValue.toString());
						eval('HTMLTag.' + attName.toString().split('_').join('Name') + ' = attValue.toString()');
					}
				}
			}
		}
		if (this.tagName === 'select') {
			for (optValue in this.tagOptions) {
				optText = this.tagOptions[optValue]; //eval('this.tagOptions.' + optValue);
				opts = {'value':optValue};
				if (optValue == this.tagSelected) {
					opts.selected = 'selected';
				}
				tempTag = new Tag(['option', opts, optText]);
				HTMLTag.appendChild(tempTag.html);
			}
		}
		if (this.tagText !== '') {
			HTMLTag.appendChild(document.createTextNode(this.tagText));
		}
		if (this.tagAttributes.type) {
			if (this.tagAttributes.type == 'radio' || this.tagAttributes.type == 'checkbox') {
				HTMLTag.setAttribute('style', 'border: 0px none;');
				HTMLTag.style.border = '0px none';
			}
		}
		return HTMLTag;
	}; //initialize
	this.refresh = function() {
		var HTMLTag, attName, attValue, optValue, optText, opts, tempTag, tempFunc;
		if (this.tagName === '') {
			return;
		}
		HTMLTag = this.html;
		while (HTMLTag.hasChildNodes()) {
			HTMLTag.removeChild(HTMLTag.firstChild);
		}
		for (attName in this.tagAttributes) {
			if (attName != undefined) {
				attValue = this.tagAttributes[attName]; //eval('this.tagAttributes.' + attName);
				if (attValue != undefined) {
					if (attName.toString().substring(0, 2) == 'on') {
						eval('tempFunc = function (e) { '+attValue+' }');
						if (HTMLTag.addEventListener) {
							HTMLTag.addEventListener(attName.toString().substr(2), tempFunc, false);
						} else if (HTMLTag.attachEvent) { 
							HTMLTag.attachEvent(attName.toString(), tempFunc);
						} 
					} else {
						// Prenvent scientific notation due to the Number.toString() method:
						if (attName == 'value' && typeof(attValue) == 'number' && attValue.toString().split('e').length > 1) {
							attValue = xtdRound(attValue);
						}
						try {
						  HTMLTag.setAttribute(attName.toString().split('_').join(''), attValue.toString());
						} catch (err) { } //IE BUG
						eval('HTMLTag.' + attName.toString().split('_').join('Name') + ' = attValue.toString()');
					}
				}
			}
		}
		if (this.tagName === 'select') {
			for (optValue in this.tagOptions) {
				optText = this.tagOptions[optValue]; //eval('this.tagOptions.' + optValue);
				opts = {'value':optValue};
				if (optValue == this.tagSelected) {
					opts.selected = 'selected';
				}
				tempTag = new Tag(['option', opts, optText]);
				HTMLTag.appendChild(tempTag.html);
			}
		}
		if (this.tagText !== '') {
			HTMLTag.appendChild(document.createTextNode(this.tagText));
		}
		if (this.tagAttributes.type) {
			if (this.tagAttributes.type == 'radio' || this.tagAttributes.type == 'checkbox') {
				HTMLTag.setAttribute('style', 'border: 0px none;');
				HTMLTag.style.border = '0px none';
			}
		}
		this.html = HTMLTag;
	}; //refresh
	this.html = this.initialize();
};