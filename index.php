<?
include_once 'cms/public/api.php';
$api->header(array('page-title'=>'<!--object:[138][18]-->'));
?>
    <div id="page_main">
        <!-- Главный слайдер -->
        <?=$api->mainSlider($api->section->mainSliderId)?>

        <figure class="ind_menu">
            <?=$api->imageMenu($api->section->imageMenuId)?>
            <?=$api->mainSectionMenu()?>
        </figure>
        <div class="text_block" <?=$api->section->sectionName === 'truck' ? 'style="min-height:20px"' : ''?>>

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