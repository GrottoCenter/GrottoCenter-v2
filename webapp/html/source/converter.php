<?php
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
$got_part = (isset($_GET['p'])) ? $_GET['p'] : '';
$part = (isset($part)) ? $part : $got_part;
$parted = ($part != '');
$conv_lat = (isset($_GET['lat'])) ? $_GET['lat'] : 0;
$conv_lng = (isset($_GET['lng'])) ? $_GET['lng'] : 0;
$conv_length = (isset($_GET['len'])) ? $_GET['len'] : 0;
$conv_temp = (isset($_GET['temp'])) ? $_GET['temp'] : 0;
$conv_lat = (!$parted) ? $conv_lat : "";
$conv_lng = (!$parted) ? $conv_lng : "";
$conv_length = (!$parted) ? $conv_length : "";
$conv_temp = (!$parted) ? $conv_temp : "";
$got_converter = (isset($_GET['c'])) ? $_GET['c'] : '';
$converter = (isset($converter)) ? $converter : $got_converter;
$selected = ($converter != '');
$show_coords_conv = (isset($show_coords_conv)) ? $show_coords_conv : false;
$got_geodesic = (isset($_GET['g'])) ? $_GET['g'] : '';
$geodesic = (isset($geodesic)) ? $geodesic : $got_geodesic;
$geodesic = urldecode(stripslashes($geodesic));
$readonly = (isset($_GET['readonly'])) ? ($_GET['readonly'] == 'true' ? 'true' : 'false') : 'false';
$conv_iso = (isset($_GET['i'])) ? $_GET['i'] : '';
?>
<?php if(!$parted) { ?>
<?php
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" charset="UTF-8" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=243<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/details.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php } ?>
<?php if ($part == "js" || !$parted) { ?>
		<script type="text/javascript" src="../scripts/lib/proj4js-combined.js"></script>
		<script type="text/javascript" src="../scripts/lib/proj4js-combined.CRO.1.0.0.js"></script>
		<!--script type="text/javascript" src="../scripts/converter.class.js"></script-->
		<script type="text/javascript" src="../scripts/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../scripts/converter.class.2.0.1.js"></script>
    <script type="text/javascript" charset="UTF-8">
    <?php echo getCDataTag(true); ?>
    //Gona need those functions : 
		//resetDetails, strToFloat, switchDOM, getTargetNode, openWindow, getResponseText,
		//showId, hideId, setSelectedIndex, orderSelect
		var converterHash, convUnits, convLabels, convWrapper, convOptions, convDefs, convSrc, convDest, convNfo, convCallBack, convReadOnly, firstCallBack;
		mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
		convUnits = {'dms':{'D':'<convert>#label=290<convert>', 'M':"<convert>#label=291<convert>", 'S':'<convert>#label=292<convert>'},
									'dd':{'x':{'DD':'<convert>#label=290<convert><convert>#label=294<convert>'}, 'y':{'DD':'<convert>#label=290<convert><convert>#label=293<convert>'}},
									'xy':{'XY':{'m':'<convert>#label=268<convert>', 'km':'<convert>#label=271<convert>'}},
									'zxy':{'XY':{'m':'<convert>#label=268<convert>', 'km':'<convert>#label=271<convert>'}},
									'csv':{'CSV':'', 'L':''}};
		convLabels = {'dms':{'x':'<convert>#label=288<convert> ', 'y':'<convert>#label=287<convert> '},//Lng = //Lat =
									'dd':{'x':'<convert>#label=288<convert> ', 'y':'<convert>#label=287<convert> '},//Lng = //Lat =
									'xy':{'x':'<convert>#label=298<convert> ', 'y':'<convert>#label=299<convert> '},//X = //Y = 
									'zxy':{'x':'<convert>#label=298<convert> ', 'y':'<convert>#label=299<convert> ', 'z':'<convert>#label=305<convert> ', 'e':'<convert>#label=304<convert> '},
									'csv':{'csv':'<convert>#label=922<convert>', 'l':'<convert>#label=923<convert>'}};
		convOptions = {'x':{'E':'<convert>#label=294<convert>','W':'<convert>#label=296<convert>'},
									 'y':{'N':'<convert>#label=293<convert>','S':'<convert>#label=295<convert>'},
									 'o':{'_DMS':'<convert>#label=891<convert> ', '_DD':'<convert>#label=892<convert>'},
									 'e':{'n':'<convert>#label=893<convert>', 's':'<convert>#label=894<convert>'},
									 'f':{'c':'<convert>#label=921<convert>', 'm':'<convert>#label=920<convert>'},
									 'u':{'_M':'<convert>#label=925<convert>', '_KM':'<convert>#label=926<convert>'}};
		convWrapper = {'converter':['div', {'class':'unit_div'}],
										'title':['h3'],
										'set':['table', {'border':'0', 'cellspacing':'1', 'cellpadding':'0', 'class':'form_tbl'}],
										'fields':['td', {'class':'field', 'width':'100%'}],
										'label':['td', {'class':'label'}],
										'container':['tr']};
		convDefs = "<?php echo $_SESSION['Application_url']; ?>/html/webservices/getCrsJson.php?iso=";
		convSrc = 'Source';
		convDest = 'Dest';
		convNfo = "JavaScript:openWindow('crs_info_<?php echo $_SESSION['language']; ?>.php?s=' + encodeURI(encodeURIComponent('|')), '', 500, 400);";
		firstCallBack = true;
		convReadOnly = <?php echo $readonly ?>;
<?php if($parted) { ?>
		function countryOnChange(oSelect) {
			var iso, auto;
			auto = false;
			if (typeof oSelect == 'string') { //called by setCountry()
				auto = true;
				iso = oSelect;
				oSelect = document.coords_converter_form.conv_country;
			} else { //called by user (select conv_country)
				iso = oSelect.options[oSelect.selectedIndex].value;
			}
			if (iso != undefined) {
				if (oSelect.options[oSelect.selectedIndex].value != iso || !auto) {
					if (auto) setSelectedIndex(oSelect, iso);
					converterHash.reload(convDefs + encodeURI(encodeURIComponent(iso)));
				}
			}
		}
		
    function setCountry(lat, lng) {
      var point;
			if (lat != undefined && lng != undefined) {
				point = new mySite.overview.google.maps.LatLng(lat, lng);
        mySite.overview.geocoder.getLocations(point, function (response) {
					var places, place, iso;
					places = response.Placemark;
					if (places != undefined) {
						place = places[0];
						iso = place.AddressDetails.Country.CountryNameCode;
						countryOnChange(iso);
					} else {
						countryOnChange('<?php echo Select_default; ?>');
					}
        });
      }
    }
<?php } ?>
		function loadConverter(iso, callback) {
<?php if(!$parted) { ?>
			convDest = undefined;
<?php } ?>
			$('.to-remove').remove();
			convCallBack = function (WGS84) {
				if (firstCallBack) {
					firstCallBack = false;
					callback();
				} else {
					convert_coords(WGS84);
				}
			};
			converterHash = new GeodesicConverter(convSrc, convDest, convUnits, convLabels, convWrapper, convOptions, convDefs + encodeURI(encodeURIComponent(iso)), 'converterHash', convNfo, convCallBack, convReadOnly);
		}
<?php if(!$parted) { ?>
    function load() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      <?php if ($converter == "coords") { ?>
			loadConverter('<?php echo $conv_iso; ?>', function () {
				converterHash.transform({'x':'<?php echo $conv_lng; ?>', 'y':'<?php echo $conv_lat; ?>'});
			});
      <?php } ?>
      <?php if ($converter == "length") { ?>
      convertMeter('meter_div', document.length_converter_form);
      <?php } ?>
      <?php if ($converter == "temperature") { ?>
      convertCelsius('celsius_div', document.temperature_converter_form);
      <?php } ?>
    }
<?php } ?>
<?php
if ($converter == "coords" || !$selected) {
	if($parted) {
		$btn_action = '';
	} else {
		$btn_action = 'window.opener.focus();window.close();';
	}
}
?>
<?php if ($converter == "coords" || !$selected) { ?>
    function convert_coords(WGS84) {
      var lat, lng;
			if (WGS84 != undefined) {
				lat = WGS84.y;
				lng = WGS84.x;
<?php 	if($parted) { ?>
				mySite.overview.setupConvertMarker(lat, lng);
<?php 	} else { ?>
				if (window.opener) {
					if (window.opener.recieveLocation) {
						window.opener.recieveLocation(lat, lng);
					}
				}
				//window.opener.focus();
				//window.close();
<?php		} ?>
			}
		}
<?php } ?>
 
<?php if ($converter == "temperature" || !$selected) { ?>
    function convertCelsius(sourceDiv, oForm) {
      var celsius, fahrenheit, kelvin;
    	resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			celsius = strToFloat(oForm.celsius.value);
			fahrenheit = degreesCToF(celsius);
			kelvin = degreesCToK(celsius);
			oForm.fahrenheit.value = fahrenheit;
			oForm.kelvin.value = kelvin;
		}
		
		function convertFahrenheit(sourceDiv, oForm) {
      var celsius, fahrenheit, kelvin;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			fahrenheit = strToFloat(oForm.fahrenheit.value);
			celsius = degreesFToC(fahrenheit);
			kelvin = degreesCToK(celsius);
			oForm.celsius.value = celsius;
			oForm.kelvin.value = kelvin;
		}
		
		function convertKelvin(sourceDiv, oForm) {
      var celsius, fahrenheit, kelvin;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			kelvin = strToFloat(oForm.kelvin.value);
			celsius = degreesKToC(kelvin);
			fahrenheit = degreesCToF(celsius);
			oForm.celsius.value = celsius;
			oForm.fahrenheit.value = fahrenheit;
		}
<?php } ?>

<?php if ($converter == "length" || !$selected) { ?>
		function convertMile(sourceDiv, oForm) {
      var mile, yard, foot, inch, meter, kmeter;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			mile = strToFloat(oForm.mile.value);
			yard = lengthMileToYard(mile);
			foot = lengthYardToFoot(yard);
			inch = lengthFootToInch(foot);
			meter = lengthMileToMeter(mile);
			kmeter = meter / 1000;
			oForm.yard.value = yard;
			oForm.foot.value = foot;
			oForm.inch.value = inch;
			oForm.meter.value = meter;
			oForm.kmeter.value = kmeter;
		}
		
		function convertYard(sourceDiv, oForm) {
      var mile, yard, foot, inch, meter, kmeter;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			yard = strToFloat(oForm.yard.value);
			foot = lengthYardToFoot(yard);
			inch = lengthFootToInch(foot);
			mile = lengthYardToMile(yard);
			meter = lengthMileToMeter(mile);
			kmeter = meter / 1000;
			oForm.mile.value = mile;
			oForm.foot.value = foot;
			oForm.inch.value = inch;
			oForm.meter.value = meter;
			oForm.kmeter.value = kmeter;
		}
		
		function convertFoot(sourceDiv, oForm) {
      var mile, yard, foot, inch, meter, kmeter;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			foot = strToFloat(oForm.foot.value);
			inch = lengthFootToInch(foot);
			yard = lengthFootToYard(foot);
			mile = lengthYardToMile(yard);
			meter = lengthMileToMeter(mile);
			kmeter = meter / 1000;
			oForm.mile.value = mile;
			oForm.yard.value = yard;
			oForm.inch.value = inch;
			oForm.meter.value = meter;
			oForm.kmeter.value = kmeter;
		}
		
		function convertInch(sourceDiv, oForm) {
      var mile, yard, foot, inch, meter, kmeter;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			inch = strToFloat(oForm.inch.value);
			foot = lengthInchToFoot(inch);
			yard = lengthFootToYard(foot);
			mile = lengthYardToMile(yard);
			meter = lengthMileToMeter(mile);
			kmeter = meter / 1000;
			oForm.mile.value = mile;
			oForm.yard.value = yard;
			oForm.foot.value = foot;
			oForm.meter.value = meter;
			oForm.kmeter.value = kmeter;
		}
		
		function convertMeter(sourceDiv, oForm) {
      var mile, yard, foot, inch, meter, kmeter;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			meter = strToFloat(oForm.meter.value);
			mile = lengthMeterToMile(meter);
			yard = lengthMileToYard(mile);
			foot = lengthYardToFoot(yard);
			inch = lengthFootToInch(foot);
			kmeter = meter / 1000;
			oForm.yard.value = yard;
			oForm.foot.value = foot;
			oForm.inch.value = inch;
			oForm.mile.value = mile;
			oForm.kmeter.value = kmeter;
		}
		
		function convertKMeter(sourceDiv, oForm) {
      var meter;
			resetColorAll("white");
      xtdGetElementById(sourceDiv).style.backgroundColor = "LightGoldenRodYellow";
			meter = 1000 * strToFloat(oForm.kmeter.value);
			oForm.meter.value = meter;
			convertMeter(sourceDiv, oForm);
		}
<?php } ?>
		
    function resetColorAll(color) {
<?php if ($converter == "length" || !$selected) { ?>
      xtdGetElementById("mile_div").style.backgroundColor = color;
      xtdGetElementById("yard_div").style.backgroundColor = color;
      xtdGetElementById("foot_div").style.backgroundColor = color;
      xtdGetElementById("inch_div").style.backgroundColor = color;
      xtdGetElementById("meter_div").style.backgroundColor = color;
      xtdGetElementById("kmeter_div").style.backgroundColor = color;
<?php } ?>

<?php if ($converter == "temperature" || !$selected) { ?>
      xtdGetElementById("celsius_div").style.backgroundColor = color;
      xtdGetElementById("kelvin_div").style.backgroundColor = color;
      xtdGetElementById("fahrenheit_div").style.backgroundColor = color;
<?php } ?>
    }

<?php if ($converter == "temperature" || !$selected) { ?>
    function Info_deg_C() {
      /*var sMessage = "<title>- °C -</title>\n";
      sMessage = sMessage + "<h1>Température en degrés Celsius</h1>\n";
      sMessage = sMessage + "<p>Unité du Système International</p>\n";
      sMessage = sMessage + "<p>Fonctions de convertion :</p>\n";
      sMessage = sMessage + "<p>°C = °K - 273.15</p>\n";
      sMessage = sMessage + "<p>°C = (°F - 32) * 5/9</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de °C vers °K et °F.</p>\n";
      */
      var sMessage = "<convert>#label=251<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_deg_K() {
      /*var sMessage = "<title>- °K -</title>\n";
      sMessage = sMessage + "<h1>Température en degrés Kelvin</h1>\n";
      sMessage = sMessage + "<p>Fonctions de convertion :</p>\n";
      sMessage = sMessage + "<p>°K = °C + 273.15</p>\n";
      sMessage = sMessage + "<p>°K = °F * 5/9 + 255.37</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de °K vers °C et °F.</p>\n";
      */
      var sMessage = "<convert>#label=252<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_deg_F() {
      /*var sMessage = "<title>- °F -</title>\n";
      sMessage = sMessage + "<h1>Température en degrés Fahrenheit</h1>\n";
      sMessage = sMessage + "<p>Fonctions de convertion :</p>\n";
      sMessage = sMessage + "<p>°F = °C * 9/5 + 32</p>\n";
      sMessage = sMessage + "<p>°F = °K * 9/5 - 459.67</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de °F vers °K et °C.</p>\n";
      */
      var sMessage = "<convert>#label=253<convert>";
      popUpMsg(sMessage);
    }
<?php } ?>

<?php if ($converter == "length" || !$selected) { ?>
    function Info_len_Km() {
      /*var sMessage = "<title>- Kilomètre -</title>\n";
      sMessage = sMessage + "<h1>Longueur en kilomètres</h1>\n";
      sMessage = sMessage + "<p>Equivalence :</p>\n";
      sMessage = sMessage + "<p>1Km = 1000m</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de Kilomètre vers les autres unités.</p>\n";
      */
      var sMessage = "<convert>#label=254<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_len_m() {
      /*var sMessage = "<title>- Mètre -</title>\n";
      sMessage = sMessage + "<h1>Longueur en mètres</h1>\n";
      sMessage = sMessage + "<p>Unité du Système International</p>\n";
      sMessage = sMessage + "<p>Equivalence :</p>\n";
      sMessage = sMessage + "<p>1m = 1/1609.344Mile</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de Mètre vers les autres unités.</p>\n";
      */
      var sMessage = "<convert>#label=255<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_len_M() {
      /*var sMessage = "<title>- Mile -</title>\n";
      sMessage = sMessage + "<h1>Longueur en miles</h1>\n";
      sMessage = sMessage + "<p>Equivalence :</p>\n";
      sMessage = sMessage + "<p>1Mile = 1760Yards</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de Mile vers les autres unités.</p>\n";
      */
      var sMessage = "<convert>#label=256<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_len_y() {
      /*var sMessage = "<title>- Yard -</title>\n";
      sMessage = sMessage + "<h1>Longueur en yards</h1>\n";
      sMessage = sMessage + "<p>Equivalence :</p>\n";
      sMessage = sMessage + "<p>1Yard = 3Pieds</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de Yard vers les autres unités.</p>\n";
      */
      var sMessage = "<convert>#label=257<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_len_f() {
      /*var sMessage = "<title>- Pied -</title>\n";
      sMessage = sMessage + "<h1>Longueur en pieds</h1>\n";
      sMessage = sMessage + "<p>Equivalence :</p>\n";
      sMessage = sMessage + "<p>1Pied = 12Pouces</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de Pied vers les autres unités.</p>\n";
      */
      var sMessage = "<convert>#label=258<convert>";
      popUpMsg(sMessage);
    }
    
    function Info_len_i() {
      /*var sMessage = "<title>- Pouce -</title>\n";
      sMessage = sMessage + "<h1>Longueur en pouces</h1>\n";
      sMessage = sMessage + "<p>Equivalence :</p>\n";
      sMessage = sMessage + "<p>1Pouce = 2,54cm</p>\n";
      sMessage = sMessage + "<p>Le bouton CONVERTIR appelle la tranformation de Pouce vers les autres unités.</p>\n";
      */
      var sMessage = "<convert>#label=259<convert>";
      popUpMsg(sMessage);
    }
<?php } ?>
    <?php echo getCDataTag(false); ?>
    </script>
<?php } ?>
<?php if(!$parted) { ?>
  </head>
  <body onload="JavaScript:load();" id="body">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php } ?>
<?php if ($part == "html" || !$parted) { ?>
<?php if ($converter == "length" || !$selected) { ?>
    <div class="menu" id="length_converter" <?php if(!$selected) { ?>style="display:none;visibility:hidden;"<?php } ?> >
<?php if($parted) { ?>
      <?php echo getTopMenu(getCloseBtn("JavaScript:switchConverter(false,'length_converter');","<convert>#label=371<convert>").'<div class="frame_title">'.setTitle("#", "details", "<convert>#label=264<convert>", 2).'</div><!--Distances-->'); ?>
<?php } ?>
  		<form id="length_converter_form" name="length_converter_form" action="">
        <div id="meter_div" class="unit_div">
          <h3 onclick="JavaScript:Info_len_m();"><convert>#label=267<convert><!--Mètres (USI)--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="meter" id="meter" value="<?php echo $conv_length; ?>" size="10" /> <convert>#label=268<convert><!--m-->
            </td></tr><tr><td class="field">
              <input name="conv_meter" type="button" class="button1" id="conv_meter" onclick="JavaScript:convertMeter('meter_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="kmeter_div" class="unit_div">
          <h3 onclick="JavaScript:Info_len_Km();"><convert>#label=270<convert><!--Kilomètres--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="kmeter" id="kmeter" value="" size="10" /> <convert>#label=271<convert><!--km-->
            </td></tr><tr><td class="field">
              <input name="conv_kmeter" type="button" class="button1" id="conv_kmeter" onclick="JavaScript:convertKMeter('kmeter_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="mile_div" class="unit_div">
          <h3 onclick="JavaScript:Info_len_M();"><convert>#label=272<convert><!--Miles--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="mile" id="mile" value="" size="10" /> <convert>#label=273<convert><!--miles-->
            </td></tr><tr><td class="field">
              <input name="conv_mile" type="button" class="button1" id="conv_mile" onclick="JavaScript:convertMile('mile_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="yard_div" class="unit_div">
          <h3 onclick="JavaScript:Info_len_y();"><convert>#label=274<convert><!--Yards--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="yard" id="yard" value="" size="10" /> <convert>#label=275<convert><!--yards-->
            </td></tr><tr><td class="field">
              <input name="conv_yard" type="button" class="button1" id="conv_yard" onclick="JavaScript:convertYard('yard_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="foot_div" class="unit_div" style="display:none;visibility:hidden;">
          <h3 onclick="JavaScript:Info_len_f();"><convert>#label=276<convert><!--Pieds--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="foot" id="foot" value="" size="10" /> <convert>#label=277<convert><!--feets-->
            </td></tr><tr><td class="field">
              <input name="conv_foot" type="button" class="button1" id="conv_foot" onclick="JavaScript:convertFoot('foot_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="inch_div" class="unit_div" style="display:none;visibility:hidden;">
          <h3 onclick="JavaScript:Info_len_i();"><convert>#label=278<convert><!--Pouces--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="inch" id="inch" value="" size="10" /> <convert>#label=279<convert><!--inches-->
            </td></tr><tr><td class="field">
              <input name="conv_inch" type="button" class="button1" id="conv_inch" onclick="JavaScript:convertInch('inch_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
      </form>
<?php if ($parted) { ?>
      <?php echo getBotMenu(); ?>
<?php } ?>
  	</div>
<?php } ?>

<?php if ($converter == "temperature" || !$selected) { ?>
  	<div class="menu" id="temperature_converter" <?php if(!$selected) { ?>style="display:none;visibility:hidden;"<?php } ?> >
<?php if($parted) { ?>
      <?php echo getTopMenu(getCloseBtn("JavaScript:switchConverter(false,'temperature_converter');","<convert>#label=371<convert>").'<div class="frame_title">'.setTitle("#", "details", "<convert>#label=265<convert>", 2).'</div><!--Températures-->'); ?>
<?php } ?>
  		<form id="temperature_converter_form" name="temperature_converter_form" action="">
        <div id="celsius_div" class="unit_div">
          <h3 onclick="JavaScript:Info_deg_C();"><convert>#label=280<convert><!--Degrés Celsius (USI)--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="celsius" id="celsius" value="<?php echo $conv_temp; ?>" size="10" /> <convert>#label=281<convert><!--°C-->
            </td></tr><tr><td class="field">
              <input name="conv_celsius" type="button" class="button1" id="conv_celsius" onclick="JavaScript:convertCelsius('celsius_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="fahrenheit_div" class="unit_div">
          <h3 onclick="JavaScript:Info_deg_F();"><convert>#label=282<convert><!--Degrés Fahrenheit--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="fahrenheit" id="fahrenheit" value="" size="10" /> <convert>#label=283<convert><!--°F-->
            </td></tr><tr><td class="field">
              <input name="conv_fahrenheit" type="button" class="button1" id="conv_fahrenheit" onclick="JavaScript:convertFahrenheit('fahrenheit_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
        <div id="kelvin_div" class="unit_div">
          <h3 onclick="JavaScript:Info_deg_K();"><convert>#label=284<convert><!--Degrés Kelvin--></h3>
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td class="field">
              <input name="kelvin" id="kelvin" value="" size="10" /> <convert>#label=285<convert><!--°K-->
            </td></tr><tr><td class="field">
              <input name="conv_kelvin" type="button" class="button1" id="conv_kelvin" onclick="JavaScript:convertKelvin('kelvin_div',this.form);" value="<convert>#label=269<convert>" /><!--Convertir ces données-->
            </td></tr>
          </table>
        </div>
      </form>
<?php if ($parted) { ?>
      <?php echo getBotMenu(); ?>
<?php } ?>
  	</div>
<?php } ?>
  
<?php if ($converter == "coords" || !$selected) { ?>
    <div id="coords_converter" <?php if(!$selected && !$show_coords_conv) { ?>style="display:none;visibility:hidden;"<?php } ?> >
<?php 	if($parted) { ?>
      <?php echo getTopMenu(getCloseBtn("JavaScript:switchConverter(false,'coords_converter');","<convert>#label=371<convert>").'<div class="frame_title">'.setTitle("#", "details", "<convert>#label=266<convert>", 2).'</div><!--Coordonnées-->'); ?>
<?php 	} ?>
      <form id="coords_converter_form" name="coords_converter_form" action="">
<?php 	if($parted) { ?>
				<div style="margin-top:10px;text-align:center;">
					<input name="manual" id="manual_true" type="radio" checked="checked" value="true" style="border:0px none;" onclick="javascript:converterHash.setManualMode(true);"><label for="manual_true"><convert>#label=920<convert><!--Manuel--></label>&nbsp;&nbsp;&nbsp;
					<input name="manual" id="manual_false" type="radio" value="false" style="border:0px none;" onclick="javascript:converterHash.setManualMode(false);"><label for="manual_false"><convert>#label=921<convert><!--CSV--></label>
				</div>
				<div style="margin-top:10px;">
					<label for="conv_country"><convert>#label=917<convert><!--Filtrer par pays :--></label>
          <select class="select2" name="conv_country" id="conv_country" onchange="JavaScript:countryOnChange(this);">
<?php
          echo getOptionCountry($_SESSION['language'], "","<convert>#label=918<convert>");//[TOUS]
?>
          </select>
				</div>
<?php 	} ?>
				<div style="margin-top:10px;">
					<label for="crsSource"><convert>#label=660<convert><!--Système de référence :--></label>
					<select name="crsSource" id="crsSource" onchange="javascript:converterHash.updateCrs(this, true);">
						<option value="#" class="to-remove"><convert>#label=919<convert></option>
					</select>
				</div>
				<div id="loadingSource" class="loading_crs"><convert>#label=919<convert><!--Chargement en cours, veuillez patienter...--></div>
				<div id="xySource"></div>
<?php 	if ($readonly != 'true') { ?>
				<div>
					<input type="button" class="button1" value="<convert>#label=269<convert>" onclick="javascript:converterHash.transform('Source');<?php echo $btn_action; ?>" />
				</div>
<?php 	} ?>
<?php 	if($parted || $converter != "coords") { ?>
				<div style="margin-top:20px;">
					<label for="crsDest"><convert>#label=660<convert><!--Système de référence :--></label>
					<select name="crsDest" id="crsDest" onchange="javascript:converterHash.updateCrs(this, true);">
						<option value="#" class="to-remove"><convert>#label=919<convert></option>
					</select>
				</div>
				<div id="loadingDest" class="loading_crs"><convert>#label=919<convert><!--Chargement en cours, veuillez patienter...--></div>
				<div id="xyDest"></div>
<?php 		if ($readonly != 'true') { ?>
				<div>
					<input type="button" class="button1" value="<convert>#label=269<convert>" onclick="javascript:converterHash.transform('Dest');<?php echo $btn_action; ?>" />
				</div>
<?php 		} ?>
<?php 	} ?>
			</form>
			<div class="credit">
				<convert>#label=306<convert><!--
				Basé sur la librairie Proj4js (<a href="http://proj4js.org" title="Proj4js" target="_blank">http://proj4js.org</a>)
				et sur le projet Proj.4 (<a href="http://trac.osgeo.org/proj" title="Proj.4" target="_blank">http://trac.osgeo.org/proj</a>),
				ce convertisseur  utilise les constantes de conversion de Spatial Reference
				(<a href="http://spatialreference.org" title="Spatial Reference" target="_blank">http://spatialreference.org</a>).-->
			</div>
<?php 	if ($parted) { ?>
	       <?php echo getBotMenu(); ?>
<?php 	} ?>
    </div>
<?php } ?>
<?php } ?>
<?php if(!$parted) { ?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "converter/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
<?php } ?>