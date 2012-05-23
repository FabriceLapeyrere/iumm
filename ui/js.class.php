<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */

/**
*  Classe "js", contient tout l'arsenal pour générer le javascript. 
*/

class js
	{
		/**
        * Déclaration des attributs
        */
        var $entrees=array();

        public function __construct()
        {
        }
        public function contact($id_contact)
        {
		$js="
		/*$('#ed_contact-$id_contact.tabs' ).tabs('destroy');
		$('#ed_contact-$id_contact.tabs' ).tabs({
			collapsible: 'true',
			select: function(event, ui) {
				setTimeout('scapi.reinitialise();ssapi.reinitialise();',100);
			},
			create: function(event, ui) {
				$(this).tabs('select',-1);
			}
		});*/
		$('#ed_contact-$id_contact .tab' ).draggable('destroy');
		$('#ed_contact-$id_contact .tab').draggable({
			revert: true,
			cursor: 'pointer',
			appendTo: 'body',
			zIndex: 999,
			cursorAt: { top: -10 },
			helper: function(){return $(this).parent().prev().children('.titre').clone().append('<br />> ' + $(this).html());}
		});
		/*$('#ed_contact-$id_contact .bouton.mod').button({ icons: {primary:'ui-icon-pencil'}, text: false});
		$('#ed_contact-$id_contact .bouton.aj').button({ icons: {primary:'ui-icon-plusthick'}, text: false});
		$('#ed_contact-$id_contact .barre').buttonset();*/
		$('#ed_contact-$id_contact .tab, #ed_contact-$id_contact .ui-tabs-panel' ).droppable({
			hoverClass: 'actif',
			accept: '.structures .tab, .categorie',
			tolerance:'pointer',
			drop: function( event, ui ) {
				$( this ).effect('pulsate');
				if ($(ui.draggable).hasClass('categorie')){
					$.post('ajax.php',{
						action:'edition/ass_casquette_categorie',
						id_categorie:$(ui.draggable).parent().attr('id').replace('ed_dynatree-id-',''),
						id_casquette:$(this).dataset('id')
						},
						function(data){
							if (data.succes==1) {
								eval(data.js);
							}
						},
						'json')

				}			
				if ($(ui.draggable).hasClass('tab')){
					$.post('ajax.php',{
						action:'edition/ass_casquette_etablissement',
						id_etablissement:$(ui.draggable).dataset('id'),
						id_casquette:$(this).dataset('id')
					},
					function(data){
						if (data.succes==1) {
							eval(data.js);
						}
					},
					'json')
				}			
			
			}
		});
		$('#ed_contacts .contacts>div.titre' ).droppable({
			hoverClass: 'actif',
			accept: '.structures .tab',
			tolerance:'pointer',
			drop: function( event, ui ) {		
				if ($(ui.draggable).hasClass('tab')){
					$.post('ajax.php',{
						action:'edition/aj_casquette_etab',
						id_etablissement:$(ui.draggable).dataset('id'),
						id_contact:$(this).parent().dataset('id')
					},
					function(data){
						if (data.succes==1) {
							eval(data.js);
						}
					},
					'json')
				}			
		
			}
		});
		
		$('#ed_contact-$id_contact .tab').contextMenu({
			menu: 'ed_menu_casquette'
			},
			function(action, el, pos) {
				if(action=='edit') {
					var panel=$(el).parent().parent();
					var ipanel=$(el).parent().children('li').index($(el));
					var id=$(el).dataset('id');
					if($('#mcas'+ $(el).dataset('id')).length == 0) {
						 $('<div id=\\'mcas'+ $(el).dataset('id') + '\\'></div>').dialog({
							position:[panel.offset().left+panel.width()-310-20*ipanel,panel.offset().top+10+30*ipanel],
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['form' + $(el).attr('id')];
							},
						});	
					$.post('ajax.php',{
						action:'edition/mcasquette',
						id_casquette:id
						},
						function(data){
							if (data.succes==1) {
								$('#mcas'+ id).dialog('option',{title:data.titre});
								$('#mcas'+ id).html(data.html);
								eval(data.js);
							}
						},
						'json');
					}
					else {
						$('#mcas'+ $(el).dataset('id')).dialog( 'moveToTop' );
					}
				}
				if(action=='rename') {
					var panel=$(el).parent().parent();
					var id=$(el).dataset('id');
					if($('#rncas'+ $(el).dataset('id')).length == 0) {
						 $('<div id=\\'rncas'+ $(el).dataset('id') + '\\'></div>').dialog({
							position:[panel.offset().left+20,panel.offset().top+10],
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['form' + $(el).attr('id')];
							},
						});	
					$.post('ajax.php',{
						action:'edition/rncasquette',
						id_casquette:id
						},
						function(data){
							if (data.succes==1) {
								$('#rncas'+ id).dialog('option',{title:data.titre});
								$('#rncas'+ id).html(data.html);
								eval(data.js);
							}
						},
					'json');
				}
				else {
					$('#rncas'+ $(el).dataset('id')).dialog( 'moveToTop' );
				}
			}
		});
";
		$c=new Contact($id_contact);
		foreach ($c->casquettes as $id=>$casquette) {
			$js.=js::casquette($id);
		}
		return $js;		
	}
        public function structure($id_structure)
        {
		$js="
	/*$('#ed_structure-$id_structure.tabs' ).tabs('destroy');
	$('#ed_structure-$id_structure.tabs' ).tabs({
		collapsible: 'true',
		select: function(event, ui) {
			setTimeout('scapi.reinitialise();ssapi.reinitialise();',100);
		},
		create: function(event, ui) {
			$(this).tabs('select',-1);
		}
	});*/
	$('#ed_structure-$id_structure .tabs' ).draggable('destroy');
	$('#ed_structure-$id_structure .tab').draggable({
		revert: true,
		cursor: 'pointer',
		appendTo: 'body',
		zIndex: 999,
		cursorAt: { top: -10 },
		helper: function(){return $(this).parent().prev().children('.titre').clone().append('<br />> ' + $(this).html());}
	});
	/*$('#ed_structure-$id_structure .bouton.mod').button({ icons: {primary:'ui-icon-pencil'}, text: false});
	$('#ed_structure-$id_structure .bouton.aj').button({ icons: {primary:'ui-icon-plusthick'}, text: false});
	$('#ed_structure-$id_structure .barre').buttonset();*/
	$('#ed_structure-$id_structure .tab, #ed_structure-$id_structure .ui-tabs-panel' ).droppable({
		hoverClass: 'actif',
		accept: '.contacts .tab',
		tolerance:'pointer',
		drop: function( event, ui ) {
			$( this ).effect('pulsate');
			$.post('ajax.php',{
					action:'edition/ass_casquette_etablissement',
					id_casquette:$(ui.draggable).dataset('id'),
					id_etablissement:$(this).dataset('id')
				},
				function(data){
					if (data.succes==1) {
						eval(data.js);
					}
				},
				'json')
		}
	});
	$('#ed_structure-$id_structure .tab').contextMenu({
		menu: 'ed_menu_etablissement'
		},
		function(action, el, pos) {
			if(action=='edit') {
				var panel=$(el).parent().parent();
				var ipanel=$(el).parent().children('li').index($(el));
				var id=$(el).dataset('id');
				if($('#metab'+ $(el).dataset('id')).length == 0) {
					$('<div id=\"metab'+ $(el).dataset('id') + '\"></div>').dialog({
						position:[panel.offset().left+panel.width()-310-20*ipanel,panel.offset().top+10+30*ipanel],
						dialogClass: 'css-structure',
						resizable: false,
						close:function(){ 
							$(this).remove();
							delete window['form' + $(el).attr('id')];
						}
					});	
					$.post('ajax.php',{
						action:'edition/metablissement',
						id_etablissement:id
						},
						function(data){
							if (data.succes==1) {
								$('#metab'+ id).dialog('option',{title:data.titre});
								$('#metab'+ id).html(data.html);
								eval(data.js);
							}
						},
						'json'
					);
				}
				else {
					$('#metab'+ $(el).dataset('id')).dialog( 'moveToTop' );
				}
			}
			console.log(action);
			if(action=='rename') {
				var panel=$(el).parent().parent();
				var id=$(el).dataset('id');
				if($('#rnetab'+ $(el).dataset('id')).length == 0) {
					$('<div id=\"rnetab'+ $(el).dataset('id') + '\"></div>').dialog({
						position:[panel.offset().left+20,panel.offset().top+10],
						dialogClass: 'css-structure',
						resizable: false,
						close:function(){ 
							$(this).remove();
							delete window['form' + $(el).attr('id')];
						}
					});	
					$.post('ajax.php',{
						action:'edition/rnetablissement',
						id_etablissement:id
						},
						function(data){
							if (data.succes==1) {
								$('#rnetab'+ id).dialog('option',{title:data.titre});
								$('#rnetab'+ id).html(data.html);
								eval(data.js);
							}
						},
						'json'
					);
				}
				else {
					$('#rnetab'+ $(el).dataset('id')).dialog( 'moveToTop' );
				}
			}
		});
		
";
		$s=new Structure($id_structure);
		foreach ($s->etablissements as $id=>$etablissement) {
			$js.=Js::etablissement($id);
		}
		return $js;		
	}
        public function casquette($id_casquette)
        {
		$js="
		$('#ed_casquette-$id_casquette .dynatree-node').contextMenu({
			menu: 'ed_menu_categorie_contact',
			},
			function(action, el, pos) {
				$.post('ajax.php',
					{
						action:'edition/deass_casquette_categorie',
						id_casquette:$(el).parent().parent().parent().dataset('id'), 
						id_categorie:$(el).dataset('id')
					},
					function(data){
						if(data.succes==1){
							eval(data.js);
						}
					},
				'json');
			});
		";
		return $js;	
	}
        public function casquette_selection($id_casquette)
        {
		$js="
		$('#sel_casquette-$id_casquette .dynatree-node').contextMenu({
			menu: 'sel_menu_categorie_contact',
			},
			function(action, el, pos) {
				$.post('ajax.php',
					{
						action:'selection/deass_casquette_categorie',
						id_casquette:$(el).parent().parent().parent().parent().dataset('id'), 
						id_categorie:$(el).dataset('id')
					},
					function(data){
						if(data.succes==1){
							eval(data.js);
						}
					},
				'json');
			}
		);
		$('#sel_casquette-$id_casquette .titre').draggable({
			revert: true,
			cursor: 'pointer',
			appendTo: 'body',
			zIndex: 999,
			cursorAt: { top: -10 },
			helper: function(){return $(this).clone();}
		});
		";
		return $js;	
	}
        public function entetes_email()
        {
		$js="
			$('#email .mail-entete').contextMenu({menu: 'mail_menu_email'},
				function(action, el, pos) {
					if(action=='rename') {
						var id=el.dataset('id');
						if($('#rnemail'+ id).length == 0) {
							 $('<div id=\"rnemail'+ id + '\"></div>').dialog({
								resizable: false,
								close:function(){ 
									$(this).remove();
									delete window['formrnemail'+ id];
								},
							});	
							$.post('ajax.php',{
								action:'email/rnemail',
								id_email:id
								},
								function(data){
									if (data.succes==1) {
										$('#rnemail'+ id).dialog('option',{title:data.titre});
										$('#rnemail'+ id).html(data.html);
										eval(data.js);
									}
								},
								'json'
							);
						}
						else {
							$('#rnemail'+ id).dialog( 'moveToTop' );
						}
					}
					if(action=='delete') {
						var id=el.dataset('id');
						$('<div>Suppression de <b>'+ el.html() +'</b> ?</div>').dialog({
							resizable: false,
							title:'Etes vous sûr de vouloir supprimer ?',
							modal: true,
							close:function(){ 
								$(this).remove();
							},
							buttons: {
								Supprimer: function() {
									$(this).dialog('close');
									$.post('ajax.php',{
										action:'email/sup_email',
										id_email:id
										},
										function(data){
											if (data.succes==1) {
												eval(data.js);
											}
										},
										'json'
									);
								},
								Annuler: function() {
									$(this).dialog('close');
								}
							}
						});
					}
				}
			);

		";
		return $js;	
	}
        public function etablissement($id_etablissement)
        {
		$js="
		$('#ed_contactsEtab-$id_etablissement button.moins').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#ed_contactsEtab-$id_etablissement li a').each(function(i,e){
			$(e).click(function(){
				if($('#mcas'+ $(e).dataset('id')).length == 0) {
					 $('<div id=\\'mcas'+ $(e).dataset('id') + '\\'></div>').dialog({
						resizable: false,
						close:function(){ 
							$(this).remove();
							delete window['form' + $(e).attr('id')];
						},
					});	
				$.post('ajax.php',{
					action:'edition/mcasquette',
					id_casquette:$(e).dataset('id')
					},
					function(data){
						if (data.succes==1) {
							$('#mcas'+ $(e).dataset('id')).dialog('option',{title:data.titre});
							$('#mcas'+ $(e).dataset('id')).html(data.html);
							eval(data.js);
						}
					},
					'json');
				}
				else {
					$('#mcas'+ $(e).dataset('id')).dialog( 'moveToTop' );
				}
			});
		});
		$('#ed_etablissement-$id_etablissement .dynatree-node').contextMenu({
			menu: 'ed_menu_categorie_contact',
			},
			function(action, el, pos) {
				$.post('ajax.php',
					{
						action:'edition/deass_casquette_categorie',
						id_casquette:$(el).parent().parent().parent().dataset('idcas'), 
						id_categorie:$(el).dataset('id')
					},
					function(data){
						if(data.succes==1){
							eval(data.js);
						}
					},
				'json');
			}
		);";
		return $js;		
	}
	public function emailing_envois()
        {
		$js="emailing_sesapi.reinitialise();";
		return $js;		
	}
	public function emailing_envoi()
        {
		$js="emailing_seapi.reinitialise();";
		return $js;		
	}
	public function publipostage_supports()
        {
		$js="$('#publipostage .publipostage-support').contextMenu({menu: 'publipostage_menu_support'},
			function(action, el, pos) {
				if(action=='rename') {
					var id=el.dataset('id');
					if($('#rnsup'+ id).length == 0) {
						 $('<div id=\"rnsup'+ id + '\"></div>').dialog({
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formrnsup'+ id];
							},
						});	
						$.post('ajax.php',{
							action:'publipostage/rnsupport',
							id_support:id
							},
							function(data){
								if (data.succes==1) {
									$('#rnsup'+ id).dialog('option',{title:data.titre});
									$('#rnsup'+ id).html(data.html);
									eval(data.js);
								}
							},
							'json'
						);
					}
					else {
						$('#rnsup'+ id).dialog( 'moveToTop' );
					}
				}
				if(action=='edit') {
					var id=el.dataset('id');
					if($('#msup'+ id).length == 0) {
						 $('<div id=\"msup'+ id + '\" class=\"local_publipostage\"></div>').dialog({
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formmsup'+ id];
							},
						});	
						$.post('ajax.php',{
							action:'publipostage/msupport',
							id_support:id
							},
							function(data){
								if (data.succes==1) {
									$('#msup'+ id).dialog('option',{title:data.titre});
									$('#msup'+ id).html(data.html);
									eval(data.js);
								}
							},
							'json'
						);
					}
					else {
						$('#msup'+ id).dialog( 'moveToTop' );
					}
				}
				if(action=='delete') {
					var id=el.dataset('id');
					$('<div>Suppression de <b>'+el.html() +'</b> ?</div>').dialog({
						resizable: false,
						title:'Etes vous sûr de vouloir supprimer ?',
						modal: true,
						close:function(){ 
							$(this).remove();
						},
						buttons: {
							Supprimer: function() {
								$(this).dialog('close');
								$.post('ajax.php',{
									action:'publipostage/sup_support',
									id_support:id
									},
									function(data){
										if (data.succes==1) {
											eval(data.js);
										}
									},
									'json'
								);
							},
							Annuler: function() {
								$(this).dialog('close');
							}
						}
					});
				}
			}
		);";
		return $js;
	}
	public function categories($idparent)
        {
		$js="";
		return $js;		
	}
	public function nbincat($id_categorie)
        {
		$js="";
		return $js;		
	}
	public function upload()
        {
		$js="
$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload();
    $('#fileupload').bind('fileuploadadd', function (e, data) {console.log('add');setTimeout(mail_smapi.reinitialise,1000);});
    $('#fileupload').bind('fileuploaddone', function (e, data) {emailImages.push(['fichiers/emails/'+$('.enr-email').dataset('id')+'/'+ data.result[0].name])});
    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'blueimp.github.com') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            resizeMaxWidth: 1920,
            resizeMaxHeight: 1200
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<span class=\"alert alert-error\"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $('#fileupload').each(function () {
            var that = this;
            $.getJSON(this.action, function (result) {
                if (result && result.length) {
                    $(that).fileupload('option', 'done')
                        .call(that, null, {result: result});
                }
            });
        });
    }

});
";
		return $js;		
	}

}
?>
