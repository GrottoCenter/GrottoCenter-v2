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
    <title><?php echo $_SESSION['Application_name']; ?> <convert>#label=729<convert> Menciones legales y normativa de confidencialidad</title>
    <link rel="stylesheet" type="text/css" href="../css/legal_and_privacy.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <h1>Menciones legales y normativa de confidencialidad</h1>
    <div>
      <h2>Menciones legales</h2>
      <p>Este sitio esta destinado a proporcionar informaciones de la espeleología y sus practicantes. 
      La información comunicada en este sitio no es contractual y puede ser objeto de modificaciones.</p>
      <p>Este sitio contiene texto, logos e imágenes que pueden estar protegidos por las leyes de copyright 
      y/o de otros derechos de propiedad intelectual.</p>
      <p>El sitio web puede contener enlaces a sitios terceros sin ser la responsabilidad de <?php echo $_SESSION['Application_name']; ?>.</p>
    </div>
    <div>
      <h2>Datos personales reunidos.</h2>
      <h3>Conducta del usuario y reglas para la participación.</h3>
      <p>Usted acepta el no publicar mensajes injuriosos, difamatorios, amenazantes, 
      de carácter xenófobo o  con miras a incitar el odio, o cualquier otro mensaje que viole las leyes aplicables..<br />
      Hacerlo puede conducirlo a ser expulsado inmediatamente y de manera permanente (además su proveedor de internet será informado). 
      La dirección IP y la hora de cada mensaje es registrado a fin de hacer respetar estas condiciones.</p>
    </div>
    <div>
      <h3>Moderación y responsabilidad del equipo<?php echo $_SESSION['Application_name']; ?></h3>
      <p>El equipo de moderación de este sitio se esforzará en mantener el orden dentro del mismo, 
      si es necesario tendrá el derecho de borrar o editar mensajes de carácter reprehensivo lo más rápidamente posible.</p>
      
      <p>Al inscribirse a  <?php echo $_SESSION['Application_name']; ?>, usted acepta el hecho que el web máster, 
      el administrador y los moderadores de este sitio tengan el derecho de borrar, editar, mover o cerrar cualquier 
      sujeto de discusión a todo momento, teniendo en cuenta que es imposible revisar todos los mensajes.</p>
      
      <p>Los mensajes publicados en este sitio reflejan el punto de vista y la opinión de sus respectivos autores, 
      y no aquellos de los administradores, moderadores o web másters (con excepción a los mensajes posteados por ellos mismos) 
      y por consecuente usted acepta que el equipo de GrottoCenter no es responsable de la publicación de mensajes que no respeten 
      las reglas de utilización del sitio, incluyendo los no moderados.<br />
      Una función en vista a alertar a los equipos de moderación sobre la creación de mensajes está disponible en el sitio, y permite al 
      que encuentre un mensaje "fuera de reglas" y aún no moderado, de señalarlo a los responsables.</p>
    </div>
    <div>
      <h3>Reunión y uso de informaciones.</h3>
      <p>La información obtenida por  <?php echo $_SESSION['Application_name']; ?> 
      es utilizada para fines administrativos, para el buen funcionamiento de la comunidad, 
      así como para ofrecerle la posibilidad de tener su individualidad en el seno de la comunidad 
      <?php echo $_SESSION['Application_name']; ?> , ninguna utilización comercial será hecha, y 
      la información jamás será donada o vendida a ningún organismo tercero, comercial o no.</p>
      
      <p>Al momento de su inscripción, algunas informaciones personales son demandadas po
      <?php echo $_SESSION['Application_name']; ?>. Los datos obligatorios reunidos en la inscripción 
      son los siguientes, (eventualmente pudieran incluirse más):
      </p>
      <ul>
        <li>Nombre de usuario (pseudónimo)</li>
        <li>Un correo electrónico válido</li>
      </ul>
      
      <p><?php echo $_SESSION['Application_name']; ?> demandar algunas otros datos personales facultativos para su perfil de usuario::
      </p>
      <ul>
        <li>su dirección</li>
        <li>sus ocios</li>
        <li>su edad</li>
        <li>étc.</li>
      </ul>
    </div>
    <div>
      <h3>CNIL : Acceso y derecho de ver los datos personales de los usuarios.</h3>
      <p>Para cualquier información sobre la protección de datos personales, usted puede consultar el sitio 
      de la "Commission Informatique et Liberté" (<a href="http://www.cnil.fr" target="_blank">www.cnil.fr</a>).</p>
      
      <p>En todo momento, y conforme a la legislación francesa en vigor y particularmente a la ley del 6 de enero de 1978 
      "Informatique et Liberté", usted dispone de un derecho de acceso, de rectificación, de imposición y de supresión sobre 
      estos datos que usted puede ejercer en todo momento, editando o suprimiendo su perfil dando clic en el enlace "Mis parámetros", 
      que está en el menú "herramientas", estando conectado al sitio.</p>
      
      <p>Cualquier pregunta sobre el tema, favor de contactar al administrador del sitio.</p>
      
      <p>TSus datos personales no serán divulgados a ninguna persona o sociedad sin su consentimiento, conforme a esta normativa de 
      Confidencialidad, la cuál es considerada aprobada al momento de su inscripción.</p>
      
      <p>El web máster, el administrador y los moderadores no pueden ser tomados por responsables si hay alguna tentativa de piratería 
      informática que conduzca al acceso y a la difusión de la información recolectada en el sitio, al mismo tiempo harán todo lo posible 
      por hacer respetar la confidencialidad de sus informaciones personales.</p>
    </div>
    <div>
      <h2>Modificación de menciones legales</h2>
      <p><?php echo $_SESSION['Application_name']; ?> se reserva el derecho de modificación de esta normativa a todo momento. 
      Toda modificación de las reglas de <?php echo $_SESSION['Application_name']; ?> sobre la protección de la vida privada 
      será integrada en la presente normativa y dada por conocida y aceptada. Esta normativa se aplica a todos los servicios de  
      <?php echo $_SESSION['Application_name']; ?>.</p>
    </div>
    <div>
      <h2>Aceptación tácita de las presentes menciones</h2>
      <p>El visitante de  <?php echo $_SESSION['Application_name']; ?>, a, anónimo o inscrito, reconoce,  por su utilización de los 
      diferentes servicios propuestos por el sitio, haber leído y aceptado las presentes condiciones de utilización.</p>
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