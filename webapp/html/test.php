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
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
  <script src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>" type="text/javascript"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
  	$sql = "SELECT COUNT(*) AS Count, cy.Iso, cy.Fr_name AS Name ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_country` cy ON cy.Iso = ey.Country ";
    $sql .= "GROUP BY cy.Iso ";
  	$entriesData = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  	$sql = "SELECT COUNT(*) AS Count, cy.Iso, cy.Fr_name AS Name ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ca ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_country` cy ON cy.Iso = ca.Country ";
    $sql .= "GROUP BY cy.Iso ";
  	$caversData = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  	$sql = "SELECT COUNT(*) AS Count, cy.Iso, cy.Fr_name AS Name ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_grotto` go ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_country` cy ON cy.Iso = go.Country ";
    $sql .= "GROUP BY cy.Iso ";
  	$grottoesData = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
?>
  <script type='text/javascript'>
    <?php echo getCDataTag(true); ?>
   google.load('visualization', '1', {'packages': ['geomap', 'annotatedtimeline']});
   google.setOnLoadCallback(drawVisu);

    function drawVisu() {
      var entries = new google.visualization.DataTable();
      var cavers = new google.visualization.DataTable();
      var grottoes = new google.visualization.DataTable();
      entries.addRows(<?php echo $entriesData['Count']; ?>);
      entries.addColumn('string', 'Country');
      entries.addColumn('number', 'Entrées ');
      entries.addColumn('string', 'CoutryLabel');
      cavers.addRows(<?php echo $caversData['Count']; ?>);
      cavers.addColumn('string', 'Country');
      cavers.addColumn('number', 'Spéléos ');
      cavers.addColumn('string', 'CoutryLabel');
      grottoes.addRows(<?php echo $grottoesData['Count']; ?>);
      grottoes.addColumn('string', 'Country');
      grottoes.addColumn('number', 'Clubs ');
      grottoes.addColumn('string', 'CoutryLabel');
<?php
    echo "\n";
    for($i=0;$i<$entriesData['Count'];$i++) {
      echo "entries.setValue(".$i.", 0, '".$entriesData[$i]['Iso']."');\n";
      echo "entries.setValue(".$i.", 1, ".$entriesData[$i]['Count'].");\n";
      echo "entries.setValue(".$i.", 2, '".$entriesData[$i]['Name']."');\n";
    }
    echo "\n";
    for($i=0;$i<$caversData['Count'];$i++) {
      echo "cavers.setValue(".$i.", 0, '".$caversData[$i]['Iso']."');\n";
      echo "cavers.setValue(".$i.", 1, ".$caversData[$i]['Count'].");\n";
      echo "cavers.setValue(".$i.", 2, '".$caversData[$i]['Name']."');\n";
    }
    echo "\n";
    for($i=0;$i<$grottoesData['Count'];$i++) {
      echo "grottoes.setValue(".$i.", 0, '".$grottoesData[$i]['Iso']."');\n";
      echo "grottoes.setValue(".$i.", 1, ".$grottoesData[$i]['Count'].");\n";
      echo "grottoes.setValue(".$i.", 2, '".$grottoesData[$i]['Name']."');\n";
    }
?>
      var options = {};
      options['dataMode'] = 'regions';
      
      options['colors'] = [0xFFE97F, 0xFF6A00]; //orange colors
      var container = document.getElementById('emap_canvas');
      var egeomap = new google.visualization.GeoMap(container);
      egeomap.draw(entries, options);
      
      options['colors'] = [0xC9FFB2, 0x007F0E]; //green colors
      var container = document.getElementById('cmap_canvas');
      var cgeomap = new google.visualization.GeoMap(container);
      cgeomap.draw(cavers, options);
      
      options['colors'] = [0xBAE2FF, 0x00137F]; //blue colors
      var container = document.getElementById('gmap_canvas');
      var ggeomap = new google.visualization.GeoMap(container);
      ggeomap.draw(grottoes, options);
<?php
  	$sql = "SELECT DATE_FORMAT(Date_inscription,'%Y') AS Year, DATE_FORMAT(Date_inscription,'%c') AS Month, DATE_FORMAT(Date_inscription,'%e') AS Day, COUNT(DATE_FORMAT(Date_inscription,'%Y%j')) AS Count ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ";
    $sql .= "GROUP BY DATE_FORMAT(Date_inscription,'%Y%j') ";
  	$caversData = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
?>
      var data = new google.visualization.DataTable();
      data.addRows(<?php echo $caversData['Count']; ?>);
      data.addColumn('date', 'Date');
      data.addColumn('number', 'Registered Users');
      data.addColumn('string', 'title1');
      data.addColumn('string', 'text1');
<?php
    $total_registered = 0;
    for($i = 0 ; $i < $caversData['Count']; $i++) {
      $total_registered = $total_registered + $caversData[$i]['Count'];
      echo "data.setValue(".$i.", 0, new Date(".$caversData[$i]['Year'].", ".$caversData[$i]['Month'].", ".$caversData[$i]['Day']."));\n";
      echo "data.setValue(".$i.", 1, ".$total_registered.");\n";
    }
?>

      var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('cchart_div'));
      chart.draw(data, {displayAnnotations: false});
  };
    <?php echo getCDataTag(false); ?>
  </script>
</head>

<body>
  <div id='emap_canvas'></div>
  <div id='cmap_canvas'></div>
  <div id='gmap_canvas'></div>
  <div id='cchart_div' style='width: 700px; height: 240px;'></div>
</body>

</html>