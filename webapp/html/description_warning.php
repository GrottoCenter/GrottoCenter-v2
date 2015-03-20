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
    <p><i>Ce site, bien qu'il puisse contenir des informations précises sur des cavités, 
    n'a pas été conçu dans le but d'en faciliter la visite pour un non spéléo. 
    Tout les <b>dangers éventuels</b> ne sont pas forcément signalés et toutes les 
    informations mentionnées ne sont pas vérifiées.</i></p>
    
    <p><i>La spéléologie est une discipline aux multiples facettes : culturelle, 
    scientifique et sportive. À ce dernier titre, elle requiert souvent un engagement 
    physique important. Elle n'est pas non plus exempte de risques, et même de 
    <b>risques majeurs</b>. Ces risques, s'ils ne peuvent être totalement éliminés, sont 
    du moins largement réduits par une pratique dans les règles de l'art, pratique 
    qui s'exerce pleinement dans le cadre d'un club de spéléologie.</i></p>
    
    <p><i>Le site <?php echo $_SESSION['Application_name']; ?>, ses représentants et ses 
    contributeurs ne peuvent être tenus pour responsable d'une mauvaise pratique de la 
    spéléologie ou pour tout accident ou dégradation qui pourrait survenir dans 
    les grottes présentées sur ce site web.</i></p>
    
    <p><i>Le lecteur néophyte qui souhaiterait s'engager dans une exploration 
    souterraine, est <b>vivement encouragé à se mettre en rapport avec un club 
    spéléo</b> de sa région, dont la liste et les coordonnées peuvent être obtenues 
    entre autre auprès de l'UIS (Union Internationale de Spéléologie), de 
    <?php echo $_SESSION['Application_name']; ?> ou de votre fédération 
    nationale.</i></p>
    
    <!--p><b>Droit d'auteur : L'utilisation des informations présentes sur cette page
    autre que pour un usage privé non commercial est strictement interdite sans
    l'autorisation des auteurs.</b></p-->
<?php
  break;
  case 'En':
?>
    <p><i>This site, although it may contain detailed information on caves,
     was not designed to facilitate the visit for none caver people.
     <b>Dangers</b> are not necessarily reported and information mentioned is
     not everytime verified.</i></p>
    
    <p><i>Caving is a discipline with many facets: cultural,
     science and sports. In the latter capacity, it often requires a physical 
     commitment. It is also not free of risks, and even <b>major risks</b>.
     These risks, if they can not be completely eliminated, are
     at least greatly reduced by a practice in the rules, practice
     which is fully understood when under a caving club (grotto).</i></p>
    
    <p><i><?php echo $_SESSION['Application_name']; ?>, its representatives and 
    its contributors cannot be held liable for a bad practice of caving, or for 
    any accident or deterioration that might occur in the caves displayed on this web site.</i></p>
    
    <p><i>The novice who would engage in underground exploration <b>is 
    urged to contact a caving club</b>. The names and contact information can be
    obtained among other to the IUS (International Union of Speleology), to 
    <?php echo $_SESSION['Application_name']; ?> or to his national federation.
    </i></p>
    
    <!--p><b>Copyright: The use of information contained on this page other than for
    private non-commercial use is strictly prohibited without permission from the authors.
    </b></p-->
<?php
  break;
  case 'Es':
?>
    <p><i>Este sitio, aunque puede contener información específica, no fue
    diseñado para facilitar las visitas de las grutas por los neófitos.
    No se informa necesariamente de todos los <b>riesgos</b> y todas las
    informaciones contenidas en este sitio no son necesariamente verificadas.</i></p>
    
    <p><i>La espeleología es una disciplina con múltiples facetas: cultural,
    científica y deportiva. En esa capacidad, a menudo se requiere un compromiso
    físico. Existen ciertos riesgos, que pueden convertirse en <b>riesgos mayores</b>,
    si no se práctican las normas necesarias para visitar una gruta. Las visitas
    son más seguras cuando se realiza con un club de espeleología.</i></p>
    
    <p><i>El sitio <?php echo $_SESSION['Application_name']; ?>, sus representantes 
    y sus contribuidores no pueden ser considerados responsables de una mala práctica 
    de la espeleología o de cualquier accidente o deterioración que pudiera ocurrir en 
    las cavidades presentadas en este sitio web.</i></p>
    
    <p><i>Si existe un neófito que quisiera involucrarse en una exploración
    subterránea se le <b>recomienda a comunicarse con un club de espeleología</b>
    de su región. Los nombres e información de contacto se puede obtener,
    entre otros, con la UIE (Unión Internacional de Espeleología ),
    <?php echo $_SESSION['Application_name']; ?> o con su federación nacional
    de espeleología.</i></p>
    
    <!--p><b>Copyright: El uso de la información contenida en esta página otro que
    privado y no comercial está estrictamente prohibida sin el permiso de los autores.
    </b></p-->
<?php
  break;
  case 'De':
?>
    <p><i>Auch wenn diese Seite präzise Informationen über die Höhlen enthält,
    ist  es ausdrücklich nicht das Ziel der Betreiber, Nicht-Späleologen den 
    Besuch von Höhlen zu erleichern. Alle hier genannten Informationen sind
    nicht zwangsweise überprüft, mögliche Gefahren, die beim Höhlenbesuch 
    bestehen, müssen nicht unbedingt genannt sein.</i></p>
    
    <p><i>Die Höhlenforschung hat viele Facetten, sie umfasst kulturelle,
    wissenschaftliche und auch sportlichen Aktivitäten. Im Zuge dessen ist häufig
    eine beträchtliche körperliche Leistung notwendig. Der Besuch von Höhlen
    ist stehts mit gewissen <b>Risiken</b> verbunden. Auch wenn sich diese nie
    völlig ausschließen lassen, werden sie doch deutlich reduziert, wenn du dich
    unter Tage an die anerkannten Regeln hälts und dein Hobby vorzugsweise
    im Rahmen eines entsprechenden Vereines ausübst.</i></p>
    
    <p><i>Die Webseite <?php echo $_SESSION['Application_name']; ?>, dessen 
    Vertreter und die Mitwirkenden lehnen jede Verantwortung ab für 
    Unfälle und Schäden die durch unfachgemässe Ausübung der 
    Höhlenforschung in den auf der Webseite präsentierten Höhlen vorkommen 
    könnten.</i></p>
    
    <p><i>Dem Neuling in Sachen Höhlenforschung  raten wir dringend, 
    sich mit einem Verein in Verbindung zu setzen. In Deutschland findest
    du die Adressen über den Verein Höhlen- und Karstforschung Deutschland e. V.
    (www.VHKD.de), in Frankreich über die Féderation Française de Spéléologie
    (ffspeleo.fr). International hilft die UIS 
    (Union Internationale de Spéléologie).</i></p>
    
    <!--p><b>Copyright: Die Verwendung von Informationen auf dieser Seite mit Ausnahme
    der privaten nicht enthalten kommerzielle Nutzung ist ohne Erlaubnis der
    Autoren verboten.
    </b></p-->
<?php
  break;
  case 'Pl':
?>

<?php
  break;
  case 'Ar':
?>

<?php
  break;
  case 'Bg':
?>
<p><i>Този сайт, въпреки че може да съдържа детайлна информация за пещерите,
не е замислен да улесни посещението за хората, които не са пещерняци.
<b>Опасностите</b> не са задължително описани, а предоставената информация
не винаги е проверена.</i></p>

<p><i>Пещернячеството е дисциплина, с много аспекти: културни,
научни и спортни. В последния случай, често се изисква сериозно физическо
натоварване. Съществуват различни рискове, които може да са<b>изключително опасни </b>.
Тези рискове, ако не могат да бъдат напълно премахнати, то
най-малко значително намаляват с практика и обучение в правилата, способите и 
техника за безопастност, преподавани в клубовете по пещерно дело (grotto).</i></p>

<p><i>В всеки случай <?php echo $_SESSION['Application_name']; ?>Екипа
не носи отговорност за лошата практика в пещерното дело.</i></p>

<p><i>За начинаещ, който ще участва в подземно проучване в друга държава <b>е
задължително да се свърже с местен пещерен клуб</b>. Имената на клубовете и контактна информация в
може да се намери в сайта на IUS (International Union of Speleology), в 
<?php echo $_SESSION['Application_name']; ?> или друга национална федерация.
</i></p>

<?php
  break;
  case 'Nl':
?>
    <p><i>Deze site en de informatie hierin gegeven is niet bedoeld om het bezoek voor niet-speleologen te vergemakkelijken. De <b>gevaren</b> verbonden aan de grotten zijn niet noodzakelijkerwijs gemeld en de vermelde informatie is niet iedere keer gecontroleerd.</i></p>
    
    <p><i>Speleologie is een discipline met vele facetten: culturele, wetenschappelijke en sportieve.  Een grotbezoek  vereist vaak een fysiek engagement, een gedegen kennis van speleologisch technieken en is ook <b>niet vrij van risico's</b>.</i></p>
    
    <p><i>Deze risico’s, alhoewel nooit volledig uit te sluiten, kunnen worden beperkt door het strikt toepassen van  veiligheidsregels en het volgen van degelijke opleidingen verzorgd door de verschillende nationale speleologische federaties van de European Speleological Federation. 
In ieder geval is <?php echo $_SESSION['Application_name']; ?> en haar team niet aansprakelijk voor een slechte praktijk van de speleologie.
    </i></p>
    
    <p><i>De beginner die wil kennis maken met ondergrondse exploratie wordt dringend verzocht om contact op te nemen met een speleologie federatie of een speleologie club. Voor België – Vlaanderen : www.speleovvs.be en voor Nederland : www.speleo.nl.  De contactgegevens voor andere landen zijn te verkrijgen bij de European Speleological Federation op http://www.eurospeleo.eu/en/ en de IUS (International Union of Speleologie) of bij <?php echo $_SESSION['Application_name']; ?>.
    </i></p>
<?php
  break;
  case 'Ro':
?>

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
}
?>