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
$frame = "filter";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
  	$sql = "SELECT NOW() AS Now";
  	$date_now = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  	$date_now = $date_now[0]['Now'];
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=370<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need those functions : switchDOM();
    var isDisabled = false;
    <?php include("../scripts/events.js"); ?>
      
    function selectOnClick(oSelect) {
      if (!oSelect.disabled) {
        if (oSelect.selectedIndex != -1) {
          var Id = oSelect.options[oSelect.selectedIndex].objectId
          var Category = oSelect.options[oSelect.selectedIndex].category
          document.body.focus();
          openMe(Id, Category, false);
        }
      }
    }
    
    function load() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var myOverview = mySite.overview;
      if (myOverview) { 
        if (myOverview.setNearMeList) {
          myOverview.setNearMeList();
        }
        if (myOverview.setVisibilityFilter) {
          myOverview.setVisibilityFilter();
        }
      }
      parent.setFilterSize(200,"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml)
    }
    
    function visibilityOnClick(sCategory, bValue) {
      mySite.overview.cancelAbortLoading();
      mySite.overview.setCategoryVisibility(sCategory, bValue);
      if (bValue != true && sCategory != "advanced") {
        if (sCategory == "link") {
          mySite.overview.removeLines(mySite.overview.existingLines, true);
          mySite.overview.existingLines = new mySite.overview.Array();
        } else {
          mySite.overview.removeMarkers(mySite.overview.existingMarkers[sCategory], sCategory, true);
          mySite.overview.existingMarkers[sCategory] = new mySite.overview.Array();
        }
      } else {
        mySite.overview.loadFromXML();
      }
    }
    
    function limitOnChange(oSelect) {
      mySite.overview.setClusteringLimit(oSelect.options[oSelect.selectedIndex].value);
    }
    /*function limitOnChange(oInput) {
      var limit = parseInt(oInput.value, 10);
      if (!(limit > 0)) {
        limit = 30;
      }
      mySite.overview.setClusteringLimit(limit);
    }
    
    function strOnKeyPress(evenement, oInput) {
      var key_pressed = window.event ? evenement.keyCode : evenement.which;
      if (key_pressed === 13) {
        limitOnChange(oInput);
      }
    }*/
    
    function openFilterPopUp() {
      var windowName, url;
      windowName = "<convert>#label=644<convert>";//Définissez les critères d'affichage
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=filter&category=entry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=filter&category=entry";
      openWindow(url, windowName, 990, 520);
    }
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:load();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div class="menu">
      <?php echo getTopMenu('<div class="frame_title" style="margin-right:0px;">'.setTitle("filter_".$_SESSION['language'].".php", "filter", "<convert>#label=373<convert>", 1).'</div><!--Actions-->'); ?>
      <ul>
<?php
if ($_SESSION['home_page'] == "home") {
?>
        <li class="sub_menu li_overview">
          <a href="../index.php?home_page=overview" target="_top"><!--<img src="../images/icons/overview.png" alt="<convert>#label=608<convert>" title="<convert>#label=608<convert>" style="border:0px none;" /> --><convert>#label=608<convert><!--Carte--></a>
        </li>
<?php
} else {
?>
        <li class="sub_menu li_home">
          <a href="../index.php?home_page=home" target="_top"><!--<img src="../images/icons/home.png" alt="<convert>#label=600<convert>" title="<convert>#label=600<convert>" style="border:0px none;" /> --><convert>#label=600<convert><!--Accueil--></a>
        </li>
<?php
}
if (USER_IS_CONNECTED){
?>
        <li class ="sub_menu li_logout">
          <a href = "JavaScript:logMeOut();" style="color:#FF0000;"><convert>#label=374<convert><!--Déconnexion--></a>
        </li>
        <li class ="sub_menu li_parameters">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("parameters_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <span style="color:#008000;"><convert>#label=375<convert><!--Mes paramètres--></span></a>
        </li>
<?php
}
if (allowAccess(entry_view_all)) {
?>
        <li class ="sub_menu li_entry">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="entry_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("entry_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <convert>#label=376<convert><!--Menu des entrées--></a>
        </li>
<?php
}
if (allowAccess(cave_view_all)) {
?>
        <li class ="sub_menu li_cave">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="cave_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("cave_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <convert>#label=377<convert><!--Menu des cavités--></a>
        </li>
<?php
}
if (allowAccess(massif_view_all)) {
?>
        <li class ="sub_menu li_massif">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="massif_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("massif_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <convert>#label=547<convert><!--Menu des massifs--></a>
        </li>
<?php
}
if (allowAccess(grotto_view_all)) {
?>
        <li class ="sub_menu li_grotto">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="grotto_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("grotto_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <convert>#label=378<convert><!--Menu des clubs--></a>
        </li>
<?php
}
if (allowAccess(request_view_mine)) {
?>
        <li class ="sub_menu li_topography">
          <a href="JavaScript:openWindow('request_<?php echo $_SESSION['language']; ?>.php?type=menu', '<convert>#label=799<convert>', 1150, 600);">
          <convert>#label=799<convert><!--Menu des topographies--></a>
        </li>
<?php
}
if (allowAccess(url_view_all)) {
?>
        <li class ="sub_menu li_url">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="url_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("url_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <convert>#label=673<convert><!--Menu des sites partenaires--></a>
        </li>
<?php
}
//For the following version: layer management.
if (allowAccess(layer_view_all) && false) {
?>
        <li class ="sub_menu li_layer">
<?php if ($_SESSION['home_page'] == "overview"){ ?>
          <a href="layer_<?php echo $_SESSION['language']; ?>.php?type=menu">
<?php } else { ?>
          <a href="../index.php?home_page=overview&amp;filter_page=<?php echo urlencode("layer_".$_SESSION['language'].".php?type=menu"); ?>" target="_top">
<?php } ?>
          <convert>#label=<convert><!--Claques--></a>
        </li>
<?php
}
if (allowAccess(entry_export_all)) {
?>
        <li class ="sub_menu li_download">
          <a href="export_<?php echo $_SESSION['language']; ?>.php"><convert>#label=765<convert><!--Télécharger un export pour GPS--></a>
        </li>
<?php
}
?>
        <li class ="sub_menu li_blog">
        <?php 
        $blogURL = "http://blog-" . strtolower($_SESSION['language']) . ".grottocenter.org";
        if (!in_array(array("fr", "en"), strtolower($_SESSION['language']))) {
            $blogURL = "http://blog-en.grottocenter.org";
        }?>
          <a href="<?php echo $blogURL; ?>" target="_blank"><convert>#label=928<convert><!--Blog de--></a><span class="new_feature"><convert>#label=537<convert><!--Nouveau !--></span>
        </li>
        <li class ="sub_menu li_facebook">
          <a href="https://www.facebook.com/GrottoCenter" target="_blank"><convert>#label=929<convert><!--Facebook--></a><span class="new_feature"><convert>#label=537<convert><!--Nouveau !--></span>
        </li>
        <li class ="sub_menu li_forum">
          <a href="../phpBB3/" target="_blank"><convert>#label=583<convert><!--Forum de--> <?php echo $_SESSION['Application_name']; ?></a>
        </li>
<?php
if (USER_IS_CONNECTED){
  if (allowAccess(chat_all)) {
?>
        <li class ="sub_menu li_chat">
    	    <a href="JavaScript:openWindow('grottoChat_<?php echo $_SESSION['language']; ?>.php', '<convert>#label=727<convert>', '620px', '340px');" target="filter"><convert>#label=728<convert><!--Discutez sur le grottoChat !--></a>
    	  </li>
<?php
  }
} else {
?>
		    <li class ="sub_menu li_login">
          <a href="connection_<?php echo $_SESSION['language']; ?>.php?type=login" target="filter"><convert>#label=4<convert><!--Connexion--></a>
        </li>
        <li class ="sub_menu li_register">
          <a href="connection_<?php echo $_SESSION['language']; ?>.php?type=new" target="filter"><convert>#label=379<convert><!--Inscription--></a>
        </li>
<?php
}
if (allowAccess(appli_view_all)) {
?>
        <li class ="sub_menu li_admin">
          <a href="administration_<?php echo $_SESSION['language']; ?>.php?type=menu" target="filter" style="color:red;"><convert>#label=380<convert><!--Panneau d'administration--></a>
        </li>
<?php
}
?>
      </ul>
<?php
//####################### Deprecated ###########################################
if ($_SESSION['home_page'] == "home" && USER_IS_CONNECTED && false) { 
?>
      <div class="info">
        <?php echo getTopBubble(); ?>
        <convert>#label=631<convert> <convert>#label=632<convert><!--Pour accèder aux menus de modification et de création des entrées, réseaux, massifs…-->
        <?php echo getBotBubble(); ?>
      </div>
<?php
}
//##############################################################################
?>
      <?php echo getBotMenu(); ?>
  	</div>
<?php
if ($_SESSION['home_page'] == "overview") {
?>
<?php if (false) { ?>
  	<div class="menu">
      <?php echo getTopMenu('<div class="frame_title" style="margin-right:0px;">'.setTitle("#", "filter_2", "<convert>#label=488<convert>", 1).'</div><!--Contenu de la vue courrante-->'); ?>
      <form id="nearMeFrom" name="nearMeFrom" method="post" action="">
        <select style="width:100%;" name="markersNearMeSelect" id="markersNearMeSelect" size="5" onclick="JavaScript:selectOnClick(this);">
          <option></option>
        </select>
      </form>
      <?php echo getBotMenu(); ?>
  	</div>
<?php } ?>
  	<div class="menu">
      <?php echo getTopMenu('<div class="frame_title" style="margin-right:0px;">'.setTitle("#", "filter_3", "<convert>#label=381<convert>", 1).'</div><!--Filtre d\'affichage-->'); ?>
      <form id="visibilityFrom" name="visibilityFrom" method="post" action="" onsubmit="JavaScript:return false;">
        <input class="input1" style="border:0px none;" type="checkbox" id="entry" name="entry" onclick="visibilityOnClick(this.id, this.checked);" />
        <label for="entry">
          <img src="../images/icons/entry2.png" alt="<convert>#label=384<convert>" style="height:9pt;margin:0px 3px;" /> <b>(<?php echo countByCategory("entry"); ?>) <convert>#label=384<convert><!--Entrées--></b>
        </label><br />
        <input class="input1" style="border:0px none;" type="checkbox" id="grotto" name="grotto" onclick="visibilityOnClick(this.id, this.checked);" />
        <label for="grotto">
          <img src="../images/icons/grotto1.png" alt="<convert>#label=386<convert>" style="height:9pt;" /> <b>(<?php echo countByCategory("grotto"); ?>) <convert>#label=386<convert><!--Clubs--></b>
        </label><br />
        <input class="input1" style="border:0px none;" type="checkbox" id="caver" name="caver" onclick="visibilityOnClick(this.id, this.checked);" />
        <label for="caver">
          <img src="../images/icons/caver2.png" alt="<convert>#label=385<convert>" style="height:9pt;" /> <b>(<?php echo countByCategory("caver"); ?>) <convert>#label=385<convert><!--Spéléos--></b>
        </label><br />
        <input class="input1" style="border:0px none;" type="checkbox" id="link" name="link" onclick="visibilityOnClick(this.id, this.checked);" />
        <label for="link">
          <img src="../images/icons/linkIcon.png" alt="<convert>#label=175<convert>" style="height:9pt;" /> <b><convert>#label=175<convert><!--Liens--></b>
        </label><br />
        <input class="input1" style="border:0px none;" type="checkbox" id="advanced" name="advanced" onclick="visibilityOnClick(this.id, this.checked);" />
        <b><a href="JavaScript:openFilterPopUp();" title="<convert>#label=629<convert>"><convert>#label=629<convert><!--Filtre avancé...--></a></b><br />
        <label for="limit">
          <b><convert>#label=649<convert><!--Niveau de détail--> : </b>
        </label><br />
        <!--<input class="input1" type="text" id="limit" name="limit" value="" size="4" maxlength="4" onkeypress="JavaScript:strOnKeyPress(event, this);" />
        <input type="button" class="button1" name="limit_go" id="limit_go" value="<convert>#label=171<convert>" onclick="JavaScript:limitOnChange(this.form.limit);" />-->
        <select name="limit" id="limit" size="1" onchange="JavaScript:limitOnChange(this);">
<?php
$delta = 5;
for ($i=5; $i<=Max_detail_level; $i = $i + $delta) {
  echo "<option value=\"".$i."\" >".$i."</option>";
  if ($i == 20) {
    $delta = 10;
  }
  if ($i == 100) {
    $delta = 50;
  }
  if ($i == 300) {
    $delta = 100; 
  }
}
?>
        </select>
        (<convert>#label=650<convert><!--picto./catégorie-->).<br /><br />
        <div class="info"><?php echo getTopBubble(); ?><convert>#label=628<convert><!--Vous pouvez choisir à tout moment les critères sur lesquels sont affichés les données sur la carte.<br />Plus vos critères seront précis, plus votre navigation sera rapide et agréable !--><?php echo getBotBubble(); ?></div>
      </form>
      <?php echo getBotMenu(); ?>
    </div>
<?php
}
if ($_SESSION['home_page'] == "overview" && false) {
?>
    <div class ="nearMe">
      <div class="frame_title" style="margin-right:0px;"><?php echo setTitle("#", "filter_3", "<convert>#label=381<convert>", 1); ?></div><!--Filtre d'affichage-->
      <form id="filter_tree" name="filter_tree" method="post" action="">
      	<div>
        	<a href="JavaScript:signOnClick('div|entry');" class="sign" style="margin-left:10px;">
          	<span id="span|entry" title="<convert>#label=372<convert>" class="sign_plus"><convert>#label=382<convert></span><!--Déployer--><!--+-->
        	</a>
        	<label for="entry">
          	<input type="checkbox" title="<convert>#label=383<convert>" id="entry" value="" onclick="JavaScript:checkBoxOnClick(this,true,false);" style="border: none;" checked="checked" /><!--Montrer/Cacher-->
          	<span class="filter_head"> (<?php echo countByCategory("entry"); ?>) <convert>#label=384<convert><!--Entrées--></span>
        	</label>
        	<div id="div|entry" class="filter_div">
        	</div>
      	</div>
      	<div>
        	<a href="JavaScript:signOnClick('div|caver');" class="sign" style="margin-left:10px;">
          	<span id="span|caver" title="<convert>#label=372<convert>" class="sign_plus"><convert>#label=382<convert></span><!--+-->
        	</a>
        	<label for="caver">
          	<input type="checkbox" title="<convert>#label=383<convert>" id="caver" value="" onclick="JavaScript:checkBoxOnClick(this,true,false);" style="border: none;" checked="checked" />
          	<span class="filter_head"> (<?php echo countByCategory("caver"); ?>) <convert>#label=385<convert><!--Spéléologues--></span>
        	</label>
        	<div id="div|caver" class="filter_div">
        	</div>
      	</div>
      	<div>
        	<a href="JavaScript:signOnClick('div|grotto');" class="sign" style="margin-left:10px;">
          	<span id="span|grotto" title="<convert>#label=372<convert>" class="sign_plus"><convert>#label=382<convert></span><!--+-->
        	</a>
        	<label for="grotto">
          	<input type="checkbox" title="<convert>#label=383<convert>" id="grotto" value="" onclick="JavaScript:checkBoxOnClick(this,true,false);" style="border: none;" checked="checked" />
          	<span class="filter_head"> (<?php echo countByCategory("grotto"); ?>) <convert>#label=386<convert><!--Clubs--></span>
        	</label>
        	<div id="div|grotto" class="filter_div">
        	</div>
      	</div>
      </form>
    </div>
<?php
  if (USER_IS_CONNECTED){
?>
    <div class ="nearMe">
      <div class="frame_title" style="margin-right:0px;"><?php echo setTitle("#", "filter_4", "<convert>#label=564<convert>", 1); ?></div><!--Options d'affichage-->
      <label for="new_markers" style="display:block;">
        <input type="checkbox" id="new_markers" name="new_markers" onclick="JavaScript:setFilterOptions(this);" /> <convert>#label=562<convert></span><!--Afficher uniquement les nouveautes-->
      </label>
      <label for="reviewed_markers" style="display:block;">
        <input type="checkbox" id="reviewed_markers" name="reviewed_markers" onclick="JavaScript:setFilterOptions(this);" /> <convert>#label=563<convert></span><!--Changements-->
      </label>
      <label for="connected_cavers" style="display:block;">
        <input type="checkbox" id="connected_cavers" name="connected_cavers" onclick="JavaScript:setFilterOptions(this);" /> <convert>#label=567<convert></span><!--Spéléologues connectés-->
      </label>
    </div>
<?php
  }
}
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "filter/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
