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
 * @copyright Copyright (c) 2009-2012 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
  	include("../conf/config.php");
  	include("../func/function.php");
  	include("declaration.php");
	  $labels = getLabelArray("general", $_SESSION['language']);
?>
<?php echo getDoctype(true)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
	<head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
		include("application_".$_SESSION['language'].".php");
		include("mailfunctions_".$_SESSION['language'].".php");
		//If the site is opened after an account activation
		if ((isset($_SESSION['Activated']) || $_SESSION['home_page'] == "home") && !USER_IS_CONNECTED) {
		  //Open the connection frame
      $filterSrc = "connection_".$_SESSION['language'].".php?type=login";
    } else {
      $filterSrc = $_SESSION['filter_page'];
    /*  if (isset($_SESSION['filter_page'])) {
        //Case when filter patge is specified
        $filterSrc = $_SESSION['filter_page'];//"parameters_".$_SESSION['language']".php?type=menu";
        unset($_SESSION['filter_page']);
      } else {
        //Regular case : open the filter
        $filterSrc = "filter_".$_SESSION['language'].".php";
      }*/
    }
?>
    <?php echo getMetaTags(); ?>
		<title><?php echo $_SESSION['Application_title']; ?></title>
		<!--link rel="stylesheet" type="text/css" href="../css/global.css"-->
		<!--
		##########################################
		<?php echo deleteOldImages(); ?> old and useless images have been deleted.
		<?php echo unlockOldCategories(); ?> records have been unlocked.
		<?php echo banBadCarvers(); ?> cavers have been banned because of leak of relevance.
		##########################################
    -->
    <script type="text/javascript">
<?php echo getCDataTag(true); ?>
    var colsStatusBefore, originalColsStatus, filterSizeBefore, filterUnitBefore, originalFilterSize,
        originalFilterUnit, detailsSizeBefore, detailsUnitBefore, originalDetailsSize, originalDetailsUnit, 
        openedInfoWindowId, openedInfoWindowType, loaded;
    //var infoWindowWasOpened;
    loaded = false;
    
    function load(){
      colsStatusBefore = getColsStatus();
      originalColsStatus = getColsStatus();
      filterSizeBefore = getColsValueArray()[0];
      filterUnitBefore = getColsUnitArray()[0];
      originalFilterSize = getColsValueArray()[0];
      originalFilterUnit = getColsUnitArray()[0];
      detailsSizeBefore = getColsValueArray()[2];
      detailsUnitBefore = getColsUnitArray()[2];
      originalDetailsSize = getColsValueArray()[2];
      originalDetailsUnit = getColsUnitArray()[2];
      setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      //mySite.overview.load();
      loaded = true;
    }
    
    function getValueRegExp() {
      var regVal = new RegExp("[0-9.*]+", "ig");
      return regVal;
    }
    
    function getUnitRegExp() {
      var regUnit = new RegExp("[^0-9^\\s^,^.^*]+", "ig");
      return regUnit;
    }
    
    function getColsStatus(){
      return xtdGetElementById("frameset2").cols;
    }
    
    function setColsStatus(sValue) {
      xtdGetElementById("frameset2").cols = sValue;
    }
    
    function getColsValueArray() {
      return getColsStatus().match(getValueRegExp());
    }
    
    function getColsUnitArray() {
      var unit = getColsStatus().match(getUnitRegExp());
      if (unit.length != 3) {
        unit[2] = unit[1];
        unit[1] = "";
      }
      return unit;
    }
    
    function setFilterStatusBefore() {
      var val = getColsValueArray();
      var unit = getColsUnitArray();
      filterSizeBefore = val[0];
      filterUnitBefore = unit[0];
    }
    
    function setDetailsStatusBefore() {
      var val = getColsValueArray();
      var unit = getColsUnitArray();
      detailsSizeBefore = val[2];
      detailsUnitBefore = unit[2];
    }
    
    function setFilterSize(iValue, sUnit) {
      if (loaded) {
        if (sUnit == undefined) {
          sUnit = "%";
        }
        colsStatusBefore = getColsStatus();
        var val = getColsValueArray();
        var unit = getColsUnitArray();
        setFilterStatusBefore();
        unit[0] = sUnit;
        var newSize = iValue.toString() + unit[0] + ", *, " + val[2] + unit[2];
        setColsStatus(newSize);
      }
    }
    
    function setDetailsSize(iValue, sUnit) {
      if (loaded) {
        if (sUnit == undefined) {
          sUnit = "%";
        }
        colsStatusBefore = getColsStatus();
        var val = getColsValueArray();
        var unit = getColsUnitArray();
        setDetailsStatusBefore();
        unit[2] = sUnit;
        var newSize = val[0] + unit[0] + ", *, " + iValue.toString() + unit[2];
        setColsStatus(newSize);
      }
    }
    
    function setFilterSizeTight(oHtml) {
      if (oHtml.offsetWidth != oHtml.scrollWidth) {
        setFilterSize(parseInt(oHtml.scrollWidth),"px");
        if (oHtml.offsetWidth != oHtml.scrollWidth) {
          setFilterSize(2 * parseInt(oHtml.scrollWidth) - parseInt(oHtml.offsetWidth),"px");
        }
      }        
    }
    
    function filterSizeRestore(){
      if (loaded) {
        setFilterSize(filterSizeBefore, filterUnitBefore);
      }
    }
    
    function detailsSizeRestore(){
      if (loaded) {
        setDetailsSize(detailsSizeBefore, detailsUnitBefore);
      }
    }
    
    function resetFilterSize(){
      if (loaded) {
        setFilterSize(originalFilterSize, originalFilterUnit);
      }
    }
    
    function resetDetailsSize(){
      if (loaded) {
        setDetailsSize(originalDetailsSize, originalDetailsUnit);
      }
    }
    
    function setSessionTimer(userIsConnected) {
      if (userIsConnected == "1") {
        var alertDelay = 5; //Minutes
        var expirationTime = <?php echo ini_get('session.gc_maxlifetime')/60; ?>; //Minutes
        url = "../index.php?logout=true";
        var firstMsg = "<?php echo $labels[7]; ?>";
        var lastMsg = "<?php echo $labels[6]; ?>";
        sessionTimer(alertDelay, expirationTime, url, firstMsg, lastMsg);
      } else {
        clearSessionTimer();
      }
    }
    
<?php include("../scripts/events.js"); ?>
<?php echo getCDataTag(false); ?>
    </script>
<?php
		switch ($_SESSION['Application_availability']) {
		  case 1:
?>
	</head>
	<frameset id="frameset1" rows="78px,100%" cols="100%">
		<frame src="banner_<?php echo $_SESSION['language']; ?>.php" id="banner" name="banner" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" noresize="noresize" />
		<frameset id="frameset2" rows="100%" cols="228px,*,25%" onload="JavaScript:load();">
			<frame src="<?php echo $filterSrc; ?>" id="filter" name="filter" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" />
<?php
      switch ($_SESSION['home_page']) {
        case "overview":
?>
			<frame src="overview_<?php echo $_SESSION['language']; ?>.php" id="overview" name="overview" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" />
<?php
        break;
        case "home":
        default:
?>
      <frame src="home_<?php echo $_SESSION['language']; ?>.php" id="home" name="home" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" />
<?php
        break;
      }
?>
			<frame src="details_<?php echo $_SESSION['language']; ?>.php" id="details" name="details" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" />
	  </frameset>
	</frameset>
<?php
		  break;
		  default:
?>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
	</head>
  <body>
    <center>
      <h1>
        <?php echo $_SESSION['Application_message']; ?>
      </h1>
    </center>
<?php
$virtual_page = "site/unavailable/".$_SESSION['language'];
include_once "../func/suivianalytics.php" ?>
  </body>
<?php
		  break;
		}
?>
</html>
