<?php


function extensions(){
    return array ( 
        'application/pdf' => 'pdf' ,
        'image/gif' => 'gif' ,
        'image/jpeg' => 'jpg' ,
        'image/png' => 'png' ,

        'text/html' => 'html' ,
        'text/plain' => 'txt' ,
        'text/xml' => 'xml' ,

        'application/vnd.oasis.opendocument.text' => 'odt' ,
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods' ,
        'application/vnd.oasis.opendocument.presentation' => 'odp' ,
        'application/vnd.oasis.opendocument.graphics' => 'odg' ,

        'application/vnd.ms-excel' => 'xls' ,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx' ,

        'application/vnd.ms-powerpoint' => 'ppt' ,
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx' ,

        'application/msword' => 'doc' ,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'  
    );
}

function verifier_extension($fichier){

    $extensions_autorise = extensions();
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mtype = finfo_file($finfo, $fichier['tmp_name']);
    
    if ( $extensions_autorise[$mtype] ) 
        return $extensions_autorise[$mtype];
    else 
        return false;    

    finfo_close($finfo);
}

function nettoyer_nom($fichier_nom){
    $fichier_nom = basename($fichier_nom);
    $fichier_nom = strstr($fichier_nom, '.', true);
    $fichier_nom = preg_replace('/([^.a-z0-9]+)/i', '_', $fichier_nom);
    return $fichier_nom;
}


/* Fonction charger_infos_fichier
    - vérifie le document, 
    - créer les répertoires nécessaires
    - renvois les informations pour le téléchargement et la mise à jour BD
    ou un tableau contenant un message d'erreur
*/

function charger_infos_fichier($fichier){
    
    if (!$fichier || $fichier['error']) {
        return array ( 'fichier_erreur' => 'Un problème a eu lieu lors du téléchargement' );
    }
    
    if ( verifier_extension($fichier) )
        $fichier_extension = verifier_extension($fichier);
    else 
        return array ( 'fichier_erreur' => 'Format de fichier non supporté' );
    
    // À partir d'ici, normalement il n'y a plus d'erreur
    
    $fichier_taille = filesize($fichier['tmp_name']);
    
    if ($fichier_extension == 'gif' || $fichier_extension == 'jpg' || $fichier_extension == 'png' ) {
        $fichier_media = 'image';
        $fichier_mode = 'image';
    }
    else {
        $fichier_media = 'file';
        $fichier_mode = 'document';
    }
    
    if ($fichier_media == 'image') {
        $fichier_dimensions = getimagesize($fichier['tmp_name']);
        $fichier_largeur = $fichier_dimensions[0];
        $fichier_hauteur = $fichier_dimensions[1];
    }
    else {
        $fichier_largeur = 0;
        $fichier_hauteur = 0;
    }
    
    // nettoyage du nom, création des url de destination, création des répertoires si nécessaire, évite les doublons
    $fichier_nom = nettoyer_nom($fichier['name']);
    $dossier_destination = 'IMG/'.$fichier_extension.'/';
    $fichier_adresse_absolue = 'IMG/'.$fichier_extension.'/'.$fichier_nom.'.'.$fichier_extension;
    
    if(!file_exists ($dossier_destination)){
        mkdir($dossier_destination);
    }
    
    while (file_exists($fichier_adresse_absolue)) { 
        $fichier_nom .= '_copie';
        $fichier_adresse_absolue = 'IMG/'.$fichier_extension.'/'.$fichier_nom.'.'.$fichier_extension;
    } 
    
    $fichier_adresse_relative = $fichier_extension.'/'.$fichier_nom.'.'.$fichier_extension;
    // fin
    
    $retour = array(
        'dossier_destination' => $dossier_destination,
        'fichier_nom' => $fichier_nom,
        'fichier_adresse_relative' => $fichier_adresse_relative,
        'fichier_adresse_absolue' => $fichier_adresse_absolue,
        'fichier_taille' => $fichier_taille,
        'fichier_extension' => $fichier_extension,
        'fichier_media' => $fichier_media,
        'fichier_mode' => $fichier_mode,
        'fichier_largeur' => $fichier_largeur,
        'fichier_hauteur' => $fichier_hauteur,
    ); 
    
    return ($retour);
}


/* Fonction charger_infos_fichier_distant
    - vérifie l'url, 
    - renvois les informations pour la mise à jour BD
    ou un tableau contenant un message d'erreur
*/

function charger_infos_fichier_distant ($fichier_adresse){
    include_spip('inc/distant');
    $extensions_autorise = extensions();
    
    $infos_fichier = recuperer_infos_distantes($fichier_adresse);

    if (!$infos_fichier['extension'] || !$fichier_adresse) {
        return array ( 'fichier_erreur' => 'une erreur est survenue lors de l\'analyse du fichier distant' );
    }

    if (!in_array ($infos_fichier['extension'], $extensions_autorise, true)) {
        return array ( 'fichier_erreur' => 'Format de fichier non supporté' );
    }

    if ($infos_fichier['extension'] == 'gif' || $infos_fichier['extension'] == 'jpg' || $infos_fichier['extension'] == 'png' ) {
        $fichier_media = 'image';
        $fichier_mode = 'image';
    }
    else {
        $fichier_media = 'file';
        $fichier_mode = 'document';
    }

    if ($fichier_media == 'image') {
        $fichier_dimensions = getimagesize($fichier_adresse);
        $fichier_largeur = $fichier_dimensions[0];
        $fichier_hauteur = $fichier_dimensions[1];
    }
    else {
        $fichier_largeur = 0;
        $fichier_hauteur = 0;
    }

    $retour = array(
        'fichier_nom' => $infos_fichier['titre'],
        'fichier_adresse_relative' => $fichier_adresse,
        'fichier_taille' => $infos_fichier['taille'],
        'fichier_extension' => $infos_fichier['extension'],

        'fichier_media' => $fichier_media,
        'fichier_mode' => $fichier_mode,

        'fichier_largeur' => $fichier_largeur,
        'fichier_hauteur' => $fichier_hauteur,
    ); 

    return $retour;

}

?>