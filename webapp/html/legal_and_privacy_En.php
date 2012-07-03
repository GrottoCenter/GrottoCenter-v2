<?php
  	include("../conf/config.php");
		include("../func/function.php");
	  include("declaration.php");
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
<?php
		include("application_".$_SESSION['language'].".php");
		include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
	  <!-- version IE //-->
	  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $_SESSION['Application_url']; ?>/favicon.ico" />
	  <!-- version standart //-->
	  <link rel="SHORTCUT ICON" type="image/png" href="<?php echo $_SESSION['Application_url']; ?>/favicon.png" />
    <title><?php echo $_SESSION['Application_name']; ?> <convert>#label=729<convert> Legal and Privacy Statement</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Legal and Privacy Statement</h1>
    <div>
      <h2>Legality</h2>
      <p>This site is intended to collect and provide information about caving and cavers.<br />
      The information provided on this site are not contractual and can be modified.</p>
      <p>This site contains text, logos and images that can be protected by copyright or other intellectual property rights.</p>
      <p>No responsibility will be taken by <?php echo $_SESSION['Application_name']; ?> for the links to other sites from this web site.</p>
    </div>
    <div>
      <h2>Collection and use of personal information</h2>
      <h3>Contribution rules</h3>
      <p>You agree not to post any abusive, libelous, threatening, xenophobic or 
      to incite hatred, or any other material that may violate any applicable laws.<br />
      Doing so may lead you to be banned immediately and permanently (and your
      Internet service provider will be informed). The IP address and time of
      each message is recorded to aid in enforcing these conditions.</p>
    </div>
    <div>
      <h3>Moderation and responsibility of the team <?php echo $_SESSION['Application_name']; ?></h3>
      <p>The moderation team will attempt to keep order in the site, and if necessary, 
      remove or edit any generally objectionable messages as quickly as possible.</p>
      
      <p>When you join <?php echo $_SESSION['Application_name']; ?>, you agree that
      the webmaster, administrator and moderators of this site have the right to
      delete, edit, move or lock any topic at any time. However, despite all their
      efforts, it is impossible for them to review all messages.</p>
      
      <p>Messages posted on this site express the views and opinions of their respective 
      authors, and not the administrators, moderators or webmaster (except for 
      posts by these people) and you agree that those can not therefore be held 
      responsible in the event of a message does not respect the rules of use of 
      the site, and not yet moderate.<br />
      A contact section is available, and allow anyone to report any "out of rules" content.</p>
    </div>
    <div>
      <h3>Collection and use of personal information</h3>
      <p>These information are collected by <?php echo $_SESSION['Application_name']; ?> 
      for administrative purposes, for the proper functioning of the community, and to offer 
      you the opportunity to mark your individuality within the community <?php echo $_SESSION['Application_name']; ?> -
      no commercial use would be made, and these information will never be transferred 
      or sold to any third party, commercial or not.</p>
      
      <p>When you register, some personal information are collected by 
      <?php echo $_SESSION['Application_name']; ?>. The mandatory information 
      collected during registration are as follows and may have to evolve :
      </p>
      <ul>
        <li>your username (nickname)</li>
        <li>a valid e-mail address</li>
      </ul>
      
      <p><?php echo $_SESSION['Application_name']; ?> can allow you to fill a few
      optional personal information through your user profile :
      </p>
      <ul>
        <li>your direction</li>
        <li>your hobbies</li>
        <li>your birth date</li>
        <li>etc.</li>
      </ul>
    </div>
    <div>
      <h3>CNIL: access to your personal data</h3>
      <p>For information on the protection of personal data, you can consult the 
      "Commission Informatique et Liberté" website (<a href="http://www.cnil.fr" target="_blank">www.cnil.fr</a>).</p>
      
      <p>In all cases, and in accordance with the French legislation and more particularly 
      the law of January 6, 1978 Science and freedom, you have a right of access, rectification, 
      opposition and suppression on these data that you can exercise at any time, 
      editing or deleting your profile by clicking on "Manage account", shown in the "Tools" menu 
      after identification.</p>
      
      <p>If you have any questions or problems, please contact the administrator.</p>
      
      <p>These information will be disclosed to any third party without your consent,
      in accordance with this Legal And Privacy Statement, which is deemed to be approved during registration.</p>
      
      <p>The webmaster, administrator and moderators can not be held responsible 
      for any hacking attempt led to the access and dissemination of these data, 
      and will do their utmost to make the people respect the terms of this Legal And Privacy Statement.</p>
    </div>
    <div>
      <h2>Modification of this Legal And Privacy Statement</h2>
      <p><?php echo $_SESSION['Application_name']; ?> reserves the right to amend this Legal And Privacy Statement at any time.
      Any changes to the rules of <?php echo $_SESSION['Application_name']; ?> on the protection of privacy 
      will be integrated into the present Legal And Privacy Statement, and renowned known and accepted.
      This Legal And Privacy Statement applies to all services provided by <?php echo $_SESSION['Application_name']; ?>.</p>
    </div>
    <div>
      <h2>Tacit acceptance of these terms</h2>
      <p>The visitor of <?php echo $_SESSION['Application_name']; ?>, anonymous or registered, 
      recognizes, by its use of various services offered by the site, have read 
      and accepted these conditions.</p>
    </div>
<?php
$virtual_page = "legal_and_privacy/Fr";
include_once "../func/suivianalytics.php" ?>
  </body>
<!--
http://validator.w3.org/check?uri=http%3A%2F%2Fclementronzon.free.fr%2Fgrottocenter%2Fhtml%2Flegal_and_privacy_Fr.php%3Flang%3DEn;ss=1;outline=1
  <p>
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10-blue"
        alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
  </p>
-->
</html>