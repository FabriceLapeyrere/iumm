<?php
$objet=$_POST['objet'];
$id=$_POST['id'];
$o=new $objet($id);
$o->index();
?>
