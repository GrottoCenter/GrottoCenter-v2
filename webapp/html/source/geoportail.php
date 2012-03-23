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
 * @copyright Copyright (c) 2009-2012 Cl�ment Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
//open the window in 828x625
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$lang = (isset($_GET['lang'])) ? strtolower($_GET['lang']) : 'en';
$zoom = (isset($_GET['z'])) ? $_GET['z'] : 4;
$id = (isset($_GET['id'])) ? $_GET['id'] : 0;
$langArray = array('fr', 'en', 'es', 'de', 'it');
$lang = (in_array($lang, $langArray)) ? $lang : 'en';
if ($id == "") {
  exit();
}
$sql = "SELECT AVG(Latitude) AS Latitude, AVG(Longitude) AS Longitude FROM T_entry WHERE Id IN (".$id.") ";
if (!USER_IS_CONNECTED) {
  $sql .= "AND Is_public = 'YES' ";
}
$data = getDataFromSQL($sql, __FILE__, "Geoportail", __FUNCTION__);
$selection = array('fr' => "", 'en' => "", 'es' => "", 'de' => "", 'it' => "");
$selection[$lang] = ' selected="selected"';
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']?> Géoportail</title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script src="http://api.ign.fr/api?v=1.0beta3&amp;key=<?php echo Geoportal_key; ?>&amp;instance=map"><!-- --></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    var kbControl, map;
    kbControl = null;
    function initGeoportalMap() {
      // Load the map
      geoportalLoadmap("MapDiv", "normal");
      //Proxy for WFS/KML
      map.setProxyUrl("http://api.ign.fr/JSPProxyScript.jsp?url=");
      // Languages settings
      OpenLayers.Lang.setCode('<?php echo $lang; ?>');
      var language = OpenLayers.Lang.getCode();
      var re = new RegExp("^" + language);
      var slct = OpenLayers.Util.getElement('gpChooseLang');
      var i;
      for (i= 0; i<slct.options.length; i++) {
        if (slct.options[i].value.match(re)) {
          slct.options[i].selected = true;
        }
      }
      slct.onfocus = function() {
        if (kbControl) {
          if (kbControl.active) {
            kbControl.deactivate();
          }
        }
      };
      slct.onblur= function() {
        if (kbControl) {
          if (!kbControl.active) {
            kbControl.activate();
          }
        }
      };
      slct.onchange = function() {
        map.setLocale(this.options[this.selectedIndex].value);
        this.blur();
      };
      kbControl = map.getMap().getControlsByClass(OpenLayers.Control.KeyboardDefaults.prototype.CLASS_NAME)[0];
      // Load the layers
      if(map.allowedGeoportalLayers){
        map.addGeoportalLayers(map.allowedGeoportalLayers);
      }
      // Center the map
      map.setCenterAtLonLat(<?php echo $data[0]['Longitude']; ?>, <?php echo $data[0]['Latitude']; ?>, <?php echo $zoom; ?>);
      // Load custom layers
      setTimeout('loadLayer()', 1000);
    }
    
    function loadLayer() {
      // Add KML layer
      map.addLayer("KML","<convert>#label=384<convert>","webservices/getMarkersKML.php?id=<?php echo $id; ?>");
    }
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body>
    <?php echo getTopFrame(true, "height:100%;"); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <form style="border:0;margin:0;padding:0;" name="gpLangChange" action="#">
      <select style="font-size:0.75em;" id="gpChooseLang" name="gpChooseLang" size="1">
        <option value="fr">Français</option>
        <option value="en">English</option>
        <option value="de">Deutsch</option>
        <option value="es">Español</option>
        <option value="it">Italiano</option>
      </select>
      <a href="JavaScript:window.print();" title="<convert>#label=525<convert>" class="nothing"><img src="../images/icons/printer.png" alt="<convert>#label=525<convert>" style="border:0px none;vertical-align:bottom;" /></a>
    </form>
    <div id="MapDiv" style="width:100%;height:100%;margin-top:2px;">
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "geoportail/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
