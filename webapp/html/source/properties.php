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
function getProperties($category,$id,$isConnected,$labelsBlank,$labelsSinceDate,$addTitle,$systemArray=array(),$for_printer=false,$opener)
{
  switch ($category) {
    case "entry":
      $AvgAe = getAvgAestheticism($id);
      $sqlAe = "SELECT round(Aestheticism) AS Aestheticism FROM `".$_SESSION['Application_host']."`.`T_comment` WHERE Id_entry=".$id;
      $AvgCa = getAvgCaving($id);
      $sqlCa = "SELECT round(Caving) AS Caving FROM `".$_SESSION['Application_host']."`.`T_comment` WHERE Id_entry=".$id;
      $AvgAp = getAvgApproach($id);
      $sqlAp = "SELECT round(Approach) AS Approach FROM `".$_SESSION['Application_host']."`.`T_comment` WHERE Id_entry=".$id;
      if(!$addTitle){
        $histoAe = "<div onclick=\"JavaScript:infoHistoAe();\" class=\"histo\"><img src=\"".getHistoSrc($sqlAe,'Aestheticism')."\" alt=\"image\" /></div>\n";
        $histoCa = "<div onclick=\"JavaScript:infoHistoCa();\" class=\"histo\"><img src=\"".getHistoSrc($sqlCa,'Caving')."\" alt=\"image\" /></div>\n";
        $histoAp = "<div onclick=\"JavaScript:infoHistoAp();\" class=\"histo\"><img src=\"".getHistoSrc($sqlAp,'Approach')."\" alt=\"image\" /></div>\n";
      } else {
        $histoAe = "";
        $histoCa = "";
        $histoAp = "";
      }
      $sql = "SELECT cat.*, ty.".$_SESSION['language']."_type AS typeName, ca.Name as NetwName, ca.Id as NetwId, ma.Name AS MasName, ma.Id AS MasId, ";
      //$sql .= "IF(ISNULL(ca.Id)=1,eybis.Min_depth,ca.Min_depth) AS Min_depth, ";
      //$sql .= "IF(ISNULL(ca.Id)=1,eybis.Max_depth,ca.Max_depth) AS Max_depth, ";
      $sql .= "IF(ISNULL(ca.Id)=1,eybis.Depth,ca.Depth) AS Depth, ";
      $sql .= "IF(ISNULL(ca.Id)=1,eybis.Is_diving,ca.Is_diving) AS Is_diving, ";
      $sql .= "IF(ISNULL(ca.Id)=1,eybis.Length,ca.Length) AS Length, ";
      $sql .= "IF(ISNULL(ca.Id)=1,eybis.Temperature,ca.Temperature) AS Temperature, ";
      $sql .= "IF(ISNULL(ca.Id)=1,NULL,(SELECT COUNT(*) FROM `".$_SESSION['Application_host']."`.`J_cave_entry` WHERE ca.Id = Id_cave)) AS NumberOfEntries, ";
      $sql .= "GROUP_CONCAT(DISTINCT u.Url ORDER BY u.Url SEPARATOR '<br />\n') AS Partners ";
      $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` cat ";
      $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_single_entry` eybis ON cat.Id = eybis.Id ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_type` ty ON cat.Id_type = ty.Id ";
			$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON cat.Id = ce.Id_entry ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_cave` ca ON ce.Id_cave = ca.Id ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON (mc.Id_cave = ca.Id OR mc.Id_entry = cat.Id) ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ma ON ma.Id = mc.Id_massif ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_entry_url` eu ON eu.Id_entry = cat.Id ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_url` u ON u.Id = eu.Id_url ";
      break;
    case "cave":
      $listSQL = "SELECT DISTINCT e.Id, e.Name ";
      $listSQL .= "FROM `".$_SESSION['Application_host']."`.`T_entry` e ";
      $listSQL .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ce.Id_entry = e.Id ";
      $listSQL .= "WHERE ce.Id_cave = ".$id." AND e.Is_public = 'YES' ";
			if (!$isConnected){
				$listSQL .= "AND e.Is_public = 'YES' ";
			}
      $listSQL .= "ORDER BY e.Name ";
      $entryList = getDataFromSQL($listSQL, __FILE__, "function", __FUNCTION__);
      $entriesList = "";
      for($index=0;$index<$entryList["Count"];$index++) {
        $entriesList .= "<a href=\"JavaScript:openMe(".$entryList[$index]['Id'].", 'entry', false);\">".$entryList[$index]['Name']."</a>, ";
      }
      $entriesList = substr($entriesList,0,strlen($entriesList)-2);
      $sql = "SELECT cat.*, ma.Name AS MasName, ma.Id AS MasId ";
      $sql .= "FROM `".$_SESSION['Application_host']."`.`T_cave` cat ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON mc.Id_cave = cat.Id ";
      $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ma ON ma.Id = mc.Id_massif ";
      break;
    case "massif":
      $listSQL = "SELECT COUNT(*) AS Nb, ";
      $listSQL .= "-MAX(ABS(IF(ISNULL(ca.Id)=1,eybis.Depth,ca.Depth))) AS Max_Depth, ";
      $listSQL .= "MAX(ABS(IF(ISNULL(ca.Id)=1,eybis.Length,ca.Length))) AS Max_Length, ";
      $listSQL .= "SUM(ABS(IF(ISNULL(ca.Id)=1,eybis.Length,ca.Length))) AS Sum_Length, ";
      $listSQL .= "-AVG(ABS(IF(ISNULL(ca.Id)=1,eybis.Depth,ca.Depth))) AS Avg_Depth, ";
      $listSQL .= "STD(ABS(IF(ISNULL(ca.Id)=1,eybis.Depth,ca.Depth))) AS Std_Depth, ";
      $listSQL .= "AVG(ABS(IF(ISNULL(ca.Id)=1,eybis.Length,ca.Length))) AS Avg_Length, ";
      $listSQL .= "STD(ABS(IF(ISNULL(ca.Id)=1,eybis.Length,ca.Length))) AS Std_Length ";
      $listSQL .= "FROM `".$_SESSION['Application_host']."`.`T_entry` e ";
      $listSQL .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_single_entry` eybis ON e.Id = eybis.Id ";
      $listSQL .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON e.Id = ce.Id_entry ";
      $listSQL .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_cave` ca ON ca.Id = ce.Id_cave ";
      $listSQL .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON (mc.Id_cave = ce.Id_cave OR mc.Id_entry = e.Id) ";
      $listSQL .= "WHERE mc.Id_massif = ".$id." ";
      $entriesStats = getDataFromSQL($listSQL, __FILE__, "function", __FUNCTION__);
    default:
      $sql = "SELECT cat.* ";
      $sql .= "FROM `".$_SESSION['Application_host']."`.`T_".$category."` cat ";
      break;
  }
	$sql .= "WHERE cat.Id = ".$id." ";
  if (!$isConnected && $category == "entry"){
    $sql .= "AND cat.Is_public = 'YES' ";
  }
  if ($category == "entry") {
    $sql .= "GROUP BY cat.Id ";
    $sql .= "ORDER BY MasName DESC ";
  }
  $data = getDataFromSQL($sql, __FILE__, $opener, __FUNCTION__);
  if ($data['Count'] > 0){
    $is_public = $data[0]['Contact_is_public'];
    $is_shown = ((($category == "caver") && ((($is_public == Contact_for_registered) && $isConnected) || ($is_public == Contact_for_everybody))) || ($category != "caver"));
    if ($addTitle) {
      if (isset($data[0]['Nickname'])) {
        $title_name = $data[0]['Nickname'];
      } else {
        $title_name = $data[0]['Name'];
      }
    	$innerHTML .= getTopMenu(getCloseBtn("JavaScript:resetDetails();","<convert>#label=371<convert>")."<div class=\"frame_title\">".setTitle("#", "details", "<convert>#label=440<convert> ".$title_name, 1)."</div>");//A propos de
  	}
    if ($is_shown) {
      $innerHTML .= getInnerLine($data[0]['Name'],$data[0]['Name'],"<convert>#label=199<convert> :");//Nom
    }
    $innerHTML .= getInnerLine($entriesList,$entriesList,"<convert>#label=384<convert> :");//Entrées :
    $innerHTML .= getInnerLine($entriesStats[0]['Nb'],$entriesStats[0]['Nb'],"<convert>#label=384<convert> :");//Entrées :
    $innerHTML .= getInnerLine($entriesStats[0]['Max_Depth'],round($entriesStats[0]['Max_Depth']),"<convert>#label=758<convert> :","","<convert>#label=268<convert>");//Prof. Max. ://m
    $innerHTML .= getInnerLine($entriesStats[0]['Avg_Depth'],round($entriesStats[0]['Avg_Depth']),"<convert>#label=759<convert> :","","<convert>#label=268<convert>");//Prof. Moy. ://m
    $innerHTML .= getInnerLine($entriesStats[0]['Std_Depth'],round($entriesStats[0]['Std_Depth']),"<convert>#label=760<convert> :","","<convert>#label=268<convert>");//E.Type Prof. ://m
    $innerHTML .= getInnerLine($entriesStats[0]['Max_Length'],round($entriesStats[0]['Max_Length']),"<convert>#label=761<convert> :","","<convert>#label=268<convert>");//Dev. Max. ://m
    $innerHTML .= getInnerLine($entriesStats[0]['Sum_Length'],round($entriesStats[0]['Sum_Length']),"<convert>#label=762<convert> :","","<convert>#label=268<convert>");//Dev. Total ://m
    $innerHTML .= getInnerLine($entriesStats[0]['Avg_Length'],round($entriesStats[0]['Avg_Length']),"<convert>#label=763<convert> :","","<convert>#label=268<convert>");//Dev. Moy. ://m
    $innerHTML .= getInnerLine($entriesStats[0]['Std_Length'],round($entriesStats[0]['Std_Length']),"<convert>#label=764<convert> :","","<convert>#label=268<convert>");//E.Type Dev. ://m
    if ($isConnected) {
      $innerHTML .= getInnerLine($data[0]['Locked'],convertYN($data[0]['Locked'],"<convert>#label=441<convert>","<convert>#label=442<convert>"),"<convert>#label=443<convert> :");//Oui //Non //Est en cours de modification
      $innerHTML .= getInnerLine($data[0]['Is_public'],convertYN($data[0]['Is_public'],"<convert>#label=441<convert>","<convert>#label=442<convert>"),"<convert>#label=444<convert> :");//Est publique
    }
		$innerHTML .= getInnerLine($data[0]['typeName'],$data[0]['typeName'],"<convert>#label=114<convert> :");//Type de sous-sol
    if ($is_shown) {
      $innerHTML .= getInnerLine($data[0]['Surname'],$data[0]['Surname'],"<convert>#label=200<convert> :");//Prénom
    }
    $innerHTML .= getInnerLine($data[0]['Nickname'],$data[0]['Nickname'],"<convert>#label=34<convert> :");//Alias
    if ($is_shown) {
      $innerHTML .= getInnerLine($data[0]['Date_birth'],timeToStr($data[0]['Date_birth']),"<convert>#label=445<convert> ","","<convert>#label=446<convert>",getSinceDateFromD(cDate($data[0]['Date_birth'],false),$labelsBlank),"","<convert>#label=447<convert>");//Né(e) le //(mm/jj/aaaa) //ans
    }
    $innerHTML .= getInnerLine($data[0]['Year_discovery'],$data[0]['Year_discovery'],"<convert>#label=109<convert> :","","",getSinceDateFromD("01/01/".$data[0]['Year_discovery'],$labelsSinceDate));//Année de découverte
    $innerHTML .= getInnerLine($data[0]['Year_birth'],$data[0]['Year_birth'],"<convert>#label=147<convert> :","","",getSinceDateFromD("01/01/".$data[0]['Year_birth'],$labelsSinceDate));//Année de fondation
    if ($is_shown) {
      //$innerHTML .= getInnerLine($data[0]['Date_inscription'],"<br />".timeToStr($data[0]['Date_inscription']),"<convert>#label=448<convert> ".$_SESSION['Application_name']." :","","<convert>#label=446<convert><br />",getSinceDateFromD(cDate($data[0]['Date_inscription'],false),$labelsSinceDate));//Date d'inscription à //(mm/jj/aaaa)
      $innerHTML .= getInnerLine($data[0]['Connection_counter'],$data[0]['Connection_counter'],"<convert>#label=449<convert> ","","<convert>#label=450<convert> ".$_SESSION['Application_name']);//S'est connecté //fois sur
      //$innerHTML .= getInnerLine($data[0]['Date_last_connection'],"<br />".timeToStr($data[0]['Date_last_connection']),"<convert>#label=24<convert> :","","<convert>#label=446<convert><br />",getSinceDateFromDT(cDate($data[0]['Date_last_connection'],false),$labelsSinceDate));//Dernière connection //(mm/jj/aaaa)
    }
    if ($isConnected) {
      //$innerHTML .= getInnerLine($data[0]['Date_reviewed'],"<br />".timeToStr($data[0]['Date_reviewed']),"<convert>#label=451<convert> :","","<convert>#label=446<convert><br />",getSinceDateFromDT(cDate($data[0]['Date_reviewed'],false),$labelsSinceDate));//Dernière modification //(mm/jj/aaaa)
    }
    if ($is_shown) {
      $innerHTML .= getInnerLine($data[0]['Year_initiation'],$data[0]['Year_initiation'],"<convert>#label=204<convert> :");//Année d'initiation à la spéléo
      if ($category == "entry") {
				$innerHTML .= getInnerLine($data[0]['Contact'],$data[0]['Contact'],"<convert>#label=741<convert> :");//Contact
      } else {
				$innerHTML .= getInnerLine($data[0]['Contact'],$data[0]['Contact'],"<convert>#label=146<convert> :","mailto:".$data[0]['Contact']);//Contact
      }
      if (isset($data[0]['City'])) {
        $innerHTML .= "<div class=\"detail_line\"><span class=\"details_label\"><convert>#label=102<convert> :</span><br />\n";//Adresse
        if ($category != "entry") {
          $innerHTML .= getInnerLine($data[0]['Address'],$data[0]['Address'])."<br />\n";
        }
        $innerHTML .= getInnerLine($data[0]['City'],$data[0]['Postal_code']." ".$data[0]['City'])."<br />\n";
        $innerHTML .= getInnerLine($data[0]['Region'],$data[0]['Region'].", ".strtoupper(getCountry($_SESSION['language'],$data[0]['Country'])))."<br />\n";
        $innerHTML .= "</div>";
      } else {
				$innerHTML .= getInnerLine($data[0]['Country'],getCountry($_SESSION['language'],$data[0]['Country']),"<convert>#label=98<convert> :");//Pays
			}
      $innerHTML .= getInnerLine($data[0]['Language'],$data[0]['Language'],"<convert>#label=205<convert> :");//Langue
    }
    if ($is_shown) {
      if ($data[0]['Latitude'] != "" && $data[0]['Longitude'] != "") {
        $innerHTML .= "<div class=\"detail_line\"><span class=\"details_label\"><convert>#label=660<convert></span>";
				$innerHTML .= " <span class=\"details_data\"><convert>#label=286<convert></span></div>";//Système géodésique //(GPS) - WGS84 Décimal
				$innerHTML .= getInnerLine($data[0]['Latitude'],round($data[0]['Latitude'],5),"<convert>#label=103<convert> :","","<convert>#label=104<convert> <convert>#label=293<convert>.");//Latitude //degrés N.
				$innerHTML .= getInnerLine($data[0]['Longitude'],round($data[0]['Longitude'],5),"<convert>#label=105<convert> :","","<convert>#label=104<convert> <convert>#label=294<convert>.");//Longitude //degrés S.
				$innerHTML .= "<div class=\"detail_line\"><input type=\"button\" name=\"convert\" class=\"button1\" value=\"<convert>#label=31<convert>...\" onclick=\"JavaScript:";
				$innerHTML .= "openWindow('converter_".$_SESSION['language'].".php?c=coords&amp;lat=".$data[0]['Latitude']."&amp;lng=".$data[0]['Longitude']."&amp;i=".$data[0]['Country']."&amp;readonly=true', '', 434, 260);\" /></div>"; //<!--Convertisseur...-->
      }
    }
    $innerHTML .= getInnerLine($data[0]['Altitude'],$data[0]['Altitude'],"<convert>#label=106<convert> :","","<convert>#label=66<convert>");//Altitude //mètres
    $innerHTML .= getInnerLine($data[0]['NetwId'],$data[0]['NetwName'],"<convert>#label=453<convert>","JavaScript:detailMarker(undefined, 'cave', ".$data[0]['NetwId'].", '".$_SESSION['language']."')","",$data[0]['NumberOfEntries'],"<convert>#label=454<convert>","<convert>#label=455<convert>");//Fait partie du réseau //ayant //entrées
    $innerHTML .= getInnerLine($data[0]['MasId'],$data[0]['MasName'],"<convert>#label=560<convert> ","JavaScript:detailMarker(undefined, 'massif', ".$data[0]['MasId'].", '".$_SESSION['language']."')");//Fait partie du massif
    //$innerHTML .= getInnerLine($data[0]['Min_depth'],$data[0]['Min_depth'],"<convert>#label=456<convert> :","","<convert>#label=66<convert>");//Profondeur Min. //mètres
    //$innerHTML .= getInnerLine($data[0]['Max_depth'],$data[0]['Max_depth'],"<convert>#label=457<convert> :","","<convert>#label=66<convert>");//Profondeur Max. //mètres
    $innerHTML .= getInnerLine($data[0]['Depth'],$data[0]['Depth'],"<convert>#label=64<convert> :","","<convert>#label=66<convert>");//Profondeur Min. //mètres
    $innerHTML .= getInnerLine($data[0]['Length'],$data[0]['Length'],"<convert>#label=68<convert> :","","<convert>#label=66<convert>");//Développement //mètres
    $innerHTML .= getInnerLine($data[0]['Temperature'],$data[0]['Temperature'],"<convert>#label=69<convert> :","","<convert>#label=70<convert>");//Température //degrés celsius
    $innerHTML .= getInnerLine($data[0]['Is_diving'],convertYN($data[0]['Is_diving'],"<convert>#label=441<convert>","<convert>#label=442<convert>"),"<convert>#label=71<convert> :");//Oui //Non //Spéléo. plongée
    if ($category == "entry") {
      $innerHTML .= getInnerLine($data[0]['Partners'],replaceLinks($data[0]['Partners']),"<convert>#label=670<convert> :<br />\n");//Sites partenaires
    }
    $innerHTML .= getInnerLine($AvgAe,round($AvgAe,1),"<convert>#label=458<convert> :","","<convert>#label=459<convert>");//Intérêt ///10
    if (isset($AvgAe)) {
      $innerHTML .= $histoAe;
    }
    $innerHTML .= getInnerLine($AvgCa,round($AvgCa,1),"<convert>#label=460<convert> :","","<convert>#label=459<convert>");//Progression ///10
    if (isset($AvgCa)) {
      $innerHTML .= $histoCa;
    }
    $innerHTML .= getInnerLine($AvgAp,round($AvgAp,1),"<convert>#label=461<convert> :","","<convert>#label=459<convert>");//Accès ///10
    if (isset($AvgAp)) {
      $innerHTML .= $histoAp;
    }
    if ($category == "entry" && $addTitle) {
      $innerHTML .= "<a href=\"#\" onclick=\"JavaScript:detailMarker(event, 'entry', '".$id."', '".$_SESSION['language']."',true, {'geodesic': '".$systemArray["geodesic"]."','length': '".$systemArray["length"]."','temperature': '".$systemArray["temperature"]."'});\" title=\"<convert>#label=184<convert>\" style=\"color:red;\"><!--Voir la fiche détaillée de cette entrée-->\n";
		  $innerHTML .= "<convert>#label=185<convert>\n";//Fiche détaillée...
			$innerHTML .= "</a><br />\n";
		}
    if ($isConnected && allowAccess(properties_view_all)) {
      $innerHTML .= "### Reserved for webmasters: ###<br />\n";
      foreach ($data[0] as $key => $value) {
        if ($key != "Custom_message") {
          $innerHTML .= "<b>".$key."</b> : <i>".replaceLinks(nl2br($value))."</i><br />\n";
        }
      }
    }
  }
  if ($addTitle) {
    $innerHTML .= getBotMenu();
  }
  $innerHTML = "<div class=\"menu\">".$innerHTML."</div>";
  return $innerHTML;
}
?>