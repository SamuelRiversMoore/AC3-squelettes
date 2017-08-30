<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_rubrique_charger_dist( $id_rubrique = 'new' ){

    if ( $id_rubrique == 'new' )
        return array( 'id_rubrique' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_rubriques" , "id_rubrique=$id_rubrique" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_rubrique_verifier_dist( $id_rubrique = 'new' ){
    $erreurs = array();
    if ( !_request('titre') && !_request('titre_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_rubrique_traiter_dist( $id_rubrique = 'new' , $id_parent = null, $redirection = null, $type = null){

    $objet = "rubrique";
    $set = array();

    if ($id_rubrique == 'new'){
        $id_objet = objet_inserer($objet);
        if (!$type){
          $redirection = parametre_url($redirection, 'rubrique', $id_objet);
        }
        $set['id_parent'] = $id_parent;
      }
    else
        $id_objet = $id_rubrique;

    $set['titre'] = _request('titre');
    $set['titre_en'] = _request('titre_en');

    objet_modifier($objet, $id_objet, $set);
    suivre_invalideur("id='$objet/$id_objet'");

    $retour = array();
    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;

}




?>
