<?
include('cms/public/api.php');

if (!@$_GET['serviceSectionId'] || !is_numeric($_GET['serviceSectionId']) ) header("location: /");

$title = '';
$html = '';
$firstMainImg = '';

$airLinkId = 48;

$sectionObj = $api->objects->getFullObject($_GET['serviceSectionId']);


$bannerId = 51;
$textId = 3;
$tableId = 49;
$minAnonsId = 50;
$accordionClass = 64;
$accordionBlockClass = 66;
$snoskaClass = 65;
$tabsBlockClass = 67;
$tabsElementClass = 68;
$tabsMenuClass = 69;
$linkClass = 2;

# -------- если пришел ID текстовой страницы ---
# -------- Вывод текстовой страницы ------------
if (@$_GET['servicePageId'] && is_numeric($_GET['servicePageId'])) {

    $txtObj = $api->objects->getFullObject($_GET['servicePageId']);
	
	$addObjectsList = $api->objects->getFullObjectsList($txtObj['id']);
	$addObjects = array();
    $accordionObjects = array();
    $snoskaObjects =array();
	
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

                $tabContents = array();
				
				//if(!$elements) $tabsContents[] = 'Не найдено ни одного елемента блока';

                $iCount = 0;
                foreach($elements as $element){
                    $smart_element = '
                <!--smart:{
                    id:'.$element['id'].',
                    title:"элемента",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                    $tabs[] = '<li><a href="#tab'.$addObj['id'].'_'.$iCount.'" '.($iCount==0 ? 'class="on"':'').'>'.$element['Название'].'</a></li>';

                    $tabsContents[] = '<div class="type_cont" id="tab'.$addObj['id'].'_'.$iCount.'" '.($iCount==1 ? 'style="display:block"':'').'><div class="text">'.$element['Текст'].$smart_element.'</div></div>';
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

                $addObjects[] = '<div class="block">
											<article class="tabpage">
												<ul class="tab_row">'.join("\n", $tabs).'</ul>'.
                    (!empty($tabsContents)?join("\n", $tabsContents).$smart:$smart).'
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
		
	}
	
	if (count($snoskaObjects)){
        $addObjects[] = join("\n", $snoskaObjects);
    }

    $title = $txtObj['Название'];

    $html = '
        <figure class="page_cols">
            <div class="left_column">
                <div class="wrap">
                    <ul class="sidebar_menu accord_menu">
                        <li><a href="/'.$api->lang.'/'.$api->section->sectionName.'/service/'.$sectionObj['id'].'/">'.$title.'</a></li>
                        '.$api->getLeftMenu($sectionObj['id'], '/'.$api->lang.'/'.$api->section->sectionName.'/service/'.$sectionObj['id'].'/', @$_GET['servicePageId']).'
                    </ul>
                </div>
            </div>
            <div class="right_column">
                <div class="cont">
                    <div class="main_title">
                        <h1>'.$title.'</h1>
                    </div>
                    <div class="simple_text">
                        '.$txtObj['Текст'].'
						<div style="margin-top: 15px" class="dop-objects">
						'.(!empty($addObjects)?join("\n", $addObjects):'').'
						</div>
                        <!--smart:{ 
							id:'.$txtObj['id'].', 
							title:"страницы", 
							actions:["edit", "add"], 
							p:{
								add: ["'.$bannerId.'", "'.$textId.'", "'.$minAnonsId.'", "'.$tableId.'", "'.$accordionBlockClass.'", "'.$snoskaClass.'", "'.$tabsBlockClass.'"]
							}
						}-->
                    </div>
                    <figure class="social_icons_inner">'.$api->socIconsMenu().'</figure>
                </div>
            </div>
        </figure>';

}

# ------- СПИСОК Текстовых страницы и главная страница ----
else {

    $title = $sectionObj['Название'];
    $firstMainImg = '<img src="'._IMG_.'?w=1000&url='._UPLOADS_.'/'.$sectionObj['Рисунок'].'" width="1000"/>';
    $airLinks = '';

    if ($airLinklist = $api->objects->getFullObjectsListByCLass($sectionObj['id'], $airLinkId)) {
        $tmp = array();
        foreach ($airLinklist as $ao) {
            $tmp[] = '
                <li style="top:'.$ao['От верхнего угла'].'px;left:'.$ao['От левого угла'].'px;">
                    <a href="#">'.$ao['Название'].'</a>
                    <div class="cont_serv">
                        <div class="wrap">
                            <div class="tit">'.$ao['Название'].'</div>
                            <div class="text">'.$ao['Анонс'].'</div>
                            <a href="'.$ao['Ссылка'].'" class="in3">Перейти в раздел «'.$ao['Название'].'»</a>
                        </div>
                    </div>
                    <!--smart:{ id:'.$ao['id'].', title:"ссылки", actions:["edit", "remove"] }-->
                </li>';
        }
        $airLinks = '<ul>'.join("\n", $tmp).'</ul>';
    }

    $html = '
		<a id="hidden-link" style="display:none!important" href="service_modal.php"></a>
        <div id="page_service">
            <figure class="page_service">
                <div class="service_rand">
                    <!--smart:{ id:'.$sectionObj['id'].', title:"раздела", actions:["edit", "add"], p:{ add:["1","2","7", "'.$airLinkId .'"] }, css:{ position:"absolute",right:0 } }-->
                    '.$firstMainImg.'
                    '.$airLinks.'
                </div>
                <div class="ser_menu">
                    <div class="wrap">
                        <ul class="sidebar_menu accord_menu">
                            <li class="active"><a href="/'.$api->lang.'/'.$api->section->sectionName.'/service/'.$sectionObj['id'].'/">'.$title.'</a></li>
                            '.$api->getLeftMenu($sectionObj['id'], '/'.$api->lang.'/'.$api->section->sectionName.'/service/'.$sectionObj['id'].'/', @$_GET['servicePageId']).'
                        </ul>
                    </div>
                </div>
            </figure>
        </div>';
}


$api->header(array( 'page-title' => $title ));

echo $html;


$api->footer();
?>