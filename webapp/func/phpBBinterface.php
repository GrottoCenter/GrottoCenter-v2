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
 * @copyright Copyright (c) 2009-1912 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
define('IN_PHPBB', true);
$phpbb_root_path = '../phpBB3/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
if (!is_dir($phpbb_root_path)) {
  define('NO_PHPBB_INSTALLED', true);
} else {
  include($phpbb_root_path . 'common.' . $phpEx);
  include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);

  // Start session management
  $user->session_begin();
  $auth->acl($user->data);
  $user->setup();
  
  if (isset($_POST['connection'])){
    $loginn = (isset($_POST['l_caver_login'])) ? $_POST['l_caver_login'] : '';
    $passwordd = (isset($_POST['l_caver_password'])) ? $_POST['l_caver_password'] : '';
    $keyy = (isset($_POST['l_key'])) ? $_POST['l_key'] : '';
    $passwordd = crypt_xor($passwordd, $keyy);
    $result = $auth->login(stripslashes($loginn), $passwordd);
  }
}

function addphpBBuser($login,$pwd,$mail,$lang) {
  $cryptpass = phpbb_hash($pwd);
  $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`forum_users` WHERE `username` = '".$login."'";
  $dataset_exists = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if ($dataset_exists['Count'] == 0) {
  	$new_user_array = array(
  		'username'			=> stripslashes($login),
  		'user_password'	=> $cryptpass,
  		'user_email'		=> $mail,
  		'group_id'			=> 7,
  		'user_type'			=> 0,
  		'user_lang'     => strtolower($lang),
  	);
    $phpBBid = user_add($new_user_array);
  }
}

function chgPwdphpBBuser($nick, $pwd) {
  $cryptpass = phpbb_hash($pwd);
  $sql = "UPDATE `".$_SESSION['Application_host']."`.`forum_users` ";
  $sql .= "SET `user_password` = '".$cryptpass."' ";
  $sql .= "WHERE `username` = '".$nick."'";
  echo "<!--".$sql."-->";
  $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
}
?> 
