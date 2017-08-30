<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_projet_dist(){
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

  include_spip('inc/headers');
  include_spip('action/editer_objet');
  include_spip('inc/invalideur');
  include_spip('action/editer_liens');

  $objet = "projet";
  $id_objet = intval($arg);
  $set['statut'] = 'poubelle';
  objet_modifier($objet, $id_objet, $set);
  suivre_invalideur("id='$objet/$id_objet'");

  $redirection = _request('redirect'); // on redirige sur le nouveau document créé

  redirige_par_entete($redirection);


}
?>
