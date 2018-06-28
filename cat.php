<?
# ID корневого каталога
$root_id = 15;

# ID класса каталога
$class_id_c = 19;

# ID класса элемента
$class_id = 15;

# Хит продаж, ID поля
$hit_id = 46;

# Элементов на страницу
$per_page = 1;

include('cms/public/api.php');
if (!$root_obj = $api->objects->getObject($root_id)) exit('Указан неверный корневой каталог');

if (@$_GET['id'])
	$r = @$_GET['id'];
elseif (@$_GET['cat'])
	$r = @$_GET['cat'];
else
	$r = @$root_id;
@$r = $api->objects->getFullObject($r);
$api->header(array('page-title'=>((@$r['Название']?@$r['Название']:'<!--object:[126][18]-->'))));

# ФУНЦИЯ ПОЛУЧЕНИЯ КАТАЛОГОВ
function getCatalogs($id){
	global $api;
	global $class_id_c;

	$out = array();
	if ($api->objects->getObject($id)) {
		if ($catalogs = $api->objects->getFullObjectsListByClass($id, $class_id_c)) {
			foreach($catalogs as $k=>$o){
				$out[] = '
				<div class="cat_box" id="cat_'.$o['id'].'_div">
					<a '.$api->getLink($o['id']).'><img src="'._IMGR_.'?w=160&h=120&image='._UPLOADS_.'/'.$o['Изображение'].'" width="160px" height="120px" alt="" /></a>
					<a style="color:#328DC3;" '.$api->getLink($o['id']).'>'.$o['Название'].'</a>
					<!--smart:{
						id : '.$o['id'].',
						title : "подкаталога",
						actions : ["edit","remove"],
						p : {						
							remove : "#cat_'.$o['id'].'_div"
						}
					}-->
				</div>';
			}
		}
		return join("\n", $out);		
	} else {
		return false;	
	}
}


# ФУНКЦИЯ ПОЛУЧЕНИЯ ПРОДУКЦИИ
function getProducts($id)
{
	global $api;
	global $class_id;
	global $per_page;
	$out = array();
	if (!!($obj = $api->objects->getObject($id)) && !!$obj['inside']) {
	
		$out[] = '<div class="cat_products">';
		# страницы
		$pages = $api->pages($api->objects->getObjectsCount($id, $class_id), $per_page, 5, array("lang"=>$api->lang),"/".$api->lang."/catalog/".$api->t($id)."/pg/#pg#/", $api->lang);
		if ($products = $api->objects->getFullObjectsListByClass($id, $class_id, "AND o.active=1 ORDER BY o.sort LIMIT ".$pages['start'].", $per_page")){
			// print_r($products);
			foreach($products as $k=>$o) {
				$parent = $api->objects->getFullObject($o['head']);
				$out[] = '
					<div class="cat_box" id="cat_element_'.$o['id'].'">
						<a '.$api->getLink($o['id']).'><img src="'._IMGR_.'?w=160&h=120&image='._UPLOADS_.'/'.$o['Изображение'].'" width="160px" height="120px" alt="" /></a>
						<h5>'.$o['Название'].'</h5>
						'.$api->v('Цена').': <span>'.number_format($o['Цена'], 0, ''," ").' '.$api->v('тг').'.</span>
						<div class="clear_cat">
							<a class="link_order pie" href="#в корзину" onclick="return buy('.$o['id'].', \''.$o['Название'].'\')">'.$api->v('в корзину').'</a>
							<a '.$api->getLink($o['id']).'><i>'.$api->v('Подробнее').'...</i></a>
						</div>
						<!--smart:{
							id : '.$o['id'].',
							title : "элемента",
							actions : ["edit", "remove"],
							p : {
								remove : "#cat_element_'.$o['id'].'"
							}
						}-->
					</div>
				';
			}		
			
			$out[] = '<div class="navi">'.$pages['html'].'</div>';
			$out[] = '<div style="margin-top: 20px;">&larr; <a '.$api->getLink($obj['head']).'>'.$api->v('Вернуться на уровень выше').'</a></div>';
		}
		$out[] = '</div>';
		return join("\n", $out);
	} else {
	  return false;	
	}
}

# -----------------------------------------------------------------------------------
# ПРОДУКЦИЯ

if (!empty($_GET['id']) && ($obj = $api->objects->getFullObject($_GET['id'])) ){
	echo '
	<div>
				'.(!empty($obj['Цена'])?'<div style="margin-bottom: 10px; color: red;">'.$api->v('Цена').': <strong>'.number_format($obj['Цена'], 0, ''," ").'</strong> '.$api->v('тг').'.</div>':'').'
				<table border="0" cellpadding="0" cellspacing="0" width="269" style="box-shadow: 0px 0px 3px #bfb0b9; float:left; margin-right: 20px;	-moz-box-shadow: 0px 0px 3px #bfb0b9;     -webkit-box-shadow: 0px 0px 3px #bfb0b9; border: #AAA solid 1px; margin-bottom: 15px;">
					<tr><td valign="middle" align="center"><a href="'._UPLOADS_.'/'.$obj['Изображение'].'" class="fancy largeImg"><img style="display: block;" id="largeImg" alt="" src="'._IMG_.'?w=269&url='._UPLOADS_.'/'.$obj['Изображение'].'" /></a></td></tr>
				</table>
				'.$obj['Описание'].'
				
				<div class="clear"></div>
				';
				
				# ПОЛУЧАЕМ ДОПОЛНИТЕЛЬНЫЕ ИЗОБРАЖЕНИЯ
				$now_lang = $api->lang;
				$api->lang = 'ru';
				if ($dop_imgs = $api->objects->getFullObjectsListByClass($obj['id'], 4))
				{
					echo '<span class="thumbs">';
					echo '<a href="'._IMG_.'?w=269&url='._UPLOADS_.'/'.$obj['Изображение'].'" orig="'._UPLOADS_.'/'.$obj['Изображение'].'" rel="dop_photo_group"><img height="75" width="75" src="'._IMGR_.'?w=75&h=75&image='._UPLOADS_.'/'.$obj['Изображение'].'" /></a>';
					foreach($dop_imgs as $im)
					{
						echo '
							<a id="fancy_img_'.$im['id'].'" href="'._IMG_.'?w=269&url='._UPLOADS_.'/'.$im['Ссылка'].'" orig="'._UPLOADS_.'/'.$im['Ссылка'].'" rel="dop_photo_group"><img height="75" width="75" src="'._IMGR_.'?w=75&h=75&image='._UPLOADS_.'/'.$im['Ссылка'].'" />
							<!--smart:{
									id : '.$im['id'].',
									title : "",
									actions : ["edit", "remove"],
									p: {
										remove:"#fancy_img_'.$im['id'].'"
									}
							}-->
							</a>
							
							';
					}
					echo '</span>';
				}
				$api->lang = $now_lang;
				echo '
				<!--smart:{
					id : '.$obj['id'].',
						title : "товара",
						actions : ["edit"],
						info: {
							edit:"редактировать&nbsp;товар",
						}, 
						css : {
							marginTop:20
						}
				}-->
	</div>
	<div style="clear: both;"></div>
	<div>&larr; <a '.$api->getLink($_GET['cat']).'>'.$api->v('Вернуться на уровень выше').'</a></div>';
	#СОПУТСТВУЮЩИЕ ТОВАРЫ
	// if ($products = $api->objects->getFullObjectsListByClass($_REQUEST['cat'], $class_id, "AND o.active=1 AND o.id <> ".$obj['id']." ORDER BY RAND() LIMIT 6")){
		// echo '<h1 class="page-title"><!--o:219--></h1><div class="cat_block"><div class="list_block">';
			// foreach($products as $k=>$o){
				// echo '
					// <div class="cat_box" id="cat_element_'.$o['id'].'">
						// <a '.$api->getLink($o['id']).'><img src="'._IMGR_.'?w=160&h=120&image='._UPLOADS_.'/'.$o['Изображение'].'" width="160px" height="120px" alt="" /></a>
						// <h5>'.$o['Название'].'</h5>
						// '.$api->v('Цена').': <span>'.number_format($o['Цена'], 0, ''," ").' '.$api->v('тг').'.</span>
						// <div class="clear_cat">
							// <a class="link_order pie" href="">'.$api->v('в корзину').'</a>
							// <a '.$api->getLink($o['id']).'><i>'.$api->v('Подробнее').'...</i></a>
						// </div>
						// <!--smart:{
							// id : '.$o['id'].',
							// title : "элемента",
							// actions : ["edit", "remove"],
							// p : {
								// remove : "#cat_element_'.$o['id'].'"
							// }
						// }-->
					// </div>
				// ';
			// }
		// echo '</div></div>';
	// }
}

# -----------------------------------------------------------------------------------
# НОВИНКИ
elseif (!empty($_GET['new'])){
	$mass = array();
	function getProdNewOrHit($id, $flag = ''){
		global $api;
		global $mass;
		global $class_id_c;
		if ($list = $api->objects->getFullObjectsList($id)){
			foreach ($list as $o){
				if (($o['class_id'] != $class_id_c) && ($o['class_id'] != '0') && (@$o[$flag] == 1)){
					$mass[] = $o['id'];
				} 
				else
				getProdNewOrHit($o['id'], $flag);
			}
		}
	}
	getProdNewOrHit($root_id, $flag = 'Новинка');
	foreach ($mass as $m){
		if ($o = $api->objects->getFullObject($m)){
			$parent = $api->objects->getObject($o['head']);
			$out[] = '
			<div class="cat_box" id="cat_element_'.$o['id'].'">
				<a '.$this->t($o['id']).'><img src="'._IMGR_.'?w=160&h=120&image='._UPLOADS_.'/'.$o['Изображение'].'" width="160px" height="120px" alt="" /></a>
				<h5>'.$o['Название'].'</h5>
				'.$api->v('Цена').': <span>'.number_format($o['Цена'], 0, ''," ").' '.$api->v('тг').'.</span>
				<div class="clear_cat">
					<a class="link_order pie" href="#в корзину" onclick="return buy('.$o['id'].', \''.$o['Название'].'\')">'.$api->v('в корзину').'</a>
					<a '.$this->t($o['id']).'><i>'.$api->v('Подробнее').'...</i></a>
				</div>
				<!--smart:{
					id : '.$o['id'].',
					title : "элемента",
					actions : ["edit", "remove"],
					p : {
						remove : "#cat_element_'.$o['id'].'"
					}
				}-->
			</div>
			';
		}
	}
	// $out[] = '<div class="navi">'.$pages['html'].'</div>';
	$out[] = '<div class="clear"></div><div class="back_link" style="margin-right: 10px;">&larr; <a href="javascript:history.back()">'.$api->v('Назад').'</a></div><div class="clear" style="margin-bottom: 20px;"></div>';
	echo join("\n", $out);
}
# ХИТЫ
elseif (!empty($_GET['hit'])){
	$mass = array();
	function getProdNewOrHit($id, $flag = ''){
		global $api;
		global $mass;
		global $class_id_c;
		if ($list = $api->objects->getFullObjectsList($id)){
			foreach ($list as $o){
				if (($o['class_id'] != $class_id_c) && ($o['class_id'] != '0') && (@$o[$flag] == 1)){
					$mass[] = $o['id'];
				} 
				else
				getProdNewOrHit($o['id'], $flag);
			}
		}
	}
	getProdNewOrHit($root_id, $flag = 'Хит');
	foreach ($mass as $m){
		if ($o = $api->objects->getFullObject($m)){
			$parent = $api->objects->getObject($o['head']);
			$out[] = '
			<div class="cat_box" id="cat_element_'.$o['id'].'">
				<a '.$this->t($o['id']).'><img src="'._IMGR_.'?w=160&h=120&image='._UPLOADS_.'/'.$o['Изображение'].'" width="160px" height="120px" alt="" /></a>
				<h5>'.$o['Название'].'</h5>
				'.$api->v('Цена').': <span>'.number_format($o['Цена'], 0, ''," ").' '.$api->v('тг').'.</span>
				<div class="clear_cat">
					<a class="link_order pie" href="#в корзину" onclick="return buy('.$o['id'].', \''.$o['Название'].'\')">'.$api->v('в корзину').'</a>
					<a '.$this->t($o['id']).'><i>'.$api->v('Подробнее').'...</i></a>
				</div>
				<!--smart:{
					id : '.$o['id'].',
					title : "элемента",
					actions : ["edit", "remove"],
					p : {
						remove : "#cat_element_'.$o['id'].'"
					}
				}-->
			</div>
			';
		}
	}
	// $out[] = '<div class="navi">'.$pages['html'].'</div>';
	$out[] = '<div class="clear"></div><div class="back_link" style="margin-right: 10px;">&larr; <a href="javascript:history.back()">'.$api->v('Назад').'</a></div><div class="clear" style="margin-bottom: 20px;"></div>';
	echo join("\n", $out);
}
# КАТАЛОГ

elseif (!empty($_GET['cat']) && ($obj = $api->objects->getObject($_GET['cat'])) && ($obj['class_id'] == $class_id_c))
{
	@$cats = getCatalogs($_GET['cat']);
	@$items = getProducts($_GET['cat']);
	if (!$cats && !$items){
		echo '<div style="margin:30px 0px;">Извините, в данном каталоге нет продукции!</div>';
	} else {	
		# СПИСОК КАТАЛОГОВ
		echo !empty($cats)?$cats:'';
	
		# СПИСОК ПРОДУКЦИИ
		echo !empty($items)?$items:'';
	}
	echo '
	<div style="clear: both;"></div>
	<!--smart:{
		id : '.$obj['id'].',
		title : "текущего&nbsp;каталога",
		info: {
			edit:"редактировать&nbsp;каталог",
			add:"добавить&nbsp;подкаталог&nbsp;или&nbsp;элемент"
		},
		actions : ["edit", "add", "remove"],
		p : {
			add : ['.$class_id_c.', '.$class_id.']
		},
		css:{
			marginTop:10
		}
	}-->';
}

# -----------------------------------------------------------------------------------
# СПИСОК КАТАЛОГОВ
else {
	@$cats = getCatalogs($root_id);
	@$items = getProducts($root_id);
	if ( !$cats && !$items ) {
		echo '<div style="margin:30px 0px;">Извините, в данном каталоге нет продукции!</div>';
	} else {
		# СПИСОК КАТАЛОГОВ
		echo !empty($cats)?$cats:'';
	
		# СПИСОК ПРОДУКЦИИ
		echo !empty($items)?$items:'';
	}
	echo '
	<div style="clear: both;"></div>
	<!--smart:{
		id : '.$root_id.',
		title : "текущего&nbsp;каталога",
		info: {
			add:"добавить&nbsp;подкаталог&nbsp;или&nbsp;элемент"
		},
		actions : ["add"],
		p : {
			add : ['.$class_id_c.', '.$class_id.']
		},
		css:{
			marginTop:10
		}
	}-->';
}
# -----------------------------------------------------------------------------------
$api->footer();
?>