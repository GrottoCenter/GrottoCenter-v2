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
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" charset="UTF-8" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
		//store the value
		$show_coords_conv = $_SESSION['show_converter'];
		//reset
		$_SESSION['show_converter'] = false;
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=243<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/details.css" />
    <link rel="stylesheet" type="text/css" href="../css/details_p.css" media="print" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
 if ($_SESSION['home_page'] == "overview") {
    $part = "js";
    $converter = "";
    $geodesic = "";
    include("converter_".$_SESSION['language'].".php");
} ?>
    <script type="text/javascript" charset="UTF-8">
<?php echo getCDataTag(true); ?>
//functions may be needed :
//resetDetails, strToFloat, switchDOM, getTargetNode, openWindow, getResponseText,
//showId, hideId, setSelectedIndex, orderSelect
    function load() {
<?php  if ($_SESSION['home_page'] == "overview") { ?>
			loadConverter('<?php echo Select_default; ?>');
<?php } ?>
    }
    function signUpNow() {
      mySite.filter.location.href = "connection_<?php echo $_SESSION['language']; ?>.php?type=new";
    }
    
    function arrowOnClick() {
      var oDiv = xtdGetElementById('arrow');
      if (oDiv.className == "arrow_right") {
        switchDetails(true);
      } else {
        switchDetails(false);
      }
    }
    
    function switchDetails(doClose) {
      var oDiv = xtdGetElementById('arrow');
      var oBody = xtdGetElementById('body');
      if (doClose && oDiv.className == "arrow_right") {
        oDiv.className = "arrow_left";
        oBody.className = "overflowHidden";
        mySite.setDetailsSize(10, "px");
      } else if (!doClose && oDiv.className != "arrow_right") {
        oDiv.className = "arrow_right";
        oBody.className = "";
        mySite.detailsSizeRestore();
      }
    }
    
<?php
if ($_SESSION['home_page'] == "overview") {
?>
      
    function goOnClick(oForm) {
      goSearch(oForm);
    }
    
    function goSearch(oForm) {
      var oSelect = oForm.result;
      var value = oForm.search_str.value;
      var forCavers = oForm.search_cavers.checked;
      var forEntries = oForm.search_entries.checked;
      var forGrottoes = oForm.search_grottoes.checked;
      var forCaves = oForm.search_caves.checked;
      if (value.length >= 3) {
        waitSelect(oSelect);
        //var markersArray = mySite.overview.getMarkersByName2(value,forCavers,forEntries,forGrottoes);
        var separator = "_"
        var catArray = "";
        if (forCavers) {
          catArray = catArray + "caver" + separator;
        }
        if (forEntries) {
          catArray = catArray + "entry" + separator;
        }
        if (forGrottoes) {
          catArray = catArray + "grotto" + separator;
        }
        if (forCaves) {
          catArray = catArray + "cave" + separator;
        }
        var sURL = "webservices/getSearchResultPlain_<?php echo $_SESSION['language']; ?>.php?txt=" + value + "&cat=" + catArray + "&s=" + separator;
        var list = eval(getResponseText(sURL));
        if (list.length > 0) {
          emptySelect(oSelect);
          fillSelect(list, oSelect);
          sortSelect(oSelect);
          oSelect.disabled = false;
          oSelect.style.color = "black";
        } else {
          resetSelect(oSelect, false);
        }
      } else {
        resetSelect(oSelect, true);
      }
    }
    
    function resetSelect(oSelect, bResults) {
      emptySelect(oSelect);
      if (bResults) {
        oSelect.options[0] = new Option("<convert>#label=244<convert>");//Entrez au minimum 3 caractères.
        oSelect.style.color = "#808080";
      } else {
        oSelect.options[0] = new Option("<convert>#label=245<convert>");//Aucun résultat correspondant.
        oSelect.style.color = "#808080";
      }
      oSelect.disabled = true;
    }
    
    function waitSelect(oSelect) {
      emptySelect(oSelect);
      oSelect.options[0] = new Option("<convert>#label=721<convert>");//Recherche en cours...
      oSelect.style.color = "#808080";
      oSelect.disabled = true;
    }
    
    function selectOnClick(e, oSelect) {
      var marker = getSelectedArray(oSelect);
      document.body.focus();
      if (marker) {
        if (marker.category == 'cave') {
          detailMarker(e, marker.category, marker.id, '<?php echo $_SESSION['language']; ?>');
        } else {
          openMe(marker.id, marker.category, false);
        }
      }
    }
    
    function getSelectedArray(oSelect) {
      if (!oSelect.disabled) {
        if (oSelect.selectedIndex != -1) {
          var Id = oSelect.options[oSelect.selectedIndex].objectId;
          if (Id == undefined) {
            Id = oSelect.options[oSelect.selectedIndex].attributes['objectId'].nodeValue;
          }
          var Category = oSelect.options[oSelect.selectedIndex].category;
          if (Category == undefined) {
            Category = oSelect.options[oSelect.selectedIndex].attributes['category'].nodeValue;
          }
          return {"id": Id,"category": Category};
        }
      }
      return false;
    }
    
    function resetListDiv(divId) {
      var oNode = xtdGetElementById(divId);
      var oContainer = oNode.parentNode;
      oContainer.removeChild(oNode);
    }
    
    function closeDirections() {
      mySite.overview.clearDirections();
      hideId('directions_div');
    }
    
    function openDirections(address, bFromThisPlace, category, id, locale) {
        var markerAddress;
        if (address != "") {
            var marker = mySite.overview.getMarker(id, category);
            if (marker.country == "<?php echo select_default; ?>" || marker.address == "" || marker.city == "") {
                markerAddress = marker.getPosition().lat() + "," + marker.getPosition().lng();
            } else {
                if (marker.region == "") {
                    markerAddress = marker.address + "," + marker.city + "," + marker.country;
                } else {
                    markerAddress = marker.address + "," + marker.city + "," + marker.region + "," + marker.country;
                }
            }
            var from = address;
            var to = address;
            if (bFromThisPlace) {
                from = markerAddress;
            } else {
                to = markerAddress;
            }
            mySite.overview.setDirections(from, to, locale);
        }
    }
    
    function advancedSearch() {
    }

    function switchConverter(bOpen,divId) {
      if (bOpen) {
        mySite.details.showId("converter_menu");
        mySite.details.showId(divId);
      } else {
        if (divId == "converter_menu") {
          mySite.details.hideId("length_converter");
          mySite.details.hideId("temperature_converter");
          mySite.details.hideId("coords_converter");
        }
        mySite.details.hideId(divId);
        if (divId == "coords_converter" || divId == "converter_menu") {
          mySite.overview.resetConvertMarker();
        }
      }
    }
      
<?php
    include("../scripts/events.js");
}
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:load();" id="body">
    <?php echo getTopFrame(); ?>
    <div id="arrow" class="arrow_right" onclick="JavaScript:arrowOnClick();">
    </div>
    <div style="margin-left:11px;">
      <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php
if (!USER_IS_CONNECTED) {
?>
      <div id="sign_up" style="text-align:center;">
        <?php echo getCloseBtn("JavaScript:hideId('sign_up');","<convert>#label=371<convert>"); ?><!-- Fermer -->
        <input type="button" class="button1" style="background:transparent url(../images/icons/bigButtonBg.png) repeat scroll 0% 50%;height:60px;width:80%;position:relative;top:4px;font-size:11pt;margin-bottom:10px;" id="sign_up_btn" name="sign_up_btn" value="<convert>#label=260<convert>" onclick="JavaScript:signUpNow();" /><!--Inscrivez-vous&#13;&#10;dès maintenant&#13;&#10;GRATUITEMENT !-->
      </div>
<?php
}
?>
      
      <div id="list_div" style="width:100%"></div>
      
      <div id="details_div" style="width:100%"></div>
          
<?php
if ($_SESSION['home_page'] == "overview") {
	$display_conv = ($show_coords_conv) ? '' : 'display:none;visibility:hidden;';
?>
    	<div id="converter">
        <div id="converter_menu" class ="menu" style="<?php echo $display_conv; ?>">
          <?php echo getTopMenu(getCloseBtn("JavaScript:switchConverter(false,'converter_menu');","<convert>#label=371<convert>").'<div class="frame_title">'.setTitle("#", "details", "<convert>#label=31<convert>", 1).'</div><!--Convertisseur-->'); ?>
          <ul>
            <li class ="sub_menu">
        	    <a href="JavaScript:switchConverter(true,'length_converter');"><convert>#label=261<convert><!--Convertir des distances--></a>
        	  </li>
            <li class ="sub_menu">
              <a href="JavaScript:switchConverter(true,'temperature_converter');"><convert>#label=262<convert><!--Convertir des températures--></a>
            </li>
            <li class ="sub_menu"> 
              <a href="JavaScript:switchConverter(true,'coords_converter');"><convert>#label=263<convert><!--Convertir des coordonnées--></a>
            </li>
          </ul>
          <?php echo getBotMenu(); ?>
        </div>
            
<?php
if ($_SESSION['home_page'] == "overview") {
  $part = "html";
  $converter = "";
  $geodesic = "";
  include("converter_".$_SESSION['language'].".php");
}
?>
      </div>
      
      <div id="directions_div" style="display:none;visibility:hidden;">
        <?php echo getTopMenu(getCloseBtn("JavaScript:closeDirections();","<convert>#label=371<convert>").'<a href="JavaScript:window.print();" title="<convert>#label=525<convert>" class="nothing" style="display:block;float:right;text-align:right;margin-right:5px;"><img src="../images/icons/printer.png" alt="<convert>#label=525<convert>" style="border:0px none;" /></a>'.'<div class="frame_title" style="margin-right:46px;">'.setTitle("#", "details", "<convert>#label=166<convert>", 1).'</div><!--Itinéraire-->'); ?>
        <div id="directions"></div>
        <?php echo getBotMenu(); ?>
      </div>
          
<?php
}
if (false) {
$redirect_conv = ($_SESSION['home_page'] == "overview") ? '<a href="#" onclick="JavaScript:switchConverter(true,\'coords_converter\')">' : '<a href="../index.php?home_page=overview&amp;c" target="_top">';
?>
      <div id="info_converter" style="font-weight:bold;margin-bottom:10px">
        <?php echo getTopBox("ffffff", "FFA566", "FFD7BA"); ?>
        <?php echo getCloseBtn("JavaScript:hideId('info_converter');","<convert>#label=371<convert>"); ?><!-- Fermer -->
        <!--<h3>Nouveau convertisseur de coordonnées ! %s->ici</a></h3>
				<p>Le convertisseur de coordonnées a été réécrit pour permettre les conversions dans un plus large pannel de systèmes de références.</p>
				<p>144 systèmes sont mis à disposition pour l'instant, mais pour des raisons techniques une partie seulement a été testée.</p>
				<p>Nous vous invitons à <a href=\"contact_%s.php?type=message&amp;subject=bug_alert\" target=\"filter\">nous rapporter</a> les bugs que vous pourrez rencontrer.</p>
				<p>N'hésitez pas à <a href=\"contact_%s.php?type=message&amp;subject=other\" target=\"filter\">nous écrire</a> pour :</p>
				<ul>
					<li>communiquer vos remarques</li>
					<li>demander l'ajout de nouveaux systèmes</li>
				</ul>
				<p><i><small>L'équipe GrottoCenter.</small></i></p>-->
				<?php printf("<convert>#label=924<convert>", $redirect_conv, $_SESSION['language'], $_SESSION['language']); ?>
        <?php echo getBotBox(); ?>
      </div>
      <div id="info_lang" style="font-weight:bold;margin-bottom:10px">
        <?php echo getTopBox("ffffff", "C3D9FF", "E4EAEF"); ?>
        <?php echo getCloseBtn("JavaScript:hideId('info_lang');","<convert>#label=371<convert>"); ?><!-- Fermer -->
        <convert>#label=725<convert><!--Nous recherchons des traducteurs pour les langues suivantes :
        <ul>
          <li>Russe</li>
          <li>Portugais</li>
          <li>Italien</li>
          <li>...</li>
        </ul>
        Si vous désirez faire partie des traducteurs de--> <?php echo $_SESSION['Application_name']; ?>, <convert>#label=726<convert><!--merci de--> <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message&amp;subject=Translation" target="filter"><convert>#label=312<convert><!--Contacter votre administrateur/modérateur--></a>.
        <?php echo getBotBox(); ?>
      </div>
<?php
}
//if ($_SESSION['home_page'] == "overview") {
//CRO 2011-10-12
if(false) {
?>
      <div class="menu" id="connected_cavers_menu">
        <?php echo getTopMenu('<div class="frame_title" style="margin-right:0px;">'.setTitle("#", "details", "<convert>#label=686<convert>", 1).'</div><!--Speleo connectes-->'); ?>
        <select class="input1" style="width:100%" name="connected_cavers" id="connected_cavers" size="3" onclick="JavaScript:selectOnClick(event, this);"><!-- ondblclick="JavaScript:selectOnClick(event, this, event);"-->
<?php
        $connected_cavers = implode(", ", readSessionsVar("user_id"));//concat_WS(getConnectedCaversArray(),", ");
        if ($connected_cavers != "") {
          $sql = "SELECT ca.Id AS objectId, ca.Nickname AS text, 'caver' AS category ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ca ";
          $sql .= "WHERE ca.Id IN (".$connected_cavers.") ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "objectId";
          $categoryCol = "category";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $categoryCol, $textCol);
        } ?>
        </select>
        <?php echo getBotMenu(); ?>
      </div>
<?php
}
if ($_SESSION['home_page'] == "overview") {
?>
      <div class="menu" id="search_engine">
        <?php echo getTopMenu('<div class="frame_title" style="margin-right:0px;">'.setTitle("#", "details", "<convert>#label=307<convert>", 1).'</div><!--Recherche rapide-->'); ?>
        <form id="quick_search" name="quick_search" action="" onsubmit="JavaScript:goSearch(document.quick_search);stopSubmit(event);">
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;"><tr><td style="margin:0;padding:0;white-space:nowrap;">
            <input type="text" class="input1" id="search_str" name="search_str" value="<convert>#label=308<convert>" style="clear:both;color:#808080;margin-bottom:2px;width:117px;" onfocus="setInputDefaultValue(this,'<convert>#label=308<convert>',true);" onblur="setInputDefaultValue(this,'<convert>#label=308<convert>',false);" />
            <input type="submit" class="button1" name="search_go" id="search_go" value="<convert>#label=171<convert>" /><!--Ok--><br />
            <convert>#label=309<convert><!--Résultat de la recherche:-->
          </td><td style="margin:0;padding:0 0 0 10px;width:66px;">
            <table border="0" cellspacing="0" cellpadding="0" style="width:66px;">
							<tr><td style="width:31px;">
								<span title="<convert>#label=384<convert>">
									<input type="checkbox" class="input1" style="border:0px none;" id="search_entries" name="search_entries" checked="checked" onclick="JavaScript:goSearch(this.form);" />
									<label for="search_entries">
										<img src="../images/icons/bullet_entry.png" alt="<convert>#label=384<convert>" /><!-- <convert>#label=384<convert>Entrées-->
									</label>
								</span>
							</td><td>
								<span title="<convert>#label=119<convert>">
									<input type="checkbox" class="input1" style="border:0px none;" id="search_caves" name="search_caves" checked="checked" onclick="JavaScript:goSearch(this.form);" />
									<label for="search_caves">
										<img src="../images/icons/bullet_cave.png" alt="<convert>#label=119<convert>" /><!-- <convert>#label=119<convert>Réseau-->
									</label>
								</span>
							</td></tr><tr><td style="width:31px;">
								<span title="<convert>#label=386<convert>">
									<input type="checkbox" class="input1" style="border:0px none;" id="search_grottoes" name="search_grottoes" checked="checked" onclick="JavaScript:goSearch(this.form);" />
									<label for="search_grottoes">
										<img src="../images/icons/bullet_grotto.png" alt="<convert>#label=386<convert>" /><!-- <convert>#label=386<convert>Clubs-->
									</label>
								</span>
							</td><td>
								<span title="<convert>#label=385<convert>">
									<input type="checkbox" class="input1" style="border:0px none;" id="search_cavers" name="search_cavers" checked="checked" onclick="JavaScript:goSearch(this.form);" />
									<label for="search_cavers">
										<img src="../images/icons/bullet_caver.png" alt="<convert>#label=385<convert>" /><!-- <convert>#label=385<convert>Spéléologues-->
									</label>
								</span>
							</td></tr>
						</table>
          </td></tr><tr><td style="margin:0;padding:0;" colspan="2">
            <select class="input1" style="width:100%" name="result" id="result" size="5" disabled="disabled" onclick="JavaScript:selectOnClick(event, this);"><!-- ondblclick="JavaScript:selectOnClick(event, this, event);"-->
              <option></option>
            </select>
          </td></tr></table>
          <input type="button" class="button1" id="advanced" name="advanced" style="visibility:hidden;display:none;" value="<convert>#label=310<convert>" onclick="JavaScript:advancedSearch();" /><!--Recherche avancée...-->
        </form>
        <?php echo getBotMenu(); ?>
      </div>
<?php
}
?>
      <div class ="menu" id="help_menu">
        <?php echo getTopMenu('<div class="frame_title" style="margin-right:0px;">'.setTitle("#", "details", "<convert>#label=311<convert>", 1).'</div><!--Aide &amp; contacts-->'); ?>
        <ul>
          <li class ="sub_menu">
      	    <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message" target="filter"><convert>#label=312<convert><!--Contacter votre administrateur/modérateur--></a>
      	  </li>
          <li class ="sub_menu">
            <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message&amp;subject=bad_content" target="filter"><convert>#label=313<convert><!--Signaler du contenu hors-charte--></a>
          </li>
          <li class ="sub_menu"> 
            <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message&amp;subject=restore_element" target="filter"><convert>#label=314<convert><!--Demander la restauration d'un élément--></a>
          </li>
          <!--<li class ="sub_menu"> 
            <iframe src="http://www.google.com/talk/service/badge/Show?tk=<convert>#label=576<convert>&amp;w=251&amp;h=18&amp;linkcolor=34558A&amp;fontfamily=arial&amp;fontsize=12" allowtransparency="true" frameborder="0" height="18" width="251"></iframe>
          </li>-->
          <li class ="sub_menu"> 
            <?php 
            $blogURL = "http://blog-" . strtolower($_SESSION['language']) . ".grottocenter.org";
            if (!in_array(strtolower($_SESSION['language']), array("fr", "en"))) {
                $blogURL = "http://blog-en.grottocenter.org";
            }?>
            <a href="<?php echo $blogURL;?>" target="_blank"><convert>#label=927<convert><!--Blog--></a>
          </li>
          <li class ="sub_menu"> 
            <a href="https://www.facebook.com/GrottoCenter" target="_blank"><convert>#label=930<convert><!--Facebook--></a>
          </li>
          <li class ="sub_menu">
            <a href="../phpBB3/viewforum.php?f=<?php echo $FAQPages[$_SESSION['language']]['home']; ?>" target="_blank"><convert>#label=315<convert><!--FAQ--></a>
          </li>
          <li class ="sub_menu"> 
            <a href="http://www.wikicaves.org" target="_blank">Wikicaves</a>
          </li>
          <li class ="sub_menu"> 
            <?php if (strtolower($_SESSION['language']) == "fr") {?>
              <a href="http://fr.wikicaves.org/partners-partenaires" target="_blank">Partenaires :</a>
            <?php } else {?>
              <a href="http://en.wikicaves.org/partners-partenaires" target="_blank">Partners:</a>
            <?php }?>
          </li>
        </ul>
        <div style="text-align: center;">
            <a href="http://www.cds46.fr/" target="_blank" style="border-bottom: none;"><img src="../images/icons/cds46.jpg" alt="CDS 46 Lot" /></a>
            <a href="http://cdspeleo11.free.fr/" target="_blank" style="border-bottom: none;"><img src="../images/icons/cds11.jpg" alt="CDS 11 Aude" /></a>
            <a href="http://www.gsbm.fr/" target="_blank" style="border-bottom: none;"><img src="../images/icons/gsbm.jpg" alt="Groupe Speleo Bagnols Marcoule" /></a>
        </div>
        <div style="text-align: center;">
            <a href="http://www.speleo-lausanne.ch/" target="_blank" style="border-bottom: none;"><img src="../images/icons/gsl.png" alt="Groupe Speleo Lausanne et environs" /></a>
            <a href="http://www.groupe-speleo-vulcain.com/" target="_blank" style="border-bottom: none;"><img src="../images/icons/vulcain.gif" alt="Groupe spéléologique Vulcain" /></a>
            <a href="http://www.saint-ex.ch/" target="_blank" style="border-bottom: none;"><img src="../images/icons/st-exupery.png" alt="Groupe de spéléologique St.-Exupéry" /></a>
        </div>
        <div style="text-align: center;">
            <a href="http://gsispeleo.wix.com/gsispeleo" target="_blank" style="border-bottom: none;"><img src="../images/icons/gsi.png" alt="Groupe Spéléo Indépendant" /></a>
        </div>
        <?php if (strtolower($_SESSION['language']) == "fr") {?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="P3WFAA25AKP7U">
                <input type="image" id="donate_button" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Don via Paypal">
                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
            </form>
        <?php } else if (strtolower($_SESSION['language']) == "es") {?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="75G3WVCTM5T5S">
                <input type="image" id="donate_button" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
            </form>
        <?php } else if (strtolower($_SESSION['language']) == "de") {?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="MATNYFNYXTUVC">
                <input type="image" id="donate_button" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
            </form>
        <?php } else {?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="margin-left: 28px;">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="TJEU7C2TZ356Y">
                <input type="image" id="donate_button" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
            </form>
        <?php }?>
        <?php echo getBotMenu(); ?>
      </div>
    </div>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "details/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>