<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_ville_charger_dist( $id_ville = 'new' ){

    if ( $id_ville == 'new' )
        return array( 'id_ville' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_villes" , "id_ville=$id_ville" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_ville_verifier_dist( $id_ville = 'new' ){
    $erreurs = array();
    if ( !_request('nom_fr') && !_request('nom_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_ville_traiter_dist( $id_ville = 'new' , $lien_objet = null, $lien_id_objet = null , $redirection = null){

    $objet = "ville";

    if ($id_ville == 'new'){
        $id_objet = objet_inserer($objet);

        if ($lien_objet && $lien_id_objet) {
            objet_associer( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné
        }

        $redirection = 'spip.php?page=ville&id_ville='.$id_objet;
    }
    else
        $id_objet = $id_ville;

    $set = array(
        'nom_fr' => _request('nom_fr'),
        'nom_en' => _request('nom_en'),
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
