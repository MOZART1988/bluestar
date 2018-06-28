<?
include('cms/public/api.php');
$api->header(array( 'page-title' => 'Обзор' ));

$reviewsSectionId = 44;
$linkToFile = 35;

$headImg = '';
$headTitles = '';
$body = '';
$specialProgs = '';

# ----- ПОЛУЧЕНИЕ из ДАННОГО ID ОБЬЕКТОВ "ССЫЛКА НА ФАЙЛ с рисунком"
# ----- СФОРМИРОВАННЫХ то есть HTML ------------
function getLinkToFile($id) {
    global $api, $linkToFile;

    if ($list = $api->objects->getFullObjectsListByCLass($id, $linkToFile, "AND o.active='1' AND c.field_144 IS NULL ")) {
        return $api->getHtmlLinkToFileListOrItem($list);
    }
}

# ------------------------------------- #
if ($reviewsSectionObj = $api->objects->getFullObjectsListByCLass($api->section->sectionId, $reviewsSectionId, "AND o.active='1' LIMIT 1")) {
    $headImg = '<img src="'._IMG_.'?w=1000&url='._UPLOADS_.'/'.$reviewsSectionObj['Рисунок'].'" width="1000"/>';

    $t1 = @$reviewsSectionObj['Название']?$reviewsSectionObj['Название']:'';
    $t2 = @$reviewsSectionObj['Название (2 строка)']?$reviewsSectionObj['Название (2 строка)']:'';
    $t3 = @$reviewsSectionObj['Название (3 строка)']?$reviewsSectionObj['Название (3 строка)']:'';

    $headTitles = '
        <div class="main_title">
            <h1>'.$t1.'</h1>
            <h1>'.$t2.'</h1>
            <div class="minitit">'.$t3.'</div>
            <!--smart:{ id:'.$reviewsSectionObj['id'].', title:"Раздела", actions:["edit", "add"], p:{add:["'.$linkToFile.'"]} }-->
        </div>';

    $body = getLinkToFile($reviewsSectionObj['id']);

    if ($specialProglist = $api->objects->getFullObjectsListByCLass($reviewsSectionObj['id'], $linkToFile, "AND o.active='1' AND c.field_144='1' ")) {
        $specialProgs = $api->getHtmlLinkToFileListOrItem($specialProglist);
    }
}

?>

    <div id="page_finance">
        <figure class="page_finance">
            <?=$headImg?>
            <div class="cont">
                <?=$headTitles?>
                <div class="fin_line">
                    <?=$body?>
                </div>

                <div class="fin_line">
                    <div class="tit">СПЕЦПРОГРАММЫ</div>
                    <?=$specialProgs?>
                </div>

            </div>

        </figure>

    </div>


<?
$api->footer();
?>