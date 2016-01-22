<?php
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <?php echo getMetaTags(); ?>
	  <!-- version IE //-->
	  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $_SESSION['Application_url']; ?>/favicon.ico" />
	  <!-- version standart //-->
	  <link rel="SHORTCUT ICON" type="image/png" href="<?php echo $_SESSION['Application_url']; ?>/favicon.png" />
    <title><?php echo $_SESSION['Application_name']; ?> - Bats</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Guidelines for cavers regarding bat conservation</h1>
    <div>
	   The exploration of caves sheltering bats usually implies some degree of disturbance for the animals. However, this disturbance can be clearly reduced by responsible practices.
Here are some guidelines on how to act according to the situation:
      <h2>You are   preparing the underground trip</h2>
      <p>Then check the cave file on www.grottocenter.org * to see whether the presence of bats is mentioned. If bats are present in the cave, you should adapt the number of cavers and the chosen period. In doubt, get in touch with the regional bat correspondent.</p>
    </div>
    <div>
      <h2>You are at the cave entrance and find an information sign mentioning the presence of bats:</h2>
      <p>you should then choose to abort the exploration and turn to another cave if     this happens to be in one of the periods when bats are significantly present.
 </p>
    </div>
    <div>
      <h2>You are in the midst of an exploration and meet a colony of a large group of bats (a few dozens, up to more than 1,000):</h2>
      <p>JYou should then turn around whatever the season. The panic within a colony could take a dramatic turn. In the summer period, it can lead to the failure of mating, a major problem for animals which bear only one pup a year.
Repeated alerts in winter may eventually cause the animals’ death: their energy reserves may become exhausted with each new alert.
      <ul>
        <li>Please do inform the regional bat correspondent</li>
        <li>You might return at a later period, the presence of the bats being seasonal.
</li>
      </ul>
	  </p>

    </div>
    <div>
      <h2>You are exploring and find some isolated bats</h2>
	    you may go on exploring and must comply with the following guidelines:

      <ul>
        <li>Do not linger in the “inhabited” zone</li>
        <li>Do not stay right under the bat, even if not using a naked flame (the heat released goes up). </li>
		    <li>Do use electric lighting on low settings and direct your light towards the ground while crossing the “inhabited” zone. Note that red light is significantly less disturbing for the bats.</li>
		    <li>Limit the noise level as much as possible while moving in their clearly perceptible environment (metallic rattling, sound of footsteps, etc).</li>
		    <li>In the narrow passages, double check the presence of bats.</li>
        <li>Finally one imperative rule to respect: if they start to move or that they bend their legs, then MOVE away as fast as possible and in silence so that they regain their calm.</li>
        <li>If the number of bats is rather high (at least ten), make sure you inform the regional bat correspondent.</li>
      </ul>
    </div>
    <div>
      <h2>You are about to blow up obstructions or remove rocks</h2>
      <p>The release of noxious fumes related to the use of explosives, the detonations, and the modification of the physical integrity of a cave can result in the death of the bats or the desertion of the site.
You should make sure there are no bats on the site when explosives are being used.</p>
    </div>
    <div>
      <h2>List of useful websites and contacts in France : </h2>
      <p><a href="http://www.sfepm.org/groupeChiropteres.htm" target="_blanck">http://www.sfepm.org/groupeChiropteres.htm</a></p>
    </div>
	<hr>
	<center><img src="../images/icons/gclr.jpg"><img src="../logo.svg" height="80"></center>
	</br>
  <center>
  	*Grottocenterest a collaborative database dedicated to speleology, data is supplied by cavers on the principle of the wiki. The tool is managed by Wikicaves association.</br>
    Wikicaves and the GCLR work in partnership to educate cavers on underground environments , the impact of caving and to improve bat conservation.</br>
    This charter was drafted by the GCLR on the basis of a document of Claude Milhas.
  </center>
  <?php include_once "../func/suivianalytics.php" ?>
  </body>
</html>
