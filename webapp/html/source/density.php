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
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$frame = 'home';
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
  <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>"></script>
  <script type="text/javascript" charset="UTF-8" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=806<convert></title><!--Densité-->
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	$type = (isset($_GET['type'])) ? $_GET['type'] : '';
  	$type_array = array('entry','caver','grotto');
  	if (!in_array($type, $type_array)) {
      exit();
    }
    $sql = "SELECT COUNT(*) AS Count, cy.Iso, cy.".$_SESSION['language']."_name AS Name ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_".$type."` ty ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_country` cy ON cy.Iso = ty.Country ";
    $sql .= "GROUP BY cy.Iso ";
    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
?>
    <script type='text/javascript'>
    <?php echo getCDataTag(true); ?>
    google.load('visualization', '1', {'packages': ['geomap', 'annotatedtimeline']});
    google.setOnLoadCallback(drawVisu);

    function drawVisu() {
      var options, dataArray, container, geomap, legend;
      options = {};
      options['dataMode'] = 'regions';
<?php
    switch ($type)
    {
    	case "entry":
?>
      legend = '<convert>#label=384<convert> '; //Entrées
      options['colors'] = [0xFFE97F, 0xFF6A00]; //orange colors
<?php
        break;
    	case "caver":
?>
      legend = '<convert>#label=385<convert> '; //Spéléologues
      options['colors'] = [0xC9FFB2, 0x007F0E]; //green colors
<?php
        break;
    	case "grotto":
?>
      legend = '<convert>#label=386<convert> '; //Clubs
      options['colors'] = [0xBAE2FF, 0x00137F]; //blue colors
<?php
        break;
    }
?>
      dataArray = new google.visualization.DataTable();
      dataArray.addRows(<?php echo $data['Count']; ?>);
      dataArray.addColumn('string', 'Country');
      dataArray.addColumn('number', legend);
      dataArray.addColumn('string', 'CoutryLabel');
<?php
    echo "\n";
    for($i=0;$i<$data['Count'];$i++) {
      echo "dataArray.setValue(".$i.", 0, '".$data[$i]['Iso']."');\n";
      echo "dataArray.setValue(".$i.", 1, ".$data[$i]['Count'].");\n";
      echo "dataArray.setValue(".$i.", 2, '".$data[$i]['Name']."');\n";
    }
?>
      container = xtdGetElementById('map_canvas');
      geomap = new google.visualization.GeoMap(container);
      geomap.draw(dataArray, options);
    };
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body>
    <?php echo getTopFrame(false); ?>
    <div id='map_canvas'></div>
    <?php echo getBotFrame(false); ?>
  </body>
</html>