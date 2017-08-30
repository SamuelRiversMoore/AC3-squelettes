<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_institution_charger_dist( $id_institution = 'new' , $id_rubrique = null, $redirection = null, $annuler = null ){

    if ( $id_institution == 'new' )
        return array( 'id_institution' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    $retour = sql_fetsel( "*" , "spip_institutions" , "id_institution=$id_institution" ); // Renvoi l'ensemble de l'objet
    if ($annuler) $retour['annuler'] = $annuler;
    return $retour;
}

function formulaires_edition_institution_verifier_dist( $id_institution = 'new' ){
    $erreurs = array();
    if ( !_request('nom') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_institution_traiter_dist( $id_institution = 'new' , $id_rubrique = null, $redirection = null){

    $objet = "institution";
    $set = array();

    if ($id_institution == 'new'){
        $id_objet = objet_inserer($objet);
        $set['id_rubrique'] = $id_rubrique;
      }
    else
        $id_objet = $id_institution;

    $set['nom'] = _request('nom');
    $set['description'] = _request('description');
    $set['lien'] = _request('lien');

    objet_modifier($objet, $id_objet, $set);

    // Bug de je ne sais quelle merde spip
    if ($id_rubrique){
      sql_updateq('spip_institutions', array( 'id_rubrique' => intval($set['id_rubrique']) ), "id_institution=$id_objet");
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
