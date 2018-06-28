<?
if (isset($_REQUEST['query'])) {
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
header("Content-Type: text/html; charset=utf-8");
include('cms/public/api.php');
} else
include('cms/public/api.php');

$vars = array(
	'ru' => array(
		'Поиск по сайту' => 'Поиск по сайту',
		'Укажите слово для поиска.' => 'Укажите слово для поиска.',
		'Поиск не дал результатов' => 'Поиск не дал результатов',
	),
	'en' => array(
		'Поиск по сайту' => 'Search in site',
		'Укажите слово для поиска.' => 'Enter keywords to search.',
		'Поиск не дал результатов' => 'Your search returned no results',
	),
	'kz' => array(
		'Поиск по сайту' => 'Сайт ішінде іздеу',
		'Укажите слово для поиска.' => 'Ізделінетін мәтінді енгізіңіз',
		'Поиск не дал результатов' => 'Сіз енгізген мәтін сайттан табылған жоқ',
	),
);

if (!isset($_REQUEST['query'])) $api->header(array('page-title'=>'<!--object:[136][18]-->'));
if (!isset($_REQUEST['query'])) {
?>
<style>
.search-list{
	padding-bottom: 0 !important;
	list-style-type:none;
}

.search-list li{
	position:relative;
	margin:15px 0px;
	padding: 0 !important;
}

.search-list li .num{
	position:absolute;
	top:0px;
	left:-30px;
}
</style>
<?
}
$root_id = 15; # Корневой каталог
$cat_class_id = 19; # Класс каталога
$limit = 2; # Лимит, можно вообще не менять
$mass = array();

function getClassId($id){
	global $api;
	global $mass;
	global $limit;
	global $cat_class_id;
	if ($list = $api->objects->getObjectsList($id, $limit)){
		foreach ($list as $o){
			getClassId($o['id']);
			if ($o['class_id'] != $cat_class_id) $mass[] = $o['class_id'];
		}
	}
}

getClassId($root_id);
$mass = array_unique($mass);
$cfg = array(
	1=>array('title'=>'', '', 'name'=>'Название', 'anons'=>'Текст'),
	3=>array('title'=>'', '', 'name'=>'Название', 'anons'=>'Текст'),
	8=>array('title'=>'', '', 'name'=>'Название', 'anons'=>'Анонс'),
	24=>array('title'=>'', '', 'name'=>'Название', 'anons'=>'Анонс'),
	19=>array('title'=>'', '', 'name'=>'Название', 'anons'=>'Анонс'),
	2=>array('title'=>'', '', 'name'=>'Название', 'anons'=>'Анонс'),
);

foreach ($mass as $m){
	$cfg[$m] = array('title'=>'', 'file'=>'/'.$api->lang.'/catalog/#CAT#/#ID#/', 'name'=>'Название', 'anons'=>'Описание');
}

function getElements($where){
	global $start, $onepage, $what, $api;
	
	$end = $onepage;
	//echo $end;
	$search = array();
	foreach($api->db->select('fields', "WHERE `class_id`='".$where."' AND `type` IN('html','text','textarea')", "id") as $field_id){
		$search[]= "f.field_".$field_id." LIKE '%".$what."%'"; 
	}
	return $api->db->select('objects as o', "LEFT JOIN class_".$where." as f ON o.id=f.object_id WHERE f.lang='".$api->lang."' AND o.class_id='".$where."' AND o.active='1'".($search?' AND ('.join(' OR ', $search).')':'').' GROUP BY o.id LIMIT '.$start.','.$end, "o.id as id, o.name as name, o.class_id as class_id");
}

function countElements($where){
	global $what, $api;

	$search = array();
	foreach($api->db->select('fields', "WHERE `class_id`='".$where."' AND `type` IN('html','text','textarea')", "id") as $field_id){
		$search[]= "f.field_".$field_id." LIKE '%".$what."%'"; 
	}

	return $api->db->count('objects as o', "LEFT JOIN class_".$where." as f ON o.id=f.object_id WHERE f.lang='".$api->lang."' AND o.class_id='".$where."' AND o.active='1'".($search?' AND ('.join(' OR ', $search).')':''));
}

#GET CURRENT PAGE
if(!isset($_REQUEST['pg']) || !is_numeric($pg = $_REQUEST['pg'])) $pg = 1;
$onepage = 30;
###

if((empty($_REQUEST['what']) || !($what = $api->db->prepare( trim($_REQUEST['what']) ))) && !isset($_REQUEST['query']) ){
	echo $vars[$api->lang]['Укажите слово для поиска.'];
	$api->footer();
	exit();
}

if (isset($_REQUEST['query']) && ($_REQUEST['query'] != '')){
	$query = $what = $_REQUEST['query'];
	$counter='0';
	echo "{";
	echo "query:'$query',";
	echo "suggestions:[";
	$i = ($pg-1)*$onepage+1;
	$start = ($pg-1)*$onepage;
	$total_count = 0;
	$name = array();
	foreach($cfg as $id=>$c){
		if(!$onepage) break;
		$html = array();	
		$count = countElements($id);
		$total_count+=$count;
		if(!!$select = getElements($id)){
			$onepage-=count($select);
			$start = 0;
			
			foreach($select as $o){
				$anons = '';
				$o = $api->objects->getFullObject($o['id'], false);
				if( !empty($cfg[$o['class_id']]['anons']) ){
					$anons = strip_tags($o[$cfg[$o['class_id']]['anons']]);
					$anons = mb_strlen($anons, 'UTF-8')>200?mb_substr($anons, 0, 200, 'UTF-8').'...':$anons;
					$anons = str_replace($what, "<strong>".$what."</strong>", $anons);
				}
				
				// $akesi = $api->objects->getObject($o['head']);
				// $str = str_replace("#CAT#", $api->t($akesi['id']), $c['file']);
				// $str = str_replace("#ID#", $api->t($o['id']), $str);
				
				$name[]=strip_tags(html_entity_decode($o[$cfg[$o['class_id']]['name']]));
				
				// $html[]='<li><div class="num">'.($i++).'.</div><a href="'.$str.'">'.$o[$cfg[$o['class_id']]['name']].'</a><div class="anons">'.$anons.'</div></li>';
			}
		}else $start-=$count;
	}
	$name = array_unique($name);
	foreach ($name as $n){
		$counter++;
		if ($counter > 1) {
		echo ",";
		}
		echo "'$n'";
	}
	echo "],}";
} else {
	$i = ($pg-1)*$onepage+1;
	$start = ($pg-1)*$onepage;
	$total_count = 0;
	echo '<div style="height: 5px;"></div>';
	foreach($cfg as $id=>$c){
		if(!$onepage) break;
		$html = array('');	
		$count = countElements($id);
		$total_count+=$count;
		if(!!$select = getElements($id)){
			$onepage-=count($select);
			$start = 0;
			
			$html[]='<ul class="search-list">';
			foreach($select as $o){
				$anons = '';
				$o = $api->objects->getFullObject($o['id'], false);
				if( !empty($cfg[$o['class_id']]['anons']) ){
					@$anons = strip_tags($o[$cfg[$o['class_id']]['anons']]);
					$anons = mb_strlen($anons, 'UTF-8')>200?mb_substr($anons, 0, 200, 'UTF-8').'...':$anons;
					$anons = str_replace($what, "<strong>".$what."</strong>", $anons);
				}
												
				$html[]='<li><div class="num">'.($i++).'.</div><a '.$api->getLink($o['id']).'>'.html_entity_decode($o[$cfg[$o['class_id']]['name']]).'</a><div class="anons">'.$anons.'</div></li>';
			}
			$html[]='</ul>';
			echo join("\n", $html);
		}else $start-=$count;
	}
	if(!$total_count) echo $vars[$api->lang]['Поиск не дал результатов'];
	 $html = array();
	if(!$onepage){
		if($pg>1){ 
			$html[]='&larr; <a href="'.$_SERVER['SCRIPT_NAME'].'?what='.$what.'&pg='.($pg-1).'&lang='.$api->lang.'">'.$api->v('Назад').'</a> &mdash;';
		}
		$html[]='<a href="'.$_SERVER['SCRIPT_NAME'].'?what='.$what.'&pg='.($pg+1).'&lang='.$api->lang.'">'.$api->v('В перёд').'</a> &rarr;';
	}else if($pg>1){ 
		$html[]='&larr; <a href="'.$_SERVER['SCRIPT_NAME'].'?what='.$what.'&pg='.($pg-1).'&lang='.$api->lang.'">'.$api->v('Назад').'</a>';
	}
	echo join("\n", $html); 
}
if (!isset($_REQUEST['query'])) $api->footer();
?>