<?
#CONFIG
$head_object_id = 14;
$class_id = 14;

$trans = array(
	'ru' => array(
		'Регистрация' => 'Изменение анкеты',
		'update code' => 'обновить код',
		'Код с картинки введён неверно.' => 'Код с картинки введён неверно.',
		'E-mail адрес не корректен.' => 'E-mail адрес не корректен.',
		'Пользователь с таким e-mail адресом уже есть в системе.' => 'Пользователь с таким e-mail адресом уже есть в системе.',
		'Пароли не совпадают.' => 'Пароли не совпадают.',
		'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.' => 'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.',
		'Ошибка' => 'Ошибка',
		
		'Ф.И.О.' => 'Ф.И.О.',
		'E-mail' => 'E-mail',
		'Не вереный старый пароль.' => 'Не вереный старый пароль.',
		'Старый пароль' => 'Старый пароль',
		'Пароль' => 'Пароль',
		'Пароль повторно' => 'Пароль повторно',
		'День рождения' => 'День рождения',
		'Адрес' => 'Адрес',
		'Телефон' => 'Телефон',
		'Код с картинки' => 'Код с картинки',
		'Сохранить' => 'Сохранить',
		'обязательное поле' => 'обязательное поле',
		'Анкета отредактирована успешно.' => 'Анкета отредактирована успешно.',
		'сменить пароль' => 'Сменить пароль',
		'не менять пароль' => 'Не менять пароль',
	),
	'en' => array(
		'Регистрация' => 'Changing the profile',
		'update code' => 'update code',
		'Код с картинки введён неверно.' => 'Код с картинки введён неверно.',
		'E-mail адрес не корректен.' => 'E-mail address is not correct.',
		'Пользователь с таким e-mail адресом уже есть в системе.' => 'User with this e-mail address is already in the system.',
		'Пароли не совпадают.' => 'Passwords do not match.',
		'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.' => 'You have successfully logged in! Now you can log into the system.',
		'Ошибка' => 'Error',
		
		'Ф.И.О.' => 'Name',
		'E-mail' => 'E-mail',
		'Не вереный старый пароль.' => 'Not true to the old password.',
		'Старый пароль' => 'Old password',
		'Пароль' => 'Password',
		'Пароль повторно' => 'Password again',
		'День рождения' => 'Birthday',
		'Адрес' => 'Address',
		'Телефон' => 'Phone',
		'Код с картинки' => 'Code from image',
		'Сохранить' => 'Save',
		'обязательное поле' => 'requiered field',
		'Анкета отредактирована успешно.' => 'Profile edited successfully.',
		'сменить пароль' => 'Change password',
		'не менять пароль' => 'Does not change password',
	),
	'kz' => array(
		'Регистрация' => 'Анкетаны өзгерту',
		'update code' => 'кодты жаңарту',
		'Код с картинки введён неверно.' => 'Суреттегі кодты қате тердіңіз',
		'E-mail адрес не корректен.' => 'E-mail адресіңіз қате.',
		'Пользователь с таким e-mail адресом уже есть в системе.' => 'Бұл E-mail жүйеде тіркеліп қойылған',
		'Пароли не совпадают.' => 'Парольдер сәйкес емес',
		'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.' => 'Сіз жүйеде сәтті тіркелдіңіз! Енді жүйге авторизациядан өтіп кіре аласыз',
		'Ошибка' => 'Қате',
		
		'Ф.И.О.' => 'Аты-жөніңіз',
		'E-mail' => 'E-mail',
		'Не вереный старый пароль.' => 'Бұрынғы пароліңіз сәйкес емес.',
		'Старый пароль' => 'Бұрынғы пароліңіз',
		'Пароль' => 'Пароль',
		'Пароль повторно' => 'Парольді қайталаңыз',
		'День рождения' => 'Туылған күніңіз',
		'Адрес' => 'Адресіңіз',
		'Телефон' => 'Телефоныңыз',
		'Код с картинки' => 'Суреттегі код',
		'Сохранить' => 'Сақтау',
		'обязательное поле' => 'міндетті түрде толтырылу керек ұяшық',
		'Анкета отредактирована успешно.' => 'Өзгертулер сәтті енгізілді.',
		'сменить пароль' => 'Парольді ауыстыру',
		'не менять пароль' => 'Ауыстырмау',
	)
);

include('cms/public/api.php');
$api->header(array('page-title'=>'<!--object:[130][18]-->'));

?>
<style type="text/css">
.formsubmit input[type="text"], .formsubmit input[type="password"], .formsubmit select{
	padding: 2px 5px;
	margin-bottom: 10px;
	width: 230px;
	height: 20px;
	color: #777;
}
.formsubmit select{
	height: 30px;
	width: 243px;
}
.formsubmit span.error{
	padding-left: 7px;
}
.formsubmit input[type="submit"]{
	margin-top: 15px;
}
</style>
<?

if(@!$AUTH_USER){
	echo '<p class="error">Требуется авторизация.</p>';
	exit( $api->footer() );
}

$lang = $api->lang;
$api->lang = 'ru';
$u = $api->objects->getFullObject($AUTH_USER['id'], false);
$api->lang = $lang;

#ПОШЛА РЕГИСТРАЦИЯ
if(!empty($_REQUEST['reg']) && is_array($reg = $_REQUEST['reg'])){
	$b = false;
	if(!empty($reg['oldpass'])){
		$lang = $api->lang;
		$api->lang = 'ru';
		if (!$user = $api->objects->getFullObjectsListByClass(14, 14, "AND o.active='1' AND o.name='".$api->db->prepare($AUTH_USER['mail'])."' AND c.field_41='".sha1($reg['oldpass'])."'")) {echo '<p class="error">'.$trans[$api->lang]['Не вереный старый пароль.'].'</p>'; $b=true;}
		$api->lang = $lang;
		if(!empty($reg['pass']) && $_REQUEST['pass_check']!=$reg['pass']){
			echo '<p class="error">'.$trans[$api->lang]['Пароли не совпадают.'].'</p>';
			$b = true;
		}
	} 
	if (!$b){
		/*
		#ЗАГРУЗКА ОВОТОРКИ!
		$logo = $u['Аватар'];
		if(!empty($_FILES['logo']['tmp_name']) && !empty($_FILES['logo']['name'])){
			$type = substr($_FILES['logo']['name'], strrpos($_FILES['logo']['name'], '.')+1);
			$new_name = "file_".time()."_".rand(0, 1000000000).($type ? ".".$type : "");
			if(!!@move_uploaded_file( $_FILES['logo']['tmp_name'], _UPLOADS_ABS_."/".$new_name)) $logo = $new_name;
		}
		*/
		$pass = !empty($reg['pass'])?sha1($reg['pass']):$u['Пароль'];
		
		#ПОЛЯ
		$fields = array(
			40=>$api->db->prepare($reg['name']),//фио,
			41=>$pass,//пароль,
			42=>$reg['birthday'],//день рождения,
			43=>$reg['city'],//город проживания
			// 59=>$api->db->prepare($reg['address']), //адрес
			44=>$api->db->prepare($reg['phone'])//телефон
		);
		
		$html = array('<br />');
		$html[] = '<div><b>'.$trans[$api->lang]['Ф.И.О.'].':</b><br />'.$reg['name'].'</div><br /><br />';
		$html[] = '<div><b>'.$trans[$api->lang]['Пароль'].':</b><br />'.$reg['pass'].'</div><br /><br />';
		$html[] = '<div><b>'.$trans[$api->lang]['День рождения'].':</b><br />'.$reg['birthday'].'</div><br /><br />';
		$html[] = '<div><b>'.$trans[$api->lang]['Адрес'].':</b><br />'.$reg['city'].'</div><br /><br />';
		$html[] = '<div><b>'.$trans[$api->lang]['Телефон'].':</b><br />'.$reg['phone'].'</div><br /><br />';
		
		$api->mail->from = 'info@'.str_replace('www.','', $_SERVER['HTTP_HOST']);
		$api->mail->headers = 'X-Mailer: PHP/' . phpversion();
		$api->mail->subject = $trans[$api->lang]['Анкета отредактирована успешно.'];
		$api->mail->body = join("\n", $html);
		
		$api->mail->send($AUTH_USER['mail']);
		
		#ЛОЖИМ В БАЗУ, ВЫДАЕМ СООБЩЕНИЕ
		$lang = $api->lang;
		$api->lang = 'ru';
		if( !!$api->objects->editObjectFields($AUTH_USER['id'], $fields) ){
			$api->lang = $lang;
			echo '<p class="ok">'.$trans[$api->lang]['Анкета отредактирована успешно.'].'</p>';
			$lang = $api->lang;
			$api->lang = 'ru';
			$u = $api->objects->getFullObject($AUTH_USER['id'], false);
			$api->lang = $lang;
		}else{ 
			$api->lang = $lang;
			echo '<p class="error">'.$trans[$api->lang]['Ошибка'].'</p>';
		}
	}
}
?>
<div id="reg-form" class="formsubmit">
	<form method="post" onsubmit="return checkRegForm(this)" enctype="multipart/form-data">
		<div><?=$trans[$api->lang]['Ф.И.О.']?></div>
		<div class="input-row"><input name="reg[name]" type="text" value="<?=@$u['фио']?>" class="textinput" /><span class="error"></span></div>
		<div style="margin:0 0 10px;" class="input-row"><strong><a class="d_b" href="#смена пароля" onclick="return toggleChangePassForm(this)" class="punkt"><?=$trans[$api->lang]['сменить пароль']?></a></strong></div>
		<div id="new-pass-div" style="display:none">
			<div><?=$trans[$api->lang]['Старый пароль']?></div>
			<div class="input-row"><input name="reg[oldpass]" type="password" class="textinput" /><span class="error"></span></div>
			<div><?=$trans[$api->lang]['Пароль']?></div>
			<div class="input-row"><input name="reg[pass]" type="password" class="textinput" /><span class="error"></span></div>
			<div><?=$trans[$api->lang]['Пароль повторно']?></div>
			<div class="input-row"><input name="pass_check" type="password" class="textinput" /><span class="error"></span></div>
			<br />
			<br />
		</div>
		<div><?=$trans[$api->lang]['День рождения']?></div>
		<div class="input-row"><input name="reg[birthday]" type="text" value="<?=@$u['День рождения']?>" class="textinput date-picker" /><span class="error"></span></div>
		<!--<div>Семейное положение</div>
		<div class="input-row">
			<input id="marital-1" name="reg[marital]" type="radio" value="0"<?=(@!$u['Семейное положение']?' checked':'')?> /> <label for="marital-1">Холост / не замужем</label>
			<input id="marital-2" name="reg[marital]" type="radio" value="1"<?=(@$u['Семейное положение']==1?' checked':'')?> /> <label for="marital-2">Женат / замужем</label>
		</div>-->
		<div><?=$trans[$api->lang]['Адрес']?></div>
		<div class="input-row"><textarea name="reg[city]" type="text" style="width: 238px;font-family: tahoma;height: 70px;" ><?=@$u['Город']?></textarea><span class="error"></span></div>
		<div><?=$trans[$api->lang]['Телефон']?></div>
		<div class="input-row"><input name="reg[phone]" type="text" value="<?=@$u['Телефон']?>" class="textinput" /><span class="error"></span></div>
		<div><input type="submit" value="<?=$trans[$api->lang]['Сохранить']?>" class="butintext" /></div>
	</form>
</div>
<script type="text/javascript">
$(function(){
	$('.date-picker').keypress(function(){ 
		return false; 
	}).datepicker();
});

function toggleChangePassForm(link){
	link = $(link);
	$('#new-pass-div').slideToggle('normal');
	if( link.text()=='<?=$trans[$api->lang]['сменить пароль']?>' ) link.text('<?=$trans[$api->lang]['не менять пароль']?>');
	else link.text('<?=$trans[$api->lang]['сменить пароль']?>');
	
	return false;
}

function checkRegForm(f){
	
	var msg = [];
	
	if(f['reg[name]'].value=='') msg.push({field:f['reg[name]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
	else noErrors(f['reg[name]']);
	
	if(!$('#new-pass-div').is(':hidden')){
		if(f['reg[oldpass]'].value=='') msg.push({field:f['reg[oldpass]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
		else{
			noErrors(f['reg[oldpass]']);
			if(f['reg[pass]'].value=='') msg.push({field:f['reg[pass]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
			else{ 
				noErrors(f['reg[pass]']);
				if(f['pass_check'].value=='') msg.push({field:f['pass_check'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
				else if(f['reg[pass]'].value!=f['pass_check'].value) msg.push({field:f['pass_check'], txt:'<?=$trans[$api->lang]['Пароли не совпадают.']?>'});	
				else noErrors(f['pass_check']);
			}
		}
	}else f['reg[pass]'].value='';
	
	
	if(f['reg[birthday]'].value=='') msg.push({field:f['reg[birthday]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['reg[birthday]']);
	
	// if(f['reg[address]'].value=='') msg.push({field:f['reg[address]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	// else noErrors(f['reg[address]']);
	// alert(f['reg[phone]'].value);
	if(f['reg[phone]'].value=='') msg.push({field:f['reg[phone]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['reg[phone]']);
	
	if(msg.length){
		msg[0].field.focus();
		$('.ok').remove();
		return gotErrors(msg);
	}
	return true;
}

function gotErrors(msg){
	for(var i in msg){
		var m = msg[i];
		$(m.field).addClass('error').find('+.error').text(m.txt);
	}
	return false;
}

function noErrors(field){
	$(field).removeClass('error').find('+.error').empty();
}

</script>
<?
$api->footer();
?>