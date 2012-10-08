<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

function filter($txt) {
	$search = array ('@(é|è|ê|ë|Ê|Ë)@i','@(á|ã|à|â|ä|Â|Ä)@i','@(ì|í|i|i|î|ï|Î|Ï)@i','@(ú|û|ù|ü|Û|Ü)@i','@(ò|ó|õ|ô|ö|Ô|Ö)@i','@(ñ|Ñ)@i','@(ý|ÿ|Ý)@i','@(ç)@i','@( )@i','@[^a-zA-Z0-9_]@');
	$replace = array ('e','a','i','u','o','n','y','c','_','');
	return preg_replace($search, $replace, $txt);
}
function noaccent($txt) {
	$search = array ('@(é|è|ê|ë|Ê|Ë|È)@i','@(á|ã|à|â|ä|Â|Ä|À)@i','@(ì|í|i|i|î|ï|Î|Ï)@i','@(ú|û|ù|ü|Û|Ü)@i','@(ò|ó|õ|ô|ö|Ô|Ö)@i','@(ñ|Ñ)@i','@(ý|ÿ|Ý)@i','@(ç)@i','@[^a-zA-Z0-9_!]@');
	$replace = array ('e','a','i','u','o','n','y','c',' ');
	$res=strtolower(trim(preg_replace($search, $replace, $txt)));
	$res=preg_replace('@([ ]+)@i', ' ', $res);
	#error_log("-----$res-----\n",3,"tmp/fab.log");
	return $res;
}
function mot($mot, $string)
{
	$motif=noaccent($mot);
	$string=noaccent($string);
	return preg_match("/\b$mot\b/i", $string);
}
function motif($motif, $string)
{
	$motif=noaccent($motif);
	$string=noaccent($string);
	return preg_match("/$motif/i", $string);
}
function cp($string)
{
	$cp="";
	if ($string!="") {
		$matches=array();
		$pattern = '/^\d{4,5}/';
		preg_match($pattern, str_replace(" ","",$string), $matches);
		if (isset($matches[0])) $cp=$matches[0];
	}
	return $cp;
}

/**
     * Copy file or folder from source to destination, it can do
     * recursive copy as well and is very smart
     * It recursively creates the dest file or directory path if there weren't exists
     * Situtaions :
     * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
     * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
     * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest     
     * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
     * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
     * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
     * @todo
     *     - Should have rollback technique so it can undo the copy when it wasn't successful
     *  - Auto destination technique should be possible to turn off
     *  - Supporting callback function
     *  - May prevent some issues on shared enviroments : http://us3.php.net/umask
     * @param $source //file or folder
     * @param $dest ///file or folder
     * @param $options //folderPermission,filePermission
     * @return boolean
     */
    function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755))
    {
        $result=false;
       
        if (is_file($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if (!file_exists($dest)) {
                    cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
                }
                $__dest=$dest."/".basename($source);
            } else {
                $__dest=$dest;
            }
            $result=copy($source, $__dest);
            chmod($__dest,$options['filePermission']);
           
        } elseif(is_dir($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if ($source[strlen($source)-1]=='/') {
                    //Copy only contents
                } else {
                    //Change parent itself and its contents
                    $dest=$dest.basename($source);
                    @mkdir($dest);
                    chmod($dest,$options['filePermission']);
                }
            } else {
                if ($source[strlen($source)-1]=='/') {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                }
            }

            $dirHandle=opendir($source);
            while($file=readdir($dirHandle))
            {
                if($file!="." && $file!="..")
                {
                     if(!is_dir($source."/".$file)) {
                        $__dest=$dest."/".$file;
                    } else {
                        $__dest=$dest."/".$file;
                    }
                    //echo "$source/$file ||| $__dest<br />";
                    $result=smartCopy($source."/".$file, $__dest, $options);
                }
            }
            closedir($dirHandle);
           
        } else {
            $result=false;
        }
        return $result;
    }

$departements=array();
$departements['1']=array('nom'=>'Ain ', 'prefecture'=>'Bourg-en-Bresse ', 'region'=>'Rhône-Alpes');
$departements['2']=array('nom'=>'Aisne ', 'prefecture'=>'Laon ', 'region'=>'Picardie');
$departements['3']=array('nom'=>'Allier ', 'prefecture'=>'Moulins ', 'region'=>'Auvergne');
$departements['4']=array('nom'=>'Alpes de Hautes-Provence ', 'prefecture'=>'Digne ', 'region'=>'Provence-Alpes-Côte d\'Azur');
$departements['5']=array('nom'=>'Hautes-Alpes ', 'prefecture'=>'Gap ', 'region'=>'Provence-Alpes-Côte d\'Azur');
$departements['6']=array('nom'=>'Alpes-Maritimes ', 'prefecture'=>'Nice ', 'region'=>'Provence-Alpes-Côte d\'Azur');
$departements['7']=array('nom'=>'Ardèche ', 'prefecture'=>'Privas ', 'region'=>'Rhône-Alpes');
$departements['8']=array('nom'=>'Ardennes ', 'prefecture'=>'Charleville-Mézières ', 'region'=>'Champagne-Ardenne');
$departements['9']=array('nom'=>'Ariège ', 'prefecture'=>'Foix ', 'region'=>'Midi-Pyrénées');
$departements['10']=array('nom'=>'Aube ', 'prefecture'=>'Troyes ', 'region'=>'Champagne-Ardenne');
$departements['11']=array('nom'=>'Aude ', 'prefecture'=>'Carcassonne ', 'region'=>'Languedoc-Roussillon');
$departements['12']=array('nom'=>'Aveyron ', 'prefecture'=>'Rodez ', 'region'=>'Midi-Pyrénées');
$departements['13']=array('nom'=>'Bouches-du-Rhône ', 'prefecture'=>'Marseille ', 'region'=>'Provence-Alpes-Côte d\'Azur');
$departements['14']=array('nom'=>'Calvados ', 'prefecture'=>'Caen ', 'region'=>'Basse-Normandie');
$departements['15']=array('nom'=>'Cantal ', 'prefecture'=>'Aurillac ', 'region'=>'Auvergne');
$departements['16']=array('nom'=>'Charente ', 'prefecture'=>'Angoulême ', 'region'=>'Poitou-Charentes');
$departements['17']=array('nom'=>'Charente-Maritime ', 'prefecture'=>'La Rochelle ', 'region'=>'Poitou-Charentes');
$departements['18']=array('nom'=>'Cher ', 'prefecture'=>'Bourges ', 'region'=>'Centre');
$departements['19']=array('nom'=>'Corrèze ', 'prefecture'=>'Tulle ', 'region'=>'Limousin');
$departements['2A']=array('nom'=>'Corse-du-Sud ', 'prefecture'=>'Ajaccio ', 'region'=>'Corse');
$departements['2B']=array('nom'=>'Haute-Corse ', 'prefecture'=>'Bastia ', 'region'=>'Corse');
$departements['21']=array('nom'=>'Côte-d\'Or ', 'prefecture'=>'Dijon ', 'region'=>'Bourgogne');
$departements['22']=array('nom'=>'Côtes d\'Armor ', 'prefecture'=>'Saint-Brieuc ', 'region'=>'Bretagne');
$departements['23']=array('nom'=>'Creuse ', 'prefecture'=>'Guéret ', 'region'=>'Limousin');
$departements['24']=array('nom'=>'Dordogne ', 'prefecture'=>'Périgueux ', 'region'=>'Aquitaine');
$departements['25']=array('nom'=>'Doubs ', 'prefecture'=>'Besançon ', 'region'=>'Franche-Comté');
$departements['26']=array('nom'=>'Drôme ', 'prefecture'=>'Valence ', 'region'=>'Rhône-Alpes');
$departements['27']=array('nom'=>'Eure ', 'prefecture'=>'Évreux ', 'region'=>'Haute-Normandie');
$departements['28']=array('nom'=>'Eure-et-Loir ', 'prefecture'=>'Chartres ', 'region'=>'Centre');
$departements['29']=array('nom'=>'Finistère ', 'prefecture'=>'Quimper ', 'region'=>'Bretagne');
$departements['30']=array('nom'=>'Gard ', 'prefecture'=>'Nîmes ', 'region'=>'Languedoc-Roussillon');
$departements['31']=array('nom'=>'Haute-Garonne ', 'prefecture'=>'Toulouse ', 'region'=>'Midi-Pyrénées');
$departements['32']=array('nom'=>'Gers ', 'prefecture'=>'Auch ', 'region'=>'Midi-Pyrénées');
$departements['33']=array('nom'=>'Gironde ', 'prefecture'=>'Bordeaux ', 'region'=>'Aquitaine');
$departements['34']=array('nom'=>'Hérault ', 'prefecture'=>'Montpellier ', 'region'=>'Languedoc-Roussillon');
$departements['35']=array('nom'=>'Ille-et-Vilaine ', 'prefecture'=>'Rennes ', 'region'=>'Bretagne');
$departements['36']=array('nom'=>'Indre ', 'prefecture'=>'Châteauroux ', 'region'=>'Centre');
$departements['37']=array('nom'=>'Indre-et-Loire ', 'prefecture'=>'Tours ', 'region'=>'Centre');
$departements['38']=array('nom'=>'Isère ', 'prefecture'=>'Grenoble ', 'region'=>'Rhône-Alpes');
$departements['39']=array('nom'=>'Jura ', 'prefecture'=>'Lons-le-Saunier ', 'region'=>'Franche-Comté');
$departements['40']=array('nom'=>'Landes ', 'prefecture'=>'Mont-de-Marsan ', 'region'=>'Aquitaine');
$departements['41']=array('nom'=>'Loir-et-Cher ', 'prefecture'=>'Blois ', 'region'=>'Centre');
$departements['42']=array('nom'=>'Loire ', 'prefecture'=>'Saint-Étienne ', 'region'=>'Rhône-Alpes');
$departements['43']=array('nom'=>'Haute-Loire ', 'prefecture'=>'Le Puy-en-Velay ', 'region'=>'Auvergne');
$departements['44']=array('nom'=>'Loire-Atlantique ', 'prefecture'=>'Nantes ', 'region'=>'Pays de la Loire');
$departements['45']=array('nom'=>'Loiret ', 'prefecture'=>'Orléans ', 'region'=>'Centre');
$departements['46']=array('nom'=>'Lot ', 'prefecture'=>'Cahors ', 'region'=>'Midi-Pyrénées');
$departements['47']=array('nom'=>'Lot-et-Garonne ', 'prefecture'=>'Agen ', 'region'=>'Aquitaine');
$departements['48']=array('nom'=>'Lozère ', 'prefecture'=>'Mende ', 'region'=>'Languedoc-Roussillon');
$departements['49']=array('nom'=>'Maine-et-Loire ', 'prefecture'=>'Angers ', 'region'=>'Pays de la Loire');
$departements['50']=array('nom'=>'Manche ', 'prefecture'=>'Saint-Lô ', 'region'=>'Basse-Normandie');
$departements['51']=array('nom'=>'Marne ', 'prefecture'=>'Châlons-en-Champagne ', 'region'=>'Champagne-Ardenne');
$departements['52']=array('nom'=>'Haute-Marne ', 'prefecture'=>'Chaumont ', 'region'=>'Champagne-Ardenne');
$departements['53']=array('nom'=>'Mayenne ', 'prefecture'=>'Laval ', 'region'=>'Pays de la Loire');
$departements['54']=array('nom'=>'Meurthe-et-Moselle ', 'prefecture'=>'Nancy ', 'region'=>'Lorraine');
$departements['55']=array('nom'=>'Meuse ', 'prefecture'=>'Bar-le-Duc ', 'region'=>'Lorraine');
$departements['56']=array('nom'=>'Morbihan ', 'prefecture'=>'Vannes ', 'region'=>'Bretagne');
$departements['57']=array('nom'=>'Moselle ', 'prefecture'=>'Metz ', 'region'=>'Lorraine');
$departements['58']=array('nom'=>'Nièvre ', 'prefecture'=>'Nevers ', 'region'=>'Bourgogne');
$departements['59']=array('nom'=>'Nord ', 'prefecture'=>'Lille ', 'region'=>'Nord-Pas-de-Calais');
$departements['60']=array('nom'=>'Oise ', 'prefecture'=>'Beauvais ', 'region'=>'Picardie');
$departements['61']=array('nom'=>'Orne ', 'prefecture'=>'Alençon ', 'region'=>'Basse-Normandie');
$departements['62']=array('nom'=>'Pas-de-Calais ', 'prefecture'=>'Arras ', 'region'=>'Nord-Pas-de-Calais');
$departements['63']=array('nom'=>'Puy-de-Dôme ', 'prefecture'=>'Clermont-Ferrand ', 'region'=>'Auvergne');
$departements['64']=array('nom'=>'Pyrénées-Atlantiques ', 'prefecture'=>'Pau ', 'region'=>'Aquitaine');
$departements['65']=array('nom'=>'Hautes-Pyrénées ', 'prefecture'=>'Tarbes ', 'region'=>'Midi-Pyrénées');
$departements['66']=array('nom'=>'Pyrénées-Orientales ', 'prefecture'=>'Perpignan ', 'region'=>'Languedoc-Roussillon');
$departements['67']=array('nom'=>'Bas-Rhin ', 'prefecture'=>'Strasbourg ', 'region'=>'Alsace');
$departements['68']=array('nom'=>'Haut-Rhin ', 'prefecture'=>'Colmar ', 'region'=>'Alsace');
$departements['69']=array('nom'=>'Rhône ', 'prefecture'=>'Lyon ', 'region'=>'Rhône-Alpes');
$departements['70']=array('nom'=>'Haute-Saône ', 'prefecture'=>'Vesoul ', 'region'=>'Franche-Comté');
$departements['71']=array('nom'=>'Saône-et-Loire ', 'prefecture'=>'Mâcon ', 'region'=>'Bourgogne');
$departements['72']=array('nom'=>'Sarthe ', 'prefecture'=>'Le Mans ', 'region'=>'Pays de la Loire');
$departements['73']=array('nom'=>'Savoie ', 'prefecture'=>'Chambéry ', 'region'=>'Rhône-Alpes');
$departements['74']=array('nom'=>'Haute-Savoie ', 'prefecture'=>'Annecy ', 'region'=>'Rhône-Alpes');
$departements['75']=array('nom'=>'Paris ', 'prefecture'=>'Paris ', 'region'=>'Ile-de-France');
$departements['76']=array('nom'=>'Seine-Maritime ', 'prefecture'=>'Rouen ', 'region'=>'Haute-Normandie');
$departements['77']=array('nom'=>'Seine-et-Marne ', 'prefecture'=>'Melun ', 'region'=>'Ile-de-France');
$departements['78']=array('nom'=>'Yvelines ', 'prefecture'=>'Versailles ', 'region'=>'Ile-de-France');
$departements['79']=array('nom'=>'Deux-Sèvres ', 'prefecture'=>'Niort ', 'region'=>'Poitou-Charentes');
$departements['80']=array('nom'=>'Somme ', 'prefecture'=>'Amiens ', 'region'=>'Picardie');
$departements['81']=array('nom'=>'Tarn ', 'prefecture'=>'Albi ', 'region'=>'Midi-Pyrénées');
$departements['82']=array('nom'=>'Tarn-et-Garonne ', 'prefecture'=>'Montauban ', 'region'=>'Midi-Pyrénées');
$departements['83']=array('nom'=>'Var ', 'prefecture'=>'Toulon ', 'region'=>'Provence-Alpes-Côte d\'Azur');
$departements['84']=array('nom'=>'Vaucluse ', 'prefecture'=>'Avignon ', 'region'=>'Provence-Alpes-Côte d\'Azur');
$departements['85']=array('nom'=>'Vendée ', 'prefecture'=>'La Roche-sur-Yon ', 'region'=>'Pays de la Loire');
$departements['86']=array('nom'=>'Vienne ', 'prefecture'=>'Poitiers ', 'region'=>'Poitou-Charentes');
$departements['87']=array('nom'=>'Haute-Vienne ', 'prefecture'=>'Limoges ', 'region'=>'Limousin');
$departements['88']=array('nom'=>'Vosges ', 'prefecture'=>'Épinal ', 'region'=>'Lorraine');
$departements['89']=array('nom'=>'Yonne ', 'prefecture'=>'Auxerre ', 'region'=>'Bourgogne');
$departements['90']=array('nom'=>'Territoire-de-Belfort ', 'prefecture'=>'Belfort ', 'region'=>'Franche-Comté');
$departements['91']=array('nom'=>'Essonne ', 'prefecture'=>'Évry ', 'region'=>'Ile-de-France');
$departements['92']=array('nom'=>'Hauts-de-Seine ', 'prefecture'=>'Nanterre ', 'region'=>'Ile-de-France');
$departements['93']=array('nom'=>'Seine-Saint-Denis ', 'prefecture'=>'Bobigny ', 'region'=>'Ile-de-France');
$departements['94']=array('nom'=>'Val-de-Marne ', 'prefecture'=>'Créteil ', 'region'=>'Ile-de-France');
$departements['95']=array('nom'=>'Val-d\'Oise ', 'prefecture'=>'Pontoise ', 'region'=>'Ile-de-France');


function json_escape($string){
	$p=array();
	$p[0]="/\R/";
	$p[1]="/ /";
	$r=array();
	$r[0]=" ";
	$r[1]=" ";
	return addslashes(preg_replace($p,$r,$string));
}

function async($action,$params=array()){
	if (PHP_SAPI !== 'cli')
	{
	#c'est une ruse pour indexer de manière asynchrone
	#(pour ne pas perdre de temps) merci à 'arr1 at hotmail dot co dot uk'
	# http://www.php.net/manual/en/features.connection-handling.php#71172
			
	$cle="1A2Z3E4R5T6Y";
	
	//open connection
	$ch = curl_init();
	$url=$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME'])."/actions.php";
	//url-ify the data for the POST
	$params_string="";
	foreach($params as $key=>$value) {
		if (is_array($value)) {
			foreach($value as $v)
				$params_string .= $key.'[]='.urlencode($v).'&';

		} else {
			$params_string .= $key.'='.urlencode($value).'&';
		}
	}
	$params_string="auth_cle=$cle&action=$action&".$params_string;
	rtrim($params_string,'&');
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$params_string);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);
	}
}
?>
