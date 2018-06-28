<?php
	include_once("cms/public/api.php");
	
	$api->template = '/empty.html';
	
	if($obj = $api->objects->getFullObject(4873)){
		$api->header(array('page-title'=>'Защита данных'));
		$body = '<h1><!--#page-title#--></h1>' . $obj['Текст'];
	}else{
		$body = '<h1>Данный раздел находится в разработке</h1>';
	}
	
	echo $body;
	
	$api->footer();
?>