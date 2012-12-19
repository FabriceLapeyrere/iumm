 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$(function() {
	news_sn = $('#news_newsletter').jScrollPane({
		mouseWheelSpeed:15,
	});
	news_snapi = news_sn.data('jsp');
	news_se = $('#news_entetes').jScrollPane({
		mouseWheelSpeed:15
	});
	news_seapi = news_se.data('jsp');
	$('#news_entetes_head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#news_entetes_head .filtre button").click();
		}
	});
	$('#news_entetes_head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'news/entetes',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#news_entetes .jspPane').html(data.html);
				$('#news_entetes_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#news_entetes').on('click','.news-entete', function(){
		var id_news=$(this).dataset('id');
		$.post('ajax.php',{
			action:'news/mnews',
			id_news:id_news,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#news_newsletter .jspPane').html(data.html);
				eval(data.js);
				news_snapi.reinitialise();
			}
		},
		'json');
	});
	$('#news_entetes_head').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'news/entetes', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#news_entetes .jspPane').html(data.html);
					$('#news_entetes_head .pagination').html(data.pagination);
					eval(data.js);
					news_seapi.reinitialise();
				}
			},'json'
		);
	});
	/*$('#news_news').on('click', '.mod-news', function(){
		var id=$(this).dataset('id');
		$.post('ajax.php',{action:'news/mnews', id_news:id, format:'html'},
			function(data){
				if (data.succes==1) {
					$('#news_news .jspPane').html(data.html);
					eval(data.js);
				}
			},
			'json'
		);
	});*/
	$('#news_newsletter').on('click', 'button.modbloc', function(){
		var id_bloc=$(this).dataset('id');
		var id_news=$(this).dataset('idnews');
		if($('#mbloc'+id_news+'_'+id_bloc).length == 0) {
			$('<div id="mbloc'+id_news+'_'+id_bloc+'" class="local_edition"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formmbloc'+id_news+'_'+id_bloc];
				}
			});	
			$.post('ajax.php',{action:'news/mbloc', id_news:id_news, id_bloc:id_bloc},
				function(data){
					if (data.succes==1) {
						$('#mbloc'+id_news+'_'+id_bloc).dialog('option',{title:data.titre});
						$('#mbloc'+id_news+'_'+id_bloc).html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#mbloc'+id_news+'_'+id_bloc).dialog('moveToTop');
	});
	$('#news_newsletter').on('click', 'button.supbloc', function(){
		var id=$(this).dataset('id');
		var id_news=$(this).dataset('idnews');
		$('<div>Suppression du bloc ?</div>').dialog({
			resizable: false,
			title:'Etes vous sûr de vouloir supprimer ?',
			modal: true,
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Supprimer: function() {
					$(this).dialog('close');
					$.post('ajax.php',{action:'news/sup_bloc', id_news:id_news, id_bloc:id},
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
	});
	$('#news_entetes_head').on('click', 'button.ajmain', function(){
		if($('#nnews').length == 0) {
			$('<div id="nnews" class="local_news"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formnnews'];
				}
			});	
			$.post('ajax.php',{action:'news/nnews'},
				function(data){
					if (data.succes==1) {
						$('#nnews').dialog('option',{title:data.titre});
						$('#nnews').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nnews').dialog('moveToTop');
	});
	$('#news_modeles').on('click', 'button.ajmain', function(){
		if($('#nmodele').length == 0) {
			$('<div id="nmodele" class="local_news"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formnmodele'];
				}
			});	
			$.post('ajax.php',{action:'news/nmodele'},
				function(data){
					if (data.succes==1) {
						$('#nmodele').dialog('option',{title:data.titre});
						$('#nmodele').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nmodele').dialog('moveToTop');
	});
	contextmenu_modeles=function(){
		$('ul#modeles>li>ul>li>ul>li').contextMenu({menu: "news_menu_modele"},
			function(action, el, pos) {
				if(action=='edit') {
					var id=el.dataset('id');
					if($('#mmodele'+ id).length == 0) {
						 $('<div id="mmodele'+ id + '" class="local_news"></div>').dialog({
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formmmmodele'+ id];
							},
						});	
						$.post('ajax.php',{
							action:'news/mmodele',
							id_modele:id
							},
							function(data){
								if (data.succes==1) {
									$('#mmodele'+ id).dialog('option',{title:data.titre});
									$('#mmodele'+ id).html(data.html);
									eval(data.js);
								}
							},
							'json'
						);
					}
					else {
						$('#mmodele'+ id).dialog( 'moveToTop' );
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
									action:'news/sup_modele',
									id_modele:id
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
		$('ul.sf-menu').supersubs({minWidth:12, maxWidth:27, extraWidth:1}).superfish();
		$('ul.sf-menu>li>ul>li>ul>li').draggable({
			delay: 500,
			helper:'clone',
			start:function(){$('#modeles ul').hide();}
		});
	}
	contextmenu_modeles();
	news_entetes=function(){
		$('#news .news-entete').droppable({
			hoverClass: 'actif',
			accept: '#news .bloc',
			tolerance: 'pointer',
			drop: function( event, ui ) {		
				$.post('ajax.php',{
					action:'news/copie_bloc',
					id_n_orig:$('#news_content').dataset('id'),
					id_bloc:$(ui.draggable).dataset('id'),
					id_news:$(this).dataset('id')
				},
				function(data){
					if (data.succes==1) {
						eval(data.js);
					}
				},
				'json')
			}
		});
		$('#news .news-entete').contextMenu({menu: "news_menu_news"},
				function(action, el, pos) {
					if(action=='rename') {
						var id=el.dataset('id');
						if($('#rnnews'+ id).length == 0) {
							 $('<div id="rnnews'+ id + '" class="local_news"></div>').dialog({
								resizable: false,
								close:function(){ 
									$(this).remove();
									delete window['formrnnews'+ id];
								},
							});	
							$.post('ajax.php',{
								action:'news/rnnews',
								id_news:id
								},
								function(data){
									if (data.succes==1) {
										$('#rnnews'+ id).dialog('option',{title:data.titre});
										$('#rnnews'+ id).html(data.html);
										eval(data.js);
									}
								},
								'json'
							);
						}
						else {
							$('#rnnews'+ id).dialog( 'moveToTop' );
						}
					}
					if(action=='duplicate') {
						var id=el.dataset('id');
						$.post('ajax.php',{
							action:'news/dup_news',
							id_news:id
							},
							function(data){
								if (data.succes==1) {
									eval(data.js);
								}
							},
							'json'
						);
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
										action:'news/sup_news',
										id_news:id
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
	}
	news_ajuste=function(){
		var W=window.innerWidth;
		var H=window.innerHeight
		$("#news").css({
			'top': '0px',
			'left': '0px',
			'width': W+'px',
			'height': H+'px'
		});
		$("#news_entetes_head").css({
			'top': '50px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': '120px'
		
		});
		$("#news_entetes").css({
			'top': '170px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': (H-170)+'px'
		
		});
		$("#news_modeles").css({
			'top': '50px',
			'left':'10px',
			'width':parseInt(2*W/3.-25.)+'px',
			'height': '50px'
		
		});
		$("#news_newsletter").css({
			'top': '100px',
			'left':'10px',
			'width':parseInt(2*W/3.-25.)+'px',
			'height': (H-100)+'px'
		
		});
		
		news_seapi.reinitialise();
		news_snapi.reinitialise();
	}
	$('.env-news').click(function(){
		var id=$('#news_content').dataset('id');
		if($('#enews'+id).length == 0) {
			$('<div id="enews'+id+'" class="local_news"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formenews' + id];
				}
			});	
			$.post('ajax.php',{action:'mailing/enews', id_news:id, format:'html'},
				function(data){
					if (data.succes==1) {
						$('#enews'+id).dialog('option',{title:data.titre});
						$('#enews'+id).html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#enews'+id).dialog('moveToTop');
		
	});
	$.post('ajax.php',{action:'news/mnews', format:'html'},
		function(data){
			if (data.succes==1) {
				$('#news_newsletter .jspPane').html(data.html);
				eval(data.js);
				news_ajuste();
			}
		},
		'json'
	);
	$(window).resize(news_ajuste);
	news_entetes();
	news_ajuste();
	menuLock=0;
	news_scroll_lock=0;
	news_scroll=0;
	news_monte=function(pas){
		if (news_snapi.getContentPositionY()==0) news_scroll=0;
		if (pas==0) news_scroll=1;
		news_snapi.scrollByY(-20);
		if(news_scroll==1) setTimeout('news_monte(1);',100);
	}
	news_descend=function(pas){
		if (news_snapi.getContentPositionY() - $('#news_newsletter .jspPane').height()+$('#news_newsletter').height()==0) news_scroll=0;
		news_snapi.scrollByY(20);
		if (pas==0) news_scroll=-1;
		if(news_scroll==-1) setTimeout('news_descend(1);',100);
	}
	news_stop=function(){news_scroll=0;news_scroll_lock=0;}
	newsImages = function(){
		var tab=[];
		$('#news_fileupload .template-download .name a').each(function(i,e){
			var valid_extensions = /(.JPG|.jpg|.JPEG|.jpeg|.GIF|.gif|.PNG|.png)$/i;
			var fichier=$(e).attr('href');
			if(valid_extensions.test(fichier))
			{ 
  				tab.push([fichier]);
			}
		});
		return tab;
	}
	newsMajImg=function(){
			var images=newsImages();
			for (index in images) {	
				var r=new RegExp('src="'+images[index][0]);
				var s=new RegExp('min/'+images[index][0].replace(/\\/g,'/').replace( /.*\//, '' ).replace( /\.[^.]*$/, '' ));
				var html=$('#news_content').html();
				console.log($('button[data-url*="'+images[index][0].replace(/\\/g,'/').replace(/.*\//,'' )+'"]').css('display'));
				if (r.test(html) || s.test(html)) {
						$('button[data-url*="'+images[index][0].replace(/\\/g,'/').replace(/.*\//,'' )+'"]').css('display','none').next().remove();
				} else {
						$('button[data-url*="'+images[index][0].replace(/\\/g,'/').replace( /.*\//, '' )+'"]').css('display','inline-block')
				}
			}
		};
});
