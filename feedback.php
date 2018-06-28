<?
include('cms/public/api.php');

function getCaption($id){
	global $api;
	if ($o = $api->objects->getFullObject($id)){
		return $o['Значение'];
	}
	return ;
}

$trans = array(
	'ru' => array(
		'message has been sent' => 'сообщение успешно отправлено',
		'Incorrect code from the image' => 'Неверный код с картинки',
		'Send a message' => 'Отправка сообщения',
		'Your name' => 'Ваше имя',
		'Your message' => 'Ваше сообщение',
		'update code' => 'обновить код',
		'Code from the image' => 'Код с картинки',
		'Send' => 'Отправить',
		'Enter' => 'Не заполнено поле ',
		'Theme' => 'Тема сообщения',
		'Sending Message ...' => 'Отправка сообщения...',
		'Unable to connect to the server ...' => 'Невозможно подключиться к серверу...',
		'Please enter a valid E-Mail' => 'Укажите правильный E-Mail',
		'Please enter a valid Name' => 'Укажите правильное Имя',
	),
	'en' => array(
		'message has been sent' => 'message has been sent',
		'Incorrect code from the image' => 'Incorrect code from the image',
		'Send a message' => 'Send a message',
		'Your name' => 'Your name',
		'Your message' => 'Your message',
		'update code' => 'update code',
		'Code from the image' => 'Code from the image',
		'Send' => 'Send',
		'Enter' => 'Please enter ',
		"Theme"=>'Theme',
		'Sending Message ...' => 'Sending Message ...',
		'Unable to connect to the server ...' => 'Unable to connect to the server ...',
		'Please enter a valid E-Mail' => 'Please enter a valid E-Mail',
		'Please enter a valid Name' => 'Please enter a valid Name',
	),
	'kz' => array(
		'message has been sent' => 'хат ойдағыдай жіберілді',
		'Incorrect code from the image' => 'Суреттен енгізілген мәтін дұрыс емес',
		'Send a message' => 'Хат жазу',
		'Your name' => 'Сіздің атыңыз',
		'Your message' => 'Мәтін',
		'update code' => 'кодты жаңарту',
		'Code from the image' => 'Суреттегі кодты қате тердіңіз',
		'Send' => 'Жіберу',
		'Enter' => 'Көрсетілмеген ұяшық ',
		"Theme"=>'Такырыбы',
		'Sending Message ...' => 'Хат жіберілуде...',
		'Unable to connect to the server ...' => 'Серверге қосылу мүмкін емес...',
		'Please enter a valid E-Mail' => 'Дұрыс E-Mail көрсетіңіз',
		'Please enter a valid Name' => 'Дұрыс есiм көрсетіңіз',
	)
);
# ---------------------------------------------------------------------------				
# Если послан AJAX запрос
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && isset($_POST['x']) && ($_POST['x']=='secure'))
{
	# Если отправка письма
	if (
		!empty($_POST['email']) && 
		!empty($_POST['name']) && 
		!empty($_POST['text']) &&
		!empty($_POST['capcha'])
	   )
	{
		# CAPCHA
		
		if (!empty($_SESSION['captcha_keystring']) && ($_POST['capcha'] == $_SESSION['captcha_keystring']))
		{
		
			$email = $api->db->prepare($_POST['email']);
			$name  = $api->db->prepare($_POST['name']);
			$theme  = $api->db->prepare($_POST['theme']);
			$text  = nl2br($api->db->prepare($_POST['text']));
			
	
			# Подключаем почтовый класс
			include_once(_FILES_ABS_.'/mail.php');
			$smail = new mime_mail();

			if(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!='')){
				$smail->to=trim($obj['Значение']);
			}else{
				$smail->to='as@go-web.kz';
			}
			$smail->from 		= 'admin@'.$_SERVER['HTTP_HOST'];
			$smail->subject		= 'Сообщение с сайта '.$_SERVER['HTTP_HOST'];
			$smail->body		= '<html>
									<body>
									Отправлено: '.date('d.m.Y').' в '.date('h:i').' с IP '.$_SERVER['REMOTE_ADDR'].'<br/>
									<br/>
									<b>Имя:</b><br/>
									'.$name.'<br/>
									<br/>
									<b>E-Mail:</b><br/>
									<a href="mailto:'.$email.'">'.$email.'</a><br/>
									<br/>
									<b>Тема:</b><br/>
									'.$theme.'<br/>
									<br/>
									<b>Сообщение:</b><br/>
									'.$text.'<br/>
									<br/>
									</body>
									</html>';

			# отправляем
			$smail->send($smail->to);
			
			echo '
			<script type="text/javascript">
				$("#feed_back_protocol").css({"height": "20px", "margin-top": "-10px", "margin-bottom": "10px"}).hide().html("<div style=\"color:green;\">'.$trans[$api->lang]['message has been sent'].'</div>").show();
				$("#fancybox-inner").css("height", "480px");
				$("#fancybox-outer").css("height", "475px");
				$("#update_captcha").click();
				$("#s_email").val($("#s_email").attr("title"));
				$("#s_name").val($("#s_name").attr("title"));
				$("#s_text").val($("#s_text").attr("title"));
                                $("#s_theme").val($("#s_theme").attr("title"));
				$("#s_capcha").val($("#s_capcha").attr("title"));
				$("#captcha_img").attr("src", "'._FILES_.'/appends/kcaptcha/index.php?rand='.rand(3,5).'");
			</script>';
		
		} else {
			echo '
			<script type="text/javascript">
				$("#s_capcha").focus();
				alert("'.$trans[$api->lang]['Incorrect code from the image'].'");
			</script>';
		}
	}
	
	exit;
}
?>
<?header ('Content-type: text/html; charset=utf-8');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Отправка сообщения</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="title" content="" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<!--<script type="text/javascript" src="/js/jquery.js"></script>-->
	<script type="text/javascript" src="/js/ui/ui.js"></script>
	<link href="/js/ui/ui.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		.text-input {
			font-family: Arial, Verdana;
			font-size:14px;
		}
		.feedback_wrapper p{
			margin-bottom: 0px;
			padding-bottom: 0px;
		}
	</style>
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
</head>
<body>
<div style="padding:0 20px; overflow-x: hidden;	overflow-y: auto;" class="feedback_wrapper">
	<h1 style="font-size: 26px; margin-bottom: 10px;"><?=(isset($_GET['type']) && $_GET['type'] == 'order'?getCaption(356):$trans[$api->lang]['Send a message'])?></h1>
	<div id="feed_back_protocol"></div>
	<p><input class="forminput text-input" id="s_name" style="width:300px;" type="text" maxlength="250" title="<?=$trans[$api->lang]['Your name']?>" value="<?=$trans[$api->lang]['Your name']?>"/></p>
	<p><input class="forminput text-input" id="s_email" style="width:300px;" type="text" maxlength="250" title="E-Mail" value="E-Mail"/></p>
	<p><input class="forminput text-input" id="s_theme" style="width:300px;" type="text" maxlength="250" title="<?=$trans[$api->lang]['Theme']?>" value="<?=$trans[$api->lang]['Theme']?>"/></p>
	<p><textarea onkeypress='f(this);' MaxLength='1200' title="<?=$trans[$api->lang]['Your message']?>" class="forminput text-input" id="s_text" style="padding: 7px; width:298px;  height:150px; margin-bottom: 10px;"><?=$trans[$api->lang]['Your message']?></textarea></p>
	<table width="314" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top" height="60">
				<img height="60" width="120" class="captcha_img" src="<?=_FILES_?>/appends/kcaptcha/?<?php echo session_name()?>=<?php echo session_id()?>" />
			</td>
			<td valign="top" align="right">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" height="30" align="right"><input maxlength="6" title="<?=$trans[$api->lang]['Code from the image']?>" class="forminput text-input" id="s_capcha" style="width:126px" type="text" value="<?=$trans[$api->lang]['Code from the image']?>" /></td>
					</tr>
					<tr>
						<td align="right"><a href="#" id="update_captcha" onclick="return newCaptcha( $('.captcha_img') )"><?=$trans[$api->lang]['update code']?></a></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input id="s_send_btn" style="width:93px; margin-top: 13px;" type="button" class="btn" value="<?=$trans[$api->lang]['Send']?>" /></td>
		</tr>
	</table>
	<div id="will_clean" style="height:15px;"></div>
</div>
<script type="text/javascript">
// Проверка E-MAIL
function EmailCheck(value)
{
  var re = /^\w+([\.-]?\w+)*@(((([a-z0-9]{2,})|([a-z0-9][-][a-z0-9]+))[\.][a-z0-9])|([a-z0-9]+[-]?))+[a-z0-9]+\.([a-z]{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/i; 

  if(re.test(value))
  {
    return true; 
  } else { 
    return false; 
  } 
}
function NameCheck(value)
{
  var re = /^[а-яА-ЯёЁa-zA-Z0-9 ]+$/i;

  if(re.test(value))
  {
    return true; 
  } else { 
    return false; 
  } 
}

function newCaptcha(img){
	var randDig=(Math.random()*10)+1|0;
	img.attr('src', '/cms/files/appends/kcaptcha/index.php?<?php echo session_name()?>='+randDig);
	return false;
}
 $(document).ready(function()
 {
	newCaptcha($('.captcha_img'));
	// Фокусы :)
	$("#s_name").focusin(function()    { if($(this).val() == '<?=$trans[$api->lang]['Your name']?>') { $(this).val(''); } });
 	$("#s_name").focusout(function()   { if($(this).val() == '') { $(this).val('<?=$trans[$api->lang]['Your name']?>'); } });
	$("#s_email").focusin(function()   { if($(this).val() == 'E-Mail') { $(this).val(''); } });
 	$("#s_email").focusout(function()  { if($(this).val() == '') { $(this).val('E-Mail'); } });
	$("#s_theme").focusin(function()    { if($(this).val() == '<?=$trans[$api->lang]['Theme']?>') { $(this).val(''); } });
 	$("#s_theme").focusout(function()   { if($(this).val() == '') { $(this).val('<?=$trans[$api->lang]['Theme']?>'); } });
	$("#s_text").focusin(function()    { if($(this).val() == '<?=$trans[$api->lang]['Your message']?>') { $(this).val(''); } });
 	$("#s_text").focusout(function()   { if($(this).val() == '') { $(this).val('<?=$trans[$api->lang]['Your message']?>'); } });
	$("#s_capcha").focusin(function()  { if($(this).val() == '<?=$trans[$api->lang]['Code from the image']?>') { $(this).val(''); } });
 	$("#s_capcha").focusout(function() { if($(this).val() == '') { $(this).val('<?=$trans[$api->lang]['Code from the image']?>'); } });
	
	// Отправка данных
	$("#s_send_btn").click(function()
	{
		$("#feed_back_protocol").html("");
	// alert('test');
	// return false;
		var err_msg = '';
		var focused = 0;
		
		// Проверяем данные
		
		
		// if (($("#s_name").val() == '')   || ($("#s_name").val() == '<?=$trans[$api->lang]['Your name']?>'))				{ err_msg = err_msg+"<?=$trans[$api->lang]['Enter']?><?=$trans[$api->lang]['Your name']?>\n"; 		if (focused == 0)	{ $("#s_name").focus();  focused=1; } }
		
		if (($("#s_name").val() == '')  || ($("#s_name").val() == '<?=$trans[$api->lang]['Your name']?>') || (NameCheck($("#s_name").val()) == false))				{ err_msg = err_msg+"<?=$trans[$api->lang]['Please enter a valid Name']?>\n"; $("#s_name").focus(); focused=1; }
		
		if (($("#s_email").val() == '')  || ($("#s_email").val() == 'E-Mail') || (EmailCheck($("#s_email").val()) == false)){ err_msg = err_msg+"<?=$trans[$api->lang]['Please enter a valid E-Mail']?>\n"; $("#s_email").focus(); focused=1; }
		
		if (($("#s_theme").val() == '')  || ($("#s_theme").val() == '<?=$trans[$api->lang]['Theme']?>')){ err_msg = err_msg+"<?=$trans[$api->lang]['Enter']?><?=$trans[$api->lang]['Theme']?>\n"; $("#s_theme").focus(); focused=1; }
		
		if (($("#s_text").val() == '')   || ($("#s_text").val() == '<?=$trans[$api->lang]['Your message']?>'))		{ err_msg = err_msg+"<?=$trans[$api->lang]['Enter']?><?=$trans[$api->lang]['Your message']?>\n"; 		if (focused == 0) 	{ $("#s_text").focus();  focused=1; } }
		if (($("#s_capcha").val() == '') || ($("#s_capcha").val() == '<?=$trans[$api->lang]['Code from the image']?>'))		{ err_msg = err_msg+"<?=$trans[$api->lang]['Enter']?><?=$trans[$api->lang]['Code from the image']?>\n"; if (focused == 0) 	{ $("#s_capcha").focus(); } }
	
		// Ошибок нет - отправляем
		if (err_msg == '')
		{
			$.ajax({
				url: "<?=$_SERVER['PHP_SELF']?>",
				data: "email="+$("#s_email").val()+"&name="+$("#s_name").val()+"&theme="+$("#s_theme").val()+"&text="+$("#s_text").val()+"&capcha="+$("#s_capcha").val()+"&x=secure",
				type: "POST",
				dataType : "html",
				cache: false,

				beforeSend: function() 		{ $("#feed_back_protocol").html("<?=$trans[$api->lang]['Sending Message ...']?>"); },
				success:  function(data) 	{ $("#feed_back_protocol").html(data); },
				error: function() 			{ $("#feed_back_protocol").html("<div style=\"color:red;\"><?=$trans[$api->lang]['Unable to connect to the server ...']?></div>"); }
    		});

		} else {
		  alert(err_msg);
		}
	
	});
 });
</script>
</body>
</html>