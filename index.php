<?
include_once 'cms/public/api.php';
$api->header(array('page-title'=>'<!--object:[138][18]-->'));
?>
    <div id="page_main">
        <!-- Главный слайдер -->
        <?=$api->mainSlider($api->section->mainSliderId)?>

        <figure class="ind_menu">
            <?php if (!empty($api->section->sectionName) && ($api->section->sectionName != 'truck')):?>
                <?=$api->imageMenu($api->section->imageMenuId)?>
            <?php endif; ?>
            <?=$api->mainSectionMenu()?>
        </figure>
        <div class="text_block">

            <div class="cont_link">
                <?=$api->smallMenu($api->section->minMenuId)?>
                <?=$api->socIconsMenu()?>
            </div>

            <?=$api->typeOfNewsBlock($api->section->sectionId)?>

            <div class="clearfix"></div>

        </div>

    </div>

<?
$api->footer();
?>