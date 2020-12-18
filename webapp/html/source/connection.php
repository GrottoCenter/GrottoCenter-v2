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
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
include("../func/phpBBinterface.php");
$frame = "filter";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=187<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
    //Variables initialisation
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $alert_for_news = "YES";
    $country = getCountryCodeByIP(getIp());
    $register_language = $_SESSION['language'];
    $name = "";
    $surname = "";
    $login = "";
    $password = "";
    $key = "";
    $contact = "";
    $latitude = "";
    $longitude = "";
    $default_lat = "";
    $default_lng = "";
    $default_zoom = "";
    $tmp_key = rand(1,8);
    $connection_failed = false;
    $choose_another_login = false;
    $registered = true;
    $save_failed = false;
    $reload = (isset($_POST['n_reload'])) ? ($_POST['n_reload'] == "True") : false;
	  $was_shown = (isset($_POST['n_i_was_shown'])) ? ($_POST['n_i_was_shown'] == "True") : false;
	  $string = "";
	  $user_check = "";
	  $helpId = array("login" => 1, "new" => 2, "forgotten" => 3);
	  $activated = true;
	  $banned = false;

    //Save the new user's parameters :
	  if (isset($_POST['save']) || $reload){
	    $name = (isset($_POST['n_caver_name'])) ? $_POST['n_caver_name'] : '';
	    $surname = (isset($_POST['n_caver_surname'])) ? $_POST['n_caver_surname'] : '';
	    $login = (isset($_POST['n_caver_login'])) ? $_POST['n_caver_login'] : '';
	    $password = (isset($_POST['n_caver_password'])) ? $_POST['n_caver_password'] : '';
	    $key = (isset($_POST['n_key'])) ? $_POST['n_key'] : '';
	    $password = crypt_xor(stripslashes($password), $key);
	    $country = (isset($_POST['n_caver_country'])) ? $_POST['n_caver_country'] : '';
	    $contact = (isset($_POST['n_caver_contact'])) ? $_POST['n_caver_contact'] : '';
			$register_language = (isset($_POST['n_caver_language'])) ? $_POST['n_caver_language'] : '';
      if ($register_language == "" || $register_language == Select_default) {
        $register_language = $_SESSION['language'];
      }
	    $alert_for_news = (isset($_POST['n_caver_news'])) ? $_POST['n_caver_news'] : '';
	    $latitude = (isset($_POST['n_caver_latitude'])) ? $_POST['n_caver_latitude'] : '';
	    $longitude = (isset($_POST['n_caver_longitude'])) ? $_POST['n_caver_longitude'] : '';
	    $default_lat = (isset($_POST['n_caver_default_lat'])) ? $_POST['n_caver_default_lat'] : '';
	    $default_lng = (isset($_POST['n_caver_default_lng'])) ? $_POST['n_caver_default_lng'] : '';
	    $default_zoom = (isset($_POST['n_caver_default_zoom'])) ? $_POST['n_caver_default_zoom'] : '';
	    //If it's not a reload
      if (!$reload) {
      	//If the form is correctly filled
  	    if (formIsValid()) {
          $sql = "SELECT * FROM T_caver WHERE Login = ".returnDefault($login, 'text');
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          if ($data['Count'] == 0) {
            $activation_code = generatePassword(10,8);
      	    $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_caver` ";
      	    $sql .= "(`Name`, `Surname`, `Login`, `Nickname`, `Password`, `Country`, `Contact`, `Date_inscription`, `Language`, `Contact_is_public`, `Alert_for_news`, `Latitude`, `Longitude`, `Default_latitude`, `Default_longitude`, `Default_zoom`, `Activation_code`)";
      	    $sql .= " VALUES (";
      	    $sql .= returnDefault($name, 'Name').", ";
      	    $sql .= returnDefault($surname, 'text').", ";
      	    $sql .= returnDefault($login, 'text').", ";
      	    if ($surname != "" && $name != "") {
              $sql .= returnDefault($surname." ".$name, 'Name').", ";
            } else {
              $sql .= returnDefault($login, 'Name').", ";
            }
      	    $sql .= returnDefault(getCryptedPwd($login,$password), 'text').", ";
      	    $sql .= returnDefault($country, 'list').", ";
      	    $sql .= returnDefault($contact, 'text').", ";
      	    $sql .= "Now(), ";
      	    $sql .= returnDefault($register_language, 'list').", ";
      	    $sql .= "1, ";
      	    $sql .= returnDefault($alert_for_news, 'checkbox').", ";
      	    $sql .= returnDefault($latitude, 'latlng').", ";
      	    $sql .= returnDefault($longitude, 'latlng').", ";
      	    $sql .= returnDefault($default_lat, 'text').", ";
      	    $sql .= returnDefault($default_lng, 'text').", ";
      	    $sql .= returnDefault($default_zoom, 'text').", ";
      	    $sql .= returnDefault($activation_code, 'text').")";
      	    $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      	    $new_id = $req['mysql_insert_id'];
      	    $defaultUserGroup = 3;
      	    $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_caver_group` ";
      	    $sql .= "(`Id_caver`, `Id_group`)";
      	    $sql .= " VALUES (";
      	    $sql .= $new_id.", ".$defaultUserGroup.") ";
      	    $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            if (!defined('NO_PHPBB_INSTALLED')) {
              addphpBBuser($login,$password,$contact,$language);
            }
      	    sendActivationMail($contact, $login, $password, $new_id, $activation_code);
      	    trackAction("insert_user",$new_id,"T_caver");
      	    $save_failed = false;
      	  } else {
            $choose_another_login = true;
            $save_failed = true;
          }
    	  } else {
          $save_failed = true;
        }
      }
	  }
	  
	  //Connection of a user
	  if (isset($_POST['connection']) && !USER_IS_CONNECTED){
	    $login = (isset($_POST['l_caver_login'])) ? $_POST['l_caver_login'] : '';
	    $password = (isset($_POST['l_caver_password'])) ? $_POST['l_caver_password'] : '';
	    $key = (isset($_POST['l_key'])) ? $_POST['l_key'] : '';
	    $password = crypt_xor(stripslashes($password), $key);
	    $string = (isset($_POST['l_caver_check'])) ? $_POST['l_caver_check'] : '';
	    $connectionArray = connectUser($login, $password, $string);
	    $activated = $connectionArray['Activated'];
	    $banned = $connectionArray['Banned'];
	    $registered = $connectionArray['Registered'];
	    $connection_failed = !$connectionArray['Connected'];
      if ($connection_failed) {
        $_SESSION['login_retry'] += 1; 
      } else {
        if ($result['status'] != LOGIN_SUCCESS && !defined('NO_PHPBB_INSTALLED')) {
          addphpBBuser($login,$password,$_SESSION['user_contact'],$_SESSION['user_language']);
        }
      }
	  } else {
      $_SESSION['login_retry'] = 1;    
    }
	  
	  //Send a new password to the user :
	  if (isset($_POST['send_pwd'])){
	    $login = (isset($_POST['f_caver_login'])) ? $_POST['f_caver_login'] : '';
	    $contact = (isset($_POST['f_caver_contact'])) ? $_POST['f_caver_contact'] : '';
	    $password = generatePassword(10,8);
      $string = (isset($_POST['f_caver_check'])) ? $_POST['f_caver_check'] : '';
      $user_check = (isset($_SESSION['userCheck'])) ? $_SESSION['userCheck'] : '';
      if (!$_SESSION['do_check'] || md5(getIp().strtolower($string)) == $user_check) {
  	    $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_caver`";
  	    $sql .= " WHERE Login = '".$login."' AND Contact ='".$contact."'";
  	    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  	    if ($data['Count'] > 0){
          if ($data[0]['Activated'] == 'YES') {
      	    $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver`";
      	    $sql .= " SET Password ='".getCryptedPwd($login,$password)."'";
      	    $sql .= " WHERE Id = ".$data[0]['Id'];
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            if (!defined('NO_PHPBB_INSTALLED')) {
              chgPwdphpBBuser($login,$password);
            }
      	    sendNewPwdMail($data[0], $password);
  //echo $password;
      	    trackAction("pwd_user",$data[0]['Id'],"T_caver");
      	    $_SESSION['user_pwd_sent'] = true;
      	  } else {
            $activated = false;
            $_SESSION['user_pwd_sent'] = false;
          }
  	    } else {
          $_SESSION['user_pwd_sent'] = false;
          $_SESSION['send_retry'] += 1;
        }
      } else {
        $_SESSION['user_pwd_sent'] = false;
        $_SESSION['send_retry'] += 1;
      }
    } else {
      $_SESSION['send_retry'] = 1;    
    }
	  
    switch ($_SESSION['home_page']) {
      case "overview":
        $back_src = "filter_".$_SESSION['language'].".php";
        $back_delta = 0;
      break;
      case "home":
      default:
        $back_src = "connection_".$_SESSION['language'].".php?type=login";
        $back_delta = -1;
      break;
    }
	  
	  function formIsValid() {
		  $string = (isset($_POST['n_caver_check'])) ? $_POST['n_caver_check'] : '';
      $user_check = (isset($_SESSION['userCheck'])) ? $_SESSION['userCheck'] : '';
      $check = md5(getIp().strtolower($string)) == $user_check;
      return $check;
    }
	  
	  deleteImage();
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
<?php
    switch ($type)
    {
    	case "login":
?>
    function loginSubmit(event) {
      var oForm = document.login_user;
      oForm.l_caver_password.value = c(oForm.l_caver_password.value, oForm.l_key.value);
      return true;
    }
    
    function loginBeforeLoad(failed) {
      if (failed) {
        mySite.setFilterSize(280,'px');
      } else {
        mySite.setFilterSize(15);
      }
    }
    
    function loginOnLoad(failed) {
      var oForm = document.login_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
<?php
if ($banned) { ?>
			alert("<convert>#label=536<convert>"); //Vous avez été banni du site. Si vous pensez qu'il s'agit d'une erreur veuillez contacter votre administrateur.
<?php } ?>
      if (failed) {
        mySite.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        mySite.setFilterSizeTight(oHtml);
      } else {
        window.top.location = "../index.php?lang=<?php echo $_SESSION['language']; ?>";
      }
    }
    
<?php
			break;
    	case "new":
?>
    var namesArray = new Array();
    
    function newBeforeLoad(failed) {
      if (failed) {
        mySite.setFilterSize(488,'px');
      } else {
        mySite.setFilterSize(25);
      }
    }
    
    function countryOnChange(oSelect) {
      var value, mainFrame;
      if (oSelect.selectedIndex !== 0) {
        value = oSelect.options[oSelect.selectedIndex].id;
        if (mySite.overview) {
          mainFrame = mySite.overview;
        } else if(mySite.home) {
          mainFrame = mySite.home;
        }
        mainFrame.getCoordsByDirection(value, mainFrame.updateDisplay);
      }
    }
    
    function newOnLoad(failed) {
      var oForm, mainFrame;
      oForm = document.new_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      mySite.details.switchDetails(failed);
      if (failed) {
        if (mySite.overview) {
          mainFrame = mySite.overview;
        } else if(mySite.home) {
          mainFrame = mySite.home;
        }
        mainFrame.location = 'pick_a_place_<?php echo $_SESSION['language']; ?>.php?cat=caver&lat=<?php echo $latitude; ?>&lng=<?php echo $longitude; ?>';
        mySite.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        mySite.setFilterSizeTight(oHtml);
        namesArray = loadNames("caver");
        checkThisName(oForm.n_caver_login);
        checkMail(oForm.n_caver_contact, 'mail_pic');
        checkPwd(oForm.n_caver_password, 'pwd_pic');
        checkPwd2(oForm.n_caver_password2, oForm.n_caver_password, 'pwd2_pic');
      } else {
        if (mySite.overview) {
          reload();
        }
      }
    }
    
    function checkThisName(oObject) {
      checkName(oObject, "login_pic", "caver", "", namesArray, true);
    }
    
    function newOnUnload(failed) {
      mySite.details.switchDetails(false);
      if (mySite.overview) {
        mySite.overview.location = 'overview_<?php echo $_SESSION['language']; ?>.php';
      } else if(mySite.home) {
        mySite.home.location = 'home_<?php echo $_SESSION['language']; ?>.php';
      }
    }
    
    function newCancel() {
      connectUser("<?php echo $_SESSION['language']; ?>");
    }
    
    function newSubmit(event) {
      var oForm = document.new_user;
      if (!userIsOk(document)) {
        stopSubmit(event);
      } else {
        oForm.n_caver_password.value = c(oForm.n_caver_password.value, oForm.n_key.value);
        oForm.n_caver_password2.value = "";
        if (mySite.overview) {
          saveDefaultPosition();
          oForm.n_caver_default_lat.value = getDefaultLat();
          oForm.n_caver_default_lng.value = getDefaultLng();
          oForm.n_caver_default_zoom.value = getDefaultZoom();
        } else {
          oForm.n_caver_default_lat.value = oForm.n_caver_latitude.value;
          oForm.n_caver_default_lng.value = oForm.n_caver_longitude.value;;
          oForm.n_caver_default_zoom.value = "4";
        }
      }
    }
    
    function newReload() {
      var oForm = document.new_user;
      oForm.n_reload.value='True';
      oForm.submit();
    }
    
    function recieveLocation(lat, lng) {
      var oForm = document.new_user;
      oForm.n_caver_latitude.value = lat;
      oForm.n_caver_longitude.value = lng;
    }
    
<?php
			break;
    	case "forgotten":
?>   
    
    function forgotBeforeLoad(sent) {
      if (sent) {
        mySite.setFilterSize(330,'px');
      } else {
        mySite.setFilterSize(35);
      }
    }
    
    function forgotOnLoad(sent) {
      var oForm = document.forgot_pwd;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      if (!sent) {
        mySite.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        mySite.setFilterSizeTight(oHtml);
      }
    }
    
    function forgotSubmit(event) {
      return true;
    }
    
    function forgotCancel() {
      connectUser("<?php echo $_SESSION['language']; ?>");
    }
    
<?php
			break;
    	default:
?> 
    
    function defaultBeforeLoad() {
      mySite.resetFilterSize();
    }
    
    function defaultOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
    
<?php
		}
?> 
   
    
<?php
if ($_SESSION['home_page'] == "overview") {
  include("../scripts/events.js");
}
    switch ($type)
    {
    	case "login":
        if (!isset($_POST['connection']) || $connection_failed){
?>
      loginBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:loginOnLoad(true);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("connection_".$_SESSION['language'].".php?type=login", "connect_filter", "<convert>#label=4<convert>", 2+$back_delta); ?></div><!-- Connexion -->
<?php
if (!$banned) {
  if (!$registered) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=189<convert><!--Erreur de mot de passe ou d'identifiant.<br />Veuillez recommencer.--><?php echo getBotBubble(); ?></div>
<?php
  } elseif (!$activated) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=823<convert><!--Votre compte est innactif et doit être activé avant sa première utilisation.<br />Veuillez activer votre compte ou contactez votre administrateur.--><?php echo getBotBubble(); ?></div>
<?php
  }
}
?>
<iframe jsname="L5Fo6c" class="YMEQtf DnR2hf" sandbox="allow-scripts allow-popups allow-forms allow-same-origin allow-popups-to-escape-sandbox allow-downloads" frameborder="0" aria-label="Google Forms, Contact Wikicaves EN" src="https://docs.google.com/forms/d/e/1FAIpQLSfqRdI9a0uFmgE06kOvZWJe8Ly6T_CUcrqJwE6xT-6Zy0EdxA/viewform?embedded=true" style="height: 99%;min-width: 400px;" allowfullscreen=""></iframe>
		<form id="login_user" name="login_user" method="post" action="" onsubmit="JavaScript:loginSubmit(event);" style="display: none;">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
  				<label for="l_caver_login">
					  <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=192<convert><!--Pseudonyme-->
  				</label>
  			</td><td class="field">
				  <input class="input1" type="text" name="l_caver_login" id="l_caver_login" value="" size="10" maxlength="20" />
				</td></tr><tr><td width="170" class="label">
  				<label for="l_caver_password">
					  <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=193<convert><!--Mot de passe-->
					</label>
				</td><td class="field">
          <input class="input1" type="password" name="l_caver_password" id="l_caver_password" value="" size="10" maxlength="32" />
        </td></tr><tr><td width="170" class="label">
<?php
          $_SESSION['do_check'] = ($_SESSION['login_retry'] > 3);
          if ($_SESSION['do_check']) {
            $_SESSION['userCheck'] = createImage(6, 16);
?>
		      <label for="check">
		      </label>
	      </td><td class="field">
	      	<img class="image1" name="check" id="check" src="<?php echo $_SESSION['userCheck']; ?>.gif" alt="image" />
	      </td></tr><tr><td width="170" class="label">
					<label for="reload_btn">
		        <convert>#label=210<convert><!--Je ne peux pas lire le texte-->
		      </label>
		    </td><td class="field">
          <input class="button1" onclick="JavaScript:reloadCaptcha(6, 16, this, 'check');" type="button" id="reload_btn" name="reload_btn" value="<convert>#label=108<convert>" /><!--Cliquez ici-->
      	</td></tr><tr><td width="170" class="label">
		      <label for="l_caver_check">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=194<convert><!--Texte contenu dans la grotte-->
		      </label>
	      </td><td class="field">
	      	<input class="input1" type="text" id="l_caver_check" name="l_caver_check" value="" size="6" maxlength="6" />
	      </td></tr><tr><td width="170" class="label">
<?php
          }
?>
		      <label for="connection">
	    		</label>
	    	</td><td class="field">
          <input type="hidden" id="l_key" name="l_key" value="<?php echo $tmp_key; ?>" />
          <input class="button1" type="submit" name="connection" id="connection" value="<convert>#label=4<convert>" /><!--Connexion-->
        </td></tr><tr><td colspan="2">
          <br />
          <a href="connection_<?php echo $_SESSION['language']; ?>.php?type=new"><convert>#label=195<convert> <?php echo $_SESSION['Application_name']; ?>...<!--Je suis nouveau sur--></a>
          <br />
	    		<br />
	    		<a href="connection_<?php echo $_SESSION['language']; ?>.php?type=forgotten"><convert>#label=196<convert><!--J'ai oublié mon mot de passe...--></a>
          <br />
	    		<br />
	    		<a href="activation_<?php echo $_SESSION['language']; ?>.php" target="_blank"><convert>#label=777<convert>...<!--Activer mon compter manuellement...--></a>
	    	</td></tr>
      </table>
    </form>
<?php
        } else {
          if (!$connection_failed) {
?>
      loginBeforeLoad(false);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:loginOnLoad(false);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
    <div class="frame_title"><?php echo setTitle("connection_".$_SESSION['language'].".php?type=login", "connect_filter", "<convert>#label=4<convert>", 2+$back_delta); ?></div><!-- Connexion -->
    <div class="info"><?php echo getTopBubble(); ?><convert>#label=722<convert><!--Connexion en cours, veuillez patienter.--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
			break;
    	case "new":
    		if (!isset($_POST['save']) || $save_failed || $reload){
    		  $_SESSION['userCheck'] = createImage(6, 16);
?>
      newBeforeLoad(true);
<?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onunload="JavaScript:newOnUnload(true);" onload="JavaScript:newOnLoad(true);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("connection_".$_SESSION['language'].".php?type=login","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("connection_".$_SESSION['language'].".php?type=new", "connect_filter", "<convert>#label=219<convert>", 3+$back_delta); ?></div><!--Inscription-->
<?php     if ($choose_another_login) { ?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=843<convert><!--L'identifiant de connexion que vous avez saisi est déjà utilisé. Veuillez choisir un autre identifiant !--><?php echo getBotBubble(); ?></div>
<?php     } elseif ($save_failed) { ?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div>
<?php     } ?>
    <div class="info"><?php echo getTopBubble(); ?><convert>#label=689<convert><!--La casse (majuscules/minuscules) est prise en compte-->.<?php echo getBotBubble(); ?></div>
  	<iframe jsname="L5Fo6c" class="YMEQtf DnR2hf" sandbox="allow-scripts allow-popups allow-forms allow-same-origin allow-popups-to-escape-sandbox allow-downloads" frameborder="0" aria-label="Google Forms, Contact Wikicaves EN" src="https://docs.google.com/forms/d/e/1FAIpQLSfqRdI9a0uFmgE06kOvZWJe8Ly6T_CUcrqJwE6xT-6Zy0EdxA/viewform?embedded=true" style="height: 99%;min-width: 400px;" allowfullscreen=""></iframe>
  	<form id="new_user" name="new_user" method="post" action="" onsubmit="JavaScript:newSubmit(event);" style="display:none;">
      <table border="0" cellspacing="1" cellpadding="0" width="460" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
		      <label for="n_caver_login">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><img src="../images/icons/FlagEnterOnce.gif" alt="-" /><img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=192<convert><!--Identifiant--><sup>1</sup>
		      </label>
		    </td><td class="field">
          <input class="input1" type="text" id="n_caver_login" name="n_caver_login" value="<?php echo stripslashes($login); ?>" size="20" maxlength="20" onkeyup="JavaScript:checkThisName(this);" />
      	  <img class="status1" name="login_pic" id="login_pic" src="../images/icons/wrong.png" alt="image" />
      	</td></tr><tr><td width="170" class="label">
					<label for="n_caver_contact">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=197<convert><!--E-mail de contact--><sup>2</sup>
          </label>
        </td><td class="field">
      	  <input class="input1" type="text" id="n_caver_contact" name="n_caver_contact" value="<?php echo $contact; ?>" size="20" maxlength="40" onkeyup="JavaScript:checkMail(this, 'mail_pic');" />
      	  <img class="status1" name="mail_pic" id="mail_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td width="170" class="label">
					<label for="n_caver_password">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=193<convert><!--Mot de passe--><sup>3</sup>
		      </label>
		    </td><td class="field">
          <input class="input1" type="password" id="n_caver_password" name="n_caver_password" value="" size="10" maxlength="32" onkeyup="JavaScript:checkPwd(this, 'pwd_pic');" />
      	  <img class="status1" name="pwd_pic" id="pwd_pic" src="../images/icons/wrong.png" alt="image" />
      	</td></tr><tr><td width="170" class="label">
					<label for="n_caver_password2">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=198<convert><!--Confirmez votre mot de passe-->
		      </label>
		    </td><td class="field">
          <input class="input1" type="password" id="n_caver_password2" name="n_caver_password2" value="" size="10" maxlength="32" onkeyup="JavaScript:checkPwd2(this, xtdGetElementById('n_caver_password'), 'pwd2_pic');" />
      	  <img class="status1" name="pwd2_pic" id="pwd2_pic" src="../images/icons/wrong.png" alt="image" />
    		</td></tr><tr><td width="170" class="label">
					<label for="n_caver_name">
	      		<convert>#label=199<convert><!--Nom-->
	      	</label>
		    </td><td class="field">
          <input class="input1" type="text" id="n_caver_name" name="n_caver_name" value="<?php echo $name; ?>" size="15" maxlength="36" />
    		</td></tr><tr><td width="170" class="label">
					<label for="n_caver_surname">
	      		<convert>#label=200<convert><!--Prénom-->
		      </label>
		    </td><td class="field">
          <input class="input1" type="text" id="n_caver_surname" name="n_caver_surname" value="<?php echo $surname; ?>" size="15" maxlength="32" />
      	</td></tr><tr><td width="170" class="label">
					<label for="n_caver_country">
		      	<convert>#label=98<convert><!--Pays-->
					</label>
		    </td><td class="field">
<?php echo "<!--Ip : ".getIp()." Default country : ".$country."-->"; ?>
          <select class="select2" name="n_caver_country" id="n_caver_country" onchange="JavaScript:countryOnChange(this);">
<?php
          echo getOptionCountry($_SESSION['language'], $country, "<convert>#label=99<convert>");//Sélectionnez un pays...
?>
				 </select>
				</td></tr><tr><td width="170" class="label">
					<label for="n_caver_language">			      
		      	<convert>#label=205<convert><!--Langue-->
		      </label>
		    </td><td class="field">
          <select class="select2" name="n_caver_language" id="n_caver_language">
<?php
          echo getOptionLanguage($register_language);
?>
      	  </select>
      	</td></tr><tr><td width="170" class="label">
					<label for="check">
          </label>
		    </td><td class="field">
          <img class="image2" name="check" id="check" src="<?php echo $_SESSION['userCheck']; ?>.gif" alt="image" />
      	</td></tr><tr><td width="170" class="label">
					<label for="reload_btn">
		        <convert>#label=210<convert><!--Je ne peux pas lire le texte-->
		      </label>
		    </td><td class="field">
          <input class="button1" onclick="JavaScript:reloadCaptcha(6, 16, this, 'check');" type="button" id="reload_btn" name="reload_btn" value="<convert>#label=108<convert>" /><!--Cliquez ici-->
      	</td></tr><tr><td width="170" class="label">
					<label for="n_caver_check">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=194<convert><!--Texte contenu dans la grotte-->
		      </label>
		    </td><td class="field">
          <input class="input1" type="text" id="n_caver_check" name="n_caver_check" value="" size="6" maxlength="6" />
      	</td></tr><tr><td width="170" class="label">
					<label for="n_caver_news">
		      	<convert>#label=561<convert><!--Je souhaite être tenu informé des nouveautés par e-mail-->
          </label>
		    </td><td class="field">
          <input class="input1" style="border:0px none;" type="checkbox" id="n_caver_news" name="n_caver_news" <?php if($alert_for_news=="YES" || $alert_for_news=="on"){echo "checked=\"checked\"";} ?> />
      	</td></tr><tr><td width="170" class="label">
					<label for="n_caver_lnp_no_label">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=211<convert><!--J'ai lu et j'accepte--> <a href="JavaScript:openLegalNPrivacy('<?php echo $_SESSION['language']; ?>');"><convert>#label=212<convert><!--les règles.--></a>
		      </label>
		    </td><td class="field">
          <input class="input1" type="checkbox" id="n_caver_lnp" name="n_caver_lnp" style="border: none;" />
      	</td></tr><tr><td colspan="2">
          <div class="warning"><?php echo getTopBubble(); ?><convert>#label=738<convert><!--Avant d'enregistrer, veuillez vous localiser sur la carte ci contre.--><?php echo getBotBubble(); ?></div>
        </td></tr><tr><td width="170" class="label">
					<label for="save">
          </label>
		    </td><td class="field">
          <input type="hidden" id="n_caver_latitude" name="n_caver_latitude" value="<?php echo $latitude; ?>" />
      		<input type="hidden" id="n_caver_longitude" name="n_caver_longitude" value="<?php echo $longitude; ?>" />
      		<input type="hidden" id="n_caver_default_lat" name="n_caver_default_lat" value="<?php echo $default_lat; ?>" />
      		<input type="hidden" id="n_caver_default_lng" name="n_caver_default_lng" value="<?php echo $default_lng; ?>" />
      		<input type="hidden" id="n_caver_default_zoom" name="n_caver_default_zoom" value="<?php echo $default_zoom; ?>" />
      		<input type="hidden" id="n_key" name="n_key" value="<?php echo $tmp_key; ?>" />
      		<input type="hidden" id="n_reload" name="n_reload" value="False" />
      		<input type="hidden" id="n_i_was_shown" name="n_i_was_shown" value="<?php if ($was_shown) {echo 'True';} else {echo 'False';} ?>" />
      	  <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
      	</td></tr><tr><td width="170" class="label">
					<label for="cancel">
          </label>
		    </td><td class="field">
          <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <img src="../images/icons/FlagEnterOnce.gif" alt="-" /><convert>#label=735<convert><!--Entré une seule fois, non modifiable par la suite.--><br />
            <img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=736<convert><!--Doit être unique.--><br />
            <sup>1</sup> <convert>#label=192<convert><!--Votre identifiant--> <convert>#label=394<convert><!--doit être unique de 3 à 20 caractères sauf--> <convert>#label=577<convert><!--les caracteres speciaux et les accentuations.--> <br />
            <sup>2</sup> <convert>#label=214<convert><!--Votre e-mail doit être un e-mail valide.--> <br />
            <sup>3</sup> <convert>#label=215<convert><!--Votre mot de passe doit être composé d'au moins 8 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <b>¨</b> <convert>#label=216<convert><!--et les espaces.--><br />
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
				} else {
?>
      newBeforeLoad(false);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:newOnLoad(false);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
  	<div class="frame_title"><?php echo setTitle("#", "connect_filter", "<convert>#label=220<convert>", 2+$back_delta); ?></div><!--Validation-->
  	<div class="warning"><?php echo getTopBubble(); ?><convert>#label=217<convert><!--Vous avez été inscrit avec succès dans--> <?php echo $_SESSION['Application_name'];?>, <convert>#label=218<convert><!--un mail d'activation de votre compte va vous parvenir dans les prochaines minutes.--><?php echo getBotBubble(); ?></div>
<?php
				}
			break;
    	case "forgotten":
        if (!isset($_POST['send_pwd']) || !$_SESSION['user_pwd_sent']){
?>
      forgotBeforeLoad(false);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:forgotOnLoad(false);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("connection_".$_SESSION['language'].".php?type=login","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("connection_".$_SESSION['language'].".php?type=forgotten", "connect_filter", "<convert>#label=221<convert>", 3+$back_delta); ?></div><!--Mot de passe-->
<?php
          $password_recovery_error = (isset($_SESSION['user_pwd_sent'])) ? !$_SESSION['user_pwd_sent'] : false;
          if (!$activated) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=823<convert><!--Votre compte est innactif et doit être activé avant sa première utilisation.<br />Veuillez activer votre compte ou contactez votre administrateur.--><?php echo getBotBubble(); ?></div>
<?php
          } elseif ($password_recovery_error) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez ressaisir correctement vos données !--><?php echo getBotBubble(); ?></div>
<?php
          }
?>
<iframe jsname="L5Fo6c" class="YMEQtf DnR2hf" sandbox="allow-scripts allow-popups allow-forms allow-same-origin allow-popups-to-escape-sandbox allow-downloads" frameborder="0" aria-label="Google Forms, Contact Wikicaves EN" src="https://docs.google.com/forms/d/e/1FAIpQLSfqRdI9a0uFmgE06kOvZWJe8Ly6T_CUcrqJwE6xT-6Zy0EdxA/viewform?embedded=true" style="height: 99%;min-width: 400px;" allowfullscreen=""></iframe>
  	<form id="forgot_pwd" name="forgot_pwd" method="post" action="" onsubmit="JavaScript:forgotSubmit(event);" style="display:none;">
  	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
					<label for="f_caver_login">
					    <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=192<convert><!--Identifiant-->
					</label>
		    </td><td class="field">
          <input class="input1" type="text" id="f_caver_login" name="f_caver_login" value="<?php echo $login; ?>" size="20" maxlength="20" />
		    </td></tr><tr><td width="170" class="label">
					<label for="f_caver_contact">
						  <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=197<convert><!--E-mail de contact-->
					</label>
		    </td><td class="field">
          <input class="input1" type="text" id="f_caver_contact" name="f_caver_contact" value="<?php echo $contact; ?>" size="20" maxlength="40" />
		    </td></tr><tr><td width="170" class="label">
<?php
          $_SESSION['do_check'] = ($_SESSION['send_retry'] > 3);
          if ($_SESSION['do_check']) {
            $_SESSION['userCheck'] = createImage(6, 16);
?>
          <label for="check">
          </label>
		    </td><td class="field">
          <img class="image1" name="check" id="check" src="<?php echo $_SESSION['userCheck']; ?>.gif" alt="image" />
		    </td></tr><tr><td width="170" class="label">
					<label for="reload_btn">
		        <convert>#label=210<convert><!--Je ne peux pas lire le texte-->
		      </label>
		    </td><td class="field">
          <input class="button1" onclick="JavaScript:reloadCaptcha(6, 16, this, 'check');" type="button" id="reload_btn" name="reload_btn" value="<convert>#label=108<convert>" /><!--Cliquez ici-->
      	</td></tr><tr><td width="170" class="label">
          <label for="f_caver_check">
          	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=194<convert><!--Texte contenu dans la grotte-->
          </label>
		    </td><td class="field">
          <input class="input1" type="text" id="f_caver_check" name="f_caver_check" value="" size="6" maxlength="6" />
		    </td></tr><tr><td width="170" class="label">
<?php
          }
?>
          <label for="send_pwd">
          </label>
		    </td><td class="field">
          <input class="button1" type="submit" id="send_pwd" name="send_pwd" value="<convert>#label=239<convert>" /><!--Envoyer-->
		    </td></tr><tr><td width="170" class="label">
          <label for="cancel">
          </label>
		    </td><td class="field">
          <input class="button1" onclick="JavaScript:forgotCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr>
      </table>
    </form>
<?php
        } else {
?>
      forgotBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:forgotOnLoad(true);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
    <div class="frame_title"><?php echo setTitle("#", "connect_filter", "<convert>#label=222<convert>", 2+$back_delta); ?></div><!--Validation-->
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=223<convert><!--Un nouveau mot de passe vous a été envoyé par e-mail, n'oubliez pas de le changer une fois connecté.--><?php echo getBotBubble(); ?></div>
    <input  class="button1" type="button" id="next" name="next" value="<convert>#label=4<convert>" onclick="JavaScript:connectUser('<?php echo $_SESSION['language']; ?>');" /><!--Se connecter-->
<?php        
        }
			break;
    	default:
?>
      defaultBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:defaultOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
  	<div class="frame_title"><?php echo setTitle("connection_".$_SESSION['language'].".php?type=", "connect_filter", "<convert>#label=80<convert>", 2+$back_delta); ?></div><!--Erreur-->
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div>
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "connection/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
