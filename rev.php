<?
include('cms/public/api.php');
include_once(_FILES_ABS_."/mail.php");

$api->header(array('page-title'=>'<!--object:[134][18]-->'));
$mime_mail = new mime_mail();

$object_id = 153;
$class_id = 25;
$onepage = 7;

//$to_mails = array('as@go-web.kz');

# ЛОКАЛИ
$vars = array(
	"ru"=>array(
		"title"=>'Написать отзыв',
		"topText"=>'<div>Вы можете <a href="#" onclick="return askForm()">написать свой отзыв</a>.</div>',
		"addOk"=>'<font color="green">Спасибо за Ваш отзыв. Он успешно добавлен и в данный момент рассматривается администрацией сайта. После того, как администрация активирует его - он появится в общем списке.</font>',
		"errorCaptcha"=>'<font color="red">Код с картинки введён неверно.</font>',
		"errorAdding"=>'<font color="red">Ошибка добавления отзыва.</font>',
		"errorText"=>'<font color="red">Ошибка, не заполнен текст отзыва.</font>',
		"showAll"=>'раскрыть всё',
		"hideAll"=>'скрыть всё',
		"noQ"=>'Отзывов нет.',
		"yourName"=>'Ваше имя',
		"yourQ"=>'Ваш отзыв',
		"captcha"=>'Код с картинки',
		"обновить код"=>'обновить код',
		"send"=>'Отправить'
	),
	"en"=>array(
		"title"=>'Write a review',
		"topText"=>'<div>Here you can <a href="#" onclick="return askForm()">write your review</a>.</div>',
		"addOk"=>'<font color="green">Thank you for your review! After approving it by the administration, it will appeare on the site.</font>',
		"errorCaptcha"=>'<font color="red">Wrong picture data.</font>',
		"errorAdding"=>'<font color="red">Adding error. Try later.</font>',
		"errorText"=>'<font color="red">Text field is empty.</font>',
		"showAll"=>'show all',
		"hideAll"=>'hide all',
		"noQ"=>'There is no any reviews yet.',
		"yourName"=>'Your name',
		"yourQ"=>'Your question',
		"captcha"=>'Code from picture',
		"обновить код"=>'update cod',
		"send"=>'Send review'
	),
	"kz"=>array(
		"title"=>'Пiкiріңізді жазу',
		"topText"=>'<div>Here you can <a href="#" onclick="return askForm()">write your review</a>.</div>',
		"addOk"=>'<font color="green">Thank you for your review! After approving it by the administration, it will appeare on the site.</font>',
		"errorCaptcha"=>'<font color="red">Wrong picture data.</font>',
		"errorAdding"=>'<font color="red">Adding error. Try later.</font>',
		"errorText"=>'<font color="red">Text field is empty.</font>',
		"showAll"=>'show all',
		"hideAll"=>'hide all',
		"noQ"=>'There is no any reviews yet.',
		"yourName"=>'Your name',
		"yourQ"=>'Your question',
		"captcha"=>'Code from picture',
		"обновить код"=>'кодты жаңарту',
		"send"=>'Send review'
	)
);

# ДОБАВИТЬ
$error = '';
if(isset($_REQUEST['fields']) && is_array($fields = $_REQUEST['fields']))
{
	$error.= '<div>';
	if(@empty($_SESSION['captcha_keystring']) || $_REQUEST['captcha']!=$_SESSION['captcha_keystring']){
		$error.= $vars[$api->lang]['errorCaptcha'];
	} else {
		$object = array(
			'head'=>$object_id,
			'name'=>'Отзыв',
			'class_id'=>$class_id,
			'active'=>'0'
		);
		$d=array('Дата'=>date('Y-m-d'));
		$fields=array_merge($fields,$d);
		if($api->objects->createObjectAndFields($object, $fields))
		{
			$html = array();
			foreach($fields as $k=>$value){
				$html[]='<div><b>'.$k.'</b></div>';
				$html[]='<div>'.$value.'</div>';
				$html[]='<br>';
			}
			$mime_mail->from 	= $_SERVER['HTTP_HOST'];
			$mime_mail->subject	= 'Уведомление о новом отзыве';
			$mime_mail->body	= join("", $html);
			
			# РАССЫЛАЕМ
			if(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!='')){
				$mime_mail->to=trim($obj['Значение']);
			}else{
				$mime_mail->to='as@go-web.kz';
			}
			$mime_mail->send($mime_mail->to);
			# OK
			$error.= $vars[$api->lang]['addOk'];
			
		} else { $error.= $vars[$api->lang]['errorAdding']; }
	}
	$error.= '</div><br>';
}
echo $error;
?>
<style type="text/css">
ul.advices-list{
	list-style-type:square;
	color:red;
}

ul.advices-list li{
	margin-bottom:20px;
}

ul.advices-list li .nick{
	margin-bottom:5px;
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

function askForm(){
	var div = $('#ask-form').slideToggle('normal');
	return false;
}

function checkForm(f){
	var msg = [];
	
	if(f['fields[Имя]'].value=='') msg.push('Не заполнено Ваше имя');	
	if(!f['fields[Email]'].value.match(/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/)) msg.push('Не корректный адрес электронной почты');
	if(f['fields[Текст]'].value=='') msg.push('Не заполнен текст отзыва');
	if(f['captcha'].value=='') msg.push('Не заполнен код с картинки');
	
	if(msg.length>0){
		alert("Следующие ошибки в заполнении:\n\n"+msg.join("\n"));
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
<?=$vars[$api->lang]['topText']?>
<div id="ask-form" style="<?=($error?'':'display:none')?>">
	<form method="POST" onsubmit="return checkForm(this)">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
					<div class="row">
						<div class="title"><?=$vars[$api->lang]['yourName']?></div>
						<div><input type="text" name="fields[Имя]" value="<?=@$fields['Имя']?>"></div>
					</div>
				</td>
					<td width="10">&nbsp;</td>
				<td valign="top">
					<div class="row">
						<div class="title">E-mail</div>
						<div><input type="text" name="fields[Email]" value="<?=@$fields['Email']?>"></div>
					</div>
				</td>
			</tr>
		</table>
		<div class="row">
			<div class="title"><?=$vars[$api->lang]['yourQ']?></div>
			<div><textarea name="fields[Текст]" style="width:280px; height:100px;"><?=@$fields['Текст']?></textarea></div>
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
<?/*?>
<div>
	<a href="#" onclick="$('.text2').slideDown(); return false;"><?=$vars[$api->lang]['showAll']?></a> | <a href="#" onclick="$('.text2').slideUp(); return false;"><?=$vars[$api->lang]['hideAll']?></a>
</div>
<?*/
# СТРАНИЦЫ
$pages = $api->pages($api->objects->getObjectsCount($object_id, $class_id, (!empty($_SESSION['cms_root_auth']) ? "" : "AND o.active='1'")), $onepage, 5);
if($list = $api->objects->getFullObjectsListByClass($object_id, $class_id, (!empty($_SESSION['cms_root_auth']) ? "" : "AND o.active='1'")." ORDER BY o.sort DESC LIMIT ".$pages['start'].", $onepage"))
{
	$html = array('<div>');
	foreach($list as $o){
		$name = $o['Имя']?$o['Имя']:'Анонимно';
		$html[]='<div style="margin-bottom: 7px; border-bottom: #AAA dotted 1px; padding-bottom: 7px;" id="p-'.$o['id'].'"><div style="padding-bottom:5px;"><strong id="advice-42-'.$o['id'].'" style="font-weight: bold;">'.$name.'</strong>, '.$api->strings->date($o['Дата'], 'sql', 'textdateday').'</div>'.$o['Текст'].'</div>';
		$html[]='<div><!--smart:{
						id : '.$o['id'].',
						actions : ["edit", "remove"],
						p : {
							remove : "#p-'.$o['id'].'"
						}
				}--></div>';
	}
	$html[]='</div>';
	$html[]='<div style="padding-top:20px;">'.$pages['html'].'</div>';
	
	echo join("\n", $html);
	
} else echo $vars[$api->lang]['noQ'];

$api->footer();
?>