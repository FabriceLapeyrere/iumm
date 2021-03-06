$(function() {
	admin_u = $('#admin_utilisateurs').jScrollPane({
		mouseWheelSpeed:15
	});
	admin_uapi = admin_u.data('jsp');
	admin_e = $('#admin_expediteurs').jScrollPane({
		mouseWheelSpeed:15
	});
	admin_eapi = admin_e.data('jsp');

	$('#admin_utilisateurs_head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#admin_utilisateurs_head .filtre button").click();
		}
	});
	$('#admin_utilisateurs_head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'admin/utilisateurs',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#admin_utilisateurs .jspPane').html(data.html);
				$('#admin_utilisateurs_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#admin_utilisateurs_head').on('click', 'button.ajmain', function(){
		if($('#nutilisateur').length == 0) {
			$('<div id="nutilisateur"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formnutilisateur'];
				}
			});	
			$.post('ajax.php',{action:'admin/nutilisateur'},
				function(data){
					if (data.succes==1) {
						$('#nutilisateur').dialog('option',{title:data.titre});
						$('#nutilisateur').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nutilisateur').dialog('moveToTop');
	});
	$('#admin_expediteurs_head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#admin_expediteurs_head .filtre button").click();
		}
	});
	$('#admin_expediteurs_head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'admin/expediteurs',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#admin_expediteurs .jspPane').html(data.html);
				$('#admin_expediteurs_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#admin_expediteurs_head').on('click', 'button.ajmain', function(){
		if($('#nexpediteur').length == 0) {
			$('<div id="nexpediteur"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formnexpediteur'];
				}
			});	
			$.post('ajax.php',{action:'admin/nexpediteur'},
				function(data){
					if (data.succes==1) {
						$('#nexpediteur').dialog('option',{title:data.titre});
						$('#nexpediteur').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nexpediteur').dialog('moveToTop');
	});
	admin_ajuste=function(){
		var W=window.innerWidth;
		var H=window.innerHeight
		$("#admin").css({
			'top': '0px',
			'left': '10px',
			'width': W-10+'px',
			'height': H+'px'
		});
		$("#admin_utilisateurs_head").css({
			'top': '50px',
			'left':'0px',
			'width':parseInt(W/3.-25.)+'px',
			'height': '120px'
		});
		$("#admin_utilisateurs").css({
			'top': '170px',
			'left':'0px',
			'width':parseInt(W/3.-25.)+'px',
			'height': (H-170)+'px'
		});
		admin_uapi.reinitialise()
		$("#admin_expediteurs_head").css({
			'top': '50px',
			'left':parseInt(W/3)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': '120px'
		});
		$("#admin_expediteurs").css({
			'top': '170px',
			'left':parseInt(W/3)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': (H-170)+'px'
		});
		admin_eapi.reinitialise()
	}
	admin_utilisateurs=function(){
		$('#admin .admin-utilisateur').contextMenu({menu: "admin_menu_utilisateur"},
			function(action, el, pos) {
				if(action=='edit') {
					var id=$(el).dataset('id');
					if($('#mutilisateur'+ $(el).dataset('id')).length == 0) {
						 $('<div id="mutilisateur'+ $(el).dataset('id') + '"></div>').dialog({
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formmutilisateur' + id];
							},
						});	
					$.post('ajax.php',{
						action:'admin/mutilisateur',
						id_utilisateur:id
						},
						function(data){
							if (data.succes==1) {
								$('#mutilisateur'+ id).dialog('option',{title:data.titre});
								$('#mutilisateur'+ id).html(data.html);
								eval(data.js);
							}
						},
						'json');
					}
					else {
						$('#mutilisateur'+ $(el).dataset('id')).dialog( 'moveToTop' );
					}
				}
				if(action=='delete') {
					var id=$(el).dataset('id');
					$('<div>Suppression de <b>'+$(el).html()+'</b> ?</div>').dialog({
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
									action:'admin/sup_utilisateur',
									id_utilisateur:id
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
	admin_expediteurs=function(){
		$('#admin .admin-expediteur').contextMenu({menu: "admin_menu_expediteur"},
			function(action, el, pos) {
				if(action=='edit') {
					var id=$(el).dataset('id');
					if($('#mexpediteur'+ $(el).dataset('id')).length == 0) {
						 $('<div id="mexpediteur'+ $(el).dataset('id') + '"></div>').dialog({
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formmexpediteur' + id];
							},
						});	
					$.post('ajax.php',{
						action:'admin/mexpediteur',
						id_expediteur:id
						},
						function(data){
							if (data.succes==1) {
								$('#mexpediteur'+ id).dialog('option',{title:data.titre});
								$('#mexpediteur'+ id).html(data.html);
								eval(data.js);
							}
						},
						'json');
					}
					else {
						$('#mexpediteur'+ $(el).dataset('id')).dialog( 'moveToTop' );
					}
				}
				if(action=='delete') {
					var id=$(el).dataset('id');
					$('<div>Suppression de <b>'+$(el).html()+'</b> ?</div>').dialog({
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
									action:'admin/sup_expediteur',
									id_expediteur:id
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
	admin_expediteurs();
	admin_utilisateurs();
	admin_ajuste();
	$(window).resize(admin_ajuste);
});	
