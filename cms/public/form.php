<?
include_once(_PUBLIC_ABS_.'/tools.php');
include_once(_PUBLIC_ABS_.'/notes_languages.php');
include_once(_PUBLIC_ABS_.'/form_validator.php');
class form
{
	
	protected  $ansTable;
	protected  $api;
	protected  $formId;
	protected  $arForm;
	protected  $strFormNote;
	protected  $__error_msg;
	protected  $notes_lang;
	
	function __construct($formId)
	{
		global $api;
		global $errors;
		global $notes;
		global $notes_lang;
		
		$this->api = $api;
		$this->ansTable = 'form_answer';
		$this->formId = $formId;
		$this->notes_lang = $notes_lang;
		$this->arForm = $this->api->objects->getFullObject($this->formId);
		$this->strFormNote = isset($notes[$formId])?$notes[$formId]:'';
		$this->__error_msg = isset($errors[$formId])?$errors[$formId]:'';
	}
	
	function JSValidate(){
		if (!$this->isUseJSValidate()) return ;
		
		$out = array();
		
		$out[] = '
			$(\'form[name="form_'.$this->formId.'"]\').submit(function(){
				var err = [];
				';
		if ($list = $this->api->objects->getFullObjectsListByClass($this->formId, 21)){
			foreach ($list as $o){
				$tmp = explode(" ", $o['Тип поля']);
				$type = '';
				for ($i = 0; $i < count($tmp); $i++)
					$type .= $tmp[$i].(($i < (count($tmp) - 1))?'_':'');
				
				$out[] = 'var el = $(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="form_'.$type.'_'.$o['id'].($type == 'checkbox'? '[]"]:checked':'"]').', select[name="form_'.$type.'_'.$o['id'].($type == 'multiselect'? '[]"]' : '"]').', textarea[name="form_'.$type.'_'.$o['id'].'"]\');
					el.removeClass("error_field");
				';
				
				if ($o['Обязательное'])
				{
					$out[] = '
						if (emptyCheck(el)){
							err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.empty_or_notselected'], $o['Название'])).'");
							el.addClass("error_field");
						}
					';
					switch ($o['Тип поля'])
					{
						case 'email':
							$out[] = '
								if (notEmptyCheck(el))
									if (!EmailCheck(el.val())){
										err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
										el.addClass("error_field");
									}
							';
							break;
						case 'only numeric':
							$out[] = '
								if (notEmptyCheck(el))
									if (!numberCheck(el.val())){
										err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
										el.addClass("error_field");
									}
							';
							break;
						case 'only alpha':
							$out[] = '
								if (notEmptyCheck(el))
									if (!nameCheck(el.val())){
										err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
										el.addClass("error_field");
									}
							';
							break;
						case 'url':
							$out[] = '
								if (notEmptyCheck(el))
									if (!urlCheck(el.val())){
										err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
										el.addClass("error_field");
									}
							';
							break;
					}
				}
				else
				{
					switch ($o['Тип поля'])
					{
						case 'email':
							$out[] = '
								if (!EmailCheck(el.val()) && notEmptyCheck(el)){
									err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
									el.addClass("error_field");
								}
							';
							break;
						case 'only numeric':
							$out[] = '
								if (!numberCheck(el.val()) && notEmptyCheck(el)){
									err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
									el.addClass("error_field");
								}
							';
							break;
						case 'only alpha':
							$out[] = '
								if (!nameCheck(el.val()) && notEmptyCheck(el)){
									err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
									el.addClass("error_field");
								}
							';
							break;
						case 'url':
							$out[] = '
								if (!urlCheck(el.val()) && notEmptyCheck(el)){
									err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.notcorrect'], $o['Название'])).'");
									el.addClass("error_field");
								}
							';
							break;
					}
				}
			}
			
			# CAPTCHA
			if ($this->isUseCaptcha())
			{
				$out[] = '
					if ($(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="captcha_word"]\').val() == ""){
						err.push("'.(sprintf($this->notes_lang[$this->api->lang]['error.captcha_empty'], $o['Название'])).'");
						$(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="captcha_word"]\').addClass("error_field");
					} else $(\'input[name="captcha_word"]\').removeClass("error_field");
				';
			}
			
		}
		
		$out[] = '
			if (err.length != 0){
			';
				if ($this->arForm['Вывод ошибок не в alert'] == 1)
					$out[] = '
						$(".error_'.$this->formId.'").html(err.join("<br />"));
					';
				else 
					$out[] = '
						alert(err.join("\n"));
					';
					
				$out[] = 'return false;
			}
			';
				
				$out[] = $this->ajaxSend();
				
			$out[] = '
				return true;
			});
		';
		
		return '<script type="text/javascript">'."\n".'$(document).ready(function(){'.join("\n", $out).'});'."\n".'</script>';
	}
	
	function ajaxSend($with_JS_validate = true){
		if (!$this->isUseAjax()) return ;
		
		$beginScript = $endScript = '';
		
		if ($with_JS_validate === false){
			$beginScript = '<script type="text/javascript">'."\n".'$(document).ready(function(){'."\n".'$(\'form[name="form_'.$this->formId.'"]\').submit(function(){';
			$endScript = '});'."\n".'});'."\n".'</script>';
		}
		
		$out = array('
			var fields = [];
		');
		
		if ($list = $this->api->objects->getFullObjectsListByClass($this->formId, 21)){
			foreach ($list as $o){
				$tmp = explode(" ", $o['Тип поля']);
				$type = '';
				for ($i = 0; $i < count($tmp); $i++)
					$type .= $tmp[$i].(($i < (count($tmp) - 1))?'_':'');
				
				$out[] = '
					var fields_val = [];
					var el = $(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="form_'.$type.'_'.$o['id'].($type == 'checkbox'? '[]"]:checked':'"]').', select[name="form_'.$type.'_'.$o['id'].($type == 'multiselect'? '[]"]' : '"]').', textarea[name="form_'.$type.'_'.$o['id'].'"]\');
					
					if (el.length > 0)
						var name = el.attr("name").replace("[]", "");
					else
						var name = el.attr("name");
				';
				if ($type == 'checkbox')
					$out[] = '
						el.each(function(index){
							fields_val.push(el.eq(index).val());
						});
						fields.push({name: name, val: fields_val});
					';
				elseif (($type == 'image') || ($type == 'file')) {
					$out[] = '
						$("<div>").attr({id: "upload-files-conteiner"})
						.hide()
						.html(\'<iframe id="form-transport-id" name="form-transport" el_name="\'+name+\'" src="about:blank"></iframe>\')
						.append(\'<form method="POST" action="/ajax.php?go=uploadFiles&name=\'+name+\'" target="form-transport" id="upload-form" enctype="multipart/form-data"></form>\')
						.appendTo($(\'form[name="form_'.$this->formId.'"]\').parent());
						
						var form = $("#upload-form");
						var el_copy = el.clone(true);
						el_copy.insertAfter(el);
						el.appendTo(form);
					';
				}
				else {
					$out[] = '
						fields.push({name: name, val: el.val()});
					';
				}
			}
		}
		
		$note = getJSTypedString($this->arForm['Текст об успешной отправке формы']);
		
		$out[] = '
			var submit_btn = $(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="web_form_submit"]\');
			var reset_btn = $(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="web_form_reset"]\');
			submit_btn.attr("disabled", "disabled");
			reset_btn.attr("disabled", "disabled");
			if ($("#form-transport-id").length > 0){
				files = {};
				$("#form-transport-id").load(function(){
					var el_name = $(this).attr("el_name");
					files = eval("("+$(this).contents().find("body").html()+")");
					mass = [];
					for (i in files){
						mass.push(files[i]);
					}
					
					fields.push({name: el_name, val: (mass)});
					
					$.post("/cms/public/form_validator.php", {web_form_submit:"1", formId:"'.$this->formId.'", f:fields, x:"ajaxRequest"'.($this->isUseCaptcha()? ', captcha_word: $(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="captcha_word"]\').val()' : '').'}, function(data){
						if (data.st == "success"){
							$(\'form[name="form_'.$this->formId.'"]\').find(\'img.captcha_img\').attr("src", "'._FILES_.'/appends/kcaptcha/index.php?'.session_name().'='.session_id().'");
							$(\'form[name="form_'.$this->formId.'"]\').find("div.error_'.$this->formId.'").html("");
							$(".error_'.$this->formId.'").html("");
							alert(\''.$note.'\');
							$.each($(\'form[name="form_'.$this->formId.'"] input, form[name="form_'.$this->formId.'"] textarea\'),function(){
								if (($(this).attr("type") != "submit") && ($(this).attr("type") != "reset"))
									$(this).val("");
								$(this).removeAttr("checked");
							});
						} else if (data.st == "error"){
							alert(data.error_msg);
						}
						submit_btn.removeAttr("disabled");
						reset_btn.removeAttr("disabled");
					}
					, "json"
					);
					$("#upload-files-conteiner").remove();
				});
				form.submit();
				
			} else {
				$.post("/cms/public/form_validator.php", {web_form_submit:"1", formId:"'.$this->formId.'", f:fields, x:"ajaxRequest"'.($this->isUseCaptcha()? ', captcha_word: $(\'form[name="form_'.$this->formId.'"]\').find(\'input[name="captcha_word"]\').val()' : '').'}, function(data){
					if (data.st == "success"){
						$(\'form[name="form_'.$this->formId.'"]\').find(\'img.captcha_img\').attr("src", "'._FILES_.'/appends/kcaptcha/index.php?'.session_name().'='.session_id().'");
						$(".error_'.$this->formId.'").html("");
						alert(\''.$note.'\');
						$.each($(\'form[name="form_'.$this->formId.'"] input, form[name="form_'.$this->formId.'"] textarea\'),function(){
							if (($(this).attr("type") != "submit") && ($(this).attr("type") != "reset"))
								$(this).val("");
							$(this).removeAttr("checked");
						});
					} else if (data.st == "error"){
						alert(data.error_msg);
					}
					submit_btn.removeAttr("disabled");
					reset_btn.removeAttr("disabled");
				}, "json");
			}
		';
		
		return $beginScript . join("\n", $out).' return false; ' . $endScript;
	}
	
	function getArAnswers($FIELD_SID = '')
	{
		if (!empty($FIELD_SID) && is_numeric($FIELD_SID))
		{
			$list = $this->api->db->select($this->ansTable, "WHERE `FIELD_ID` = '".$FIELD_SID."' ORDER BY `C_SORT`");
			return $list;
		}
		else
		return ;
	}
	
	function getFormId(){
		return $this->formId;
	}
	
	function ShowInput($FIELD_SID, $caption_css_class = '')
	{
		$res = "";
		
		$arAnswers = $this->getArAnswers($FIELD_SID);
		reset($arAnswers);
		$value = getCheckedValue($FIELD_SID, $arAnswers);
		
		$object = $this->api->objects->getFullObject($FIELD_SID);
		$tmp = explode(" ", $object['Тип поля']);
		$type = '';
		for ($i = 0; $i < count($tmp); $i++)
			$type .= $tmp[$i].(($i < (count($tmp) - 1))?'_':'');
			
		if ($object['active']){
			
			switch ($type)
			{
				case 'radio':
					$res .= InputType("radio", "form_radio_".$FIELD_SID, $arAnswers, $value, false, "", '', $caption_css_class).(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'checkbox':
					$res .= InputType("checkbox", "form_checkbox_".$FIELD_SID, $arAnswers, $value, false, "", '', $caption_css_class).(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'dropdown':
					$res .= SelectBoxFromArray("form_dropdown_".$FIELD_SID, $arAnswers, $value).(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'multiselect':
					$res .= SelectBoxFromArray("form_multiselect_".$FIELD_SID, $arAnswers, $value, true, 5).(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'text':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="text" size="'.$arAnswer['FIELD_WIDTH'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' name="form_text_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'only_numeric':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="text" size="'.$arAnswer['FIELD_WIDTH'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' name="form_'.$type.'_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" class="placeholder" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'only_alpha':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="text" size="'.$arAnswer['FIELD_WIDTH'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' name="form_'.$type.'_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'hidden':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="hidden" size="'.$arAnswer['FIELD_WIDTH'].'" name="form_hidden_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />';
					break;
				case 'password':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="password" size="'.$arAnswer['FIELD_WIDTH'].'" name="form_password_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'email':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="email" size="'.$arAnswer['FIELD_WIDTH'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' name="form_email_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'url':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="url" size="'.$arAnswer['FIELD_WIDTH'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' name="form_url_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'textarea':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<textarea name="form_textarea_'.$object['id'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' cols="'.$arAnswer['FIELD_WIDTH'].'" rows="'.$arAnswer['FIELD_HEIGHT'].'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'">'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'</textarea>'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case 'date':
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="text" size="'.$arAnswer['FIELD_WIDTH'].'" '.(!empty($object['Maxlength'])?'MaxLength="'.$object['Maxlength'].'"':'').' name="form_date_'.$object['id'].'" class="date-picker" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'" placeholder="'.htmlspecialchars($object['Значение по умолчанию']).'" />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case "image":
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="file" size="'.$arAnswer['FIELD_WIDTH'].'" name="form_image_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'"  />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
				case "file":
					list($key, $arAnswer) = each($arAnswers);
					$res .= '<input type="file" size="'.$arAnswer['FIELD_WIDTH'].'" name="form_file_'.$object['id'].'" value="'.(isset($_REQUEST['form_'.$type.'_'.$object['id']])?$_REQUEST['form_'.$type.'_'.$object['id']]:'').'"  />'.(!empty($object['Описание'])? '<span class="form_desc">' . htmlspecialchars( $object['Описание'] ) .'</span>' : '');
					break;
			}
			
		}

		return $res;
	}
	
	function ShowInputCaption($FIELD_SID, $css_style = "")
	{
		$ret = "";
		if (!($object = $this->api->objects->getFullObject($FIELD_SID)) && empty($object['Название'])) $ret = "";
		else
		{
			$ret = "<b>".$object['Название']."</b>".$this->ShowRequired($object["Обязательное"]);
		}
		
		if (strlen($css_style) > 0) $ret = "<span class=\"".$css_style."\">".$ret."</span>";

		// if (is_array($this->__form_validate_errors) && array_key_exists($FIELD_SID, $this->__form_validate_errors))
			// $ret = '<span class="form-error-fld" title="'.htmlspecialchars($this->__form_validate_errors[$FIELD_SID]).'"></span>'."\r\n".$ret;
		
		return $ret;
	}
	
	function ShowInputField($FIELD_SID, $css_style = ""){
		return $this->ShowInputCaption($FIELD_SID, $css_style) . $this->ShowInput($FIELD_SID, $css_style);
	}
	
	function ShowRequired($flag)
	{
		if ($flag=="1") return "<font color='red'><span class='form-required'>*</span></font>";
	}
	
	function ShowFormTitle($css_style = "")
	{
		$ret = trim(htmlspecialchars($this->arForm["Название"]));
		
		if (strlen($css_style) > 0) $ret = "<div class=\"".$css_style."\">".$ret."</div>";
		
		return $ret;
	}
	
	function ShowFormDescription($css_style = "")
	{
		$ret = trim($this->arForm["Описание формы"]);
		
		if (strlen($css_style) > 0) $ret = "<div class=\"".$css_style."\">".$ret."</div>";
		
		return $ret;
	}
	
	function ShowFormErrors(){
		ob_start();
		echo $this->__error_msg;
		$ret = ob_get_contents();
		ob_end_clean();
		
		return '<div class="error_'.$this->formId.'">'.$ret.'</div>';
	}
	
	function ShowFormNote($css_style = "")
	{
		if (!empty($this->__error_msg)) return ;
		$strNote = $this->strFormNote;
		
		if (!isset($strNote) || strlen($strNote) <= 0)
			return;		

		$strNote = str_replace("<br>", "\n", $strNote);
		$strNote = str_replace("<br />", "\n", $strNote);

		$strNote = htmlspecialchars($strNote);

		$strNote = str_replace("\n", "<br />", $strNote);
		$strNote = str_replace("&amp;", "&", $strNote);

		$ret = $strNote;
		$ret = '<p><font class="'.$css_style.'">'.$ret.'</font></p>';
		
		return $ret;
		
	}
	
	function ShowDateFormat($type, $css_style = "")
	{
		$format = $this->api->strings->date(date("Y-m-d"), "sql", $type);

		if (strlen($css_style) > 0) return '<span class="'.$css_style.'">'.$format.'</span>';
		else return $format;
	}
	
	function ShowFormTagBegin($method = 'post', $css_style = "")
	{
		$inputFormIdHidden = '<input type="hidden" name="formId" value="'.$this->formId.'" />';
		return '<form name="form_'.$this->formId.'" method="'.$method.'" enctype="multipart/form-data" '.(!empty($css_style)?'class="'.$css_style.'"':'').'>'.$inputFormIdHidden;
	}
	
	function ShowFormTagEnd()
	{
		return '</form>' . $this->JSValidate() . ($this->isUseJSValidate()? '' : $this->ajaxSend(false));
	}
	
	function getSubmitTitle(){
		return trim($this->arForm["Текст кнопки отправки"]);
	}
	
	function ShowSubmitButton($caption = "", $css_style = "")
	{		
		$button_value = strlen(trim($caption)) > 0 ? trim($caption) : (strlen(trim($this->arForm["Текст кнопки отправки"]))<=0 ? '' : $this->arForm["Текст кнопки отправки"]);
	
		return "<input type=\"submit\" name=\"web_form_submit\" value=\"".$button_value."\"".(!empty($css_style) ? " class=\"".$css_style."\"" : "")." />";
	}
	
	function ShowResetButton($caption = "", $css_style = "")
	{
		$button_value = strlen(trim($caption)) > 0 ? trim($caption) : (strlen(trim($this->arForm["Текст кнопки отправки"]))<=0 ? '' : $this->arForm["Текст кнопки отмены"]);
	
		return "<input type=\"reset\" name=\"web_form_reset\" value=\"".$button_value."\"".(!empty($css_style) ? " class=\"".$css_style."\"" : "")." />";	
	}
	
	function isUseCaptcha()
	{
		return $this->arForm["Использовать CAPTCHA"] == 1;
	}
	
	function isUseAjax(){
		return $this->arForm["Отправка через AJAX"] == 1;
	}
	
	function isUseJSValidate(){
		return $this->arForm["JS валидация"] == 1;
	}
	
	function ShowCaptchaImage()
	{
		
		if ($this->isUseCaptcha())
			return "<img src=\""._FILES_."/appends/kcaptcha/?".session_name()."=".session_id()."\" class=\"captcha_img\" width=\"120\" height=\"60\" /><br /><a href=\"#\" id=\"update_captcha\" onclick=\"return newCaptcha( $('.captcha_img') )\">".trim($this->arForm["Текст ссылки обновить для капчи"])."</a>";
		else return "";
	}
	
	function ShowCaptchaField()
	{
		if ($this->isUseCaptcha())
			return "<input type=\"text\" name=\"captcha_word\" size=\"30\" maxlength=\"6\" value=\"\" class=\"inputtext\" />";
		else return "";
	}
	
	function ShowCaptcha()
	{
		return ($this->ShowCaptchaImage()?$this->ShowCaptchaImage()."<br />":"").$this->ShowCaptchaField();
	}
	
	function ShowForm(){
		$out = '';
		
		if ($list = $this->api->objects->getFullObjectsListByClass($this->formId, 21, "AND o.active=1 ORDER BY o.sort")){
			$out .= $this->ShowFormTagBegin();
			$out .= $this->ShowFormTitle('form_title');
			$out .= $this->ShowFormDescription();
			$out .= '<div style="color: red;">'.$this->ShowFormErrors().'</div>';
			$out .= $this->ShowFormNote("good");
			foreach ($list as $o){
				$out .= '<p>'.$this->ShowInputCaption($o['id']).$this->ShowInput($o['id']).'</p>';
			}
			$out .= '<p>'.$this->ShowCaptcha().'</p>';
			$out .= '<p>'.$this->ShowSubmitButton().'&nbsp;&nbsp;&nbsp;'.$this->ShowResetButton().'</p>';
			$out .= $this->ShowFormTagEnd();
		}
		
		return $out;
		
	}
	
}
?>