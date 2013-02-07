 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$(function() {
	sel_scas = $('#sel_casquettes').jScrollPane({
		mouseWheelSpeed:15
	});
	sel_scasapi = sel_scas.data('jsp');
	sel_sstr = $('#sel_structures').jScrollPane({
		mouseWheelSpeed:15
	});
	sel_sstrapi = sel_sstr.data('jsp');
	sel_scat = $('#sel_categories').jScrollPane({
		mouseWheelSpeed:15
	});
	sel_scatapi = sel_scat.data('jsp');
	$('#sel_casquettes').on('mouseenter', '.casquette', function(){
		$('#etat').html($(this).dataset('id')).show();
	});
	$('#sel_casquettes').on('mouseleave', '.casquette', function(){
		$('#etat').html($(this).dataset('id')).hide();
	});
	$('#sel_action_cat').click(function(){
		if($('#s_action_cat').length == 0) {
			 $('<div id="s_action_cat" class="local_selection"></div>').dialog({
				resizable: false,
				close:function(){ 
					$(this).remove();
					delete window['form' + $(el).attr('id')];
				},
			});	
		$.post('ajax.php',{
			action:'selection/s_action_cat',
			id_casquette:1
			},
			function(data){
				if (data.succes==1) {
					$('#s_action_cat').dialog('option',{title:data.titre});
					$('#s_action_cat').html(data.html);
					eval(data.js);
				}
			},
			'json');
		}
		else {
			$('#s_action_cat').dialog( 'moveToTop' );
		}
	});
	$('#sel_casquettes').on({
			mouseenter: function(){
				var interieur=$(this).find('.titre').height()+$(this).find('.cas').height()
				if ($(this).height()<interieur+20) $(this).css('overflow-y','scroll');
			},
			mouseleave: function(){
				if ($(this).css('overflow-y')=='scroll') $(this).css('overflow-y','hidden');
			}
		},
		'.casquette'
	);
	$('#sel_structures').on('keydown','.filtre input', function(e){
		if (e.keyCode == '13') {
			$("#sel_structures .filtre button").click();
		}
	});
	$('#sel_filtres').on('keydown','input', function(e){
		if (e.keyCode == '13') {
			$(this).next().click();
		}
	});
	$('#sel_filtres').on('click','button.motifs', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'selection/select_motifs',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_filtres').on('click','button.mots', function(){
		var mots=$(this).prev().val();
		$.post('ajax.php',{
			action:'selection/select_mots',
			mots:mots,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_filtres').on('click','.pagination a', function(){
		var binfc=$(this).dataset('binf');
		$.post('ajax.php',{
			action:'selection/select_binfc',
			binfc:binfc,
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_filtres').on('click','button.depts', function(){
		var depts=$(this).prev().val();
		$.post('ajax.php',{
			action:'selection/select_depts',
			depts:depts,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_casquettes').on('click', '.dynatree-node', function(){
		var id=$(this).dataset('id');
		$('#sel_tree').dynatree('getTree').getNodeByKey(id).activate();
		sel_scatapi.scrollToElement($('#sel_dynatree-id-'+id),false,true);
	});
	$('#sel_structures').on('click','.filtre button', function(){
		var motifs=$(this).prev().val();
		$.post('ajax.php',{
			action:'selection/structures',
			motifs:motifs,
			format:'html'
		},
		function(data){
			if (data.succes==1) {
				$('#sel_structures .liste').html(data.html);
				$('#sel_structures .pagination').html(data.pagination);
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_structures').on('click', '.pagination a', function(){
		$.post('ajax.php',{action:'selection/structures', format:'html', binf:$(this).dataset('binf')},function(data){
				if(data.succes==1){
					$('#sel_structures .liste').html(data.html);
					$('#sel_structures .pagination').html(data.pagination);
					eval(data.js);
				}
			},'json'
		);
	});
	$('#sel_humains').on('click','.combiner', function(){
		$.post('ajax.php',{
			action:'selection/combine_recherche'
		},
		function(data){
			if (data.succes==1) {
				$(this).remove();
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_humains').on('click','.decombiner', function(){
		$.post('ajax.php',{
			action:'selection/decombine_recherche'
		},
		function(data){
			if (data.succes==1) {
				$(this).remove();
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_humains').on('click','.et', function(){
		$(this).addClass('ui-state-active');
		$('#sel_humains .ou').removeClass('ui-state-active');
		$.post('ajax.php',{
			action:'selection/select_op_combine',
			op:1
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_decoche').click(function(){
		$('#sel_casquettes .casquette .titre input').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_cass_reset',
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_humains').on('click','.ou', function(){
		$(this).addClass('ui-state-active');
		$('#sel_humains .et').removeClass('ui-state-active');
		$.post('ajax.php',{
			action:'selection/select_op_combine',
			op:0
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_Ncats').change(function(){
		var Ncats= $(this).is(':checked') ? 1 : 0 ;
		$.post('ajax.php',{
			action:'selection/select_ncats',
			Ncats:Ncats
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_Netabs').change(function(){
		var Netabs= $(this).is(':checked') ? 1 : 0 ;
		$.post('ajax.php',{
			action:'selection/select_netabs',
			Netabs:Netabs
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_N').change(function(){
		var N= $(this).is(':checked') ? 1 : 0 ;
		$.post('ajax.php',{
			action:'selection/select_n',
			N:N
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_email').change(function(){
		var email= $('#sel_email').is(':checked') ? 1 : 0 ;
		var semail= $('#sel_semail').is(':checked') ? 1 : 0 ;
		if (semail==1 && email==1) $('#sel_semail').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_email',
			email:email,
			Nemail:0
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_semail').change(function(){
		var email= $('#sel_email').is(':checked') ? 1 : 0 ;
		var semail= $('#sel_semail').is(':checked') ? 1 : 0 ;
		if (semail==1 && email==1) $('#sel_email').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_email',
			email:semail,
			Nemail:1
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_adresse').change(function(){
		var adresse= $('#sel_adresse').is(':checked') ? 1 : 0 ;
		var sadresse= $('#sel_sadresse').is(':checked') ? 1 : 0 ;
		if (sadresse==1 && adresse==1) $('#sel_sadresse').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_adresse',
			adresse:adresse,
			Nadresse:0
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_sadresse').change(function(){
		var adresse= $('#sel_adresse').is(':checked') ? 1 : 0 ;
		var sadresse= $('#sel_sadresse').is(':checked') ? 1 : 0 ;
		if (sadresse==1 && adresse==1) $('#sel_adresse').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_adresse',
			adresse:sadresse,
			Nadresse:1
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_casquettes').on('change','.casquette .titre input',function(){
		var id=$(this).dataset('id');
		if ($(this).is(':checked')) {
			$.post('ajax.php',{action:'selection/select_cas',id_casquette:id},function(data){if(data.succes==1){eval(data.js);}},'json');
		} else {
			$.post('ajax.php',{action:'selection/deselect_cas',id_casquette:id},function(data){if(data.succes==1){eval(data.js);}},'json');
		}
	});
	$('#sel_cas').change(function(){
		var cas= $('#sel_cas').is(':checked') ? 1 : 0 ;
		var scas= $('#sel_scas').is(':checked') ? 1 : 0 ;
		if (scas==1 && cas==1) $('#sel_scas').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_et_cas',
			cas:cas,
			Ncas:0
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_scas').change(function(){
		var cas= $('#sel_cas').is(':checked') ? 1 : 0 ;
		var scas= $('#sel_scas').is(':checked') ? 1 : 0 ;
		if (scas==1 && cas==1) $('#sel_cas').attr('checked',false);
		$.post('ajax.php',{
			action:'selection/select_et_cas',
			cas:scas,
			Ncas:1
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_humains').on('change','#sel_scombinaison',function(){
		var scombinaison= $('#sel_scombinaison').is(':checked') ? 1 : 0 ;
		$.post('ajax.php',{
			action:'selection/select_scombinaison',
			scombinaison:scombinaison,
		},
		function(data){
			if (data.succes==1) {
				eval(data.js);
			}
		},
		'json');
	});
	$('#sel_structures').on('click','.tab',function(){
			var id=$(this).dataset('id');
			if ($(this).hasClass('etab-sel')) {
				$.post('ajax.php',{action:'selection/deselect_etab',id_etablissement:id},function(data){if(data.succes==1){eval(data.js);}},'json');
			} else {
				$.post('ajax.php',{action:'selection/select_etab',id_etablissement:id},function(data){if(data.succes==1){eval(data.js);}},'json');
			}
		}
	);
	selectRec=function(node,sel){
		$('#sel_dynatree-id-'+node.data.key+'>span').removeClass('dynatree-partsel');
		node.selectNoFire(sel);
		var enfants=node.getChildren();
		for(key in enfants){
			selectRec(enfants[key],sel);
		}
	};
	isOneSelectedRec=function(node){
		var test=node.isSelected();
		var enfants=node.getChildren();
		for(key in enfants){
			test= test || isOneSelectedRec(enfants[key]);
		}
		return test
	};
	$("#sel_tree").dynatree({
		initAjax:{url:'ajax.php',data:{action:'selection/categories', format:'json'}},
		generateIds:true,
		idPrefix: "sel_dynatree-id-",
		checkbox: true,
	        selectMode: 2,
	        onSelect: function(select, node) {
			if (select) {
				if ($('#sel_dynatree-id-'+node.data.key+'>span').hasClass('dynatree-partsel')){
					selectRec(node,false);
					var n=node;
					while(n.data.key!='0'){
						n=n.parent;
						if (!isOneSelectedRec(n)) $('#sel_dynatree-id-'+n.data.key+'>span').removeClass('dynatree-partsel');
					}
				} else {
					selectRec(node,true);
					var n=node;
					while(n.data.key!='0'){
						n=n.parent;
						if (!n.isSelected()) $('#sel_dynatree-id-'+n.data.key+'>span').addClass('dynatree-partsel');
					}
				}
			} else {
				if (isOneSelectedRec(node)) $('#sel_dynatree-id-'+node.data.key+'>span').addClass('dynatree-partsel');
				var n=node;
				while(n.data.key!='0'){
					n=n.parent;
					if (!isOneSelectedRec(n)) $('#sel_dynatree-id-'+n.data.key+'>span').removeClass('dynatree-partsel');
				}
			}
			$.post('ajax.php',{action:'selection/select_cat',selection:node.tree.serializeArray()},function(data){if(data.succes==1){eval(data.js);}},'json');
		},

		dnd: {
			onDragStart: function(node) {
				/** This function MUST be defined to enable dragging for the tree.
				 *  Return false to cancel dragging of node.
				 */
				return true;
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
				$.post('ajax.php',{
					action:'selection/mod_categorie',
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
			}
		},
		onExpand : function(dtnode, nodeSpan){
			sel_ajuste_cat();
		},
		onRender: function(dtnode, nodeSpan){
			if (isOneSelectedRec(dtnode) && !dtnode.isSelected()) $('#sel_dynatree-id-'+dtnode.data.key+'>span').addClass('dynatree-partsel');
			if (dtnode.data.key==0) {dtnode.expand(true);setTimeout(sel_ajuste_cat,1000);}
		},
		onPostInit: function(){
				$.post('ajax.php',{
						action:'selection/selection_humains',
						format:'html'
					},function(data){
						if(data.succes==1){
							$('#sel_humains').html(data.html);
							eval(data.js);
							sel_ajuste();
							$("#mask").css('z-index',0);
						}
					},
					'json'
				);
		}
	});
	$("#sel_tree").on('mouseenter','span.dynatree-node',function(){
			$(this).droppable({
				accept: "#sel_casquettes .casquette>.titre",
				tolerance:'pointer',
				drop: function( event, ui ) {
					$( this ).effect('highlight');
					$('body>.titre.ui-draggable').remove();
					$.post('ajax.php',{
						action:'edition/ass_casquette_categorie',
						id_categorie:$(this).parent().attr('id').replace('sel_dynatree-id-',''),
						id_casquette:$(ui.draggable).parent().dataset('id')
						},
						function(data){
							if (data.succes==1) {
								eval(data.js);
							}
						},
						'json');
				}
			});
		}
	);
	$("#sel_tree").on('mouseleave','span.dynatree-node', function(){$(this).droppable("destroy");});
	
	sel_ajuste=function(){
		var hsel=$('#sel_humains').height()+50+$('#sel_filtres').height();
		var W=window.innerWidth;
		var H=window.innerHeight-hsel;
		var Wcas=parseInt(2.*W/3. - 5.);
		var Wpane=Wcas-7;
		var worig=250;
		var wcas=Math.floor(Wpane/Math.floor(Wpane/(worig+7.)))-7;
		$("#selection").css({
			'top': '0px',
			'left': '0px',
			'width': W+'px',
			'height': window.innerHeight+'px'
		});
		$("#sel_humains").css({
			'width':Wcas+'px'
		});
		$("#sel_filtres").css({
			'top':(hsel-$('#sel_filtres').height())+'px',
			'left':'5px',
			'width':Wcas+'px'
		});
		$("#sel_casquettes").css({
			'top':hsel + 'px',
			'left':'5px',
			'width':Wcas+'px',
			'height': (H-10) +'px'
		});
		$("#sel_casquettes .casquette").css({
			'width':wcas+'px'
		});
	
		sel_scasapi.reinitialise();
		sel_ajuste_cat();
	}
	sel_ajuste_cat=function(){
		var W=window.innerWidth;
		var H=window.innerHeight;
		var Hcat=Math.min($('#sel_tree').height(),parseInt((H-10.)/3.));
		var Hstr=H-Hcat-45;
		$("#sel_categories").css({
			'top': 40 +'px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': Hcat +'px'
		});
		$("#sel_structures").css({
			'top':45+Hcat+ 'px',
			'left':parseInt(5. + 2.*W/3.)+'px',
			'width':parseInt(W/3.-25.)+'px',
			'height': Hstr +'px'
		});
		sel_scatapi.reinitialise();
		sel_sstrapi.reinitialise();
	}
	sel_casquettes=function(){
		$('#sel_casquettes .casquette>.titre').draggable({
			revert: true,
			cursor: 'pointer',
			appendTo: 'body',
			zIndex: 999,
			cursorAt: { top: -10 },
			helper: function(){return $(this).clone();}
		});
		$('#sel_casquettes .casquette .cas>div>div>span.dynatree-node').contextMenu({
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
		$('div.cas button.moins').parent().remove();
		$('#sel_casquettes .css-casquette').contextMenu({
			menu: 'sel_menu_casquette'
			},
			function(action, el, pos) {
				if(action=='edit') {
					var id=$(el).parent().parent().dataset('id');
					if($('#mcas'+ id).length == 0) {
						 $('<div id="mcas'+ id + '" class="local_selection"></div>').dialog({
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
				if(action=='renameco') {
					var id=$(el).children('input').dataset('idcont');
					if($('#rncont'+ id).length == 0) {
						 $('<div id="rncont'+ id + '" class="local_selection"></div>').dialog({
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['form' + $(el).attr('id')];
							},
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
						'json');
					}
					else {
						$('#rncont'+ $(el).dataset('id')).dialog( 'moveToTop' );
					}
				}
				if(action=='renamecas') {
					var id=$(el).parent().parent().dataset('id');
					if($('#rncas'+ id).length == 0) {
						 $('<div id="rncas'+ id + '" class="local_selection"></div>').dialog({
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
			}
		);
		$('#sel_casquettes .css-structure').contextMenu({
			menu: 'sel_menu_etablissement'
			},
			function(action, el, pos) {
				if(action=='edit') {
					var id=$(el).children('input').dataset('idetab');
					if($('#metab'+ id).length == 0) {
						$('<div id="metab'+ id + '" class="local_selection"></div>').dialog({
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
				if(action=='renamest') {
					var id=$(el).children('input').dataset('idstr');
					if($('#rnstr'+id).length == 0) {
						$('<div id="rnstr'+ id + '" class="local_selection"></div>').dialog({
							dialogClass: 'css-structure',
							resizable: false,
							close:function(){ 
								$(this).remove();
								delete window['form' + $(el).attr('id')];
							}
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
					else {
						$('#rnstr'+ $(el).dataset('id')).dialog( 'moveToTop' );
					}
				}if(action=='renameet') {
					var id=$(el).children('input').dataset('idetab');
					if($('#rnetab'+id).length == 0) {
						$('<div id="rnetab'+ id + '" class="local_selection"></div>').dialog({
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
			}
		);


	}	
	sel_casquettes();
	sel_ajuste();
	$(window).resize(sel_ajuste);
});
