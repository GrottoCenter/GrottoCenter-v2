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
    <title><?php echo $_SESSION['Application_name']; ?> <convert>#label=729<convert> Mentions légales et Charte de confidentialité</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Mentions légales et Charte de confidentialité</h1>
    <div>
      <h2>Mentions légales</h2>
      <p>Ce site est destiné à recueillir et fournir des informations sur la spéléologie
      et sur leurs pratiquants.<br />
      Les informations communiquées sur ce site ne sont pas contractuelles et peuvent
      faire l'objet de modifications.</p>      
      <p>Ce site contient du texte, des logos et des images pouvant être protégés par les
      lois de copyright ou d'autres droits de propriété intellectuelle.</p>
      <p>Le site Web peut contenir des liens vers des sites tiers n'engageant aucunement
      la responsabilité de <?php echo $_SESSION['Application_name']; ?>.</p>
    </div>
    <div>
      <h2>Données personnelles collectées</h2>
      <h3>Netiquette et règles de participation</h3>
      <p>Vous consentez à ne pas poster de messages injurieux, diffamatoires, menaçants,
      à caractère xénophobe ou de nature à inciter à la haine, ou tout autre message
      qui violerait les lois applicables.<br />
      Le faire peut vous conduire à être banni immédiatement, et de façon permanente
      (et votre fournisseur d'accès à internet en sera informé). L'adresse IP et
      l'heure de chaque message est enregistrée afin d'aider à faire respecter ces
      conditions.</p>
    </div>
    <div>
      <h3>Modération et responsabilité de l'équipe de <?php echo $_SESSION['Application_name']; ?></h3>
      <p>L'équipe de modération de ce site s'efforcera de tenir en ordre le site,
      et si besoin est, de supprimer ou éditer tous les messages à caractère
      répréhensible, et ce, aussi rapidement que possible.</p>
      
      <p>En vous inscrivant à <?php echo $_SESSION['Application_name']; ?>, vous acceptez le fait que le webmestre,
      l'administrateur et les modérateurs de ce site ont le droit de supprimer,
      éditer, déplacer ou verrouiller n'importe quel sujet de discussion à tout moment
      et que, malgré tous leurs efforts, il leur est toutefois impossible de passer
      en revue tous les messages.</p>
      
      <p>Les messages postés sur ce site expriment la vue et opinion de leurs auteurs
      respectifs, et non pas celle des administrateurs, ou modérateurs, ou webmestres
      (excepté les messages postés par eux-même) et vous admettez que ceux ci ne peuvent
      par conséquent être tenus pour responsables en cas de message ne respectant pas
      les règles d'utilisation du site, et non encore modéré.<br />
      Une fonction visant à alerter les équipes de modération sur la teneur d'un message
      est disponible, et permettra à quiconque trouverait un message "hors charte" et
      non encore modéré, de le signaler aux responsables.</p>
    </div>
    <div>
      <h3>Collecte et utilisation des informations</h3>
      <p>Ces informations sont recueillies par <?php echo $_SESSION['Application_name']; ?> à des fins administratives,
      pour le bon fonctionnement de la communauté, ainsi que pour vous offrir la possibilité
      de marquer votre individualité au sein de la communauté <?php echo $_SESSION['Application_name']; ?> -
      aucune utilisation commerciale n'en sera faite, et ces informations ne seront
      jamais cédées ou vendues à aucun organisme tiers, commercial ou pas.</p>
      
      <p>Lors de votre inscription, certaines informations personnelles sont recueillies
      par <?php echo $_SESSION['Application_name']; ?>. Les informations obligatoires recueillies lors de l'inscription
      sont les suivantes et peuvent être amenées à évoluer :
      </p>
      <ul>
        <li>Votre identifiant d'utilisateur (pseudonyme)</li>
        <li>Une adresse e-mail valide</li>
      </ul>
      
      <p><?php echo $_SESSION['Application_name']; ?> peut vous permettre de renseigner quelques informations personnelles
      facultatives par l'intermédiaire de votre profil d'utilisateur :
      </p>
      <ul>
        <li>votre adresse</li>
        <li>vos loisirs</li>
        <li>votre âge</li>
        <li>etc.</li>
      </ul>
    </div>
    <div>
      <h3>CNIL : accès et droit de regard sur vos données personnelles</h3>
      <p>Pour toute information sur la protection des données personnelles, vous pouvez
      consulter le site de la Commission Informatique et Liberté (<a href="http://www.cnil.fr" target="_blank">www.cnil.fr</a>).</p>
      
      <p>Dans tous les cas, et conformément à la législation française en vigueur et plus
      particulièrement à la loi du 6 janvier 1978 Informatique et liberté, vous
      disposez d'un droit d'accès, de rectification, d'opposition et de suppression
      sur ces données que vous pouvez exercer à tout moment, en éditant ou supprimant
      votre profil, en cliquant sur le lien "Mes paramètres", affiché dans le menu "outils"
      après votre identification.</p>
      
      <p>En cas de question ou de difficultés, contactez l'administrateur du site.</p>
      
      <p>Ces informations ne seront divulguées à aucune tierce personne ou société sans
      votre accord, conformément à cette Charte de Confidentialité, laquelle est
      réputée approuvée lors de votre inscription.</p>
      
      <p>Le webmestre, l'administrateur, et les modérateurs ne peuvent pas être tenus
      pour responsables si une tentative de piratage informatique conduit à l'accès
      et à la diffusion de ces données, et feront tout leur possible pour faire
      respecter la confidentialité de vos informations personnelles.</p>
    </div>
    <div>
      <h2>Modifications des mentions légales</h2>
      <p><?php echo $_SESSION['Application_name']; ?> se réserve le droit de modifier cette charte à tout moment.
      Toute modification des règles de <?php echo $_SESSION['Application_name']; ?> sur la protection de la vie privée
      sera intégrée dans la présente charte, et réputée connue et acceptée.
      Cette charte s'applique à tous les services de <?php echo $_SESSION['Application_name']; ?>.</p>
    </div>
    <div>
      <h2>Acceptation tacite des présentes mentions</h2>
      <p>Le visiteur de <?php echo $_SESSION['Application_name']; ?>, anonyme ou inscrit, reconnaît, par son utilisation
      des différents services proposés par le site, avoir lu et accepté les présentes
      conditions d'utilisation.</p>
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