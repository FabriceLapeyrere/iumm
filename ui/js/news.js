 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$(function() {
	news_sn = $('#news_news').jScrollPane({
		mouseWheelSpeed:15
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
				$('#news_news .jspPane').html(data.html);
				eval(data.js);
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
	$('#news_news').on('click', '.enr-news.on', function(){
		var id=$(this).dataset('id');
		$.post('ajax.php',{action:'news/mod_news', id_news:id, html:$( 'textarea.editor' ).val(), pj:'', format:'html'},
			function(data){
				if (data.succes==1) {
					eval(data.js);
				}
			},
			'json'
		);
	});
	$('#news_news').on('click', '.env-news', function(){
		var id=$(this).dataset('id');
		$.post('ajax.php',{action:'news/mod_news', id_news:id, html:$( 'textarea.editor' ).val(), pj:'', format:'html'},
			function(data){
				if (data.succes==1) {
					eval(data.js);
				}
			},
			'json'
		);
		if($('#enews'+id).length == 0) {
			$('<div id="enews'+id+'" class="local_news"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formenews' + id];
				}
			});	
			$.post('ajax.php',{action:'newsing/enews', id_news:id, format:'html'},
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
		$("#news_news").css({
			'top': '50px',
			'left':'10px',
			'width':parseInt(2*W/3.-25.)+'px',
			'height': (H-50)+'px'
		
		});
		
		news_seapi.reinitialise();
		news_snapi.reinitialise();
	}
	$.post('ajax.php',{action:'news/mnews', format:'html'},
			function(data){
				if (data.succes==1) {
					$('#news_news .jspPane').html(data.html);
					eval(data.js);
					news_ajuste();
				}
			},
			'json'
		);
	$(window).resize(news_ajuste);
	news_ajuste();

});
