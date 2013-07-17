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
$Reset_application_vars = (isset($_GET['Reset_application_vars'])) ? ($_GET['Reset_application_vars'] == "True") : false;
$Application_data_is_set = (isset($_SESSION['Application_data_set'])) ? ($_SESSION['Application_data_set'] != "") : false;
if (!$Application_data_is_set || $Reset_application_vars || $_SESSION['Update_application_noframe'] == "True") {
  $app_prop = appProp();
  $_SESSION['Application_url'] = $app_prop['Url'];
  $_SESSION['Application_mail'] = $app_prop['Contact'];
  $_SESSION['Application_name'] = $app_prop['Name'];
  $_SESSION['Application_title'] = $app_prop['Name']." ".$app_prop['Version']." <convert>#label=729<convert>";
  $_SESSION['Application_version'] = $app_prop['Version'];
  $_SESSION['Application_timer'] = $app_prop['Timer_min'];
  $_SESSION['Application_availability'] = $app_prop['Availability'];
  $_SESSION['Application_message'] = $_SESSION['Application_title']."<convert>#label=156<convert>".$app_prop['Estimated_reopening_time']."<br /><a href=\"JavaScript:window.top.location='".$_SESSION['Application_url']."/index.php?logout=true';\"><convert>#label=334<convert> <!--Raffraichir la page--></a>"; //<br /><br />Work in progress, please come back later...<br />Estimated time for reopening :
  //$_SESSION['Application_message'] = $_SESSION['Application_title']."<convert>#label=581<convert> :<br />"."<a href=\"http://www.grottocenter.org\" title=\"GrottoCenter\">www.grottocenter.org</a><br />"."<convert>#label=582<convert>.<br /><a href=\"JavaScript:window.top.location='".$_SESSION['Application_url']."/index.php?logout=true';\"><convert>#label=334<convert> <!--Raffraichir la page--></a>"; 
  $_SESSION['Application_revision'] = $app_prop['Revision'];
  $_SESSION['Application_creation'] = $app_prop['Creation'];
  $_SESSION['Application_authors'] = $app_prop['Authors'];
  $_SESSION['Application_copyright'] = $app_prop['Copyright'];
  $_SESSION['Application_authors_contact'] = $app_prop['Authors_contact'];
  $_SESSION['Application_thanks'] = $app_prop['Thanks'];
  $_SESSION['Application_noframe'] = "<convert>#label=157<convert>";//Frames are not supported !
  $_SESSION['Application_data_set'] = True;
  if (allowAccess(keep_connected)) {
    $_SESSION['Application_availability'] = 1;
  } else {
    if ($_SESSION['Application_availability'] != 1) {
      $labels = getLabelArray("general", $_SESSION['language']);
      $script = '<script type="text/javascript" charset="UTF-8">';
      $script .= getCDataTag(true);
      $script .= 'alert("<convert>#label=158<convert> '.$_SESSION['Application_timer'].' <convert>#label=159<convert> '.$_SESSION['Application_title'].' <convert>#label=160<convert>");';//Vous allez \352tre d\351connect\351 dans //minutes car //va entrer en mode de maintenance.\nVeuillez validez votre travail en cours. 
      $script .= 'mySite.setLogOff('.$_SESSION['Application_timer'].',"<convert>#label=161<convert> '.$_SESSION['Application_title'].' <convert>#label=162<convert>");';//Vous allez \352tre d\351connect\351 car //est en cours de maintenance.\nVeuillez nous en excuser.
      $script .= getCDataTag(false);
      $script .= '</script>';
      echo $script;
    }
  }
}
if ($_SESSION['Update_application_noframe'] == "True") {
  $_SESSION['Application_noframe'] = "<convert>#label=157<convert>";//Frames are not supported !
  $_SESSION['Update_application_noframe'] = "False";
  unset($_SESSION['Content_for_meta_tags']);
}

function getLicense($imgType = 1) {
  $license = "";
  $title = "";
  switch($imgType) {
    case 1:
    case 2:
    case 3:
      $license .= "<br />";
      $license .= "<span>".$_SESSION['Application_name']."</span> ";
			//$license .= "<convert>#label=507<convert> <a href=\"http://perso.apec.fr/clement_ronzon\" target=\"_blank\">".$_SESSION['Application_authors']."</a> "; //par
			$license .= "<convert>#label=508<convert> ";// est mis à disposition selon les termes de la"
      $license .= "<a href=\"http:/"."/creativecommons.org/licenses/by-sa/3.0/deed.".strtolower($_SESSION['language'])."\" target=\"_blank\"> <convert>#label=509<convert></a>";//licence Creative Commons Attribution - Partage dans les Mêmes Conditions 3.0 non transposé.</a>."
      break;
  }
  $title = $_SESSION['Application_name'];
	//$title .= " <convert>#label=507<convert> ".$_SESSION['Application_authors'];
	$title .= " <convert>#label=508<convert> <convert>#label=509<convert>";
  $license = getLicensePicture($imgType,$title).$license;
  return $license;
}
?>