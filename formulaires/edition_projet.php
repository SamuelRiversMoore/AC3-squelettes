<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_projet_charger_dist( $id_projet = 'new' , $lien_objet = null, $lien_id_objet = null , $redirection = null, $annuler = null ){

    if ( $id_projet == 'new' )
        return array( 'id_projet' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    $retour = sql_fetsel( "*" , "spip_projets" , "id_projet=$id_projet" ); // Renvoi l'ensemble de l'objet
    if ($annuler) $retour['annuler'] = $annuler;
    return $retour;
}

function formulaires_edition_projet_verifier_dist( $id_projet = 'new' , $lien_objet = null, $lien_id_objet = null , $redirection = null ){
    $erreurs = array();
    if ( !_request('titre_fr') && !_request('titre_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_projet_traiter_dist( $id_projet = 'new' , $lien_objet = null, $lien_id_objet = null , $redirection = null){

    $objet = "projet";

    if ($id_projet == 'new'){
        $id_objet = objet_inserer($objet);

        if ($lien_objet && $lien_id_objet) {
            objet_associer( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné
        }

        $redirection = 'spip.php?page=projet&id_projet='.$id_objet;
    }
    else
        $id_objet = $id_projet;

    $set = array(
        'titre_fr' => _request('titre_fr'),
        'titre_en' => _request('titre_en'),
        'description_fr' => _request('description_fr'),
        'description_en' => _request('description_en'),
        'latitude' => _request('latitude'),
        'longitude' => _request('longitude'),
    );

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
