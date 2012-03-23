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
 * @copyright Copyright (c) 2009-2012 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../conf/config.php");
include("../func/function.php");
include("declaration.php"); 
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
$source = (isset($_GET['s'])) ? urldecode(stripslashes($_GET['s'])) : 'WGS84';

function getCrsDefsAsArray($s)
{
	$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_crs` WHERE Code = ".returnDefault($s, 'text');
	$data = getDataFromSQL($sql, __FILE__, "details", __FUNCTION__);
	if ($data['Count'] != 1) {
		exit();
	} else {
		return $data;
	}
}
function getWrappedRow($label, $value)
{
	return '
	<tr><td class="label">
		'.$label.'
	</td><td class="field">
		'.$value.'
	</td></tr>';
}
function getWrappedList($array, $repalcement)
{
	if (count($array) < 1) return '';
	$html = '<ul style="margin:0 0 0 -25px;">';
	foreach ($array as $key => $value) {
		if ($key != 'ellipse') {
			$html .= '<li>'.strtr($key, $repalcement).' '.$value.'</li>';
		}
	}
	$html .= '</ul>';
	return $html;
}
$primeMeridians = array(
    "greenwich"=> "0dE",
    "lisbon"=> "9d07'54.862\"W",
    "paris"=> "2d20'14.025\"E",
    "bogota"=> "74d04'51.3\"W",
    "madrid"=> "3d41'16.58\"W",
    "rome"=> "12d27'8.4\"E",
    "bern"=> "7d26'22.5\"E",
    "jakarta"=> "106d48'27.79\"E",
    "ferro"=> "17d40'W",
    "brussels"=> "4d22'4.71\"E",
    "stockholm"=> "18d3'29.8\"E",
    "athens"=> "23d42'58.815\"E",
    "oslo"=> "10d43'22.5\"E"
);

$ellipsoids = array(
  "MERIT"=> array("a"=>6378137.0, "rf"=>298.257, "ellipseName"=>"MERIT 1983"),
  "SGS85"=> array("a"=>6378136.0, "rf"=>298.257, "ellipseName"=>"Soviet Geodetic System 85"),
  "GRS80"=> array("a"=>6378137.0, "rf"=>298.257222101, "ellipseName"=>"GRS 1980(IUGG, 1980)"),
  "IAU76"=> array("a"=>6378140.0, "rf"=>298.257, "ellipseName"=>"IAU 1976"),
  "airy"=> array("a"=>6377563.396, "b"=>6356256.910, "ellipseName"=>"Airy 1830"),
  "APL4."=> array("a"=>6378137, "rf"=>298.25, "ellipseName"=>"Appl. Physics. 1965"),
  "NWL9D"=> array("a"=>6378145.0, "rf"=>298.25, "ellipseName"=>"Naval Weapons Lab., 1965"),
  "mod_airy"=> array("a"=>6377340.189, "b"=>6356034.446, "ellipseName"=>"Modified Airy"),
  "andrae"=> array("a"=>6377104.43, "rf"=>300.0, "ellipseName"=>"Andrae 1876 (Den., Iclnd.)"),
  "aust_SA"=> array("a"=>6378160.0, "rf"=>298.25, "ellipseName"=>"Australian Natl & S. Amer. 1969"),
  "GRS67"=> array("a"=>6378160.0, "rf"=>298.2471674270, "ellipseName"=>"GRS 67(IUGG 1967)"),
  "bessel"=> array("a"=>6377397.155, "rf"=>299.1528128, "ellipseName"=>"Bessel 1841"),
  "bess_nam"=> array("a"=>6377483.865, "rf"=>299.1528128, "ellipseName"=>"Bessel 1841 (Namibia)"),
  "clrk66"=> array("a"=>6378206.4, "b"=>6356583.8, "ellipseName"=>"Clarke 1866"),
  "clrk80"=> array("a"=>6378249.145, "rf"=>293.4663, "ellipseName"=>"Clarke 1880 mod."),
  "CPM"=> array("a"=>6375738.7, "rf"=>334.29, "ellipseName"=>"Comm. des Poids et Mesures 1799"),
  "delmbr"=> array("a"=>6376428.0, "rf"=>311.5, "ellipseName"=>"Delambre 1810 (Belgium)"),
  "engelis"=> array("a"=>6378136.05, "rf"=>298.2566, "ellipseName"=>"Engelis 1985"),
  "evrst30"=> array("a"=>6377276.345, "rf"=>300.8017, "ellipseName"=>"Everest 1830"),
  "evrst48"=> array("a"=>6377304.063, "rf"=>300.8017, "ellipseName"=>"Everest 1948"),
  "evrst56"=> array("a"=>6377301.243, "rf"=>300.8017, "ellipseName"=>"Everest 1956"),
  "evrst69"=> array("a"=>6377295.664, "rf"=>300.8017, "ellipseName"=>"Everest 1969"),
  "evrstSS"=> array("a"=>6377298.556, "rf"=>300.8017, "ellipseName"=>"Everest (Sabah & Sarawak)"),
  "fschr60"=> array("a"=>6378166.0, "rf"=>298.3, "ellipseName"=>"Fischer (Mercury Datum) 1960"),
  "fschr60m"=> array("a"=>6378155.0, "rf"=>298.3, "ellipseName"=>"Fischer 1960"),
  "fschr68"=> array("a"=>6378150.0, "rf"=>298.3, "ellipseName"=>"Fischer 1968"),
  "helmert"=> array("a"=>6378200.0, "rf"=>298.3, "ellipseName"=>"Helmert 1906"),
  "hough"=> array("a"=>6378270.0, "rf"=>297.0, "ellipseName"=>"Hough"),
  "intl"=> array("a"=>6378388.0, "rf"=>297.0, "ellipseName"=>"International 1909 (Hayford)"),
  "kaula"=> array("a"=>6378163.0, "rf"=>298.24, "ellipseName"=>"Kaula 1961"),
  "lerch"=> array("a"=>6378139.0, "rf"=>298.257, "ellipseName"=>"Lerch 1979"),
  "mprts"=> array("a"=>6397300.0, "rf"=>191.0, "ellipseName"=>"Maupertius 1738"),
  "new_intl"=> array("a"=>6378157.5, "b"=>6356772.2, "ellipseName"=>"New International 1967"),
  "plessis"=> array("a"=>6376523.0, "rf"=>6355863.0, "ellipseName"=>"Plessis 1817 (France)"),
  "krass"=> array("a"=>6378245.0, "rf"=>298.3, "ellipseName"=>"Krassovsky, 1942"),
  "SEasia"=> array("a"=>6378155.0, "b"=>6356773.3205, "ellipseName"=>"Southeast Asia"),
  "walbeck"=> array("a"=>6376896.0, "b"=>6355834.8467, "ellipseName"=>"Walbeck"),
  "WGS60"=> array("a"=>6378165.0, "rf"=>298.3, "ellipseName"=>"WGS 60"),
  "WGS66"=> array("a"=>6378145.0, "rf"=>298.25, "ellipseName"=>"WGS 66"),
  "WGS72"=> array("a"=>6378135.0, "rf"=>298.26, "ellipseName"=>"WGS 72"),
  "WGS84"=> array("a"=>6378137.0, "rf"=>298.257223563, "ellipseName"=>"WGS 84"),
  "sphere"=> array("a"=>6370997.0, "b"=>6370997.0, "ellipseName"=>"Normal Sphere (r=6370997)")
);

$datums = array(
  "WGS84"=> array("towgs84"=> "0,0,0", "ellipse"=> "WGS84", "datumName"=> "WGS84"),
  "GGRS87"=> array("towgs84"=> "-199.87,74.79,246.62", "ellipse"=> "GRS80", "datumName"=> "Greek_Geodetic_Reference_System_1987"),
  "NAD83"=> array("towgs84"=> "0,0,0", "ellipse"=> "GRS80", "datumName"=> "North_American_Datum_1983"),
  "NAD27"=> array("nadgrids"=> "@conus,@alaska,@ntv2_0.gsb,@ntv1_can.dat", "ellipse"=> "clrk66", "datumName"=> "North_American_Datum_1927"),
  "potsdam"=> array("towgs84"=> "606.0,23.0,413.0", "ellipse"=> "bessel", "datumName"=> "Potsdam Rauenberg 1950 DHDN"),
  "carthage"=> array("towgs84"=> "-263.0,6.0,431.0", "ellipse"=> "clark80", "datumName"=> "Carthage 1934 Tunisia"),
  "hermannskogel"=> array("towgs84"=> "653.0,-212.0,449.0", "ellipse"=> "bessel", "datumName"=> "Hermannskogel"),
  "ire65"=> array("towgs84"=> "482.530,-130.596,564.557,-1.042,-0.214,-0.631,8.15", "ellipse"=> "mod_airy", "datumName"=> "Ireland 1965"),
  "nzgd49"=> array("towgs84"=> "59.47,-5.04,187.44,0.47,-0.1,1.024,-4.5993", "ellipse"=> "intl", "datumName"=> "New Zealand Geodetic Datum 1949"),
  "OSGB36"=> array("towgs84"=> "446.448,-125.157,542.060,0.1502,0.2470,0.8421,-20.4894", "ellipse"=> "airy", "datumName"=> "Airy 1830")
);

$properties = getCrsDefsAsArray($source);
$defs = explode('+', $properties[0]['Definition']);
$html = '';
$ellps = true;
foreach ($defs as $def) {
	if ($def != '') {
		$tmpArray = explode('=', $def);
		$defArray[trim($tmpArray[0])] = trim($tmpArray[1]);
		$repalcement = array('title'=>'',
													'proj'=>"<convert>#label=895<convert>", //Projection:
													'units'=>"<convert>#label=896<convert>", //Units:
													'datum'=>"<convert>#label=897<convert>", //Datum:
													'datumName'=>"<convert>#label=898<convert>", //Name:
													'nadgrids'=>"<convert>#label=899<convert>", //NAD Grids:
													'ellps'=>"<convert>#label=900<convert>", //Ellipsoid:
													'ellipse'=>"<convert>#label=900<convert>", //Ellipsoid:
													'ellipseName'=>"<convert>#label=898<convert>", //Name:
													'a'=>"<convert>#label=901<convert>", //Semi-major radius:
													'b'=>"<convert>#label=902<convert>", //Semi-minor radius:
													'rf'=>"<convert>#label=903<convert>", //Inverse flattening:
													'lat_0'=>"<convert>#label=904<convert>", //Central latitude:
													'lat_1'=>"<convert>#label=905<convert>", //Standard parallel 1:
													'lat_2'=>"<convert>#label=906<convert>", //Standard parallel 2:
													'lat_ts'=>"<convert>#label=907<convert>", //Used in merc and eqc:
													'lon_0'=>"<convert>#label=908<convert>", //Central longitude:
													'alpha'=>"<convert>#label=909<convert>", //For somerc projection:
													'lonc'=>"<convert>#label=909<convert>", //For somerc projection:
													'x_0'=>"<convert>#label=910<convert>", //False easting:
													'y_0'=>"<convert>#label=911<convert>", //False northing:
													'k_0'=>"<convert>#label=912<convert>", //Projection scale factor:
													'k'=>"<convert>#label=912<convert>", //Projection scale factor:
													'r_a'=>"<convert>#label=913<convert>", //Sphere--area of ellipsoid:
													'zone'=>'',
													'south'=>'',
													'north'=>'',
													'towgs84'=>"<convert>#label=914<convert>", //Toward WGS84 scaling:
													'to_meter'=>"<convert>#label=915<convert>", //Cartesian scaling:
													'from_greenwich'=>"<convert>#label=916<convert>", //From greenwich scaling:
													'pm'=>'Prime meridian:');
		$prop_label = strtr(trim($tmpArray[0]), $repalcement);
		$prop_value = trim($tmpArray[1]);
		if ($prop_label != '' && $prop_value != '') {
			switch (trim($tmpArray[0])) {
				case 'pm':
					$html .= getWrappedRow($prop_label, $primeMeridians[$prop_value]);
					break;
				case 'datum':
					$html .= getWrappedRow($prop_label, getWrappedList($datums[$prop_value], $repalcement));
					$prop_label = strtr("ellipse", $repalcement);
					$prop_value = $datums[$prop_value]["ellipse"];
				case 'ellps':
					if ($ellps) {
						$ellps = false;
						$html .= getWrappedRow($prop_label, getWrappedList($ellipsoids[$prop_value], $repalcement));
					}
					break;
				default:
					$html .= getWrappedRow($prop_label, $prop_value);
					break;
			}
		}
	}
}
$html .= '<tr><td colspan="2" class="label"><a href="'.$properties[0]['Url'].'">'.$properties[0]['Url'].'</a></td></tr>';
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']?></title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
<?php echo getTopFrame(false); ?>
<h3><?php echo $defArray['title']; ?></h3>
<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
<?php echo $html; ?>
</table>
<?php echo getBotFrame(false); ?>
  </body>
</html>