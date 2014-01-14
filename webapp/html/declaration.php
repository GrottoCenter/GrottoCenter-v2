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
//http://www.commentcamarche.net/php/phpvar.php3

  //Define translation var
  $ConvertedFilesArray = array( "fileName" => array("application.php","error.php","banner.php","file.php","cave.php","massif.php","caverInfowindow.php",
                                                    "connection.php","details.php","converter.php","entry.php","entryInfowindow.php","filter.php",
                                                    "grotto.php","url.php","grottoInfowindow.php","overview.php","pick_a_place.php","parameters.php",
                                                    "contact.php","properties.php","mailfunctions.php","altitude.php","home.php","portlet.php",
                                                    "pick_window.php","administration.php","grottoChat.php","help.php","geoportail.php","export.php",
                                                    "activation.php","request.php","request_diagram.php","density.php","crs_info.php"),
                                "frame" => array( "general","general","banner","file","filter","filter","overview","filter","details","details",
                                                  "filter","overview","filter","filter","filter","overview","overview","overview","filter","filter",
                                                  "loader","loader","loader","home","home","home","filter","filter","general","general","filter",
                                                  "filter","filter","filter","home","details"));

  //Get the hostname
  if (!isset($_SESSION['Application_host'])) {
    $app_prop = appProp();
    $_SESSION['Application_host'] = $app_prop['Host'];
  }
  
  // Log the user out
  $logout_user_now = (isset($_GET['logout'])) ? ($_GET['logout'] == "true") : false;
  if ($logout_user_now) {
    //Delete the cookie
    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time()-42000, '/');
    }
    //Delete the session file
    throwMySession();
    //Kill the session
    @session_destroy();
    //Unset the logout variable
    //unset($_GET['logout']);
  }

  // Suspend users sessions
  $suspend_user_now = (isset($_GET['suspend'])) ? $_GET['suspend'] : '';
  $suspend_user_now = str_replace("\\","",$suspend_user_now);
	if (isset($_GET['suspend'])) {
    if ($suspend_user_now == $_SESSION['user_login']) {
      $thrown = throwSessions();
    }
  }
  
  //Set the language into a session variable
  $lang_user_now = (isset($_GET['lang'])) ? $_GET['lang'] : '';
	if (isset($_GET['lang']))	{
    $_SESSION['language']  = checkLang($lang_user_now);
    $_SESSION['Update_application_noframe'] = "True";
    unset($_SESSION['filter_page']);
  }

  //First time the user connects
  $check_lang_auto = (isset($_GET['check_lang_auto'])) ? ($_GET['check_lang_auto'] == "true") : true;
  if (!isset($_SESSION['language']) && $check_lang_auto) {
      //Get the client language
      $clientLang = explode(",", $HTTP_ACCEPT_LANGUAGE);
      $clientLang = ucFirst(StrToLower(subStr(rTrim($clientLang[0]), 0, 2)));
      //Check if this language is available (return En if not)
      $language = checkLang($clientLang);
      //If the requested URI is a file
      if (strpos($_SERVER["PHP_SELF"], '.php') !== false) {//$_SERVER['REQUEST_URI'], '.php') !== false) {
        //Get the file's name
        $parentName = basename($_SERVER["PHP_SELF"]);
      } else {
        //Get the default value
        $parentName = "index.php";
      }
      //Get the URL Parameters
      $url_parameters = "";
      foreach($_GET as $key => $value) {
        if ($key != "lang" && $key != "logout") {
        	$url_parameters .= "&".$key."=".$value;
        }
      }
      //Reload the page with the client's language
      header("location:".$parentName."?check_lang_auto=false&lang=".$language.$url_parameters);
      exit();
  }
  
  //Set the home page session variable
  $_SESSION['home_page'] = (isset($_SESSION['home_page'])) ? $_SESSION['home_page'] : "home";
  $_SESSION['home_page'] = (isset($_GET['home_page'])) ? urldecode($_GET['home_page']) : $_SESSION['home_page'];
  
  //Set the filter page session variable
  $_SESSION['filter_page'] = (isset($_SESSION['filter_page'])) ? $_SESSION['filter_page'] : "filter_".$_SESSION['language'].".php";
  $_SESSION['filter_page'] = (isset($_GET['home_page'])) ? "filter_".$_SESSION['language'].".php" : $_SESSION['filter_page'];
  $_SESSION['filter_page'] = (isset($_GET['filter_page'])) ? urldecode($_GET['filter_page']) : $_SESSION['filter_page'];
  
  // Remove the lock status when unloading an editor page
  $cancel_user_now = (isset($_GET['cancel'])) ? ($_GET['cancel'] == "True") : false;
  if ($cancel_user_now) {
    $cid = (isset($_GET['cid'])) ? $_GET['cid'] : '';
    $ccat = (isset($_GET['ccat'])) ? $_GET['ccat'] : '';
    if ($cid != "") {
      backOver($ccat,$cid);
    }
  }
  
  //Get the user's rights
  $user_id_for_rights = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0;
  $_SESSION['user_rights'] = (isset($_SESSION['user_rights'])) ? $_SESSION['user_rights'] : getUserRights($user_id_for_rights);

	//Show the coords converter if needed
	$_SESSION['show_converter'] = (isset($_GET['c'])) ? true : ((isset($_SESSION['show_converter'])) ? $_SESSION['show_converter'] : false);
	
  define ("USER_IS_CONNECTED", userIsConnected(), true);
  
  $FAQPages = array("Fr" => array("home" => 13),"En" => array("home" => 14),"Es" => array("home" => 19));
  
  define ("LEADER_GROUP_ID", 5, true);
  define ("ENTRY_COUNT_MAX", 3000, true);
  define ("Max_detail_level", 1000, true);
  define ("Select_default", "00", true);
  define ("start_comment", "<!--", true);
  define ("end_comment", "-->", true);
  define ("Contact_for_nobody", "0", true);
  define ("Contact_for_registered", "1", true);
  define ("Contact_for_everybody", "2", true);
  define ("Google_key","ABQIAAAABppewhix0m2aGtrxzFsM1hTUoYxFMVJ0pZ8eIP2qT6O2FCqTDBSrYiCqarW5lo9hEXEt4pCtZ6bVVA", true); //GMaps API Key for grottocenter.org
  //define ("Google_key", "ABQIAAAA_X2bDeJ9Hz-baUkItUM1WRQODwNLvymVen2-L56iEshlhUPpFBShcyTJURuPJ0Mx3AIa8-nTBRJBXg", true); //GMaps API Key for localhost
  define ("Geoportal_key", "2228631060319443257", true);//Geoportal API Key for grottocenter.org
  //define ("Geoportal_key", "", true);//Geoportal API Key for localhost
  define ("Analytics_key", "UA-4684361-2", true);//Analytics API Key for grottocenter.org
  //define ("Analytics_key", "", true);//Analytics API Key for localhost
  
	require("cst_declaration.php");
	
  //rights :
  /*$sql = "SELECT Name, Id FROM `".$_SESSION['Application_host']."`.`T_right` ";
  $data = getDataFromSQL($sql, __FILE__, '', __FUNCTION__);
  for($i=0;$i<$data['Count'];$i++) {
    define ($data[$i]['Name'], $data[$i]['Id'], true);
  }*/
  define ("appli_view_all", 1, true);
  define ("appli_edit_all", 2, true);
  define ("appli_delete_all", 3, true);
  define ("biblio_view_all", 4, true);
  define ("biblio_edit_all", 5, true);
  define ("biblio_delete_all", 6, true);
  define ("cave_view_all", 7, true);
  define ("cave_edit_all", 8, true);
  define ("cave_delete_all", 9, true);
  define ("caver_view_all", 10, true);
  define ("caver_edit_all", 11, true);
  define ("caver_delete_all", 12, true);
  define ("comment_view_all", 13, true);
  define ("comment_edit_all", 14, true);
  define ("comment_delete_all", 15, true);
  define ("description_view_all", 16, true);
  define ("description_edit_all", 17, true);
  define ("description_delete_all", 18, true);
  define ("entry_view_all", 19, true);
  define ("entry_edit_all", 20, true);
  define ("entry_delete_all", 21, true);
  define ("grotto_view_all", 22, true);
  define ("grotto_edit_all", 23, true);
  define ("grotto_delete_all", 24, true);
  define ("group_view_all", 25, true);
  define ("group_edit_all", 26, true);
  define ("group_delete_all", 27, true);
  define ("history_view_all", 28, true);
  define ("history_edit_all", 29, true);
  define ("history_delete_all", 30, true);
  define ("location_view_all", 31, true);
  define ("location_edit_all", 32, true);
  define ("location_delete_all", 33, true);
  define ("massif_view_all", 34, true);
  define ("massif_edit_all", 35, true);
  define ("massif_delete_all", 36, true);
  define ("rigging_view_all", 37, true);
  define ("rigging_edit_all", 38, true);
  define ("rigging_delete_all", 39, true);
  define ("right_view_all", 40, true);
  define ("right_edit_all", 41, true);
  define ("right_delete_all", 42, true);
  define ("url_view_all", 43, true);
  define ("url_edit_all", 44, true);
  define ("url_delete_all", 45, true);
  define ("warning_view_all", 46, true);
  define ("error_view_all", 47, true);
  define ("caver_edit_himself", 50, true);
  define ("caver_delete_himself", 51, true);
  define ("properties_view_all", 52, true);
  define ("keep_connected", 53, true);
  define ("location_lock_all", 54, true);
  define ("description_lock_all", 55, true);
  define ("rigging_lock_all", 56, true);
  define ("history_lock_all", 57, true);
  define ("biblio_lock_all", 58, true);
  define ("comment_lock_all", 59, true);
  define ("translation_view_all", 60, true);
  define ("translation_edit_all", 61, true);
  define ("translation_delete_all", 62, true);
  define ("cache_refresh_all", 63, true);
  define ("chat_all", 64, true);
  define ("request_view_all", 65, true);
  define ("request_edit_all", 66, true);
  define ("request_delete_all", 67, true);
  define ("request_approve_all", 68, true);
  define ("request_view_mine", 69, true);
  define ("request_edit_mine", 70, true);
  define ("request_delete_mine", 71, true);
  define ("entry_export_all", 72, true);
  define ("topo_view_all", 73, true);
?>