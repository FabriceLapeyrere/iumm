$(function() {
	admin_u = $('#admin_utilisateurs').jScrollPane({
		mouseWheelSpeed:15
	});
	admin_uapi = admin_u.data('jsp');
	$('#admin_utilisateurs_head').on('click', 'button.ajmain', function(){
		alert('nouvel utilisateur');
	});
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
	$('#admin .admin-utilisateur').contextMenu({menu: "admin_menu_utilisateur"},
		function(action, el, pos) {
			if(action=='edit') {
				alert('edit');
			}
			if(action=='delete') {
				alert('delete');
			}
		}
	);
	admin_ajuste=function(){
		var W=window.innerWidth;
		var H=window.innerHeight
		$("#admin").css({
			'top': '0px',
			'left': '0px',
			'width': W+'px',
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
	}
	admin_ajuste();
	$(window).resize(admin_ajuste);
});	
