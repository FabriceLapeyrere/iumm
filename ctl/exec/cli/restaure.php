<?php
$fichier=$argv[2];
function unzip($file){
    $zip = zip_open($file);
    if(is_resource($zip)){
        echo "Restauration de la sauvegarde : $file\n";
        $tree = "";
        while(($zip_entry = zip_read($zip)) !== false){
            if(strpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR) !== false){
                $last = strrpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR);
                $dir = substr(zip_entry_name($zip_entry), 0, $last);
                $file = substr(zip_entry_name($zip_entry), strrpos(zip_entry_name($zip_entry), DIRECTORY_SEPARATOR)+1);
                if(!is_dir($dir)){
                    @mkdir($dir, 0755, true) or die("impossible de créer $dir\n");
                }
                if(strlen(trim($file)) > 0){
                    $return = @file_put_contents($dir."/".$file, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
                    if($return === false){
                        die("impossible de créer le fichier $dir/$file\n");
                    }
                }
            }else{
                file_put_contents($file, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
            }
        }
    }else{
        echo "Impossible d'ouvrir la sauvegarde, vérifier les droits...\n";
    }
}

unzip($fichier);


?>
