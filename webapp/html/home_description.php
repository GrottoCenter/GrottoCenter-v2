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

switch($_SESSION['language']) {
  case 'Fr':
?>

<h2>Bonjour et bienvenu(e) sur <?php echo $_SESSION['Application_name']; ?> !</h2>
<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">Qu'est ce que <?php echo $_SESSION['Application_name']; ?> ?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> est une base de données 
    communautaire dédiée à la spéléologie et alimentée par les spéléologues sur le 
    principe du Wiki.<br />
    Toute cavité naturelle présentant un intérêt à tes yeux peut y être enregistrée !</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">Comment utiliser <?php echo $_SESSION['Application_name']; ?> ?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>La navigation se fait par l'intermédiaire de l'outil google-map. 
    Les spéléologue sont représentés par des casques rouges 
    <img src="../images/icons/caver2.png" alt="" />, les entrées des cavités 
    par des kits jaunes <img src="../images/icons/entry2.png" alt="" /> et les club par
    des maisons bleues <img src="../images/icons/grotto1.png" alt="" />.<br />
    En cliquant sur un des pictogrammes, tu fais apparaître une bulle qui contient ses 
    propriétés (massif, développement, profondeur, etc.).<br />
    Pour les objets « Entrées » (les kits jaunes), la bulle permet d'accéder à la 
    fiche détaillée qui contient entre autre :</p>
    <ul>
      <li>la description de l'accès</li>
      <li>la description de la cavité</li>
      <li>la fiche d'équipement</li>
      <li>un lien internet vers les sites traitant dudit réseau</li>
      <li>des commentaires permettant entre autre de noter l'esthétique et la 
      difficulté de la cavité, signaler des explorations etc...</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Pourquoi es tu vital au projet <?php echo $_SESSION['Application_name']; ?> ?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> vit avec la contribution 
    des spéléologues (donc la tienne), nous comptons sur toi pour nous aider à 
    compléter et fiabiliser cette base de données. Tu accèderas au menu 
    création/modification à partir du menu situé à gauche de la carte après t'être 
    connecté(e).<br />
    Nous te rappelons que toute cavité naturelle présentant un intérêt à tes 
    yeux peut être enregistrée dans <?php echo $_SESSION['Application_name']; ?> !<br />
    Merci.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Qui sommes nous ?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Voici la "GrottoTeam", par ordre alphabétique des noms :</p>
    <ul>
      <li>Nathan Boinet : conseiller en ergonomie et concepteur fonctionnel, qualifieur</li>
      <li>Thomas Cabothiau : conseiller en ergonomie et fonctionnel</li>
      <li>Barbara Guzman : conseillère en ergonomie et traductrice Français-Espagnol</li>
      <li>Vanyo Gyorev : traducteur Bulgare.</li>
      <li>Ivan Herbots : traducteur Néerlandais.</li>
      <li>Stéphane Lips : conseiller en ergonomie et fonctionnel, qualifieur</li>
      <li>Francesc B. Ricart : traducteur Catalan.</li>
      <li>Clément Ronzon : développeur, designer, concepteur fonctionnel et traducteur Anglais-Français</li>
      <li>Vincent Routhieau : conseiller en ergonomie, concepteur fonctionnel, qualifieur</li>
      <li>Benjamin Soufflet : développeur, administrateur système</li>
      <li>Norbert Weber : traducteur Français-Allemand.</li>
      <li>Spéléos italiens : Traduction italienne,</li>
    </ul>
    <p>Si tu désires apporter ta pierre à l'édifice, n'hésite pas à prendre contact avec nous !</p>
    <p>Un grand merci à toutes ces personnes et à toutes celles qui ont participé
    et contribué au projet !</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">Quelles sont les conditions d'utilisation des informations ?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> a été élaboré dans un esprit
    totalement apolitique. Le site et les éléments qu'il contient constituent
    une oeuvre protégée par les traités internationaux.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">Comment ajouter une cavité sensible ?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>Si vous souhaitez entrer une cavité "sensible" il existe deux solutions
    suivant le degré de protection désiré.</p>
    <ul><li>Soit la cavité est facile d'accès et il faut juste <b>éviter une
    surfréquentation</b> par des non spéléos.<br />
    Dans ce cas, il suffit de choisir <b>"Inscrits (cavité sensible et/ou à accès réglementé)."</b>
    lors de la création de l'entrée.<br />
    De cette façon la cavité n'apparaît qu'aux inscrits qui sont connectés.</li>
    <li>Soit la cavité est <b>très sensible (concrétions, archéologie, paléontologie,
    etc...)</b> et il faut absolument éviter une fréquentation par des non spéléos.<br /> 
    Dans ce cas, en plus de cocher la case "Cette entrée est visible uniquement
    par les inscrites à <?php echo $_SESSION['Application_name']; ?>",
    il est recommandé de <b>fausser les coordonnées</b> de la cavité dans
    un rayon d'environ 1 km autour de son endroit réel et de <b>bien préciser</b> dans
    la rubrique "Localisation de l'entrée" que la cavité n'est pas pointée à son
    emplacement exact et qu'il ne faut pas en indiquer l'accès.<br />
    De cette façon si des spéléos souhaitent visiter la cavité, ils devront obligatoirement
    prendre contact avec les personnes ou organismes mentionnés ou encore avec les
    spéléos en lien avec cette cavité. Cette option est à réserver à des cas exceptionnels.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Avertissements</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'En':
?>
<h2>Hi! Welcome on <?php echo $_SESSION['Application_name']; ?>!</h2>

<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">What is <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> is a comunity database for
    cavers based on a wiki-like system. Cavers fill the databes for cavers.<br />
    Any interesting natural cavity can be added in the database!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">How to use <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>Navigation is through an embeded Google-Map. 
    Cavers are represented by red helmets <img src="../images/icons/caver2.png" alt="" />,
    entries by yellow packs <img src="../images/icons/entry2.png" alt="" /> and 
    grottoes by blue houses <img src="../images/icons/grotto1.png" alt="" />.<br />
    By clicking on any of those pictograms, an info-window appears showing its
    properties (massif, length, depth, etc.).<br />
    For the "entries" (yellow packs), the info-window allow you to access to
    a detailed sheet containing:</p>
    <ul>
      <li>a description of the access to the cave</li>
      <li>a cave description</li>
      <li>a description of rigging and ropes needs</li>
      <li>a reference to any linked web site</li>
      <li>some comments (by cavers) with an evaluation of aestetics etc.</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Why are you a key in the <?php echo $_SESSION['Application_name']; ?>'s project?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> works with caver's contributions 
    (so your's), and we count on you to help us complete and rely this database.
    You can access to the creation/modification menu with the left-hand panel
    after you signed in.<br />
    We remind you that you can add any interesting cave to <?php echo $_SESSION['Application_name']; ?>!<br />
    Thanks.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Who are we?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Here is the "GrottoTeam", alphabetically by name:</p>
    <ul>
      <li>Nathan Boinet: ergonomics advisor and functional analyst, tester</li>
      <li>Thomas Cabothiau: ergonomics advisor and functional advisor</li>
      <li>Barbara Guzman: ergonomics advisor and English-Spanish translations</li>
      <li>Vanyo Gyorev : Bulgarian translation.</li>
      <li>Ivan Herbots : Dutch translation.</li>
      <li>Stéphane Lips: ergonomics advisor and functional advisor, tester</li>
      <li>Francesc B. Ricart : Catalan translation.</li>
      <li>Clément Ronzon: designer, ergonomics and functional analyst, English-French translations</li>
      <li>Vincent Routhieau: ergonomics advisor, functional analyst, tester</li>
      <li>Benjamin Soufflet : developer, system administrator</li>
      <li>Norbert Weber : German translation.</li>
      <li>Italian cavers : Italian translation.</li>
    </ul>
    <p>If you want to make your bit, feel free to contact us!</p>
    <p>A big thank to those people and all those who participated
     and contributed to the project!</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">What are the term of use of the data?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> was designed in a spirit
    totally apolitical. The site contains elements that constitute a work
    protected by international treaties.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">How to add a sensitive cavity?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>If you want to register a "sensitive" cavity there are two options
    depending on the degree of protection desired.</p>
    <ul><li>The cavity is easily accessible and you just whant to  <b>avoid
    overcrowding</b> by non-caver people.<br />
    In this case, just chosse the option <b>"Registered (sensitive cave and/or
    regulated access)."</b> when adding the entry.<br />
    In this way the cavity appears only to registered users who are connected.</li>
    <li>The cavity is <b>very sensitive (concretions, archeology, paleontology, etc.)</b>
    and it is essential to avoid access for non-caver people.<br />
    In this case, chosse the option "Registered (sensitive cave and/or
    regulated access).", and it is recommended to <b>distort the coordinates</b> of
    the cavity within a radius of 1 km around its real place and <b>specify</b> in
    the detailed sheet that the cavity is not pointing its exact location and
    should not indicate access.<br />
    In this way the cavers that wish to visit that cave will necessarily
    contact with right peolple/organization concerned by this cavity. This option
    is reserved for exceptional cases.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Warning</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'Es':
?>

<h2>Hola y bienvenidos en <?php echo $_SESSION['Application_name']; ?> !</h2>


<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">¿Qué es <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> es una base de datos dedicada
    a la espeleología, la información que encontrará en la base de datos
    es ingresada por los visitantes de grutas, está basado en el concepto de la
    enciclopedia Wikipedia.<br />
    Puedes ingresar a nuestra base de datos cualquiera gruta natural que
    consideres interesante!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">¿Cómo utilizar <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>Al hacer clic en uno de los iconos, se abre una ventana que contiene sus
    propiedades (macizo, profundidad, etc...).
    Para los objetos "entradas" (bolsas amarillas
    <img src="../images/icons/entry2.png" alt="" />), la ventana da acceso a los
    detalles que incluyen:</p>
    <ul>
      <li>la descripción del acceso a la gruta</li>
      <li>la descripción de la gruta</li>
      <li>el equipo necesario para visitarla</li>
      <li>enlaces hacia los sitios de internet relacionados</li>
      <li>observaciones a tomar en cuenta, entre otras: la estética y la
      dificultad de la gruta, informe, exploraciones etc...</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">¿Por qué eres necesario para el proyecto <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> funciona con la aportación
    de los espeleólogos (es decir, la tuya), contamos contigo para ayudarnos a
    completar la base de datos y lograr que esta sea fiable. Para comenzar,
    una vez conectado, accede al menú de creación o de modificación en la
    izquierda del mapa.
    Y recuerda: puedes ingresar a <?php echo $_SESSION['Application_name']; ?>
    cualquiera gruta natural que consideres interesante!<br />
    Gracias.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">¿Quiénes somos?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Te presentamos los miembros del "GrottoTeam", en orden alfabético:</p>
    <ul>
      <li>Nathan Boinet : Consultor de diseño, funcionalidad y de ergonomía, así como probador.</li>
      <li>Thomas Cabothiau : Consultor de diseño, funcionalidad y de ergonomía, así como probador.</li>
      <li>Barbara Guzman : Traductor Francés-Español.</li>
      <li>Vanyo Gyorev : Traductor Búlgaro.</li>
      <li>Ivan Herbots : Traductor Holandés.</li>
      <li>Stéphane Lips : Consultor de diseño, funcionalidad y de ergonomía, así como probador.</li>
      <li>Francesc B. Ricart : Catalan translation.</li>
      <li>Clément Ronzon : Diseñador del código, funcionalidad, ergonomía, y traductor Francés-Inglés.</li>
      <li>Vincent Routhieau : Consultor de diseño, funcionalidad y de ergonomía, así como probador.</li>
      <li>Benjamin Soufflet : Diseñador del código, administrador del sistema.</li>
      <li>Norbert Weber : Traductor Alemán.</li>
      <li>Italian cavers : Italian translation.</li>
    </ul>
    <p>Si deseas participar a la construcción de <?php echo $_SESSION['Application_name']; ?>,
    no dudes en ponerte en contacto con nosotros!</p>
    <p>Muchas gracias a todas estas personas, todos los que participaron y participaran al proyecto!</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">¿Cuáles son las condiciones de uso de la información?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> fue desarrollado en un espíritu
    totalmente apolítico. El sitio y la información que contiene es una obra
    protegida por tratados internacionales.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">Cómo agregar una cavidad sensible?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>Si quieres añadir una cavidad "sensible", hay dos opciones dependiendo
    del grado de protección deseado.<p>
    <ul><li>La cavidad es fácil de acceso y hay que <b>evitar el hacinamiento</b> por
    no espeleólogos.<br />
    En este caso, simplemente <b>selccionne "Registrados (cueva sensible y/o a acceso regulado)."</b>
    al crear la entrada.<br />
    De esta manera, la cavidad aparece sólo a los registrados que están conectados.</li>
    <li>La cavidad es <b>muy sensible (concreciones, arqueología, paleontología, etc ...)</b>
    y es esencial de prohibir el acceso a no espeleólogos.<br />
    En este caso, también selccionne "Registrados (cueva sensible y/o a acceso regulado).",
    y se recomienda a <b>distorsionar las coordenadas</b> de la cavidad dentro
    de un radio de 1 km alrededor de su verdadero lugar y especificar en la hoja detallada
    de que la cavidad no está señalando en su ubicación exacta y no se debe indicar el acceso.<br />
    De esta manera, los espeleólogos que deseen visitar la cueva se pondrán necesariamente
    en contacto con personas o entidades mencionadas o con espeleólogos en relación
    con esta cavidad. Esta opción está reservada para casos excepcionales.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Advertencias</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'De':
?>
<h2>Hallo und herzlich Willkommen bei <?php echo $_SESSION['Application_name']; ?>!</h2>


<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">Was ist <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> ist eine Höhlendatenbank der Gemeinschaft,
aufbauend auf dem Prinzip von Wikipedia.<br />
    Jede natürliche Höhle, die von Interesse ist, kann hier gespeichert werden!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">Wie verwendet man <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>Die Navigation funktioniert per Google-map. 
    Höhlenforscher werden in der Karte durch einen roten Helm symbolisiert 
    <img src="../images/icons/caver2.png" alt="" />, Mundlöcher durch einen gelben
    Schleifsack <img src="../images/icons/entry2.png" alt="" /> und Vereine durch
    ein blaues Häuschen <img src="../images/icons/grotto1.png" alt="" />.<br />
    Der Klick auf eins dieser Symbole öffnet ein Informationsfenster zum gewählten
    Objekt.<br />
    Im Falle von Höhlen lässt sich über besagtes Fenster auch die Detailansicht anzeigen,
    die unter anderem folgende Daten enthält:</p>
    <ul>
      <li>Beschreibung des Höhlenzugangs</li>
      <li>Beschreibung der Höhle selbst</li>
      <li>Ausrüstungsliste</li>
      <li>Links zum Thema</li>
      <li>Angaben zur Schönheit, Schwierigkeitsgrad uvm.</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Warum bist du wichtig für <?php echo $_SESSION['Application_name'];?> ?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> funktioniert durch die rege
    Mitarbeit der Höhlenforscher (also durch deine Hilfe!) - wir zählen auf DICH! ;-)
    Nur so ist eine stetige Erweiterung und Aktualisierung möglich.<br />
    Wir erinnern dich noch mal daran: Jede Höhle ist von Interesse und kann in
    <?php echo $_SESSION['Application_name']; ?> gespeichert werden!<br />
    Vielen Dank.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Wer sind wir?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Das "GrottoTeam", in alphabetischer Reihenfolge:</p>
    <ul>
      <li>Nathan Boinet : Programmierung und Beratung in Sachen Ergonomie</li>
      <li>Thomas Cabothiau : Berater Funktion und Ergonomie</li>
      <li>Barbara Guzman : Beratung sowie Übersetzung Französisch-Spanisch</li>
      <li>Vanyo Gyorev : Übersetzung Bulgarisch</li>
      <li>Ivan Herbots : Übersetzung Niederländisch.</li>
      <li>Stéphane Lips : Beratung</li>
      <li>Francesc B. Ricart : Catalan translation.</li>
      <li>Clément Ronzon : Entwicklung, Design, Übersetzung Französisch-Englisch</li>
      <li>Vincent Routhieau : Beratung</li>
      <li>Benjamin Soufflet : Entwicklung, Systemadministrator</li>
      <li>Norbert Weber : Übersetzung Deutsch</li>
      <li>Italian cavers : Italian translation.</li>
    </ul>
    <p>Wenn du uns helfen möchtest, schreib doch bitte eine Email, wir würden uns freuen!</p>
    <p>Ein großes Dankeschön an alle diese Leute und auch an die, die anderweitig zum Projekt beitragen!</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">Warum eine "Creative Commons" Lizenz</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> wurde mit der Absicht 
    entwickelt, der Allgemeinheit zu dienen. Daher steht der Inhalt unter der sogenannten
    "Creative Commons Share-Alike" Lizenz.
    <?php echo getLicense(4); ?></p>
    <p>Die Höhlenforschung ist ein Sport, der die Natur respektiert. Die Höhlen entstanden
    vor langer Zeit, sie sind (i. A.) für jeden zugänglich - 
    <?php echo $_SESSION['Application_name']; ?> macht das gleiche.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">Wie man einen Hohlraum sensiblen hinzufügen?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
		<p>Wenn Sie einen Hohlraum eintreten wollen "sensibel" gibt es zwei Lösungen,
		da das Ausmaß des Schutzes gewünscht.</p>
    <ul><li>Lassen Sie die Kavität leicht zugänglich ist und Sie nur vermeiden
		Überbelegung durch Nicht-Höhlenforscher.<br />
    In diesem Fall wählen Sie einfach "Registered (Hohlraum empfindliche und / oder beschränkten)."
		bei der Erstellung des Eintrags.<br />
    Auf diese Weise wird der Hohlraum erscheint nur Registranten, die miteinander verbunden sind.</li>
    <li>Lassen Sie den Hohlraum ist sehr empfindlich (Formationen, Archäologie, Paläontologie, etc. ...)
		und man sollte die Teilnahme von Nicht-Höhlenforscher zu halten.<br />
    In diesem Fall, zusätzlich zu prüfen, die "Dieser Eintrag nur sichtbar für registrierte <?php echo $_SESSION['Application_name']; ?>
		ist, ist es empfehlenswert, die Koordinaten der Hohlraum innerhalb eines Radius von 1 km um ihren aktuellen
		Standort und zu präzisieren, zu verfälschen in den "Ort der Eintragung, dass der Hohlraum ist nicht auf
		seinen genauen Standort anzugeben und darf nicht zugreifen.<br />
    Auf diese Weise, wenn Höhlenforscher wollen die Höhle zu besuchen, werden sie zwangsläufig in
		Kontakt mit Personen oder Stellen aufgeführt sind, oder mit Höhlenforschern im Zusammenhang mit
		diesem Hohlraum. Diese Option sollte für Ausnahmefälle reserviert werden.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Warnung</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'It':
?>
<h2>Ciao, benvenuto su <?php echo $_SESSION['Application_name']; ?>!</h2>

<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">Cos'è <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> è un database comunitario per speleologi basato su un sistema tipo "wiki". Gli speleologi popolano il database per gli speleologi.<br>
    Qualunque cavità naturale interessante può essere aggiunta al database!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">Come usare <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>Navigation is through an embeded Google-Map. 
    Cavers are represented by red helmets <img src="../images/icons/caver2.png" alt="" />,
    entries by yellow packs <img src="../images/icons/entry2.png" alt="" /> and 
    grottoes by blue houses <img src="../images/icons/grotto1.png" alt="" />.<br />
    Cliccando su ognuno di questi simboli, una finestra informativa mostrerà le sue proprietà (massiccio, lunghezza, profondità, ecc).<br />
    For the "entries" (yellow packs), the info-window allow you to access to
    a detailed sheet containing:</p>
    <ul>
      <li>descrizione dell'ingresso della grotta</li>
      <li>descrizione della grotta</li>
      <li>scheda d'armo</li>
      <li>a reference to any linked web site</li>
      <li>alcuni commenti (di speleologi) con una valutazione estetica ecc.</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Why are you a key in the <?php echo $_SESSION['Application_name']; ?>'s project?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> works with caver's contributions 
    (so your's), and we count on you to help us complete and rely this database.
    You can access to the creation/modification menu with the left-hand panel
    after you signed in.<br />
    We remind you that you can add any interesting cave to <?php echo $_SESSION['Application_name']; ?>!<br />
    Grazie.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Chi siamo?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Questo è il "GrottoTeam", in ordine alfabetico per nome:</p>
    <ul>
      <li>Nathan Boinet: ergonomics advisor and functional analyst, tester</li>
      <li>Thomas Cabothiau: ergonomics advisor and functional advisor</li>
      <li>Barbara Guzman: ergonomics advisor and English-Spanish translations</li>
      <li>Vanyo Gyorev: traduzione dal bulgaro</li>
      <li>Ivan Herbots : Dutch translation.</li>
      <li>Stéphane Lips: ergonomics advisor and functional advisor, tester</li>
      <li>Francesc B. Ricart : Catalan translation.</li>
      <li>Clément Ronzon: designer, ergonomics and functional analyst, English-French translations</li>
      <li>Vincent Routhieau: ergonomics advisor, functional analyst, tester</li>
      <li>Benjamin Soufflet: sviluppatore, amministratore di sistema</li>
      <li>Norbert Weber: traduzione dal tedesco</li>
      <li>Gli speleo italiani hanno curato la traduzione in italiano</li>
    </ul>
    <p>If you want to make your bit, feel free to contact us!</p>
    <p>Un grosso ringraziamento a tutti coloro i quali hanno partecipato e contribuito al progetto</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">Quali sono le condizioni d'uso dei dati?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> è realizzato con spirito totalmente apolitico. il sito contiene elementi che costituiscono un lavoro proteto dai trattati internazionali</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">Come aggiungere una grotta sensibile?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>Se vuoi aggiungere una grotta "sensibile" ci sono due opzioni, dipende dal grado di protezione che si desidera dare</p>
    <ul><li>The cavity is easily accessible and you just whant to  <b>avoid
    overcrowding</b> by non-caver people.<br />
    In this case, just chosse the option <b>"Registered (sensitive cave and/or
    regulated access)."</b> when adding the entry.<br />
    In this way the cavity appears only to registered users who are connected.</li>
    <li>La grotta è molto sensibile (concrezioni, archeologia, paleontologia, ecc) è necessario evitare l'accesso ai non speleologi<br />
    In this case, chosse the option "Registered (sensitive cave and/or
    regulated access).", and it is recommended to <b>distort the coordinates</b> of
    the cavity within a radius of 1 km around its real place and <b>specify</b> in
    the detailed sheet that the cavity is not pointing its exact location and
    should not indicate access.<br />
    In this way the cavers that wish to visit that cave will necessarily
    contact with right peolple/organization concerned by this cavity. This option
    is reserved for exceptional cases.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Attenzione</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'Ar':
?>

<?php
  break;
  case 'Bg':
?>
<h2>Здравейте! Добре дошли в <?php echo $_SESSION['Application_name']; ?>!</h2>


<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">Какво е <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> е общнeствена  база данни за
пещерняци основава като уики-система. Пещерняците предоставят данни за пещерняци. <br />
Всякакви интересни, природни подземни кухина може да се добавят в базата данни!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">Как да използвате <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>Навигацията е посредством вградена Google-карта. Пещерняците са представени с червени каски <img src="../images/icons/caver2.png" alt="" />,
Входовете са означени с жълта прониквачка, <img src="../images/icons/entry2.png" alt="" /> а 
Клубовете са означени със сини къщички <img src="../images/icons/grotto1.png" alt="" />.<br />
С кликване върху някоя от тези пиктограми, се появява инфо-прозорец за състоянието на неговите
свойства (масив, дължина, дълбочина и др.)<br />
За "входове" (жълти прониквачки), инфо-прозорец ви позволява да осъществите достъп до
детайлен лист, който съдържа:</p>
    <ul>
      <li>описание за достъп до пещерата</li>
      <li>описание на пещерата</li>
      <li>описание на необходимата екипировка и въжета</li>
      <li>връзка към друг свързан уебсайт</li>
      <li>някои коментари (от пещерняци) с оценка за естетиката (красотата) и т.н.</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Защо сте важни (ключови) за  проекта на <?php echo $_SESSION['Application_name']; ?>'s ?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> Работи с приносите (информацията) на пещерняците (както вашият), а ние се надяваме  
да ни помогнете за завършването на  тази база данни.
Можете да получите достъп до менюто "създаване / промяната" панела в ляво като използвате бутон "Редакция"
след като сте влезли в профила си<br />
    Напомняме ви, че можете да добавите всяка интересна пещера в <?php echo $_SESSION['Application_name']; ?>!<br />
    Благодарим Ви.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Кои сме ние?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Това е нашия "GrottoTeam":</p>
    <ul>
      <li>Nathan Boinet: консултант ергономичност, функционален анализатор и тестер.</li>
      <li>Thomas Cabothiau: ергономичност, функционален анализатор и тестер.</li>
      <li>Barbara Guzman: ергономичност и преводач от Английски на Испански език.</li>
      <li>Vanyo Gyorev : превод на Български език.</li>
      <li>Ivan Herbots : Dutch translation.</li>
      <li>Stéphane Lips: ергономичност, функционален анализатор и тестер.</li>
      <li>Francesc B. Ricart : Catalan translation.</li>
      <li>Clément Ronzon: дизайнер, ергономичност и функционален анализатор, превод от Английски на Френски език.</li>
      <li>Vincent Routhieau: ергономичност, функционален анализатор и тестер.</li>
      <li>Benjamin Soufflet : програмист, системен администратор</li>
      <li>Norbert Weber : превод на Немски език.</li>
      <li>Italian cavers : Italian translation.</li>
    </ul>
    <p>Ако желаете да се включите, не се колебайте да се свържете с нас!</p>
    <p>Големи благодарности на тези хора, както и всички онези, които участват и допринасят за проекта!</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">Какви са условията за ползване на данните?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> е създаден в духа на пълна аполитичност. Сайтът съдържа елементи, които съставляват работа защитена от международни договори.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">Как да добавите защитена пещера?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>Ако искате да регистрирате "защитена" пещера има два варианта в зависимост от степента на желаната защита.</p>
    <ul><li>Пещерата е лесно достъпна и вие искате <b>
да я предпазите от</b> не желан достъп.<br />
В този случай, изберете опцията <b>"Регистрирай (защитена пещера и/или Регулиран достъп)."</b> когато добавяте данни за нея.<br />
При този подход пещерата се вижда само от регистрирани потребители.</li>
    <li>Ако пещерата <b>е много "чувствителна" (биология, археология, палеонтология и т.н.)</b> 
и е изключително важно да се ограничи достъпа до нея.<br />
В този случай, изберете опцията "Регистрирай (защитена пещера и/или Регулиран достъп)", и препоръчваме <b>промяна на координатите</b> на пещерата в радиус от 1 км от реалната позиция <b>При оказване</b>на информацията в детайли да не се оказва точно разположение за достъп и подхода към входа на пещерата.<br />
По този начин пещерняците, които желаят да посетят пещерата задължително ще се свържат с клуба / организация отговорни за нея. Тази опция е запазена за извънредни случаи.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Предупреждение</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'Pt':
?>

<?php
  break;
  case 'Ca':
?>
<h2>Hola! Benvingut a <?php echo $_SESSION['Application_name']; ?>!</h2>
<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">Què és <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> és una base de dades comunitària per a espeleòlegs basada en un sistema semblant a les wikipèdies. Els espeleòlegs omplen la base de dades pels espeleòlegs.<br>
    Es pot afegir qualsevol cova natural interessant!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">Com utilitzar <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>La navegació es fa a través d'un mapa incrustat de Google. Els espeleòlegs estan representats amb cascos vermells <img src="../images/icons/caver2.png" alt="" />, les entrades en paquets grocs <img src="../images/icons/entry2.png" alt="" /> i les coves en cases blaves <img src="../images/icons/grotto1.png" alt="" />. La navegació es fa a través d'un mapa incrustat de Google. 
    <br />
    Clicant a qualsevol d'aquests pictogrames apareixerà una finestra d'informació que mostrarà les seves propietats (massís, longitud, profunditat, etc.).<br />
    Per les "entrades" (paquets grocs), la finestra d'informació et permet accedir a la fitxa detallada que conté:</p>
    <ul>
      <li>una descripció de l'accés a la cova</li>
      <li>una descripció de la cova</li>
      <li>una descripció de l'equipament i les cordes necessàries</li>
      <li>una referència cap a qualsevol vincle a un lloc web associat</li>
      <li>alguns comentaris (d'espeleòlegs) amb una evaluació de l'estètica, etc.</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Perquè tu ets una de les claus en el projecte <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> creix amb les contribucions dels espeleòlegs (per tant, les teves), i nosaltres comptem amb tu per ajudar-nos a completar i depurar aquesta base de dades. Pots accedir al menú de creació/modificació amb el panell que apareix a mà dreta després d'haver accedit amb el teu usuari.<br />
    Et recordem que pots afegir a <?php echo $_SESSION['Application_name']; ?> qualsevol cova interessant!<br />
    Gràcies.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Qui som?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Aquí hi ha el "GrottoTeam", ordenat alfabèticament per nom:</p>
    <ul>
      <li>Nathan Boinet: assessor d'ergonomia i analista funcional, tester</li>
      <li>Thomas Cabothiau: assessor d'ergonomia i analista funcional</li>
      <li>Vanyo Gyorev : traducció al Búlgar</li>
      <li>Barbara Guzman: assessora d'ergonomia i traductora d'Anglès a Espanyol</li>
      <li>Ivan Herbots : traducció al holandès</li>
      <li>Stéphane Lips: assessor d'ergonomia i analista de funcional, tester</li>
      <li>Francesc B. Ricart : traducció al Català</li>
      <li>Clément Ronzon: dissenyador, assessor d'ergonomia i analista de funcional i traductor d'Anglès a Francès</li>
      <li>Vincent Routhieau: assessor d'ergonomia i analista funcional, tester</li>
      <li>Benjamin Soufflet : desenvolupador, administrador de sistemes</li>
      <li>Norbert Weber : traducció a l'Alemany</li>
      <li>Italian cavers : Italian translation.</li>
    </ul>
    <p>Si vols posar el teu gra de sorra, si et plau, contacta'ns!</p>
    <p>Un gran agraïment a tota aquella gent que han participat i contribuït en el projecte!</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">Quins són els termes d'utilització de les dades?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> ha estat dissenyat en un esperit totalment apolític. El lloc conté elements que constitueixen un treball protegit pels tractats internacionals.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">Com afegir una cosa 'sensible'?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>Si desitges registrar una cova 'sensible' tens dues opcions que depenen del grau de protecció que desitgis aplicar-li.</p>
    <ul><li>La cova és fàcilment accessible i només vols evitar massificacions per gent no provinent del món de l'espeleologia.<br />
    En aquest cas, escull l'opció 'Registrada (cova sensible i/o d'accés regulat).' quan afegeixis l'entrada.<br />
    En aquest sentit, la cova només apareix per als usuaris registrats que estan connectats.</li>
    <li>La cova és 'molt sensible' (concrecions, arqueologia, paleontologia, etc.) i és molt important evitar l'accés a gent no espeleòloga.<br />
    En aquest cas, escull l'opció 'Registrada (cova sensible i/o d'accés regulat).' quan afegeixis l'entrada, i a més a més, es recomana distorsionar les coordinades de la cova dintre d'un radi d'1km al voltant de la cova. Llavors, s'ha d'especificar a la fitxa detallada que la cova no està situada amb exactitud i no s'han d'indicar les dades d'accés real a la cova.<br />
    En aquest sentit, els espeleòlegs que desitgin visitar aquesta cova hauran de contactar necessàriament amb la persona/organització relativa per aquesta cova. Aquesta opció està reservada a casos excepcionals.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Avís</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
  case 'He':
?>

<?php
  break;
  case 'Oc':
?>

<?php
  break;
  case 'Ru':
?>

<?php
  break;
  case 'Nl':
?>

<h2>Hi! Welcome on <?php echo $_SESSION['Application_name']; ?>!</h2>
<div>
  <div id="about_s" onclick="JavaScript:switchMe('about');" class="div_switcher_c">
    <ul>
      <li><span class="title">What is <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="about" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> is a comunity database for
    cavers based on a wiki-like system. Cavers fill the databes for cavers.<br />
    Any interesting natural cavity can be added in the database!</p>
  </div>
  
  <div id="howto_s" onclick="JavaScript:switchMe('howto');" class="div_switcher_c">
    <ul>
      <li><span class="title">How to use <?php echo $_SESSION['Application_name']; ?>?</span></li>
    </ul>
  </div>
  <div id="howto" style="display:none;visibility:hidden;" class="content" >
    <p>Navigation is through an embeded Google-Map. 
    Cavers are represented by red helmets <img src="../images/icons/caver2.png" alt="" />,
    entries by yellow packs <img src="../images/icons/entry2.png" alt="" /> and 
    grottoes by blue houses <img src="../images/icons/grotto1.png" alt="" />.<br />
    By clicking on any of those pictograms, an info-window appears showing its
    properties (massif, length, depth, etc.).<br />
    For the "entries" (yellow packs), the info-window allow you to access to
    a detailed sheet containing:</p>
    <ul>
      <li>a description of the access to the cave</li>
      <li>a cave description</li>
      <li>a description of rigging and ropes needs</li>
      <li>a reference to any linked web site</li>
      <li>some comments (by cavers) with an evaluation of aestetics etc.</li>
    </ul>
  </div>
  
  <div id="why_s" onclick="JavaScript:switchMe('why');" class="div_switcher_c">
    <ul>
      <li><span class="title">Why are you a key in the <?php echo $_SESSION['Application_name']; ?>'s project?</span></li>
    </ul>
  </div>
  <div id="why" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> works with caver's contributions 
    (so your's), and we count on you to help us complete and rely this database.
    You can access to the creation/modification menu with the left-hand panel
    after you signed in.<br />
    We remind you that you can add any interesting cave to <?php echo $_SESSION['Application_name']; ?>!<br />
    Thanks.</p>
  </div>
  
  <div id="who_s" onclick="JavaScript:switchMe('who');" class="div_switcher_c">
    <ul>
      <li><span class="title">Who are we?</span></li>
    </ul>
  </div>
  <div id="who" style="display:none;visibility:hidden;" class="content" >
    <p>Here is the "GrottoTeam", alphabetically by name:</p>
    <ul>
      <li>Nathan Boinet: ergonomics advisor and functional analyst, tester</li>
      <li>Thomas Cabothiau: ergonomics advisor and functional advisor</li>
      <li>Barbara Guzman: ergonomics advisor and English-Spanish translations</li>
      <li>Vanyo Gyorev : Bulgarian translation.</li>
      <li>Ivan Herbots : Dutch translation.</li>
      <li>Stéphane Lips: ergonomics advisor and functional advisor, tester</li>
      <li>Francesc B. Ricart : Catalan translation.</li>
      <li>Clément Ronzon: designer, ergonomics and functional analyst, English-French translations</li>
      <li>Vincent Routhieau: ergonomics advisor, functional analyst, tester</li>
      <li>Benjamin Soufflet : developer, system administrator</li>
      <li>Norbert Weber : German translation.</li>
      <li>Italian cavers : Italian translation.</li>
    </ul>
    <p>If you want to make your bit, feel free to contact us!</p>
    <p>A big thank to those people and all those who participated
     and contributed to the project!</p>
  </div>
  
  <div id="license_s" onclick="JavaScript:switchMe('license');" class="div_switcher_c">
    <ul>
      <li><span class="title">What are the term of use of the data?</span></li>
    </ul>
  </div>
  <div id="license" style="display:none;visibility:hidden;" class="content" >
    <p><?php echo $_SESSION['Application_name']; ?> was designed in a spirit
    totally apolitical. The site contains elements that constitute a work
    protected by international treaties.</p>
    <p><?php echo getLicense(1); ?></p>
  </div>
  
  <div id="sensitive_s" onclick="JavaScript:switchMe('sensitive');" class="div_switcher_c">
    <ul>
      <li><span class="title">How to add a sensitive cavity?</span></li>
    </ul>
  </div>
  <div id="sensitive" style="display:none;visibility:hidden;" class="content" >
    <p>If you want to register a "sensitive" cavity there are two options
    depending on the degree of protection desired.</p>
    <ul><li>The cavity is easily accessible and you just whant to  <b>avoid
    overcrowding</b> by non-caver people.<br />
    In this case, just chosse the option <b>"Registered (sensitive cave and/or
    regulated access)."</b> when adding the entry.<br />
    In this way the cavity appears only to registered users who are connected.</li>
    <li>The cavity is <b>very sensitive (concretions, archeology, paleontology, etc.)</b>
    and it is essential to avoid access for non-caver people.<br />
    In this case, chosse the option "Registered (sensitive cave and/or
    regulated access).", and it is recommended to <b>distort the coordinates</b> of
    the cavity within a radius of 1 km around its real place and <b>specify</b> in
    the detailed sheet that the cavity is not pointing its exact location and
    should not indicate access.<br />
    In this way the cavers that wish to visit that cave will necessarily
    contact with right peolple/organization concerned by this cavity. This option
    is reserved for exceptional cases.</li></ul>
  </div>
  
  <div id="warning_s" onclick="JavaScript:switchMe('warning');" class="div_switcher_c">
    <ul>
      <li><span class="title" style="color:red;">Warning</span></li>
    </ul>
  </div>
  <div id="warning" style="display:none;visibility:hidden;" class="content" >
    <?php include("description_warning.php"); ?>
  </div>
</div>
<?php
  break;
}
?>