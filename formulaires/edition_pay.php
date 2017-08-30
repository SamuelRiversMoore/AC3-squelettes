<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

    include_spip('action/editer_objet');
    include_spip('action/editer_liens');
    include_spip('inc/invalideur');

function formulaires_edition_pay_charger_dist( $id_pay = 'new' ){

    if ( $id_pay == 'new' )
        return array( 'id_pay' => 'new', 'nouveau' => 'oui' ); // Renvoi un objet vide

    return sql_fetsel( "*" , "spip_pays" , "id_pay=$id_pay" ); // Renvoi l'ensemble de l'objet
}

function formulaires_edition_pay_verifier_dist( $id_pay = 'new' ){
    $erreurs = array();
    if ( !_request('nom_fr') && !_request('nom_en') )
        $erreurs['message_erreur'] = _T('lped:pays_erreur_champs');
    return $erreurs;
}

function formulaires_edition_pay_traiter_dist( $id_pay = 'new' , $redirection = null){

    $objet = "pay";

    if ($id_pay == 'new'){
        $id_objet = objet_inserer($objet);
        $redirection = 'spip.php?page=pay&id_pay='.$id_objet;
      }
    else
        $id_objet = $id_pay;

    $set = array(
        'nom_fr' => _request('nom_fr'),
        'nom_en' => _request('nom_en'),
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
