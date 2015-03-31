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
    <h1>Informació legal</h1>
    <div>
      <h2>Legalitat</h2>
      <p>Aquest lloc està destinat a recopilar i proporcionar informació sobre espeleologia i espeleòlegs.</p>
      <p>La informació proporcionada en aquest lloc no són contractuals i poden ser modificats.</p>
      <p>Aquest lloc conté text, logotips i imatges que poden ser protegits per drets d'autor o altres drets de propietat intellectual.</p>
      <p>GrottoCenter no aceptarà cap responsabilitat per als enllaços a altres llocs d'aquest lloc web.</p>
    </div>
    <div>
      <h2>Obtenció i utilització d'informació personal</h2>
      <h3>Normes per contribuir-hi</h3>
      <p>Vostè està d'acord en no publicar material abusiu, difamatori, amenaçador, xenòfob o incitar a l'odi, o qualsevol altre tipus que pugui violar qualsevol llei aplicable.<br />
      Fer això provocarà que siguis immediata i permanentment prohibit (i el proveïdor de serveis d'Internet serà informat). L'adreça IP i l'hora de cada missatge es guarda per ajudar a complir aquestes normes.</p>
    </div>
    <div>
      <h3>La moderació i la responsabilitat de l'equip GrottoCenter</h3>
      <p>L'equip de moderació intentarà mantenir l'ordre en el lloc web, i si cal, eliminar o editar qualsevol missatge desagradable tan aviat com sigui possible.</p>
      
      <p>Quan s'uneix a GrottoCenter, vostè està d'acord amb que el webmaster, administrador i moderadors del lloc tenen el dret d'eliminar, editar, moure o tancar qualsevol tema en qualsevol moment. No obstant això, malgrat tots els seus esforços, és impossible per a ells revisar tots els missatges.</p>
      
      <p>Els missatges publicats en aquest lloc expressen el punt de vista i opinions dels seus respectius autors i no la dels administradors, moderadors o el webmaster (excepte en missatges publicats per ells mateixos) i estàs d'acord en que no es pot, per tant, fer-los responsables dels missatges que no compleixen les normes de funcionament del lloc web, i que encara no hagin estat moderats.<br />
      Hi ha disponible una secció de contacte, i permet a qualsevol persona informar de qualsevol contingut "fora de les normes".</p>
    </div>
    <div>
      <h3>Obtenció i utilització de la informació personal</h3>
      <p>Aquestes dades són recollides per GrottoCenter per a fins administratius, per al bon funcionament de la comunitat, i per oferir-li la oportunitat de marcar la seva individualitat dins la comunitat GrottoCenter - garantint que serà sense cap ús comercial, i aquestes dades mai seran transferides o venudes a tercers, comercials o no.</p>
      
      <p>Quan es registri, GrottoCenter recollirà algunes dades personals. La informació obligatòria recollida durant el registre és la següent i pot donar-se el cas que aquesta sigui ampliada:
      </p>
      <ul>
        <li>el seu nom d'usuari (nick)</li>
        <li>una adreça d'e-mail vàlida</li>
      </ul>
      
      <p>Opcionalment, GrottoCenter li pot permetre omplir altres dades personals a través del seu perfil d'usuari:
      </p>
      <ul>
        <li>la seva direcció</li>
        <li>les seves aficions</li>
        <li>la seva data de naixement</li>
        <li>etcètera</li>
      </ul>
    </div>
    <div>
      <h3>CNIL: l'accés a les seves dades personals</h3>
      <p>Per obtenir informació sobre la protecció de dades de caràcter personal, podeu consultar el lloc web "Comissió Informàtica i Llibertat" (www.cnil.fr).</p>
      
      <p>En tots els casos, i d'acord amb la legislació francesa i en particular la llei de 6 gener 1978, Ciència i la llibertat, té el dret d'accés, rectificació, oposició i supressió d'aquestes dades que podrà exercir en qualsevol moment, l'edició o esborrar el seu perfil fent clic a "Administrar compte", que es mostra en el menú "Eines", després de la identificació.</p>
      
      <p>Si té qualsevol pregunta o problema, si us plau, posi's en contacte amb l'administrador.</p>
      
      <p>Aquesta informació es proporcionarà a tercers sense el vostre consentiment, d'acord amb aquesta Declaració legal i de privacitat, que es considerarà autoritzada durant el registre.</p>
      
      <p>El webmaster, administrador i moderadors no poden ser considerats responsables per qualsevol atac al sistema portat a l'accés i la difusió d'aquestes dades, i farà tot el possible perquè les persones respectin els termes d'aquesta Declaració legal i privacitat.</p>
    </div>
    <div>
      <h2>La modificació d'aquesta Declaració Legal i de Privacitat</h2>
      <p>GrottoCenter es reserva el dret de modificar aquesta Declaració legal i de privacitat en qualsevol moment. Qualsevol canvi en les regles de GrottoCenter sobre la protecció de la privacitat s'integraran en el present Avís Legal i de Privacitat, i reconeguts conegudes i acceptades. Aquesta Declaració Legal i de Orivacitat s'aplica a tots els serveis prestats per GrottoCenter.</p>
    </div>
    <div>
      <h2>L'acceptació tàcita d'aquests termes</h2>
      <p>El visitant de GrottoCenter, anònim o registrat, reconeix, pel seu ús dels diferents serveis que ofereix el lloc, haver llegit i acceptat aquestes condicions.</p>
    </div>
<?php
$virtual_page = "legal_and_privacy/Fr";
include_once "../func/suivianalytics.php" ?>
  </body>
</html>