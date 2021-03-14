// upload called from mon-profil.php
import {server_name,upload,supprime} from './variables.js';
import {sizeUnit,getCibles} from './functions.js';
import {send_doc,send_delete_doc} from './api_user.js';

// Debut : addEventLisener
document.addEventListener('DOMContentLoaded',create_all_events);
function create_all_events(){ // add event listener
	upload.forEach(element => {
		element.addEventListener('change',ckeck_before_upload_doc);
		// element.addEventListener('change',readPhoto2);
	});
	supprime.forEach(element => {
		element.addEventListener('click',delete_doc);
	});
}
// Fin	: addEventLisener

/**
 * Vérifie la validité des extensions et de la taille before call resize_photo...
 * @param {Object[]} ev event
 */
function ckeck_before_upload_doc(ev) {
	let errors = []
	// let MAX_FILE_SIZE = ev.target.parentElement.children[2].value
	let MAX_FILE_SIZE = 10000000 // env. 10Mo
	let MAX_FILE_SIZE_tab = sizeUnit(MAX_FILE_SIZE)
	let extensions_text = ev.target.accept
	let extensions_tab = extensions_text.split(',')
	let extensions_tab_nim = extensions_tab
	extensions_tab_nim = extensions_tab_nim.map(x => x.toLowerCase().replace("."," ")); //  str.replace(
	extensions_tab_nim = Array.from(new Set(extensions_tab_nim))
	extensions_text = extensions_tab_nim.toString()
	// fullpath = ev.target.value // C:\\fakepath\\huh.gif
	let extension = "."+ev.target.value.split('.').pop();
	// console.log("ev target :\n",ev.target)

	const suffixe_item = ev.target.name // yoga / certif / avatar / licence / danse
	console.log("suffixe item :\n","|"+suffixe_item+"|")

	let cibles = getCibles(suffixe_item)


	if(extensions_tab.indexOf(extension) == -1){
		errors.push("Vous devez choisir l'une de ces extensions : "+extensions_text) // eq php : in_array()
	}
	let size_file =ev.target.files[0].size
	let size_file_tab =sizeUnit(ev.target.files[0].size)

	if (size_file>MAX_FILE_SIZE){
		errors.push("Le fichier est trop gros, il doit être inférieur à "+MAX_FILE_SIZE_tab[0].toFixed(0)+MAX_FILE_SIZE_tab[1]+")")
	}

	if (errors && !errors.length){
		cibles["error"].textContent = "" // errors
		if (typeof variable !== 'undefined'){
			cibles["image"].src = `${server_name}/wp-content/themes/blossom-travel-child/img/spinning-circles.svg`
		}
		// setTimeout(function(){ // pour que le gif de loading apparaisse
		// 	ev.target.parentElement.firstElementChild.click();// click sur submit button => $_POST sur la page courante
		// }, 200);
		console.log("Appel de readPhoto2")
		console.log(cibles)

		resize_photo_before_send(ev,suffixe_item,cibles)
	}else{
		cibles["error"].textContent = "<li>"+errors[0]+"</li>"
	}
}
/**
 * Supprime le document sélectionné
 * @param {Object[]} ev event
 */
function delete_doc(ev){ // supprime le document sélectionné
	ev.preventDefault()
	ev.stopPropagation()
	ev.stopImmediatePropagation()
	// console.log(ev.target.parentElement.parentElement.id)
	const suffixe_item = ev.target.parentElement.parentElement.id
	const action = ev.target.parentElement.firstElementChild.value
	send_delete_doc(suffixe_item,action)
}
/**
 * Resize jpg before call send_doc
 * @param {Object[]} ev event
 * @param {string} suffixe_item 
 * @param {Object[]} cibles 
 */
async function resize_photo_before_send(ev,suffixe_item,cibles) {
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
			// console.log(cibles)

			send_doc(base64Image,suffixe_item,cibles)
			
			// replace_img_by_canvas.removeChild(canvas)
			// cibles["image"].hidden = false;
			URL.revokeObjectURL(img.src)
		}
		img.src = URL.createObjectURL(ev.target.files[0]);
	}else{// Ne fct pas en local car wp ne génère pas de jpg à partir du pdf
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

			send_doc(base64pdf,suffixe_item,cibles)
		};
		// Convert data to base64
		fileReader.readAsDataURL(fileToLoad);
	}
}









