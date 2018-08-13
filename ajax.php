<?
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
header("Content-Type: text/html; charset=utf-8");
include('cms/public/api.php');

if (@$_REQUEST["object-row"]){
	$dataArray= $_REQUEST["object-row"];
	// выкидываем первый элемент массива (который пришел из заголовка таблицы - тег TH)
	array_shift($dataArray);
	// обрабатываем массив
	foreach($dataArray as $line=>$value) { // $line у нас будет содержать номер позиции
		$value = str_replace('tPosition-', '', $value); // выкинем из значения ненужное, оставим только идентификаторы
		$id  = (int)$value; // на всякий случай. "Вдруг монетка встанет на ребро" :)
		// обновляем позиции в БД
		exit($id);
	}
}

if(isset($_REQUEST['vote']) && is_numeric($voting_id = $_REQUEST['vote'])){
	if(isset($_REQUEST['a_id']) && is_numeric($a_id = $_REQUEST['a_id'])) $api->voteOne($voting_id, $a_id);
	exit( $api->getVotingResults($voting_id) );
}

$go = $_REQUEST['go'];
switch($go){
    case 'loadYearWihImage':
        if (!empty($_GET['yearId'])) {
            $yearid = $_GET['yearId'];
            $src = '/img/window-year.png';
            $object = $api->objects->getFullObject($yearid);
            if ($object && isset($object['Картинка'])) {
                $src = 'src="'._IMGR_.'?w=156&h=100&image='._UPLOADS_.'/'.$object['Картинка'].'"';
                exit(json_encode(array('success' => true, 'src' => $src)));
            }
        }

        exit(json_encode(array('success' => false)));
    break;
	case 'parts':
		$where = !empty($_REQUEST['query']) ? " AND c.field_263 LIKE '".$api->db->prepare(trim($_REQUEST['query']))."%'":"";
		$parts = $api->objects->getFullObjectsListByClass(7720, 84, $where." AND o.active='1' ORDER BY c.field_263 LIMIT 10");
		echo json_encode(array_map(function($element){return ['id'=>$element['Артикул'], 'name'=> ''.$element['Артикул'].' - <b style="color:#b0b0b0;">' . $element['Название'] . '</b>'];}, $parts));
		exit();

		break;

	case 'loadTabContent':
		/*ID класса конфигуратора*/
			$config_class = 60;
		/*ID класса подраздела конфигуратора*/
			$sub_config_class = 63;
		/*ID класса элемента конфигуратора*/
	
		$config_element_class = 75;
		
		if(!empty($_REQUEST['tab_id'])){
		
			$tab_id = !empty($_REQUEST['tab_id'])?$_REQUEST['tab_id']:"";
			$config = $api->objects->getFullObject($tab_id);
			
		
			if($list = $api->objects->getFullObjectsListByClass($tab_id, $config_element_class, "AND o.active ORDER BY o.sort")){
			$out = array('<table><p>'.(!empty($config['Текст что выбрать'])?$config['Текст что выбрать']:'').'</p>');
				foreach($list as $o){
					
					/*Фотки внутри каждого элемента конфигуратора*/
						
					if($photos = $api->objects->getFullObjectsListByClass($o['id'], 4, "AND o.active ORDER BY o.sort LIMIT 3")){
						$photos_out = array('<td class="tableimg">');
						foreach($photos as $photo){
							$photos_out[] = '<img id="photo-conf-'.$photo['id'].'" style="padding: 3px;" src="'._IMGR_.'?w=70&h=40&image='._UPLOADS_.'/'.$photo['Ссылка'].'"/>';
						}
						$photos_out[] = '</td>';
					}

					/*Цвета*/
					if($colors = $api->objects->getFullObjectsListByClass($o['id'], 77, "AND o.active ORDER BY o.sort")){
						$mass = array('<div style="width:350px;" class="color-block">');
						foreach($colors as $color){
							$mass[] = '<img alt="'.(!empty($color['Описание'])?$color['Описание']:'Нет описания').'" color-id="'.$color['id'].'" '.(!empty($color['Цена'])?'price="'.$color['Цена'].'"':'').' class="color" style="padding:3px; width:51; height:20px;" '.(!empty($color['Картинка на изменение'])?'img-to-load="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$color['Картинка на изменение'].'"':'').' title="'.$color['Название'].'" src="'._IMGR_.'?w=51&h=20&image='._UPLOADS_.'/'.$color['Картинка'].'"/>';
						}
						$mass[] = '</div>';
					}
					/*Цвета обивки*/
					if($colors_inner = $api->objects->getFullObjectsListByClass($o['id'], 79, "AND o.active ORDER BY o.sort")){
						$colors_inner_out = array('<div style="width:450pxl" class="colors-inner-block">');
						foreach($colors_inner as $c_i){
							$colors_inner_out[] = '<img alt="'.(!empty($c_i['Описание'])?$c_i['Описание']:'Описание отсутствует').'" '.(!empty($c_i['Цена'])?'price="'.$c_i['Цена'].'"':'').' class="color-inner" style="padding:3px;" '.(!empty($c_i['Картинка на изменение'])?'img-to-load="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$c_i['Картинка на изменение'].'"':'').' title="'.$c_i['Название'].'" src="'._IMGR_.'?w=41&h=29&image='._UPLOADS_.'/'.$c_i['Картинка'].'"/>';
						}
						$colors_inner_out[] = '</div>';
					}
					
					
					/*Внутренние табы*/
					
					if($tabs = $api->objects->getFullObjectsListByClass($o['id'], 71, "AND o.active ORDER BY o.sort")){
						$counter = 0;
						$tabs_menu = array('<ul class="nav nav-pills">');
						$tabs_content = array('<div class="tab-content-inner">');							
						
								 $kurs = $api->objects->getFullObject(7299); //EURO
                if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(2)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
        	}
                if($api->section->sectionId == 322 || $api->section->sectionId == 595) {

                 $kurs = $api->objects->getFullObject(7300); //RUB
                if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(3)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

                }

						foreach($tabs as $t){

					

							$tabs_menu[] = '<li><a href="#tb'.$counter.'" data-toggle="tab"><span>'.$t['Название'].'</span></a></li>';
							$tabs_content[] = '<div class="tab-pane" id="tb'.$counter.'"><p style="margin:10px;">'.$t['Название'].'</p>';
							if($tabs_inner = $api->objects->getFullObjectsListByClass($t['id'], $config_element_class, "AND o.active ORDER BY o.sort")){
								$tabs_content[] = '<div class="tab-inner-block">';
								foreach($tabs_inner as $t_i){

									  $cost = $t_i['Цена'];

                       if(!empty($kurs['Значение'])) {
                            $cost = preg_replace('/\D+/Ui', '', $t_i['Цена']);

                            $cost = intval($cost);

                            $cost*=$kurs['Значение'];
                            $cost = number_format($cost, 0, ''," ");
                            $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $t_i['Цена']));


                        } else {
                           

                           $cost = preg_replace('/\D+/Ui', '', $t_i['Цена']);

                        $cost = intval($cost);
                      //  echo '<script>alert(" ('.$kursBanks.' * 0.015) = '.( intval($kursBanks) * 0.015).'")</script>';

                  //  echo '<script>alert("цена * ( евро + евро * 0,015 ) = '.$cost.' * ('.$kursBanks.' + '.$kursBanks.' * 0.015) = '.($cost * ($kursBanks + $kursBanks * 0.015)).'")</script>';
                        $cost = $cost * ($kursBanks + $kursBanks * 0.015);

                  //  $cost = $cost * intval($euroBanks);

                        $cost = number_format($cost, 0, ''," ");

                        $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $t_i['Цена']));



                        }

                        $t_i['Цена'] = $cost;

									if($photos_tab_inner = $api->objects->getFullObjectsListByClass($t_i['id'], 4, "AND o.active ORDER BY o.sort LIMIT 3")){
										$photos_tab_inner_out = array();
										foreach($photos_tab_inner as $photo){
											$photos_tab_inner_out[] = '<img id="photo-conf-'.$photo['id'].'" style="padding: 3px;" src="'._IMGR_.'?w=70&h=40&image='._UPLOADS_.'/'.$photo['Ссылка'].'"/>';
										}
									}
									$tabs_content[] = '	
									<div class="element row" style="border-top: 1px solid #BEBEBE;">
											<div class="single input"><input type="radio" value="" name="element-'.$t['id'].'" '.(!empty($t_i['Цена'])?'price="'.$t_i['Цена'].'"':'').' '.(!empty($t_i['Картинка на замену'])?'img-to-load="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$o['Картинка на изменение'].'"':'').'
											'.(!empty($t_i['Название'])?'fieture-name="'.$t_i['Название'].'"':'').' '.(!empty($t_i['Доп текст 1'])?'text1="'.$t_i['Доп текст 1'].'"':'').'
											'.(!empty($t_i['Доп текст 2'])?'text2="'.$t_i['Доп текст 2'].'"':'').'
											'.(!empty($t_i['Доп текст 3'])?'text3="'.$t_i['Доп текст 3'].'"':'').'> </div>
											<div class="single title"><p>'.$t_i['Название'].'</p></div>
											'.(!empty($photos_tab_inner)?'<div class="tab-photos">'.join("\n", $photos_tab_inner_out).'</div>':'<div class="tab-photos"></div>').'
											<div class="single">'.(!empty($t_i['Цена'])?$t_i['Цена'].' тг':'').'</div>
											<div style="clear:both"></div>
									</div>';
								}
								$tabs_content[] = '</div>';
							}
							$tabs_content[]='</div>';
							$counter++;
						}
						$tabs_menu[] = '</ul>';
						$tabs_contnent[]= '</div>';
						
					}

					/*Если есть цвета, либо есть цвета обивки - тогда это страничка декора, у нее своя верстка*/
					
					if(!empty($colors) || !empty($colors_inner)){
						if(!empty($colors)){
							$out[] = '
								<div class="colors" style="width: 400px;float:left">
									<div class="bigprew">
										<img src="/cms/uploads/config/BGT.png" alt="">
										<div class="bigpic"><img src="'._IMGR_.'?w=69&h=66&image='._UPLOADS_.'/'.$colors[0]['Картинка'].'"/></div>											
									</div>
									'.join("\n", $mass).'
									<div style="width:100%; padding-top: 10px;"><p class="color-title" style="clear:both; margin:5px;">'.(!empty($colors[0]['Название'])?$colors[0]['Название']:'').'<span style="font-weight:normal;">'.(!empty($colors[0]['Описание'])?' '.$colors[0]['Описание']:' Нет описания').'</span></p></div>
								</div>
								<div class="ajax-wheel-load"></div>';
						}
						
						if(!empty($colors_inner)){
							$out[] = '
								<div class="colors-inner" style="width: 450px;float:left">
									<div class="bigprew">
										<img src="/cms/uploads/config/BGT.png" alt="">
										<div class="bigpic"><img src="'._IMGR_.'?w=69&h=66&image='._UPLOADS_.'/'.$colors_inner[0]['Картинка'].'"/></div>											
									</div>
									'.join("\n", $colors_inner_out).'
									<div style="width:100%; padding-top: 10px;"><p class="color-inner-title" style="clear:both; margin:5px;">'.(!empty($colors_inner[0]['Название'])?$colors_inner[0]['Название']:'').'<span style="font-weight:normal;">'.(!empty($colors_inner[0]['Описание'])?$colors_inner[0]['Описание']:'Описание отсутствует').'</span></p></div>
								</div>';
						}
					}elseif(!empty($tabs)){
						/*Если у нас есть табы то выводим табы и все что внутри табов*/
						$out[] = join("\n",$tabs_menu).join("\n",$tabs_content);
					}
					else{

						 $kurs = $api->objects->getFullObject(7299); //EURO
               
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(2)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
        
                if($api->section->sectionId == 322 || $api->section->sectionId == 595) {

                 $kurs = $api->objects->getFullObject(7300); //RUB
               
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(3)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);

                }





                
                        
                         $cost = $o['Цена'];

                       if(!empty($kurs['Значение'])) {
                            $cost = preg_replace('/\D+/Ui', '', $o['Цена']);

                            $cost = intval($cost);

                            $cost*=$kurs['Значение'];
                            $cost = number_format($cost, 0, ''," ");
                            $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $o['Цена']));


                        } else {
                           

                           $cost = preg_replace('/\D+/Ui', '', $o['Цена']);

                        $cost = intval($cost);
                      //  echo '<script>alert(" ('.$kursBanks.' * 0.015) = '.( intval($kursBanks) * 0.015).'")</script>';

                  //  echo '<script>alert("цена * ( евро + евро * 0,015 ) = '.$cost.' * ('.$kursBanks.' + '.$kursBanks.' * 0.015) = '.($cost * ($kursBanks + $kursBanks * 0.015)).'")</script>';
                        $cost = $cost * ($kursBanks + $kursBanks * 0.015);

                  //  $cost = $cost * intval($euroBanks);

                        $cost = number_format($cost, 0, ''," ");

                        $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $o['Цена']));



                        }

                        $o['Цена'] = $cost;



						/*А иначе это обычная страничка где списком выводим элементы конфигуратора*/
						$out[] = '
							<tr>
								<td><input type="radio" name="element-'.$tab_id.'" '.(!empty($o['Название'])?'fieture-name="'.$o['Название'].'"':'').' '.(!empty($o['Цена'])?'price="'.$o['Цена'].'"':'').' '.(!empty($o['Картинка на замену'])?'img-to-load="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$o['Картинка на замену'].'"':'').'
								'.(!empty($o['Доп текст 1'])?'text1="'.$o['Доп текст 1'].'"':'').' '.(!empty($o['Доп текст 2'])?'text2="'.$o['Доп текст 2'].'"':'').' 
								'.(!empty($o['Доп текст 3'])?'text3="'.$o['Доп текст 3'].'"':'').'></td>
								<td><p>'.$o['Название'].'</p></td>'
								.(!empty($photos)?'<td class="tableimg">'.join("\n",$photos_out).'</td>':'<td class="tableimg"></td>')
								.(!empty($o['Доп текст 1'])?'<td>'.$o['Доп текст 1'].'</td>':'')
								.(!empty($o['Доп текст 2'])?'<td>'.$o['Доп текст 2'].'</td>':'')
								.(!empty($o['Доп текст 3'])?'<td>'.$o['Доп текст 3'].'</td>':'').'
								<td>'.(!empty($o['Цена'])?$o['Цена']:'').' тг</td>
								<td><a class="description-info" href="#description-'.$o['id'].'"></a>'
									.(!empty($o['Общая информация'])?'<div class="description-open">
									<div class="description-header">
										<a class="description-close" href="#close">x</a>
									</div>
									<h4>'.$o['Название'].'</h4><p class="description-price">Цена '.$o['Цена'].' &#8364</p>
									<div class="description">'.$o['Общая информация'].'</div></div>':'').'
								</td>
								
							</tr>';
					}
				}
				$out[] = '
				<button class="next">Далее</button>
				</table>';
				exit(join("\n", $out));
			}else exit('Пусто');
			//Если это не таб, и не цвет, и не элемент конфигуратора, и все параметры заполнены, значит это подсчет цены
		}else{
			if(!empty($_REQUEST['model']) || !empty($_REQUEST['lines']) || !empty($_REQUEST['colors']) || !empty($_REQUEST['decors'])
			|| !empty($_REQUEST['wheels']) || !empty($_REQUEST['options'])){
			$out = array();
			$out[] = '
				<div class="row header"><h1>Ваш Mersedes:</h1></div>
				<div class="container result-container" style="font-weight: bold; color: #666;">
					'.(!empty($_REQUEST['model'])?'<div class="row list-group-item"><h3>Модель</h3></div><div class="row">'.$_REQUEST['model'].'</div>':'').'
					'.(!empty($_REQUEST['lines'])?'<div class="row list-group-item"><h3>Линии исполнения экстерьера</h3></div><div class="row">'.$_REQUEST['lines'].'</div>':'').'
					'.(!empty($_REQUEST['colors'])?'<div class="row list-group-item"><h3>Цвета</h3></div><div class="row">'.$_REQUEST['colors'].'</div>':'').'
					'.(!empty($_REQUEST['wheels'])?'<div class="row list-group-item"><h3>Диски</h3></div><div class="row">'.$_REQUEST['wheels'].'</div>':'').'
					'.(!empty($_REQUEST['decors'])?'<div class="row list-group-item"><h3>Оббивка и декоративные элементы</h3></div><div class="row">'.$_REQUEST['decors'].'</div>':'').'
					'.(!empty($_REQUEST['options'])?'<div class="row list-group-item"><h3>Дополнительное оборудование</h3></div><div class="row">'.$_REQUEST['options'].'</div>':'').'
				</div>';
				exit(join("\n", $out));
			}else exit('<div class="row header"><h1>Выберите хотя бы одну конфигурацию</h1></div>');
		}
	break;
	// Загрузка дисков
	case 'load_wheel':
		if(!empty($_REQUEST['color_id'])){
			/*Диски*/
			if($wheels = $api->objects->getFullObjectsListByClass($_REQUEST['color_id'], 78, "AND o.active ORDER BY o.sort")){
				$wheels_out = array('<div style=width:580px; class="wheel-block"">');
				foreach($wheels as $wheel){
					$wheels_out[] = '<img alt="'.(!empty($wheel['Описание'])?$wheel['Описание']:'Описание отсутствует').'" '.(!empty($wheel['Цена'])?'price="'.$wheel['Цена'].'"':'').' class="wheel" style="padding:3px;" '.(!empty($wheel['Картинка на изменение'])?'img-to-load="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$wheel['Картинка на изменение'].'"':'').' title="'.$wheel['Название'].'" src="'._IMGR_.'?w=45&h=45&image='._UPLOADS_.'/'.$wheel['Картинка'].'"/>';
				}
				$wheels_out[] = '</div>';
			}
			
			
			if(!empty($wheels)){
				$out[] = '
					<div class="wheels" style="width:400px; float:left;">
						<div class="bigprew">
							<img src="/cms/uploads/config/BGT.png" alt="">
							<div class="bigpic"><img src="'._IMGR_.'?w=69&h=66&image='._UPLOADS_.'/'.$wheels[0]['Картинка'].'"/></div>
						</div>
						'.join("\n", $wheels_out).'
						<div style="width:100%; padding-top:10px;"><p class="wheel-title" style="clear: both; margin:5px;">'.(!empty($wheels[0]['Название'])?$wheels[0]['Название']:'').'<span style="font-weight:normal;">'.(!empty($wheels[0]['Описание'])?$wheels[0]['Описание']:'Описание отсутствует').'</span></p></div>
					</div>';
				exit(join("\n",$out));	
			}else exit('<div class="wheels" style="width:400px; float:left;"><h3>Нет ни одного диска для этого цвета...</h3></div>');
			
			
		}	
	break;
	
	case 'uploadFiles':
		if(empty($_FILES)) return "[]";
		$out = array();
		move_uploaded_file( $_FILES[$_REQUEST['name']]['tmp_name'], _UPLOADS_ABS_."/".$_FILES[$_REQUEST['name']]['name']);
		$_FILES[$_REQUEST['name']]['tmp_name'] = _UPLOADS_ABS_."/".$_FILES[$_REQUEST['name']]['name'];
		exit(json_encode($_FILES[$_REQUEST['name']]));
	case 'auth':
		#CONFIG
		$auth_base_id = 14;

		if( !preg_match("/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/", ($mail=$_REQUEST['mail'])) )
			exit('{"st": "bad", "text": "E-mail некорректен."}');
		else if( !($u = $api->db->select("objects", "WHERE `head`='".$auth_base_id."' AND `name`='".$mail."' LIMIT 1")) )
			exit('{"st":"bad", "text":"Такой пользователь не зарегистрирован."}');
		else if( $u['active'] != 1 )
			exit('{"st":"bad", "text":"Ваша учётная запись не активна. Дождитесь пожалуйста проверки администратором."}');
		$lang = $api->lang;
		$api->lang = 'ru';
		$u = array_merge($u, $api->objects->getObjectFields($u['id'], $u['class_id']));
		$api->lang = $lang;

		if($u['Пароль']!=sha1($_REQUEST['pass'])) exit('{"st":"bad", "text":"Неверный пароль."}');

		$out = array(
			"st"=>'ok',
			"id"=>$u['id'],
			"head"=>$u['head'],
			"name"=>$u['фио'],
			"mail"=>$u['name']
		);

		$_SESSION['auth']['u'] = $out;

		exit( $api->json($out) );
	case 'exit':
		unset($_SESSION['auth']['u']);
		exit('ok');
	case 'subscribe_add':
		if( !preg_match("/^[\d\w\.-]+@([\d\w-]+)((\.[\w\d-]+)+)?\.\w{2,6}$/", ($mail=$_REQUEST['mail'])) )
			exit('E-mail некорректен.');
		else if( !!$api->db->select('objects', "WHERE `head`='11' AND `name`='".$mail."' LIMIT 1") )
			exit('Вы уже подписаны.');

		$out = array(
			"head"=>11,
			"sort"=>time(),
			"name"=>$mail
		);

		$api->db->insert('objects', $out);
		exit('Вы успешно подписались!');
		break;
	case 'getCount':
		$total_count = 0;
		$total_summ = 0;
		if (is_array($_SESSION['rycle']))
		{
			foreach($_SESSION['rycle'] as $o)
			{
				if ($obj = $api->objects->getFullObject($o['id'], false))
				{
					if ($_REQUEST['city'] != $o['city']) continue; #Показ товара только из определенного города
					$total_count++;
					$total_summ += (intval($obj['Цена'])*$o['count']);
				}
			}
		}
		exit(json_encode($total_count));
		break;
	case 'getSumm':
		$total_summ = 0;
		if (is_array($_SESSION['rycle']))
		{
			foreach($_SESSION['rycle'] as $o)
			{
				if ($obj = $api->objects->getFullObject($o['id'], false))
				{
					if ($_REQUEST['city'] != $o['city']) continue; #Показ товара только из определенного города
					$total_summ += (intval($obj['Цена'])*$o['count']);
				}
			}
		}
		exit(json_encode($total_summ));
		break;
	case 'buy':
		if(!empty($_REQUEST['id']) && is_numeric($id = $_REQUEST['id']) && !empty($_REQUEST['count']) && is_numeric($count = $_REQUEST['count']) && !!($item = $api->objects->getFullObject($id))){
			$lang = @$_REQUEST['lang'];
			if(!isset($_SESSION['rycle'])) $_SESSION['rycle']= array();
			$_SESSION['rycle'][$id]=array(
				"id"=>$id,
				"count"=>$count,
				"pic"=>$item['Изображение'],
				"price"=>$item['Цена'],
				"name"=>$item['Название'],
				"anons"=>$item['Анонс']
			);
			exit($api->v('Товар успешно добавлен в корзину.', $lang));
		}
		exit('Ошибка в параметрах.');
		break;
	case 'order':
	    if (!empty($_REQUEST['fields'])){
		    include_once(_FILES_ABS_.'/mail.php');
		    $fields = $_REQUEST['fields'];
				    $smail = new mime_mail();
				    $html = array();
				    if(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!='')){
					    $smail->to=trim($obj['Значение']);
				    }else{
					    $smail->to='as@go-web.kz';
				    }
				    $smail->from 		= 'order@'.$_SERVER['HTTP_HOST'];
				    $smail->subject		= 'Сообщение с сайта '.$_SERVER['HTTP_HOST'];
				    foreach($fields as $k=>$f){
					    if(empty($f)) continue;
					    $html[]='<div><b>'.$k.'</b></div>';
					    $html[]='<div>'.strip_tags($f).'</div>';
					    $html[]='<br>';
				    }
				    $smail->body = join("", $html);

				    # отправляем
				    $smail->send($smail->to);
		    exit('Ваше заявка успешно отправлена');
	    }
	break;
	default:
		echo '0';
		break;
}
?>