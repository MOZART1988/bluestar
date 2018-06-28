<?
$tmId=4; //top menu id
$catalog=15; //catalog
$map = array(4, 19,324);
# ФУНКЦИЯ ПОЛУЧЕНИЯ ВЛОЖЕННЫХ ОБЪЕКТОВ
function getSubm($id)
{
	global $api;
	if ($sub_menu = $api->objects->getFullObjectsList($id)) {
		$out = array('<ul style="padding: 0px 0 0 30px;">');
		foreach($sub_menu as $o)
		{
			# Ссылка на файл
			if ($o['class_id'] == 2){
				$out[] = '<li><a '.$api->getLink($o['id']).''.($o['В новом окне'] == 1 ? ' target="_blank"':'').'>'.htmlspecialchars_decode($o['Название']).'</a>'.getSubm($o['id']).'</li>';		
			} else if (($o['class_id'] == 1) || ($o['class_id'] == 3))  {
				$out[] ='<li><a '.$api->getLink($o['id']).'>'.htmlspecialchars_decode($o['Название']).'</a>'.getSubm($o['id']).'</li>';
			}
		}
		$out[] = '</ul>';
		
		return join("\n", $out);		
	}
	return '';
}

# ЕСЛИ ЕСТЬ МЕНЮ
for($i = 0; $i < count($map); $i++){
	if($menu = $api->objects->getFullObjectsList($map[$i]))
	{
		$tm=$api->objects->getFullObject($map[$i]);
		$out = Array('<ul>');
		foreach($menu as $o)
		{
			# Ссылка на файл
			if ($o['class_id'] == 2)
			{
				$href='http://'.$o['Ссылка'];
				if(strstr($o['Ссылка'],'http://')){//если внешняя
					$href=$o['Ссылка'];
				}elseif(strpos($o['Ссылка'],'/')==0){ //если внутренняя
					if(strstr($o['Ссылка'], '.php')){
						$href=(strstr($o['Ссылка'],'?') ? $o['Ссылка'].'&' : $o['Ссылка']);
					}else{
						$href='/'.$api->lang.''.$o['Ссылка'];
					}
				}
				$out[] = '<li><a '.$api->getLink($o['id']).''.($o['В новом окне'] == 1 ? ' target="_blank"':'').'>'.htmlspecialchars_decode($o['Название']).'</a>';	
				$out[] = getSubm($o['id']);
				$out[] = '</li>';		
			}
			else if (($o['class_id'] == 1) || ($o['class_id'] == 3)) 
			{
				$out[] ='<li><a '.$api->getLink($o['id']).'>'.htmlspecialchars_decode($o['Название']).'</a>';
				$out[] = getSubm($o['id']);
				$out[] = '</li>';
			}
		}
		
		$out[] = '</ul>';	
		echo join("\n", $out);
	}
}
// echo '<ul><li><a href="/'.$api->lang.'/news/"><!--o:132--></a></li></ul>';
	// $out = array();
	// if (($obj = $api->objects->getObject($catalog)) && ($ll = $api->objects->getFullObjectsList($catalog))){
		// $out[] = '<h1>'.htmlspecialchars_decode($obj['name']).'</h1><ul>';
		// foreach ($ll as $o){
			// $out[] = '<li><a href="/'.$api->lang.'/cat/'.$o['id'].'/">'.$o['Название'].'</a>';
			// $out[] = getSubm($o['id']);
			// $out[] = '</li>';
		// }
		// $out[] = '</ul>';
		// echo join("\n", $out);
	// }
	
?>