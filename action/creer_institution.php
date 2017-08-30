<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_creer_institution_dist(){
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

  include_spip('inc/headers');
  include_spip('action/editer_objet');
  include_spip('inc/invalideur');
  include_spip('action/editer_liens');

  $objet = "institution";
  $id_objet = objet_inserer($objet);
  $set['id_rubrique'] = intval($arg);
  objet_modifier($objet, $id_objet, $set);

  // Bug de je ne sais quelle merde spip
  if (intval($arg)){
    sql_updateq('spip_institutions', array( 'id_rubrique' => intval($set['id_rubrique']) ), "id_institution=$id_objet");
  }

  suivre_invalideur("id='$objet/$id_objet'");

  $redirection = _request('redirect'); // on redirige sur le nouveau document créé
  $redirection = parametre_url($redirection,'afficher','editer_institution','&');
  $redirection = parametre_url($redirection,'cible',$id_objet,'&');
  $redirection = parametre_url($redirection,'annuler',_request('redirect'),'&');

  redirige_par_entete($redirection);


}
?>
