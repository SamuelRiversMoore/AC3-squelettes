<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_chapitre_charger_dist( $id_chapitre = 'new' ){

    if ( $id_chapitre == 'new' )
        return array( 'id_chapitre' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_chapitres" , "id_chapitre=$id_chapitre" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_chapitre_verifier_dist( $id_chapitre = 'new' ){
    $erreurs = array();
    if ( !_request('titre_fr') && !_request('titre_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_chapitre_traiter_dist( $id_chapitre = 'new', $lien_objet = null, $lien_id_objet = null, $redirection = null){

    $retour = array();

    $objet = "chapitre";

    if ($id_chapitre == 'new')
    {
        $id_objet = objet_inserer($objet);

        if ($lien_objet && $lien_id_objet) {
            objet_associer( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné
        }

        if ($redirection) {
            $redirection = parametre_url($redirection, 'chapitre', $id_objet); // on redirige sur le nouveau document créé
        }

    }
    else
        $id_objet = $id_chapitre;

    $set = array(
        'titre_fr' => _request('titre_fr'),
        'titre_en' => _request('titre_en'),
        'texte_fr' => _request('texte_fr'),
        'texte_en' => _request('texte_en'),
    );

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
