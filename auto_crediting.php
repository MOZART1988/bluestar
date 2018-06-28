<?
include('cms/public/api.php');
$autoCreditingSectionId = 45;
$sliderId = 46;
$slideId = 35;

if (!$sectionObj = $api->objects->getFullObjectsListByCLass($api->section->sectionId, $autoCreditingSectionId,"AND o.active='1' LIMIT 1")) exit;

if (!empty($sectionObj['Ссылка'])) header("Location: ".$sectionObj['Ссылка']);


$html = '';
$title = $sectionObj['Название'];


# --------- ЕСЛИ ПРИШЕЛ ID ОБЬЕКТА "ПРОГРАММА КРЕЛИТОВАНИЯ" ---------
if (@$_GET['creditProgId'] && is_numeric($_GET['creditProgId'])) {

    $crediProgObj = $api->objects->getFullObject($_GET['creditProgId']);

    $title = $crediProgObj['Название'];

    $html = '
        <figure class="page_cols">
            <div class="left_column">
                <div class="wrap">
                    <ul class="sidebar_menu accord_menu">
                        <li '.($_SERVER['REQUEST_URI'] == '/'.$api->lang.'/'.$api->section->sectionName.'/auto_crediting/'?'class="active"':'').'>
                            <a href="/'.$api->lang.'/'.$api->section->sectionName.'/auto_crediting/">'.$sectionObj['Название'].'</a>
                        </li>
                        '.$api->getLeftMenu($sectionObj['id'], '/'.$api->lang.'/'.$api->section->sectionName.'/auto_crediting/', @$_GET['creditProgId']).'
                    </ul>
                </div>
            </div>
            <div class="right_column">
                <div class="cont">
                    <div class="main_title">
                        <h1>'.$title.'</h1>
                    </div>
                    <div class="simple_text">
                        '.$crediProgObj['Текст'].'
                        <!--smart:{ id:'.$crediProgObj['id'].', title:"текста", actions:["edit"] }-->
                    </div>
                    <figure class="social_icons_inner">'.$api->socIconsMenu().'</figure>
                </div>
            </div>
        </figure>';

}

# ---------- ЕСЛИ ВНУТРИ РАЗДЕЛА ЕСТЬ ОБЬЕКТ СЛАЙДЕРА ----------
elseif ($sliderObj = $api->objects->getFullObjectsListByCLass($sectionObj['id'], $sliderId, "AND o.active='1' LIMIT 1")) {

    $title = $sliderObj['Название'];
    $title2 = $sliderObj['Слоган'];

    $mainfirstImg = '';
    if ( !empty($sliderObj['Рисунок']) )
        $mainfirstImg = '
            <img src="'._IMGR_.'?W=1000&h=470&image='._UPLOADS_.'/'.$sliderObj['Рисунок'].'" width="1000" height="470"/>';

    if ($slidesList = $api->objects->getFullObjectsListByCLass($sliderObj['id'], $slideId)) {
        $s1 = array();
        $s2 = array();
        $s3 = array();

        $si = 0;
        foreach ($slidesList as $so) {
            $tmpI = $si + 1;
            $s1[] = '
                <li>
                    <div class="one">
                        <div class="img"><a href="#" onclick="return false;"><img src="'._IMGR_.'?w=141&h=61&image='._UPLOADS_.'/'.$so['Рисунок'].'" width="141" height="68"/></a></div>
                        <div class="cr_link"><a href="#" class="in2">'.$so['Название ссылки'].'</a></div>
                        <!--smart:{ id:'.$so['id'].', title:"Слайда", actions:["edit", "remove"], css:{ position:"absolute" } }-->
                    </div>
                </li>';
            $s2[] = '<li><a data-slide-index="'.$si.'" href="" class="in2"><span>'.$so['Название ссылки'].'</span></a></li>';
            $s3[] = '
                <li id="s_'.$tmpI.'">
                    <img src="'._IMGR_.'?w=1000&h=470&image='._UPLOADS_.'/'.$so['Рисунок'].'" width="1000" style="height:470px;"/>
                    <div class="caption">
                        <!--smart:{ id:'.$so['id'].', title:"Слайда", actions:["edit", "remove"], css:{ position:"absolute",right:0} }-->
                        <h1>'.$so['Название'].'</h1>
                        <div class="text">'.$so['Анонс'].'</div>
                        <a href="'.$so['Ссылка'].'" class="in2">Подробнее</a>
                    </div>
                </li>';
            $si++;
        }
        unset($si, $tmpI);
        $html = '
            <div id="page_credit">
                <figure class="page_credit">
                    <div class="fullimg">
                        <div class="credit_main_text">
                            <div class="tit">'.$title.'</div>
                            <div class="note">'.$title2.'</div>
                        </div>
                        <!--smart:{ id:'.$sliderObj['id'].', title:"Слайдера", actions:["edit", "add"], p:{add:["'.$slideId.'"]}, css:{ position:"absolute", right:0 } }-->
                        '.$mainfirstImg.'
                        <div class="credit_cat">
                            <ul>'.join("\n", $s1).'</ul>
                        </div>
                    </div>
                    <div class="bxslider4" style="display: none;">
                        <ul id="bxslider4">'.join("\n", $s3).'</ul>
                        <div id="bx-pager4">
                            <ul>'.join("\n", $s2).'</ul>
                        </div>
                    </div>
                    <div class="credit_menu" style="">
                        <div class="wrap">
                            <ul class="sidebar_menu accord_menu">
                                <li '.($_SERVER['REQUEST_URI'] == '/'.$api->lang.'/'.$api->section->sectionName.'/auto_crediting/'?'class="active"':'').'>
                                    <a href="/'.$api->lang.'/'.$api->section->sectionName.'/auto_crediting/">'.$sectionObj['Название'].'</a>
                                </li>
                                '.$api->getLeftMenu($sectionObj['id'], '/'.$api->lang.'/'.$api->section->sectionName.'/auto_crediting/', @$_GET['creditProgId']).'
                            </ul>
                        </div>
                    </div>
                    <div class="text_block">
                        <figure class="social_icons">
                            '.$api->socIconsMenu().'
                        </figure>
                    </div>
                </figure>
            </div>';
    }
}

$api->header(array( 'page-title' => $title ));

echo $html;

$api->footer();
?>