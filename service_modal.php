<?
include('cms/public/api.php');

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

    if(($obj=$api->objects->getFullObject(5420)) && (trim($obj['Значение'])!='')){
        $smail->to=trim($obj['Значение']);
    }else{
        $smail->to='naivlife@mail.ru';
    }

    $smail->from 	= 'admin@'.$_SERVER['HTTP_HOST'];
    $smail->subject	= 'Заявка на сервис '.$_SERVER['HTTP_HOST'];
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

    # отправляем
    $smail->send($smail->to);

    unset($_POST);

    $resMsg = '<span class="result" style="color:green;">Заявка успешно отправлена!!!</span>';
}

?>
    