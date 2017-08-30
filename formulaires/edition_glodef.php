<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_glodef_charger_dist( $id_glodef = 'new' , $id_glomot = null, $redirection = null, $annuler = null ){

    if ( $id_glodef == 'new' )
        return array( 'id_glodef' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    $retour = sql_fetsel( "*" , "spip_glodefs" , "id_glodef=$id_glodef" ); // Renvoi l'ensemble de l'objet
    if ($annuler) $retour['annuler'] = $annuler;
    return $retour;
}

function formulaires_edition_glodef_verifier_dist( $id_glodef = 'new' , $id_glomot = null, $redirection = null ){
    $erreurs = array();
    if ( !_request('definition') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_glodef_traiter_dist( $id_glodef = 'new' , $id_glomot = null, $redirection = null){

    $objet = "glodef";
    $set = array();

    if ($id_glodef == 'new'){
        $id_objet = objet_inserer($objet);
        objet_associer( array($objet => $id_objet), array('glomot' => $id_glomot) );
      }
    else
        $id_objet = $id_glodef;

    $set['definition'] = _request('definition');

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
