<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_moderation_objet_charger_dist( $table, $type, $id_objet ){
    $retour = sql_fetsel( "statut" , "spip_$table" , "id_$type=$id_objet" ); // Renvoi l'ensemble de l'objet
    $retour['role'] = $GLOBALS['auteur_session']['statut'];
    return $retour;
}

function formulaires_moderation_objet_verifier_dist( $table, $type, $id_objet ){
    $erreurs = array();

    $statuts = array ("prepa", "prop", "publie", "refuse", "poubelle");
    if (!in_array(_request('form_statut'), $statuts)  )
        $erreurs['message_erreur'] = _T('lped:erreur');
    return $erreurs;
}

function formulaires_moderation_objet_traiter_dist( $table, $type, $id_objet ){

    $objet = $type;

    $set = array(
        'statut' => _request('form_statut'),
    );

    objet_modifier($objet, $id_objet, $set);
    suivre_invalideur("id='$objet/$id_objet'");

    $retour = array();
    $retour['message_ok'] = _T('lped:enregistrement_ok');

    return $retour;

}




?>
