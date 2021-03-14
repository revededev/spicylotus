import {server_name} from './variables.js';

console.log("sever name : ", server_name)

function updateUser(ev){
    ev.preventDefault();
    ev.stopPropagation();
    ev.stopImmediatePropagation();

    formSeeUser.classList.remove("displayON");
    gestion.removeAttribute("hidden","");
    const user_id = document.getElementById("id").textContent
    // ev.target.parentElement.children[1].textContent;// id de l'adhérent

    for (const key of list_statut_keys) {
            console.log(key);
/*= init all statut ================================================================================*/
            let query = "input[name="+ key +"]:checked";
            if (document.querySelector(query)){
                list_statut[key] = document.querySelector(query).value;
            }else{
                list_statut[key] = 'noChange';
            }
/*===================================================================================================*/
    };
    // console.log(list_statut);

    if (isNaN(user_id)) {
      formSeeUser.textContent = "";
      return;
    } else {
        const ajax = new XMLHttpRequest();
        // wp_enqueue_style( 'style-footer',get_stylesheet_directory_uri().'/
        const adress = server_name+"/wp-content/themes/blossom-travel-child/include/api_user.php";
        

        ajax.open("POST", adress, true);
        ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        ajax.addEventListener("readystatechange", function() {
            if (ajax.readyState === XMLHttpRequest.DONE /* = 4 */) {
                if (ajax.status === 200) {
                    display_errors = document.getElementById("display_errors");
                    display_errors.innerHTML = ajax.responseText
                    // console.log("RéponseText :\n",ajax.responseText)

                    while (formSeeUser.firstChild) {
                        formSeeUser.removeChild(formSeeUser.firstChild);
                    }
                    // console.log("user_id :\n",user_id)
                    let go_id = 'numID-' +user_id;
                    // console.log("go ID :\n",go_id)
                    let cible = document.getElementById(go_id).parentElement;
                    // for (const [key, value] of Object.entries(list_statut)) {
                    //     console.log(`${key}: ${value}`);
                    const list_statut_values = Object.values(list_statut);
                    // console.log("list_statut_values")
                    // console.log(list_statut_values)
                    // console.log(list_statut_values[0])

                    for (let i=0;i<list_statut_values.length;i++) {
/*================== Update affichage statut sur la page====================================================*/
                        let j=i+2
                        console.log("cible :\n",cible)
                        if (list_statut_values[i] === 'valide' || list_statut_values[i] === 'non valide'){
                            // console.log(list_statut_values[i])
                            cible.children[j].textContent = list_statut_values[i];
                            cible.children[j].removeAttribute('class');
                            cible.children[j].setAttribute("class","petit "+list_statut_values[i]);
/*==========================================================================================================*/
                        }
                    }
                } else {
                     alert("Une erreur est survenue.");
                }
            }
        })
/*================== Update statut base de données ====================================================*/
        // console.log("check :\n")
        // console.log("current_user_ID PROFIL = ",encodeURIComponent(current_user_ID))
        // console.log("user_id GET = ",encodeURIComponent(user_id))

        // console.log("statut certif    = ",list_statut["statut_certif"])
        // console.log("statut assurance = ",list_statut["statut_assurance"])
        // console.log("statut licence   = ",list_statut["statut_licence"])
        // console.log("statut yoga      = ",list_statut["statut_yoga"])
        // console.log("token = ",encodeURIComponent(token))

        ajax.send(
            "user_id="+ encodeURIComponent(user_id) +"&"+
            "statut_certif="+ encodeURIComponent(list_statut["statut_certif"]) +"&"+
            "statut_assurance="+encodeURIComponent(list_statut["statut_assurance"]) +"&"+
            "statut_licence="+encodeURIComponent(list_statut["statut_licence"]) +"&"+
            "statut_yoga="+encodeURIComponent(list_statut["statut_yoga"]) +"&"+
            "current_user_ID="+encodeURIComponent(current_user_ID) +"&"+
            "action=update_status&token="+encodeURIComponent(token)
        );
    }
}


function showUser(ev) {
    ev.preventDefault();
    ev.stopPropagation();
    ev.stopImmediatePropagation();
    let user_id = ev.target.textContent;
    console.log("current ID :\n",document.getElementById("current_user_ID"))
    formSeeUser.setAttribute("class","displayON");
    gestion.setAttribute("hidden","");

    console.log("user_id : ",user_id)

    if (isNaN(user_id)) {
        formSeeUser.textContent = "";
      return;
    } else {
        const ajax = new XMLHttpRequest();
        const adress = server_name+"/wp-content/themes/blossom-travel-child/include/api_user.php";
        console.log(adress)
        ajax.open("GET", adress+'?user_id='+user_id+'&action=get_user_action&token='+token+'&current_user_ID='+current_user_ID, true);
        ajax.addEventListener("readystatechange", function() {
            if (ajax.readyState === XMLHttpRequest.DONE /* = 4 */) {
                if (ajax.status === 200) {
                    let res = ajax.responseText;
                    // console.log(res)
                    formSeeUser.innerHTML = res;

                    // laisse le temps du chargement de "formSeeUser.innerHTML" l'instruction précédente
                    setTimeout(()=>{
                        document.getElementById("close").addEventListener("click",updateUser);
                    }, 300);

                } else {
                     alert("Une erreur est survenue.");
                }
            }
        })
        ajax.send();
    }
}

const formSeeUser = document.getElementById("seeUser");
const gestion = document.getElementById("gestion");
const listes = document.querySelectorAll("li[id^='numID-']");
const list_statut = {"statut_assurance":"","statut_certif":"","statut_licence":"","statut_yoga":""}
const list_statut_keys = Object.keys(list_statut);

// token & id du user connecté (de l'admin)
const token = document.getElementById("token_profil").value
const current_user_ID = document.getElementById("current_user_ID").value


for (let i=0;i<listes.length;i++){
    listes[i].addEventListener("click",showUser);
}