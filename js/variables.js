
let res = window.location.href.split(":");
let server_name
if(res[0]==="https"){
	server_name = "https://www.spicylotus.fr"
}else{
	server_name = "http://localhost/spicylotus"
}
const upload = document.querySelectorAll(".get_upload"); // get all input type='file' from dom
const supprime = document.querySelectorAll("p.supprime"); // get all p.supprime from dom

export {server_name,upload,supprime}