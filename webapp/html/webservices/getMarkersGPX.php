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
//validation : C:\00Clarity\z-xerces\bin>SaxCount.exe -v=always -n -s -f C:\00Clarity\z-xerces\bin\getMarkersGPX.php
include(dirname(__FILE__)."/../../conf/config.php");
include(dirname(__FILE__)."/../../func/function.php");
include(dirname(__FILE__)."/../declaration.php");
include(dirname(__FILE__)."/../application_".$_SESSION['language'].".php");
include(dirname(__FILE__)."/../mailfunctions_".$_SESSION['language'].".php");
include(dirname(__FILE__)."/../../func/firewall.php");

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
if (count($idsArray) > ENTRY_COUNT_MAX) {
  $idsArray = array_slice($idsArray, 0, ENTRY_COUNT_MAX);
  $id = implode(",", $idsArray);
}
$data = array();
if ($id != "") {
  $sql = "SELECT ey.Latitude, ey.Longitude, ey.Name, ey.Altitude, ";
  $sql .= "DATE_FORMAT(IF(ey.Date_reviewed > ey.Date_inscription, ey.Date_reviewed, ey.Date_inscription), '%Y-%m-%dT%TZ') AS Last_date, ";
  $sql .= "ey.Is_sensitive, ";
  $sql .= "(SELECT GROUP_CONCAT(DISTINCT des.Body ORDER BY des.Body SEPARATOR ' ') FROM T_description des LEFT OUTER JOIN J_entry_description ede ON ede.Id_description = des.Id WHERE ede.Id_entry = ey.Id) AS Body, ";
  $sql .= "ey.Id ";
  $sql .= "FROM T_entry ey ";
  $sql .= "WHERE ey.Id IN (".$id.") ";
  if (!USER_IS_CONNECTED) {
    $sql .= "AND Is_public = 'YES' ";
  }
  $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
}
$contactArray = explode("@", $_SESSION['Application_mail']);
$latArray = array();
$lngArray = array();
$nameArray = array();
for($i=0;$i<$data['Count'];$i++) {
  $latArray[] .= $data[$i]['Latitude'];
  $lngArray[] .= $data[$i]['Longitude'];
  $nameArray[] .= $data[$i]['Name'];
}
echo '<?xml version="1.0" encoding="UTF-8"?'.'>';
?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="<?php echo str_replace("&", "&amp;", $_SESSION['Application_url'].$_SERVER['REQUEST_URI']); ?>" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.topografix.com/GPX/gpx_overlay/0/3 http://www.topografix.com/GPX/gpx_overlay/0/3/gpx_overlay.xsd">
  <metadata>
    <name>Waypoints from <?php echo $_SESSION['Application_name']; ?></name>
    <desc><?php echo $_SESSION['Application_title']; ?></desc>
    <author>
      <name><?php echo $_SESSION['Application_name']; ?></name>
      <email id="<?php echo $contactArray[0]; ?>" domain="<?php echo $contactArray[1]; ?>"/>
      <link href="<?php echo $_SESSION['Application_url']; ?>">
        <text><?php echo $_SESSION['Application_name']; ?></text>
      </link>
    </author>
    <copyright author="<?php echo $_SESSION['Application_name']; ?>">
      <year><?php echo date("Y"); ?></year>
      <license>http://creativecommons.org/licenses/by-sa/3.0/deed.en</license>
    </copyright>
    <time><?php echo date("Y-m-d\TH:i:s\Z"); ?></time>
    <keywords><?php echo implode(", ", $nameArray); ?></keywords>
<?php if (count($nameArray) > 0) { ?>
    <bounds minlat="<?php echo min($latArray); ?>" minlon="<?php echo min($lngArray); ?>" maxlat="<?php echo max($latArray); ?>" maxlon="<?php echo max($lngArray); ?>"/>
<?php } ?>
  </metadata>
<?php
for($i=0;$i<$data['Count'];$i++) {
?>
  <wpt lat="<?php echo $data[$i]['Latitude']; ?>" lon="<?php echo $data[$i]['Longitude']; ?>">
<?php if ($data[$i]['Altitude'] != "") { ?>
    <ele><?php echo $data[$i]['Altitude']; ?></ele>
<?php } ?>
    <time><?php echo $data[$i]['Last_date']; ?></time>
    <name><?php echo $data[$i]['Name']; ?></name>
<?php if ($data[$i]['Is_sensitive'] == "YES") { ?>
    <cmt>WARNING: SENSITIVE CAVE!</cmt>
<?php }
      if ($data[$i]['Body'] != "") { ?>
    <desc><![CDATA[<?php echo $data[$i]['Body']; ?>]]></desc>
<?php } ?>
    <link href="<?php echo $_SESSION['Application_url']; ?>/file_<?php echo $_SESSION['language']; ?>.php?category=entry&amp;id=<?php echo $data[$i]['Id']; ?>">
      <text><?php echo $_SESSION['Application_title']." ".$data[$i]['Name']; ?></text>
    </link>
  </wpt>
<?php
}
?>
</gpx>