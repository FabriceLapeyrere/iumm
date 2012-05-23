 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$(function() {
	$(window).hashchange( function(){
		var hash = location.hash;
		switch(hash)
		{
		case '#edition':
			$("#edition").css('z-index',1);
			$("#selection").css('z-index',0);
			$("#email").css('z-index',0);
			$("#emailing").css('z-index',0);
			$("#publipostage").css('z-index',0);
			$(".local_email, .local_selection, .local_emailing, .local_publipostage").each(function(i,e){
				var id=$(e).attr('id');
				$(e).dialog('close');
				delete window['form' + id];
			})
			break;
		case '#selection':
			$("#edition").css('z-index',0);
			$("#selection").css('z-index',1);
			$("#email").css('z-index',0);
			$("#emailing").css('z-index',0);
			$("#publipostage").css('z-index',0);
			$(".local_email, .local_edition, .local_emailing, .local_publipostage").each(function(i,e){
				var id=$(e).attr('id');
				$(e).dialog('close');
				delete window['form' + id];
			})
			break;
		case '#email':
			$("#edition").css('z-index',0);
			$("#selection").css('z-index',0);
			$("#email").css('z-index',1);
			$("#emailing").css('z-index',0);
			$("#publipostage").css('z-index',0);
			$(".local_selection, .local_edition, .local_emailing, .local_publipostage").each(function(i,e){
				var id=$(e).attr('id');
				$(e).dialog('close');
				delete window['form' + id];
			})
			break;
		case '#emailing':
			$("#edition").css('z-index',0);
			$("#selection").css('z-index',0);
			$("#email").css('z-index',0);
			$("#emailing").css('z-index',1);
			$("#publipostage").css('z-index',0);
			$(".local_selection, .local_edition, .local_email, .local_publipostage").each(function(i,e){
				var id=$(e).attr('id');
				$(e).dialog('close');
				delete window['form' + id];
			})
			break;
		case '#publipostage':
			$("#edition").css('z-index',0);
			$("#selection").css('z-index',0);
			$("#email").css('z-index',0);
			$("#emailing").css('z-index',0);
			$("#publipostage").css('z-index',1);
			$(".local_selection, .local_edition, .local_email, .local_emailing").each(function(i,e){
				var id=$(e).attr('id');
				$(e).dialog('close');
				delete window['form' + id];
			})
			break;
		}
	})
	 
	// Since the event is only triggered when the hash changes, we need to trigger
	// the event now, to handle the hash the page may have loaded with.
	$(window).hashchange();

	infos = function (message){
		if (message!="") {
			$('#infos').queue(function(){
				$(this).html(message);
				$(this).dequeue();
			}).fadeIn('slow').delay(1000).fadeOut('slow');
		}
	}
	up = function(){
		$.post('ajax.php',{
			action:'up',
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	}
	setTimeout(up,30000);
});
