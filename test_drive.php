<?
include('cms/public/api.php');
$api->header(array( 'page-title' => 'Заявка на тест-драйв' ));

$subscribe_root_id = 5465;
$class_id = 7;

$resMsg = '';

if (
    @$_POST['submit']
    && @$_POST['city'] && ($city = $_POST['city'])
    && @$_POST['klass'] && ($klass = $_POST['klass'])
    # && @$_POST['model'] && ($model = $_POST['model'])
    && @$_POST['date'] && ($date = $_POST['date'])
    //&& @$_POST['hour'] && ($hour = $_POST['hour'])
    //&& @$_POST['minute'] && ($minute = $_POST['minute'])
    # && @$_POST['asking'] && ($asking = $_POST['asking'])
    && @$_POST['surname'] && ($surname = $_POST['surname'])
    && @$_POST['name'] && ($name = $_POST['name'])
    && @$_POST['mail'] && ($mail = $_POST['mail'])
	&& @$_POST['tel'] && ($tel = $_POST['tel'])

    && @$_POST['iAgree'] && ($iAgree = $_POST['iAgree'])
) {

    # Подключаем почтовый класс
    include_once(_FILES_ABS_.'/mail.php');
    $smail = new mime_mail();

    if(($obj=$api->objects->getFullObject(5419)) && (trim($obj['Значение'])!='')){
        $smail->to=trim($obj['Значение']);
    }else{
        $smail->to='naivlife@mail.ru';
    }

    $smail->from 	= 'admin@'.$_SERVER['HTTP_HOST'];
    $smail->subject	= 'Заявка на тест драйв на сайте '.$_SERVER['HTTP_HOST'];
    $smail->body	= '
        <html>
            <body>
                <p>Отправлено: '.date('d.m.Y').' в '.date('h:i').' с IP '.$_SERVER['REMOTE_ADDR'].'</p>
                <p>Город - '.$city.'</p>
                <p>Класс - '.$klass.'</p>
                <!--<p>Модель - '.@$model.'</p>-->
                <p>Дата - '.$date.'</p>
                <!--<p>Обращаться - '.@$asking.'</p>-->
                <p>Фамилия - '.$surname.'</p>
                <p>Имя - '.$name.'</p>
                '.(@$_POST['middleName']?'
                    <p>Отчество - '.$_POST['middleName'].'</p>
                ':'').'
                <p>Email - '.$mail.'</p>
				<p>Телефон - '.$tel.'</p>
                '.(@$_POST['wantGetInfo'] == 'on'?'
                    <p>Получать информацию о новых продуктах и специальных предложен - Да</p>
                ':'').'
            </body>
        </html>';
	
	# Добавляем емайл в базу
	
	$object = array(
		'active'=>1,
		'name'=>$mail,
		'head' => $subscribe_root_id,
		'class_id' => $class_id,
		'sort'=>time()
	);
	
	$fields = array(
		18 => $mail
	);
	
	if(!$list = $api->objects->getFullObjectsListByClass($subscribe_root_id, $class_id, "AND o.active AND c.field_18='$mail'")){
		$api->objects->createObjectAndFields($object, $fields);
	}
	
	

    # отправляем
	if($smail->send($smail->to)){
		$resMsg = '<span class="result" style="color:green;">Заявка на тест драйв успешно отправлена!!!</span>';
	}else{
		$resMsg = '<span class="result" style="color:red;">Что то пошло не так и заявку не удалось отправить</span>';
	}


    //unset($_POST);


}

?>
    <div id="page_drive">
        <figure class="page_cols">

            <div class="left_column">
                <div class="wrap">
                    <figure class="banner">
                        <div class="title"><!--object:[401][113]--></div>
                        <a href="<!--object:[401][114]-->">
                            <img src="<?=_IMG_?>?w=230&url=<?=_UPLOADS_?>/<!--object:[401][115]-->" width="230"/>
                        </a>
                        <div class="desc">
                            <div class="text"><!--object:[401][117]--></div>
                            <a href="<!--object:[401][114]-->" class="in2">Узнайте подробности</a>
                        </div>
                        <!--smart:{ id:401, title:"Баннера", actions:["edit"], css:{ position:"absolute" } }-->
                    </figure>
                </div>
            </div>

            <div class="right_column">
                <div class="cont">

                    <div class="main_title">
                        <h1><!--#page-title#--></h1>
                    </div>
                    <div class="drive">
                        <img src="/source/ban3.jpg"/>
                        <div class="text">Зарегистрироваться для участия в тест-драйве Вы можете через он-лайн форму.<br/>Поля, отмеченные , обязательны для заполнения. </div>
                    </div>

                    <?=(!empty($_POST)?$resMsg:'');?>

                    <figure class="block_form">
                        <form method="POST" onsubmit="return formCheck(this)">
                            <div class="tit">Дата тест-драйва</div>
                            <div class="rows">
                                <label class="f_lab">Город<span>*</span></label>
                                <select name="city">
                                    <option value="">Выберите город</option>
                                    <?
                                        $cityObj = $api->objects->getFullObject(395);
                                        $city = explode("\n", $cityObj['Значение']);
                                        foreach ($city as $co) {
                                            $checked = '';
                                            if ($co == @$_POST['city'])
                                                $checked = 'selected';
                                            echo '<option value="'.$co.'" '.$checked.'>'.$co.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="rows">
                                <label class="f_lab">Класс<span>*</span></label>
                                <select name="klass">
                                    <option value="">Выберите класс</option>
                                    <?
                                        $cityObj = $api->objects->getFullObject(396);
                                        $city = explode("\n", $cityObj['Значение']);
                                        foreach ($city as $co) {
                                            $checked = '';
                                            if ($co == @$_POST['klass'])
                                                $checked = 'selected';
                                            echo '<option value="'.$co.'" '.$checked.'>'.$co.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <!--<div class="rows">
                                <label class="f_lab">Выберите модель<span>*</span></label>
                                <select name="model">
                                    <option value="">Выберите модель</option>
                                    <?
                                        $cityObj = $api->objects->getFullObject(397);
                                        $city = explode("\n", $cityObj['Значение']);
                                        foreach ($city as $co) {
                                            $checked = '';
                                            if ($co == @$_POST['model'])
                                                $checked = 'selected';
                                            echo '<option value="'.$co.'" '.$checked.'>'.$co.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>-->
                            <div class="rows">
                                <label class="f_lab">Начало тест-драйва<span>*</span></label>
                                <a href="#" class="calend"><img src="/img/cal.jpg"/></a>
                                <input type="text" name="date" style="width:214px!important" class="date-picker f_text f_date" value="<?=@$_POST['date']?>"/>

                                <!--<select name="hour" class="f_date">
                                    <option></option>
                                    <?
                                    $cityObj = $api->objects->getFullObject(398);
                                    $city = explode("\n", $cityObj['Значение']);
                                    foreach ($city as $co) {
                                        $checked = '';
                                        if ($co == @$_POST['hour'])
                                            $checked = 'selected';
                                        echo '<option value="'.$co.'" '.$checked.'>'.$co.'</option>';
                                    }
                                    ?>
                                </select>-->

                                <!--<select name="minute" class="f_date">
                                    <option></option>
                                    <?
                                    $cityObj = $api->objects->getFullObject(399);
                                    $city = explode("\n", $cityObj['Значение']);
                                    foreach ($city as $co) {
                                        $checked = '';
                                        if ($co == @$_POST['minute'])
                                            $checked = 'selected';
                                        echo '<option value="'.$co.'" '.$checked.'>'.$co.'</option>';
                                    }
                                    ?>
                                </select>-->
                            </div>
                            <div class="form_cols">
                                <div class="form_one">
                                    <div class="tit">Персональные данные</div>
                                    <!--<div class="rows">
                                        <label class="f_lab">Обращение<span>*</span></label>
                                        <input type="radio" class="f_radio" id="mr" name="asking" value="Господин" checked/>
                                        <label  for="mr">Господин</label>
                                        <input type="radio" class="f_radio" id="miss" name="asking" value="Госпожа"/>
                                        <label for="miss">Госпожа</label>
                                    </div>-->
                                    <div class="rows">
                                        <label class="f_lab">Фамилия<span>*</span></label>
                                        <input type="text" name="surname" class="f_text" value="<?=@$_POST['surname']?>"/>
                                    </div>
                                    <div class="rows">
                                        <label class="f_lab">Имя<span>*</span></label>
                                        <input type="text" name="name" class="f_text" value="<?=@$_POST['name']?>"/>
                                    </div>
                                    <div class="rows">
                                        <label class="f_lab">Отчество</label>
                                        <input type="text" name="middleName" class="f_text" value="<?=@$_POST['middleName']?>"/>
                                    </div>
                                    <div class="rows">
                                        <label class="f_lab">Email<span>*</span></label>
                                        <input type="text" name="mail" class="f_text" value="<?=@$_POST['mail']?>"/>
                                    </div>
									<div class="rows">
                                        <label class="f_lab">Телефон<span>*</span></label>
                                        <input type="number" onkeydown="var ar = String.fromCharCode(event.keyCode);if(!(ar>0 && ar<10) && (event.keyCode!=8) && !(ar >= 'a' && ar <= 'i') && (ar!='`')) return false; if(this.value.length>10  && (event.keyCode!=8)) return false;" name="tel" class="f_text" value="<?=@$_POST['tel']?>"/>
                                    </div>
                                </div>
                                <!--
                                <div class="form_two">
                                    <div class="tit">Ваш автомобиль</div>
                                    <div class="rows">
                                        <label class="f_lab">Марка </label>
                                        <select>
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="rows">
                                        <label class="f_lab">Модель </label>
                                        <select>
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="rows">
                                        <label class="f_lab">Год выпуска </label>
                                        <select>
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="rows">
                                        <label class="f_lab">Планируемая дата покупки нового автомобиля</label>
                                        <select>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                -->
                            </div>

                            <div class="rows checks">
                                <div>
                                    <input type="checkbox" id="chNew" class="f_check" name="wantGetInfo" <?=@$_POST['wantGetInfo']?'checked':''?>/>
                                    <label for="chNew">Я бы хотел получать информацию о новых продуктах и специальных предложен</label>
                                </div>
                                <div>
                                    <input type="checkbox" value="agree" id="chPer" class="f_check" name="iAgree" <?=@$_POST['iAgree']?'checked':''?>/>
                                    <label for="chPer"> Согласен с обработкой персональных данных [1]</label>
                                </div>
                            </div>
                            <div class="rows">
                                <input type="submit" name="submit" class="f_submit" value="Отправить"/>
                            </div>
                            <div class="snoski">
                                <span>[1]</span>
                                <div class="text">
                                    <!--o:400-->
                                    <!--smart:{ id:400, title:"текста", actions:["edit"] }-->
                                </div>
                            </div>
                        </form>
                    </figure>

                    <figure class="social_icons_inner">
                        <?=$api->socIconsMenu()?>
                    </figure>
                </div>
            </div>

        </figure>
    </div>

    <script type="text/javascript">
        function formCheck(f) {

            var data = {
                city : {
                    val: f.city.value,
                    er: '<span class="f_err er" style="display:none;">Необходимо выбрать город</span>'
                },
                klass : {
                    val: f.klass.value,
                    er: '<span class="f_err er" style="display:none;">Необходимо выбрать класс</span>'
                },
                /*model : {
                    val: f.model.value,
                    er: '<span class="f_err er" style="display:none;">Необходимо выбрать модель</span>'
                },*/
                date : {
                    val:{
                        date: f.date.value,
                        hour : f.hour.value,
                        minute : f.minute.value
                    },
                    er: '<span class="f_err er" style="display:none;">Укажите дату и время тест-драйва</span>'
                },
                surname : {
                    val: f.surname.value,
                    er: '<span class="f_err er" style="display:none;">Укажите Вашу фамилию.</span>'
                },
                name : {
                    val: f.name.value,
                    er: '<span class="f_err er" style="display:none;">Укажите Ваше имя.</span>'
                },
                mail : {
                    val: f.mail.value,
                    er: '<span class="f_err er" style="display:none;">Email не введен или введен не корректно</span>'
                },
				tel : {
                    val: f.tel.value,
                    er: '<span class="f_err er" style="display:none;">Номер телефона не введен</span>'
                },
                iAgree : {
                    val: f.iAgree.value,
                    er: '<span class="f_err er" style="padding-left: 0px;display:none;">Подтвердите своё согласие.</span>'
                }
            };

            var errors = 0;
            $('span.er').remove();

            for (var key in data) {

                var obj = data[key];
                var val = obj.val;
                var er = obj.er;
                var el = $('[name="' + key + '"]');

                if ( $.isPlainObject( val ) ) {
                    if (val.date == "") {
                        el.next().next().after(er).next().fadeIn();
                        errors = 1;
                        continue;
                    }
                    if (val.hour == "") {
                        el.next().next().after(er).next().fadeIn();
                        errors = 1;
                        continue;
                    }
                    if (val.minute == "") {
                        el.next().next().after(er).next().fadeIn();
                        errors = 1;
                        continue;
                    }
                    continue;
                }

                if ( key == 'iAgree' && !el.prop("checked") ) {
                    el.next().after(er).next().fadeIn();
                    errors = 1;
                    continue;
                }

                if (key == 'mail' && (val == "" || EmailCheck(val) == false) ) {
                    el.after(er).next().fadeIn();
                    errors = 1;
                    continue;
                }
				
				if (key == 'tel' && val == "" ) {
                    el.after(er).next().fadeIn();
                    errors = 1;
                    continue;
                }

                if (val == "") {
                    el.after(er).next().fadeIn();
                    errors = 1;
                }

            }
            if (errors == 0) {
                return true;
            } else {
                return false;
            }
        }
    </script>

<?
$api->footer();
?>