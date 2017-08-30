<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_gloref_charger_dist( $id_gloref = 'new' , $id_glodef = null ){

    if ( $id_gloref == 'new' )
        return array( 'id_gloref' => 'new', 'id_glodef' => $id_glodef, 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_glorefs" , "id_gloref=$id_gloref" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_gloref_verifier_dist( $id_gloref = 'new' ){
    $erreurs = array();

    if (!_request('delier')) { // si ce n'est pas une suppression
      if ( !_request('titre') && !_request('gloref_lien') )
          $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    }
    return $erreurs;
}

function formulaires_edition_gloref_traiter_dist( $id_gloref = 'new' , $id_glodef = null, $redirection = null){

    if (!_request('delier')) { // si ce n'est pas une suppression
          $objet = "gloref";
          $set = array();

          if ( $id_gloref == 'new' && _request('gloref_lien') ) { // si c'est une sélection d'une référence déjà existante
              $id_objet = _request('gloref_lien');
              objet_associer( array($objet => $id_objet), array('glodef' => $id_glodef) );
          }
          else if ($id_gloref == 'new'){ // sinon si c'est une nouvelle reference
              $id_objet = objet_inserer($objet);
              objet_associer( array($objet => $id_objet), array('glodef' => $id_glodef) );
            }
          else // sinon c'est une modification d'une déjà existante
              $id_objet = $id_gloref;

          $set['titre'] = _request('titre');
          $set['description'] = _request('description');
          $set['statut'] = 'publie';

          if ( !_request('gloref_lien') ) { // si ce n'est pas une sélection d'une référence déjà existante on modifie l'objet
            objet_modifier($objet, $id_objet, $set);
          }

          suivre_invalideur("id='$objet/$id_objet'");

          // traitements du lien des auteurs
          objet_associer( array('auteur' => $GLOBALS['auteur_session']['id_auteur']), array($objet => $id_objet) );

    }

    if (_request('delier')) {
      objet_dissocier( array('gloref' => _request('delier')), array('glodef' => $id_glodef) );
    }

    $retour = array();
    $retour['message_ok'] = _T('lped:enregistrement_ok');

    if ($redirection)
        $retour['redirect'] = $redirection;

    return $retour;

}




?>
