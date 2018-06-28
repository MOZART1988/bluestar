<?
#config
$base_id = 14;
$pass_field_id = 41;
$hash_field_id = 45;

include('cms/public/api.php');
$vars = array(
	"ru"=>array(
		"title"=>'Восстановление пароля',
		"mail"=>'Электронная почта',
		"recover"=>'восстановить',
		"sentOk"=>'На указанный e-mail выслано письмо с инструкциями.',
		"wrongMail"=>'Неверный адрес электронной почты.',
		"emptyMail"=>'Электронная почта не заполнена',
		'обязательное поле' => 'обязательное поле',
	),
	"en"=>array(
		"title"=>'Password recovery',
		"mail"=>'E-mail',
		"recover"=>'recover',
		"sentOk"=>'On this e-mail address was sent the letter with futher instructions.',
		"wrongMail"=>'Wrong e-mail address.',
		"emptyMail"=>'E-mail field is empty',
		'обязательное поле' => 'requiered field',
	),
	"kz"=>array(
		"title"=>'Парольді қайта қалпына келтіру',
		"mail"=>'E-mail',
		"recover"=>'қалпына келтіру',
		"sentOk"=>'Көрсетілген e-mail-ге парольді қайта қалпына келтіру туралы мағлуматы бар хат жіберілді',
		"wrongMail"=>'E-mail адресіңіз қате.',
		"emptyMail"=>'E-mail адресіңіз толтырылмаған',
		'обязательное поле' => 'міндетті түрде толтырылу керек ұяшық',
	)
);

$api->header(array('page-title'=>'<!--object:[127][18]-->'));

if(isset($_POST['mail']) && preg_match("/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/", $_POST['mail'])){
	if(!!$u = $api->db->select("objects", "WHERE `head`='".$base_id."' AND `name`='".$_POST['mail']."' LIMIT 1") ){
		$u = array_merge($u, $api->objects->getObjectFields($u['id'], $u['class_id']));
		$hash = md5(time().$u['id'].rand(999, 99999999));
		$api->db->update("class_".$u['class_id'], array("field_".$hash_field_id=>$hash), "WHERE `object_id`='".$u['id']."' LIMIT 1");
		
		$theme = 'Password recovery!';
		
		if($api->lang=='ru'){
			$html = array('<div><b>Здравствуйте, '.$u['фио'].'!</b></div><br>');
			$html[]='<div>На сайте <a href="http://'.$_SERVER['HTTP_HOST'].'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'</a> была подана заявка на восстановление пароля к аккаунту, который зарегистирован на этот e-mail.</div>';
			$html[]='<div>Если вы действительно подавали заявку на восстановление пароля, то перейдите по ссылке: <a href="http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/changePass/?h='.$hash.'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/changePass/?h='.$hash.'</a>.</div>';
			$html[]='<div>Иначе просто проигнорируйте это письмо.</div>';
			$html[]='<br><br>';
			$html[]='<div>---<br>С уважением, администрация.</div>';
		}else if($api->lang=='en'){
			$html = array('<div><b>Hello, '.$u['фио'].'!</b></div><br>');
			$html[]='<div>On website <a href="http://'.$_SERVER['HTTP_HOST'].'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'</a> someone ran password recovery for this e-mail address.</div>';
			$html[]='<div>If the person, which ran it was you click here <a href="http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/changePass/?h='.$hash.'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/changePass/?h='.$hash.'" target="_blank"</a>.</div>';
			$html[]='<div>Or just ignore this letter.</div>';
			$html[]='<br><br>';
			$html[]='<div>---<br>Regards, administration.</div>';

		}else{
			$html = array('<div><b>Здравствуйте, '.$u['фио'].'!</b></div><br>');
			$html[]='<div>На сайте <a href="http://'.$_SERVER['HTTP_HOST'].'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'</a> была подана заявка на восстановление пароля к аккаунту, который зарегистирован на этот e-mail.</div>';
			$html[]='<div>Если вы действительно подавали заявку на восстановление пароля, то перейдите по ссылке: <a href="http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/changePass/?h='.$hash.'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'/'.$api->lang.'/changePass/?h='.$hash.'</a>.</div>';
			$html[]='<div>Иначе просто проигнорируйте это письмо.</div>';
			$html[]='<br><br>';
			$html[]='<div>---<br>С уважением, администрация.</div>';
		}
		$body = join("", $html);
		
		$api->mail->from = 'info@'.str_replace('www.','', $_SERVER['HTTP_HOST']);
		$api->mail->headers = 'X-Mailer: PHP/' . phpversion();
		$api->mail->subject = $theme;
		$api->mail->body = $body;
		
		$api->mail->send($u['name']);
		
		echo '<font color="green">'.$vars[$api->lang]['sentOk'].'</font>';
		$api->footer();
		exit();
	}else echo '<font color="red">'.$vars[$api->lang]['wrongMail'].'</font>';
}
?>
<form method="post" onsubmit="return checkForm(this)" class="formsubmit">
	<table style="margin-bottom: 10px;">
		<tr>
			<td valign="bottom">
				<div class="row">
					<div class="title"><?=$vars[$api->lang]['mail']?></div>
					<div><input type="text" name="mail" style="width:180px; margin: 0;"></div>
				</div>
			</td>
			<td valign="bottom">
				<div class="row">
					<input type="submit" class="butintext" value="<?=$vars[$api->lang]['recover']?>">
				</div>
			</td>
		</tr>
	</table>
	<span class="error" style="padding: 0;"></span>
</form>
<script type="text/javascript">
function checkForm(f){
	var msg = [];
	
	if(f['mail'].value=='') msg.push({field:f['mail'], txt:'<?=$vars[$api->lang]['обязательное поле']?>'});	
	else if(!f['mail'].value.match(/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/)) msg.push({field:f['mail'], txt:'<?=$vars[$api->lang]['обязательное поле']?>'});
	else noErrors(f['mail']);
	
	if(msg.length){
		msg[0].field.focus();
		return gotErrors(msg);
	}
	return true;
}

function gotErrors(msg){
	for(var i in msg){
		var m = msg[i];
		$(m.field).addClass('error');
		$('.error').text(m.txt);
	}
	return false;
}

function noErrors(field){
	$(field).removeClass('error');
	$('.error').empty();
}
</script>
<?
$api->footer();
?>