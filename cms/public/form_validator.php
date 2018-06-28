<?php
$errors = '';

function isValidEmail($email)
{
	$email = trim($email);
	return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email);
}

function isValidNumeric($text)
{
	$text = trim($text);
	// return preg_match("/^[\Q^+-\E0-9\s]+$/", $text);
	return preg_match("/[0-9]+$/", $text);
}

function isValidAlpha($text)
{
	$text = trim($text);
	return preg_match('/[a-zA-Zа-ячфёйА-ЯФЧЁЙ]+$/', $text);
}

function isValidUrl($text)
{
	$text = trim($text);
    $url_pattern = "~(?:(?:ftp|https?)?://|www\.)(?:[a-z0-9\-]+\.)*[a-z]{2,6}(:?/[a-z0-9\-?\[\]=&;#]+)?~i"; 
    return preg_match($url_pattern, $text);
}

function isAjax(){
	if (!empty($_REQUEST['x']))
		if ($_REQUEST['x'] == 'ajaxRequest')
			return true;
	
	return false;
}

if (isset($_REQUEST['web_form_submit']))
{
	if (isset($_REQUEST['formId'])){
		$formId = $_REQUEST['formId'];
		$errors[$formId] = '';
		if (isAjax()){
			include_once 'api.php';
			// exit(json_encode(($_REQUEST['form_image_170'])));
			foreach ($_REQUEST['f'] as $k => $fields){
				if (strpos($fields['name'], 'image') || strpos($fields['name'], 'file')){
					$_FILES[$fields['name']]['name'] = $fields['val'][0];
					$_FILES[$fields['name']]['type'] = $fields['val'][1];
					$_FILES[$fields['name']]['tmp_name'] = $fields['val'][2];
					$_FILES[$fields['name']]['error'] = $fields['val'][3];
					$_FILES[$fields['name']]['size'] = $fields['val'][4];
				}
				else
					$_REQUEST[$fields['name']] = $fields['val'];
			}
			// print_r($_REQUEST['f']);
			// exit();
			// exit((($_REQUEST['form_image_170'])));
		}
		$api->lang = 'ru';
		
		if (!empty($_REQUEST['lang']))
			$api->lang = $_REQUEST['lang'];
		
		if (($formObject = $api->objects->getFullObject($formId)) && ($list = $api->objects->getFullObjectsListByClass($formId, 21)))
		{
			foreach ($list as $o)
			{
				
				$tmp = explode(" ", $o['Тип поля']);
				$type = '';
				for ($i = 0; $i < count($tmp); $i++)
					$type .= $tmp[$i].(($i < (count($tmp) - 1))?'_':'');
				if ($o['Обязательное'] && empty($_REQUEST['form_'.$type.'_'.$o['id']]) && ($o['Тип поля'] != 'image') && ($o['Тип поля'] != 'file'))
				{
					$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.empty_or_notselected'], $o['Название'])).'<br />';
				} elseif($o['Обязательное'] && (($o['Тип поля'] == 'image') || ($o['Тип поля'] == 'file')) && empty($_FILES['form_'.$type.'_'.$o['id']]['name'])){
					$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.empty_or_notselected'], $o['Название'])).'<br />';
				}
				else
				{
					if (!empty($_REQUEST['form_'.$type.'_'.$o['id']])){
						switch ($o['Тип поля'])
						{
							case 'email':
								if (!isValidEmail($_REQUEST['form_'.$type.'_'.$o['id']]))
									$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.notcorrect'], $o['Название'])).'<br />';
								break;
							case 'only numeric':
								if (!isValidNumeric($_REQUEST['form_'.$type.'_'.$o['id']]))
									$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.notcorrect'], $o['Название'])).'<br />';
								break;
							case 'only alpha':
								if (!isValidAlpha($_REQUEST['form_'.$type.'_'.$o['id']]))
									$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.notcorrect'], $o['Название'])).'<br />';
								break;
							case 'url':
								if (!isValidUrl($_REQUEST['form_'.$type.'_'.$o['id']]))
									$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.notcorrect'], $o['Название'])).'<br />';
								break;
						}
					}
				}
			}
			
			# CAPTCHA
			if (!empty($formObject['Использовать CAPTCHA']))
			{
				if (empty($_SESSION['captcha_keystring']) || ($_REQUEST['captcha_word'] != $_SESSION['captcha_keystring']))
					$errors[$formId] .= stripslashes(sprintf($notes_lang[$api->lang]['error.captcha_not_correct'], $o['Название'])).'<br />';
			}
			
			# ЕСЛИ ОШИБОК НЕТ, ОТПРАВЛЯЕМ ФОРМУ НА АДМИНСКИЙ ЯЩИК ИЛИ НА E-MAIL ВВЕДЕННЫЙ В ПОЛЕ "E-mail для отправки"
			if (empty($errors[$formId])){
			
				if (!empty($formObject['E-mail для отправки']))
					$to=trim($formObject['E-mail для отправки']);
				elseif(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!=''))
					$to=trim($obj['Значение']);
				else
					$to='baitileuov.gani@go-web.me';
				
				$api->mail->to = $to;
				
				$api->mail->from = 'admin@'.$_SERVER['HTTP_HOST'];
				$api->mail->subject = 'Сообщение с формы '.$formObject['Название'].' на сайте '.$_SERVER['HTTP_HOST'].'';
				
				$html = array();
				if ($list = $api->objects->getFullObjectsListByClass($formId, 21)){
					foreach ($list as $o){
						$tmp = explode(" ", $o['Тип поля']);
						$type = '';
						for ($i = 0; $i < count($tmp); $i++)
							$type .= $tmp[$i].(($i < (count($tmp) - 1))?'_':'');
							
						if (!empty($_REQUEST['form_'.$type.'_'.$o['id']])){
							$html[] = '<div><strong>'.$o['Название'].':</strong></div><div>'.(is_array($_REQUEST['form_'.$type.'_'.$o['id']])? join(", ", $_REQUEST['form_'.$type.'_'.$o['id']]) : $_REQUEST['form_'.$type.'_'.$o['id']]).'</div><br />';
						}
						if (!empty($_FILES['form_'.$type.'_'.$o['id']]['name'])){
							$api->mail->add_attachment(file_get_contents($_FILES['form_'.$type.'_'.$o['id']]['tmp_name']), $_FILES['form_'.$type.'_'.$o['id']]['name'], $_FILES['form_'.$type.'_'.$o['id']]['type']);
							@unlink($_FILES['form_'.$type.'_'.$o['id']]['tmp_name']);
						}
						
					}
				}
				
				$api->mail->body = join("", $html);
				$api->mail->send($api->mail->to);
				
				$notes[$formId] = $formObject['Текст об успешной отправке формы'];
				
			}
			
			// exit($errors);
			if (isAjax()){
				if (empty($errors[$formId])){
					exit($api->json(array('st' => 'success', 'data' => join("", $html))));
				} else {
					exit($api->json(array('st' => 'error', 'error_msg' => getJSTypedString($errors[$formId]))));
				}
			}
			
		}
	}
	
}

?>