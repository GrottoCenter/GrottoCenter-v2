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
if (!allowAccess(request_view_mine)) {
  exit();
}
$frame = "filter";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" charset="UTF-8" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
		$_sess_user_id = $_SESSION['user_id'];
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=82<convert></title><!--Ajouter une entrée.-->
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/request.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $parameters = "";
    $status_name = "";
    $locked = false;
    $read_write = false;
    $is_public = "NO";
    $remove_north = "NO";
    $remove_scale = "NO";
    $distort_topo = "NO";
    $message = "";
    $authors_all_validated = 'false';
    $rw_status_array = array("draft","rejected");
    if (allowAccess(request_approve_all)) {
      $manage_status_array = array("submitted","canceled");
      $def_status_array = array();
      $approve_status_array = array("canceled","submitted");
      $reject_status_array = array("canceled","submitted","approved");
      $draft_status_array = array("", "draft","rejected","canceled","submitted");
      $submit_status_array = array("", "draft","rejected");
    } elseif (allowAccess(request_edit_mine)) {
      $manage_status_array = array("none!");
      $def_status_array = array("draft","rejected");
      $approve_status_array = array();
      $reject_status_array = array();
      $draft_status_array = array("", "draft","rejected","submitted");
      $submit_status_array = array("", "draft","rejected");
    }
    $regForCat = "*";
    $helpId = array('edit' => 19, 'topo' => 19, 'author' => 19);
    
    if (allowAccess(request_delete_mine)) {
      //Delete the element
      if (isset($_POST['delete'])){
        $did = (isset($_POST['delete_id'])) ? $_POST['delete_id'] : '';
        if ($did != "") {
          $sql = "SELECT T_topography.Id AS Id_topography, T_status.Name, T_request.Id_author AS Id_caver, J_topo_file.Id_file, J_topo_author.Id_author, T_file.Path ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_topography` ON T_request.Id = T_topography.Id_request ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_file` ON J_topo_file.Id_topography = T_topography.Id ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_file` ON T_file.Id = J_topo_file.Id_file ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_status` ON T_status.Id = T_request.Id_status ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_author` ON J_topo_author.Id_topography = T_topography.Id ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_author` ON T_author.Id = J_topo_author.Id_author ";
          $sql .= "WHERE T_request.Id = ".$did;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $dtid = $data[0]['Id_topography'];
          $dst = $data[0]['Name'];
          $aid = $data[0]['Id_caver'];
          if (in_array($dst,$rw_status_array) && $aid == $_sess_user_id) {
            trackAction("delete_request",$did,"T_request");
            $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_request` WHERE Id = ".$did;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            trackAction("delete_topography",$dtid,"T_topography");
            $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_topography` WHERE Id = ".$dtid;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_topo_cave` WHERE Id_topography = ".$dtid;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            for ($i=0; $i<$data['Count']; $i++) {
              $dfid = $data[$i]['Id_file'];
              $dpid = $data[$i]['Path'];
              $daid = $data[$i]['Id_author'];
              if ($dfid != "") {
                trackAction("delete_file",$dfid,"T_file");
                $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_file` WHERE Id = ".$dfid;
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_topo_file` WHERE Id_file = ".$dfid;
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                @unlink('../upload/topos/'.basename($dpid));
              }
              if ($daid != "") {
                trackAction("delete_author",$daid,"T_author");
                $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_author` WHERE Id = ".$daid;
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_topo_author` WHERE Id_author = ".$daid;
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
              }
            }
            $delete_failed = false;
          } else {
            $delete_failed = true;
          }
        } else {
          $delete_failed = true;
        }
        $type = "menu";
      }
      
      //Open Deleting window
      if ($type == "delete") {
        $did = (isset($_GET['did'])) ? $_GET['did'] : '';
        if (takeOver("request",$did) && $did != "") {
          $sql = "SELECT Name FROM T_request WHERE Id = ".$did;
          $name = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $name = $name[0]['Name'];
          $parameters = "&cancel=True&cid=".$did."&ccat=request";
        } else {
          $locked = true;
          $type = "menu";
        }
      }
    }

    if (allowAccess(request_edit_mine)) {
      if ($type != "topo" && $type != "author") {
        // Save the request
        if (isset($_POST['save']) || isset($_POST['send']) || isset($_POST['approve']) || isset($_POST['forward']) || isset($_POST['reject'])) {
          $save_failed = true;
          $name = (isset($_POST['n_request_name'])) ? $_POST['n_request_name'] : '';
          $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
          $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
          $recipient = (isset($_POST['n_request_recipient'])) ? $_POST['n_request_recipient'] : '';
          $comments = (isset($_POST['n_request_comments'])) ? $_POST['n_request_comments'] : '';
          $old_comments = (isset($_POST['n_request_old_comments'])) ? $_POST['n_request_old_comments'] : '';
    	    $is_public = (isset($_POST['n_request_is_public'])) ? $_POST['n_request_is_public'] : '';
    	    $remove_north = (isset($_POST['n_request_remove_north'])) ? $_POST['n_request_remove_north'] : '';
    	    $remove_scale = (isset($_POST['n_request_remove_scale'])) ? $_POST['n_request_remove_scale'] : '';
    	    $distort_topo = (isset($_POST['n_request_distort_topo'])) ? $_POST['n_request_distort_topo'] : '';
          $status_name = (isset($_POST['status_name'])) ? $_POST['status_name'] : '';
          $topography = (isset($_POST['id_topography'])) ? $_POST['id_topography'] : '';
          $enabled = "NO";
          if (isset($_POST['save'])) {
            if ($status_name == "") {
              $status = 'draft';
            } else {
              $status = $status_name;
            }
          } elseif (isset($_POST['send'])) {
            $status = 'submitted';
            $message = "<convert>#label=856<convert>"; //Votre demande a été envoyée à son destinataire qui recevra un email. Elle sera traitée dans les meilleurs délais. Vous recevrez une notification par mail dès la fin de son traitement.
          } elseif (isset($_POST['approve'])) {
            $status = 'approved';
            $enabled = "YES";
            $message = "<convert>#label=859<convert>"; //La demande a été validée.
          } elseif (isset($_POST['forward'])) {
            $status = 'submitted';
            $message = "<convert>#label=857<convert>"; //La demande a été correctement transférée.
          } elseif (isset($_POST['reject'])) {
            if ($status_name == "approved") {
              $status = 'canceled';
              $message = "<convert>#label=858<convert>"; //La demande a été retirée.
            } else {
              $status = 'rejected';
              $message = "<convert>#label=860<convert>"; //La demande a été refusée.
            }
          } else {
            $status = 'draft';
          }
          $date_stamp = '-------- '.addslashes($_SESSION['user_login']).' '.date("Y-m-d H:i:s").' --------';
					if ($status_name == 'draft' || $status_name == '') {
						$comments = ($old_comments == '' && $comments != '') ? $date_stamp."\n".$comments : $comments;
					} else {
            $comments = ($comments != '') ? $date_stamp."\n".$comments : '';
            if ($old_comments != '') {
              $comments = ($comments != '') ? $old_comments."\n".$comments : $old_comments;
            }
          }
          if (isset($_POST['send']) || isset($_POST['approve']) || isset($_POST['forward']) || isset($_POST['reject'])) {
            $comments = $comments."\n".addslashes($_SESSION['user_login'])." ".date("Y-m-d H:i:s")." -&gt; ".$status;
          }
          $sql = "SELECT Id FROM T_status WHERE Name = ".returnDefault($status, 'text');
          $status_id = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $status_id = $status_id[0]['Id'];
          if ($isNew == "True") {
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_request` ";
            $sql .= "(`Id_author`, `Name`, `Date_inscription`, `Id_recipient`, `Id_status`, `Comments`)";
            $sql .= " VALUES (";
            $sql .= $_sess_user_id.", ";
            $sql .= returnDefault($name, 'text').", ";
            $sql .= "Now(), ";
            $sql .= returnDefault($recipient, 'id').", ";
            $sql .= returnDefault($status_id, 'id').", ";
            $sql .= returnDefault($comments, 'text').") ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $nid = $req['mysql_insert_id'];
            trackAction("insert_request",$nid,"T_request");
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_topography` ";
            $sql .= "(`Id_author`, `Date_inscription`, `Id_request`, `Is_public`, `Remove_north`, `Remove_scale`, `Distort_topo`, `Enabled`)";
            $sql .= " VALUES (";
            $sql .= $_sess_user_id.", ";
            $sql .= "Now(), ";
            $sql .= returnDefault($nid, 'id').", ";
            $sql .= returnDefault($is_public, 'inv_checkbox').", ";
            $sql .= returnDefault($remove_north, 'checkbox').", ";
            $sql .= returnDefault($remove_scale, 'checkbox').", ";
            $sql .= returnDefault($distort_topo, 'checkbox').", ";
            $sql .= returnDefault($enabled, 'text').") ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $topography = $req['mysql_insert_id'];
            trackAction("insert_topography",$topography,"T_topography");
          } else {
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_request` ";
            $sql .= " SET ";
            $sql .= "Locked = 'NO', ";
            $sql .= "Id_reviewer = ".$_sess_user_id.", ";
            $sql .= "Date_reviewed = Now(), ";
            $sql .= "Name = ".returnDefault($name, 'text').", ";
            $sql .= "Id_recipient = ".returnDefault($recipient, 'id').", ";
            $sql .= "Id_status = ".returnDefault($status_id, 'id').", ";
            if (isset($_POST['send'])) {
              $sql .= "Date_issue = Now(), ";
            }
            $sql .= "Comments = ".returnDefault($comments, 'text')." ";
            $sql .= "WHERE Id = ".$id;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          	$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_topo_cave` ";
          	$sql .= "WHERE `Id_topography` = ".$topography;
          	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            trackAction("edit_request",$id,"T_request");
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_topography` ";
            $sql .= " SET ";
            $sql .= "Id_request = ".returnDefault($id, 'id').", ";
            $sql .= "Is_public = ".returnDefault($is_public, 'inv_checkbox').", ";
            $sql .= "Remove_north = ".returnDefault($remove_north, 'checkbox').", ";
            $sql .= "Remove_scale = ".returnDefault($remove_scale, 'checkbox').", ";
            $sql .= "Distort_topo = ".returnDefault($distort_topo, 'checkbox').", ";
            $sql .= "Enabled = ".returnDefault($enabled, 'text')." ";
            $sql .= "WHERE Id = ".$topography;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            trackAction("edit_topography",$topography,"T_topography");
          }
        	if ($isNew == "True") {
        		$onid = $nid;
        	} else {
        		$onid = $id;
        	}
          if ($list != "") {
            $arrList = explode("|", $list);
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_topo_cave` (`Id_topography`, `Id_cave`, `Id_entry`) VALUES ";
            foreach($arrList as $value) {
              $linked_id = explode($regForCat, $value);
              $sql .= "(".$topography.", ".$linked_id[0].", ".$linked_id[1]."), ";
            }
            $sql = substr($sql,0,strlen($sql)-2);
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          }
          $sql = "SELECT GROUP_CONCAT(DISTINCT T_entry.Id SEPARATOR ',') AS Id_entry ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_cave` ON J_topo_cave.Id_topography = T_topography.Id ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_cave = J_topo_cave.Id_cave ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_entry` ON (T_entry.Id = J_cave_entry.Id_entry OR T_entry.Id = J_topo_cave.Id_entry) ";
          $sql .= "WHERE T_topography.Id_request = ".$onid." ";
          $sql .= "GROUP BY T_topography.Id_request ";
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          if ($data[0]['Id_entry'] != "") {
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_entry` ";
            $sql .= "SET Has_contributions = IF((SELECT COUNT(*) FROM `".$_SESSION['Application_host']."`.`V_contributions` WHERE Id_entry IN (".$data[0]['Id_entry'].")) = 0, 'NO', 'YES') ";
            $sql .= "WHERE Id IN (".$data[0]['Id_entry'].") ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          }
          $save_failed = false;
          $type = "menu";
          $reload = (isset($_POST['save']));
          if (isset($_POST['send']) || isset($_POST['approve']) || isset($_POST['forward']) || isset($_POST['reject'])) {
            sendRequestMail($onid);
          }
        } else {
          if (isset($_GET['id'])) {
            $id = (isset($_GET['id'])) ? $_GET['id'] : '';
            if ($id != "") {
              $sql = "SELECT ca.Nickname, req.Name, req.Id_recipient, req.Id_status, st.Name AS Status_name, lbl.".$_SESSION['language']." AS Status_label, ";
              $sql .= "req.Id_author, req.Comments, topo.Is_public, topo.Remove_north, topo.Remove_scale, topo.Distort_topo, topo.Id AS Id_topography ";
              $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` req ";
              $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_topography` topo ON req.Id = topo.Id_request ";
              $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_caver` ca ON req.Id_author = ca.Id ";
              $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_status` st ON st.Id = req.Id_status ";
              $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_label` lbl ON lbl.Id = st.Id_label ";
              $sql .= "WHERE req.Id = ".$id;
              $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
            }
            $taken = false;
            if ($data[0]['Status_name'] == 'approved') {
              $taken = true;
            } else {
              $taken = takeOver("request",$id);
            }
            if ($taken) {
              if ($data['Count'] > 0) {
                $name = $data[0]['Name'];
                $recipient = $data[0]['Id_recipient'];
                $status_id = $data[0]['Id_status'];
                $status_name = $data[0]['Status_name'];
                $status = $data[0]['Status_name'];
                $diag_status = $data[0]['Status_name'];
                $status_lbl = $data[0]['Status_label'];
                $applicant_lbl = $data[0]['Nickname'];
                $comments = $data[0]['Comments'];
          	    $is_public = $data[0]['Is_public'];
          	    $remove_north = $data[0]['Remove_north'];
          	    $remove_scale = $data[0]['Remove_scale'];
          	    $distort_topo = $data[0]['Distort_topo'];
          	    $topography = $data[0]['Id_topography'];
                $is_mine = $data[0]['Id_author'] == $_sess_user_id;
                $read_write = ($is_mine && in_array($status_name,$rw_status_array)) || in_array($status_name,$manage_status_array);
                $isNew = "False";
              }
              $parameters = "&cancel=True&cid=".$id."&ccat=request";
            } else {
              $locked = true;
              $type = "menu";
            }
          } else {
            $isNew = "True";
            $status = 'draft';
            $diag_status = 'new';
            $status_lbl = "<convert>#label=54<convert>"; //Nouveau
            $applicant_lbl = $_SESSION['user_nickname'];
            $read_write = true;
            $preselected_entry_id = (isset($_GET['entry_id'])) ? $_GET['entry_id'] : '';
          }
        }
      }
      if ($type == "topo") {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        $upload_error = (isset($_GET['error'])) ? $_GET['error'] : '';
        $upload_error = urldecode($upload_error);
        $original_topo_file = (isset($_GET['original_filename'])) ? $_GET['original_filename'] : '';
        $original_topo_file = urldecode($original_topo_file);
        $sql = "SELECT Id ";
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
        $sql .= "WHERE Id_request = ".$id;
        $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        $topo_id = $data[0]['Id'];
        if ($topo_id != "") {
      	  if (isset($_GET['topo_changed']) && $_GET['topo_changed'] == "true") {
            $topo_file = (isset($_GET['topo_name'])) ? $_GET['topo_name'] : '';
            $topo_file = urldecode($topo_file);
            if (isset($_GET['uploaded']) && $_GET['uploaded'] == "true") {
              if ($topo_file != "") {
                $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_file` ";
                $sql .= "(Id_author, Date_inscription, Name, Path) VALUES (";
                $sql .= returnDefault($_sess_user_id, 'id').", ";
                $sql .= "Now(), ";
                $sql .= returnDefault($original_topo_file,'text').", ";
                $sql .= returnDefault($_SESSION['Application_url'].'/upload/topos/'.$topo_file,'text').") ";
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                $file_id = $req['mysql_insert_id'];
                trackAction("insert_file",$file_id,"T_file");
                $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_topo_file` ";
                $sql .= "(Id_topography, Id_file) VALUES (";
                $sql .= returnDefault($topo_id, 'id').", ";
                $sql .= returnDefault($file_id, 'id').") ";
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
              }
            } elseif (isset($_GET['deleted']) && $_GET['deleted'] == "true") {
              $file_id = (isset($_GET['file_id'])) ? $_GET['file_id'] : '';
              if ($file_id != "") {
                $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_topo_file` WHERE Id_file = ".$file_id;
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                trackAction("delete_file",$file_id,"T_file");
                $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_file` WHERE Id = ".$file_id;
                $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
              }
            }
          }
          $sql = "SELECT DISTINCT T_file.Id, T_file.Path, T_topography.Id_author, T_status.Name AS Status_name ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_topography` ON T_request.Id = T_topography.Id_request ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_file` ON J_topo_file.Id_topography = T_topography.Id ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_file` ON T_file.Id = J_topo_file.Id_file ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_status` ON T_status.Id = T_request.Id_status ";
          $sql .= "WHERE T_topography.Id = ".$topo_id;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $files_array = array();
          for ($i = 0; $i < $data['Count']; $i = $i + 1) {
            if ($data[$i]['Id'] != "" && $data[$i]['Path'] != "") {
              $files_array[] .= $data[$i]['Id'].'": "'.basename($data[$i]['Path']);
            }
          }
          array_walk($files_array,'set_quotes',"\"");
          $files_array = implode(",", $files_array);
          $status_name = $data[0]['Status_name'];
          $is_mine = $data[0]['Id_author'] == $_sess_user_id;
          $read_write = ($is_mine && in_array($status_name,$rw_status_array)) || in_array($status_name,$manage_status_array);
        }
      }
      if ($type == "author") {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        $sql = "SELECT Id ";
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
        $sql .= "WHERE Id_request = ".$id;
        $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        $topo_id = $data[0]['Id'];
        if ($topo_id != "") {
          if (isset($_POST['addAuthor'])) {
            $name = (isset($_POST['n_author_name'])) ? $_POST['n_author_name'] : '';
            $contact = (isset($_POST['n_author_contact'])) ? $_POST['n_author_contact'] : '';
            $i_am = (isset($_POST['n_author_iam'])) ? $_POST['n_author_iam'] : '';
            if ($name != "" && $contact != "") {
              $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_author` ";
              $sql .= "(Id_author, Date_inscription, Name, Contact, Creator_is_author) VALUES (";
              $sql .= returnDefault($_sess_user_id, 'id').", ";
              $sql .= "Now(), ";
              $sql .= returnDefault($name,'text').", ";
              $sql .= returnDefault($contact,'text').", ";
              $sql .= returnDefault($i_am, 'checkbox').") ";
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
              $author_id = $req['mysql_insert_id'];
              trackAction("insert_author",$author_id,"T_author");
              $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_topo_author` ";
              $sql .= "(Id_topography, Id_author) VALUES (";
              $sql .= returnDefault($topo_id, 'id').", ";
              $sql .= returnDefault($author_id, 'id').") ";
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            }
          } elseif (isset($_POST['d_author_id']) && $_POST['d_author_id'] != "") {
            $author_id = (isset($_POST['d_author_id'])) ? $_POST['d_author_id'] : '';
            if ($author_id != "") {
              $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_topo_author` WHERE Id_author = ".$author_id;
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
              trackAction("delete_author",$author_id,"T_author");
              $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_author` WHERE Id = ".$author_id;
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            }
          } elseif (isset($_POST['v_author_id']) && $_POST['v_author_id'] != "" && allowAccess(request_approve_all)) {
            $author_id = (isset($_POST['v_author_id'])) ? $_POST['v_author_id'] : '';
            $validated = (isset($_POST['n_author_validated'])) ? $_POST['n_author_validated'] : 'NO';
            if ($author_id != "") {
              $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_author` SET ";
              $sql .= "Validated = ".returnDefault($validated, 'text')." ";
              $sql .= "WHERE Id = ".$author_id;
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
              trackAction("edit_author",$author_id,"T_author");
            }
          } elseif (isset($_GET['uploaded']) || isset($_GET['deleted'])) {
            $upload_error = (isset($_GET['error'])) ? $_GET['error'] : '';
            $upload_error = urldecode($upload_error);
            $original_attachment_file = (isset($_GET['original_filename'])) ? $_GET['original_filename'] : '';
            $uploaded_author_id = (isset($_GET['authorid'])) ? $_GET['authorid'] : '';
            $original_attachment_file = urldecode($original_attachment_file);
        	  if (isset($_GET['attachment_changed']) && $_GET['attachment_changed'] == "true") {
              $attachment_file = (isset($_GET['attachment_name'])) ? $_GET['attachment_name'] : '';
              $attachment_file = urldecode($attachment_file);
              if (isset($_GET['uploaded']) && $_GET['uploaded'] == "true") {
                if ($attachment_file != "") {
                  $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_file` ";
                  $sql .= "(Id_author, Date_inscription, Name, Path) VALUES (";
                  $sql .= returnDefault($_sess_user_id, 'id').", ";
                  $sql .= "Now(), ";
                  $sql .= returnDefault($original_attachment_file,'text').", ";
                  $sql .= returnDefault($_SESSION['Application_url'].'/upload/attachments/'.$attachment_file,'text').") ";
                  $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                  $file_id = $req['mysql_insert_id'];
                  trackAction("insert_file",$file_id,"T_file");
                  $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_author_file` ";
                  $sql .= "(Id_author, Id_file) VALUES (";
                  $sql .= returnDefault($uploaded_author_id, 'id').", ";
                  $sql .= returnDefault($file_id, 'id').") ";
                  $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                }
              } elseif (isset($_GET['deleted']) && $_GET['deleted'] == "true") {
                $file_id = (isset($_GET['file_id'])) ? $_GET['file_id'] : '';
                if ($file_id != "") {
                  $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_author_file` WHERE Id_file = ".$file_id;
                  $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                  trackAction("delete_file",$file_id,"T_file");
                  $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_file` WHERE Id = ".$file_id;
                  $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
                }
              }
            }
          }
          $sql = "SELECT DISTINCT T_file.Id AS File_id, T_file.Path, T_author.Id, T_author.Validated, T_topography.Id_author, T_status.Name AS Status_name ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_topography` ON T_request.Id = T_topography.Id_request ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_status` ON T_status.Id = T_request.Id_status ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_author` ON J_topo_author.Id_topography = T_topography.Id ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_author` ON T_author.Id = J_topo_author.Id_author ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_author_file` ON J_author_file.Id_author = T_author.Id ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_file` ON T_file.Id = J_author_file.Id_file ";
          $sql .= "WHERE T_topography.Id = ".$topo_id;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $files_array = array();
          $authors_array = array();
          $authors_all_validated = 'true';
          for ($i = 0; $i < $data['Count']; $i = $i + 1) {
            if ($data[$i]['Id'] != "" && $data[$i]['Path'] != "") {
              $files_array[] .= $data[$i]['File_id'].'": "'.basename($data[$i]['Path']);
            }
            if ($data[$i]['Id'] != "" && $data[$i]['Validated'] != "") {
              $author_validated = ($data[$i]['Validated']=='YES') ? 'NO' : 'YES';
              $authors_array[] .= $data[$i]['Id'].'": "'.$author_validated;
              if ($data[$i]['Validated']=='NO') {
                $authors_all_validated = 'false';
              }
            }
          }
          array_walk($files_array,'set_quotes',"\"");
          $files_array = implode(",", $files_array);
          array_walk($authors_array,'set_quotes',"\"");
          $authors_array = implode(",", $authors_array);
          $status_name = $data[0]['Status_name'];
          $is_mine = $data[0]['Id_author'] == $_sess_user_id;
          $read_write = ($is_mine && in_array($status_name,$rw_status_array)) || in_array($status_name,$manage_status_array);
        }
      }
    }
?>
    <script type="text/javascript" charset="UTF-8" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript" charset="UTF-8">
    <?php echo getCDataTag(true); ?>
    var doCancel, namesArray, filenamesArray, authorNamesArray, advancedCheck, onlineCheck, authorsValidated, authorsCount, filesCount;
<?php if($reload) { ?>
    self.location.href = "request_<?php echo $_SESSION['language']; ?>.php?type=edit&id=<?php echo $onid; ?>";
<?php } ?>
    doCancel = true;
    onlineCheck = false;
    advancedCheck = false;
    namesArray = new Array();
    filenamesArray = new Array();
    authorNamesArray = new Array();
    authorsValidated = <?php echo $authors_all_validated; ?>;
    <?php include("../scripts/events.js"); ?>
      
<?php
    switch ($type)
    {
    	case "menu":
?>
    function menuOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
    
    function requestEdit(oForm) {
      var oRadio = oForm.radio_list;
      var requestId = getRadioValue(oRadio);
      if (requestId) {
        self.location.href = "request_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + requestId;
      }
    }
    
    function requestRefresh(oForm) {
      oForm.submit();
    }
    
    function requestDelete(oForm) {
      var oRadioArray = oForm.radio_list;
      var requestId = getRadioValue(oRadioArray);
      if (requestId) {
        self.location.href = "request_<?php echo $_SESSION['language']; ?>.php?type=delete&did=" + requestId;
      }
    }
    
    function requestOnClick(id) {
      if (id) {
        self.location.href = "request_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + id;
      }
    }
<?php
      break;
    	case "delete":
?>
    function deleteOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
<?php
      break;
    	case "edit":
?>
    
    function newOnLoad(hasFailed) {
      var oForm = document.new_request;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
<?php if (!$read_write) { ?>
      blockFields(oForm);
      disableField(xtdGetElementById('cancel'),false);
<?php   if (in_array($status_name, $draft_status_array)) { ?>
	    disableField(xtdGetElementById('save'),false);
<?php   }
        if (in_array($status_name, $submit_status_array)) { ?>
	    disableField(xtdGetElementById('send'),false);
<?php   }
        if (in_array($status_name, $approve_status_array)) { ?>
	    disableField(xtdGetElementById('approve'),false);
	    disableField(xtdGetElementById('forward'),false);
<?php   }
        if (in_array($status_name, $reject_status_array)) { ?>
	    disableField(xtdGetElementById('reject'),false);
	    disableField(xtdGetElementById('n_request_comments'),false);
<?php   }
      } ?>
      if (hasFailed) {
        namesArray = loadNames("request");
        checkThisName(oForm.n_request_name);
      }
    }
    
    function newOnUnload() {
      closeAllChildWindows();
    }

    function newSubmit(event) {
      var oForm, rightSource, oField, sMessage, oTarget;
      oForm = document.new_request;
      rightSource = toAbsURL(rightPic);
      oField = xtdGetElementById('name_pic');
      sMessage = "<convert>#label=793<convert> <convert>#label=874<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom de la demande //doit être composé de 2 à 100 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      oTarget = getTargetNode(event);
      // Dans le cas ou on n'a pas cliqué sur "Enregistrer le brouillon" :
      if (advancedCheck) {
        //   * Le nombre d'entrees choisies > 0 !!
        oField = xtdGetElementById('e_myList');
        sMessage = "<convert>#label=829<convert>"; //Veuillez indiquer les entrées / réseaux liés à cette topographie
        createTest(oField.name, oField.length, 0, ">", sMessage, true);
        //   * Le destinataire doit etre sélectionné !!
        oField = xtdGetElementById('n_request_recipient');
        oOption = oField.options[oField.selectedIndex];
        sMessage = "<convert>#label=830<convert>"; //Le champ Destinataire est obligatoire.
        wrongValue = "<?php echo Select_default; ?>";
        createTest(oField.name, oOption.value, wrongValue, "!=", sMessage, true);
        //   * Le nombre d'auteurs > 0 !!
        sMessage = "<convert>#label=831<convert>"; //Veuillez ajouter au moins un auteur
        createTest('authorsCount', window.author_frame.authorsCount, 0, ">", sMessage, true);
        //   * Le nombre de fichiers ajoutés > 0 !!
        sMessage = "<convert>#label=832<convert>"; //Veuillez ajouter au moins un fichier topographique
        createTest('filesCount', window.file_frame.filesCount, 0, ">", sMessage, true);
        advancedCheck = false;
      }
      if (onlineCheck) {
        //   * Chaque auteur doit être validé !!
        sMessage = "<convert>#label=826<convert>"; //Les auteurs doivent être tous validés pour pouvoir continuer
        createTest('allAuthorsValidated', window.author_frame.authorsValidated, true, "==", sMessage, true);
        onlineCheck = false;
      }
      if (!testForm()) {
        stopSubmit(event);
      } else {
        freeFields(oForm);
      	doChallengeList(oForm.e_myList,oForm.e_list);
        doCancel = false;
      } 
    }
    
    function sendOnClick() {
      advancedCheck = true;
    }
    
    function approveOnClick() {
      advancedCheck = true;
      onlineCheck = true;
    }
    
    function saveOnClick() {
    }
    
    function forwardOnClick() {
    }
    
    function rejectOnClick() {
    }
  	
  	function entryRemove() {
      var oForm = document.new_request;
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
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=entryncave&callback=addEntry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=entryncave&callback=addEntry";
      openWindow(url, windowName, 690, 520);
  	}
  	
  	function addEntry(oForm) {
      var uForm = document.new_request;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }
    
    function checkThisName(oObject) {
      checkName(oObject, "name_pic", "request", "<?php echo $name; ?>", namesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, namesArray, "<?php echo $name; ?>"), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }
    
    function newOnBeforeUnload(event) {
      if (doCancel) {
        var msg = "<convert>#label=92<convert>";//Les modifications seront perdues !
        stopUnload(event, msg);
      }
    }
<?php
      break;
    	case "topo":
?>

    function topoOnLoad() {
      var oForm;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
<?php if ($read_write) { ?>
      oForm = document.upload_file;
      filenamesArray = loadNames("file");
      checkThisName(oForm.topo_filename);
<?php } ?>
    }
    
    function checkThisName(oObject) {
      checkName(oObject, "name_pic", "file", "", filenamesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, filenamesArray, ""), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }

    function deleteOnClick(formId, fileId) {
      var oField, oForm, filesArray;
      oForm = xtdGetElementById(formId);
      filesArray = {<?php echo $files_array; ?>};
      if (fileId) {
        if (confirm("<convert>#label=44<convert> <convert>#label=816<convert>?")) { //Etes vous sur de vouloir supprimer ce fichier ?
          oField = xtdGetElementById('topo_file');
          oField.value = filesArray[fileId];
          oField = xtdGetElementById('file_id');
          oField.value = fileId;
          oForm.setAttribute('action', '../upload/topos/topo.php');
          oForm.submit();
        }
      }
    }
    
    function addOnSubmit(event) {
      var oForm = document.upload_file;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=817<convert> <convert>#label=874<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du fichier //doit être composé de 2 à 100 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
        oForm.target_name.value = oForm.target_name.value + oForm.topo_filename.value;
      }
    }
<?php
      break;
    	case "author":
?>

    function authorOnLoad() {
      var oForm;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
<?php if ($read_write) { ?>
      oForm = document.add_author
      authorNamesArray = loadNames("author");
      checkThisName(oForm.n_author_name);
      oForm = document.upload_file;
      /*filenamesArray = loadNames("file");
      checkThisName(oForm.attachment_filename);*/
<?php } ?>
    }
    
    function checkThisName(oObject) {
      checkName(oObject, "name_pic", "author", "", authorNamesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, authorNamesArray, ""), '<convert>#label=844<convert>'); //Noms existants déjà en base :(oObject, getMatchingValues(oObject.value, namesArray, "<?php echo $name; ?>"), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }

    function deleteOnClick(formId, authorId) {
      var oField, oForm;
      oForm = xtdGetElementById(formId);
      if (authorId) {
        if (confirm("<convert>#label=44<convert> <convert>#label=822<convert>?")) { //Etes vous sur de vouloir supprimer cet auteur ?
          oField = xtdGetElementById('d_author_id');
          oField.value = authorId;
          oForm.submit();
        }
      }
    }
    
    function toggleOnClick(formId, authorId) {
      var authorsArray, oField, oForm;
      authorsArray = {<?php echo $authors_array; ?>};
      oForm = xtdGetElementById(formId);
      if (authorId) {
        oField = xtdGetElementById('v_author_id');
        oField.value = authorId;
        oField = xtdGetElementById('n_author_validated');
        oField.value = authorsArray[authorId];
        oForm.submit();
      }
    }

    function deleteAttOnClick(formId, fileId) {
      var oField, oForm, filesArray;
      oForm = xtdGetElementById(formId);
      filesArray = {<?php echo $files_array; ?>};
      if (fileId) {
        if (confirm("<convert>#label=44<convert> <convert>#label=816<convert>?")) { //Etes vous sur de vouloir supprimer ce fichier ?
          oField = xtdGetElementById('attachment_file');
          oField.value = filesArray[fileId];
          oField = xtdGetElementById('file_id');
          oField.value = fileId;
          oForm.setAttribute('action', '../upload/attachments/attachment.php');
          oForm.submit();
        }
      }
    }

    function addAttOnClick(formId, authorId) {
      var oField, oForm, url;
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&uploaded=true&id=<?php echo $id; ?>&authorid=";
			url = "../../html/request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&uploaded=true&id=<?php echo $id; ?>&authorid=";
      oForm = xtdGetElementById(formId);
      if (authorId) {
        xtdGetElementById('add_source_manager').value = url + authorId;
        filenamesArray = loadNames("file");
        showId("upload_form");
        checkThisAttName(xtdGetElementById('attachment_filename'));
      }
    }

    function checkThisAttName(oObject) {
      checkName(oObject, "name_pic_att", "file", "", filenamesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, filenamesArray, ""), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }
    
    function addAttOnSubmit(event) {
      var oForm = document.upload_file;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic_att');
      var sMessage = "<convert>#label=817<convert> <convert>#label=874<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du fichier //doit être composé de 2 à 100 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
        oForm.target_name.value = oForm.target_name.value + oForm.attachment_filename.value;
      }
    }
    
    function addOnSubmit(event) {
      var oForm, rightSource, oField, sMessage
      oForm = document.add_author;
      rightSource = toAbsURL(rightPic);
      oField = xtdGetElementById('name_pic');
      sMessage = "<convert>#label=827<convert> <convert>#label=875<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom de l'auteur //doit être composé de 2 à 70 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      oField = xtdGetElementById('n_author_contact');
      sMessage = "<convert>#label=828<convert>"; //Le champ Contact est obligatoire.
      createTest(oField.name, oField.value, "", "!=", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      }
    }
<?php
      break;
    	default:
?>
    function defaultOnload() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
<?php
      break;
    }
?>
    function requestNew() {
      self.location.href = "request_<?php echo $_SESSION['language']; ?>.php?type=edit";
    }
    
    function newCancel() {
      doCancel = false;
      self.location.href = "request_<?php echo $_SESSION['language']; ?>.php?type=menu<?php echo $parameters; ?>";
    }
<?php
    switch ($type)
    {
    	case "menu":
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:menuOnLoad();">
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("request_".$_SESSION['language'].".php?type=menu", "popup", "<convert>#label=783<convert>", 1); ?></div><!--Menu des demandes-->
<?php
      if ($locked) {
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=784<convert> <convert>#label=49<convert><!--Cette demande est en cours de modification par un autre utilisateur, veuillez essayer plus tard !--><?php echo getBotBubble(); ?></div>
<?php
      } else {
        if ((isset($_POST['save']) || isset($_POST['send'])) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=785<convert> <convert>#label=140<convert><!--La demande a été enregistrée avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
        if ($message != "") {
?>
    <div class="warning"><?php echo getTopBubble().$message.getBotBubble(); ?></div>
<?php
        }
        if (isset($_POST['delete'])) {
          if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=784<convert><!--cette demande--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=785<convert> <convert>#label=610<convert><!--La demande a été supprimée avec succès !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
      }
?>

<?php
    $columns_params = array(
			0 => "T_request*Id".(allowAccess(request_approve_all) ? '' : '[hidden]')."|".(allowAccess(request_approve_all) ? '' : '[hidden]')."#",
			1 => "[hidden]|[hidden]Locked",
			2 => "(SELECT@Nickname@FROM@".$_SESSION['Application_host']."*T_caver@WHERE@Id=T_request*Id_author)|<convert>#label=802<convert>",
			3 => "[hidden]|<convert>#label=786<convert>",
			4 => "T_request*Name|[hidden]<convert>#label=786<convert>",
			5 => "[hidden]|<convert>#label=787<convert>",
			6 => "T_caver*Nickname|<convert>#label=789<convert>",
			7 => "T_status*Name|<convert>#label=790<convert>|SELECT T_status.Name AS value, T_label.".$_SESSION['language']." AS text FROM ".$_SESSION['Application_host'].".T_status INNER JOIN ".$_SESSION['Application_host'].".T_label ON T_label.Id = T_status.Id_label ORDER BY text|5",
			8 => "[hidden]|[hidden]8",
			9 => "[hidden]|[hidden]9",
			10 => "[hidden]|[hidden]10",
			11 => "[hidden]|[hidden]11",
			12 => "[hidden]|[hidden]12",
			13 => "[hidden]|[hidden]13",
			14 => "[hidden]|<convert>#label=796<convert>",
			15 => "[hidden]|[hidden]15"
		);
		$sql = "SELECT DISTINCT ";
    $sql .= "T_request.Id AS `0`, ";
    $sql .= "IF((T_request.Locked = 'YES' AND NOT T_request.Id_locker = '".$_sess_user_id."'),1,0) AS `1`, "; //Is_locked
    $sql .= "(SELECT Nickname from `".$_SESSION['Application_host']."`.`T_caver` WHERE Id = T_request.Id_author) AS `2`, ";//Demandeur
    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_request.Name,IF(T_request.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_request.Locked = 'YES' AND T_request.Id_locker = '".$_sess_user_id."'),'".str_replace("'","''","<convert>#label=574<convert>")."',NULL)) AS `3`, ";//Nom de la demande
    $sql .= "T_request.Name AS `4`, ";//Nom de la demande
    $sql .= "T_request.Date_issue AS `5`, ";//Date d'émission
    $sql .= "T_caver.Nickname AS `6`, ";//Destinataire
    $sql .= "T_label.".$_SESSION['language']." AS `7`, ";//Statut de la demande
    $sql .= "IF((T_request.Locked = 'YES' AND T_request.Id_locker = '".$_sess_user_id."'),1,0) AS `8`, "; //I_am_the_locker
    $rw_array_for_walk = $rw_status_array;
    array_walk($rw_array_for_walk,'set_quotes',"'");
    $sql .= "IF(T_status.Name IN (".implode(",",$rw_array_for_walk)."),1,0) AS `9`, "; //I_am_allowed_to_edit
    $manage_array_for_walk = $manage_status_array;
    array_walk($manage_array_for_walk,'set_quotes',"'");
    $sql .= "IF(T_status.Name IN (".implode(",",$manage_array_for_walk)."),1,0) AS `10`, "; //I_am_allowed_to_manage
    $sql .= "IF(T_request.Id_author = '".$_sess_user_id."',1,0) AS `11`, "; //Is_mine
    $sql .= "IF(T_request.Id_recipient = '".$_sess_user_id."',1,0) AS `12`, "; //I_am_validator
    $sql .= "IF(((T_request.Id_author = '".$_sess_user_id."' AND T_status.Name IN (".implode(",",$rw_array_for_walk).")) OR T_status.Name IN (".implode(",",$manage_array_for_walk).")),1,0) AS `13`, "; //(Is_mine AND I_am_allowed_to_edit) OR I_am_allowed_to_manage
    $sql .= "IF(T_topography.Enabled='YES','../images/icons/right.png','../images/icons/wrong.png') AS `14`, ";// Validé / En ligne
    $sql .= "IF(T_topography.Enabled='YES','<convert>#label=626<convert>','<convert>#label=627<convert>') AS `15` "; //Oui //Non
    if (allowAccess(request_approve_all)) {
      //$sql .= ", IF(((T_request.Id_author = '".$_sess_user_id."' AND T_status.Name IN (".implode(",",$rw_array_for_walk).")) OR (T_status.Name IN (".implode(",",$manage_array_for_walk).") AND T_caver.Login='".addslashes($_SESSION['user_login'])."')),'<convert>#label=626<convert>','<convert>#label=627<convert>') AS `IF(((T_request*Id_author='".$_sess_user_id."'@AND@T_status*Name@IN@(".implode(",",$rw_array_for_walk)."))@OR@(T_status*Name@IN@(".implode(",",$manage_array_for_walk).")@AND@T_caver*Login='".addslashes($_SESSION['user_login'])."')),'<convert>#label=626<convert>','<convert>#label=627<convert>')|<convert>#label=807<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>|2`, "; //Demandes me concernant
			$sql .= ", IF(((T_request.Id_author = '".$_sess_user_id."' AND T_status.Name IN (".implode(",",$rw_array_for_walk).")) OR (T_status.Name IN (".implode(",",$manage_array_for_walk).") AND T_request.Id_recipient='".$_sess_user_id."')),'<convert>#label=626<convert>','<convert>#label=627<convert>') AS `16`, "; //Demandes me concernant
      $sql .= "(SELECT Language from `".$_SESSION['Application_host']."`.`T_caver` WHERE Id = T_request.Id_author) AS `17` ";//Langue
			$columns_params[16] = "IF(((T_request*Id_author='".$_sess_user_id."'@AND@T_status*Name@IN@(".implode(",",$rw_array_for_walk)."))@OR@(T_status*Name@IN@(".implode(",",$manage_array_for_walk).")@AND@T_request*Id_recipient='".$_sess_user_id."')),'<convert>#label=626<convert>','<convert>#label=627<convert>')|<convert>#label=807<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>|2";
			$columns_params[17] = "(SELECT@Language@FROM@".$_SESSION['Application_host']."*T_caver@WHERE@Id=T_request*Id_author)|<convert>#label=205<convert>|".implode(';', array_flip(getAvailableLanguages()))."|3";
    }
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_topography` ON T_request.Id = T_topography.Id_request ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` ON T_caver.Id = T_request.Id_recipient ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_status` ON T_status.Id = T_request.Id_status ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_label` ON T_label.Id = T_status.Id_label ";
    $sql .= "WHERE ";
    if (!allowAccess(request_view_all)) {
      $sql .= "T_request.Id_author = '".$_sess_user_id."' ";
    } else {
      $sql .= "1=1 ";
    }
    
    $param_link = "JavaScript:requestOnClick('<Id>');";
    $links = array (3 => array('conditions' =>  array(1 => '0'),
                                'parameters' => array('<Id>' => 0),
                                'link' => $param_link,
                                'target' => '',
                                'title' => '<convert>#label=834<convert>')); //Editer la demande
    $images = array(14 => array('conditions' =>  array(),
                                  'parameters' => array('<Src>' => 14, '<Alt>' => 15),
                                  'src' => '<Src>',
                                  'class' => 'status1',
                                  'alt' => '<Alt>',
                                  'style' => 'border:0px none;'));
    $input_type = array('type' => 'radio',
                        'title' => '',
                        'conditions' => array(1 => '0',
                                              13 => '1'));
    $style = array(3 => array('tag' => 'div',
                              'class' => 'plt_warning',
                              'conditions' => array(8 => '1')));
    $default_order = 8;
?>

<?php
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 10;
    $filter_form = "automatic_form";
    $list_form = "result_form";

    $default_array = array('submit_filter' => '1'); 
    if (allowAccess(request_approve_all)) {
      //$default_array[idEncode("IF(((T_request*Id_author='".$_sess_user_id."'@AND@T_status*Name@IN@(".implode(",",$rw_array_for_walk)."))@OR@(T_status*Name@IN@(".implode(",",$manage_array_for_walk).")@AND@T_caver*Login='".addslashes($_SESSION['user_login'])."')),'<convert>#label=626<convert>','<convert>#label=627<convert>')")] = '<convert>#label=626<convert>'; //Oui
			$default_array[idEncode("IF(((T_request*Id_author='".$_sess_user_id."'@AND@T_status*Name@IN@(".implode(",",$rw_array_for_walk)."))@OR@(T_status*Name@IN@(".implode(",",$manage_array_for_walk).")@AND@T_request*Id_recipient='".$_sess_user_id."')),'<convert>#label=626<convert>','<convert>#label=627<convert>')")] = '<convert>#label=626<convert>'; //Oui
      $default_array[idEncode("(SELECT@Language@FROM@".$_SESSION['Application_host']."*T_caver@WHERE@Id=T_request*Id_author)")] = $_SESSION['language'];
    } elseif (allowAccess(request_edit_mine)) {
      $default_array[idEncode('T_status*Name')] = $def_status_array;
    }
    
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "", $default_array, $images);
    $resource_id = $result['resource_id'];
    $filter_fields = getFilterFields($sql,$columns_params,$_POST,$filter_form,"<convert>#label=542<convert>",false,$resource_id,$default_array);//Tous
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
    $current_page = (isset($_POST['current'])) ? $_POST['current'] : 1;
    $order = (isset($_POST['order'])) ? $_POST['order'] : '';
    $by = (isset($_POST['by'])) ? $_POST['by'] : $default_order;
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    } else {
      $navigator = "";
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>">
        <table border="0" cellspacing="1" cellpadding="0" id="filter_set">
          <tr><td colspan="2"><convert>#label=601<convert><!--Pour rechercher une partie d'un mot utiliser le caractère *, ex: *erre pourrais retourner Pierre ou Terre etc.--></td></tr>
          <?php echo $filter_fields; ?>
        </table>
        <input type="hidden" name="current" value="" />
        <input type="hidden" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" name="by" value="<?php echo $by; ?>" />
        <input type="submit" name="submit_filter" class="button1" value="<convert>#label=602<convert>" /><!--Filtrer-->
        <input type="submit" name="reset_filter" class="button1" value="<convert>#label=603<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Tout afficher-->
        <input type="button" name="reset" class="button1" value="<convert>#label=604<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Effacer-->
        <br /><select class="select2" name="records_by_page" id="records_by_page" onchange="JavaScript:this.form.submit();">
          <?php echo getOptionsFromArray($records_by_page_array,"",$records_by_page); ?>
        </select> <convert>#label=664<convert><!--Lignes par page-->.
      </form>
    </div>
    <?php if ($local_count >= $records_by_page) { ?>
    <div class="navigator">
      <?php echo $navigator; ?>
    </div>
    <?php } ?>
    <div>
      <form id="<?php echo $list_form; ?>" name="<?php echo $list_form; ?>" method="post" action="">
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php if ($total_count > 0) { echo $rows; } else { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </table>
        <div class="navigator">
          <?php echo $navigator; ?>
        </div>
        <div>
          <convert>#label=605<convert><!--Nb total de résultats--> : <?php echo $total_count; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<convert>#label=606<convert><!--Nb total de pages--> : <?php echo $count_page; ?><br />
        </div>
<?php
    if (allowAccess(request_edit_mine)) {
?>
        <input type="button" class="button1" id="edit_request" name="edit_request" value="<convert>#label=53<convert>" onclick="JavaScript:requestEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="new_request" name="new_request" value="<convert>#label=54<convert>" onclick="JavaScript:requestNew();" /><!--Nouveau--><br />
<?php
    }
    if (allowAccess(request_delete_mine)) {
?>
        <input type="button" class="button1" id="del_request" name="del_request" value="<convert>#label=55<convert>" onclick="JavaScript:requestDelete(this.form);" /><!--Supprimer--><br />
<?php
    }
?>
        <input type="button" class="button1" id="refresh_request" name="refresh_request" value="<convert>#label=56<convert>" onclick="JavaScript:requestRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir--><br />
        <input class="button1" type="button" id="close" name="close" onclick="JavaScript:self.close();" value="<convert>#label=371<convert>" /><!--Fermer-->
      </form>
    </div>
<?php
			break;
    	case "delete":
        if (!allowAccess(request_delete_mine)) {
          exit();
        }
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:deleteOnLoad();">
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?>
  	<div class="frame_title"><?php echo setTitle("#", "popup", "<convert>#label=810<convert>", 2); ?></div><!--Suppression d'une demande-->
  	<form id="delete_request" name="delete_request" method="post" action="">
  		<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
  		  <tr><td>
  		    <div class="warning"><?php echo getTopBubble(); ?>
  		      <convert>#label=44<convert> <convert>#label=811<convert> <?php echo $name; ?> ?<!--Etes vous sûr de vouloir supprimer--> <!--la demande-->
  		    <?php echo getBotBubble(); ?></div>
  		  </td></tr><tr><td class="field">
  		    <input type="hidden" id="delete_id" name="delete_id" value="<?php echo $did; ?>" />
          <input type="submit" class="button1" id="delete" name="delete" value="<convert>#label=55<convert>" /><!--Supprimer-->
        </td></tr><tr><td class="field">
          <input class="button1" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" onclick="JavaScript:newCancel();" /><!--Annuler-->
        </td></tr>
      </table>
    </form>
<?php
			break;
    	case "edit":
        if (!allowAccess(request_edit_mine)) {
          exit();
        }
        if ($locked) {
        } else {
          if (!(isset($_POST['save']) || isset($_POST['send'])) || $save_failed){
            $date_stamp = '-------- '.$_SESSION['user_login'].' '.date("Y-m-d H:i:s").' --------';
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onbeforeunload="JavaScript:newOnBeforeUnload(event);" onload="JavaScript:newOnLoad(true);" onunload="JavaScript:newOnUnload();" >
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("#", "popup", "<convert>#label=61<convert>", 2); ?></div><!--Création / Modification-->
<?php     if ($save_failed) { ?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div>
<?php     } ?>
  	<form id="new_request" name="new_request" method="post" action="" onsubmit="JavaScript:newSubmit(event);" style="float:left;clear:both;">
      <table border="0" cellspacing="1" cellpadding="0"><tr><td style="vertical-align:bottom;">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
<?php     if (allowAccess(request_approve_all)) { ?>
<?php       if ($isNew != "True") { ?>
        <tr><td width="300" class="label">
          <convert>#label=861<convert><!--Numéro de la demande-->
        </td><td class="field">
    		  <?php echo $id; ?>
    		</td></tr>
<?php       } ?>
        <tr><td width="300" class="label">
	      	<convert>#label=802<convert><!--Demandeur-->
        </td><td class="field">
    		  <?php echo $applicant_lbl; ?>
    		</td></tr>
<?php     } ?>
        <tr><td width="300" class="label">
	      	<convert>#label=790<convert><!--Statut de la demande-->
        </td><td class="field">
    		  <?php echo $status_lbl; ?>
    		</td></tr><tr><td width="300" class="label">
	      	<label for="n_request_name">
	      		<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=786<convert><!--Nom de la demande--><sup>1</sup>
	      	</label>
        </td><td class="field">
    		  <input class="input1" type="text" id="n_request_name" name="n_request_name" value="<?php echo $name; ?>" size="15" maxlength="100" onkeyup="JavaScript:checkThisName(this);" />
    		  <img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
    		  <i><convert>#label=813<convert><!--Ex : Topographie de la grotte de Lascaux--></i>
    		</td></tr><tr><td width="300" class="label">
          <label for="n_request_recipient">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=789<convert><!--Destinataire de la demande-->
          </label>
	      </td><td class="field">
          <select class="select2" name="n_request_recipient" id="n_request_recipient">
<?php
          $sql = "SELECT Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, ca.Nickname As NName ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ca ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` co ON ca.Country = co.Iso ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` cg ON ca.Id = cg.Id_caver ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_group` gr ON (cg.Id_group = gr.Id AND gr.Name = 'Leader') ";
          $sql .= "ORDER BY country, NName ";
          $msg = "<convert>#label=792<convert>";//Sélectionnez un leader ...
          $comparedCol = "value";
          $countryCol = "country";
          $textCol = "NName";
          $selected = $recipient;
          echo groupOptions(getOptions($sql, $msg, $selected, $comparedCol, $countryCol, $textCol),$countryCol);
?>
          </select>
		    </td></tr><tr><td width="300" class="label">
	      	<label for="n_request_comments">
	      		<convert>#label=812<convert><!--Commentaires sur la demande-->
	      	</label>
        </td><td class="field">
          <div style="width:350px;white-space:normal;">
            <?php if ($status != 'draft') { echo replaceLinks(nl2br($comments."\n".$date_stamp)); } ?><br />
          </div>
          <textarea class="input1" id="n_request_comments" name="n_request_comments" style="width:100%" rows="5" cols="" wrap="soft"><?php if ($status == 'draft') { echo $comments; } ?></textarea>
    		</td></tr><tr><td class="field" colspan="2" height="5px">
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <img src="../images/icons/FlagRequired.gif" alt="*" /><b><convert>#label=814<convert><!--Fichiers topographiques--> :</b>
        </td></tr><tr><td class="field" colspan="2">
<?php if ($isNew == "True") { ?>
          <i><convert>#label=800<convert> <convert>#label=819<convert><!--Enregistrer le brouillon--> <!--pour pouvoir ajouter des fichiers--></i>
<?php } else { ?>
          <iframe src="request_<?php echo $_SESSION['language']; ?>.php?type=topo&amp;id=<?php echo $id; ?>" id="file_frame" name="file_frame" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="150px">
            <p>Your browser does not support iframes.</p>
          </iframe>
<?php } ?>
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><b><convert>#label=791<convert><!--Les entrées et réseaux liés à cette topographie--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="5" multiple="multiple" ondblclick="JavaScript:entryRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT CONCAT(ca.Id,'".$regForCat."','0') AS value, CONCAT(ca.Name,' [<convert>#label=119<convert>]') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_cave` ca ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_topo_cave` tc ON tc.Id_cave = ca.Id ";
          $sql .= "WHERE tc.Id_topography = ".$id." ";
          $sql .= "UNION ";
          $sql .= "SELECT CONCAT('0','".$regForCat."',ey.Id) AS value, CONCAT(ey.Name,' [<convert>#label=625<convert>]') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_topo_cave` tc ON tc.Id_entry = ey.Id ";
          $sql .= "WHERE tc.Id_topography = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} elseif ($preselected_entry_id != "") {
          $sql = "SELECT CONCAT(ca.Id,'".$regForCat."','0') AS value, CONCAT(ca.Name,' [<convert>#label=119<convert>]') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_cave` ca ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ce.Id_cave = ca.Id ";
          $sql .= "WHERE ce.Id_entry = ".$preselected_entry_id." ";
          $sql .= "UNION ";
          $sql .= "SELECT CONCAT('0','".$regForCat."',ey.Id) AS value, CONCAT(ey.Name,' [<convert>#label=625<convert>]') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ce.Id_entry = ey.Id ";
          $sql .= "WHERE ey.Id = ".$preselected_entry_id." AND ce.Id_cave IS NULL ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="add" name="add" value="<convert>#label=74<convert>" onclick="JavaScript:entryAdd();" /><!--Ajouter à la liste...-->
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="remove" name="remove" value="<convert>#label=73<convert>" onclick="JavaScript:entryRemove();" /><!--Retirer de la liste-->
        </td></tr>
      </table>
      </td><td style="vertical-align:top;">
        <?php include("request_diagram_".$_SESSION['language'].".php"); ?>
      </td></tr><tr><td colspan="2">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl" style="width:100%;">
        <tr><td class="label" colspan="2" style="text-align:left;">
          <img src="../images/icons/FlagRequired.gif" alt="*" /><b><convert>#label=820<convert><!--Auteurs des topographies--> :</b>
        </td></tr><tr><td class="field" colspan="2">
<?php if ($isNew == "True") { ?>
          <i><convert>#label=800<convert> <convert>#label=825<convert><!--Enregistrer le brouillon--> <!--pour pouvoir ajouter des autheurs--></i>
<?php } else { ?>
          <iframe src="request_<?php echo $_SESSION['language']; ?>.php?type=author&amp;id=<?php echo $id; ?>" id="author_frame" name="author_frame" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="150px">
            <p>Your browser does not support iframes.</p>
          </iframe>
<?php } ?>
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <b><convert>#label=835<convert><!--Options de publication--> :</b>
        </td></tr><tr><td width="300" class="label">
	      	<label for="n_request_is_public">
	      		<convert>#label=836<convert><!--Consultable par les inscrits uniquement-->
	      	</label>
        </td><td class="field">
          <input class="input1" type="checkbox" id="n_request_is_public" name="n_request_is_public" style="border:0px none;" <?php if($is_public=="NO"){echo "checked=\"checked\"";} ?> />
    		</td></tr><tr><td width="300" class="label">
	      	<label for="n_request_remove_north">
	      		<convert>#label=837<convert><!--Retirer l'indication du Nord-->
	      	</label>
        </td><td class="field">
          <input class="input1" type="checkbox" id="n_request_remove_north" name="n_request_remove_north" style="border:0px none;" <?php if($remove_north=="YES"){echo "checked=\"checked\"";} ?> />
    		</td></tr><tr><td width="300" class="label">
	      	<label for="n_request_remove_scale">
	      		<convert>#label=838<convert><!--Retirer l'échelle-->
	      	</label>
        </td><td class="field">
          <input class="input1" type="checkbox" id="n_request_remove_scale" name="n_request_remove_scale" style="border:0px none;" <?php if($remove_scale=="YES"){echo "checked=\"checked\"";} ?> />
    		</td></tr><tr><td width="300" class="label">
	      	<label for="n_request_distort_topo">
	      		<convert>#label=839<convert><!--Déformer la topographie-->
	      	</label>
        </td><td class="field">
          <input class="input1" type="checkbox" id="n_request_distort_topo" name="n_request_distort_topo" style="border:0px none;" <?php if($distort_topo=="YES"){echo "checked=\"checked\"";} ?> />
    		</td></tr><tr><td width="300" class="label">
          <label for="save">
	      	</label>
        </td><td class="field">
          <input type="hidden" id="e_list" name="e_list" />
          <input type="hidden" id="n_request_old_comments" name="n_request_old_comments" value="<?php echo $comments; ?>" />
          <input type="hidden" id="id_topography" name="id_topography" value="<?php echo $topography; ?>" />
          <input type="hidden" id="status_name" name="status_name" value="<?php echo $status_name; ?>" />
          <input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
      		<input type="hidden" id="request_id" name="request_id" value="<?php echo $id; ?>" />
<?php if ($read_write) {
        if (in_array($status_name, $draft_status_array)) { ?>
	        <input class="button1" type="submit" id="save" name="save" value="<convert>#label=800<convert>" onclick="JavaScript:saveOnClick();" /> <!--Enregistrer le brouillon-->
<?php   }
        if (in_array($status_name, $submit_status_array) && $isNew != "True") { ?>
	        <input class="button1" type="submit" id="send" name="send" value="<convert>#label=801<convert>" onclick="JavaScript:sendOnClick();" /> <!--Soumettre la demande au valideur-->
<?php   }
      }
      if (in_array($status_name, $approve_status_array)) { ?>
	        <input class="button1" type="submit" id="approve" name="approve" value="<convert>#label=803<convert>" onclick="JavaScript:approveOnClick();" /><br /><!--Approuver / Publier la demande-->
	        <input class="button1" type="submit" id="forward" name="forward" value="<convert>#label=805<convert>" onclick="JavaScript:forwardOnClick();" /> <!--Transmettre la demande-->
<?php }
      if (in_array($status_name, $reject_status_array)) {
        if ($status_name == 'approved') {
          $button_reject_label = "<convert>#label=313<convert>"; //Signaler du contenu illicite
        } else {
          $button_reject_label = "<convert>#label=804<convert>"; //Rejeter la demande
        }
        ?>
	        <input class="button1" type="submit" id="reject" name="reject" value="<?php echo $button_reject_label; ?>" onclick="JavaScript:rejectOnClick();" />
<?php } ?>
	      </td></tr><tr><td width="300" class="label">
          <label for="cancel">
	      	</label>
        </td><td class="field">
	        <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=793<convert><!--Le nom de la demande--> <convert>#label=874<convert><!--doit être composé de 2 à 100 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
            <sup>2</sup> <convert>#label=817<convert><!--Le nom du fichier--> <convert>#label=874<convert><!--doit être composé de 2 à 100 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
            <sup>3</sup> <convert>#label=827<convert><!--Le nom de l'auteur--> <convert>#label=875<convert><!--doit être composé de 2 à 70 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
      </td></tr></table>
    </form>
<?php
				  }
				}
			break;
    	case "topo":
        if (!allowAccess(request_edit_mine)) {
          exit();
        }
        include("../func/upload_restrictions.php");
?>
<?php
    $offset = 0;
		$columns_params = array();
    $sql = "SELECT DISTINCT ";
    $sql .= "T_file.Id AS `0`, ";
		$columns_params[] = "[hidden]|[hidden]Id";
    if (allowAccess(request_delete_mine) && $read_write) {
      $offset = $offset + 1;
      $sql .= "'../images/icons/delete.png' AS `1`, "; //Supprimer
			$columns_params[] = "[hidden]|<convert>#label=55<convert>";
    }
    $sql .= "T_file.Name AS `".(1 + $offset)."`, "; //Nom
		$columns_params[] = "T_file*Name|<convert>#label=767<convert>";
    $sql .= "T_file.Date_inscription AS `".(2 + $offset)."`, "; //Date d'ajout
		$columns_params[] = "[hidden]|<convert>#label=824<convert>";
    $sql .= "T_file.Path AS `".(3 + $offset)."` ";
		$columns_params[] = "[hidden]|[hidden]4";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_topography` ON T_topography.Id_request = T_request.Id ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_topo_file` ON J_topo_file.Id_topography = T_topography.Id ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_file` ON T_file.Id = J_topo_file.Id_file ";
    $sql .= "WHERE ";
    if (!allowAccess(request_view_all)) {
      $sql .= "T_request.Id_author = '".$_sess_user_id."' AND ";
    }
    $sql .= "T_request.Id = ".$id;
    
    $list_form = "result_form";

    $path_link = "JavaScript:openWindow('<Path>', '');";
    $delete_link = "JavaScript:deleteOnClick('".$list_form."', '<Id>');";
    $links = array (1 + $offset => array('conditions' =>  array(),
                                'parameters' => array('<Path>' => 3 + $offset),
                                'link' => $path_link,
                                'target' => '',
                                'title' => '<convert>#label=833<convert>')); //Voir la topographie
    $images = array();
    if (allowAccess(request_delete_mine) && $read_write) {
      $links[1] = array('conditions' =>  array(),
                        'parameters' => array('<Id>' => 0),
                        'link' => $delete_link,
                        'target' => '',
                        'class' => 'nothing',
                        'title' => "<convert>#label=55<convert>"); //Supprimer
      $images[1] = array('conditions' =>  array(),
                          'parameters' => array('<Src>' => 1),
                          'src' => '<Src>',
                          'class' => 'status1',
                          'alt' => 'X',
                          'style' => 'border:0px none;');
    }
    
    $input_type = array(
                'type' => '',
                'title' => '',
                'conditions' => array());
    $style = array();
    $default_order = 2 + $offset;
?>

<?php
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 10;
    $filter_form = "automatic_form";

    $default_array = array();
    
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "", $default_array, $images);
    $resource_id = $result['resource_id'];
    //$filter_fields = getFilterFields($sql,$_POST,$filter_form,"<convert>#label=542<convert>",false,$resource_id,$default_array);//Tous
//    mysql_free_result($resource_id);
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
    $current_page = (isset($_POST['current'])) ? $_POST['current'] : 1;
    $order = (isset($_POST['order'])) ? $_POST['order'] : '';
    $by = (isset($_POST['by'])) ? $_POST['by'] : $default_order;
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    } else {
      $navigator = "";
    }
    echo 'filesCount = '.$total_count.';'."\n";
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:topoOnLoad();">
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php
          if ($upload_error != "") {
?>
	  <div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><br />
      Error : <?php echo $upload_error; ?>
    <?php echo getBotBubble(); ?></div>
<?php
          }
?>

<?php if (allowAccess(request_edit_mine) && $read_write) { ?>
    <form id="upload_file" name="upload_file" method="post" action="../upload/topos/topo.php" enctype="multipart/form-data" onsubmit="JavaScript:addOnSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="300" class="label">
          <label for="topo_filename">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=818<convert><sup>2</sup><!--Nom et chemin du fichier-->
          </label>
		    </td><td class="field">
		      <input class="input1" type="text" id="topo_filename" name="topo_filename" value="" size="15" maxlength="100" onkeyup="JavaScript:checkThisName(this);" />
    		  <img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
		     	<input class="input1" type="file" id="filename" name="filename" value="<?php echo $_FILES['filename']; ?>" size="20" accept="image/*" />
          <input type="submit" class="button1" id="upload" name="upload" value="<convert>#label=586<convert>" /><!--Envoyer le fichier-->
          <input type="hidden" id="add_type" name="upload_type" value="add_topo" />
          <input type="hidden" id="target_name" name="target_name" value="u<?php echo $_sess_user_id; ?>_t<?php echo $topo_id; ?>_r<?php echo md5(uniqid(mt_rand())); ?>_" />
          <input type="hidden" id="add_source_manager" name="source_manager" value="../../html/request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&amp;uploaded=true&amp;id=<?php echo $id; ?>" />
		    </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <sup>2</sup> <?php echo '('.implode(', ', $upload_restrictions_ext_array['add_topo']).')'; ?> <?php echo round($upload_restrictions_size_array['add_topo']/1000, 2).'Ko.'; ?> <convert>#label=67<convert><!--Max.-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php } ?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>">
        <input type="hidden" name="current" value="" />
        <input type="hidden" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" name="by" value="<?php echo $by; ?>" />
      </form>
    </div>
    <?php if ($local_count >= $records_by_page) { ?>
    <div class="navigator">
      <?php echo $navigator; ?>
    </div>
    <?php } ?>
    <div>
      <form id="<?php echo $list_form; ?>" name="<?php echo $list_form; ?>" method="post" action="">
<?php if (allowAccess(request_delete_mine) && $read_write) { ?>
        <input type="hidden" id="delete_type" name="upload_type" value="delete_topo" />
        <input type="hidden" id="topo_file" name="topo_file" value="" />
        <input type="hidden" id="file_id" name="file_id" value="" />
        <input type="hidden" id="delete_source_manager" name="source_manager" value="../../html/request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&amp;deleted=true&amp;id=<?php echo $id; ?>" />
<?php } ?>
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php echo $rows; ?>
        </table>
        <div>
          <?php if ($total_count <= 0) { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </div>
        <div class="navigator">
          <?php echo $navigator; ?>
        </div>
      </form>
    </div>
<?php
			break;
    	case "author":
        if (!allowAccess(request_edit_mine)) {
          exit();
        }
        include("../func/upload_restrictions.php");
?>
<?php
		$columns_params = array();
    $offset = 0;
    $sql = "SELECT DISTINCT ";
    $sql .= "T_author.Id AS `0`, ";
		$columns_params[] = "[hidden]|[hidden]Id";
    if (allowAccess(request_delete_mine) && $read_write) {
      $sql .= "'../images/icons/delete.png' AS `1`, "; //Supprimer
      $offset = $offset + 1;
			$columns_params[] = "[hidden]|<convert>#label=55<convert>";
    }
    $sql .= "T_author.Name AS `".(1 + $offset)."`, "; //Auteurs des topographies
		$columns_params[] = "[hidden]|<convert>#label=820<convert>";
    $sql .= "T_author.Contact AS `".(2 + $offset)."`, "; //Contact (email, téléphone, etc…)
		$columns_params[] = "[hidden]|<convert>#label=821<convert>";
    $sql .= "IF(T_author.Creator_is_author='YES','<convert>#label=626<convert>','<convert>#label=627<convert>') AS `".(3 + $offset)."`, "; //Je suis l'autheur
		$columns_params[] = "[hidden]|<convert>#label=877<convert>";
    $sql .= "IF(T_author.Validated='YES','../images/icons/right.png','../images/icons/wrong.png') AS `".(4 + $offset)."`, "; //Validé
		$columns_params[] = "[hidden]|<convert>#label=842<convert>";
    $sql .= "IF(T_author.Validated='YES','<convert>#label=626<convert>','<convert>#label=627<convert>') AS `".(5 + $offset)."`, "; //Oui //Non
		$columns_params[] = "[hidden]|[hidden]5";
    $sql .= "T_author.Date_inscription AS `".(6 + $offset)."`, "; //Date d'ajout
		$columns_params[] = "[hidden]|<convert>#label=824<convert>";
    //$sql .= "GROUP_CONCAT(DISTINCT T_file.Name ORDER BY T_file.Name SEPARATOR '\\n') AS `[hidden]|<convert>#label=880<convert>`, "; //Pièces jointes
    $sql .= "T_file.Name AS `".(7 + $offset)."`, "; //Pièces jointes
		$columns_params[] = "[hidden]|<convert>#label=880<convert>";
    $sql .= "T_file.Path AS `".(8 + $offset)."` ";
		$columns_params[] = "[hidden]|[hidden]8";
    if (allowAccess(request_approve_all) && in_array($status_name, $approve_status_array)) {
      $sql .= ", IF(T_file.Id IS NULL, '../images/icons/add.png', '../images/icons/delete.png') AS `".(9 + $offset)."`, "; //Supprimer / Ajouter une pièce jointe
			$columns_params[] = "[hidden]|<convert>#label=878<convert>";
      $sql .= "IFNULL(T_file.Id, T_author.Id) AS `".(10 + $offset)."`, ";
			$columns_params[] = "[hidden]|[hidden]10";
      $sql .= "IF(T_file.Id IS NULL, 'add','delete') AS `".(11 + $offset)."` ";
			$columns_params[] = "[hidden]|[hidden]11";
    }
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_request` ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_topography` ON T_topography.Id_request = T_request.Id ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_topo_author` ON J_topo_author.Id_topography = T_topography.Id ";
    $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_author` ON T_author.Id = J_topo_author.Id_author ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_author_file` ON T_author.Id = J_author_file.Id_author ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_file` ON T_file.Id = J_author_file.Id_file ";
    $sql .= "WHERE ";
    if (!allowAccess(request_view_all)) {
      $sql .= "T_request.Id_author = '".$_sess_user_id."' AND ";
    }
    $sql .= "T_request.Id = ".$id." ";
    //$sql .= "GROUP BY T_author.Id";
    $list_form = "result_form";
    
    $path_link = "JavaScript:openWindow('<Path>', '');";
    $toggle_link = "JavaScript:toggleOnClick('".$list_form."', '<Id>');";
    $delete_link = "JavaScript:deleteOnClick('".$list_form."', '<Id>');";
    $att_link = "JavaScript:<Fct>AttOnClick('".$list_form."', '<Id>');";
    $links = array ();
    $images = array(4 + $offset => array('conditions' =>  array(),
                                  'parameters' => array('<Src>' => 4 + $offset, '<Alt>' => 5 + $offset),
                                  'src' => '<Src>',
                                  'class' => 'status1',
                                  'alt' => '<Alt>',
                                  'style' => 'border:0px none;'));
    if ($read_write) {
      if (allowAccess(request_delete_mine)) {
        $links[1] = array('conditions' =>  array(),
                          'parameters' => array('<Id>' => 0),
                          'link' => $delete_link,
                          'target' => '',
                          'class' => 'nothing',
                          'title' => "<convert>#label=55<convert>"); //Supprimer
        $images[1] = array('conditions' =>  array(),
                            'parameters' => array('<Src>' => 1),
                            'src' => '<Src>',
                            'class' => 'status1',
                            'alt' => 'X',
                            'style' => 'border:0px none;');
      }
      if (allowAccess(request_approve_all) && in_array($status_name, $approve_status_array)) {
        $links[5] = array('conditions' =>  array(),
                          'parameters' => array('<Id>' => 0),
                          'link' => $toggle_link,
                          'target' => '',
                          'title' => "<convert>#label=841<convert>"); //Changer l'état
        $links[9 + $offset] = array('conditions' =>  array(),
                          'parameters' => array('<Id>' => 10 + $offset, '<Fct>' => 11 + $offset),
                          'link' => $att_link,
                          'target' => '',
                          'class' => 'nothing',
                          'title' => "<convert>#label=878<convert>"); //Supprimer / Ajouter une pièce jointe
        $images[9 + $offset] = array('conditions' =>  array(),
                            'parameters' => array('<Src>' => 9 + $offset),
                            'src' => '<Src>',
                            'class' => 'status1',
                            'alt' => 'X',
                            'style' => 'border:0px none;');
      }
    }
    $links[7 + $offset] = array('conditions' =>  array(),
                      'parameters' => array('<Path>' => 8 + $offset),
                      'link' => $path_link,
                      'target' => '',
                      'title' => "<convert>#label=880<convert>"); //Pièces jointes
    $input_type = array('type' => '',
                        'title' => '',
                        'conditions' => array());
    $style = array();
    $default_order = 2 + $offset;
?>

<?php
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 10;
    $filter_form = "automatic_form";

    $default_array = array();
    
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "", $default_array, $images);
    $resource_id = $result['resource_id'];
//    mysql_free_result($resource_id);
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
    $current_page = (isset($_POST['current'])) ? $_POST['current'] : 1;
    $order = (isset($_POST['order'])) ? $_POST['order'] : '';
    $by = (isset($_POST['by'])) ? $_POST['by'] : $default_order;
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    } else {
      $navigator = "";
    }
    echo 'authorsCount = '.$total_count.';'."\n";
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:authorOnLoad();">
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php
          if ($upload_error != "") {
?>
	  <div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><br />
      Error : <?php echo $upload_error; ?>
    <?php echo getBotBubble(); ?></div>
<?php
          }
?>
<?php if (allowAccess(request_edit_mine) && $read_write) { ?>
    <form id="add_author" name="add_author" method="post" action="#" onsubmit="JavaScript:addOnSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="300" class="label">
					<label for="n_author_name">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=199<convert><!--Nom-->, <convert>#label=200<convert><!--Prénom--><sup>3</sup>
          </label>
		    </td><td class="field">
          <input class="input1" type="text" id="n_author_name" name="n_author_name" value="" size="30" maxlength="70" onkeyup="JavaScript:checkThisName(this);" />
    		  <img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
    		  <input class="input1" style="border:0px none;" type="checkbox" id="n_author_iam" name="n_author_iam" /> <convert>#label=877<convert><!--Je suis l'autheur-->
        </td></tr><tr><td width="300" class="label">
					<label for="n_author_contact">
	      		<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=821<convert><!--Contact (email, téléphone, etc…)-->
	      	</label>
        </td><td class="field">
            <input class="input1" type="text" id="n_author_contact" name="n_author_contact" value="" size="50" maxlength="70" />
            <input class="button1" type="submit" id="addAuthor" name="addAuthor" value="<convert>#label=360<convert>" /><!--Ajouter-->
        </td></tr>
      </table>
    </form>
<?php } ?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>">
        <input type="hidden" name="current" value="" />
        <input type="hidden" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" name="by" value="<?php echo $by; ?>" />
      </form>
    </div>
    <?php if ($local_count >= $records_by_page) { ?>
    <div class="navigator">
      <?php echo $navigator; ?>
    </div>
    <?php } ?>
    <div>
      <form id="<?php echo $list_form; ?>" name="<?php echo $list_form; ?>" method="post" action="">
<?php if (allowAccess(request_delete_mine) && $read_write) { ?>
        <input type="hidden" id="d_author_id" name="d_author_id" value="" />
<?php }
      if (allowAccess(request_approve_all) && in_array($status_name, $approve_status_array)) { ?>
        <input type="hidden" id="v_author_id" name="v_author_id" value="" />
        <input type="hidden" id="n_author_validated" name="n_author_validated" value="" />
        <input type="hidden" id="delete_type" name="upload_type" value="delete_attachment" />
        <input type="hidden" id="attachment_file" name="attachment_file" value="" />
        <input type="hidden" id="file_id" name="file_id" value="" />
        <input type="hidden" id="delete_source_manager" name="source_manager" value="../../html/request_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&amp;deleted=true&amp;id=<?php echo $id; ?>" />
<?php } ?>
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php echo $rows; ?>
        </table>
        <div>
          <?php if ($total_count <= 0) { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </div>
        <div class="navigator">
          <?php echo $navigator; ?>
        </div>
      </form>
    </div>
    <div id="upload_form" style="visibility:hidden;display:none;background-color:#F9F9F9;left:25%;position:fixed;text-align:center;top:10px;width:50%;border:1px solid #000000;padding:2px;">
      <?php echo getCloseBtn("JavaScript:hideId('upload_form');","<convert>#label=371<convert>"); ?>
      <form id="upload_file" name="upload_file" method="post" action="../upload/attachments/attachment.php" enctype="multipart/form-data" onsubmit="JavaScript:addAttOnSubmit(event);">
        <label for="attachment_filename">
          <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=818<convert><sup>4</sup><!--Nom et chemin du fichier-->
        </label>
	      <input type="text" class="input1" id="attachment_filename" name="attachment_filename" value="" size="15" maxlength="100" onkeyup="JavaScript:checkThisAttName(this);" />
  		  <img class="status1" name="name_pic_att" id="name_pic_att" src="../images/icons/wrong.png" alt="image" />
	     	<input type="file" class="input1" id="filename" name="filename" value="<?php echo $_FILES['filename']; ?>" size="20" accept="*" />
        <input type="submit" class="button1" id="upload" name="upload" value="<convert>#label=879<convert>" /> <!--Joindre-->
        <input type="button" class="button1" id="close_hide" name="close_hide" onclick="JavaScript:hideId('upload_form');" value="<convert>#label=371<convert>" /><!--Fermer--><br /><br />
        <div class="notes">
          <?php echo getTopBubble(); ?>
          <sup>4</sup> <?php echo '('.implode(', ', $upload_restrictions_ext_array['add_attachment']).')'; ?> <?php echo round($upload_restrictions_size_array['add_attachment']/1000, 2).'Ko.'; ?> <convert>#label=67<convert><!--Max.-->
          <?php echo getBotBubble(); ?>
        </div>
        <input type="hidden" id="add_type" name="upload_type" value="add_attachment" />
        <input type="hidden" id="target_name" name="target_name" value="u<?php echo $_sess_user_id; ?>_a<?php echo $attachment_id; ?>_r<?php echo md5(uniqid(mt_rand())); ?>_" />
        <input type="hidden" id="add_source_manager" name="source_manager" value="" />
      </form>
    </div>
<?php
			break;
    	default:
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:defaultOnload();">
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php echo getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>"); ?>
  	<div class="frame_title"><?php echo setTitle("#", "popup", "<convert>#label=80<convert>", 1); ?></div>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div>
<?php
    	break;
    }
?>
    <?php if ($type != "topo" && $type != "author") {
            echo getBotFrame(false);
          } ?>
<?php
    $virtual_page = "request/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>