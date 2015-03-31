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
    <title><?php echo $_SESSION['Application_name']; ?> Juridische en Bescherming Persoonlijke Levenssfeer Verklaring</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Juridische en Bescherming Persoonlijke Levenssfeer Verklaring</h1>
    <div>
      <h2>Wettigheid</h2>
      <p>Deze site is bedoeld voor het verzamelen en verstrekken van informatie over speleologie en over speleologen.<br />
      De informatie op deze site is niet bindend en kan worden gewijzigd.</p>
      <p>Deze site bevat tekst, logo's en beelden die kunnen worden beschermd door het auteursrecht of andere intellectuele eigendomsrechten.</p>
      <p><?php echo $_SESSION['Application_name']; ?> neemt geen verantwoordelijkheid voor de links naar andere websites.</p>
    </div>
    <div>
      <h2>Verzamelen en gebruiken van persoonlijke gegevens</h2>
      <h3>Bijdrage regels</h3>
      <p>U gaat ermee akkoord geen inhoud te posten die kwetsend, lasterlijk, bedreigend, xenofoob of tot haat aanzet  of in strijd is met toepasselijke wetten.<br />
      Dit kan leiden tot onmiddellijk en permanent te worden verbannen van de website (en uw internetprovider wordt geïnformeerd). Het IP-adres en tijd van elk bericht wordt opgeslagen.</p>
    </div>
    <div>
      <h3>Moderator en verantwoordelijkheid van het team <?php echo $_SESSION['Application_name']; ?></h3>
      <p>Het moderator team draagt zorg voor het respecteren van de bijdrageregels en zal indien nodig, aanstootgevende inhoud en berichten zo snel mogelijk verwijderen.</p>
      
      <p>Als geregistreerd lid van <?php echo $_SESSION['Application_name']; ?>, gaat u ermee akkoord dat de webmaster, beheerder en moderators van deze site het recht hebben om elk onderwerp op elk gewenst moment te verwijderen, te bewerken, te verplaatsen of te vergrendelen. Echter, ondanks al hun inspanningen, is het voor hen onmogelijk om alle berichten te bekijken en te beoordelen.</p>
      
      <p>Berichten die op deze site worden geplaatst zijn de visies en meningen van de respectievelijke auteurs en niet deze van de beheerders, moderators of webmaster (behalve berichten geplaatst door deze mensen) en U gaat  ermee akkoord dat deze niet aansprakelijk kunnen worden gesteld in geval van een melding die de regels voor het gebruik van de site niet respecteert, en toch niet gemodereerd zou zijn.<br />
      Er is een contactformulier beschikbaar, voor het melden van ongewenste inhoud.</p>
    </div>
    <div>
      <h3>Verzamelen en gebruiken van persoonlijke gegevens</h3>
      <p>Deze gegevens worden verzameld door <?php echo $_SESSION['Application_name']; ?> voor administratieve doeleinden, voor de goede werking van de gemeenschap. Deze informatie zal nooit worden overgedragen of verkocht aan derden, commercieel of niet.</p>
      
      <p>Als u zich registreert, worden enkele persoonlijke gegevens verzameld door <?php echo $_SESSION['Application_name']; ?>. De verplichte informatie tijdens registratie verzameld zijn als volgt en kan evolueren:
      </p>
      <ul>
        <li> je gebruikersnaam (bijnaam) en  een geldig e-mailadres</li>
      </ul>
      
      <p><?php echo $_SESSION['Application_name']; ?> kan u toestaan om een paar optionele persoonlijke gegevens in te vullen via uw gebruikersprofiel zoals je geboortedatum,  hobby's etc.
      </p>
    </div>
    <div>
      <h3>CNIL: toegang tot uw persoonlijke gegevens</h3>
      <p>Voor informatie over de bescherming van persoonsgegevens, kunt u de "Commissie Informatique et Liberte" website (<a href="http://www.cnil.fr" target="_blank">www.cnil.fr</a>) raadplegen.</p>
      
      <p>In alle gevallen, en in overeenstemming met de Franse wetgeving en meer in het bijzonder de wet van 6 januari 1978 Wetenschap en vrijheid,  hebt je een recht van toegang, rectificatie, verzet en onderdrukking ,het bewerken of het verwijderen van uw profiel, door te klikken op "Manage account", weergegeven in het menu "Extra" na identificatie.</p>
      
      <p>Als u nog vragen of problemen, neem dan contact op met de beheerder.</p>
      
      <p>Deze informatie wordt nooit aan derden doorgegeven zonder uw toestemming, in overeenstemming met deze Juridische en Bescherming Persoonlijke Levenssfeer Verklaring, die geacht wordt tijdens de registratie te worden goedgekeurd.</p>
      
      <p>De webmaster, beheerder en moderators zijn niet verantwoordelijk voor hackpogingen die leiden tot de toegang en de verspreiding van deze gegevens, en zij zullen hun uiterste best doen om respect af te dwingen voor de voorwaarden van deze Juridische en Bescherming Persoonlijke Levenssfeer Verklaring.</p>
    </div>
    <div>
      <h2>Wijziging van de Juridische en Bescherming Persoonlijke Levenssfeer Verklaring</h2>
      <p><?php echo $_SESSION['Application_name']; ?> behoudt zich het recht voor om deze Juridische en Bescherming Persoonlijke Levenssfeer verklaring te wijzigen op elk gewenst moment. Eventuele wijzigingen in de regels van <?php echo $_SESSION['Application_name']; ?> betreffende de bescherming van de persoonlijke levenssfeer zal worden geïntegreerd in de huidige Juridische en Bescherming Persoonlijke Levenssfeer Verklaring. Deze juridische en Bescherming Persoonlijke Levenssfeer Verklaring geldt voor alle diensten die door <?php echo $_SESSION['Application_name']; ?>.</p>
    </div>
    <div>
      <h2>Stilzwijgende aanvaarding van deze voorwaarden</h2>
      <p>De bezoeker van <?php echo $_SESSION['Application_name']; ?>, anoniem of geregistreerd, erkent, door het gebruik van verschillende diensten aangeboden door de site, deze voorwaarden te hebben gelezen en te hebben geaccepteerd.</p>
    </div>
<?php
$virtual_page = "legal_and_privacy/Fr";
include_once "../func/suivianalytics.php" ?>
  </body>
</html>