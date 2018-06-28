<?
include('cms/public/api.php');

$title = 'Модельный ряд';

$subsectionMenu = '';
if ($api->section->sectionName == 'passengercars'){
    $subsectionMenu .= '<div class="block_link">
        <!--<a href="/'.$api->lang.'/passengercars/models/"><span>Все модели</span></a>
        <a href="/'.$api->lang.'/passengercars/models/amg/"><span>Модели AMG</span></a>-->
    </div>';
}

$modelsByTypes = array();

foreach ($api->models as $model){
    @$modelsByTypes[$model['Тип кузова']][] = $model;
}

$bodyTypes = $api->objects->getObjectsList($api->section->bodyTypeListId);

$bodyTypeMenu = '';
$bodyTypeSlides = '';
$iCount = 0;

$kurs = $api->objects->getFullObject(7299); //EURO
               if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(2)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

           
//var_dump($api->section->sectionId);
if(count($modelsByTypes)){
    foreach($bodyTypes as $bodyType){
        $bodyTypeMenu .= '<a data-slide-index="'.$iCount.'" href=""><span>'.$bodyType['name'].'</span></a>';

        $bodyTypeSlides .= '<li class="slide">
                    <div class="sl_one">
                        <h1>'.$bodyType['name'].'</h1>
                        <div class="sl_mod_all">';

        $iCountModel = 0;
        if(isset($modelsByTypes[$bodyType['name']]) && count($modelsByTypes[$bodyType['name']])){
            foreach($modelsByTypes[$bodyType['name']] as $type=>$model){
                
     if($api->section->sectionId == 322 || $api->section->sectionId == 595 || $model['Класс']=='Sprinter Classic' ||  $model['Тип кузова'] == 'Минивэны и кемперы') {
                $kurs = $api->objects->getFullObject(7300); //RUB
                     if(empty($kurs['Значение'])) {
                 
               
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(3)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

                }
                $iCountModel++;

                if($iCountModel % 2 == 1){
                    $bodyTypeSlides .= '<div class="sl_mod_row">';
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

                        $cost = $cost * ($kursBanks + $kursBanks * 0.015);
                  //  $cost = $cost * intval($euroBanks);

                        $cost = number_format($cost, 0, ''," ");

                        $cost  = $cost.' '.trim(preg_replace('/\d+/Ui', '', $model['Цена']));



                        }
				# <span class="cost">'.$model['Цена'].' тг.</span>
                $bodyTypeSlides .= '<div class="one">
                                    <a href="/'.$api->lang.'/'.$api->section->sectionName.'/model/'.urlencode($model['Код модели']).'/">
                                        <img src="'._UPLOADS_.'/'.$model['Мини-фото'].'" />
                                        <span class="tit">'.$model['Название'].'</span>
                                        <span class="cost">'.$cost.' тг</span>
                                    </a>
                                    <!--smart:{ id:'.$model['id'].', title:"", actions:["edit", "remove"] }-->
                                </div>';
                if($iCountModel % 2 == 0){
                    $bodyTypeSlides .= '</div>';
                }
            }

            if($iCountModel % 2 == 1){
                $bodyTypeSlides .= '</div>';
            }


        }

        $bodyTypeSlides .= '</div>
                    </div>
                </li>';

        $iCount++;
    }
}



$api->header(array( 'page-title' => $title ));
?>

<div id="page_allmodel">
    <figure class="page_allmodel">
        <div class="bxslider3">
            <!-- images: width:270px; height: 120px; -->
            <!-- по три блока в одном слайдей, в каждом блоке два элемента -->
            <ul id="bxslider3">
                <?php echo $bodyTypeSlides;?>
            </ul>
            <?php echo $subsectionMenu;?>

            <div id="bx-pager3">
                <?php echo $bodyTypeMenu;?>
            </div>
            <div class="clearfix"></div>
        </div><!-- /slider_bx -->
    </figure>
    <!-- /page_allmodel -->
</div>

<?
$api->footer();
?>