<?
include('cms/public/api.php');
$api->header(array( 'page-title' => 'Анонсы' ));

$anonsSectionId = 7;
$anonsId = 41;
$linkToFileId = 35;
$root = 402;

$body = '';
$title = 'Анонсы';

# --------- функция получает анонсы и формирует левое меню --------
# --------- Записывает обьекты анонсов в переменну ------
# --------- Возвращает массив ---------
function getLeftMenu() {
    global $api, $anonsSectionId, $root, $anonsId;
    if ($list = $api->objects->getFullObjectsListByCLass($root, $anonsSectionId)) {

        # -------- перемнная для записи обьектов ивлеченных анонсов
        $anonsObjects = array();

        $mainLi = array();
        foreach ($list as $o) {
            $subMenu = '';

            if ($subList = $api->objects->getFullObjectsListByCLass($o['id'], $anonsId) ) {
                $subLi = array();

                foreach ($subList as $so) {

                    $subHref = 'href="/'.$api->lang.'/'.$api->section->sectionName.'/announcements/section/'.$o['id'].'/'.$so['id'].'/"';

                    $subClass = '';
                    if (@$_GET['anonsId'] == $so['id'])
                        $subClass = 'class="active"';

                    $subLi[] = '
                        <li '.$subClass.'>
                            <a '.$subHref.'>'.$so['Название'].'</a>
                        </li>';

                    $anonsObjects[] = $so;
                }
                $subMenu = '<ul>'.join("\n", $subLi).'</ul>';
            }

            $href = 'href="/'.$api->lang.'/'.$api->section->sectionName.'/announcements/section/'.$o['id'].'/"';
            if (empty($subMenu))
                $href = 'href="#" onclick="return false;"';
            $class = '';
            if (@$_GET['anonsSectionId'] == $o['id'])
                $class = 'class="active"';

            $mainLi[] = '
                <li '.$class.'>
                    <a '.$href.'>'.$o['Значение'].'</a>
                    '.$subMenu.'
                     <!--smart:{ id:'.$o['id'].', title:"", actions:["edit", "add"], p: {add: ["'.$anonsId.'"]} }-->
                </li>';
        }

        return '
            <ul class="sidebar_menu accord_menu">
                <li><a href="/'.$api->lang.'/'.$api->section->sectionName.'/announcements/">Анонсы </a></li>
                '.join("\n", $mainLi).'
            </ul>
            <!--smart:{ id:'.$root.', title:"раздела", actions:["add"], p: {add:["'.$anonsSectionId.'"]}, info: {add: "добавить раздел"}  }-->';
    }
}

# ------ Сформиррованное левое меню --------
$leftMenu = getLeftMenu();

# ----- Если пришел ID одного Анонса
# ----- То просмотр Анонса ПОДРОБНЫЙ -------
if (@$_GET['anonsId'] && is_numeric($_GET['anonsId']) ) {

    $obj = $api->objects->getFullObject($_GET['anonsId']);

    $title = $obj['Название'];

    $body = '
        <div class="simple_text">
            '.$obj['Текст'].'
            <!--smart:{ id:'.$obj['id'].', title:"", actions:["edit", "remove"] }-->
        </div>';
}

# ------- СПИСОК АНОНСОВ ----------
else {

    if ($LinkList = $api->objects->getFullObjectsListByCLass($root, $linkToFileId)) {
        $linkHtml = array();
        foreach ($LinkList as $lo) {

            $linkHref = 'href="'.$lo['Ссылка'].'"';
            $linkSmart = '<!--smart:{ id:'.$lo['id'].', title:"", actions:["edit", "remove"] }-->';
            $linkHtml[] = '
                <li>
                    <div class="one">
                        <div class="one_lin">
                            <a '.$linkHref.' class="in2"><span>'.$lo['Название'].'</span></a>
                        </div>
                        <div class="img">
                            <a '.$linkHref.' >
                                <img src="'._IMG_.'?w=175&url='._UPLOADS_.'/'.$lo['Рисунок'].'" width="175">
                            </a>
                        </div>
                        <div class="text">'.$lo['Анонс'].'</div>
                    </div>
                    '.$linkSmart.'
                </li>';
        }
        $body = '
            <figure class="model_row">
                <ul>'.join("\n", $linkHtml).'</ul>
            </figure>';
    }

}


?>
    <div id="page_spec">
        <figure class="page_cols">

            <div class="left_column">
                <div class="wrap">
                    <?=$leftMenu?>
                </div>
            </div>

            <div class="right_column">
                <div class="cont">

                    <div class="main_title">
                        <h1><?=$title?></h1>
                    </div>

                    <?=$body?>

                    <figure class="social_icons_inner" style="float: left;">
                        <?=$api->socIconsMenu()?>
                    </figure>

                </div>
            </div>

        </figure>
    </div>

<?
$api->footer();
?>