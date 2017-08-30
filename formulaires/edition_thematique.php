<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_thematique_charger_dist( $id_thematique = 'new' ){

    if ( $id_thematique == 'new' )
        return array( 'id_thematique' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_thematiques" , "id_thematique=$id_thematique" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_thematique_verifier_dist( $id_thematique = 'new' ){
    $erreurs = array();
    if ( !_request('titre_fr') && !_request('titre_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_thematique_traiter_dist( $id_thematique = 'new', $lien_objet = null, $lien_id_objet = null, $redirection = null){

    $retour = array();

    $objet = "thematique";

    if ($id_thematique == 'new')
    {
        $id_objet = objet_inserer($objet);

        if ($lien_objet && $lien_id_objet) {
            objet_associer( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné
        }

        if ($redirection) {
            $redirection = parametre_url($redirection, 'thematique', $id_objet); // on redirige sur le nouveau document créé
        }

    }
    else
        $id_objet = $id_thematique;

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
