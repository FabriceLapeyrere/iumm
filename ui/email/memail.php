<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	if (isset($_POST['id_email'])) $id_email=$_POST['id_email'];
	else $id_email=Emails::dernier();
	$format=$_POST['format'];
	$html="";
	$js="";
		
	if ($id_email==0) {
		$html="Aucun e-mail.";
	} else {
		$_SESSION['email_rep']="emails/$id_email/";
		$e=new Email($id_email);
		$tab=$e->images();
		$images=array();
		foreach ($tab as $image){
			$images[]="['".addslashes($image)."']";
		}
		$html.="
		<button class='enr-email off ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' data-id='$id_email' role='button' aria-disabled='false' title='enregistrer'>
			<span class='ui-button-text'>enregistrer</span>
		</button>
		<button data-id='$id_email' class='env-email ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' role='button' aria-disabled='false' title='envoyer à la selection'>
			<span class='ui-button-text'>envoyer à la selection</span>
		</button>
		<div class='titre'>".$e->sujet."</div>
		<div id='mail_editeur'>
		<textarea class='editor'>".$e->html."</textarea>
		</div>
		".Html::upload("emails/$id_email/")."
	
		";
		$js.="
		$( 'textarea.editor' ).ckeditor(function(){mail_smapi.reinitialise();});
		$('textarea.editor').ckeditorGet().on( 'resize', function( e ){ mail_smapi.reinitialise(); });
		var majImg=function(){
			var images=emailImages();
			for (index in images) {
				
				var r=new RegExp('src=\"'+images[index][0]);
				var html=$('textarea.editor').val();
				if (r.test(html)) {
					if ($('button[data-url*=\"'+images[index][0].replace(/\\\\/g,'/').replace(/.*\\//,'' )+'\"]').css('display')=='inline-block')
						$('button[data-url*=\"'+images[index][0].replace(/\\\\/g,'/').replace(/.*\\//,'' )+'\"]').css('display','none').next().remove();
				} else {
					if ($('button[data-url*=\"'+images[index][0].replace(/\\\\/g,'/').replace(/.*\\//,'' )+'\"]').css('display')=='none')
						$('button[data-url*=\"'+images[index][0].replace(/\\\\/g,'/').replace( /.*\\//, '' )+'\"]').css('display','inline-block').after('<input type=\"checkbox\" value=\"1\" name=\"delete\">');	
				}
			}
		};
		setTimeout(majImg,1000);
		$('textarea.editor').ckeditorGet().on('change', function(){                    
			if (this.checkDirty()) {
				$('#mail_email .enr-email').removeClass('off');
				$('#mail_email .enr-email').addClass('on');
			} else {
				$('#mail_email .enr-email').removeClass('on');
				$('#mail_email .enr-email').addClass('off');		
			}
			var supImage = function(){
				var html=$('textarea.editor').val();
				if (/<img alt=\"\" src=\"data:image\/png;base64,.*\" \/>/.test(html)) $('textarea.editor').val(html.replace( /<img alt=\"\" src=\"data:image\/png;base64,.*\" \/>/g, ''));
			}
			setTimeout(supImage,500);
			majImg();
		});
		".js::upload()."
		";
	}
	
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['message']="";
			$reponse['html']=$html;
			$reponse['js']=$js;
			break;
	}
?>
