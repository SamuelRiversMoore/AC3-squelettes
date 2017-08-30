<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_personne_charger_dist( $id_personne = 'new' ){

    if ( $id_personne == 'new' )
        return array( 'id_personne' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_personnes" , "id_personne=$id_personne" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_personne_verifier_dist( $id_personne = 'new' ){
    $erreurs = array();
    if ( !_request('nom') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_personne_traiter_dist( $id_personne = 'new' , $id_rubrique = null, $redirection = null){

    $objet = "personne";
    $set = array();

    if ($id_personne == 'new'){
        $id_objet = objet_inserer($objet);
        $set['id_rubrique'] = $id_rubrique;
      }
    else
        $id_objet = $id_personne;

    $set['nom'] = _request('nom');
    $set['fonction'] = _request('fonction');
    $set['societe'] = _request('societe');
    $set['pays'] = _request('pays');
    $set['email'] = _request('email');

    objet_modifier($objet, $id_objet, $set);

    // Bug de je ne sais quelle merde spip
    if ($id_rubrique){
      sql_updateq('spip_personnes', array( 'id_rubrique' => intval($set['id_rubrique']) ), "id_personne=$id_objet");
    }

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
