$(function() {
	emailing_se = $('#emailing_envoi').jScrollPane({
		mouseWheelSpeed:15
	});
	emailing_seapi = emailing_se.data('jsp');
	emailing_ses = $('#emailing_envois').jScrollPane({
		mouseWheelSpeed:15
	});
	emailing_sesapi = emailing_ses.data('jsp');
	$('#emailing_envois_head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#emailing_envois_head .filtre button").click();
		}
	});
	$('#emailing_envois_head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'mailing/envois',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#emailing_envois .jspPane').html(data.html);
				$('#emailing_envois_head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#emailing_envois_head').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'mailing/envois', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#emailing_envois .jspPane').html(data.html);
					$('#emailing_envois_head .pagination').html(data.pagination);
					eval(data.js);
					emailing_sesapi.reinitialise();
				}
			},'json'
		);
	});
	$('#emailing_envoi').on('click', '.pagination a', function(){
		var id_envoi=$('#emailing_envoi .titre').dataset('id');
		$.post('ajax.php',{action:'mailing/envoi', id_envoi:id_envoi,   format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#emailing_envoi .jspPane').html(data.html);
					eval(data.js);
					emailing_seapi.reinitialise();
				}
			},'json'
		);
	});
	$('#emailing_envoi').on('click','button.pause', function(){
		var id_envoi=$('#emailing_envoi .titre').dataset('id');
		$.post('ajax.php',{
			action:'mailing/pause',
			id_envoi:id_envoi,
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#emailing_envoi').on('click','button.play', function(){
		var id_envoi=$('#emailing_envoi .titre').dataset('id');
		$.post('ajax.php',{
			action:'mailing/play',
			id_envoi:id_envoi,
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#emailing_envoi').on('click','button.recommencer', function(){
		var id_envoi=$('#emailing_envoi .titre').dataset('id');
		$.post('ajax.php',{
			action:'mailing/recommencer',
			id_envoi:id_envoi,
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#emailing_envois').on('click','.emailing-envoi', function(){
		var id_envoi=$(this).dataset('id');
		$.post('ajax.php',{
			action:'mailing/envoi',
			id_envoi:id_envoi,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#emailing_envoi .jspPane').html(data.html);
				eval(data.js);
			}
		},
		'json');
	});
	$('#emailing_envoi').on('click','li.message button.suppr', function(){
		var id=$(this).dataset('id');
		$('<div>Suppression du message adressé à <b>'+ $(this).prev().html() +'</b> ?</div>').dialog({
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
						action:'mailing/sup_message',
						id_message:id
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
	});
	$('#emailing_envoi').on('click','a.supprtt', function(){
		var id=$('#emailing_envoi .titre').dataset('id');
		$('<div>Suppression de tous les messages restants pour cet envoi ?</div>').dialog({
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
						action:'mailing/sup_messages',
						id_envoi:id
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
	});
	emailing_ajuste=function(){
		var W=window.innerWidth;
		var H=window.innerHeight
		$("#emailing").css({
			'top': '0px',
			'left': '0px',
			'width': W+'px',
			'height': H+'px'
		});
		$("#emailing_envois_head").css({
			'top': '50px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': '100px'
		});
		$("#emailing_envois").css({
			'top': '150px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': (H-150)+'px'
		});
		$("#emailing_envoi").css({
			'top': '50px',
			'left':'10px',
			'width':parseInt(2*W/3.-25.)+'px',
			'height': (H-50)+'px'
		});
		emailing_sesapi.reinitialise()
		emailing_seapi.reinitialise()
	}
	emailing_ajuste();
	$(window).resize(emailing_ajuste);
});
