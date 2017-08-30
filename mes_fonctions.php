<?php


function filtre_parametre_lang($url){

    // la langue demandée
    $lang = _request('lang') ;
    
    if ($lang)
	   $url = parametre_url($url, 'lang', $lang);    
    
    return $url;
 
}
?>