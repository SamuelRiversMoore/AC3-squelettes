<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_suppression_rubrique_charger_dist( $id_rubrique = null ){

    $retour['id_rubrique'] = $id_rubrique;
    return $retour; // Renvoi l'ensemble de l'objet

}

function formulaires_suppression_rubrique_verifier_dist( $id_rubrique = null  ){
    $erreur = array();

    if (!$id_rubrique){
      $erreur['formulaire'] = 'un problème est intervenu lors du chargement du formulaire';
    }

    return $erreur;
}

function formulaires_suppression_rubrique_traiter_dist( $id_rubrique = null  ){

    $objet = 'rubrique';
    $id_objet = $id_rubrique;

    sql_delete('spip_rubriques', 'id_rubrique = ' . intval($id_rubrique));

    suivre_invalideur("id='$objet/$id_objet'");

    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;

}




?>
