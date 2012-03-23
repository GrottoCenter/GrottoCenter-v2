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
 * @copyright Copyright (c) 2009-2012 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */

// **************************************************************************
// Copyright 2007 - 2008 The JSLab Team, Tavs Dokkedahl and Allan Jacobs
// Contact: http://www.jslab.dk/contact.php
//
// This file is part of the JSLab Standard Library (JSL) Program.
//
// JSL is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// any later version.
//
// JSL is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ***************************************************************************
// File created 2008-10-16 14:54:03

// Return elements which are in A but not in arg0 through argn
Array.prototype.diff =
  function () {
    var a2, a1, a, n, l, l2, diff, i, j;
    a1 = this;
    a = a2 = null;
    n = 0;
    while (n < arguments.length) {
      a = [];
      a2 = arguments[n];
      l = a1.length;
      l2 = a2.length;
      diff = true;
      for (i = 0; i < l; i = i + 1) {
        diff = true;
        for (j = 0; j < l2; j = j + 1) {
          if (a1[i].objectId && a2[j].objectId) {
            if (a1[i].objectId === a2[j].objectId) { //a1[i].getLatLng() === a2[j].getLatLng() && 
              diff = false;
            }
          }
          if (diff) {
            if (a1[i].sId && a1[i].eId && a2[j].sId && a2[j].eId) {
              if (a1[i].sId === a2[j].sId && a1[i].eId === a2[j].eId) {
                diff = false;
              }
            }
          }
          if (!diff) {
            break;
          }
        }
        if (diff) {
          a.push(a1[i]);
        }
      }
      a1 = a;
      n = n + 1;
    }
    return a; //a.unique();
  };

// Compute the intersection of n arrays
Array.prototype.intersect =
  function () {
    var a2, a1, a, n, l, l2, i, j, flag;
    if (!arguments.length) {
      return [];
    }
    a1 = this;
    a = a2 = null;
    n = 0;
    while (n < arguments.length) {
      a = [];
      a2 = arguments[n];
      l = a1.length;
      l2 = a2.length;
      for (i = 0; i < l; i = i + 1) {
        for (j = 0; j < l2; j = j + 1) {
          flag = false;
          if (a1[i].objectId && a2[j].objectId) {
            if (a1[i].objectId === a2[j].objectId) {
              flag = true;
            }
          }
          if (!flag) {
            if(a1[i].sId && a1[i].eId && a2[j].sId && a2[j].eId) {
              if (a1[i].sId === a2[j].sId && a1[i].eId === a2[j].eId) {
                flag = true;
              }
            }
          }
          if (flag) {
            a.push(a1[i]);
            break;
          }
        }
      }
      a1 = a;
      n = n + 1;
    }
    return a; //a.unique();
  };

// Return new array with duplicate values removed
Array.prototype.unique =
  function () {
    var a, l, i, j, flag;
    a = [];
    l = this.length;
    for (i = 0; i < l; i = i + 1) {
      for (j = i + 1; j < l; j = j + 1) {
        flag = false;
        if (this[i].objectId && this[j].objectId) {
          if (this[i].objectId === this[j].objectId) {
            flag = true;
          }
        }
        if (!flag) {
          if (this[i].sId && this[i].eId && this[j].sId && this[j].eId) {
            if (this[i].sId === this[j].sId && this[i].eId === this[j].eId) {
              flag = true;
            }
          }
        }
        // If this[i] is found later in the array
        if (flag) {
          i = i + 1;
          j = i;
        }
      }
      a.push(this[i]);
    }
    return a;
  };

