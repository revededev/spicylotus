# spicylotus
Sauvegarde du thème enfant de spicylotus, site web : https://www.spicylotus.fr

Deux espaces sont disponibles :
- Mon-profil => Permet aux adhérents de télécharger leurs documents et de modifier leurs coordonnées. Ils pourront aussi supprimer leur compte
- Gestion    => Permet à l'un des administrateurs du site web de générer une liste d'adhérents, d'en sélectionner un et de donner un avis sur la validation de leurs documents



1. mon-profil

A/ Initialement toutes les modifications se faisaient en rechargeant la page :
  - les 4 documents + l'avatar
  - Les coordonnées de l'utilisateur
  - La suppression du compte utilisateur
  
B/ Désormais les 4 documents et l’avatar se modifient en ajax, ce qui permet un resize des jpg en Javascript avant l'envoie.
Pour les documents et l'avatar, un token accompagne la requête ajax

Ligne 128, case "update-user-docs": ne sert plus

La modification des coordonnées et la suppression de l'utilisateur sont fonctionnelles mais non sécurisées
Elles se font toujours en rechargeant la page



2. Gestion se trouve dans functions.php, lignes 170 à 345 : validation_page_html()

A/ Changer tous les statuts "valide" par "obsolete"
Chaque année, en septembre, le statut des documents valides doit être remis à zéro pour permettre aux adhérents d'envoyer de nouveaux documents
Cette fonctionnalité n'a pas été développée

B/ Générer une liste d'adhérents
=> Pas de token

C/ Sélectionner un adhérent
=> Présence d'un token

D/ Affichage des documents à verifier, puis validation (ou non) de chaque document par l'administrateur
=> Présence d'un token


Il resterait :

- Gestion : Créer la fonctionnalité concernant le changement du statut de tous les documents à "obsolète"
- Gestion : Ajout d'un token pour la création de la liste des adhérents
- Gestion, à l'affichage des propriétés de l'adhérent sélectionner : Ajouter la possibilité de modifier le statut d'un document quan il a été "validé"

- Mon-profil : Transmission des coordonnées en ajax
- Mon-profil : Sécurisation de la modification des coordonnées et de la suppression du compte (token, id...)
