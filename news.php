<?
# Edited by ShadoW
# 9.09.2010
include('cms/public/api.php');

$object_id 	= 9;			# ID объекта в котором лежат новости
$class_id	= 8;			# ID класса новостей
$onepage	= 15;			# На страницу
$vars = array(
	"ru"=>array(
		"news"=>'Новости',
		"back"=>'Вернуться',
		"noNews"=>'Новостей нет.'
	),
	"en"=>array(
		"news"=>'News',
		"back"=>'Back to news list',
		"noNews"=>'There is no any news yet.'
	)
);
# ЗАГРУЖЕНА НОВОСТЬ
if(isset($_REQUEST['id']) && ($id=$_REQUEST['id']) && ($o = $api->objects->getFullObject($id)) && ($o['class_id']==$class_id))
{
    $galleryHtml = '';

    if($photos = $api->objects->getFullObjectsListByClass($o['id'], 4, "AND o.active='1' ORDER BY o.sort"))
    {
        //echo '<br><h2>'.$vars[$lang]['attachedPhotos'].'</h2>';
        $n=0;
        $out = array();
        foreach($photos as $photo){
            $n++;
            if ($n == 1) { $out[] = '<tr valign="top">'; }
            $out[] = '
		<td id="photo-'.$photo['id'].'" align="center">
			<a class="photo" href="'._UPLOADS_.'/'.$photo['Ссылка'].'" rel="photo_group_'.$o['id'].'" title="'.$photo['Название'].'"><img style="padding:3px;border:1px solid #e7e7e7; background-color:#fff;" src="'._IMGR_.'?w=136&h=136&image='._UPLOADS_.'/'.$photo['Ссылка'].'"></a>
			<div>
			<!--smart:{
				id:'.$photo['id'].',
				actions:["edit", "remove"],
				p:{
					remove : "#photo-'.$photo['id'].'"
				}
			}-->
			</div>
		</td>';
            if ($n == 3) { $out[] = '</tr>'; $n = 0; }
        }
        if ($out[sizeof($out)-1] != '</tr>') $out[] = '</tr>';
        $galleryHtml =  '<table id="photos-list" width="100%" cellpadding="7" cellspacing="0">'.join("\n", $out).'</table>';
    }

    $api->header(array('page-title'=>htmlspecialchars($o['Название'])));
	echo '
	<div class="news1" id="news-'.$o['id'].'">
		<div style="margin:10px 0;">'.$api->strings->date($o['Дата']).'</div>
		<div>'.$o['Текст'].'</div>
	</div>
	<!--smart:{
		id : '.$o['id'].',
		actions : ["edit", "remove"],
		p:{
			remove:"#news-'.$o['id'].'"
		}
	}-->
	<br />
	'.$galleryHtml.'
	<br />
	<div>&larr; <a href="/'.$api->lang.'/news/">'.$api->v('Вернуться на уровень выше').'</a></div>';
}
# -----------------------------------------------------------------------
# ЗАГРУЖЕН СПИСОК НОВОСТЕЙ
else 
{
	$api->header(array('page-title'=>'<!--object:[132][18]-->'));
	
	# страницы
	$pages = $api->pages($api->objects->getObjectsCount($object_id, $class_id, "AND o.active='1'"), $onepage, 5, array("lang"=>$api->lang),"/".$api->lang."/news/pg/#pg#/", $api->lang);
	
	# получаем страницу
	if($news = $api->objects->getFullObjectsListByClass($object_id, $class_id, "AND o.active='1' ORDER BY c.field_19 DESC LIMIT ".$pages['start'].", $onepage"))
	{
		$html = array();
		foreach($news as $n)
		{
			if($n['Название'])
			{
				$html[]='
				<div class="news1" id="news-'.$n['id'].'">
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
		title : "новостей",
		actions : ["add"],
		p:{
			add:['.$class_id.']
		},
		info:{
			add : "добавить&nbsp;новость"
		}
	}-->';
}

$api->footer();
?>