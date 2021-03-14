import {server_name} from './variables.js';
import {getCibles,write_debugs_infos} from './functions.js';

/**
 * Post les paramètres vers api_user.php
 * @param {string} base64Image 
 * @param {string} suffixe_item
 */
function send_doc(base64Image,suffixe_item,cibles) {

	// let response = document.getElementById("response")
	const token = document.getElementById("token_profil").value
	const current_user_ID = document.getElementById("current_user_ID").value
	// console.log("ID = ",current_user_ID)
	// console.log("Token = ",token)
	// console.log("Base64:\n",base64Image)
	var ajax = null;
	if (window.XMLHttpRequest) {
		ajax = new XMLHttpRequest();
	}else if (window.ActiveXObject) {
		ajax = new ActiveXObject("Microsoft.XMLHTTP");
	}
	const adress = server_name+"/wp-content/themes/blossom-travel-child/include/api_user.php";

	ajax.open('POST', adress,true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded")
	ajax.onreadystatechange = () => {
		if (ajax.readyState === XMLHttpRequest.DONE) {
			if (ajax.status === 200) {
				// Success
				// ajax.response = {"infos-debug":[],"infos-user":[],"???": ??? }
				let res = JSON.parse(ajax.response);
				// res = Object { "infos-debug": [], "infos-user": [], "???": ??? }
				console.log("Succes");
				// console.log("JSON.parse(Ajax.response) :\n",res)
				if (typeof res.src !== "undefined") {
					cibles["image"].style.visibility = "visible";
					cibles["image"].hidden = false;
					cibles["noImage"].hidden = true;
					cibles["supprimer"].hidden = false;
					// cibles["noImage"].style.visibility = "hidden"
					cibles["image"].setAttribute("src", res.src);
					if (typeof cibles["status"] !== "undefined") { // => suffixe_item = "monAvatar"
						cibles["status"].removeAttribute("class");
						cibles["status"].setAttribute("class", "cible status en attente");
						cibles["status"].textContent = "statut du document : en attente";
					}
				}
				write_debugs_infos(res, cibles);
			} else {
				// Fail (en cas d'erreur sur api_user.php)
				console.log("Fail");
			}
		}
	};
	// console.log(cibles)
	ajax.send(`base64=${base64Image}&token=${token}&current_user_ID=${current_user_ID}&action=${cibles["action-upload"]}&suffixe_item=${suffixe_item}`);

}
/**
 * Post les paramètres vers api_user.php
 * @param {string} suffixe_item 
 * @param {string} action 
 */
function send_delete_doc(suffixe_item,action) {

	const token = document.getElementById("token_profil").value
	const current_user_ID = document.getElementById("current_user_ID").value
	let cibles = getCibles(suffixe_item)

	var ajax = null;
	if (window.XMLHttpRequest) {
		ajax = new XMLHttpRequest();
	}else if (window.ActiveXObject) {
		ajax = new ActiveXObject("Microsoft.XMLHTTP");
	}
	const adress = server_name+"/wp-content/themes/blossom-travel-child/include/api_user.php";

	ajax.open('POST', adress,true);
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded")
	ajax.onreadystatechange = function() {
		if (ajax.readyState === XMLHttpRequest.DONE) {
			if (ajax.status === 200) {
				// Success
				// ajax.response = {"infos-debug":[],"infos-user":[],"delete": ?done? }
				let res= JSON.parse(ajax.response)
				console.log("Succes")
				console.log("JSON.parse(Ajax.response) :\n",res)
				// res = Object { "infos-debug": [], "infos-user": [], "delete": ??? }
				write_debugs_infos(res,cibles)
				console.log(res)

				if(res.is_delete === true ){
					cibles["image"].style.visibility = "hidden"
					cibles["image"]  .hidden = true
					cibles["noImage"].hidden = false
					// cibles["noImage"].style.visibility = "hidden"
					cibles["supprimer"].hidden = true
					// cibles["image"].style.visibility = "hidden"
					// cibles["noImage"].style.visibility = "visible"
					cibles["image"].setAttribute("src","");
					if(typeof cibles["status"] !== "undefined"){// => suffixe_item = "monAvatar"
						cibles["status"].removeAttribute("class")
						cibles["status"].setAttribute("class", "cible status non fourni ")
						cibles["status"].textContent = "statut du document : non fourni"
					}
				}else{
					console.log("Le document n'a pas été effacé.")
				}

			}else{
				// Fail
				console.log("Fail")
			}
		}
	};
	ajax.send(`token=${token}&current_user_ID=${current_user_ID}&action=${action}&suffixe_item=${suffixe_item}`);
}
export {send_doc,send_delete_doc};
