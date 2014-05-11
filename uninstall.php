<?php
$table = new Temp();
$table->drop();

$table_configuration = new configuration();
$table_configuration->delete(array('key'=>'plugin_temp_port'));



?>
