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
 * @copyright Copyright (c) 2009-1912 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include(dirname(__FILE__)."/../../conf/config.php");
include(dirname(__FILE__)."/../../func/function.php");
include(dirname(__FILE__)."/../declaration.php");
include(dirname(__FILE__)."/../application_".$_SESSION['language'].".php");
include(dirname(__FILE__)."/../mailfunctions_".$_SESSION['language'].".php");
include(dirname(__FILE__)."/../../func/firewall.php");

/*
if (!$passed) {
  $mail_header = 'From: contact@grottocenter.org'."\n";
  $mail_header .= 'Reply-To: contact@grottocenter.org'."\n";
  $mail_header .= 'Return-Path: contact@grottocenter.org'."\n";
  $mail_header .= 'Content-Type: text/plain; charset=UTF-8'."\n";
  $mail_header .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body = '  - URL: '.__FILE__."\n".'  - Login: '.$_SESSION['user_login']."\n".'  - Referer: '.$_SERVER['HTTP_REFERER'];
  mail('clem.rz@gmail.com','wrong referer',$mail_body,$mail_header);
  exit();
}
$frame = "overview";
if (!isset($_POST['dwl'])) {
  header("Content-type: text/xml");
}
$id = (isset($_POST['id'])) ? $_POST['id'] : 0;
$idsArray = explode(",", $id);
if (count($idsArray) > ENTRY_COUNT_MAX) {
  $idsArray = array_slice($idsArray, 0, ENTRY_COUNT_MAX);
  $id = implode(",", $idsArray);
}
$data = array();
if ($id != "") {
*/


$frame = "overview";
if (!isset($_POST['dwl'])) {
  header("Content-type: text/xml");
}
if (!$passed) { //May be the geoportal one
	$id = (isset($_GET['id'])) ? $_GET['id'] : 0;
	$idsArray = explode(",", $id);
	//Allow only one id
	$idsArray = array($idsArray[0]);
} else {
	$id = (isset($_POST['id'])) ? $_POST['id'] : 0;
	$idsArray = explode(",", $id);
}
if (count($idsArray) > ENTRY_COUNT_MAX) {
  $idsArray = array_slice($idsArray, 0, ENTRY_COUNT_MAX);
  $id = implode(",", $idsArray);
}
if ($id != "") {
  $sql = "SELECT Name, Latitude, Longitude FROM T_entry WHERE Id IN (".$id.") ";
  if (!USER_IS_CONNECTED) {
    $sql .= "AND Is_public = 'YES' ";
  }
  $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
}
echo '<?xml version="1.0" encoding="UTF-8"?'.'>';
?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://earth.google.com/kml/2.1 http://code.google.com/intl/fr-FR/apis/kml/schema/kml21.xsd">
  <Document>
    <Style id="entry_place_style">
      <IconStyle>
        <Icon>
          <href><?php echo $_SESSION['Application_url']; ?>/images/icons/entry3.png</href>
        </Icon>
      </IconStyle>
      <BalloonStyle>
        <text>$[name]</text>
      </BalloonStyle>
    </Style>
<?php
for($i=0;$i<$data['Count'];$i++) {
?>
    <Placemark>
      <name><?php echo $data[$i]['Name']; ?></name>
      <styleUrl>#entry_place_style</styleUrl>
      <Point>
        <extrude>1</extrude>
        <altitudeMode>relativeToGround</altitudeMode>
        <coordinates><?php echo $data[$i]['Longitude']; ?>,<?php echo $data[$i]['Latitude']; ?>,0</coordinates>
      </Point>
    </Placemark>
<?php
}
?>
  </Document>
</kml>