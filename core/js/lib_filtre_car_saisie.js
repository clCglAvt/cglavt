
// lib_filtre_car_saisie - Javascript libraries for Dolibarr ERP CRM (https://www.dolibarr.org)



/**
 * Pour interdiction de la saisie de caractères
 */

// Aucun retour
// modifie le champ de saisie
function verifierCaracteres(event) {
	 		
	var keyCode = event.which ? event.which : event.keyCode;
	var touche = String.fromCharCode(keyCode);
			
	var champ = document.getElementById('mon_input');
			
	var caracteres_interdits = '"';
			
	if(caracteres_interdits.indexOf(touche) == -1) {
		champ.value += touche;
	}			
}


/**
 * Pour remplacement  de caractères par un autre
 */
// Aucun retour
// modifie le champ de saisie
function remplaceGuillements(o, event) {
	 		
	var keyCode = event.which ? event.which : event.keyCode;
	var touche = String.fromCharCode(keyCode);
	var CaracteresInterdits = '"';	
	//var champ = document.getElementById(id_input);
	if(CaracteresInterdits.indexOf(touche) != -1) 
		o.value += '~';
	else
		o.value += touche;
}


/**
 * Pour remplacement  de caractères par un autre sur un copier/coller
 */
// Aucun retour
// modifie le champ de saisie
function remplaceToutGuillements(o, event) {
	let chaine = o.value;
	let chaineneuve = '';
	for (i = 0; i < chaine.length; i++)
	{
		if (chaine.charAt(i) == '"')
			chaineneuve += '~';
		else 
			chaineneuve += chaine.charAt(i) ;
	}
	o.value = chaineneuve	;

}

// End of lib_filtre_car_saisie.js.php

