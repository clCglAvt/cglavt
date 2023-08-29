<!--- 
* Script nécessaire pour gérer infobulle
*
ù
 * Version CAV - 2.8 - hiver 2023 - reassociation BU/LO à un autre contrat

 \file       cglavt/core/js/info_bulle.js
 \brief      Fichier incluant les fonctions js poir info_bulle
 
-->
<style>
	.infobulle{
		position: absolute;   
		visibility : hidden;
		border: 1px solid Black;
		padding: 10px;
		font-family: Verdana, Arial;
		font-size: 10px;
		background-color: #FFFFCC;
	}
</style>
	
<script language="javascript" type="text/javascript">
	<!--
	function GetId(id)
	{
	return document.getElementById(id);
	}
	var i=false; // La variable i nous dit si la bulle est visible ou non
	 
	function move(e) {
	  if(i) {  // Si la bulle est visible, on calcul en temps reel sa position ideale
		if (navigator.appName!="Microsoft Internet Explorer") { // Si on est pas sous IE
		GetId("curseur").style.left=e.pageX + 5+"px";
		GetId("curseur").style.top=e.pageY + 10+"px";
		}
		else { // Modif proposé par TeDeum, merci à  lui
			if(document.documentElement.clientWidth>0) {
				GetId("curseur").style.left=20+event.x+document.documentElement.scrollLeft+"px";
				GetId("curseur").style.top=10+event.y+document.documentElement.scrollTop+"px";
			} else {
				GetId("curseur").style.left=20+event.x+document.body.scrollLeft+"px";
				GetId("curseur").style.top=10+event.y+document.body.scrollTop+"px";
			}
		}
	  }
	}
	 
	function montre(text) {
		if(i==false) {
		  GetId("curseur").style.visibility="visible"; 
		  GetId("curseur").innerHTML = text;
		  i=true;
	  }	  
	}
	function cache() {
		if(i==true) {
		GetId("curseur").style.visibility="hidden"; // Si la bulle est visible on la cache
		i=false;
		}
	}
	document.onmousemove=move; // dès que la souris bouge, on appelle la fonction move pour mettre à jour la position de la bulle.
	//-->
</script>
