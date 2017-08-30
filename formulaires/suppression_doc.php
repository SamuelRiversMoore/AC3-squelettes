<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_suppression_doc_charger_dist( $id_document = null , $lien_objet = null , $lien_id_objet = null ){

    $retour['id_document'] = $id_document;
    return $retour; // Renvoi l'ensemble de l'objet

}

function formulaires_suppression_doc_verifier_dist( $id_document = null , $lien_objet = null , $lien_id_objet = null ){
    $erreur = array();

    if (!$id_document || !$lien_objet || !$lien_id_objet){
      $erreur['formulaire'] = 'un problème est intervenu lors du chargement du formulaire';
    }

    return $erreur;
}

function formulaires_suppression_doc_traiter_dist( $id_document = null , $lien_objet = null , $lien_id_objet = null ){

    $objet = 'document';
    $id_objet = $id_document;

    objet_dissocier( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on dissocie pour les nouveaux documents si c'est renseigné

    suivre_invalideur("id='$objet/$id_objet'");

    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;

}




?>
