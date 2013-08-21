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
$labelsSinceDate = array("<convert>#label=8<convert>","<convert>#label=9<convert>","<convert>#label=10<convert>","<convert>#label=11<convert>","<convert>#label=12<convert>","<convert>#label=13<convert>","<convert>#label=14<convert>");
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
  	
    if ($_SESSION['home_page'] == "overview") {
      $to_page = "home";
      $to_title = "<convert>#label=600<convert>";//Accueil
    } else {
      $to_page = "overview";
      $to_title = "<convert>#label=608<convert>";//Carte
    }
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=32<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/banner.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    function titleOnClick() {
      var msg = "<convert>#label=15<convert> : <?php echo $_SESSION['Application_name']; ?>\n";
      msg = msg + "<convert>#label=16<convert> : <?php echo $_SESSION['Application_version']; ?>\n";
      msg = msg + "<convert>#label=17<convert> : <?php echo $_SESSION['Application_creation']; ?>\n";
      msg = msg + "<convert>#label=18<convert> : <?php echo $_SESSION['Application_revision']; ?>\n";
      msg = msg + "<convert>#label=19<convert> : <?php echo $_SESSION['Application_url']; ?>\n";
      msg = msg + "<convert>#label=20<convert> : <?php echo $_SESSION['Application_authors']; ?>\n";
      msg = msg + "<convert>#label=21<convert> : <?php echo $_SESSION['Application_authors_contact']; ?>\n";
      msg = msg + "<convert>#label=510<convert> : <?php echo $_SESSION['Application_thanks']; ?>";
      alert(msg);
    }
    
    function load() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
    
    function userIsOk(oDocument) {
      var rightSource = toAbsURL(rightPic);
      var sMessage = "";
      var oField = oDocument.getElementById('login_pic');
      if (oField) {
        var sMessage = "<convert>#label=192<convert> <convert>#label=394<convert> <convert>#label=577<convert>"; //Votre identifiant //doit être unique, de 3 à 20 caractères sauf...
        createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      }
      
      var oField = oDocument.getElementById('nick_pic');
      if (oField) {
        var sMessage = "<convert>#label=579<convert> <convert>#label=580<convert> | / \\ \" # & + <convert>#label=46<convert> ¨";//Votre pseudonyme //doit être composé d'au moins 3 caractères sauf //et
        createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      }

      oField = oDocument.getElementById('mail_pic');
      if (oField) {
        sMessage = "<convert>#label=214<convert> <convert>#label=395<convert>"; //Votre e-mail doit être un e-mail valide //Il sera utilisé pour vous envoyer un courrier de confirmation.
        createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      }
    
      oField = oDocument.getElementById('pwd_pic');
      if (oField) {
        sMessage = "<convert>#label=215<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Votre mot de passe doit être composé d'au moins 8 caractères sauf //et
        createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      }
    
      oField = oDocument.getElementById('pwd2_pic');
      if (oField) {
        sMessage = "<convert>#label=396<convert>";//Vous vous êtes trompé en reproduisant votre mot de passe.
        createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      }
    
      oField = oDocument.getElementById('n_caver_lnp');
      if (oField) {
        sMessage = "<convert>#label=397<convert>";//L'acceptation des règles est nécessaire pour valider l'inscription.
        createTest(oField.name, oField.checked, "", "isTrue", sMessage, true);
      }
      
      return testForm();
    }
      
<?php
if ($_SESSION['home_page'] == "overview") {
?>
    function convertOnClick() {
      mySite.details.switchConverter(true,"coords_converter");
    }
    
    function convertLength() {
      mySite.details.switchConverter(true,"length_converter");
      var oForm = mySite.details.document.length_converter_form;
      oForm.meter.value = document.measurement.measure.value;
      mySite.details.convertMeter("meter_div",oForm);
    }
      
<?php
  include("../scripts/events.js");
}
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body style="font-size:8pt;" onload="JavaScript:load();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<table class="table1" cellspacing="0" cellpadding="0">
  		<tr>
  			<td class="bannerCell1">
          <select class="select1" name="banner_language" id="banner_language" onchange="JavaScript:changeLanguage(this);">
<?php
            echo getOptionLanguage($_SESSION['language']);
?>
          </select> <img src="../images/icons/<?php echo $to_page; ?>.png" alt="<?php echo $to_title; ?>" title="<?php echo $to_title; ?>" style="cursor:pointer;vertical-align:top;" onclick="JavaScript:window.top.location='../index.php?home_page=<?php echo $to_page; ?>';" />
<?php
          if (USER_IS_CONNECTED) {
?>
          <br /><!--Dernière connexion :-->
          <convert>#label=24<convert> : <?php
            echo " ".$_SESSION['user_last_connection']." (".getSinceDateFromDT($_SESSION['user_last_connection'],$labelsSinceDate).")";
          }
?>
        </td>
        <td class="bannerCell2" rowspan="3">
          <img onclick="JavaScript:titleOnClick();" src="../favicon.png" alt="" style="width:60px;height:60px;cursor:help;" />
        </td>
        <td class="bannerCell2" rowspan="3">
  				<div onclick="JavaScript:titleOnClick();" class="h1">
            <?php echo $_SESSION['Application_name']." ".substr($_SESSION['Application_version'],0,strrpos($_SESSION['Application_version'],'.')); //."2.1" ?>
            <div><convert>#label=729<convert></div>
          </div><!--La base de données Wiki faite par les spéléos, pour les spéléos.-->
  			</td>
  			<td class="bannerCell3">
<?php
          if (USER_IS_CONNECTED) {
            echo "<convert>#label=25<convert> ".$_SESSION['user_nickname']." (<convert>#label=192<convert>: ".$_SESSION['user_login'].") | "; //Bonjour //Identifiant de connexion
          }
//CRO 2011-10-12
?>
          <!--<?php //echo sessionCount();?> <convert>#label=5<convert>--><!-- utilisateur(s) en ligne--><!-- | <?php //echo countByCategory("caver"); ?> <convert>#label=26<convert>--><!-- inscrits --> <!--| <img src="http://perso0.free.fr/cgi-bin/wwwcount.cgi?df=<?php echo $_SESSION['Application_host']; ?>.dat&amp;dd=C&amp;display=counter&amp;ft=0&amp;negate=T" style="height:12px;vertical-align:middle;" alt="Compteur" /> <convert>#label=27<convert>visites -->
    		</td>
  		</tr>
  		<tr>
        <td class="bannerCell4">
          
        </td>
        <td class="bannerCell5">
          
        </td>
      </tr>
      <tr>
        <td class="bannerCell4">
          <?php echo getLicense(4);//getLicensePicture(3); ?> <a href="JavaScript:openLegalNPrivacy('<?php echo $_SESSION['language']; ?>');"><convert>#label=3<convert><!--Mentions légales &amp; Charte de confidentialité--></a>
  			</td>
  			<td class="bannerCell5">
      			<div class="banner_fse">
      			   <a href="http://eurospeleo.org/" target="_blank"><convert>#label=931<convert><!-- Grottocenter bénéficie du soutien de la FSE --> <img src="../images/icons/fse-logo.png" alt="Logo FSE" /></a>
      			</div>
<?php
if ($_SESSION['home_page'] == "overview") {
?>
              <div class="banner_info">
                <form action="#" method="post" name="measurement" id="measurement">
                  <input type="button" class="button1" value="<convert>#label=28<convert>" id="measure_btn" name="measure_btn" style="font-size:8pt;" onclick="JavaScript:startMeasurement('<convert>#label=29<convert>','<convert>#label=28<convert>','<convert>#label=30<convert>');" /> <label for="measure" id="measure_label" style="visibility:hidden;display:none;">l=<input type="text" size="10" value="<convert>#label=29<convert>" id="measure" name="measure" style="font-size:8pt;color:#808080;" /> <a href="JavaScript:convertLength();" title="<convert>#label=31<convert>"><convert>#label=268<convert><!--m--></a>, </label>
                  <input type="button" class="button1" value="<convert>#label=31<convert>" id="convert_btn" name="convert_btn" style="font-size:8pt;" onclick="JavaScript:convertOnClick();" />
                  <br />
                  <label for="mouseLat">Lat=<input type="text" size="4" class="input1" value="" id="mouseLat" name="mouseLat" readonly="readonly" style="font-size:8pt;" /> <convert>#label=290<convert><!--°--><convert>#label=293<convert><!--N-->, </label>
                  <label for="mouseLng">Lng=<input type="text" size="4" class="input1" value="" id="mouseLng" name="mouseLng" readonly="readonly" style="font-size:8pt;" /> <convert>#label=290<convert><!--°--><convert>#label=294<convert><!--E--> </label>
                </form>
              </div>
<?php
}
?>
  			</td>
    	</tr>
    </table>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "banner/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>