<?
include('cms/public/api.php');
$vars = array(
	'ru' => array(
		'Не верный логин или пароль!' => 'Не верный E-mail или пароль!',
	),
	'en' => array(
		'Не верный логин или пароль!' => 'Wrong E-mail or password!',
	),
	'kz' => array(
		'Не верный логин или пароль!' => 'E-mail немесе пароліңіз қате!',
	),
);
# ---------------------------------------------------------------------------			
# Если послан AJAX запрос
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && isset($_POST['x']) && ($_POST['x']=='secure'))
{
	# Текст ошибки
	$err_msg = '<span class="error">'.$vars[$_REQUEST['lang']]['Не верный логин или пароль!'].'</span>';
	
	if (!empty($_POST['email']) && !empty($_POST['passwd']))
	{
		$lang = $api->lang;
		$api->lang = 'ru';
		if ($u = $api->objects->getFullObjectsListByClass(14, 14, "AND o.active='1' AND o.name='".$api->db->prepare($_POST['email'])."' AND c.field_41='".sha1($_POST['passwd'])."'"))
		{
			$out = array(
				"st"=>'ok',
				"id"=>$u[0]['id'],
				"head"=>$u[0]['head'],
				"name"=>$u[0]['фио'],
				"mail"=>$u[0]['name'],
				"roul"=>$u[0]['Роль'],
				"adres"=>$u[0]['Город'],
			);
				
			$_SESSION['auth']['u'] = $out;
			
			echo '
			<span class="ok">Вход выполнен!</span>
			<script type="text/javascript">
				$("#login_win").dialog("close"); 
				location.reload();
			</script>';
		
		} else {
		$api->lang = $lang;
		  echo $err_msg;
		}	

	} else {
	  echo $err_msg;
	}
}
# ---------------------------------------------------------------------------
?>