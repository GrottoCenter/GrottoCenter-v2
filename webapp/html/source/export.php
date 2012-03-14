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
 * @copyright Copyright (c) 2009-1912 Cl�ment Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
if (!allowAccess(entry_export_all)){
  exit();
}
$frame = "filter";
$list = "";
$arrList = array();
$params = "";
$file_name = "";
$file_format = "";
if (isset($_POST['save'])){
  $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
  $file_name = (isset($_POST['file_name'])) ? $_POST['file_name'] : '';
  $file_format = (isset($_POST['file_format'])) ? $_POST['file_format'] : '';
  if ($list != "") {
    $arrList = split('[|]+', $list);
  }
	$file_param = "file=getMarkers".$file_format.".php";
  $params = $file_param."&id="; //webservices/download.php?id=
  foreach($arrList as $value) {
    $params .= $value.",";
  }
  $fileName = ($file_name=='') ? '' : urlencode($file_name);
  $params .= "0&dwl=&name=".$fileName."&ext=".urlencode(strtolower($file_format));
}
$helpId = array("export" => 17);
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
    <script type="text/javascript" src="../scripts/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../scripts/jquery.cookie.js"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=765<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script type="text/javascript">
<?php echo getCDataTag(true); ?>

<?php include("../scripts/events.js"); ?>

    function exportBeforeLoad() {
      parent.setFilterSize(320, "px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml)
    }
		
		$.download = function(url, data, method){
			//url and data options required
			if( url && data ){ 
				//data can be string of parameters or array/object
				data = typeof data == 'string' ? data : $.param(data);
				//split params into form inputs
				var inputs = '';
				$.each(data.split('&'), function(){ 
					var pair = this.split('=');
					inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
				});
				//send request
				$('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>').appendTo('body').submit().remove();
			};
		};
  
    function exportOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
<?php if ($params != ""){ ?>
			var myUrl = "webservices/download.php";
			t = $.ajax({type:'POST', url:myUrl, async:false, cache:false, data:'ff=g&<?php echo $file_param; ?>'}).responseText;
			if(t.length<10) return alert("<?php echo MESSAGE_NOT_SENT; ?>"+t);
			$.cookie('<?php echo TOKEN_NAME; ?>',t);
			$.download(myUrl, 'ff=d&<?php echo $params; ?>');
<?php } ?>
    }
    
    function selectOnClick(e, oSelect) {
      var Id = oSelect.options[oSelect.selectedIndex].value;
      document.body.focus();
    	var Category = "entry";
    	openMe(Id, Category, false);
      detailMarker(e, Category, Id, '<?php echo $_SESSION['language']; ?>', false);
  	}

  	function entryRemove() {
      var oForm = document.new_export;
      var oOptions = oForm.e_myList.options;
      for (var i=0; i<oOptions.length; i++) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
  	}
  
  	function entryAdd() {
      var windowName, url;
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrées à ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      openWindow(url, windowName, 1025, 520);
  	}
	
  	function addEntry(oForm) {
      var uForm = document.new_export;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }
    
    function exportOnSubmit(event) {
      var oForm = document.new_export;
      doChallengeList(oForm.e_myList,oForm.e_list);
    }
    
    function exportCancel() {
      self.location.href = "filter_<?php echo $_SESSION['language']; ?>.php";
    }
    
    exportBeforeLoad();
<?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:exportOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:exportCancel();","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=765<convert>", 2); ?></div><!--Télécharger un export pour GPS-->
  	<form id="new_export" name="new_export" method="post" action="" onsubmit="JavaScript:exportOnSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId['export'], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList">
            <b><convert>#label=766<convert><!--Les entrées à télécharger--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(event, this);" ondblclick="JavaScript:entryRemove();">
<?php
          $sql = "SELECT Id AS value, Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ";
          $sql .= "WHERE Id IN (";
          foreach($arrList as $value) {
            $sql .= $value.", ";
          }
          $sql .= "0) ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
          </select>
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="add" name="add" value="<convert>#label=74<convert>" onclick="JavaScript:entryAdd();" /><!--    Ajouter à ma liste  /\-->
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="remove" name="remove" value="<convert>#label=73<convert>" onclick="JavaScript:entryRemove();" /><!--\/  Retirer de ma liste    -->
        </td></tr><tr><td width="170" class="label">
          <label for="file_format0">
  			    <convert>#label=768<convert><!--Format du ficher-->
          </label>
        </td><td class="field">
      		<input class="input1" type="radio" id="file_format0" name="file_format" value="GPX" style="border: none;" <?php if ($file_format=='GPX' || $file_format=='') {echo 'checked="checked"';} ?> /> <convert>#label=770<convert><!--GPX-->
    		</td></tr><tr><td width="170" class="label">
          <label for="file_format1">
          </label>
        </td><td class="field">
      		<input class="input1" type="radio" id="file_format1" name="file_format" value="KML" style="border: none;" <?php if ($file_format=='KML') {echo 'checked="checked"';} ?> /> <convert>#label=771<convert><!--KML-->
    		</td></tr><tr><td width="170" class="label">
          <label for="file_name">
  			    <convert>#label=767<convert><!--Nom du ficher-->
          </label>
        </td><td class="field">
      		<input class="input1" type="text" id="file_name" name="file_name" value="<?php echo $file_name; ?>" size="15" maxlength="36" />
    		</td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="e_list" name="e_list" />
		      <input class="button1" type="submit" id="save" name="save" value="<convert>#label=769<convert>" /><!--Telecharger-->
        </td></tr><tr><td class="field" colspan="2">
		      <input class="button1" onclick="JavaScript:exportCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr>
      </table>
    </form>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "export/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>