<?php
$id=$_POST['id'];
$t=microtime();
Index::update($id);
$t1=microtime()-$t;
if(DEBUG_LOG) error_log(date('d/m/Y H:i:s')." - index $id $t1 \n", 3, "tmp/index.log");

?>
