<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');
    include_spip('formulaires/edition_doc_inc');

function formulaires_edition_doc_charger_dist( $id_document = 'new' , $lien_objet = null, $lien_id_objet = null, $redirection = null, $mode = null){

    if ( $id_document == 'new' ){
      $retour = array( 'id_document' => 'new', 'nouveau' => 'oui', 'mode' => $mode, 'lien_objet' => $lien_objet, 'lien_id_objet' => $lien_id_objet ); // Renvoi un objet vide

    }
    else {
      $retour = sql_fetsel( "*" , "spip_documents" , "id_document=$id_document" );
    }

    $retour['mode'] = $mode;

    return $retour; // Renvoi l'ensemble de l'objet
}

function formulaires_edition_doc_verifier_dist( $id_document = 'new' , $lien_objet = null, $lien_id_objet = null, $redirection = null, $mode = null){
    $erreur = array();
    if ( !_request('delier') ){  // si ce n'est pas une suppression

        if ( $id_document != 'new' ){
            $req = sql_fetsel( "fichier" , "spip_documents" , "id_document=$id_document");
            $ancien_fichier = $req['fichier'];
        } // on récupère le nom du fichier actuel s'il existe

        $fichier_distant = _request('fichier_distant');
        if ($fichier_distant == 'http://') $fichier_distant = null;

        $mediatheque = _request('mediatheque');
        if ($mediatheque == '') $mediatheque = null;

        if ( !$_FILES['fichier_upload']['tmp_name'] && !$fichier_distant && !$ancien_fichier && !$mediatheque ) $erreur['fichier_manquant'] = 'vous devez indiquer un fichier'; // si aucun fichier renseigné alors erreur

        if ($_FILES['fichier_upload']['tmp_name']){
            $infos_fichier =  charger_infos_fichier($_FILES['fichier_upload']);
            if ($infos_fichier['fichier_erreur']) $erreur['fichier_upload_erreur'] = $infos_fichier['fichier_erreur'];

        } // si un fichier upload est renseigné, alors on tente de récupérer les infos

        if ($fichier_distant){
            $infos_fichier = charger_infos_fichier_distant($fichier_distant);
            if ($infos_fichier['fichier_erreur']) $erreur['fichier_distant_erreur'] = $infos_fichier['fichier_erreur'];
        } // si un fichier distant est renseigné, alors on tente de récupérer les infos

        /* Pour protéger le mode image

        if ($mode=='image' && !$mediatheque) {
          if (!in_array($infos_fichier['fichier_extension'],array('png','jpg','gif'))){
            $erreur['fichier_non_image'] = 'le fichier n\'est pas une image';
          }
        }

        */

        if (!_request('titre') && $mode!='image' && !$mediatheque) {
            $erreur['titre'] = 'vous devez indiquer un titre';
        }

    }

    return $erreur;
}

function formulaires_edition_doc_traiter_dist( $id_document = 'new' , $lien_objet = null, $lien_id_objet = null, $redirection = null , $mode = null, $statut = null){

    if ( !_request('delier') ){ // si ce n'est pas une suppression

        if ( $id_document != 'new' ){
            $req = sql_fetsel( "fichier" , "spip_documents" , "id_document=$id_document" );
            $ancien_fichier = $req['fichier'];
        } // on récupère le nom du fichier actuel s'il existe

        $fichier_distant = _request('fichier_distant');
        if ($fichier_distant == 'http://') $fichier_distant = null;

        $mediatheque = _request('mediatheque');
        if ($mediatheque == '') $mediatheque = null;

        $set = array();

        // si c'est un upload, on upload
        if ( $_FILES['fichier_upload']['tmp_name'] ){
            $infos_fichier =  charger_infos_fichier($_FILES['fichier_upload']);
            $set['extension'] = $infos_fichier['fichier_extension'];
            $set['fichier'] = $infos_fichier['fichier_adresse_relative'];
            $set['taille'] = $infos_fichier['fichier_taille'];
            $set['largeur'] = $infos_fichier['fichier_largeur'];
            $set['hauteur'] = $infos_fichier['fichier_hauteur'];
            $set['media'] = $infos_fichier['fichier_media'];
            $set['mode'] = $infos_fichier['fichier_mode'];
            $set['distant'] = 'non';
            move_uploaded_file($_FILES['fichier_upload']['tmp_name'], $infos_fichier['fichier_adresse_absolue']);
        }


        // si c'est un fichier distant
        else if( $fichier_distant){
            $infos_fichier =  charger_infos_fichier_distant($fichier_distant);
            $set['extension'] = $infos_fichier['fichier_extension'];
            $set['fichier'] = $infos_fichier['fichier_adresse_relative'];
            $set['taille'] = $infos_fichier['fichier_taille'];
            $set['largeur'] = $infos_fichier['fichier_largeur'];
            $set['hauteur'] = $infos_fichier['fichier_hauteur'];
            $set['media'] = $infos_fichier['fichier_media'];
            $set['mode'] = $infos_fichier['fichier_mode'];
            $set['distant'] = 'oui';
        }

        if (!$mediatheque) { // si ce n'est pas un choix de la médiathèque

              $objet = 'document';
              if ($id_document == 'new')
              {
                  $id_objet = objet_inserer($objet);

                  if ($lien_objet && $lien_id_objet) {
                      objet_associer( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné
                  }

                  if ($redirection) {
                      $redirection = parametre_url($redirection, 'document', $id_objet); // on redirige sur le nouveau document créé
                  }

              }
              else {
                  $id_objet = $id_document;

                  if ($_FILES['fichier_upload']['tmp_name'] || $fichier_distant){
                      sql_updateq('spip_documents', array( 'fichier' => '' ), "id_document=$id_objet");
                  }// supprime un bug du plugin media trop restrictif
              }

              $set['titre'] = _request('titre');
              $set['auteur'] = _request('auteur');
              $set['date_document'] = _request('date_document');
              $set['reference'] = _request('reference');
              $set['descriptif'] = _request('descriptif');
              $set['auteur_resume'] = _request('auteur_resume');
              $set['date_resume'] = _request('date_resume');
              $set['statut'] = 'prepa';

              if ($statut) // au cas ou si on force le statut
                $set['statut'] = $statut;


              objet_modifier($objet, $id_objet, $set);
              suivre_invalideur("id='$objet/$id_objet'");

              // traitements du lien des auteurs
              objet_associer( array('auteur' => $GLOBALS['auteur_session']['id_auteur']), array($objet => $id_objet) );

        }

        if ($mediatheque){ // si c'est un choix de la médithèque on créé juste le lien
            $objet = 'document';
            if ($lien_objet && $lien_id_objet) {
                objet_associer( array($objet => $mediatheque), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné
            }
        }

    }

    if ( _request('delier') ) { // si c'est une suppression
        objet_dissocier( array('document' => _request('delier') ), array($lien_objet => $lien_id_objet) ); // on dissocie pour les nouveaux documents si c'est renseigné
    }


    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;


}




?>
