/**
 * sizeUnit( 2048 ) = 2 ko
 * @param {number} size en octets
 * @returns {Object[]} [taille, unité]
 */
function sizeUnit(size)
{
	let unite
	if(size>1048576){
		size = size / 1024 / 1024
		unite = " Mo"
	}else{
		if(size>1024){
			size = size / 1024
			unite = " ko"
		}else{
			unite = " octets"
		}
	}
	return [size,unite]
}
/**
 * Ecrit les infos et les bugs provenants de api_user
 * @param {string} res json
 * @param {array} cibles Liste les cibles utiles
 */
function write_debugs_infos(res,cibles){// utilisée dans les send...
	const debug = document.getElementById("debug")
	let html_debugs = ""
	let html_send = ""
	console.log("Inside write_debugs_infos res :\n",res)

	console.log("res.debugs :\n",res.debugs)
	console.log("type of res.debugs.lenght : ",typeof res.debugs.lenght)

	console.log("res.method :\n",res.method)
	console.log("type of res.method : ",typeof res.method)

	console.log("res.infos :\n",res.infos)
	console.log("type of res.infos.lenght : ",typeof res.infos.lenght)

	if(res.debugs){
		res.debugs.forEach(element => {
			html_debugs = `${html_debugs}<li>${element}</li>`
		});
		html_debugs = `${html_debugs}<br>`
	}
	if(html_debugs === "<br>")	html_debugs = ""
	// res.method = {key : value, key : value...}
	if(typeof res.method  !== "undefined"){
		html_send = "ajax.send(contenu)<br>Contenu :"
		for(const [key, value] of Object.entries(res.method)) {
			html_send = `${html_send}<li>${key} : ${value.slice(0,42)}</li>`
		}
	}
	// console.log("html :\n",html_send)
	debug.innerHTML = html_debugs+html_send
	if(res.infos){
		res.infos.forEach(element => {
			let li = "<li>"+element+"</li>"
			cibles["error"].innerHTML = element
			console.log(cibles["error"])
			console.log(li)
		});
	}

}
/**
 * Get targets form suffixe-item 
 * @param {string} suffixe_item id relatif au doc sélectionné
 * @returns {Object[]}
 */
function getCibles(suffixe_item)
{
	const ciblesTemp = document.querySelectorAll("#"+suffixe_item+" .cible")
	// console.log("Les ciblesTemp inside getCibles :\n",ciblesTemp)

	let cibles ={}
	for(let i =0 ; i<ciblesTemp.length;i++){
		if(ciblesTemp[i].className.split(" ")[1] === "action-del" ||ciblesTemp[i].className.split(" ")[1] === "action-upload") {
			cibles[ciblesTemp[i].className.split(" ")[1]] = ciblesTemp[i].value
		}else{
			cibles[ciblesTemp[i].className.split(" ")[1]] = ciblesTemp[i]			
		}
	}
	console.log("Les cibles inside getCilbes :\n",cibles)
	return cibles
}
export {sizeUnit,getCibles,write_debugs_infos};