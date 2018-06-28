<?
include('cms/public/api.php');

$linkToFile = 35;

$root = 513;

$headImg = '';
$headTitles = '';
$body = '';
$specialProgs = '';

# ----- ПОЛУЧЕНИЕ из ДАННОГО ID ОБЬЕКТОВ "ССЫЛКА НА ФАЙЛ с рисунком"
# ----- СФОРМИРОВАННЫХ то есть HTML ------------
function getLinkToFile($id) {
    global $api, $linkToFile;

    if ($list = $api->objects->getFullObjectsListByCLass($id, $linkToFile, "AND o.active='1' ")) {
        return $api->getHtmlLinkToFileListOrItem($list);
    }
}

# ------------------------------------- #
$vacancySectionObj = $api->objects->getFullObject($root);

$headImg = '<img src="'._IMG_.'?w=1000&url='._UPLOADS_.'/'.$vacancySectionObj['Рисунок'].'" width="1000"/>';

$t1 = @$vacancySectionObj['Название']?$vacancySectionObj['Название']:'';
$t2 = @$vacancySectionObj['Название (2 строка)']?$vacancySectionObj['Название (2 строка)']:'';
$t3 = @$vacancySectionObj['Название (3 строка)']?$vacancySectionObj['Название (3 строка)']:'';

$headTitles = '
    <div class="main_title">
        <h1>'.$t1.'</h1>
        <h1>'.$t2.'</h1>
        <div class="minitit">'.$t3.'</div>
        <!--smart:{ id:'.$vacancySectionObj['id'].', title:"Раздела", actions:["edit", "add"], p:{add:["'.$linkToFile.'"]} }-->
    </div>';

$body = getLinkToFile($vacancySectionObj['id']);

$api->header(array( 'page-title' => $vacancySectionObj['Название'] ));

?>

    <div id="page_finance">
        <figure class="page_finance">
            <?=$headImg?>
            <div class="cont">

                <?=$headTitles?>

                <div class="fin_line">
                    <?=$body?>
                </div>

            </div>
        </figure>
    </div>


<?
$api->footer();
?>