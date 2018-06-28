<?
# Edited by ShadoW
# 9.09.2010
include('cms/public/api.php');

$object_id 	= 119;			# ID объекта в котором лежат новости
$class_id	= 24;			# ID класса новостей
$onepage	= 15;			# На страницу
$vars = array(
	"ru"=>array(
		"news"=>'Статьи',
		"back"=>'Вернуться',
		"noNews"=>'Статьей нет.'
	),
	"en"=>array(
		"news"=>'Articles',
		"back"=>'Back to news list',
		"noNews"=>'There is no any articles yet.'
	),
	"kz"=>array(
		"news"=>'Макалалар',
		"back"=>'Iлгерi',
		"noNews"=>'There is no any articles yet.'
	)
);
# ЗАГРУЖЕНА НОВОСТЬ
if(isset($_REQUEST['id']) && ($id=$_REQUEST['id']) && ($o = $api->objects->getFullObject($id)) && ($o['class_id']==$class_id))
{
	$api->header(array('page-title'=>htmlspecialchars($o['Название'])));
	echo '
	<div class="news" id="news-'.$o['id'].'">
		<div style="margin:10px 0;">'.$api->strings->date($o['Дата']).'</div>
		<div>'.$o['Текст'].'</div>
	</div>
	<!--smart:{
		id : '.$o['id'].',
		actions : ["edit"],
		p:{
			remove:"#news-'.$o['id'].'"
		}
	}-->
	<br />
	<div>&larr; <a href="/'.$api->lang.'/articles/">'.$api->v('Вернуться на уровень выше').'</a></div>';
}
# -----------------------------------------------------------------------
# ЗАГРУЖЕН СПИСОК НОВОСТЕЙ
else 
{
	$api->header(array('page-title'=>'<!--object:[124][18]-->'));
	
	# страницы
	$pages = $api->pages($api->objects->getObjectsCount($object_id, $class_id, "AND o.active='1'"), $onepage, 5, array("lang"=>$api->lang),"/".$api->lang."/articles/pg/#pg#/");
	
	# получаем страницу
	if($news = $api->objects->getFullObjectsListByClass($object_id, $class_id, "AND o.active='1' ORDER BY o.sort DESC LIMIT ".$pages['start'].", $onepage"))
	{
		$html = array();
		foreach($news as $n)
		{
			if($n['Название'])
			{
				$html[]='
				<div class="news" id="news-'.$n['id'].'">
					<div class="date">'.$api->strings->date($n['Дата']).'</div>
					<div class="name"><a '.$api->getLink($n['id']).'>'.$n['Анонс'].'</a></div>
				</div>';
				
			} else $html[]='<div class="news"><font color="red">Языковая версия не заполнена.</font></div>';
			
			$html[]= '
				<!--smart:{
					id : '.$n['id'].',
					actions : ["edit", "remove"],
					p:{
						remove:"#news-'.$n['id'].'"
					}
				}-->
				<br>';
		}
		
		# страницы
		$html[]='<div style="margin-top:20px;">'.$pages['html'].'</div>';
					
		echo join("\n", $html);
	}
	
	# новостей нет
	else echo $vars[$api->lang]['noNews'];
	
	echo '
	<!--smart:{
		id : '.$object_id.',
		title : "статьей",
		actions : ["add"],
		p:{
			add:['.$class_id.']
		},
		info:{
			add : "добавить&nbsp;статью"
		}
	}-->';
}

$api->footer();
?>