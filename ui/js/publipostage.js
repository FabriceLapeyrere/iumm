$(function() {
	publipostage_s = $('#publipostage_support').jScrollPane({
		mouseWheelSpeed:15
	});
	publipostage_sapi = publipostage_s.data('jsp');
	publipostage_ss = $('#publipostage_supports').jScrollPane({
		mouseWheelSpeed:15
	});
	publipostage_ssapi = publipostage_ss.data('jsp');
	$('#publipostage_supports_head').on('click', 'button.ajmain', function(){
		if($('#nsup').length == 0) {
			$('<div id="nsup"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formnsup'];
				}
			});	
			$.post('ajax.php',{action:'publipostage/nsupport'},
				function(data){
					if (data.succes==1) {
						$('#nsup').dialog('option',{title:data.titre});
						$('#nsup').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nsup').dialog('moveToTop');
	});
	$('#publipostage_supports_head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#publipostage_supports_head .filtre button").click();
		}
	});
	$('#publipostage_supports_head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'publipostage/supports',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#publipostage_supports .jspPane').html(data.html);
				$('#publipostage_supports_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#publipostage_supports_head').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'publipostage/supports', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#publipostage_supports .jspPane').html(data.html);
					$('#publipostage_supports_head .pagination').html(data.pagination);
					eval(data.js);
					publipostage_ssapi.reinitialise();
				}
			},'json'
		);
	});
	$('#publipostage_support').on('click', '.pagination a', function(){
		var id_support=$('#publipostage_support .titre').dataset('id');
		$.post('ajax.php',{action:'publipostage/support', id_support:id_support,   format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#publipostage_support .jspPane').html(data.html);
					eval(data.js);
					publipostage_sapi.reinitialise();
				}
			},'json'
		);
	});
	$('#publipostage_supports').on('click','.publipostage-support', function(){
		var id_support=$(this).dataset('id');
		$.post('ajax.php',{
			action:'publipostage/support',
			id_support:id_support,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#publipostage_support .jspPane').html(data.html);
				eval(data.js);
			}
		},
		'json');
	});
	$('#publipostage .publipostage-support').contextMenu({menu: "publipostage_menu_support"},
		function(action, el, pos) {
			if(action=='rename') {
				var id=el.dataset('id');
				if($('#rnsup'+ id).length == 0) {
					 $('<div id="rnsup'+ id + '"></div>').dialog({
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
					 $('<div id="msup'+ id + '" class="local_publipostage"></div>').dialog({
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
				$('<div class="local_publipostage">Suppression de <b>'+el.html() +'</b> ?</div>').dialog({
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
	);
	publipostage_ajuste=function(){
		var W=window.innerWidth;
		var H=window.innerHeight
		$("#publipostage").css({
			'top': '0px',
			'left': '0px',
			'width': W+'px',
			'height': H+'px'
		});
		$("#publipostage_supports_head").css({
			'top': '50px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': '120px'
		});
		$("#publipostage_supports").css({
			'top': '170px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': (H-170)+'px'
		});
		$("#publipostage_support").css({
			'top': '50px',
			'left':'10px',
			'width':parseInt(2*W/3.-25.)+'px',
			'height': (H-50)+'px'
		});
		publipostage_ssapi.reinitialise()
		publipostage_sapi.reinitialise()
	}
	publipostage_ajuste();
	$(window).resize(publipostage_ajuste);
});
