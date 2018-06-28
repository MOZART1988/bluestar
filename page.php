<?
include('cms/public/api.php');

$title = '';
$html = '';
$leftMenu = '';

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

$today_class = 1;


$subscribe_root_id = 5465;
$class_id = 7;


//данные по динамической форме

$form_themes = array(
    1 => 'Автомобили (заказ, цены, комплектации)',
    2 => 'Запись на тест-драйв',
    3 => 'Запасные части и аксессуары',
    4 => 'Записаться на сервис и техобслуживание',
    5 => 'Работа и карьера',
    6 => 'Предложение о сотрудничестве',
    7 => 'Жалоба или претензия'
);

$current_theme = 1;

if (!empty($_GET['theme'])) {
    $current_theme = $_GET['theme'];
}

if (!empty($_POST['theme'])) {
    $current_theme = $_POST['theme'];
}






# ------ ЕСЛИ только одна страница без левого меню
if (@$_GET['pageId'] && is_numeric($_GET['pageId'])) {


    $pageObj = $api->objects->getFullObject($_GET['pageId']);

    $tabMenuHtml = '';

    $tabMenuHead = $api->objects->getFullObjectsListByClass($pageObj['id'], $tabsMenuClass, "AND o.active='1' ORDER BY o.sort LIMIT 1");

    if (!$tabMenuHead) {
        $tabMenuHead = $api->objects->getFullObjectsListByClass($pageObj['head'], $tabsMenuClass, "AND o.active='1' ORDER BY o.sort LIMIT 1");
    }

    if ($tabMenuHead) {
        $tabMenus = $api->objects->getFullObjectsListByClass($tabMenuHead['id'], $linkClass, "AND o.active='1' ORDER BY o.sort");
        $tabMenuHtml = '<div>';
        foreach ($tabMenus as $tabMenu) {
            $tabMenuHtml .= '<a ' . $api->getLink($tabMenu) . '>' . $tabMenu['Название'] . '</a>&nbsp;';
        }
        $tabMenuHtml .= '</div>';
    }

    $title = $pageObj['Название'];
    if (!empty($pageObj['Название (длинное)']))
        $title = $pageObj['Название (длинное)'];

    $html = '
        <!--smart:{ 
			id:' . $pageObj['id'] . ', 
			title:"страницы", 
			actions:["edit", "add"], 
			p:{
				add: ["' . $bannerId . '", "' . $textId . '", "' . $minAnonsId . '", "' . $tableId . '", "' . $accordionBlockClass . '", "' . $snoskaClass . '", "' . $tabsBlockClass . '", "' . $today_class . '"]
			}
		}-->
		' . $pageObj['Текст'];


    $lang = $api->lang;
    $api->lang = 'ru';
# ФОТОГАЛЛЕРЕЯ
    $onepage = 100;
    $pages = $api->pages($api->objects->getObjectsCount($pageObj['id'], 4), $onepage, 5, array(), "/" . $api->lang . "/" . $api->t($pageObj['id']) . "/pg/#pg#.html#photos-list", $api->lang);
    if ($photos = $api->objects->getFullObjectsListByClass($pageObj['id'], 4, "AND o.active='1' ORDER BY o.sort LIMIT " . $pages['start'] . ", $onepage")) {
        //echo '<br><h2>'.$vars[$lang]['attachedPhotos'].'</h2>';
        $n = 0;
        $out = array();
        foreach ($photos as $photo) {
            $n++;
            if ($n == 1) {
                $out[] = '<tr valign="top">';
            }
            $out[] = '
		<td id="photo-' . $photo['id'] . '" align="center">
			<a class="photo" href="' . _UPLOADS_ . '/' . $photo['Ссылка'] . '" rel="photo_group_' . $pageObj['id'] . '" title="' . $photo['Название'] . '"><img style="padding:3px;border:1px solid #e7e7e7; background-color:#fff;" src="' . _IMGR_ . '?w=136&h=136&image=' . _UPLOADS_ . '/' . $photo['Ссылка'] . '"></a>
			<div>
			<!--smart:{
				id:' . $photo['id'] . ',
				actions:["edit", "remove"],
				p:{
					remove : "#photo-' . $photo['id'] . '"
				}
			}-->
			</div>
		</td>';
            if ($n == 4) {
                $out[] = '</tr>';
                $n = 0;
            }
        }
        if ($out[sizeof($out) - 1] != '</tr>') $out[] = '</tr>';
        $html .= '<table id="photos-list" width="100%" cellpadding="7" cellspacing="0">' . join("\n", $out) . '</table>';
    }

    $api->lang = $lang;


    # -------- ДОПОЛНИТЕЛЬНЫЕ ЭЛЕМЕНТЫ СТРАНИЦЫ ----------
    if ($addObjectsList = $api->objects->getFullObjectsList($pageObj['id'])) {
        $addObjects = array();
        $accordionObjects = array();
        $snoskaObjects = array();
        foreach ($addObjectsList as $addObj) {

            # ------------ Если Текст --------
            if ($addObj['class_id'] == $textId) {
                $addObjects[] = '
					<div class="block">
						' . $addObj['Текст'] . '
						<!--smart:{ id:' . $addObj['id'] . ', title:"текста", actions:["edit", "remove"] }-->
					</div>';
                continue;
            }

            #-------------- Если блок "сегодня в продаже"----------

            if (($addObj['class_id'] == $today_class) && (!empty($addObj['Добавить в сегодня в продаже']))) {
                $addObjects[] = '
					<div class="today-block" style="width:200px; height: 150px; float: left;">
						<article class="fullimg">
							<img src="' . _IMGR_ . '?w=167&h=95&image=' . _UPLOADS_ . '/' . $addObj['Картинка'] . '" />
							<a style="display:block;" href="/' . $api->lang . '/' . $api->section->sectionName . '/page/5483/' . $addObj['id'] . '/">' . $addObj['Название'] . '</a>
						</article>
					</div>
					<!--smart:{ id:' . $addObj['id'] . ', title:"блока сегодня в продаже", actions:["edit", "remove"] }-->
				';
            }

            # ------------ Если Баннер --------
            if ($addObj['class_id'] == $bannerId) {
                $addObjects[] = '
					<div class="block">
						<article class="fullimg">
							<img src="' . _IMG_ . '?w=720&url=' . _UPLOADS_ . '/' . $addObj['Рисунок'] . '" width="720">
						</article>
					</div>
					<!--smart:{ id:' . $addObj['id'] . ', title:"банера", actions:["edit", "remove"] }-->';
                continue;
            }

            # ------------ Если таблица --------
            if ($addObj['class_id'] == $tableId) {
                $addObjects[] = $addObj['Содержание'] . '<!--smart:{ id:' . $addObj['id'] . ', title:"элемента", actions:["edit", "remove"] }-->';
                continue;
            }

            # ------------ Если миниАнонс --------
            if ($addObj['class_id'] == $minAnonsId) {

                $minAnonsImg = '';
                if (!empty($addObj['Рисунок']))
                    $minAnonsImg = '
						<div class="img">
							<a href="' . _UPLOADS_ . '/' . $addObj['Рисунок'] . '" class="fancy">
								<img src="' . _IMG_ . '?w=175&url=' . _UPLOADS_ . '/' . $addObj['Рисунок'] . '" width="175">
							</a>
						</div>';

                $addObjects[] = '
					<article class="minianons">
						<div class="one">
							' . $minAnonsImg . '
							<div class="desc">
								<div class="tit">' . $addObj['Название'] . '</div>
								<div class="text">' . $addObj['Анонс'] . '</div>
								' . $addObj['Ссылки'] . '
							</div>
						</div>
						<!--smart:{ id:' . $addObj['id'] . ', title:"элемента", actions:["edit", "remove"] }-->
					</article>';
                continue;
            }

            # ------------ Если аккордеон --------
            if ($addObj['class_id'] == $accordionBlockClass) {

                $elements = $api->objects->getFullObjectsListByClass($addObj['id'], $accordionClass, "AND o.active='1' ORDER BY o.sort");


                $accordionObjects = array();

                foreach ($elements as $element) {
                    $smart = '
                <!--smart:{
                    id:' . $element['id'] . ',
                    title:"элемента",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                    $accordionObjects[] = '<h3>' . $element['Название'] . '</h3>
								  <div>
								    ' . $smart . $element['Текст'] . '
								  </div>';
                }

                $smart = '
                <!--smart:{
                    id:' . $addObj['id'] . ',
                    title:"секции",
                    actions:["add","edit","remove"],
                    p:{add: ["' . $accordionClass . '"]}
                }-->
                ';

                $addObjects[] = $smart . '<div class="accordion">' . join("\n", $accordionObjects) . '</div>';

                continue;
            }

            # ------------ Если вкладки --------
            if ($addObj['class_id'] == $tabsBlockClass) {

                $elements = $api->objects->getFullObjectsListByClass($addObj['id'], $tabsElementClass, "AND o.active='1' ORDER BY o.sort");


                $tabs = array();

                $tabContents = array();

                //if(!$elements) $tabsContents[] = 'Не найдено ни одного елемента блока';

                $iCount = 0;
                foreach ($elements as $element) {
                    $smart = '
                <!--smart:{
                    id:' . $element['id'] . ',
                    title:"элемента",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                    $tabs[] = '<li><a href="#tab' . $addObj['id'] . '_' . $iCount . '" ' . ($iCount == 0 ? 'class="on"' : '') . '>' . $element['Название'] . '</a></li>';

                    $tabsContents[] = '<div class="type_cont" id="tab' . $addObj['id'] . '_' . $iCount . '" ' . ($iCount == 1 ? 'style="display:block"' : '') . '><div class="text">' . $element['Текст'] . '</div></div>';
                    $iCount++;
                }

                $smart = '
                <!--smart:{
                    id:' . $addObj['id'] . ',
                    title:"блока",
                    actions:["add","edit","remove"],
                    p:{add: ["' . $tabsElementClass . '"]}
                }-->
                ';

                $addObjects[] = '<div class="block">
											<article class="tabpage">
												<ul class="tab_row">' . join("\n", $tabs) . '</ul>' .
                    (!empty($tabsContents) ? join("\n", $tabsContents) . $smart : $smart) . '
										    </article>
											<!-- /tabpage -->
										</div>';

                continue;
            }

            # ------------ Если сноска --------
            if ($addObj['class_id'] == $snoskaClass) {

                $smart = '
                <!--smart:{
                    id:' . $addObj['id'] . ',
                    title:"блока",
                    actions:["edit", "remove"],
                    p:{}
                }-->
                ';
                $snoskaObjects[] = '<div class="snos">
								    ' . $smart . $addObj['Текст'] . '
								  </div>';
                continue;
            }
        }

        if (count($snoskaObjects)) {
            $addObjects[] = join("\n", $snoskaObjects);
        }

        $html .= join("\n", $addObjects);
    }

    $api->mothers = array();
    $api->getMothers(5484);
    $api->mothers = array_reverse($api->mothers);

    $current_left_menu = '';

    if (@$_GET['pageSectionID'] == 5483) {
        $current_left_menu = $api->getLeftMenu(@$_GET['pageSectionID'], '/' . $api->lang . '/' . $api->section->sectionName . '/page/' . @$_GET['pageSectionID'] . '/', @$_GET['pageId']) .
            $api->getLeftMenuForToday(5484, 1, '/' . $api->lang . '/' . $api->section->sectionName . '/page/' . @$_GET['pageSectionID'] . '/');
    } else {
        $current_left_menu = $api->getLeftMenu(@$_GET['pageSectionID'], '/' . $api->lang . '/' . $api->section->sectionName . '/page/' . @$_GET['pageSectionID'] . '/', @$_GET['pageId']);
    }

    //print_r($api->mothers);

    # ----------- ЛЕВОЕ МЕНЮ -------------
    if (@$_GET['pageSectionID']) {
        $leftMenu = '
            <ul class="sidebar_menu accord_menu">
                ' . $current_left_menu . '
            </ul>';
        if ($api->auth())
            $leftMenu .= '<a href="/cms/#list/' . $_GET['pageSectionID'] . '" class="fe-smart-menu" style="color:#ffffff;position:absolute;" target="_blank">В раздел &rarr;</a>';
    } else {
        $leftMenu = '<ul></ul>';
    }

} else {

    header("location: / ");
}

if (@$_GET['pageId'] == 5684) {
    $lastUpdate = $api->objects->getFullObjectsListByClass(7720, 84, "AND o.active='1' ORDER BY c.field_268 DESC LIMIT 1");
    $lastUpdate = new DateTime($lastUpdate['Дата обновления']);
    $partsExamples = $api->objects->getFullObjectsListByClass(7720, 84, "AND o.active='1' ORDER BY sort LIMIT 3");
    $example = join(', ', array_map(function ($element) {
        return $element['Артикул'];
    }, $partsExamples));
    $articulRows = '';
    $codes = [];

    if (!empty($_GET['articul']) && is_array($_GET['articul'])) {

        foreach ($_GET['articul'] as $articul) {
            $articul = $api->db->prepare(strtoupper(trim($articul)));
            if (strlen($articul) > 7) {
                $codes[] = $articul;
            }
        }

        if ($parts = $api->objects->getFullObjectsListByClass(7720, 84, "AND o.active='1' AND c.field_263 REGEXP '^" . join("|^", $codes) . "' ORDER BY c.field_263 LIMIT 50")) {
            foreach ($parts as $part) {
                $articulRows .= '<tr>
                        <td>' . $part['Артикул'] . '</td>
                        <td>' . $part['Название'] . '</td>
                        <td>' . (isFloat($part['Количество']) ? number_format($part['Количество'], 3, ',', ' ') : number_format($part['Количество'], 0, ',', ' ')) . '</td>
                        <td>' . (isFloat($part['Цена']) ? number_format($part['Цена'], 2, ',', ' ') : number_format($part['Цена'], 0, ',', ' ')) . ($part['Скидка'] ? ' <span style="color: #00adef">*</span>' : '') . '</td>
                    </tr>';
            }
        }

    }
    $html .= '
<style>
.typeahead.dropdown-menu
{
list-style: none;
}
#content .right_column ul.typeahead.dropdown-menu li {
    list-style-type: none !important;
}
</style>



    <div class="row">

         <form  method="get" action="">';
    if (!empty($codes)) {
        foreach ($codes as $articul) {
            $html .= '<div class="input-append articul-row"><input pattern="^[A-Za-z0-9]+$" class="input-xxlarge search-articul" style="text-transform: uppercase;" name="articul[]" type="text" value="' . $articul . '"  placeholder="Введите артикул" autocomplete="off">
                            <button type="button" class="btn add-articul">Добавить</button>';
            if ($articul != $codes[0]) {
                $html .= '<button type="button" class="btn remove-articul">Удалить</button>';
            }
            $html .= '</div>';
        }
    } else {
        $html .= '<div class="input-append articul-row">
<input pattern="^[A-Za-z0-9]+$" class="input-xxlarge search-articul" style="text-transform: uppercase;" name="articul[]" type="text" value=""  placeholder="Введите артикул" autocomplete="off">
<button type="button" class="btn add-articul" >Добавить</button>
</div>';
    }

    $html .= '
 <button type="submit" class="btn btn-info" style="margin:5px;">Найти запасные части</button>

        </form>
        <div class="row">
            <div class="col-md-12">
                <p class="example"><strong>Пример:</strong> ' . $example . '</p>
            </div>
        </div>
    </div>

<div class="row">
 <div class="col-md-12">
 <div class="table-responsive">
 <table class="table table-bordered">
 <thead>
 <tr>
 <th style="background-color: #65696e;color: #ffffff;text-align:center !important; vertical-align: middle !important">Артикул детали</th>
 <th style="background-color: #65696e;color: #ffffff;text-align:center !important; vertical-align: middle !important">Наименование</th>
 <th style="background-color: #65696e;color: #ffffff;text-align:center !important;">Наличие на складе <br/> на ' . $lastUpdate->format('d.m.Y') . '</th>
 <th style="background-color: #65696e;color: #ffffff;text-align:center !important;">Цена в тенге <br/> на ' . $lastUpdate->format('d.m.Y') . '</th>
 </tr>
 </thead>
 <tbody>' . $articulRows . '</tbody>
 </table>
 <p>* - товары со скидкой</p>
 </div>
 </div>
 </div>
 <script src="/js/libs/bootstrap-typeahead.js"></script>

';
}

$pageId = null;

if (!empty($_GET['pageId'])) {
    $pageId = $_GET['pageId'];
}

if ($pageId == 5367) {
    $resMsg = '';
    $admin_email = !empty ($_POST['email-to']) ? $_POST['email-to'] : null;
    $admin_copy = !empty($_POST['email-to-copy']) ? $_POST['email-to-copy'] : null;

    $attach = !empty($_FILES['attach']) ? $_FILES['attach'] : null;

    include_once(_FILES_ABS_ . '/mail.php');
    $smail = new mime_mail();
    $smail->from = 'admin@' . $_SERVER['HTTP_HOST'];
    $smail->subject = 'Заявка - ' . $form_themes[$current_theme] . ', ' . $_SERVER['HTTP_HOST'];
    $smail->to = $admin_email;

    if (!empty($admin_copy)) {
        $smail->headers = 'Cc: ' . $admin_copy;
    }

    if (!empty($attach)) {
        $uploaddir = __DIR__ . '/cms/uploads/';
        $uploadfile = $uploaddir . basename('attach.jpg');
        if (move_uploaded_file($attach['tmp_name'], $uploadfile)) {
            $smail->add_attachment(file_get_contents($uploadfile));
        }
    }

    $general_body = '';

    if (!empty($_POST['surname']) && !empty($_POST['name']) && !empty($_POST['tel']) && !empty($_POST['mail'])) {
        $general_body = '
        <p>Отправлено: ' . date('d.m.Y') . ' в ' . date('h:i') . ' с IP ' . $_SERVER['REMOTE_ADDR'] . '</p>        
        <p>'.$_POST['call_name'].' '.$_POST['name'].' '.@$_POST['middleName'].' ' .$_POST['surname'] . '</p>
        <p>Телефон: '.$_POST['tel'].'</p>
        <p>Email: '.@$_POST['mail'].'</p>
    ';
    }



    switch ($current_theme) {
        case 2:
            if (!empty($_POST['klass']) && !empty($_POST['date']) && !empty($_POST['time'])) {
                $smail->body = '
                     <html>
            <body>
                '.$general_body.'
                <p>Класс: ' . $_POST['klass'] . '</p>
				<p>Дата посещения: '.$_POST['date'].'</p>
				<p>Время посещения: '.$_POST['time'].'</p>
            </body>
        </html>
                ';
            }
        break;
        case 3:
            if (!empty($_POST['detali']) && !empty($_POST['year']) && !empty($_POST['vin'])) {
                $smail->body = '
                    <html>
            <body>
                '.$general_body.'
                <p>Детали: ' . $_POST['detali'] . '</p>
				<p>Год выпуска: '.$_POST['year'].'</p>
				<p>Вин: '.$_POST['vin'].'</p>
            </body>
        </html>
                ';
            }
        break;
        case 4:
            if (!empty($_POST['klass']) && !empty($_POST['year']) && !empty($_POST['vin'])
            && !empty($_POST['date']) && !empty($_POST['service_work_description']) && !empty($_POST['time'])) {
                $smail->body = '
                    <html>
            <body>
                '.$general_body.'
                <p>Класс автомобиля: ' . $_POST['klass'] . '</p>
				<p>Год выпуска: '.$_POST['year'].'</p>
				<p>Вин: '.$_POST['vin'].'</p>
				<p>Дата посещения: '.$_POST['date'].'</p>
				<p>Время посещения: '.$_POST['time'].'</p>
				<p>Необходимые сервисные работы: '.$_POST['service_work_description'].'</p>
            </body>
        </html>
                ';
            }
        break;
        case 5:case 6:case 7:
            if (!empty($_POST['description'])) {
                $smail->body = '
                <html>
            <body>
                '.$general_body.'
				<p>Вопрос: '.$_POST['description'].'</p>
            </body>
        </html>
                ';
            }
        break;
        default:
            if (!empty($_POST['submit']) && !empty($_POST['question']) && !empty($_POST['klass'])) {
                $smail->body = '
        <html>
            <body>
                '.$general_body.'
                <p>Класс: ' . $_POST['klass'] . '</p>
				<p>Вопрос: '.$_POST['question'].'</p>
            </body>
        </html>';
            }
        break;
    }


    # Добавляем емайл в базу
    if (!empty($_POST['mail'])) {
        $mail = $_POST['mail'];
        $object = array(
            'active' => 1,
            'name' => $mail,
            'head' => $subscribe_root_id,
            'class_id' => $class_id,
            'sort' => time()
        );

        $fields = array(
            18 => $mail
        );

        if (!$list = $api->objects->getFullObjectsListByClass($subscribe_root_id, $class_id, "AND o.active AND c.field_18='$mail'")) {
            $api->objects->createObjectAndFields($object, $fields);
        }
    }

    # отправляем
    if (!empty($_POST['submit']) && !empty($_POST["g-recaptcha-response"])) {
        $responce = $_POST["g-recaptcha-response"];
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $data = array(
            'secret' => '6LcltEcUAAAAAMMxW6ATxC4_8D8sCKVcp8VN_fh7',
            'response' => $responce
        );
        $options = array(
            'http' => array (
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                    "User-Agent:MyAgent/1.0\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success=json_decode($verify);
        if ($captcha_success->success == true) {
            if ($smail->send($smail->to)) {
                unset($_POST);
                $resMsg = '<span class="result" style="color:green;">Заявка успешно отправлена!</span>';
            }
        }
    } else {
        if (!empty($_POST['submit']) && empty($_POST['g-recaptcha-response'])) {
            $resMsg = '<span style="color:red!important">Необходимо подтвердить, что Вы не робот!</span>';
        } else {
            $resMsg = '';
        }
    }

    $themes = array('<select required="required" id="form-ajax-select" name="theme">');

    foreach ($form_themes as $item => $theme) {
        $themes[]  = '<option '.($current_theme == $item ? 'selected' : '').' value="'.$item.'">'.$theme.'</option>';
    }

    $themes[] = '</select>';
}


$api->header(array('page-title' => $title));
?>

    <div id="page_text">

        <figure class="page_cols">

            <div class="left_column">
                <div class="wrap">
                    <?= $leftMenu ?>
                </div>
            </div>

            <div class="right_column">
                <div class="cont">
                    <div class="main_title">
                        <h1><?= $title ?></h1>
                    </div>

                    <div class="simple_text clearfix">
                        <?php if ($pageId != 5367) : ?>
                            <?= $html ?>
                        <?php endif;?>
                        <?php if ($pageId == 5367) : ?>
                            <div id="page_drive">
                                <div class="cont">
                                    </br>
                                    <div class="main-title">
                                        <p>
                                            <!--o:5423-->
                                        </p>
                                    </div>

                                    <figure class="block_form" style="margin-left:0!important; border-top: none!important;">
                                        <?php echo @$resMsg ?>
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="rows">
                                                <label class="f_lab">Тема письма<span>*</span></label>
                                                <?php echo join("\n", $themes) ?>
                                            </div>
                                            <div class="ajax-part-multiform">
                                                <?php require_once('_forms/_form_'.$current_theme.'.php');?>
                                            </div>
                                            <div class="form_cols">
                                                <div class="form_one">
                                                    <div class="tit">Персональные данные</div>
                                                    <div class="rows">
                                                        <label class="f_lab">Господин/Госпожа<span>*</span></label>
                                                        <select name="call_name" required>
                                                            <option value="Господин">Господин</option>
                                                            <option value="Госпожа">Госпожа</option>
                                                        </select>
                                                    </div>
                                                    <div class="rows">
                                                        <label class="f_lab">Фамилия<span>*</span></label>
                                                        <input required="required" type="text" name="surname" class="f_text" value="<?php echo @$_POST['surname'] ?>"/>
                                                    </div>
                                                    <div class="rows">
                                                        <label class="f_lab">Имя<span>*</span></label>
                                                        <input required="required" type="text" name="name" class="f_text" value="<?php echo @$_POST['name'] ?>"/>
                                                    </div>
                                                    <div class="rows">
                                                        <label class="f_lab">Отчество</label>
                                                        <input type="text" name="middleName" class="f_text" value="<?php echo @$_POST['middleName'] ?>"/>
                                                    </div>
                                                    <div class="rows">
                                                        <label class="f_lab">Телефон<span>*</span></label>
                                                        <input required="required" name="tel" class="f_text service-phone" style="width: 233px!important;" value="' . @$_POST['tel'] . '"/>
                                                    </div>
                                                    <div class="rows">
                                                        <label class="f_lab">Email<span>*</span></label>
                                                        <input required="required" type="text" name="mail" class="f_text" value="<?php echo @$_POST['mail'] ?>"/>
                                                    </div>
                                                    <div class="rows">
                                                        <div style="margin-left: 65px;" class="g-recaptcha" data-sitekey="6LcltEcUAAAAAPOBq04NgG84yjb5iv8BbzpqeTo6"></div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="rows checks">
                                                <div>
                                                    <input type="checkbox" value="agree" required="required" id="chPer" class="f_check" name="iAgree" <?=@$_POST['iAgree']?'checked':''?>/>
                                                    <label for="chPer"> Согласен с обработкой персональных данных [1]</label>
                                                </div>
                                            </div>

                                            <div class="rows">
                                                <input type="submit" name="submit"  class="f_submit" value="Отправить письмо"/>
                                            </div>

                                            <div class="snoski">
                                                <span>[1]</span>
                                                <div class="text">
                                                    В соответствии с Законом Республики Казахстан от 21 мая 2013 года  № 94-V “О персональных данных и их защите” и иными нормативными правовыми актами Республики Казахстан, я даю свое безусловное согласие ТОО «Голубая звезда Казахстана» на сбор, обработку, использование, обезличивание, распространение и трансграничную передачу моих персональных данных, таких как фамилия; имя; отчество; год; месяц, дата и место рождения; адрес, номер паспорта и сведения о дате выдачи паспорта и выдавшем его органе; образование, профессия, место работы и должность; домашний, рабочий и мобильный телефоны; адрес электронной почты. При этом сбор, обработка, использование, обезличивание, распространение и трансграничная передача моих персональных данных должна производится не противоречащими законодательству способами, в целях, связанных с возможностью предоставления информации о товарах и услугах, которые потенциально могут представлять интерес, а также в целях сбора и обработки статистической информации и проведения маркетинговых исследований, и в источниках, в том числе общедоступных.
                                                </div>
                                            </div>
                                        </form>
                                    </figure>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>

                    <figure class="social_icons_inner">
                        <?= $api->socIconsMenu() ?>
                    </figure>
                </div>

            </div>

        </figure>


    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($('#hidden-link')) {
                $("#hidden-link").fancybox().trigger('click');
            } else {
                alert('Не тот раздел');
            }
        });
    </script>

<?
function isFloat($value)
{
    return ((int)$value != $value);
}

$api->footer();
?>