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
    <title><?php echo $_SESSION['Application_name']; ?> - Chauves souris</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Charte de bonne conduite du spéléologue en présence de chauves­ souris</h1>
    <div>
	    La fréquentation de cavités abritant des chauves-souris implique généralement un dérangement des animaux. Cependant, ce dérangement peut être nettement réduit par des pratiques responsables envers les chauves-souris. Voici comment réagir selon les cas :
      <h2>Je prépare une sortie dans une cavité</h2>
      <p>Je consulte la fiche cavité sur www.grottocenter.org* et vérifie la présence ou non de chauve-souris. Si des chauves-souris sont présentes, j'adapte le nombre de pratiquants et la période de pratique aux enjeux du site. En cas de doute, je prends contact avec le correspondant chiroptères local.</p>
    </div>
    <div>
      <h2>J'arrive à l'entrée d'une cavité équipée d'un panneau signalant la présence de chiroptères  :</h2>
      <p>je fais demi-tour si je suis dans une des périodes sensibles où elles sont présentes. </p>
    </div>
    <div>
      <h2>Je suis en exploration et rencontre une colonie de chiroptères (groupe de quelques dizaines à plus de 1000 ) : </h2>
      <p>Je fais demi-tour quelle que soit la saison. En effet, l'affolement dans une colonie peut être dramatique. En période estivale, il peut conduire à l'échec de la reproduction, ce qui est grave pour ces animaux qui n'ont qu'un seul petit par an. Des réveils répétés en hiver mettent l'animal en danger de mort : ses réserves énergétiques s'épuisent à chaque nouveau réveil.
      <ul>
        <li>J'avertis le correspondant chiroptères local.</li>
        <li>Je peux revenir à une autre saison, la présence des chiroptères étant saisonnière. </li>
      </ul>
	  </p>

    </div>
    <div>
      <h2>Je suis en exploration et rencontre des chauves-souris isolées :</h2>
	    Je poursuis mon exploration en respectant les règles suivantes :
      <ul>
        <li>Je ne m'attarde pas dans la zone “habitée”,</li>
        <li>Je ne stationne pas à la verticale directe de la chauve-souris, même sans lampe acétylène (la chaleur dégagée par le corps monte). </li>
		    <li>J'utilise un éclairage électrique de faible puissance et j'éclaire au sol le temps de franchir la zone « habitée ». La lumière rouge est significativement moins dérangeante pour les chauves-souris.</li>
		    <li>Je limite le plus possible les bruits et la parole lors de ma progression dans leur environnement perceptible (cliquetis métalliques, bruits de pas sur les éboulis, etc.). </li>
		    <li>Dans les passages étroits, je redouble de vigilance pour détecter la présence de chiroptères.</li>
        <li>Enfin règle impérative à respecter : si elles se mettent à bouger ou qu'elles fléchissent leur pattes, alors je m'éloigne au plus vite et en silence afin qu'elles retrouvent leur quiétude. </li>
        <li>Si le nombre de chiroptères est assez élevé (au moins une dizaine), j'informe le correspondant chiroptères local. </li>
      </ul>
    </div>
    <div>
      <h2>Je conduis des chantiers de désobstruction</h2>
      <p>L'emploi d'explosifs dégageant des gaz toxiques, les détonations, la modification de l'intégrité physique d'une grotte peuvent entraîner la mort des chauve-souris ou une désertion du site. Il est nécessaire de vous assurer de l'absence de chiroptères sur les sites ou de tels chantiers sont réalisés. </p>
    </div>
    <div>
      <h2>Coordonnées des correspondants chiroptères en France : </h2>
      <p><a href="http://www.sfepm.org/groupeChiropteres.htm" target="_blanck">http://www.sfepm.org/groupeChiropteres.htm</a></p>
    </div>
	<hr>
	<center><img src="../images/icons/gclr.jpg"><img src="../logo.svg" height="80"></center>
	</br>
  <center>
  	Grottocenter est une base de données communautaire dédiée à la spéléologie, alimentée par les spéléologues sur le principe du wiki. L'outil est administré par l'association Wikicaves.</br>
    Wikicaves et le GCLR travaillent en partenariat pour sensibiliser les utilisateurs du milieu souterrain à l'impact de leur pratique et améliorer la protection des chiroptères.</br>
    La présente charte a été rédigée par le GCLR, sur la base d'un document de Claude Milhas.
  </center>
  <?php include_once "../func/suivianalytics.php" ?>
  </body>
</html>
