const deleteMyProfil = document.getElementById("deleteMyProfil");
deleteMyProfil.addEventListener("click",confirmer)
function confirmer(ev) {
    ev.preventDefault()
    ev.stopPropagation()
    ev.stopImmediatePropagation()
    if (confirm("Etes-vous sûr de vouloir supprimer votre compte ?")) {
      ev.target.parentElement.firstElementChild.click()
    }
  }