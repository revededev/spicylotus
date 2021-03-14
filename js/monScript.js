function color_H3(ev) {
    // mettre une class ds H3 est plus long que de faire comme ça :
    let cible = ev.target.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.children[0].children[0].firstElementChild.firstElementChild
    cible.classList.add("hover");
}
function unColor_H3(ev) {
    let cible = ev.target.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.children[0].children[0].firstElementChild.firstElementChild
    cible.classList.remove("hover");
}

function imgEvent(){
    const sliders = document.querySelectorAll(".swiper-slide-image")
    for(slider of sliders){
    slider.addEventListener("mouseenter",color_H3)
    slider.addEventListener("mouseleave",unColor_H3)
    slider.addEventListener("touchstart",color_H3)
    slider.addEventListener("touchend"  ,unColor_H3)
    }
}
// ================================================================================================================ 

// charge le script qd la page est chargée (appel de imgEvent)
const doc = document.addEventListener('DOMContentLoaded',imgEvent)