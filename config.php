<?php
	include_once 'cms/public/api.php';
	$api->header(array('page-title'=>'<!--object:[138][18]-->'));
	
	/*ID класса конфигуратора*/
	$config_class = 60;
	/*ID класса подраздела конфигуратора*/
	$sub_config_class = 63;
	
	/*ID класса элемента конфигуратора*/
	
	$config_element_class = 75;
	
	if(!empty($_REQUEST['model']) && $model = $_REQUEST['model']){
		if($obj = $api->objects->getFullObject($model)){
			if($config = $api->objects->getObjectsListByClass($obj['id'], $config_class, "AND o.active ORDER BY o.sort LIMIT 1")){
				$config_parent = $api->objects->getFullObject($config['head']);
				if(!empty($config_parent) && !empty($config_parent['Название по классу'])) 
					$car_class = $config_parent['Название по классу'];
				else $car_class = 'Mercedes-Benz';	
				//Массив где будет весь хтмл
				$html = array('<!--smart:{ id:'.$config['id'].', title: "текущего конфигуратора",  actions:["edit", "add"], p:{add:['.$sub_config_class.']}, css:{ position:"absolute"} }-->
				<div id="config-result" style="display:none!important;"></div>');
				//Базовая картинка сверху
				$main_picture = '';
				$base_image = $api->objects->getFullObjectsListByClass($config['id'], 4, "AND o.active ORDER BY o.sort LIMIT 1");
				if(!empty($base_image)){
					$main_picture = '
						<div id="config">
							<section id="mainphoto" default="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$base_image['Ссылка'].'"">
								<img src="'._IMGR_.'?w=1000&h=295&image='._UPLOADS_.'/'.$base_image['Ссылка'].'"/>
							</section>
						</div>
					';
					
				}else{
					$main_picture = '
					<div id="config">
						<section id="mainphoto">
							<h1 style="text-align:center">Картинка отсутствует</h1>
						</section>
					</div>	
					';
				}

				$html[] = $main_picture;
				
				//Табы сверху
				
				if($config_tabs = $api->objects->getFullObjectsListByClass($config['id'], $sub_config_class, "AND o.active ORDER BY o.sort")){
					$config_tabs_out = array('<ul class="nav nav-tabs config-tab-menu" id="myTab">');
					foreach($config_tabs as $c_t){
						$config_tabs_out[] = '
						<li class="config-li" data-id="'.$c_t['id'].'">
						<!--smart:{ id:'.$c_t['id'].', actions:["edit", "add"], p:{add:['.$config_element_class.']},   css:{ position:"absolute"} }-->'.
						( $api->auth()? '<a href="/cms/#list/'.$c_t['id'].'" target="_blank" class="fe-smart-menu" style="position: absolute; top: 300px; margin-top: 50px;">Посмотреть &rarr;</a>' : '' )
						.'<a href="#'.$c_t['css_id'].'"  data-toggle="tab">'.$c_t['Название'].'</a>
						</li>
						';
					}
					$config_tabs_out[] = '
					<li class="yours"><a href="#finish" data-toggle="tab">Ваш '.$car_class.'</a></li>
					</ul>
					<img src="/img/key.png" alt="" class="keypng"/>
					<div class="span9">
					<div class="tab_content config-tab-content">';
					foreach($config_tabs as $c_t){
						$config_tabs_out[] = '
						<div class="tab-pane" id="'.$c_t['css_id'].'" data-id="'.$c_t['id'].'">
							<!--ajax load here-->
						</div>';
					}
					$config_tabs_out[] = '
					<div class="tab-pane" id="finish">
						<!--Последний таб здесь идет выгрузка результатов -->
					</div>
					</div>
					</div>
					';
				}
				
				$html[] = '<div class="row"><section id="inform">'.(!empty($config_tabs)?join("\n", $config_tabs_out):'').'</section></div>';
				
				echo join("\n", $html);
			}
		}
	}
?>


<? $api->footer(); ?>