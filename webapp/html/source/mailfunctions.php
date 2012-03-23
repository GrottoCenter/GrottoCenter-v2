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
function sendNewPwdMail($data, $password)
{
  $mail_dest = $data['Contact'];
  $subject = "<convert>#label=462<convert>";//Votre demande de nouveau mot de passe.
  $mail_body = "<p><b><convert>#label=463<convert> ".$_SESSION['Application_name']."</b></p>";//Vous avez demandé à recevoir un nouveau mot de passe sur 
  $mail_body .= "<u><convert>#label=464<convert> :</u><br />";//Veuillez respecter la casse
  $mail_body .= "<ul><li><b><convert>#label=465<convert> :</b> ".$password."</li>";//votre nouveau mot de passe
  $mail_body .= "<li><b><convert>#label=466<convert> :</b> ".$data['Login']."</li></ul>";//rappel de votre identifiant de connexion
  $mail_body .= "<p><b><convert>#label=467<convert> ".$_SESSION['Application_name'].", <convert>#label=468<convert>.</b></p>";//Une fois connecté sur //il est important que vous remplaciez ce mot de passe par un autre de votre choix en vous rendant dans vos 'Paramètres'
  $mail_body .= getSignature();
  sendMail($mail_dest,$subject,$mail_body,"","",false);
}

function sendActivationMail($contact, $login, $password, $id, $code)
{
  $login = stripslashes($login);
  $url = $_SESSION['Application_url']."/html/activation_".$_SESSION['language'].".php?type=auto&amp;i=".$id."&amp;c=".urlencode($code);
  $other_url = $_SESSION['Application_url']."/html/activation_".$_SESSION['language'].".php";
  $mail_dest = $contact;
  $subject = "<convert>#label=469<convert> ".$_SESSION['Application_name'];//Ouverture d'un compte sur
  $mail_body = "<p><convert>#label=470<convert>,<br />";//Bonjour
  $mail_body .= "<convert>#label=471<convert> ".$_SESSION['Application_name']."</p>";//Bienvenue sur le site
  $mail_body .= "<p><convert>#label=472<convert> :</p>";//Votre inscription a été reçu et prise en compte avec les informations de connections suivantes
  $mail_body .= "<ul><li><b><convert>#label=473<convert> :</b> <a href=\"mailto:".$contact."\">".$contact."</a></li>";//Mail
  $mail_body .= "<li><b><convert>#label=192<convert> :</b> ".$login."</li>";//Identifiant de connexion
  $mail_body .= "<li><b><convert>#label=193<convert> :</b> ".$password."</li></ul>";//Mot de passe
  $mail_body .= "<p><b><convert>#label=474<convert> :</b><br />";//Votre compte est inactif pour le moment, pour l'activer merci de cliquer sur le lien suivant (ou recopiez le tel quel dans votre navigateur)
  $mail_body .= "<a href=\"".$url."\" title=\"".$_SESSION['Application_name']."\">".$url."</a><br />";
  $mail_body .= "<convert>#label=781<convert> :</p>";//Si ce lien ne foncitonne pas, voici votre code d'activation
  $mail_body .= "<ul><li><b><convert>#label=778<convert> :</b> ".$code."</li>";//Code d'activation
  $mail_body .= "<li><b><convert>#label=782<convert> :</b> <a href=\"".$other_url."\" title=\"".$_SESSION['Application_name']."\">".$other_url."</a></li></ul>";//suivez ce lien
  $mail_body .= "<p><u><convert>#label=475<convert></u></p>";//Gardez cet email pour votre information
  //$mail_body .= "<convert>#label=476<convert><br /><br />";//Pour des raisons de sécurité votre mot de passe a été crypté et ne peut pas vous être envoyé
  //$mail_body .= "<convert>#label=477<convert><br /><br />";//Vous pouvez cependant demander un nouveau mot de passe (que vous pourrez modifier) si vous l'oubliez ou le perdez
  $mail_body .= getSignature();
  sendMail($mail_dest,$subject,$mail_body,"","",false);
}

function alertForCommentReply($comment_answered_id,$comment_answerer_id,$category,$id)
{
  $get_answered_sql = "SELECT cr.*,cm.Title FROM `".$_SESSION['Application_host']."`.`T_caver` cr INNER JOIN `".$_SESSION['Application_host']."`.`T_comment` cm on cr.Id = cm.Id_author WHERE cm.Id = ".$comment_answered_id;
  $get_answerer_sql = "SELECT cr.* FROM `".$_SESSION['Application_host']."`.`T_caver` cr INNER JOIN `".$_SESSION['Application_host']."`.`T_comment` cm on cr.Id = cm.Id_author WHERE cm.Id = ".$comment_answerer_id;
  $answerer_array = getDataFromSQL($get_answerer_sql, __FILE__, "function", __FUNCTION__);
  
  $link = $_SESSION['Application_url']."/html/file_Fr.php?lang=".$_SESSION['language']."&amp;check_lang_auto=false&amp;category=".$category."&amp;id=".$id;
  $subject = "<convert>#label=514<convert> : '".$answered_array[0]["Title"]."'."; //Réponse à votre commentaire
  $mail_dest = $answered_array[0]["Contact"];
  $mail_body = "<p><convert>#label=515<convert> '".$answered_array[0]["Title"]."'.<br />"; //Vous avez demandé à suivre les éventuelles réponse à votre commentaire
  $mail_body .= "<convert>#label=516<convert> "; //Si vous ne souhaitez plus recevoir de mail pour ce commentaire,
  $mail_body .= "<convert>#label=517<convert> '<convert>#label=513<convert>'.</p>"; //veuillez vous rendre sur le lien cité plus bas pour modifier votre commentaire en décochant la case //Je veux recevoir un e-mail lorsqu'une réponse m'est postée
  $mail_body .= "<p>".$answerer_array[0]["Nickname"]." <convert>#label=518<convert> '".$answered_array[0]["Title"]."'.<br />"; //a répondu à votre commentaire
  $mail_body .= "<convert>#label=519<convert> :<br />"; //Pour voir sa réponse, cliquez sur le lien suivant (ou recopiez le tel quel dans votre navigateur)
  $mail_body .= "<a href=\"".$link."\" title=\"".$_SESSION['Application_name']."\">".$link."</a></p>";
  $mail_body .= getSignature();
  sendMail($mail_dest,$subject,$mail_body,"","",false); //Don't send a copy of this mail to the webmaster (not necesary)
}

function getSignature()
{
  $signature = "<br /><br />";
  $signature .= "<convert>#label=478<convert> <a href=\"mailto:".$_SESSION['Application_mail']."\">".$_SESSION['Application_mail']."</a> <convert>#label=479<convert><br /><br />";//Pour toute question, merci d'écrire un mail à //en répondant à  ce courrier.
  $signature .= "<convert>#label=480<convert> ".$_SESSION['Application_name']." <convert>#label=481<convert><br />";//L'équipe de //vous remercie pour votre fidélité et pour votre confiance.
  $signature .= "<a href=\"".$_SESSION['Application_url']."\" title=\"".$_SESSION['Application_name']."\">".$_SESSION['Application_url']."</a>";
  return $signature;
}

function sendMessageToWM($admin_id,$from_mail,$real_from_mail,$from_name,$mail_subject,$txt_mail_body)
{
  $reception_body = "<p><convert>#label=227<convert> ".$from_name.",<br /><convert>#label=228<convert></p>";//Bonjour //Votre message a été envoyé.\nIl sera traité dans les meilleurs délais. 
  $reception_body .= "<p><convert>#label=229<convert> :</p>";//Votre message
  $reception_body .= "<hr /><p>";
  $reception_body .= stripslashes($txt_mail_body);
  $reception_body .= "</p><hr />";
  $reception_body .= getSignature();
  $reception_subject = "<convert>#label=229<convert> : ".$mail_subject;//Votre message
  //Send a msg to the admin :
  $mail_body = "<p><b>From : \"".$from_name."\" &lt;".$from_mail."&gt; ".$real_from_mail."<br />";
  $mail_body .= "Object : ".$mail_subject.".</b></p><p>".stripslashes($txt_mail_body)."</p>";
  if ($admin_id == "") {
    $mail_dest = getAdminContact();
  } else {
    $mail_dest = getContactForMessage($admin_id);
  }
  $mail_subject = "New message : ".$mail_subject;
  sendMail($mail_dest,$mail_subject,$mail_body,$from_mail);
  //Send a reception msg to the sender :
  sendMail($from_mail,$reception_subject,$reception_body,"","",false);
}

function sendRequestMail($request_id) {
  if ($request_id != "") {
    $request_sql = "SELECT T_label.".$_SESSION['language']." AS Status, T_status.Name AS Status_name, T_request.Name, ";
    $request_sql .= "T_caver_a.Contact AS Aut_contact, T_caver_a.Nickname AS Aut_nick,  ";
    $request_sql .= "T_caver_b.Contact AS Rec_contact, T_caver_b.Nickname AS Rec_nick  ";
    $request_sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
    $request_sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_request` ON T_request.Id = T_topography.Id_request ";
    $request_sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_status` ON T_status.Id = T_request.Id_status ";
    $request_sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_label` ON T_label.Id = T_status.Id_label ";
    $request_sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_caver` T_caver_a ON T_caver_a.Id = T_request.Id_author ";
    $request_sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`T_caver` T_caver_b ON T_caver_b.Id = T_request.Id_recipient ";
    $request_sql .= "WHERE T_request.Id = '".$request_id."' ";
    $request_data = getDataFromSQL($request_sql, __FILE__, "function", __FUNCTION__);
    $status_lbl = $request_data[0]['Status'];
    $request_name = $request_data[0]['Name'];
    $status_name = $request_data[0]['Status_name'];
    $rec_contact = $request_data[0]['Rec_contact'];
    $rec_name = $request_data[0]['Rec_nick'];
    $aut_contact = $request_data[0]['Aut_contact'];
    $aut_name = $request_data[0]['Aut_nick'];
    $copy = false;
    switch ($status_name) {
      case "submitted":
        $mail_dest = $rec_contact;
        $name_dest = $rec_name;
        $mail_from = $aut_contact;
        $name_from = $aut_name;
        $subject = "<convert>#label=847<convert> '".$request_name."' <convert>#label=849<convert>."; //La demande : //vous a été envoyée
        $temp_body = "<convert>#label=851<convert>"; //Veuillez vérifier les droits d'auteurs et appliquer les modifications nécessaires à la topographie. Ensuite vous pourez choisir de valider ou refuser la demande.
        break;
      case "rejected":
        $mail_dest = $aut_contact;
        $name_dest = $aut_name;
        $mail_from = $rec_contact;
        $name_from = $rec_name;
        $subject = "<convert>#label=846<convert> '".$request_name."' <convert>#label=848<convert> '".$status_lbl."'."; //Votre demande : //est passée à l'état :
        $temp_body = "<convert>#label=852<convert>"; //Votre demande a été rejetée, la cause du rejet peut être indiquée dans le champ commentaire de la demande. Veuillez modifier votre demande avant de la soumettre à nouveau.
        break;
      case "approved":
				trackAction("approve_request",$request_id,"T_request");
        $mail_dest = $aut_contact;
        $name_dest = $aut_name;
        $mail_from = $rec_contact;
        $name_from = $rec_name;
        $subject = "<convert>#label=846<convert> '".$request_name."' <convert>#label=848<convert> '".$status_lbl."'."; //Votre demande : //est passée à l'état :
        $temp_body = "<convert>#label=853<convert>"; //Votre demande a été accepté et est dès à présent en ligne, vous pouvez consulter les topographies sur GrottoCenter.org.
        break;
      case "canceled":
				trackAction("cancel_request",$request_id,"T_request");
        $mail_dest = $rec_contact;
        $name_dest = $rec_name;
        $mail_from = $aut_contact;
        $name_from = $aut_name;
        $subject = "<convert>#label=847<convert> '".$request_name."' <convert>#label=850<convert>."; //La demande : //a été signalée comme illicite
        $temp_body = "<convert>#label=851<convert>"; //Veuillez vérifier les droits d'auteurs et appliquer les modifications nécessaires à la topographie. Ensuite vous pourez choisir de valider ou refuser la demande.
        break;
    }
    $mail_body = "<p><convert>#label=470<convert> ".$name_dest.",<br />";//Bonjour
    $mail_body .= $subject."<br/>";
    $mail_body .= $temp_body."</p>";
    $mail_body .= "<ul><li><convert>#label=854<convert> ".$name_dest." ".$mail_dest."</li>";//Vous :
    $mail_body .= "<li><convert>#label=855<convert> ".$name_from." ".$mail_from."</li>";//Votre contact (Leader) :
    $mail_body .= "<li><convert>#label=861<convert>: ".$request_id."</li></ul>";//Numéro de la demande :
    $mail_body .= getSignature();
    return sendMail($mail_dest,$subject,$mail_body,"","",$copy);
  } else {
    return false;
  }
}
?>
