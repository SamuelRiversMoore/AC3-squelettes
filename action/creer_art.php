<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_creer_art_dist(){
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

  include_spip('inc/headers');
  include_spip('action/editer_objet');
  include_spip('inc/invalideur');
  include_spip('action/editer_liens');

  $objet = "article";
  $id_objet = objet_inserer($objet);
  $set['id_parent'] = intval($arg);
  objet_modifier($objet, $id_objet, $set);
  suivre_invalideur("id='$objet/$id_objet'");

  if (intval($arg)!=7){ // si ce n'est pas un article d'à propos
    $redirection = 'spip.php?page=article&id_article='.$id_objet.'&afficher=editer_article&article='.$id_objet.'&annuler='._request('redirect'); // on redirige sur le nouveau document créé
  }
  else { // si c'est un article d'à propos on reste sur la même page
    $redirection = _request('redirect');
    $redirection = parametre_url($redirection,'afficher','editer_article','&');
    $redirection = parametre_url($redirection,'article',$id_objet,'&');
    $redirection = parametre_url($redirection,'annuler',_request('redirect'),'&');
  }

  redirige_par_entete($redirection);


}
?>
