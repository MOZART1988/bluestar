<?
$head_object_id = 14;
$class_id = 14;

$trans = array(
	'ru' => array(
		'Вам отправлено письмо для подтверждения регистрации. Для подтверждения регистрации вашей учётной записи, пожалуйста, следуйте инструкциям в этом письме.' => 'Вам отправлено письмо для подтверждения регистрации. Для подтверждения регистрации вашей учётной записи, пожалуйста, следуйте инструкциям в этом письме.',
		'Пожалуйста, подтвердите свой адрес электронной почты' => 'Пожалуйста, подтвердите свой адрес электронной почты',
		'Регистрация' => 'Регистрация',
		'update code' => 'обновить код',
		'Код с картинки введён неверно.' => 'Код с картинки введён неверно.',
		'E-mail адрес не корректен.' => 'E-mail адрес не корректен.',
		'Имя некорректно' => 'Имя некорректно.',
		'Пользователь с таким e-mail адресом уже есть в системе.' => 'Пользователь с таким e-mail адресом уже есть в системе.',
		'Пароли не совпадают.' => 'Пароли не совпадают.',
		'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.' => 'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.',
		'Ошибка' => 'Ошибка',
		
		'Ф.И.О.' => 'Ф.И.О.',
		'E-mail' => 'E-mail',
		'Пароль' => 'Пароль',
		'Пароль повторно' => 'Пароль повторно',
		'День рождения' => 'День рождения',
		'Адрес' => 'Адрес',
		'Телефон' => 'Телефон',
		'Код с картинки' => 'Код с картинки',
		'Зарегистрироваться' => 'Зарегистрироваться',
		'обязательное поле' => 'обязательное поле',
	),
	'en' => array(
		'Вам отправлено письмо для подтверждения регистрации. Для подтверждения регистрации вашей учётной записи, пожалуйста, следуйте инструкциям в этом письме.' => 'You sent an email to confirm your registration. To confirm the registration of your account, please follow the instructions in this letter.',
		'Пожалуйста, подтвердите свой адрес электронной почты' => 'Please confirm your email address',
		'Регистрация' => 'Registration',
		'update code' => 'update code',
		'Код с картинки введён неверно.' => 'Код с картинки введён неверно.',
		'E-mail адрес не корректен.' => 'E-mail address is not correct.',
		'Имя некорректно' => 'Field "name" is not correct.',
		'Пользователь с таким e-mail адресом уже есть в системе.' => 'User with this e-mail address is already in the system.',
		'Пароли не совпадают.' => 'Passwords do not match.',
		'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.' => 'You have successfully logged in! Now you can log into the system.',
		'Ошибка' => 'Error',
		
		'Ф.И.О.' => 'Name',
		'E-mail' => 'E-mail',
		'Пароль' => 'Password',
		'Пароль повторно' => 'Password again',
		'День рождения' => 'Birthday',
		'Адрес' => 'Address',
		'Телефон' => 'Phone',
		'Код с картинки' => 'Code from image',
		'Зарегистрироваться' => 'Registration',
		'обязательное поле' => 'requiered field',
	),
	'kz' => array(
		'Вам отправлено письмо для подтверждения регистрации. Для подтверждения регистрации вашей учётной записи, пожалуйста, следуйте инструкциям в этом письме.' => 'Сіз енгізген электрондық почта жалған емес екенін растау үшін сіздің электрондық почтаңызға хат жіберілді. Хаттағы нұсқауларды оқыңыз.',
		'Пожалуйста, подтвердите свой адрес электронной почты' => 'Электрондық почтаңызды растау',
		'Регистрация' => 'Тіркелу',
		'update code' => 'кодты жаңарту',
		'Код с картинки введён неверно.' => 'Суреттегі кодты қате тердіңіз',
		'E-mail адрес не корректен.' => 'E-mail адресіңіз қате.',
		'Имя некорректно' => 'Аты-жөніңіз қате.',
		'Пользователь с таким e-mail адресом уже есть в системе.' => 'Бұл E-mail жүйеде тіркеліп қойылған',
		'Пароли не совпадают.' => 'Парольдер сәйкес емес',
		'Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.' => 'Сіз жүйеде сәтті тіркелдіңіз! Енді жүйге авторизациядан өтіп кіре аласыз',
		'Ошибка' => 'Қате',
		
		'Ф.И.О.' => 'Аты-жөніңіз',
		'E-mail' => 'E-mail',
		'Пароль' => 'Пароль',
		'Пароль повторно' => 'Парольді қайталаңыз',
		'День рождения' => 'Туылған күніңіз',
		'Адрес' => 'Адресіңіз',
		'Телефон' => 'Телефоныңыз',
		'Код с картинки' => 'Суреттегі код',
		'Зарегистрироваться' => 'Тіркелу',
		'обязательное поле' => 'міндетті түрде толтырылу керек ұяшық',
	)
);

include('cms/public/api.php');
$api->header(array('page-title'=>'<!--object:[133][18]-->'));
?>
<style type="text/css">
.register input, .register textarea, .register select{vertical-align: top;}
.register textarea{height: 95px; width: 234px; padding: 2px 5px; color: #777; margin-bottom: 10px; font-family: Arial;}
.register input[type="text"], .register input[type="password"], .register select{
	padding: 2px 5px;
	margin-bottom: 10px;
	width: 230px;
	height: 20px;
	color: #777;
}
.register select{
	height: 30px;
	width: 243px;
}
.register span.error{
	padding-left: 7px;
}
.register input[type="submit"]{
	margin-top: 15px;
}
</style>
<?

#ПОШЛА РЕГИСТРАЦИЯ
if(!empty($_REQUEST['reg']) && is_array($reg = $_REQUEST['reg'])){
	if( !preg_match("/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/", ($reg['mail'])) ){
		echo '<p class="error">'.$trans[$api->lang]['E-mail адрес не корректен.'].'</p>';
	}else if( !!($u = $api->db->select("objects", "WHERE `head`='".$head_object_id."' AND `name`='".$reg['mail']."' LIMIT 1")) ){
		echo '<p class="error">'.$trans[$api->lang]['Пользователь с таким e-mail адресом уже есть в системе.'].'</p>';
	}else if($_REQUEST['pass_check']!=$reg['pass']){
		echo '<p class="error">'.$trans[$api->lang]['Пароли не совпадают.'].'</p>';
	}else if($_REQUEST['captcha']!=$_SESSION['captcha_keystring']){
		echo '<p class="error">'.$trans[$api->lang]['Код с картинки введён неверно.'].'</p>';
	}else{
		#ОБЪЕКТ
		$object = array(
			"active"=>0,
			"head"=>$head_object_id,
			"name"=>$reg['mail'],
			"class_id"=>$class_id,
			"sort"=>time()
		);
		/*
		#ЗАГРУЗКА ОВОТОРКИ!
		$logo = '';
		if(!empty($_FILES['logo']['tmp_name']) && !empty($_FILES['logo']['name'])){
			$type = substr($_FILES['logo']['name'], strrpos($_FILES['logo']['name'], '.')+1);
			$new_name = "file_".time()."_".rand(0, 1000000000).($type ? ".".$type : "");
			if(!!@move_uploaded_file( $_FILES['logo']['tmp_name'], _UPLOADS_ABS_."/".$new_name)) $logo = $new_name;
		}*/
		
		// if (!empty($_FILES['fields'])){
			// $smail->add_attachment(file_get_contents($_FILES['fields']['tmp_name'][3]), $_FILES['fields']['name'][3], $_FILES['fields']['type'][3]);
		// }
		
		#ПОЛЯ
		$fields = array(
			40=>$reg['name'],//фио,
			41=>sha1($reg['pass']),//пароль,
			42=>$reg['birthday'],//день рождения,
			43=>$reg['city'],//горд проживания
			44=>$reg['phone']//телефон	
		);
		
		#ЛОЖИМ В БАЗУ, ВЫДАЕМ СООБЩЕНИЕ
		$lang = $api->lang;
		$api->lang = 'ru';
		if( !!$object_id = $api->objects->createObjectAndFields($object, $fields) ){
		
			$base_id = 14;
			$pass_field_id = 41;
			$hash_field_id = 45;
			$hash = md5(time().$object_id.rand(999, 99999999));
			$api->db->update("class_14", array("field_".$hash_field_id=>$hash), "WHERE `object_id`='".$object_id."' LIMIT 1");
			$link = 'http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/confirm/?h='.$hash.'';
			if($api->lang=='ru'){
				$mess = '<p>Добро пожаловать на '.$_SERVER['HTTP_HOST'].'.</p>
				
				<p>Прежде, чем вы сможете использовать ваш новый аккаунт, вы должны активировать его - это гарантирует, что адрес электронной почты, который вы использовали действителен и принадлежит вам. Чтобы активировать свою учетную запись, нажмите на ссылку ниже или скопируйте и вставьте все это в адресную строку вашего браузера:</p>

				<p><a href="'.$link.'" target="_blank">'.$link.'</a></p>
				
				<p>Ваш пароль: '.$reg['pass'].'</p>

				<p>Спасибо!</p>

				<p>С уважением, '.$_SERVER['HTTP_HOST'].'.</p>';
			}elseif($api->lang=='en'){
				$mess = '<p>Welcome to '.$_SERVER['HTTP_HOST'].'.</p>
				
				<p>Before you can use your new account you must activate it - this ensures the email address you used is valid and belongs to you. To activate your account, click on the link below or copy and paste all this into the address bar of your browser:</p>

				<p><a href="'.$link.'" target="_blank">'.$link.'</a></p>
				
				<p>Your password: '.$reg['pass'].'</p>

				<p>Thank you!</p>

				<p>Sincerely, '.$_SERVER['HTTP_HOST'].'.</p>';
			}else{
				$mess = '<p>'.$_SERVER['HTTP_HOST'].' сайтына қош келдіңіз.</p>
				
				<p>Өзініздің аккаунтыңызды қолданбас бұрын, сіз электрондық почтаныздың шын екенін көрсету керексіз. Ол үшін келесі сілтеуішті басыңыз:</p>

				<p><a href="'.$link.'" target="_blank">'.$link.'</a></p>
				
				<p>Сіздің пароліңіз: '.$reg['pass'].'</p>

				<p>Рақмет!</p>

				<p>Құрметпен, '.$_SERVER['HTTP_HOST'].'.</p>';
			}
			
			$api->mail->from = 'info@'.str_replace('www.','', $_SERVER['HTTP_HOST']);
			$api->mail->headers = 'X-Mailer: PHP/' . phpversion();
			$api->mail->subject = $trans[$api->lang]['Пожалуйста, подтвердите свой адрес электронной почты'];
			$api->mail->body = $mess;
			
			$api->mail->send($reg['mail']);
			
			echo '<p class="ok">'.$trans[$api->lang]['Вам отправлено письмо для подтверждения регистрации. Для подтверждения регистрации вашей учётной записи, пожалуйста, следуйте инструкциям в этом письме.'].'</p>';
			// echo '<p class="ok">'.$trans[$api->lang]['Вы успешно зарегистрированы! Теперь вы можете авторизоваться в системе.'].'</p>';//<script type="text/javascript"> $(function(){ $("#reg-block").hide(); }); </script>
			$api->lang = $lang;
			exit( $api->footer() );
		}else{ 
			$api->lang = $lang;
			echo '<p class="error">'.$trans[$api->lang]['Ошибка'].'</p>';
		}
	}
}
?>
<!--Registr-->
<div class="register" id="reg-form">
	<form method="post" onsubmit="return checkRegForm(this)" enctype="multipart/form-data">
		<!--<div>Ваше фото</div>
		<div class="input-row"><input name="logo" type="file" class="textinput" /><span class="error"></span></div>-->
		<div><?=$trans[$api->lang]['Ф.И.О.']?></div>
		<div class="input-row"><input name="reg[name]" type="text" value="<?=@$reg['name']?>" class="textinput" /><span class="error"></span></div>
		<div><?=$trans[$api->lang]['E-mail']?></div>
		<div class="input-row"><input name="reg[mail]" type="text" value="<?=@$reg['mail']?>" class="textinput" /><span class="error"></span></div>
		<div><?=$trans[$api->lang]['Пароль']?></div>
		<div class="input-row"><input name="reg[pass]" type="password" value="<?=@$reg['pass']?>" class="textinput" /><span class="error"></span></div>
		<div><?=$trans[$api->lang]['Пароль повторно']?></div>
		<div class="input-row"><input name="pass_check" type="password" value="<?=@$_REQUEST['pass_check']?>" class="textinput" /><span class="error"></span></div>
		<div><?=$trans[$api->lang]['День рождения']?></div>
		<div class="input-row"><input name="reg[birthday]" type="text" value="<?=@$reg['birthday']?>" class="textinput date-picker" /><span class="error"></span></div>
		<!--<div>Семейное положение</div>
		<div class="input-row">
			<input id="marital-1" name="reg[marital]" type="radio" value="0"<?=(@!$reg['marital']?' checked':'')?> /> <label for="marital-1">Холост / не замужем</label>
			<input id="marital-2" name="reg[marital]" type="radio" value="1"<?=(@$reg['marital']==1?' checked':'')?> /> <label for="marital-2">Женат / замужем</label>
		</div>-->
		<div><?=$trans[$api->lang]['Адрес']?></div>
		<div class="input-row"><textarea name="reg[city]"><?=@$reg['city']?></textarea><span class="error"></span></div>
		<!--<div>Адрес</div>
		<div class="input-row"><input name="reg[address]" type="text" value="<?=@$reg['address']?>" class="textinput" /><span class="error"></span></div>-->
		<div><?=$trans[$api->lang]['Телефон']?></div>
		<div class="input-row"><input name="reg[phone]" type="text" value="<?=@$reg['phone']?>" class="textinput" /><span class="error"></span></div>
		<div><?=$trans[$api->lang]['Код с картинки']?></div>
		<div class="input-row">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="top" style="padding-right: 10px;">
						<img height="60" width="120" class="captcha_img" src="<?=_FILES_?>/appends/kcaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" />
					</td>
					<td valign="top" align="right">
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" height="30"><input type="text" maxlength="6" name="captcha" class="textinput" style="width:100px" /><span class="error"></span></td>
							</tr>
							<tr>
								<td align="left"><a href="#" id="update_captcha" onclick="return newCaptcha( $('.captcha_img') )"><?=$trans[$api->lang]['update code']?></a></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div><input type="submit" value="<?=$trans[$api->lang]['Зарегистрироваться']?>" class="butintext" /></div>
	</form>
</div>
<script type="text/javascript">
$(function(){
	$('.date-picker').keypress(function(){ 
		return false; 
	}).datepicker("option", $.datepicker.regional[ "<?=$api->lang?>" ]);
});

function newCaptcha(img){
	var randDig=(Math.random()*10)+1|0;
	img.attr('src', '/cms/files/appends/kcaptcha/index.php?rand='+randDig);
	return false;
}

function checkRegForm(f){
	var msg = [];
	
	if(f['reg[name]'].value=='') msg.push({field:f['reg[name]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
        else if(!f['reg[name]'].value.match(/^[a-zA-Zа-яА-ЯЁё][a-zA-Zа-яА-ЯЁё\s-]*$/)) msg.push({field:f['reg[name]'], txt:'<?=$trans[$api->lang]['Имя некорректно']?>'});
        else noErrors(f['reg[name]']);
	
	if(f['reg[mail]'].value=='') msg.push({field:f['reg[mail]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
	else if(!f['reg[mail]'].value.match(/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/)) msg.push({field:f['reg[mail]'], txt:'<?=$trans[$api->lang]['E-mail адрес не корректен.']?>'});
	else noErrors(f['reg[mail]']);
	
	if(f['reg[pass]'].value=='') msg.push({field:f['reg[pass]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
	else{ 
		noErrors(f['reg[pass]']);
		if(f['pass_check'].value=='') msg.push({field:f['pass_check'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
		else if(f['reg[pass]'].value!=f['pass_check'].value) msg.push({field:f['pass_check'], txt:'<?=$trans[$api->lang]['Пароли не совпадают.']?>'});	
		else noErrors(f['pass_check']);
	}
	
	if(f['pass_check'].value=='') msg.push({field:f['pass_check'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['pass_check']);
	
	if(f['reg[city]'].value=='') msg.push({field:f['reg[city]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['reg[city]']);
	
	if(f['reg[birthday]'].value=='') msg.push({field:f['reg[birthday]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['reg[birthday]']);
	
	if(f['reg[phone]'].value=='') msg.push({field:f['reg[phone]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['reg[phone]']);
	
	if(f['captcha'].value=='') msg.push({field:f['captcha'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['captcha']);
	
	if(msg.length){
		msg[0].field.focus();
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