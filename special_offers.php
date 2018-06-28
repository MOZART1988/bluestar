<?
include('cms/public/api.php');
$api->header(array( 'page-title' => 'Спец предложения' ));

$offersSectionId = 38;
$offersId = 39;
$root_id = 321;
//$root_id = 5488;
$body = '';
$title = 'Специальные предложения';
$minTitle = 'Легковые автомобили Mercedes-Benz';
$minTitle = '';
function getLiLeftMenu($o,$subO) {
    global $api;
    $res = array();

                    $class = '';
                    if (@$_GET['offer'] == $subO['id'])
                        $class = 'class="active"';

                    $res[] = '<li '.$class.'><a href="/'.$api->lang.'/'.$api->section->sectionName.'/special_offers/section/'.$o['id'].'/'.$subO['id'].'/">'.$subO['Название'].'</a></li>';
                
    return $res;
}
function getLeftMenu() {

    global $api, $offersSectionId, $offersId;

    $out = array();
    if ($list = $api->objects->getFullObjectsListByCLass($api->section->sectionId, $offersSectionId)) {
        foreach ($list as $o) {
            if ($subList = $api->objects->getFullObjectsList($o['id'])) {
                foreach ($subList as $subO) {

                   if($subO['class_id']==$offersId)
                    $out = array_merge($out,getLiLeftMenu($o,$subO));
                else if($subO['class_id']==$offersSectionId) {

                    if($sub2 = $api->objects->getFullObjectsListByClass($subO['id'],$offersId))
                     foreach ($sub2 as $sub) {

                   if($sub['class_id']==$offersId)
                    $out = array_merge($out,getLiLeftMenu($o,$sub));
            }

                }
                }
            }
        }
        return '
            <ul class="sidebar_menu accord_menu">
                <li '.($_SERVER['REQUEST_URI'] == '/'.$api->lang.'/'.$api->section->sectionName.'/special_offers/'?'class="active"':'').'>
                    <a href="/'.$api->lang.'/'.$api->section->sectionName.'/special_offers/">Специальные предложения</a>
                </li>
                '.join("\n", $out).'
            </ul>';
    }
}

# ---------- ПОДРОБНЫЙ ПРОСМОТР СПЕЦ. ПРЕДЛОЖЕНИЯ --------
if (@$_GET['offer']) {
//    echo $api->section->sectionId;
    $offerObj = $api->objects->getFullObject($_GET['offer']);

    $galleryHtml = '';

    if($photos = $api->objects->getFullObjectsListByClass($offerObj['id'], 4, "AND o.active='1' ORDER BY o.sort"))
    {
        //echo '<br><h2>'.$vars[$lang]['attachedPhotos'].'</h2>';
        $n=0;
        $out = array();
        foreach($photos as $photo){
            $n++;
            if ($n == 1) { $out[] = '<tr valign="top">'; }
            $out[] = '
		<td id="photo-'.$photo['id'].'" align="center">
			<a class="photo" href="'._UPLOADS_.'/'.$photo['Ссылка'].'" rel="photo_group_'.$offerObj['id'].'" title="'.$photo['Название'].'"><img style="padding:3px;border:1px solid #e7e7e7; background-color:#fff;" src="'._IMGR_.'?w=136&h=136&image='._UPLOADS_.'/'.$photo['Ссылка'].'"></a>
			<div>
			<!--smart:{
				id:'.$photo['id'].',
				actions:["edit", "remove"],
				p:{
					remove : "#photo-'.$photo['id'].'",
				},
                info:{
                    add : "добавить&nbsp;фото"
                }
			}-->
			</div>
		</td>';
            if ($n == 3) { $out[] = '</tr>'; $n = 0; }
        }
        if ($out[sizeof($out)-1] != '</tr>') $out[] = '</tr>';
        $galleryHtml =  '<table id="photos-list" width="100%" cellpadding="7" cellspacing="0">'.join("\n", $out).'</table>';
    }

    $title = $offerObj['Название'];
    $body = '
        <div class="simple_text">
            '.$offerObj['Текст'].'
            <!--smart:{
                id:'.$offerObj['id'].',
                title:"",
                p:{
                    add: ["4"]
                },
                actions:["edit","add"]
            }-->
            <br />
	        '.$galleryHtml.'
        </div>';

}

# ---------- СПИСОК СПЕЦ ПРЕДЛОЖЕНИЙ --------------------
else {
     if ($offersSectionsList = $api->objects->getFullObjectsListByCLass($root_id, $offersSectionId)) {

        $offerSectionDivs = array();
        $offerSectionLies = array();

        $offersI = 1;
        foreach ($offersSectionsList as $oSo) {

            $offers = array();
            if ($offersList = $api->objects->getFullObjectsListByCLass($oSo['id'], $offersId)) {
                foreach ($offersList as $oo) {

                    $offersHref = 'href="/'.$api->lang.'/'.$api->section->sectionName.'/service_offers/section/'.$oSo['id'].'/'.$oo['id'].'/"';
                    $smart = '<!--smart:{ id:'.$oo['id'].', title:"", actions:["edit", "remove"] }-->';

                    $offers[] = '
                        <li>
                            <div class="one">
                                <div class="one_lin">
                                    <a '.$offersHref.' class="in2"><span>'.$oo['Название'].'</span></a>
                                </div>
                                <div class="img">
                                    <a '.$offersHref.'>
                                        <img src="'._IMG_.'?w=175&url='._UPLOADS_.'/'.$oo['Рисунок'].'" width="175">
                                    </a>
                                </div>
                                <div class="text">'.$oo['Анонс'].'</div>
                                '.$smart.'
                            </div>
                        </li>';
                }
            }

            $sectionSmart = '
                <!--smart:{
                    id:'.$oSo['id'].',
                    title:"раздела",
                    actions:["edit", "add"],
                    p:{ add: ["'.$offersId.'"] },
                    info: {add:"добавить спец предложение"}
                }-->';
            $offerSectionLies[] = '
                <li>
                    <a href="#type'.$offersI.'" '.($offersI == 1?'class="on"':'').'>
                        '.$oSo['Название'].'
                    </a>
                </li>';
            $offerSectionDivs[] = '
                <div class="type_list '.($offersI == 1?'on':'').'" id="type'.$offersI.'" '.($offersI == 1?'style="display: block;"':'').'>
                    <figure class="model_row">
                        '.$sectionSmart.'
                        <ul>'.join("\n", $offers).'</ul>
                    </figure>
                </div>';

            $offersI++;
        }

        $body = '
            <figure id="tabs">

                <div class="tabs">
                    <ul>'.join("\n", $offerSectionLies).'</ul>
                </div>

                '.join("\n",$offerSectionDivs).'

                <div class="offerta">
                    <!--object:[391][57]-->
                    <!--smart:{ id:391, title:"текста", actions:["edit"] }-->
                </div>
            </figure>';
    }
}

?>

    <div id="page_spec">
        <figure class="page_cols">

            <div class="left_column">
                <div class="wrap">
                    <?=getLeftMenu()?>
                </div>
            </div>

            <div class="right_column">
                <div class="cont">

                    <div class="main_title">
                        <h1><?=$title?></h1>
                        <div class="minitit"><?=$minTitle?></div>
                    </div>

                    <?=$body?>

                    <figure class="social_icons_inner">
                        <?=$api->socIconsMenu()?>
                    </figure>

                </div>
            </div>

        </figure>
    </div>

<?
$api->footer();
?>