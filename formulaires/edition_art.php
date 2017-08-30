<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_art_charger_dist( $id_article = 'new', $id_rubrique, $redirection = null, $annuler = null ){

    if ( $id_article == 'new' )
        return array( 'id_article' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    $retour = sql_fetsel( "*" , "spip_articles" , "id_article=$id_article" );
    if ($annuler) { $retour['annuler'] = $annuler; }
    return $retour; // Renvoi l'ensemble de l'objet
}

function formulaires_edition_art_verifier_dist( $id_article = 'new', $id_rubrique, $redirection = null ){
    $erreurs = array();
    if ( !_request('titre') && !_request('titre_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_art_traiter_dist( $id_article = 'new', $id_rubrique, $redirection = null){

    $retour = array();

    $objet = "article";
    $set = array();

    if ($id_article == 'new')
    {
        $id_objet = objet_inserer($objet);
        $redirection = 'spip.php?page=article&id_article='.$id_objet; // on redirige sur le nouveau document créé
        $set['id_parent'] = intval($id_rubrique);
    }
    else
        $id_objet = $id_article;

    $set['titre'] = _request('titre');
    $set['titre_en'] = _request('titre_en');
    $set['chapo'] = _request('chapo');
    $set['chapo_en'] = _request('chapo_en');
    $set['texte'] = _request('texte');
    $set['texte_en'] = _request('texte_en');


    objet_modifier($objet, $id_objet, $set);
    suivre_invalideur("id='$objet/$id_objet'");

    // traitements du lien des auteurs
    objet_associer( array('auteur' => $GLOBALS['auteur_session']['id_auteur']), array($objet => $id_objet) );

    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;

}




?>
