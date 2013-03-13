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
    <title><?php echo $_SESSION['Application_name']; ?> <convert>#label=729<convert> Nutzungsbedingungen und Datenschutz</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Nutzungsbedingungen und Datenschutz</h1>
    <div>
      <h2>Nutzungsbedingungen</h2>
      <p>Diese Seite dient der Bereitstellung von Informationen zur Höhlenforschung und
      Höhlenforschern.<br />
      Die veröffentlichten Informationen sind nicht verbindlich, sie können jeder Zeit
      verändert werden.</p>      
      <p>Texte, Logos und Bilder auf dieser Seite können durch Copyright oder anderweitig
      geschützt sein.</p>
      <p><?php echo $_SESSION['Application_name']; ?> ist nicht für 
      den Inhalt verlinkter Internetseiten verantwortlich.</p>
    </div>
    <div>
      <h2>Persönliche Daten</h2>
      <h3>Netetikette</h3>
      <p>Du erklärst dich damit einverstanden keine beleidigenden, diffamierenden,
      bedrohenden oder sonst welche Nachrichten, die die Rechte anderer verletzen 
      bzw. gegen geltendes Recht verstoßen, zu veröffentlichen.<br />
      Verstößt du dagegen führt das zur sofortigen Sperrung deines Zugangs, dein
      Internetprovider wird informiert. Die IP-Adresse sowie die Uhrzeit jedes
      Beitrages werden gespeichert, um diesen Regeln Nachdruck zu verleihen.
    </p>
    </div>
    <div>
      <h3>Moderation und Verantwortlichkeit der Mannschaft von <?php echo $_SESSION['Application_name']; ?></h3>
      <p>Die Moderatoren dieser Seite sind bemüht, die Übersicht und Ordnung zu
erhalten. Wenn erforderlich, werden unpassende Beiträge schnellstmöglich
editiert bzw. gelöscht.</p>
      
      <p>Mit der Anmeldung bei <?php echo $_SESSION['Application_name']; ?> akzeptierst
du, dass Administratoren und Moderatoren das Recht haben, deine Beiträge zu löschen, zu
verschieben oder anderweitig zu verändern. Trotz größten Bemühens ist es uns nicht immer
möglich sämtliche Beiträge auf ihren Inhalt hin zu überprüfen.</p>
      
      <p>Die veröffentlichten Texte auf dieser Seite drücken die Sichtweise und Meinung
des Einzelnen aus, nicht die der Administratoren bzw. Moderatoren. Damit sind Moderatoren
und Administratoren nicht für Beiträge verantwortlich, die obige Regeln missachten.<br />
      Eine Funktion zum Melden von Beiträgen ist verfügbar und ermöglicht jedem Nutzer
      unangebrachte Texte den Verantwortlichen zu melden.</p>
    </div>
    <div>
      <h3>Sammlung und Verwendung von Informationen</h3>
      <p>Die von <?php echo $_SESSION['Application_name']; ?> empfangenen Daten werden zu
      aministrativen Zwecken verwendet, für ein gutes Funktionieren der Webseite und um dir
      die Möglichkeit zu geben, dir innerhalb der Gemeinschaft von <?php echo $_SESSION['Application_name']; ?> 
      deine Individualität zu bewahren - es wird keine kommerzielle Verwendung deiner 
      Daten geben, die Informationen werden nicht an Dritte weitergegeben.</p>
      
      <p>Bei deiner Einschreibung werden gewisse persönlich Informationen von <?php echo $_SESSION['Application_name']; ?>
      empfangen. Die bei der Anmeldung obligatorischen Daten sind die folgenden
      (Liste kann erweitert werden) :
      </p>
      <ul>
        <li>Dein Benutzername (Pseudonym)</li>
        <li>Eine gültige Emailadresse</li>
      </ul>
      
      <p><?php echo $_SESSION['Application_name']; ?> ermöglicht dir die Eingabe weiterer persönlicher
      Daten:
      </p>
      <ul>
        <li>deine Adresse</li>
        <li>Freizeitbeschäftigung</li>
        <li>dein Alter</li>
        <li>etc.</li>
      </ul>
    </div>
    <div>
      <h3>Recht auf Auskunft über die gespeicherten Daten</h3>
      <p>Für jegliche Information zum Datenschutz kannst du folgende Seite besuchen: 
      Commission Informatique et Liberté (<a href="http://www.cnil.fr" target="_blank">www.cnil.fr</a>).</p>
      
      <p>In jedem Fall besitzt zu - komform zur aktuellen französischen Gesetzgebung
      (Gesetz von 6. Januar 1978 Informatik und Freiheit) ein Recht zum Zugang, der
      Berichtigung und zum Löschen deiner persönlichen Daten. Dieses Recht kannst du
      jederzeit ausüben, indem du dein Profil editierst oder löschst, über den Link
      "Meine Einstellungen", der im Menü "Werkzeuge" angezeigt wird.</p>
      
      <p>Bei Fragen oder Problemen wende dich bitte an den Administrator.</p>
      
      <p>Alle diese Informationen werden ohne deine Zustimmung an keinen Dritten weitergegeben,
      konform zu dieser Datenschutzerklärung, die du bei deiner Einschreibung anerkennst.</p>
      
      <p>Der Webmaster, der Administrator und die Moderatoren können in keinem Falle für Angriffe
      auf die Website - und die damit verbundene Verbreitung deiner Daten - verantwortlich gemacht
      werden. Trotzdem sind sie bemüht den Datenschutz bestmöglich zu gewährleisten.</p>
    </div>
    <div>
      <h2>Änderung der Nutzungsbedingungen</h2>
      <p><?php echo $_SESSION['Application_name']; ?> behält sich das Recht vor, die vorliegenden
      Nutzungsbedingungen jederzeit zu ändern. Jede Änderung der Datenschutzregeln von 
      <?php echo $_SESSION['Application_name']; ?>  wird in diesem Dokument vorgenommen, damit
      als bekannt und akzeptiert vorausgesetzt.
      Die Nutzungsbedingungen sind auf alle von <?php echo $_SESSION['Application_name']; ?> 
      bereitgestellten Services anzuwenden.</p>
    </div>
    <div>
      <h2>Stillschweigende Akzeptierung der vorliegenden Regeln</h2>
      <p>Der Besucher von <?php echo $_SESSION['Application_name']; ?>, anonym oder angemeldet,
      kennt durch seine Nutzung der unterschiedlichen Service dieser Seite die Nutzungsbedingungen,
      hat diese gelesen und akzeptiert.</p>
    </div>
<?php
$virtual_page = "legal_and_privacy/Fr";
include_once "../func/suivianalytics.php" ?>
  </body>
</html>