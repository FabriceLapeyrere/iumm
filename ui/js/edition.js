 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$(function() {
	$('html').click(function() {
		$('ul.plus').each(function(i,e){
			if($(e).css('display')=='block' && $(e).height()>10) $(e).slideUp();
		});
	});
	ed_sc = $('#ed_contacts').jScrollPane({
		mouseWheelSpeed:15
	});
	ed_scapi = ed_sc.data('jsp');
	ed_ss = $('#ed_structures').jScrollPane({
		mouseWheelSpeed:15
	});
	ed_ssapi = ed_ss.data('jsp');
	ed_scat = $('#ed_categories').jScrollPane({
		mouseWheelSpeed:15
	});
	ed_scatapi = ed_scat.data('jsp');
	$('#ed_categories-head').on('click', 'button.ajmain', function(){
		if($('#ncat').length == 0) {
			$('<div id="ncat" class="local_edition"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formncat' + id];
				}
			});	
			$.post('ajax.php',{action:'edition/ncategorie'},
				function(data){
					if (data.succes==1) {
						$('#ncat').dialog('option',{title:data.titre});
						$('#ncat').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#ncat').dialog('moveToTop');
	});
	$('#ed_contacts-head').on('click', 'button.ajmain', function(){
		if($('#ncont').length == 0) {
			$('<div id="ncont" class="local_edition"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formncont'];
				}
			});	
			$.post('ajax.php',{action:'edition/ncontact'},
				function(data){
					if (data.succes==1) {
						$('#ncont').dialog('option',{title:data.titre});
						$('#ncont').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#ncont').dialog('moveToTop');
	});
	$('#ed_structures-head').on('click', 'button.ajmain', function(){
		if($('#nstr').length == 0) {
			$('<div id="nstr" class="local_edition"></div>').dialog({
				resizable: false,
				dialogClass: 'css-structure',
				close:function(){ 
					$(this).remove();
					delete window['formnstr' + id];
				}
			});	
			$.post('ajax.php',{action:'edition/nstructure'},
				function(data){
					if (data.succes==1) {
						$('#nstr').dialog('option',{title:data.titre});
						$('#nstr').html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#nstr').dialog('moveToTop');
	});
	$('#ed_contacts-head').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'edition/contacts', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#ed_contacts .jspPane').html(data.html);
					$('#ed_contacts-head .pagination').html(data.pagination);
					eval(data.js);
				}
			},'json'
		);
	});
	$('#ed_structures-head').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'edition/structures', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#ed_structures .jspPane').html(data.html);
					$('#ed_structures-head .pagination').html(data.pagination);
					eval(data.js);
				}
			},'json'
		);
	});
	$('#ed_contacts-head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#ed_contacts-head .filtre button").click();
		}
	});
	$('#ed_structures-head').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#ed_structures-head .filtre button").click();
		}
	});
	$('#ed_contacts-head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'edition/contacts',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#ed_contacts .jspPane').html(data.html);
				$('#ed_contacts-head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#ed_structures-head').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'edition/structures',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#ed_structures .jspPane').html(data.html);
				$('#ed_structures-head .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#ed_contacts').on('click', '.dynatree-node', function(){
		var id=$(this).dataset('id');
		$('#ed_tree').dynatree('getTree').getNodeByKey(id).activate();
	});
	$('#ed_structures').on('click', '.dynatree-node', function(){
		var id=$(this).dataset('id');
		$('#ed_tree').dynatree('getTree').getNodeByKey(id).activate();
	});
	$('#ed_contacts').on('click', '.bouton.suppr', function(){
		var id=$(this).dataset('id');
		$('<div>Suppression de <b>'+$(this).parent().parent().parent().prev().children('.titre').html() +', '+ $(this).parent().prev().html()+'</b> ?</div>').dialog({
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
						action:'edition/sup_casquette',
						id_casquette:id
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
	$('#ed_contacts').on('click', '.bouton.supprmain', function(){
		var id=$(this).parent().parent().parent().dataset('id');
		$('<div>Suppression de <b>'+$(this).parent().prev().html() +'</b> ?</div>').dialog({
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
						action:'edition/sup_contact',
						id_contact:id
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
	$('#ed_contacts').on('click', '.bouton.mod', function(){
		var panel=$(this).parent().parent().parent();
		var id=panel.dataset('id');
		if($('#rncont'+ id).length == 0) {
			$('<div id="rncont'+ id + '" class="local_edition"></div>').dialog({
				position:[panel.offset().left+20,panel.offset().top+10],
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formrncont' + id];
				}
			});	
			$.post('ajax.php',{
				action:'edition/rncontact',
				id_contact:id
				},
				function(data){
					if (data.succes==1) {
						$('#rncont'+ id).dialog('option',{title:data.titre});
						$('#rncont'+ id).html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#rncont'+ $(this).dataset('id')).dialog('moveToTop');
	});
	$('#ed_contacts').on('click', '.etabcas', function(){
		var id=$(this).dataset('id');
		if($('#metab'+ id).length == 0) {
			$('<div id="metab'+ id + '" class="local_edition"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formmetab' +id];
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
			$('#metab'+ id).dialog( 'moveToTop' );
		}
	});
	$('#ed_contacts').on('click', '.bouton.aj', function(){
		var id=$(this).parent().parent().parent().dataset('id');
		$.post('ajax.php',{
			action:'edition/aj_casquette',
			id_contact:id
			},
			function(data){
				if (data.succes==1) {
					eval(data.js);
				}
			},
			'json'
		);
	});
	$('#ed_contacts').on('click', '.tab a', function(){
		var panelSel=$(this).parent().dataset('tab');
		$(this).parent().parent().children('li').removeClass('ui-tabs-selected ui-state-active');
		$(this).parent().addClass('ui-tabs-selected ui-state-active');
		if ($(panelSel).hasClass('ui-tabs-hide')) {
			$(this).parent().parent().parent().children('.ui-tabs-panel').addClass('ui-tabs-hide');
			$(panelSel).removeClass('ui-tabs-hide');
		} else {
			$(this).parent().parent().parent().children('.ui-tabs-panel').addClass('ui-tabs-hide');
		}
		setTimeout('ed_scapi.reinitialise();',100);
	});
	$('#ed_structures').on('click', '.tab a', function(){
		var panelSel=$(this).parent().dataset('tab');
		$(this).parent().parent().children('li').removeClass('ui-tabs-selected ui-state-active');
		$(this).parent().addClass('ui-tabs-selected ui-state-active');
		if ($(panelSel).hasClass('ui-tabs-hide')) {
			$(this).parent().parent().parent().children('.ui-tabs-panel').addClass('ui-tabs-hide');
			$(panelSel).removeClass('ui-tabs-hide');
		} else {
			$(this).parent().parent().parent().children('.ui-tabs-panel').addClass('ui-tabs-hide');
		}
		setTimeout('ed_ssapi.reinitialise();',100);
	});
	$('#ed_contacts').on('click', '.casquette button.moins', function(){ 
		var id=$(this).dataset('id');
		$('<div>Désassocier de <b>'+$(this).parent().prev().html()+'</b> ?</div>').dialog({
			resizable: false,
			title:'Etes vous sûr de vouloir désassocier?',
			modal: true,
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				'Désassocier': function() {
					$(this).dialog('close');
					$.post('ajax.php',
						{
							action:'edition/deass_casquette_etablissement',
							id_casquette:id
						},
						function(data){
							if(data.succes==1){
								eval(data.js);
							}
						},
					'json');
			
				},
				Annuler: function() {
					$(this).dialog('close');
				}
			}
		});
	});
	$('#ed_structures').on('click', '.bouton.supprmain', function(){
		var id=$(this).parent().parent().parent().dataset('id');
		$('<div>Suppression de <b>'+$(this).parent().prev().html() +'</b> ?</div>').dialog({
			resizable: false,
			title:'Etes vous sûr de vouloir supprimer ?',
			dialogClass: 'css-structure',
			modal: true,
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Supprimer: function() {
					$(this).dialog('close');
					$.post('ajax.php',{
						action:'edition/sup_structure',
						id_structure:id
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
	$('#ed_structures').on('click', '.contactsEtab button.moins', function(){ 
		var id=$(this).parent().prev().dataset('id');
		$('<div>Désassocier de <b>'+$(this).parent().prev().html()+'</b> ?</div>').dialog({
			resizable: false,
			title:'Etes vous sûr de vouloir désassocier?',
			dialogClass: 'css-structure',
			modal: true,
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				'Désassocier': function() {
					$(this).dialog('close');
					$.post('ajax.php',
						{
							action:'edition/deass_casquette_etablissement',
							id_casquette:id
						},
						function(data){
							if(data.succes==1){
								eval(data.js);
							}
						},
					'json');
			
				},
				Annuler: function() {
					$(this).dialog('close');
				}
			}
		});
	});
	$('#ed_structures').on('click', '.bouton.suppr', function(){
		var id=$(this).dataset('id');
		$('<div>Suppression de <b>'+$(this).parent().parent().parent().prev().children('.titre').html() +', '+ $(this).parent().prev().html()+'</b> ?</div>').dialog({
			resizable: false,
			title:'Etes vous sûr de vouloir supprimer ?',
			dialogClass: 'css-structure',
			modal: true,
			close:function(){ 
				$(this).remove();
			},
			buttons: {
				Supprimer: function() {
					$(this).dialog('close');
					$.post('ajax.php',{
						action:'edition/sup_etablissement',
						id_etablissement:id
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

	$('#ed_structures').on('click', '.bouton.mod', function(){
		var panel=$(this).parent().parent().parent();
		var id=panel.dataset('id');
		if($('#rnstr'+ id).length == 0) {
			$('<div id="rnstr'+ id + '" class="local_edition"></div>').dialog({
				position:[panel.offset().left+20,panel.offset().top+10],
				dialogClass: 'css-structure',
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formrncont' + id];
				},
			});	
			$.post('ajax.php',{
				action:'edition/rnstructure',
				id_structure:id
				},
				function(data){
					if (data.succes==1) {
						$('#rnstr'+ id).dialog('option',{title:data.titre});
						$('#rnstr'+ id).html(data.html);
						eval(data.js);
					}
				},
				'json'
			);
		}
		else $('#rnstr'+ $(this).dataset('id')).dialog('moveToTop')
	});
	$('#ed_structures').on('click', ' .bouton.aj', function(){
		var id=$(this).parent().parent().parent().dataset('id');
		$.post('ajax.php',{
			action:'edition/aj_etablissement',
			id_structure:id
			},
			function(data){
				if (data.succes==1) {
					eval(data.js);
				}
			},
			'json'
		);
	});
	$('#ed_structures').on('click','.contactsEtab li a', function(){
		var e=this;	
		if($('#mcas'+ $(e).dataset('id')).length == 0) {
			 $('<div id="mcas'+ $(e).dataset('id') + '" class="local_edition"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['formmcas' + $(e).dataset('id')];
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
				'json'
			);
		}
		else {
			$('#mcas'+ $(e).dataset('id')).dialog( 'moveToTop' );
		}
	});

	$("#ed_tree").dynatree({
		initAjax:{url:'ajax.php',data:{action:'edition/categories', format:'json'}},
		generateIds:true,
		idPrefix: "ed_dynatree-id-",
		dnd: {
			onDragStart: function(node) {
				/** This function MUST be defined to enable dragging for the tree.
				 *  Return false to cancel dragging of node.
				 */
				console.log('drag start');
				return true;
			},
			onDragStop: function(node) {
				// This function is optional.
				console.log('drag stop');
			},
			autoExpandMS: 1000,
			preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
			onDragEnter: function(node, sourceNode) {
				/** sourceNode may be null for non-dynatree droppables.
				 *  Return false to disallow dropping on node. In this case
				 *  onDragOver and onDragLeave are not called.
				 *  Return 'over', 'before, or 'after' to force a hitMode.
				 *  Return ['before', 'after'] to restrict available hitModes.
				 *  Any other return value will calc the hitMode from the cursor position.
				 */
				console.log('drag enter');
				return true;
			},
			onDragOver: function(node, sourceNode, hitMode) {
				// Prevent dropping a parent below it's own child
				if(node.isDescendantOf(sourceNode)){
				  return false;
				}
				if(hitMode!='over'){
				  return false;
				}
				return "after";
			},
			onDrop: function(node, sourceNode, hitMode, ui, draggable) {
				/** This function MUST be defined to enable dropping of items on
				 * the tree.
				 */
				console.log('drop',node.data.key, sourceNode.data.key);
				$.post('ajax.php',{
					action:'edition/mod_categorie',
					id:sourceNode.data.key,
					id_parent:node.data.key
					},
					function(data){
						if (data.succes==1) {
							if (data.move==1) {
								sourceNode.move(node, hitMode);
								// expand the drop target
								sourceNode.expand(true);
							}
							eval(data.js);
						}
					},
					'json'
				);
				
				
			},
			onDragLeave: function(node, sourceNode) {
				/** Always called if onDragEnter was called.
				 */
				console.log('drag leave');
			}
		},
		onCreate: function(node, span){
			$(span).contextMenu({menu: "ed_menu_categorie"},
				function(action, el, pos) {
					if(action=='rename') {
						var id=node.data.key;
						if($('#rncat'+ id).length == 0) {
							 $('<div id="rncat'+ id + '" class="local_edition"></div>').dialog({
								position:pos,
								resizable: false,
								close:function(){ 
									$(this).remove();
									delete window['formrncat'+ id];
								},
							});	
							$.post('ajax.php',{
								action:'edition/rncategorie',
								id_categorie:id
								},
								function(data){
									if (data.succes==1) {
										$('#rncat'+ id).dialog('option',{title:data.titre});
										$('#rncat'+ id).html(data.html);
										eval(data.js);
									}
								},
								'json'
							);
						}
						else {
							$('#rncat'+ id).dialog( 'moveToTop' );
						}
					}
					if(action=='delete') {
						var id=node.data.key;
						$.post('ajax.php',{
							action:'edition/verif_sup_categorie',
							id_categorie:id
							},
							function(data){
								if (data.succes==1) {
									eval(data.js);
								}
							},
							'json'
						);
					}
				}
			);
		},
		onRender: function(dtnode, nodeSpan){
			
			if (dtnode.data.key==0) {dtnode.expand(true);}
			if (dtnode.data.key>0) {
				$(nodeSpan).droppable({
					accept: ".contacts .tab, .structures .tab",
					tolerance:'pointer',
					drop: function( event, ui ) {
						$( this ).effect('highlight');
						$.post('ajax.php',{
							action:'edition/ass_casquette_categorie',
							id_categorie:$(this).parent().attr('id').replace('ed_dynatree-id-',''),
							id_casquette:$(ui.draggable).dataset('idcas')
							},
							function(data){
								if (data.succes==1) {
									eval(data.js);
								}
							},
							'json');
					}
				});
				/*$(nodeSpan).children('a').draggable({
					revert: true,
					cursor: 'pointer',
					appendTo: 'body',
					zIndex: 999,
					cursorAt: { top: -10, left:0 },
					helper: function(){return $(this).clone();}
				});
				$.post('ajax.php',{
					action:'edition/nbincat',
					id_categorie:$(nodeSpan).parent().attr('id').replace('dynatree-id-',''),
					format:'html'
					},
					function(data){
						if (data.succes==1) {
							$(nodeSpan).find('.nbincat').first().html('('+data.html+')');
							ed_scatapi.reinitialise();
						}
					},
					'json'
				);*/
			}
		}
	});
	
	ed_ajuste=function(){
		W=window.innerWidth;
		H=window.innerHeight-130;
		$("#edition").css({
			'top':'0px',
			'left':'0px',
			'width': W +'px',
			'height': window.innerHeight +'px'
		});
		$("#edition .tabs").css('width',parseInt(W/3.-19.)+'px');
		$("#ed_categories-head").css({
			'top':'30px',
			'left':2*parseInt(W/3.)+'px',
			'width':parseInt(W/3.)+'px',
			'height':'100px'
		});
		$("#ed_categories-head button.ajmain").css({
			'bottom':'5px',
			'left':'5px',
		});
		$("#ed_categories-head .titre").css({
			'bottom':'60px',
			'left':'5px',
		});
		$("#ed_contacts-head").css({
			'top':'30px',
			'left':'0px',
			'width':parseInt(W/3.)+'px',
			'height':'100px'
		});
		$("#ed_contacts-head .pagination").css({
			'bottom':'3px',
			'left':'5px',
		});
		$("#ed_contacts-head .filtre").css({
			'bottom':'35px',
			'left':'5px',
		});
		$("#ed_contacts-head .titre").css({
			'bottom':'60px',
			'left':'5px',
		});
		$("#ed_contacts-head button.ajmain").css({
			'bottom':'35px',
			'left':'247px',
		});
		$("#ed_contacts").css({
			'top':'130px',
			'left':'0px',
			'width':parseInt(W/3.)+'px',
			'height': (H-10) +'px'
		});
		$("#ed_structures-head").css({
			'top':'30px',
			'left':parseInt(W/3.)+'px',
			'width':parseInt(W/3.)+'px',
			'height':'100px'
		});
		$("#ed_structures-head .pagination").css({
			'bottom':'3px',
			'left':'5px',
		});
		$("#ed_structures-head .filtre").css({
			'bottom':'35px',
			'left':'5px',
		});
		$("#ed_structures-head .titre").css({
			'bottom':'60px',
			'left':'5px',
		});
		$("#ed_structures-head button.ajmain").css({
			'bottom':'35px',
			'left':'247px',
		});
		$("#ed_structures").css({
			'top':'130px',
			'left':parseInt(W/3.)+'px',
			'width':parseInt(W/3.)+'px',
			'height': (H-10) +'px'
		});
		$("#ed_categories").css({
			'top':'130px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-10.)+'px',
			'height': (H-10) +'px'
		});
		ed_scapi.reinitialise();
		ed_ssapi.reinitialise();
		ed_scatapi.reinitialise();
		
	}
	$(window).resize(ed_ajuste);
	ed_ajuste();
	ed_contacts=function(){
		/*$('#contacts .tabs' ).tabs({
			collapsible: 'true',
			select: function(event, ui) {
				setTimeout('ed_scapi.reinitialise();ed_ssapi.reinitialise();',100);
			},
			create: function(event, ui) {
				$(this).tabs('select',-1);
			}
		});*/
		$('#ed_contacts .tab').draggable({
			revert: true,
			cursor: 'pointer',
			appendTo: 'body',
			zIndex: 999,
			cursorAt: { top: -10 },
			helper: function(){return $(this).parent().prev().children('.titre').clone().append('<br />> ' + $(this).html());}
		});
		/*$('#contacts .bouton.suppr').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#contacts .bouton.supprmain').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#contacts .casquette button.moins').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#contacts .bouton.mod').button({ icons: {primary:'ui-icon-pencil'}, text: false});
		$('#contacts .bouton.aj').button({ icons: {primary:'ui-icon-plusthick'}, text: false});
		$('#contacts .barre').buttonset();
		$('#contacts-head .pagination a').button();
		$('#contacts-head .pagination').buttonset();*/
		$('#ed_contacts .tab, #ed_contacts .ui-tabs-panel' ).droppable({
			hoverClass: 'actif',
			accept: '.structures .tab, .categorie',
			tolerance:'pointer',
			drop: function( event, ui ) {
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
		$('#ed_contacts .tab').contextMenu({
			menu: 'ed_menu_casquette'
			},
			function(action, el, pos) {
				if(action=='edit') {
					var panel=$(el).parent().parent();
					var ipanel=$(el).parent().children('li').index($(el));
					var id=$(el).dataset('id');
					if($('#mcas'+ $(el).dataset('id')).length == 0) {
						 $('<div id="mcas'+ $(el).dataset('id') + '" class="local_edition"></div>').dialog({
							position:[panel.offset().left+panel.width()-310-20*ipanel,panel.offset().top+10+30*ipanel],
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formmcas' + id];
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
						 $('<div id="rncas'+ $(el).dataset('id') + '" class="local_edition"></div>').dialog({
							position:[panel.offset().left+20,panel.offset().top+10],
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formrncas' + id];
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
			}
		);

		$('#ed_contacts .ui-tabs-panel .dynatree-node').contextMenu({
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
			}
		);
		$('#ed_structures .ui-tabs-panel .dynatree-node').contextMenu({
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
		);
	}	
	ed_structures=function(){
		/*$('#structures .tabs' ).tabs({
			collapsible: 'true',
			select: function(event, ui) {
				setTimeout('ed_scapi.reinitialise();ed_ssapi.reinitialise();',100);
			},
			create: function(event, ui) {
				$(this).tabs('select',-1);
			}
		});*/
		$('#ed_structures .tab').draggable({
			revert: true,
			cursor: 'pointer',
			appendTo: 'body',
			zIndex: 999,
			cursorAt: { top: -10 },
			helper: function(){return $(this).parent().prev().children('.titre').clone().append('<br />> ' + $(this).html());}
		});
		/*$('#structures .bouton.suppr').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#structures .bouton.supprmain').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#structures .contactsEtab button.moins').button({ icons: {primary:'ui-icon-close'}, text: false});
		$('#structures .bouton.mod').button({ icons: {primary:'ui-icon-pencil'}, text: false});
		$('#structures .bouton.aj').button({ icons: {primary:'ui-icon-plusthick'}, text: false});
		$('#structures .barre').buttonset();
		$('#structures-head .pagination a').button();
		$('#structures-head .pagination').buttonset();*/
		
		$('#ed_structures .tab, #ed_structures .ui-tabs-panel').droppable({
			hoverClass: 'actif',
			accept: '.contacts .tab',
			tolerance:'pointer',
			drop: function( event, ui ) {
				$( this ).effect('highlight');
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
		$('#ed_structures .tab').contextMenu({
			menu: 'ed_menu_etablissement'
			},
			function(action, el, pos) {
				if(action=='edit') {
					var panel=$(el).parent().parent();
					var ipanel=$(el).parent().children('li').index($(el));
					var id=$(el).dataset('id');
					if($('#metab'+ $(el).dataset('id')).length == 0) {
						$('<div id="metab'+ $(el).dataset('id') + '" class="local_edition"></div>').dialog({
							position:[panel.offset().left+panel.width()-310-20*ipanel,panel.offset().top+10+30*ipanel],
							dialogClass: 'css-structure',
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formmetab' + id];
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
						$('<div id="rnetab'+ $(el).dataset('id') + '" class="local_edition"></div>').dialog({
							position:[panel.offset().left+20,panel.offset().top+10],
							dialogClass: 'css-structure',
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['formrnetab' + id];
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
			}
		);
		$('#ed_structures .ui-tabs-panel .dynatree-node').contextMenu({
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
		);
	}
	ed_contacts();
	ed_structures();
	no_accent = function (my_string) {
		var new_string = String (my_string);
		new_string = new_string.replace(/(&#x41|&#065;|A|&#x61|&#097;|&#xC0|&#192;|À|&Agrave;|&#xC1|&#193;|Á|&Aacute;|&#xC2|&#194;|Â|&Acirc;|&#xC3|&#195;|Ã|&Atilde;|&#xC4|&#196;|Ä|&Auml;|&#xC5|&#197;|Å|&Aring;|&#xE0|&#224;|à|&agrave;|&#xE1|&#225;|á|&aacute;|&#xE2|&#226;|â|&acirc;|&#xE3|&#227;|ã|&atilde;|&#xE4|&#228;|ä|&auml;|&#xE5|&#229;|å|&aring;)/gi,'a');
		new_string = new_string.replace(/(&#xC7|&#199;|Ç|&Ccedil;|&#xE7|&#231;|ç|&ccedil;)/gi,'c');
		new_string = new_string.replace(/(&#xD0|&#208;|Ð|&ETH;)/gi,'d');
		new_string = new_string.replace(/(&#x45;|&#069;|E|&#x65;|&#101;|&#xC8;|&#200;|È|&Egrave;|&#xC9;|&#201;|É|&Eacute;|&#xCA;|&#202;|Ê|&Ecirc;|&#xCB;|&#203;|Ë|&Euml;|&#xE8;|&#232;|è|&egrave;|&#xE9;|&#233;|é|&eacute;|&#xEA;|&#234;|ê|&ecirc;|&#xEB;|&#235;|ë|&euml;)/gi,'e');
		new_string = new_string.replace(/(&#x49|&#073;|I|&#x69|&#105;|&#xCC|&#204;|Ì|&Igrave;|&#xCD|&#205;|Í|&Iacute;|&#xCE|&#206;|Î|&Icirc;|&#xCF|&#207;|Ï|&Iuml;|&#xEC|&#236;|ì|&igrave;|&#xED|&#237;|í|&iacute;|&#xEE|&#238;|î|&icirc;|&#xEF|&#239;|ï|&iuml;)/gi,'i');
		new_string = new_string.replace(/(&#x4E|&#078;|N|&#x6E|&#110;|&#xD1|&#209;|Ñ|&Ntilde;|&#xF1|&#241;|ñ|&ntilde;)/gi,'n');
		new_string = new_string.replace(/(&#x4F|&#079;|O|&#x6F|&#111;|&#xD2|&#210;|Ò|&Ograve;|&#xD3|&#211;|Ó|&Oacute;|&#xD4|&#212;|Ô|&Ocirc;|&#xD5|&#213;|Õ|&Otilde;|&#xD6|&#214;|Ö|&Ouml;|&#xF2|&#242;|ò|&ograve;|&#xF3|&#243;|ó|&oacute;|&#xF4|&#244;|ô|&ocirc;|&#xF5|&#245;|õ|&otilde;|&#xF6|&#246;|ö|&ouml;|&#xF8|&#248;|ø|&oslash;)/gi,'o');
		new_string = new_string.replace(/(&#x55|&#085;|U|&#x75|&#117;|&#xD9|&#217;|Ù|&Ugrave;|&#xDA|&#218;|Ú|&Uacute;|&#xDB|&#219;|Û|&Ucirc;|&#xDC|&#220;|Ü|&Uuml;|&#xF9|&#249;|ù|&ugrave;|&#xFA|&#250;|ú|&uacute;|&#xFB|&#251;|û|&ucirc;|&#xFC|&#252;|ü|&uuml;)/gi,'u');
		new_string = new_string.replace(/(&#x59|&#089;|Y|&#x79|&#121;|&#xDD|&#221;|Ý|&Yacute;|&#xFD|&#253;|ý|&yacute;|&#xFF|&#255;|ÿ|&yuml;)/gi,'y');
		new_string = new_string.replace(/(&#xC6|&#198;|Æ|&AElig;|&#xE6|&#230;|æ|&aelig;)/gi,'ae');
		new_string = new_string.replace(/(&#x8C|&#140;|Œ|&OElig;|&#x9C|&#156;|œ|&oelig;)/gi,'oe');
		return new_string;
	}
	
});
