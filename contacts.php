<?
$to = 'info@go-web.kz';

$trans = array(
	'ru' => array(
		'обязательное поле' => 'обязательное поле',
		'E-mail адрес не корректен.' => 'E-mail адрес не корректен.',
	),
	'en' => array(
		'обязательное поле' => 'requiered field',
		'E-mail адрес не корректен.' => 'E-mail address is not correct.',
	),
	'kz' => array(
		'обязательное поле' => 'міндетті түрде толтырылу керек ұяшық',
		'E-mail адрес не корректен.' => 'E-mail адресіңіз қате.',
	)
);

include('cms/public/api.php');

if(!$obj = $api->objects->getFullObject(8)){ 
	header('location: /404.php');
	exit();
}

$vars = array(
	"ru"=>array(
		"title"=>'Контакты',
		"yourMail"=>'Ваш e-mail',
		"yourName"=>'Ваше имя',
		"theme"=>'Тема сообщения',
		"yourQ"=>'Ваш вопрос',
		"captcha"=>'Код с картинки',
		"errorCaptcha"=>'<div class="error_mess" style="color:red; margin-bottom: 10px;">Код с картинки введён неверно.</div>',
		"send"=>'Отправить',
		"sendMessage"=>'Отправить сообщение',
		"sendOk"=>'<div class="error_mess" style="color:green; margin-bottom: 10px;">Успешно отправлено!</div>',
		"needToFill"=>'Необходимо заполнить поля',
		"обновить код"=>'обновить код',
	),
	"en"=>array(
		"title"=>'Contacts',
		"yourMail"=>'Your e-mail',
		"yourName"=>'Your name',
		"theme"=>'Theme',
		"yourQ"=>'Your question',
		"captcha"=>'Code from picture',
		"errorCaptcha"=>'<div class="error_mess" style="color:red; margin-bottom: 10px;">Wrong code from picture.</div>',
		"send"=>'Send message',
		"sendMessage"=>'Send message',
		"sendOk"=>'<div class="error_mess" style="color:green; margin-bottom: 10px;">Sent successfull!</div>',
		"needToFill"=>'Need to fill fields',
		"обновить код"=>'update code',
	),
	"tr"=>array(
		"title"=>'Contacts',
		"yourMail"=>'Your e-mail',
		"yourName"=>'Your name',
		"theme"=>'Theme',
		"yourQ"=>'Your question',
		"captcha"=>'Code from picture',
		"errorCaptcha"=>'<div class="error_mess" style="color:red; margin-bottom: 10px;">Wrong code from picture.</div>',
		"send"=>'Send message',
		"sendMessage"=>'Send message',
		"sendOk"=>'<div class="error_mess" style="color:green; margin-bottom: 10px;">Sent successfull!</div>',
		"needToFill"=>'Need to fill fields',
		"обновить код"=>'update code',
	),
	"kz"=>array(
		"title"=>'Контакітілер',
		"yourMail"=>'e-mail',
		"yourName"=>'Есіміңіз',
		"theme"=>'Такырыбы',
		"yourQ"=>'Сұрақ',
		"captcha"=>'Суреттегі код',
		"errorCaptcha"=>'<div class="error_mess" style="color:red; margin-bottom: 10px;">Суреттегі код қате енгізілген</div>',
		"send"=>'Хат жіберу',
		"sendMessage"=>'Хат жіберу',
		"sendOk"=>'<div class="error_mess" style="color:green; margin-bottom: 10px;">Хат сәтті жиберілді!</div>',
		"needToFill"=>'Міндетті түрде толтырылу керек ұяшықтар',
		"обновить код"=>'кодты жаңарту',
	)
);

$api->header(array('page-title'=>$vars[$api->lang]['title']));
?>
<style type="text/css">
.contacts_form input[type="text"], .contacts_form input[type="password"], .contacts_form select{
	padding: 2px 5px;
	margin-bottom: 10px;
	width: 230px;
	height: 20px;
	color: #777;
	vertical-align: top;
	font: 13px Arial;
}
.contacts_form textarea{
	font: 13px Arial;
	color: #777;
	padding: 2px 5px;
	margin-bottom: 5px;
}
.contacts_form select{
	height: 30px;
	width: 243px;
	vertical-align: top;
}
.contacts_form span.error{
	padding-left: 7px;
	vertical-align: top;
}
.contacts_form input[type="submit"]{
	margin-top: 15px;
	vertical-align: top;
}
#captcha_img{
	width: 119px;
}
</style>
<?
?>
 <script language='javascript'>
	  function f(obj) {
		try {
		var sizetext = obj.getAttribute("MaxLength");
		var text = obj.value; 
		if(text.length > sizetext) {
			window.alert(">1200");
		} 
		} catch(err) {
		  window.alert(err.Message);
		}

	  }
	</script>
<?
echo '<div id="page-text">'.$obj['Значение'].'</div>';
?>
<!--smart:{
	id:<?=$obj['id']?>,
	title:'&laquo;Контактов&raquo;',
	actions:['edit'],
}-->
<?
$fields = array(
	array('name'=>$vars[$api->lang]['yourName'], 'type'=>'text'),
	array('name'=>$vars[$api->lang]['yourMail'], 'type'=>'text'),
	array('name'=>$vars[$api->lang]['theme'], 'type'=>'text'),
	array('name'=>$vars[$api->lang]['yourQ'], 'type'=>'textarea', 'p1'=>'232', 'p2'=>'125')
);
$error = '';
if( isset($_POST['fields']) && is_array($fields_list = $_POST['fields']) ){
	if($_SESSION['captcha_keystring']!=@$_POST['captcha']){
		$fields = array(
			array('name'=>$vars[$api->lang]['yourName'], 'type'=>'text', 'value'=>$_POST['fields'][0]),
			array('name'=>$vars[$api->lang]['yourMail'], 'type'=>'text', 'value'=>$_POST['fields'][1]),
			array('name'=>$vars[$api->lang]['theme'], 'type'=>'text', 'value'=>$_POST['fields'][2]),
			array('name'=>$vars[$api->lang]['yourQ'], 'type'=>'textarea', 'p1'=>'232', 'p2'=>'125', 'value'=>$_POST['fields'][3])
		);
		$error = $vars[$api->lang]['errorCaptcha'];
	}else{
		# Подключаем почтовый класс
		include_once(_FILES_ABS_.'/mail.php');
		$smail = new mime_mail();
		if(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!='')){
			$smail->to=trim($obj['Значение']);
		}else{
			$smail->to='as@go-web.kz';
		}
		$smail->from = 'admin@'.$_SERVER['HTTP_HOST'];
		$smail->subject = 'Сообщение с формы контактов на сайте '.$_SERVER['HTTP_HOST'].'';
		$html = array();
		foreach($fields as $k=>$f){
			if(empty($fields_list[$k])) continue;
			$html[]='<div><b>'.$f['name'].'</b></div>';
			$html[]='<div>'.$fields_list[$k].'</div>';
			$html[]='<br>';
		}
		$smail->body = join("", $html);
		
		$smail->send($smail->to);
		/*$headers  = "Content-type: text/html; charset=utf-8\n"; 
		$headers .= "From: Admin Site <admin@".$_SERVER['HTTP_HOST'].">\n";
		mail($to, "Message from site!", $body, $headers);*/
		$error = $vars[$api->lang]['sendOk'];
	}
}

$html = array('<br><div><b>'.$vars[$api->lang]['sendMessage'].'</b></div><br /><div>'.$error.'<form class="contacts_form" method="post" onsubmit="return checkForm(this)">');
foreach($fields as $k=>$f){
	// if(!$k && !empty($_SESSION['auth']['u']['mail'])) $f['value']=$_SESSION['auth']['u']['mail'];
	$html[]='<div>'.$f['name'].'</div>';
	$html[]='<div>'.$api->objects->getFieldInput($k, $f).'<span class="error"></span></div>';
	// $html[]='<br>';
}
$html[]='<table border="0" cellspacing="0" cellpadding="0">
			<tr><td colspan="2">'.$vars[$api->lang]['captcha'].'</td></tr>
			<tr>
				<td valign="top" style="padding-right: 11px;">
					<img src="'._FILES_.'/appends/kcaptcha/?'.session_name().'='.session_id().'" id="captcha_img" />
				</td>
				<td valign="top" align="right">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" height="30"><input type="text" maxlength="6" name="captcha" style="width:100px" /><span class="error"></span></td>
						</tr>
						<tr>
							<td align="left"><a href="#" onclick="return newCaptcha( $(\'#captcha_img\') )">'.$vars[$api->lang]['обновить код'].'</a></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div><input type="submit" style="margin-top: 10px;" value="'.$vars[$api->lang]['send'].'" /></div>';
$html[]='</form></div>';
echo join( $html );
?>
<script type="text/javascript">
function checkForm(f){
	$('.error_mess').remove();

	var msg = [];
	
	if(f['fields[0]'].value=='') msg.push({field:f['fields[0]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['fields[0]']);
	
	if(f['fields[1]'].value=='') msg.push({field:f['fields[1]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});	
	else if(!f['fields[1]'].value.match(/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/)) msg.push({field:f['fields[1]'], txt:'<?=$trans[$api->lang]['E-mail адрес не корректен.']?>'});
	else noErrors(f['fields[1]']);
	
	if(f['fields[2]'].value=='') msg.push({field:f['fields[2]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['fields[2]']);
	
	if(f['fields[3]'].value=='') msg.push({field:f['fields[3]'], txt:'<?=$trans[$api->lang]['обязательное поле']?>'});
	else noErrors(f['fields[3]']);
	
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
		$(m.field).addClass('error').find('+.error').hide().fadeIn().text(m.txt);
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
