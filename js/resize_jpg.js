// source readPhoto : https://stackoverflow.com/questions/49759386/resize-image-in-the-client-side-before-upload
// source readPhoto2 : https://stackoverflow.com/questions/49759386/resize-image-in-the-client-side-before-upload

// php convert base64 image en png, source :
// https://www.damienflandrin.fr/blog/post/comment-convertir-du-base64-en-image-et-lenregistrer-en-php

import {server_name} from './variables.js'; // ex : server = https://www.spicylotus.fr
import {write_debugs_infos} from './functions.js';

async function readPhoto2(ev,suffixe_item,cibles) {

	// const replace_img_by_canvas = ev.target.parentElement.parentElement.children[0]
	// console.log("ev.target.pE = <p.form-upload> : ",ev.target.parentElement)
	// console.log("ev.target.pE.pE.child[0] = <p.form-> : ",ev.target.parentElement.parentElement.children[0]) // p > img.src = svg pdt loading
// Dimensions max alouées à l'img
	const maxWidth = 1260 // équivaut à une résolution de 150dpi pour 210mm de large (format A4)
	const maxHeight = 1782 // 150dpi pour 297mm
	console.log("Target :\n",ev.target)
	const files = ev.target.files // files.lenght = 1
	// console.log("Type mime :\n",files[0].type)
	if(files[0].type !== "application/pdf" && files[0].type !== "image/gif"){
		var canvas = document.createElement('canvas')
		var ctx = canvas.getContext('2d') // ctx = canvasContext
		var img = new Image;   // Create new img element

		img.onload = function() {
		// calculate ration and new size
		const ratio = Math.min(maxWidth / img.width, maxHeight / img.height)
		const width = img.width * ratio
		const height = img.height * ratio
		// resize the canvas to the new dimensions
		canvas.width  = width;
		canvas.height = height;
		// Début étape de mis au point : Permet de visualiser l'image avant l'envoie
			// replace_img_by_canvas.appendChild(canvas);
		// hidden img.src = svg (loading)
			// clibles[0].style.display = "none";
		// FIN étape de mis au point

			// s = source / d = destination : 
			// ctx.drawImage(image, sx, sy, sLargeur, sHauteur, dx, dy, dLargeur, dHauteur);		
			ctx.drawImage(img, 0, 0, width, height);
			var base64Image = canvas.toDataURL(files[0].type, 0.8)
			// console.log("jpeg ou png / base64 :\n",base64Image)
			// Post to server
			sendImage(base64Image,suffixe_item,cibles)
			
			// replace_img_by_canvas.removeChild(canvas)
			// cibles["image"].hidden = false;
			URL.revokeObjectURL(img.src)
		}
		img.src = URL.createObjectURL(ev.target.files[0]);
	}else{
		var fileToLoad = files[0];
		// FileReader function for read the file.
		var fileReader = new FileReader();
		var base64pdf;
		// Onload of file read the file content
		fileReader.onload = function(fileLoadedEvent) {
			base64pdf = fileLoadedEvent.target.result;
			// Print data in console
			// console.log("pdf ou gif / base64 :\n",base64pdf);
			// le pdf sera affiché en fin d'upload dans sendImage

			sendImage(base64pdf,suffixe_item,cibles)
		};
		// Convert data to base64
		fileReader.readAsDataURL(fileToLoad);
	}
}
function sendImage(base64Image,suffixe_item,cibles) {

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
	ajax.onreadystatechange = function() {
		if (ajax.readyState === XMLHttpRequest.DONE) {
			if (ajax.status === 200) {
				// Success
				// ajax.response = {"infos-debug":[],"infos-user":[],"???": ??? }
				let res= JSON.parse(ajax.response)
				// res = Object { "infos-debug": [], "infos-user": [], "???": ??? }
				console.log("Succes")
				// console.log("JSON.parse(Ajax.response) :\n",res)

				if(typeof res.src !== "undefined"){
					cibles["image"].style.visibility = "visible"
					cibles["image"]  .hidden = false
					cibles["noImage"].hidden = true
					cibles["supprimer"].hidden = false
					// cibles["noImage"].style.visibility = "hidden"
					cibles["image"].setAttribute("src",res.src);
					if(typeof cibles["status"] !== "undefined"){// => suffixe_item = "monAvatar"
						cibles["status"].removeAttribute("class")
						cibles["status"].setAttribute("class", "cible status en attente");
						cibles["status"].textContent = "statut du document : en attente"
					}
				}
				write_debugs_infos(res,cibles)
			}else{
				// Fail (en cas d'erreur sur api_user.php)
				console.log("Fail")
			}
		}
	};

	ajax.send(`base64=${base64Image}&token=${token}&current_user_ID=${current_user_ID}&action=${cibles["action-upload"]}&suffixe_item=${suffixe_item}`);

}

export {readPhoto2}; // a list of exported variables
