<?
$class_id = 14;
$hash_field_id = 45;

$trans = array(
	'ru' => array(
		'Регистрация прошла успешно! Теперь вы можете авторизоваться в системе.' => 'Регистрация прошла успешно! Теперь вы можете авторизоваться в системе.',
		'Подтверждение регистрации' => 'Подтверждение регистрации',
	),
	'en' => array(
		'Регистрация прошла успешно! Теперь вы можете авторизоваться в системе.' => 'Registration was successful! Now you can log into the system.',
		'Подтверждение регистрации' => 'Confirmation of registration',
	),
	'kz' => array(
		'Регистрация прошла успешно! Теперь вы можете авторизоваться в системе.' => 'Құттықтаймыз! Тіркелу сәтті аяқталды.',
		'Подтверждение регистрации' => 'Тіркелуді растау',
	)
);
include('cms/public/api.php');
$api->header(array('page-title'=>'<!--object:[128][18]-->'));
if(isset($_GET['h'])){
	if(!!$line = $api->db->select("class_".$class_id, "WHERE `field_".$hash_field_id."`='".$api->db->prepare($_GET['h'])."' LIMIT 1") ){
		$api->db->update("class_".$class_id, array("field_".$hash_field_id=>''), "WHERE `id`='".$line['id']."'");
		$api->db->update("objects", array("active"=>'1'), "WHERE `id`='".$line['object_id']."'");
		echo '<p class="ok">'.$trans[$api->lang]['Регистрация прошла успешно! Теперь вы можете авторизоваться в системе.'].'</p>';
	}else header("location: /".$api->lang."/");
} else header("location: /".$api->lang."/");
$api->footer();
?>