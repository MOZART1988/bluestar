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
		"title"=>'Забронировать номер',
		"yourMail"=>'Ваш e-mail',
		"yourName"=>'Ваше имя',
		"yourQ"=>'Комментарии',
		"theme"=>'Адрес',
		"captcha"=>'Код с картинки',
		"errorCaptcha"=>'<div style="color:red;">Код с картинки введён неверно.</div><br>',
		"send"=>'Отправить',
		"sendMessage"=>'Отправить сообщение',
		"sendOk"=>'<div style="color:green;">Успешно отправлено!</div><br>',
		"needToFill"=>'Необходимо заполнить поля',
		"обновить код"=>'обновить код',
	),
	"en"=>array(
		"title"=>'Order on-line',
		"yourMail"=>'Your e-mail',
		"yourName"=>'Your name',
		"yourQ"=>'Comments',
		"theme"=>'Address',
		"captcha"=>'Code from picture',
		"errorCaptcha"=>'<div style="color:red;">Wrong code from picture.</div><br>',
		"send"=>'Send message',
		"sendMessage"=>'Send message',
		"sendOk"=>'<div style="color:green;">Sent successfull!</div><br>',
		"needToFill"=>'Need to fill fields',
		"обновить код"=>'update code',
	),
	"tr"=>array(
		"title"=>'Order on-line',
		"yourMail"=>'Your e-mail',
		"yourName"=>'Your name',
		"yourQ"=>'Comments',
		"theme"=>'Address',
		"captcha"=>'Code from picture',
		"errorCaptcha"=>'<div style="color:red;">Wrong code from picture.</div><br>',
		"send"=>'Send message',
		"sendMessage"=>'Send message',
		"sendOk"=>'<div style="color:green;">Sent successfull!</div><br>',
		"needToFill"=>'Need to fill fields',
		"обновить код"=>'update code',
	),
	"kz"=>array(
		"title"=>'On-line шағым жазу',
		"yourMail"=>'e-mail',
		"yourName"=>'Есіміңіз',
		"yourQ"=>'Комментарии',
		"theme"=>'Мекен жай',
		"captcha"=>'Суреттегі код',
		"errorCaptcha"=>'<div style="color:red;">Суреттегі код қате енгізілген</div><br>',
		"send"=>'Хат жіберу',
		"sendMessage"=>'Хат жіберу',
		"sendOk"=>'<div style="color:green;">Хат сәтті жиберілді!</div><br>',
		"needToFill"=>'Міндетті түрде толтырылу керек ұяшықтар',
		"обновить код"=>'кодты жаңарту',
	)
);

$api->header(array('page-title'=>'<!--o:176-->'));
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
$error = '';
$fields = array(
	array('name'=>$vars[$api->lang]['yourName'], 'type'=>'text'),
	array('name'=>$vars[$api->lang]['yourMail'], 'type'=>'text'),
	array('name'=>$vars[$api->lang]['theme'], 'type'=>'text'),
	array('name'=>$vars[$api->lang]['yourQ'], 'type'=>'textarea', 'p1'=>'232', 'p2'=>'125')
);
if( isset($_POST['fields']) && is_array($fields_list = $_POST['fields']) ){
	if($_SESSION['captcha_keystring']!=@$_POST['captcha']){
		$error = $vars[$api->lang]['errorCaptcha'].'<br>';
	}else{
		# Подключаем почтовый класс
		include_once(_FILES_ABS_.'/mail.php');
		$smail = new mime_mail();
		if(($obj=$api->objects->getFullObject(7689)) && (trim($obj['Значение'])!='')){
			$smail->to=trim($obj['Значение']);
		}else{
			$smail->to='as@go-web.kz';
		}
		$smail->from = 'admin@'.$_SERVER['HTTP_HOST'];
		$smail->subject = 'Сообщение с формы бронирования на сайте '.$_SERVER['HTTP_HOST'].'';
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
		$error =  $vars[$api->lang]['sendOk'].'<br>';
	}
}

$html = array('<form class="contacts_form" method="post" onsubmit="return checkForm(this)">');
$html[] = $error;
foreach($fields as $k=>$f){
	// if(!$k && !empty($_SESSION['auth']['u']['mail'])) $f['value']=$_SESSION['auth']['u']['mail'];
	$html[]='<div>'.$f['name'].'</div>';
	$html[]='<div>'.$api->objects->getFieldInput($k, $f).'<span class="error"></span></div>';
	// $html[]='<br>';
}
$html[]='
 <!--smart:{id: 7689,actions:["edit"],title: "E-mail для отправки"}-->
	
<table border="0" cellspacing="0" cellpadding="0">
			<tr><td colspan="2">'.$vars[$api->lang]['captcha'].'</td></tr>
			<tr>
				<td valign="top" style="padding-right: 11px;">
					<img src="'._FILES_.'/appends/kcaptcha/?'.session_name().'='.session_id().'" id="captcha_img" />
				</td>
				<td valign="top" align="right">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" height="30"><input type="text" maxlength="6" name="captcha" style="width:94px" /><span class="error"></span></td>
						</tr>
						<tr>
							<td align="left"><a href="#" onclick="return newCaptcha( $(\'#captcha_img\') )">'.$vars[$api->lang]['обновить код'].'</a></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div><input type="submit" style="margin-top: 7px;" value="'.$vars[$api->lang]['send'].'" /></div>';
$html[]='</form>';
echo join( $html );
?>
<script type="text/javascript">
function newCaptcha(img){
	img.attr('src', img.attr('src')+'/');
	return false;
}
function checkForm(f){
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
