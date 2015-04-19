<?php

require_once "dropbox-sdk-php-1.1.5/lib/Dropbox/autoload.php";

use \Dropbox as dbx;

$dropbox_config = array(
    'key'    => '',
    'secret' => ''
);

$appInfo = dbx\AppInfo::loadFromJson($dropbox_config);
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");

$accessToken = "";

// list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
// echo "Access Token: " . $accessToken . "<br>";

$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");

/*

http://dropbox.github.io/dropbox-sdk-php/api-docs/v1.1.x/class-Dropbox.Client.html

*/

$MetadataFolder = $dbxClient->getMetadataWithChildren("/");	// Directorio del cual queremos listar archivos.

// getMetadataWithChildren recoge metadatos Directorios y tambien subdirectorios dentro del principal definido. 

    if ($MetadataFolder != null) {
       
        $children = null;

        if ($MetadataFolder['is_dir']) { 

            $children = $MetadataFolder['contents'];

              if ($children !== null && count($children) > 0) {

                    // Una vez comprobado que la carpeta tiene contenido por cada directorio dentro del principal
                    // se va imprimir las carpeta que hay solo en el primer nivel.
                    
                    foreach ($children as $child) {
                          
                        if ($child['is_dir']) {

                            $name = dbx\Path::getName($child['path']);
                            $name = "$name/";
                            print "> $name\n\n";


                            /*
 			                      
                            $infoFile = $dbxClient->getDelta(null,$child['path']); //! Puede que sea mejor usar esta especificacion que searchFilesNames

                            He usado searchFilesNames:

                            searchFileNames( string $basePath, string $query, integer|null $limit = null, boolean $includeDeleted = false )                            

                            */

 			                      $infoFile = $dbxClient->searchFileNames( $child['path'],'.txt' , null, false );

                            // Por cada archivo recogido en la consulta, se va a crear un enlace de descarga directa.
       
                            foreach($infoFile as $value) {
                                foreach($value as $key => $file) {
                                    if($key == "path") {
                                      echo "<br>";

                                      $link = $dbxClient->createTemporaryDirectLink($file);
                                      $direct_link = $link[0]."?dl=1";
                                      echo "<a href=$direct_link>$file</a>";            
                                    }
                                }
                            }
                            echo "<br>";


                      } else {  /* Controlar error en acceso */ } 

                } // if comprobacion contenido > 0

              } // if metadata carpeta principal sea un directorio

        } else { /* Controlar error en acceso */ }


    } else { /* Controlar error en acceso */ }

?>