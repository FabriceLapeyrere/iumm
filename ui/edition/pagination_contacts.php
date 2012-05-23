<?php
 /**
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	$binf=$_POST['binf'];
	$format=$_POST['format'];
	$pagination="";
	$b=0;
	$fin=floor($c->nbcontacts/20)*20;
	$sui=min($binf+20,$fin);
	$pre=max($binf-20,0);
	$pagination.="<a href='#' data-binf='0' class='pagination'><<</a> &nbsp;&nbsp; \n";
	$pagination.="<a href='#' data-binf='$pre' class='pagination'><</a> &nbsp;&nbsp; \n";
	while ($b<$fin){
		if (abs($binf-$b)<=50) $pagination.="<a href='#' data-binf='$b' class='pagination'>$b</a> &nbsp;&nbsp; \n";
		$b+=20;
	}
	$pagination.="<a href='#' data-binf='$sui' class='pagination'>></a> &nbsp;&nbsp; \n";
	$pagination.="<a href='#' data-binf='$fin' class='pagination'>>></a>\n";	
	switch ($format){
		case 'html':
			$reponse['succes']=1;
			$reponse['html']=$pagination;
			break;
	}
?>















