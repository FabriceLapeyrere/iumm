 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$(function() {
	mail_sm = $('#mail_email').jScrollPane({
		mouseWheelSpeed:15
	});
	mail_smapi = mail_sm.data('jsp');
	mail_se = $('#mail_entetes').jScrollPane({
		mouseWheelSpeed:15
	});
	mail_seapi = mail_se.data('jsp');
	$('#mail_entetes_head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#mail_entetes_head .filtre button").click();
		}
	});
	$('#mail_entetes_head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'email/entetes',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#mail_entetes .jspPane').html(data.html);
				$('#mail_entetes_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#mail_entetes').on('click','.mail-entete', function(){
		var id_email=$(this).dataset('id');
		$.post('ajax.php',{
			action:'email/memail',
			id_email:id_email,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#mail_email .jspPane').html(data.html);
				eval(data.js);
			}
		},
		'json');
	});
	$('#mail_entetes_head').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'email/entetes', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#mail_entetes .jspPane').html(data.html);
					$('#mail_entetes_head .pagination').html(data.pagination);
					eval(data.js);
					mail_seapi.reinitialise();
				}
			},'json'
		);
	});
	/*$('#mail_email').on('click', '.mod-email', function(){
		var id=$(this).dataset('id');
		$.post('ajax.php',{action:'email/memail', id_email:id, format:'html'},
			function(data){
				if (data.succes==1) {
					$('#mail_email .jspPane').html(data.html);
					eval(data.js);
				}
			},
			'json'
		);
	});*/
	$('#mail_email').on('click', '.enr-email.on', function(){
		var id=$(this).dataset('id');
		$.post('ajax.php',{action:'email/mod_email', id_email:id, html:$( 'textarea.editor' ).val(), pj:'', format:'html'},
			function(data){
				if (data.succes==1) {
					eval(data.js);
				}
			},
			'json'
		);
	});
	$('#mail_email').on('click', '.env-email', function(){
		var id=$(this).dataset('id');
		$.post('ajax.php',{action:'email/mod_email', id_email:id, html:$( 'textarea.editor' ).val(), pj:'', format:'html'},
			function(data){
				if (data.succes==1) {
					eval(data.js);
				}
			},
			'json'
		);
		if($('#eemail'+id).length == 0) {
			$('<div id="eemail'+id+'" class="local_email"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formeemail' + id];
				}
			});	
			$.post('ajax.php',{action:'mailing/eemail', id_email:id, format:'html'},
				function(data){
					if (data.succes==1) {
						$('#eemail'+id).dialog('option',{title:data.titre});
						$('#eemail'+id).html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#eemail'+id).dialog('moveToTop');
		
	});
	$('#mail_entetes_head').on('click', 'button.ajmain', function(){
		if($('#nemail').length == 0) {
			$('<div id="nemail" class="local_email"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formnemail'];
				}
			});	
			$.post('ajax.php',{action:'email/nemail'},
				function(data){
					if (data.succes==1) {
						$('#nemail').dialog('option',{title:data.titre});
						$('#nemail').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nemail').dialog('moveToTop');
	});
	$('#email .mail-entete').contextMenu({menu: "mail_menu_email"},
				function(action, el, pos) {
					if(action=='rename') {
						var id=el.dataset('id');
						if($('#rnemail'+ id).length == 0) {
							 $('<div id="rnemail'+ id + '" class="local_email"></div>').dialog({
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
	mail_ajuste=function(){
		var W=window.innerWidth;
		var H=window.innerHeight
		$("#email").css({
			'top': '0px',
			'left': '0px',
			'width': W+'px',
			'height': H+'px'
		});
		$("#mail_entetes_head").css({
			'top': '50px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': '120px'
		
		});
		$("#mail_entetes").css({
			'top': '170px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': (H-170)+'px'
		
		});
		$("#mail_email").css({
			'top': '50px',
			'left':'10px',
			'width':parseInt(2*W/3.-25.)+'px',
			'height': (H-50)+'px'
		
		});
		
		mail_seapi.reinitialise();
		mail_smapi.reinitialise();
	}
	$.post('ajax.php',{action:'email/memail', format:'html'},
			function(data){
				if (data.succes==1) {
					$('#mail_email .jspPane').html(data.html);
					eval(data.js);
					mail_ajuste();
				}
			},
			'json'
		);
	$(window).resize(mail_ajuste);
	emailImages = function(){
		var tab=[];
		$('#fileupload .template-download .name a').each(function(i,e){
			var valid_extensions = /(.JPG|.jpg|.JPEG|.jpeg|.GIF|.gif|.PNG|.png)$/i;
			var fichier=$(e).attr('href');
			if(valid_extensions.test(fichier))
			{ 
  				tab.push([fichier]);
			}
		});
		return tab;
	}
	mail_ajuste();

});
