<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_glomot_charger_dist( $id_glomot = 'new' ){

    if ( $id_glomot == 'new' )
        return array( 'id_glomot' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_glomots" , "id_glomot=$id_glomot" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_glomot_verifier_dist( $id_glomot = 'new' ){
    $erreurs = array();
    if ( !_request('titre') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_glomot_traiter_dist( $id_glomot = 'new' , $redirection = null){

    $objet = "glomot";
    $set = array();

    if ($id_glomot == 'new'){
        $id_objet = objet_inserer($objet);
      }
    else
        $id_objet = $id_glomot;

    $set['titre'] = _request('titre');

    objet_modifier($objet, $id_objet, $set);

    suivre_invalideur("id='$objet/$id_objet'");

    // traitements du lien des auteurs
    objet_associer( array('auteur' => $GLOBALS['auteur_session']['id_auteur']), array($objet => $id_objet) );

    $retour = array();
    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;

}




?>
