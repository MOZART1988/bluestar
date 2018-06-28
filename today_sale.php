<?php
include('cms/public/api.php');
$classId = 53;
$modelClassId = 88;
$objectId = 28009;

if (!empty($api->section->sectionName) && $api->section->sectionName === 'van') {
    $objectId = 28229;
};

$euroObject = $api->objects->getFullObject(7299);
$rubObject = $api->objects->getFullObject(7300);

$models = $api->objects->getFullObjectsListByClass($objectId, $classId);

if ($models === null) {

    return;
}

$categoryId = !empty($_GET['category_id']) ? $_GET['category_id'] : null;

$api->header(array( 'page-title' => 'Сегодня в продаже' ));
?>

<div id="page_text">

    <figure class="page_cols">

        <div class="left_column">
            <div class="wrap">
                <ul class="sidebar_menu accord_menu">
                    <?=$api->getLeftMenu($objectId, '/' . $api->lang . '/' . $api->section->sectionName . '/page/' . @$_GET['pageSectionID'] . '/', 0);?>
                </ul>
            </div>
        </div>

        <div class="right_column">
            <div class="cont">
                <div class="main_title">
                    <h1>Сегодня в продаже</h1>
                </div>

                <div class="simple_text clearfix">
                    <?php if (empty($categoryId)) : ?>
                        <?php foreach ($models as $model) : ?>
                            <div class="today-block" style="width:200px; height: 150px; float: left;">
                                <article class="fullimg">
                                    <a href="/<?=$api->lang?>/<?=$api->section->sectionName?>/today_sale/<?=$model['id']?>/">
                                        <img src="<?=_IMGR_ ?>?w=167&h=95&image=<?= _UPLOADS_ ?>/<?=$model['Картинка'] ?>" />
                                    </a>
                                    <a style="display:block;" href="/<?=$api->lang?>/<?=$api->section->sectionName?>/today_sale/<?=$model['id']?>/"><?=$model['Название']?></a>
                                </article>
                            </div>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php
                            $items = $api->objects->getFullObjectsListByClass($categoryId, $modelClassId);
                        ?>
                        <?php if (!empty($items)) : ?>
                        <table id="CarTable" class="grid mvcgrid">
                            <thead>
                            <tr>
                                <th class="col2" data-sortable="True" data-sort-column="Engine.Name"
                                    data-sort-direction="Descending"><span>Модель</span></th>
                                <th class="col3" data-sortable="True" data-sort-column="ColorBody.Name"
                                    data-sort-direction="Descending"><span>Номер заказа</span></th>
                                <th class="col4" data-sortable="True" data-sort-column="SalonFurnish.Name"
                                    data-sort-direction="Descending"><span>Кузов</span></th>
                                <th class="col5" data-sortable="True" data-sort-column="DateManufacture"
                                    data-sort-direction="Descending"><span>Салон</span></th>
                                <th class="col7" data-sortable="True"
                                    data-sort-column="Location.LocationCategory.SortOrder"
                                    data-sort-direction="Descending"><span>Год</span></th>
                                <?php if ($api->section->sectionName !== 'van') :?>
                                    <th class="sort_asc last" sort_index="0" data-sortable="True"
                                        data-sort-column="FinalPrice" data-sort-direction="Descending"><span
                                                class="sort_asc">Опции</span></th>
                                <?php endif; ?>
                                <th class="sort_asc last" sort_index="0" data-sortable="True"
                                    data-sort-column="FinalPrice" data-sort-direction="Descending"><span
                                        class="sort_asc">Местоположение</span></th>
                                <th class="sort_asc last" sort_index="0" data-sortable="True"
                                    data-sort-column="FinalPrice" data-sort-direction="Descending"><span
                                        class="sort_asc">Цена, тенге</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr itemscope="" itemtype="http://schema.org/Product" class="gridrow">
                                    <td><a href="<?= _UPLOADS_ ?>/<?=$item['Пдф файл'] ?>" target="_blank"><?=$item['Модель']?></a></td>
                                    <td><?=$item['Номер заказа']?></td>
                                    <td><?=$item['Кузов']?></td>
                                    <td><?=$item['Салон']?></td>
                                    <td><?=$item['Год']?></td>
                                    <?php if ($api->section->sectionName !== 'van') : ?>
                                        <td><?=$item['Опции']?></td>
                                    <?php endif; ?>
                                    <td><?=$item['Местоположение']?></td>
                                    <td style="width:60px!important;"><?=number_format((int)$item['Цена'] * (!empty($item['Цена в рублях']) ? $rubObject['Значение'] : $euroObject['Значение']), 0, ' ', ' ')?></td>
                                </tr>
                            <?php endforeach ; ?>
                            </tbody>
                        </table>
                        <?php else : ?>
                            <h3>Нет ни одной модели</h3>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <figure class="social_icons_inner">
                    <?= $api->socIconsMenu() ?>
                </figure>
            </div>

        </div>

    </figure>


</div>
<?php $api->footer(); ?>