<?
#config
$base_id = 14;
$class_id = 14;
$pass_field_id = 41;
$hash_field_id = 45;

include('cms/public/api.php');
$vars = array(
	"ru"=>array(
		"title"=>'Восстановление пароля',
		"ok"=>'Пароль успешно изменен. Новый пароль:',
		"error"=>'Ошибка. Попробуйте восстановить пароль повторно.',
		"Новый пароль на сайте "=>'Новый пароль на сайте '
	),
	"en"=>array(
		"title"=>'Password recovery',
		"ok"=>'Password was changed successfully. New passsword is:',
		"error"=>'Error. Try again later.',
		"Новый пароль на сайте "=>'New password on '
	),
	"kz"=>array(
		"title"=>'Парольді қалпына келтіу',
		"ok"=>'Парольқалпына келтірілді. Жаңа пароль:',
		"error"=>'Error. Try again later.',
		"Новый пароль на сайте "=>'Жаңа пароль: '
	)
);
$api->header(array('page-title'=>'<!--object:[127][18]-->'));
if(isset($_GET['h'])){
	if(!!$line = $api->db->select("class_".$class_id, "WHERE `field_".$hash_field_id."`='".$api->db->prepare($_GET['h'])."' LIMIT 1") ){
		$mail = $api->db->select("objects", "WHERE `id` = ".$line['object_id']." LIMIT 1", "name");
		$pass = $api->genPass();
		$api->db->update("class_".$class_id, array("field_".$hash_field_id=>'', "field_".$pass_field_id=>sha1($pass)), "WHERE `id`='".$line['id']."'");
		echo '<p class="ok">'.$vars[$api->lang]['ok'].'</p>'.$pass;
		
		$api->mail->from = 'info@'.str_replace('www.','', $_SERVER['HTTP_HOST']);
		$api->mail->headers = 'X-Mailer: PHP/' . phpversion();
		$api->mail->subject = $vars[$api->lang]['Новый пароль на сайте '].str_replace('www.','', $_SERVER['HTTP_HOST']);
		$api->mail->body = $vars[$api->lang]['ok'].'<br /><strong>'.$pass.'</strong>';
		
		$api->mail->send($mail);
		
	}else header("location: /".$api->lang."/");
} else header("location: /".$api->lang."/");
$api->footer();
?>