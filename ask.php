<?
include('cms/public/api.php');
include_once(_FILES_ABS_."/mail.php");

# ЛОКАЛИ
$vars = array(
	"ru"=>array(
		"title"=>'Задать вопрос',
		"topText"=>'<div>Вы можете <a href="#" onclick="return askForm()">задать свой вопрос</a> или ознакомиться с вопросами посетителей сайта.</div>',
		"addOk"=>'<font color="green">Спасибо за Ваш вопрос. Он успешно добавлен и в данный момент рассматривается администрацией сайта. После того, как администрация даст на него ответ - он появится в общем списке.</font>',
		"errorCaptcha"=>'<font color="red">Код с картинки введён неверно.</font>',
		"errorAdding"=>'<font color="red">Ошибка добавления вопроса.</font>',
		"errorText"=>'<font color="red">Ошибка, не заполнен текст вопроса.</font>',
		"showAll"=>'раскрыть всё',
		"hideAll"=>'скрыть всё',
		"noQ"=>'Вопросов нет.',
		"yourName"=>'Ваше имя',
		"yourQ"=>'Ваш вопрос',
		"captcha"=>'Код с картинки',
		"send"=>'Отправить',
		"обновить код"=>'обновить код',
		"Не заполнен текст вопроса"=>'Не заполнен текст вопроса',
		"Не заполнен поле ФИО"=>'Не заполнен поле ФИО',
		"Не корректный адрес электронной почты"=>'Не корректный адрес электронной почты',
		"Не заполнен код с картинки"=>'Не заполнен код с картинки',
		"Следующие ошибки в заполнении"=>'Следующие ошибки в заполнении',
		"требуется ответ."=>'требуется ответ.',
	),
	"en"=>array(
		"title"=>'Ask question',
		"topText"=>'<div>Here you can <a href="#" onclick="return askForm()">ask your questions</a> or read questions of other users.</div>',
		"addOk"=>'<font color="green">Thank you for your question! After approving it by the administration, it will appeare on the site.</font>',
		"errorCaptcha"=>'<font color="red">Wrong picture data.</font>',
		"errorAdding"=>'<font color="red">Adding error. Try later.</font>',
		"errorText"=>'<font color="red">Text field is empty.</font>',
		"showAll"=>'show all',
		"hideAll"=>'hide all',
		"noQ"=>'There is no any questions yet.',
		"yourName"=>'Your name',
		"yourQ"=>'Your question',
		"captcha"=>'Code from picture',
		"send"=>'Send question',
		"обновить код"=>'update cod',
		"Не заполнен текст вопроса"=>'Not full text of the question',
		"Не заполнен поле ФИО"=>'Not full text of the name',
		"Не корректный адрес электронной почты"=>'Not a valid email address',
		"Не заполнен код с картинки"=>'Not full code from the image',
		"Следующие ошибки в заполнении"=>'The following errors in filling',
		"требуется ответ."=>'response is needed.',
	),
	"kz"=>array(
		"title"=>'Сұрақ-жауаптар',
		"topText"=>'<div>Сіз өзіңіздің <a href="#" onclick="return askForm()">сұрағыңызды</a> қоя аласыз немесе басқа адамдардың жауаптарын көре аласыз.</div>',
		"addOk"=>'<font color="green">Сұрағыңыз үшін рақмет! Сіздің сұрағыңыз жақын арада, сайттын администраторымен қарастырылып болған соң сайтқа шығады.</font>',
		"errorCaptcha"=>'<font color="red">Суреттегі код дурыс емес</font>',
		"errorAdding"=>'<font color="red">Сұрақты енгізген кезде қате шықты</font>',
		"errorText"=>'<font color="red">Қате, сұрақтың мәтіні енгізілмеген</font>',
		"showAll"=>'барлығын ашу',
		"hideAll"=>'барлығын жабу',
		"noQ"=>'Сұрақтар жоқ.',
		"yourName"=>'Сіздің атыңыз',
		"yourQ"=>'Сіздің сұрағыңыз',
		"captcha"=>'Суреттегі код',
		"send"=>'Жіберу',
		"обновить код"=>'кодты жаңарту',
		"Не заполнен текст вопроса"=>'Сұрақтын мәтіні толтырылмаған',
		"Не заполнен поле ФИО"=>'Аты-жөніңізді толтырыңыз',
		"Не корректный адрес электронной почты"=>'Электрондық почта дұрыс терілмеген',
		"Не заполнен код с картинки"=>'Суреттегі код терілмеген',
		"Следующие ошибки в заполнении"=>'Толтыру кезінде келесі қателер табылды',
		"требуется ответ."=>'жауаб қажет етіледі.',
	)
);

$api->header(array('page-title'=>'<!--object:[125][18]-->'));

$mime_mail = new mime_mail();

$object_id = 155;
$class_id = 26;
$onepage = 30;

$name_id=88; // ФИО
$quest_id=90; // Вопрос
$answ_id=91; // Ответ
?>
<?

if(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!='')){
	$mails=trim($obj['Значение']);
}else{
	$mails='as@go-web.kz';
}
// $mails = 'ganiksu01k@mail.ru';

# ДОБАВИТЬ
$error = '';
if(isset($_REQUEST['fields']) && is_array($fields = $_REQUEST['fields']))
{
	if(@empty($_SESSION['captcha_keystring']) || $_REQUEST['captcha']!=$_SESSION['captcha_keystring']){
		$error = '<div class="error_msg">'.$vars[$api->lang]['errorCaptcha'].'</div>';
	} else {
		$object = array(
			'head'=>$object_id,
			'name'=>'Вопрос',
			'class_id'=>$class_id
		);
		if($api->objects->createObjectAndFields($object, $fields))
		{
			$html = array();
			foreach($fields as $k=>$value){
				$html[]='<div><b>'.$k.'</b></div>';
				$html[]='<div>'.$value.'</div>';
				$html[]='<br>';
			}
			unset($fields);
			$mime_mail->from 	= 'info@'.preg_replace('/www\./', '', $_SERVER['HTTP_HOST']);
			$mime_mail->subject	= 'Уведомление о новом вопросе';
			$mime_mail->body	= join("", $html);
			
			# РАССЫЛАЕМ
			$mime_mail->send($mails);
			
			# OK
			$error = '<div class="error_msg">'.$vars[$api->lang]['addOk'].'</div>';
			unset($fields);
			
		} else { $error = '<div class="error_msg">'.$vars[$api->lang]['errorAdding'].'</div>'; }
	}
}
?>
<style type="text/css">
ul.advices-list{
	list-style-type:square;
	color:red;
}

div.error_msg{
	margin-top: 10px;
}

ul.advices-list li{
	margin-bottom:20px;
}

ul.advices-list li .nick{
	margin-bottom:5px;
	color: #000;
}

ul.advices-list li .nick a:link, ul.advices-list li .nick a:visited{
	
}

ul.advices-list li .name a:link, ul.advices-list li .name a:visited{
	color:#666;
}

ul.advices-list li .name a:hover{
}

ul.advices-list li .text2{
	display:none;
	margin-top:10px;
	color:black;
}

#ask-form{
	margin-top:20px;
	padding:10px 20px;
	float:left;
	background-color:#EEE;
	border:1px solid #CCC;
	font: 12px Arial;
}

#ask-form .title{
	font-size: 12px;
	text-transform: none;
	margin-top: 7px;
}

#ask-form input[type="text"]{
	width: 129px;
}

#ask-form input[type="text"], #ask-form textarea{
	border: #AAA solid 1px;
	padding: 2px 5px;
	font: 12px Arial;
	margin-top: 2px;
}
#ask-form input[type="submit"]{
	margin-top: 10px;
	padding: 1px 5px;
	cursor: pointer;
}
</style>
<script type="text/javascript">
$(function(){
	$(".advices-list>li>.name>a").click(function(){
		$(this).parent().find('+.text2').slideToggle();
		return false;
	});
});

function askForm(){
	var div = $('#ask-form').slideToggle('normal');
	return false;
}

function checkForm(f){
	$('.error_msg').remove();
	var msg = [];
	
	if(f['fields[ФИО]'].value=='') msg.push('<?=$vars[$api->lang]['Не заполнен поле ФИО']?>');	
	if(!f['fields[E-mail]'].value.match(/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/)) msg.push('<?=$vars[$api->lang]['Не корректный адрес электронной почты']?>');
	if(f['fields[Вопрос]'].value=='') msg.push('<?=$vars[$api->lang]['Не заполнен текст вопроса']?>');	
	if(f['captcha'].value=='') msg.push('<?=$vars[$api->lang]['Не заполнен код с картинки']?>');
	
	if(msg.length>0){
		alert("<?=$vars[$api->lang]['Следующие ошибки в заполнении']?>:\n\n"+msg.join("\n"));
		return false;
	}
	return true;
}
function newCaptcha(img){
	var randDig=(Math.random()*10)+1|0;
	img.attr('src', '/cms/files/appends/kcaptcha/index.php?rand='+randDig);
	return false;
}
</script>
<script language='javascript'>
	  function f(obj) {
		try {
		var sizetext = obj.getAttribute("MaxLength");
		var text = obj.value; 
		if(text.length > sizetext) {
			window.alert(">400");
		} 
		} catch(err) {
		  window.alert(err.Message);
		}

	  }
	</script>
<?=$vars[$api->lang]['topText']?>
<?=$error?>
<div id="ask-form" style="<?=($error?'':'display:none')?>">
	<form method="POST" onsubmit="return checkForm(this)">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
					<div class="row">
						<div class="title"><?=$vars[$api->lang]['yourName']?></div>
						<div><input type="text" name="fields[ФИО]" maxlength="25" value="<?=@$fields['ФИО']?>"></div>
					</div>
				</td>
					<td width="10">&nbsp;</td>
					<td valign="top">
					<div class="row">
						<div class="title">E-mail</div>
						<div><input type="text" name="fields[E-mail]" maxlength="25" value="<?=@$fields['E-mail']?>"></div>
					</div>
				</td>
			</tr>
		</table>
		<div class="row">
			<div class="title"><?=$vars[$api->lang]['yourQ']?></div>
			<div><textarea onkeypress='f(this);' MaxLength='400' name="fields[Вопрос]" style="width:280px; height:100px;"><?=@$fields['Вопрос']?></textarea></div>
		</div>
		<div class="row">
			<table border="0" cellspacing="0" cellpadding="0" style="margin-top: 7px;">
				<tr><td colspan="2"><?=$vars[$api->lang]['captcha']?></td></tr>
				<tr>
					<td valign="top" style="padding-right: 11px; padding-top: 5px;">
						<img src="<?=_FILES_?>/appends/kcaptcha/" id="captcha_img" />
					</td>
					<td valign="top" align="right" style="padding-top: 5px;">
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" height="30"><input type="text" maxlength="6" name="captcha" style="width:100px" /><span class="error"></span></td>
							</tr>
							<tr>
								<td align="left"><a href="#" onclick="return newCaptcha( $('#captcha_img') )"><?=$vars[$api->lang]['обновить код']?></a></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div><input type="submit" value="<?=$vars[$api->lang]['send']?>"></div>
	</form>
</div>
<div style="clear:both;"></div>
<br>
<?
# СТРАНИЦЫ
$pages = $api->pages($api->objects->getObjectsCount($object_id, $class_id, "AND o.active='1'".(empty($_SESSION['cms_root_auth']) ? " AND c.field_".$answ_id."!=''":'')), $onepage, 5);
if($list = $api->objects->getFullObjectsListByClass($object_id, $class_id, "AND o.active='1'".(empty($_SESSION['cms_root_auth']) ? " AND c.field_".$answ_id."!=''":'')." ORDER BY o.sort DESC LIMIT ".$pages['start'].", $onepage"))
{
	?>
	<div style="margin-bottom: 20px;">
		<a href="#" onclick="$('.text2').slideDown(); return false;"><?=$vars[$api->lang]['showAll']?></a> | <a href="#" onclick="$('.text2').slideUp(); return false;"><?=$vars[$api->lang]['hideAll']?></a>
	</div>
	<?
	$html = array('<div>', '<ul class="advices-list">');
	foreach($list as $a)
	{
		$name = $a['ФИО']?$a['ФИО']:'Анонимно';
		$html[]='<li style="margin-bottom: 7px; border-bottom: #AAA dotted 1px; padding-bottom: 7px;" id="li-'.$a['id'].'">';
			$html[]=(@$a['Показать E-mail']?'<div class="nick"><a href="mailto:'.$a['E-mail'].'" id="advice-'.$name_id.'-'.$a['id'].'">'.$name.'</a>':'<div class="nick" id="advice-'.$name_id.'-'.$a['id'].'">'.$name).'</div>';
			$html[]='<div class="name"><a href="#раскрыть ответ" id="advice-'.$quest_id.'-'.$a['id'].'">'.str_replace("\n", "<br>", $a['Вопрос']).'</a></div>';
			$html[]='<div class="text2" id="advice-'.$answ_id.'-'.$a['id'].'"'.(empty($_SESSION['cms_root_auth']) ? ' style="display:block;"':'').'>'.($a['Ответ']?str_replace("\n", "<br>", $a['Ответ']):'<font color="red">'.$vars[$api->lang]['требуется ответ.'].'</font>').'</div>';
			$html[]='<div><!--smart:{
						id : '.$a['id'].',
						actions : ["edit", "remove"],
						p : {
							remove : "#li-'.$a['id'].'"
						}
					}--></div>';
		$html[]='</li>';
	}
	$html[]='</ul></div>';
	$html[]='<div style="margin-top:20px;">'.$pages['html'].'</div>';
	
	echo join("\n", $html);
	
} else echo $vars[$api->lang]['noQ'];

$api->footer();
?>