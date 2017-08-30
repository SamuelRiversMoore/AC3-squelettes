<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_creer_projet_dist(){
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

  include_spip('inc/headers');
  include_spip('action/editer_objet');
  include_spip('inc/invalideur');
  include_spip('action/editer_liens');

  $objet = "projet";
  $id_objet = objet_inserer($objet);
  $lien_objet = 'ville';
  $lien_id_objet = intval($arg);
  objet_associer( array($objet => $id_objet), array($lien_objet => $lien_id_objet) ); // on associe pour les nouveaux documents si c'est renseigné

  suivre_invalideur("id='$objet/$id_objet'");

  $redirection = 'spip.php?page=projet&id_projet='.$id_objet.'&afficher=editer_projet&annuler='._request('redirect'); // on redirige sur le nouveau document créé

  redirige_par_entete($redirection);


}
?>
