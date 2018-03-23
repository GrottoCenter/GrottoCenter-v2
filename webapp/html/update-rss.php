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
 * @copyright Copyright (c) 2009-2012 Clement Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
/*
--Check:
SELECT DISTINCT T_entry.Id, T_entry.Has_contributions
FROM V_contributions
LEFT OUTER JOIN T_entry ON V_contributions.Id_entry = T_entry.Id
WHERE (V_contributions.Id IS NULL AND T_entry.Has_contributions = 'YES') OR (V_contributions.Id IS NOT NULL AND T_entry.Has_contributions = 'NO')
*/
$_GET['lang'] = "En";
header("Content-type: text/plain");
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$languagesArray = getAvailableLanguages();
$app_prop = appProp();
$today = date("D, d M Y H:i:s +0100");
foreach($languagesArray as $shortLang => $largeLang) {
  $labelArray = getLabelArray("home", $shortLang);
  $feedFileName = $app_prop['Url']."/html/rss_".$shortLang.".xml";
  //header rss 2.0
  $xml = '<?xml version="1.0" encoding="UTF-8"?'.'><rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
  $xml .= '<channel>';
  $xml .= '<title>'.$app_prop['Name'].' '.$app_prop['Version'].'</title>';
  $xml .= '<link>'.$app_prop['Url'].'</link>';
  $xml .= '<description>'.$labelArray[729].'</description>';
  $xml .= '<copyright>'.$app_prop['Authors'].'</copyright>';
  $xml .= '<language>'.strtolower($shortLang).'</language>';
  $xml .= '<image>';
  $xml .= '<title>'.$app_prop['Name'].' '.$app_prop['Version'].'</title>';
  $xml .= '<url>'.$app_prop['Url'].'/favicon.png</url>';
  $xml .= '<link>'.$app_prop['Url'].'</link>';
  $xml .= '</image>';
  $xml .= '<pubDate>'.$today.'</pubDate>';
  $xml .= '<atom:link href="'.$feedFileName.'" rel="self" type="application/rss+xml" />';
  //last contributions
  $max_length = 100;
  $sql = "SELECT ";
  $sql .= "IFNULL(contrib.Id_entry,contrib.Id) AS `Id`, ";
  $sql .= "CASE contrib.Category ";
  $sql .= "WHEN _latin1'T_description' THEN '".$labelArray[497]."' ";//Description
  $sql .= "WHEN _latin1'J_entry_description' THEN '".$labelArray[497]."' ";//Description
  $sql .= "WHEN _latin1'T_comment' THEN '".$labelArray[638]."' ";//Commentaire
  $sql .= "WHEN _latin1'T_location' THEN '".$labelArray[639]."' ";//Localisation
  $sql .= "WHEN _latin1'J_entry_rigging' THEN '".$labelArray[640]."' ";//Equipement
  $sql .= "WHEN _latin1'T_history' THEN '".$labelArray[593]."' ";//Historique
  $sql .= "WHEN _latin1'T_bibliography' THEN '".$labelArray[590]."' ";//Bibliographie
  $sql .= "WHEN _latin1'T_entry' THEN '".$labelArray[625]."' ";//Entrée
  $sql .= "WHEN _latin1'T_grotto' THEN '".$labelArray[186]."' ";//Club
  $sql .= "WHEN _latin1'T_cave' THEN '".$labelArray[119]."' ";//Réseau
  $sql .= "WHEN _latin1'T_massif' THEN '".$labelArray[555]."' ";//Massif
  $sql .= "WHEN _latin1'T_url' THEN '".$labelArray[669]."' ";//Site partenaire
  $sql .= "WHEN _latin1'T_topography' THEN '".$labelArray[845]."' ";//Topographie
  $sql .= "ELSE NULL END AS `Category`, ";
  //$sql .= "IF(CHAR_LENGTH(contrib.Title)<".$max_length.",contrib.Title,CONCAT(SUBSTR(contrib.Title,1,".$max_length."),'...')) AS `Title`, ";
  //$sql .= "IF(CHAR_LENGTH(contrib.Body)<".$max_length.",contrib.Body,CONCAT(SUBSTR(contrib.Body,1,".$max_length."),'...')) AS `Description`, ";
  $sql .= "contrib.Title AS `Title`, ";
  $sql .= "contrib.Body AS `Description`, ";
  $sql .= "IFNULL(contrib.Date_reviewed, contrib.Date_inscription) AS `Date`, ";
  $sql .= "IF(contrib.Category NOT IN (_latin1'T_url', _latin1'T_grotto', _latin1'T_cave', _latin1'T_massif'),'YES','NO') AS `Is_filed`, ";
  $sql .= "IF(contrib.Category IN (_latin1'T_url'),'YES','NO') AS `Is_url`, ";
  $sql .= "contrib.Body AS `Body`, ";
  $sql .= "IF(contrib.Date_reviewed>contrib.Date_inscription, rev.Nickname, auth.Nickname) AS `Contributor`, ";
  $sql .= "IF(contrib.Date_reviewed>contrib.Date_inscription, rev.Contact, auth.Contact) AS `Contact`, ";
	$sql .= "contrib.Latitude AS `Latitude`, ";
	$sql .= "contrib.Longitude AS `Longitude` ";
  $sql .= "FROM `".$app_prop['Host']."`.`V_contributions` contrib ";
  $sql .= "LEFT OUTER JOIN `".$app_prop['Host']."`.`T_caver` rev ON rev.Id = contrib.Id_reviewer ";
  $sql .= "LEFT OUTER JOIN `".$app_prop['Host']."`.`T_caver` auth ON auth.Id = contrib.Id_author ";
  $sql .= "WHERE contrib.Is_public = 'YES' OR contrib.Is_public IS NULL ";
  $sql .= "ORDER BY IFNULL(contrib.Date_reviewed, contrib.Date_inscription) DESC, contrib.Title ASC ";
  $sql .= "LIMIT 40";
  
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  for ($i=0;$i<$data['Count'];$i++) {
    if ($data[$i]['Is_filed'] == 'YES') {
      $url = $app_prop['Url']."/html/file_".$shortLang.".php?lang=".$shortLang."&check_lang_auto=false&category=entry&id=".$data[$i]['Id'];
    } elseif ($data[$i]['Is_url'] == 'YES') {
      $url = $data[$i]['Body'];
      if (strpos($url, "http://") === false) {
        $url = "http://".$url;
      }
    } else {
      $url = $app_prop['Url']."/?lang=".$shortLang;
    }
    $url = str_replace('&', '&amp;', $url);
    $category = $data[$i]['Category'];
    $author = $data[$i]['Contributor'];
    $contact = $data[$i]['Contact'];
    $title = $category." : ".$data[$i]['Title']." / ".$author;
    $description = str_replace('&', '&amp;', $data[$i]['Description']);
    $title = str_replace(CHR(10), "", str_replace(CHR(13), "", $title));
    $description = str_replace(CHR(10), "", str_replace(CHR(13), "", $description));
    $date = $data[$i]['Date'];
    $date = date("D, d M Y H:i:s +0100", strtotime($date));
    $xml .= '<item>';
    $xml .= '<title>'.$title.'</title>';
		if ($data[$i]['Latitude'] != "" && $data[$i]['Longitude'] != "") {
			if ($data[$i]['Category'] == $labelArray[625]) { //Entree
				$staticUriImg = "https://maps.google.com/maps/api/staticmap?maptype=terrain&amp;amp;zoom=09&amp;amp;sensor=false&amp;amp;markers=icon:http://www.grottocenter.org/images/icons/entry2.png|".$data[$i]['Latitude'].",".$data[$i]['Longitude']."&amp;amp;size=300x250";
			} else {
				$staticUriImg = "https://maps.google.com/maps/api/staticmap?maptype=terrain&amp;amp;zoom=09&amp;amp;sensor=false&amp;amp;markers=icon:http://www.grottocenter.org/images/icons/grotto1.png|".$data[$i]['Latitude'].",".$data[$i]['Longitude']."&amp;amp;size=300x250";
			}
			$staticUriA = "https://maps.google.com/maps?hl=".strtolower($shortLang)."&amp;amp;q=".$data[$i]['Latitude'].",".$data[$i]['Longitude'];
			$description .= '&lt;div&gt;&lt;a href="'.$staticUriA.'"&gt;&lt;img src="'.$staticUriImg.'" border="0" alt="IMG" /&gt;&lt;/a&gt;&lt;/div&gt;';
		}
    $xml .= '<link>'.$url.'</link>';
    $xml .= '<guid isPermaLink="false">'.$date.' - '.$title.'</guid>';
    $xml .= '<description>'.$description.'</description>';
    $xml .= '<pubDate>'.$date.'</pubDate>';
		if ($contact != "") {
			$xml .= '<author>'.$contact;
			if ($author != "") {
				$xml .= ' ('.$author.')';
			}
			$xml .= '</author>';
		}
    $xml .= '<category>'.$category.'</category>';
    $xml .= '<atom:link href="'.$feedFileName.'" rel="self" type="application/rss+xml" />';
    $xml .= '</item>';
  }
  $xml .= '</channel>';
  $xml .= '</rss>';
  
  $feedFileName = "rss_".$shortLang.".xml";
  $handleW = @fopen($feedFileName, 'wb');
  if ($handleW) {
    @fwrite($handleW, $xml);
  }else{
      reportError("Impossible to write $feedFileName...",__FILE__, "update_rss", "update_rss", '');
  }
  @fclose($handleW);
}
?>
