<?
include('cms/public/api.php');

$modelId = $api->db->select("class_52","WHERE field_169='".mysql_real_escape_string(urldecode($_GET['id']))."' LIMIT 1", "object_id");

if(!$modelId){
    die('Нет данных по модели. <a href="/'.$api->lang.'/'.$api->section->sectionName.'/models/">Список моделей</a>');
    //header('Location: /'.$api->lang.'/'.$api->section->sectionName.'/models/');
}

$model = $api->objects->getFullObject($modelId);

$title = '';
$html = '';
$leftMenu = '';

$bannerId = 51;
$textId = 3;
$tableId = 49;
$minAnonsId = 50;

$leftMenuClassId = 57;
$slideClassId = 58;
$topMenuClassId = 59;
$accordionClass = 64;
$accordionBlockClass = 66;
$snoskaClass = 65;
$tabsBlockClass = 67;
$tabsElementClass = 68;
$tabsMenuClass = 69;
$linkClass = 2;

$mainPage = 0;
$o = null;
//$o = $api->objects->getObjectsList($modelId,1);
$leftMenuHead = $api->objects->getObjectsListByClass($modelId, $leftMenuClassId, "AND o.active='1' ORDER BY o.sort LIMIT 1");
if($leftMenuHead){
    $o = $api->objects->getObjectsList($leftMenuHead['id'],1);
}

$sliders = array();
$sliderHead = $api->objects->getObjectsListByClass($modelId, $slideClassId, "AND o.active='1' ORDER BY o.sort LIMIT 1");
if($sliderHead){
    $sliders = $api->objects->getFullObjectsList($sliderHead['id']);
}


if(!$o){
    die('Нет данных по модели. <a href="/'.$api->lang.'/'.$api->section->sectionName.'/models/">Список моделей</a>');
    //header('Location: /'.$api->lang.'/'.$api->section->sectionName.'/models/');
}

$topMenuHead = $api->objects->getObjectsListByClass($modelId, $topMenuClassId, "AND o.active='1' ORDER BY o.sort LIMIT 1");
if($topMenuHead){
    $topMenus = $api->objects->getFullObjectsList($topMenuHead['id']);
}

$topMenusHtml = '';

if(isset($topMenus) && is_array($topMenus)){
    foreach($topMenus as $menu){
        $topMenusHtml .= '<a class="link" '.$api->getLink($menu).'>'.$menu['Название'].'</a>';
    }
}




if(empty($_GET['pageId'])){
    $_GET['pageId'] = $o['id'];
    $pageObj = $model;
    $mainPage = 1;
}else{
    $pageObj = $api->objects->getFullObject($_GET['pageId']);
}

if(empty($_GET['pageSectionID'])){
    $_GET['pageSectionID'] = $leftMenuHead['id'];
}

$tabMenuHtml = '';

$tabMenuHead = $api->objects->getFullObjectsListByClass($pageObj['id'], $tabsMenuClass, "AND o.active='1' ORDER BY o.sort LIMIT 1");

if(!$tabMenuHead){
    $tabMenuHead = $api->objects->getFullObjectsListByClass($pageObj['head'], $tabsMenuClass, "AND o.active='1' ORDER BY o.sort LIMIT 1");
}

if($tabMenuHead){

    $tabMenus = $api->objects->getFullObjectsListByClass($tabMenuHead['id'], $linkClass, "AND o.active='1' ORDER BY o.sort");
	$last = $api->objects->last;
    $tabMenuHtml = '<div class="block simple_text mb clearfix"><article class="tabpage"><ul class="tab_row2">';
    foreach($tabMenus as $tabMenu){

        $tabMenuHtml .= '<li><a '.($api->selected($tabMenu['id'], $last)?'class="on"':'').' '.$api->getLink($tabMenu).'>'.$tabMenu['Название'].'</a>&nbsp;</li>';
    }
    $tabMenuHtml .= '</ul></article></div>';
}

    //$pageObj = $model;

    $title = $pageObj['Название'];
    if (!empty($pageObj['Название (длинное)']))
        $title = $pageObj['Название (длинное)'];

    $html = '
        <!--smart:{
			id:'.$pageObj['id'].',
			title:"страницы",
			actions:["edit", "add"],
			p:{
				add: ["'.$bannerId.'", "'.$textId.'", "'.$minAnonsId.'", "'.$tableId.'", "'.$accordionBlockClass.'", "'.$snoskaClass.'", "'.$tabsBlockClass.'"]
			}
		}-->
		';

    # -------- ДОПОЛНИТЕЛЬНЫЕ ЭЛЕМЕНТЫ СТРАНИЦЫ ----------
    if ($addObjectsList = $api->objects->getFullObjectsList($pageObj['id'])) {
        $addObjects = array();
        $snoskaObjects = array();
        foreach ($addObjectsList as $addObj) {

            # ------------ Если Текст --------
            if ($addObj['class_id'] == $textId) {
                $addObjects[] = '
					<div class="block">
						'.$addObj['Текст'].'
						<!--smart:{ id:'.$addObj['id'].', title:"текста", actions:["edit", "remove"] }-->
					</div>';
                continue;
            }

            # ------------ Если Баннер --------
            if ($addObj['class_id'] == $bannerId) {
                $addObjects[] = '
					<div class="block">
						<article class="fullimg">
							<img src="'._IMG_.'?w=720&url='._UPLOADS_.'/'.$addObj['Рисунок'].'" width="720">
						</article>
					</div>
					<!--smart:{ id:'.$addObj['id'].', title:"банера", actions:["edit", "remove"] }-->';
                continue;
            }

            # ------------ Если таблица --------
            if ($addObj['class_id'] == $tableId) {
                $addObjects[] = $addObj['Содержание'].'<!--smart:{ id:'.$addObj['id'].', title:"элемента", actions:["edit", "remove"] }-->';
                continue;
            }

            # ------------ Если миниАнонс --------
            if ($addObj['class_id'] == $minAnonsId) {

                $minAnonsImg = '';
                if (!empty($addObj['Рисунок']))
                    $minAnonsImg = '
						<div class="img">
							<a href="'._UPLOADS_.'/'.$addObj['Рисунок'].'" class="fancy">
								<img src="'._IMG_.'?w=175&url='._UPLOADS_.'/'.$addObj['Рисунок'].'" width="175">
							</a>
						</div>';

                $addObjects[] = '
					<article class="minianons">
						<div class="one">
							'.$minAnonsImg.'
							<div class="desc">
								<div class="tit">'.$addObj['Название'].'</div>
								<div class="text">'.$addObj['Анонс'].'</div>
								'.$addObj['Ссылки'].'
							</div>
						</div>
						<!--smart:{ id:'.$addObj['id'].', title:"элемента", actions:["edit", "remove"] }-->
					</article>';
                continue;
            }


            # ------------ Если аккордеон --------
            if ($addObj['class_id'] == $accordionBlockClass) {

                $elements = $api->objects->getFullObjectsListByClass($addObj['id'], $accordionClass, "AND o.active='1' ORDER BY o.sort");


                $accordionObjects = array();

                foreach($elements as $element){
                    $smart = '
                <!--smart:{
                    id:'.$element['id'].',
                    title:"элемента",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                    $accordionObjects[] = '<h3>'.$element['Название'].'</h3>
								  <div>
								    '.$smart.$element['Текст'].'
								  </div>';
                }

                $smart = '
                <!--smart:{
                    id:'.$addObj['id'].',
                    title:"секции",
                    actions:["add","edit","remove"],
                    p:{add: ["'.$accordionClass.'"]}
                }-->
                ';

                $addObjects[] = $smart.'<div class="accordion">'.join("\n", $accordionObjects).'</div>';

                continue;
            }

            # ------------ Если вкладки --------
            if ($addObj['class_id'] == $tabsBlockClass) {

                $elements = $api->objects->getFullObjectsListByClass($addObj['id'], $tabsElementClass, "AND o.active='1' ORDER BY o.sort");


                $tabs = array();

                $tabsContents = array();

                $iCount = 1;
                foreach($elements as $element){
                    $smart = '
                <!--smart:{
                    id:'.$element['id'].',
                    title:"элемента",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                    $tabs[] = '<li><a href="#tab'.$addObj['id'].'_'.$iCount.'" '.($iCount==1 ? 'class="on"':'').'>'.$element['Название'].'</a></li>';

                    $tabsContents[] = '<div class="type_cont" id="tab'.$addObj['id'].'_'.$iCount.'" '.($iCount==1 ? 'style="display:block"':'').'><div class="text">'.$element['Текст'].$smart.'</div></div>';
                    $iCount++;
                }

                $smart = '
                <!--smart:{
                    id:'.$addObj['id'].',
                    title:"блока",
                    actions:["add","edit","remove"],
                    p:{add: ["'.$tabsElementClass.'"]}
                }-->
                ';

                $addObjects[] = '<div class="block simple_text">'.$smart.'
											<article class="tabpage">
												<ul class="tab_row">'.join("\n", $tabs).'</ul>'.
                                                join("\n", $tabsContents).'
										    </article>
											<!-- /tabpage -->
										</div>';

                continue;
            }


            # ------------ Если сноска --------
            if ($addObj['class_id'] == $snoskaClass) {

                $smart = '
                <!--smart:{
                    id:'.$addObj['id'].',
                    title:"блока",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                $snoskaObjects[] = '<div class="snos">
								    '.$smart.$addObj['Текст'].'
								  </div>';
                continue;
            }

            # ------------ Если доп фото --------

            if ($addObj['class_id'] == 4) {
                $obj = $api->objects->getFullObject($addObj['id']);
                $addObjects[] =
                    '<div class="img" style="float: left; margin: 10px;">
                        <a href="'._UPLOADS_.'/'.$addObj['Ссылка'].'" class="fancy" rel="gallery-truck">
                            <img src="'._IMGR_.'?w=200&h=150&image='._UPLOADS_.'/'.$addObj['Ссылка'].'">
                        </a>    
                    </div>';
            }

            # ----------- Если доп файл ------

            if ($addObj['class_id'] == 5) {
                $obj = $api->objects->getFullObject($addObj['id']);

                $addObjects[] =
                    '<div class="img" style="float: left; margin: 10px;">
                        <a target="_blank" href="'._UPLOADS_.'/'.$obj['Ссылка'].'">
                            <img src="'._IMGR_.'?w=200&h=150&image='._UPLOADS_.'/'.$obj['Картинка'].'">
                        </a>
                        <a style="display: block;color: #0088cc!important; margin-top: -15px" target="_blank" href="'._UPLOADS_.'/'.$obj['Ссылка'].'">'.$obj['Название'].'</a>
                    </div>';
            }
        }

        if (count($snoskaObjects)){
            $addObjects[] = join("\n", $snoskaObjects);
        }

        $html .= join("\n", $addObjects);
    }

    # ----------- ЛЕВОЕ МЕНЮ -------------

        $leftMenu = '
            <ul class="sidebar_menu accord_menu">
                '.$api->getLeftMenu($_GET['pageSectionID'], '/'.$api->lang.'/'.$api->section->sectionName.'/model/'.urlencode($model['Код модели']).'/'.$_GET['pageSectionID'].'/', $_GET['pageId']).'
            </ul>';
        if ($api->auth())
            $leftMenu .= '<a href="/cms/#list/'.$_GET['pageSectionID'].'" class="fe-smart-menu" style="color:#ffffff;position:absolute;" target="_blank">В раздел &rarr;</a>';




$api->header(array( 'page-title' => $title ));


                $kurs = $api->objects->getFullObject(7299); //EURO
               if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(2)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

                if($api->section->sectionId == 322 || $api->section->sectionId == 595 || $model['Класс']=='Sprinter Classic' ||  $model['Тип кузова'] == 'Минивэны и кемперы') {

                 $kurs = $api->objects->getFullObject(7300); //RUB
               if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(3)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

                }







                         $cost = $model['Цена'];

                       if(!empty($kurs['Значение'])) {
                            $cost = preg_replace('/\D+/Ui', '', $model['Цена']);

                            $cost = intval($cost);
                            $cost*=$kurs['Значение'];
                            $cost = number_format($cost, 0, ''," ");
                            $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $model['Цена']));


                        } else {


                           $cost = preg_replace('/\D+/Ui', '', $model['Цена']);

                        $cost = intval($cost);
                      //  echo '<script>alert(" ('.$kursBanks.' * 0.015) = '.( intval($kursBanks) * 0.015).'")</script>';

                   //echo '<script>console.log("цена * ( евро + евро * 0,015 ) = '.$cost.' * ('.$kursBanks.' + '.$kursBanks.' * 0.015) = '.($cost * ($kursBanks + $kursBanks * 0.015)).'")</script>';
                        $cost = $cost * ($kursBanks + $kursBanks * 0.015);

                  //  $cost = $cost * intval($euroBanks);

                        $cost = number_format($cost, 0, ''," ");

                        $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $model['Цена']));



                        }

?>


<div id="page_towar">

    <figure class="page_towar">



    <? if($mainPage == 1) { ?>
        <div class="bxslider2">
            <!-- images: width:1000px; height: 470px; -->
            <ul id="bxslider2">
                <? if(count($sliders)){
                    foreach($sliders as $slider){
                        echo '<li><img src="'._IMG_.'?w=1000&url='._UPLOADS_.'/'.$slider['Ссылка'].'" width="1000" /></li>';
                    }
                }?>

            </ul>
        </div><!-- /slider_bx -->
        <div class="towar_main_text">
            <?php if ($api->section->sectionName !== 'truck') : ?>

            <?php endif; ?>
            <div class="links">
               <?=$topMenusHtml;?>
            </div>
        </div>
    <? }else{ ?>
        <div class="towar-description">
            <h1><?=$title?></h1>
            <?=$tabMenuHtml;?>
            <?=$pageObj['Текст'];?>
            <?=$html?>
        </div>
        <div class="clearfix"></div>
    <? } ?>

        <div class="towar_menu">
            <div class="wrap">
                <div class="tit"><?=$model['Название']?></div>
                <?=$leftMenu?>
                <table>
                    <tbody><tr>
						<!--<td class="val" colspan="2">(&#8364)</td>-->
                        <!-- <td class="val" colspan="2">тг.</td> -->
                    </tr>
                    <?php if ($api->section->sectionName != 'truck') : ?>
                        <tr>
                            <td class="lab">Цена от</td>
                            <td class="cost"><?=$cost?> </td>
                        </tr>
                    <?php endif; ?>

                    </tbody></table>
            </div>
        </div>

    </figure>

    <? if($mainPage == 1) { ?>
    <div class="text_block">
        <div class="towar_mini" <?=$api->section->sectionName === 'truck' ? 'style="padding: 15px 100px 15px 230px"' : ''?>>
            <?=$model['Краткое описание']?>
            <?=$html?>
        </div>
        <div class="clearfix"></div>
    </div>
    <? }?>



</div>
<?
$api->footer();
?>