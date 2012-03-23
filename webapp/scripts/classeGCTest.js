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

/*==============================================================================
                            Exemple d'implémentation
                            ------------------------

on dispose dans le formulaire oForm d'un champ oChamp de type text qui doit etre
de valeur positive ou nulle.
Si ce test n'est pas realisé, alors le formulaire ne doit pas etre soumis et le 
message d'erreur "oChamp doit etre positif ou null." doit apparaitre.

Implementation :
----------------

//Dans le header de la page contenant le formulaire oForm :
<script type="text/javascript" src="../scripts/classeGCTest.js"></script>


//Au test du formulaire oForm :
//Creation du message :
var message;
message = "oChamp doit etre positif ou null.";
//Creation du test :
createTest(oChamp.name, oChamp.value, 0, ">=", message, true);
//Repeter les deux dernieres lignes de code autant de fois que de tests a realiser


//Soumission du formulaire oForm (event est le paramètre passé lors de l'appel de
la fonction) :
event.returnValue = testForm();

Explications :
--------------
Si un test bloquant n'est pas verifié alors le formulaire ne sera pas soumis et
un message apparaitra a l'utilisateur.
Si un test non-bloquant n'est pas vérifié alors le formulaire sera soumis et un
message d'avertissement apparaitra a l'utilisateur.
==============================================================================*/

//Collection de GCTests
var GCTestCollection = [];

//Methode estOK de la classe GCTest
function estOk() {
  var retour;
  retour = false;
  switch (this.comparateur) {
  case "==":
    if (this.valeur === this.reference) {
      retour = true;
    }
    break;
  case "!=":
    if (this.valeur !== this.reference) {
      retour = true;
    }
    break;
  case ">=":
    if (this.valeur >= this.reference) {
      retour = true;
    }
    break;
  case "<=":
    if (this.valeur <= this.reference) {
      retour = true;
    }
    break;
  case ">":
    if (this.valeur > this.reference) {
      retour = true;
    }
    break;
  case "<":
    if (this.valeur < this.reference) {
      retour = true;
    }
    break;
  case "has":
    if (this.valeur.indexOf(this.reference) !== -1) {
      retour = true;
    }
    break;
  case "hasNot":
    if (this.valeur.indexOf(this.reference) === -1) {
      retour = true;
    }
    break;
  case "isTrue":
    if (this.valeur) {
      retour = true;
    }
    break;
  case "isFalse":
    if (!this.valeur) {
      retour = true;
    }
    break;
  case "testRegExp":
    if (new RegExp(this.reference,"gi").test(this.valeur)) { //Decimal : ^[0-9]+[.,]*[0-9]*$
      retour = true;
    }
    break;
  //On peut rajouter des comparaisons au besoin
  default:
    retour = false;
    break;
  }
  return retour;
}

//Fonction de test groupé
function testForm() {
  var message, soumettre, i;
  //Message a afficher dans le cas ou l'un des tests ne serait pas vérifié :
  message = "";
  //Variable retournee par la fonction, par defaut on peut soumettre le formulaire :
  soumettre = true;
  //Pour chaque objet de la collection :
  for (i = 0; i < GCTestCollection.length; i = i + 1) {
    //Si le test n'est pas valide :
    if (!GCTestCollection[i].estOk()) {
      //On ajoute le message correspondant :
      message = message + GCTestCollection[i].message + "\n";
      //Si le test doit bloquer la soumission du formulaire :
      if (GCTestCollection[i].estBloquant) {
        //La valeur de retour devient fausse :
        soumettre = false;
      }
    }
  }
  //S'il faut delivrer un message :
  if (message !== "" && message !== undefined) {
    if (!soumettre) {
      alert(message);
    } else {
      alert(message);
    }
  }
  //Reset la collection
  GCTestCollection = [];
  //Retourne le resultat
  return soumettre;
}

//Constructeur de la classe GCTest
function GCTest(id, nomObjet, valeur, reference, comparateur, message, estBloquant) {
  this.id = id;
  this.nom = nomObjet;
  this.valeur = valeur;
  this.reference = reference;
  this.comparateur = comparateur;
  this.message = message;
  this.estBloquant = estBloquant;
  this.estOk = estOk;
}

//Creation d'un nouvel element dans la collection
function createTest(nomObjet, valeur, reference, comparateur, message, estBloquant) {
  var id, test;
  //Identifiant de l'objet dans la collection (correspond a son index dans celle-ci) :
  id = GCTestCollection.length;
  test = new GCTest(id, nomObjet, valeur, reference, comparateur, message, estBloquant);
  //Ajout de l'objet a la collection :
  GCTestCollection.push(test);
}