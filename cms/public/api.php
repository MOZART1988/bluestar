<?
/*
Title: РАСЧУДЕСНОЕ МНОГОЯЗЫКОВОЕ API-ШАБЛОНИЗАТОР С ДВОЙНОЙ БУФЕРИЗАЦИЕЙ ВЫВОДА, МОДУЛЬ-САЙД ИНКЛЮДАМИ И ВСПОМОГАТЕЛЬНЫМИ ЮЗЕР-ФУНКЦИЯМИ
Author: Derevyanko Mikhail <m-derevyanko@ya.ru>
Last UpDate: 21.05.2010
*/
error_reporting(E_ALL);
ini_set("display_errors", "On");
session_start();
include(str_replace("\\", "/", dirname(__FILE__)).'/../cfg.php');
include_once(_FILES_ABS_."/mysql.php");
include_once(_FILES_ABS_."/mail.php");
include_once(_FILES_ABS_."/appends.php");
include_once(_PUBLIC_ABS_."/objects.php");
include_once(_PUBLIC_ABS_."/strings.php");
include_once(_PUBLIC_ABS_."/simpleparser.php");
include_once(_PUBLIC_ABS_."/section.php");

class api extends appends{

    public $template;
    public $objects;
    public $section;
    public $strings;
    public $mail;
    public $body;
    public $arguments;
    public $lang;
    public $languages;
    public $models;

    function __construct() {
        parent::__construct();

        $this->template = '/pages.html';

        $this->body = null;
        $this->arguments = array();
        $this->languages = array(
            "ru"=>"Рус.",
            "en"=>"English",
            "kz"=>"Каз."
        );

        $this->lang = 'ru';

        if (!@$_GET['lang'])
            $_GET['lang'] = $this->lang;


        $this->objects = new objects( $this->lang );
        $this->strings = new Strings( $this->lang );

        $this->section = new Section($this->lang);
        $this->mail = new mime_mail();

        $this->models = $this->objects->getFullObjectsList($this->section->modelsHeadId);

        foreach ($this->models as $key => $model){
            if (isset($_GET['type']) && $_GET['type'] == 'amg'){
                if($model['AMG'] != 1){
                    unset($this->models[$key]);
                }
            }else{
                if(isset($model['AMG']) && $model['AMG'] == 1){
                    unset($this->models[$key]);
                }
            }
        }
    }

    function arg($name, $value){
        $this->arguments[$name] = $value;
    }

    function args($arr){
        if($arr) $this->arguments = array_merge($this->arguments, $arr);
    }

    function flush($buffer) {
        $this->content = explode("#CONTENT#", $buffer);
        $this->content = $this->content[0].$this->body.$this->content[1];
        #INIT HEAD
        $this->content = str_replace('<head>', "<head>\n".$this->initHead(), $this->content);

        $this->run();
        $temp = array();
        foreach ($this->arguments as $name => $value) {
            $temp['<!--#'.$name.'#-->'] = $value;
        }
        $this->content = strtr($this->content, $temp);

        #INIT ALL TYPES OF INSIDE OBJECTS
        $this->content = $this->convertSmartObjects($this->content);
        $this->content = $this->convertSimpleObjects($this->content);
        $this->content = $this->convertSimpleTransObjects($this->content);
        return $this->content;
    }

    function convertSmartObjects($buffer) {
        if(!!$this->auth()) return preg_replace_callback("/<!--\s*smart:(.*)\s*-->/sU", array($this, 'activateSmartObject'), $buffer);
        return preg_replace("/<!--\s*smart:(.*)\s*-->/sU", '', $buffer);
    }

    function activateSmartObject($ok) {
        $id = uniqid();
        $out = array( '<div id="div-'.$id.'"></div><div style="clear:both"></div>' );
        $out[]= $this->areaJS("fe.add( $('#div-".$id."'), ".str_replace("\n", "", $ok[1])." );");
        return join("\n", $out);
    }

    function convertSimpleObjects($buffer) {
        return preg_replace_callback("/<!--\s*object:(.*)\s*-->/sU", array($this, 'activateSimpleObject'), $buffer);
    }

    function activateSimpleObject($ok) {
        if(@!preg_match("/^\[(\d+)\]\[([^\]]+)\]$/", $ok[1], $p) || !($o = $this->objects->getFullObject($p[1], false)) || empty($o[$p[2]])) return '';
        return $o[$p[2]];
    }

    function convertSimpleTransObjects($buffer) {
        return preg_replace_callback("/<!--\s*o:(.*)\s*-->/sU", array($this, 'activateSimpleTransObject'), $buffer);
    }

    function activateSimpleTransObject($ok){
        if(@!preg_match("/^(\d+)$/", $ok[1], $p) || !($o = $this->objects->getFullObject($p[1], false)) || empty($o[18])) return '';
        return $o[18];
    }

    function header($args=array()){
        $this->args($args);
        ob_start(array($this, 'flush'));
        include(_HTML_ABS_.$this->template);
        ob_start();
        return true;
    }

    function footer(){
        $this->body = ob_get_contents();
        ob_end_clean();
    }

    function v($in_text, $lang = '', $city = ''){
        if ($lang == '') $lang = $this->lang;

        $this->vars = array(
            'ru' => array(
                'Главная' => 'Главная',
                'Новости' => 'Новости',
                'все новости' => 'все новости',
                'Карта сайта' => 'Карта сайта',
                'Вернуться в каталог' => 'Вернуться в каталог',
                'Назад' => 'Назад',
                'Вернуться на уровень выше' => 'Вернуться на уровень выше',
                'В перёд' => 'В перёд',
                'Войти' => 'Войти',
                'Регистрация' => 'Регистрация',
                'Выйти' => 'Выйти',
                'Авторизация пользователя' => 'Авторизация пользователя',
                'Пароль' => 'Пароль',
                'Забыли пароль?' => 'Забыли пароль?',
                'тг.' => 'тг.',
                'Товаров' => 'Товаров',
                'На сумму' => 'На сумму',
                'Подробнее' => 'Подробнее',
                'создание сайта' => 'создание сайта',
                'Не верный логин или пароль!' => 'Не верный логин или пароль!',
                'Проголосовали' => 'Проголосовали',
                'Голосовать' => 'Голосовать',
                'Посмотреть все результаты' => 'Посмотреть все результаты',
                'Назад к голосованию' => 'Назад к голосованию',
                'Цена' => 'Цена',
                'тг' => 'тг',
                'в корзину' => 'в корзину',
                'Найти' => 'Найти',
                'Отмена' => 'Отмена',
                'Товары не найдены' => 'Товары не найдены',
                'Товар успешно добавлен в корзину.' => 'Товар успешно добавлен в <a href="'.$city.'/'.$lang.'/rycle/">корзину</a>.',
            ),
            'en' => array(
                'Главная' => 'Main',
                'Новости' => 'News',
                'все новости' => 'all news',
                'Карта сайта' => 'Site map',
                'Вернуться в каталог' => 'Back to catalog',
                'Назад' => 'Back',
                'Вернуться на уровень выше' => 'Back to previous level',
                'В перёд' => 'Next',
                'Войти' => 'Signin',
                'Регистрация' => 'Register',
                'Выйти' => 'Logout',
                'Авторизация пользователя' => 'Authorization',
                'Пароль' => 'Password',
                'Забыли пароль?' => 'Forgot?',
                'тг.' => 'tg.',
                'Товаров' => 'Goods',
                'На сумму' => 'Summ',
                'Подробнее' => 'More',
                'создание сайта' => 'designed by',
                'Не верный логин или пароль!' => 'Wrong login or password!',
                'Проголосовали' => 'Voted',
                'Голосовать' => 'Vote',
                'Посмотреть все результаты' => 'View all results',
                'Назад к голосованию' => 'Back to voting',
                'Цена' => 'Price',
                'тг' => 'tg',
                'в корзину' => 'to cart',
                'Найти' => 'Search',
                'Отмена' => 'Reset',
                'Товары не найдены' => 'Products not found',
                'Товар успешно добавлен в корзину.' => 'Goods at the <a href="'.$city.'/'.$lang.'/rycle/">basket</a>.',
            ),
            'kz' => array(
                'Главная' => 'Басқы бет',
                'Новости' => 'Жаңалықтар',
                'все новости' => 'барлық жаңалықтар',
                'Карта сайта' => 'Сайт картасы',
                'Вернуться в каталог' => 'Каталогқа оралу',
                'Назад' => 'Ілгері',
                'Вернуться на уровень выше' => 'Ілгері',
                'В перёд' => 'Алға',
                'Войти' => 'Кіру',
                'Регистрация' => 'Тіркелу',
                'Выйти' => 'Шығу',
                'Авторизация пользователя' => 'Сайтқа кіру',
                'Пароль' => 'Пароль',
                'Забыли пароль?' => 'Пароліңізді ұмытып қалдыңыз ба?',
                'тг.' => 'тг.',
                'Товаров' => 'Тауарлар',
                'На сумму' => 'Бағасы',
                'создание сайта' => 'жасаған',
                'Не верный логин или пароль!' => 'Логин немесе пароліңіз қате!',
                'Проголосовали' => 'Дауыс бергендер',
                'Голосовать' => 'Дауыс беру',
                'Посмотреть все результаты' => 'Жауаптарды көру',
                'Назад к голосованию' => 'Дауыс беруге қайта оралу',
                'Цена' => 'Бағасы',
                'тг' => 'тг',
                'в корзину' => 'қоржынға салу',
                'Найти' => 'Iздеу',
                'Отмена' => 'Тазарту',
                'Товары не найдены' => 'Тауарлар табылған жоқ',
                'Товар успешно добавлен в корзину.' => 'Тауарлар <a href="'.$city.'/'.$lang.'/rycle/">қоржында</a>.',
            ),
        );

        if ($lang != '') $lang = $lang; else $lang = $this->lang;

        return $this->vars[$lang][$in_text];
    }

    #ЭТА НИФИГОВАЯ ФУНКЦИЯ ДЕЛАЕТ ИНКЛЮДЫ СТИЛЕЙ И ЖОВОСКРИПТА В САМЫЙ ВЕРХ
    function initHead(){
        $title = ( $_SERVER['PHP_SELF'] != '/index.php'? '<!--#page-title#--> &mdash; ' : '' ) . '<!--object:[5][18]-->';
        $keywords = '<!--object:[6][18]-->';
        $description = '<!--object:[7][18]-->';
        
        if (!empty($_REQUEST['pageId']) or !empty($_REQUEST['cat'])){
        $id=(!empty($_REQUEST['pageId']) ? $_REQUEST['pageId'] : @$_REQUEST['cat']);
            if ($obj = $this->objects->getFullObject($id)){
                if (!empty($obj['title'])){
                    $title = $obj['title'];
                }
                if (!empty($obj['keywords'])){
                    $keywords = $obj['keywords'];
                }
                if (!empty($obj['description'])){
                    $description = $obj['description'];
                }
            }
        }
        $files = array(
            '<meta http-equiv="content-type" content="text/html; charset=utf-8" />',
                '<title>'.$title.'</title>',
            '<meta name="author" content="artmedia.kz">',
            '<meta name="copyright" content="artmedia.kz">',
           '<meta name="keywords" content="'.$keywords.'">',
              '<meta name="description" content="'.$description.'">',
            '<meta name="Publisher-Email" content="info@artmedia.kz">',
            '<meta name="Publisher-URL" content="http://artmedia.kz/">',
            '<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />',
            '<link rel="icon" href="/favicon.ico" type="image/x-icon" />',
            '<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />',

            '<link rel="stylesheet" href="'._WWW_.'/style/libs/bootstrap.min.css" type="text/css" media="screen, projection" />',
            '<link rel="stylesheet" type="text/css" href="/js/timepicker/lib/bootstrap-datepicker.css">',
            '<link rel="stylesheet" type="text/css" href="/js/timepicker/jquery.timepicker.min.css">',
            '<link rel="stylesheet" href="'._WWW_.'/style/screen.css" type="text/css" media="screen, projection" />',
			'<link rel="stylesheet" type="text/css" href="/style/config.css">',

            '<script type="text/javascript" src="'._WWW_.'/js/jquery.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/libs/bootstrap.min.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/jquery.easing.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/jquery.bxslider.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/inter.js"></script>',

            '<script type="text/javascript" src="'._WWW_.'/js/flash.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/ui/ui.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/jquery.placeholder.min.js"></script>',
            '<link href="'._WWW_.'/js/ui/ui.css" rel="stylesheet" type="text/css" />',
            '<script type="text/javascript" src="'._WWW_.'/cms/public/validate.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/init.js" charset="UTF-8"></script>',
			'<script type="text/javascript" src="/js/configurator.js" charset="UTF-8"></script>',

            '<script type="text/javascript" src="'._WWW_.'/js/fancybox/jquery.fancybox-1.3.0.pack.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/fancybox/jquery.easing-1.3.pack.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/fancybox/jquery.mousewheel-3.0.2.pack.js"></script>',
            '<link rel="stylesheet" href="'._WWW_.'/js/fancybox/jquery.fancybox-1.3.0.css" type="text/css" media="screen">',
            '<!--[if lt IE 10]><script type="text/javascript" src="'._WWW_.'/js/PIE.js"></script><![endif]-->',
            '<script type="text/javascript" src="'._FILES_.'/appends/ckeditor/ckeditor.js"></script>',
            '<script type="text/javascript" src="'._FILES_.'/appends/ckeditor/adapters/jquery.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/maskedinput.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/js/timepicker/jquery.timepicker.min.js"></script>',
            '<script src=\'https://www.google.com/recaptcha/api.js\'></script>',
//            '<!--[if IE]> <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->'
        );

        $out = array();
        $out[]=$this->areaJS('var _LANG_ = "'.$this->lang.'", ajaxFile = "'._AJAX_FILE_.'";');
        $out[]=$this->areaJS('var _NOWORD_ = "<!--o:40-->", _TOOLONG_ = "<!--o:41-->", _SEARCHWORD_ = "<!--o:39-->";');
        foreach($files as $file){
            $out[]=$file;
        }

        if(!$this->auth()) return join("\n", $out);
        $files = array(
            '<script type="text/javascript" src="'._FILES_.'/appends/ckfinder/ckfinder.js"></script>',
            '<script type="text/javascript" src="'._WWW_.'/frontEnd/js.js" charset="utf-8"></script>',
            '<link href="'._WWW_.'/frontEnd/css.css" rel="stylesheet" type="text/css" />'
        );
        $out[]=$this->areaJS('var _ROOT_ = "'._ROOT_.'", _UPLOADS_ = "'._UPLOADS_.'", _WWW_ = "'._WWW_.'", _FILES_ = "'._FILES_.'", ajaxFeFile = "'._WWW_.'/frontEnd/ajax.php";');
        foreach($files as $file){
            $out[]=$file;
        }
        return join("\n", $out);
    }

    #USE IT FOR ACTIONS BY DEFAULT;
    function run(){
        $this->arg('auth-block', $this->authBlock());
        $this->arg('menu', $this->getMenu(4));
        $this->arg('newsList', $this->newsList(9, 8));
        $this->arg('banners-list-1', $this->bannersList(2, 6, 7));
        $this->arg('banners-list-2', $this->bannersList(22, 6, 7));
        $this->arg('voting-block', $this->votingBlock(12));
        $this->arg('langs', $this->getLangs());
        $this->arg('search', $this->search());
        $this->arg('subscribe', $this->subscribeBlock());
    }
################
#USER FUNCTIONS#
################

    # ---- ЛЕВОЕ МЕНЮ для простых текстовых страниц ------
    function getLeftMenu($id, $href, $get = 0) {

        if ($this->section->sectionName == 'truck') {
            $txtPage = 1;
            $fileId = 87;
            $linkId = 2;

            if ($list = $this->objects->getFullObjectsList($id)) {
                $out = array();

                foreach ($list as $o) {

                    # ---- определяем класс пункта -----
                    $class = '';
                    if (@$get == $o['id']) {
                        $class = 'class="active"';
                    }



                    # ---- если текстовая (программа кредитования) -----
                    if ( $o['class_id'] == $txtPage ) {
                        $name = !empty($o['Название в левом Меню'])?$o['Название в левом Меню']:$o['Название'];
                        $out[] = '<li '.$class.'><a href="'.$href.$o['id'].'/">'.$name.'</a></li>';
                    }

                    # ---- если ссылка  ----------
                    if ( $o['class_id'] == $linkId) {
                        $out[] = '<li '.$class.'><a href="/'.$this->lang.'/'.$this->section->sectionName.$o['Ссылка'].'/">'.$o['Название'].'</a></li>';
                    }

                    # ---- если ссылка на файл ----------
                    if ( $o['class_id'] == $fileId) {
                        $out[] = '<li '.$class.'>
                                <a href="/cms/uploads/'.$o['Загрузка'].'" target="_blank">
                                    Брошюра
                                </a>
                            </li>';
                    }

                    if(strpos($_SERVER['REQUEST_URI'], 'today_sale') !== false) {
                        if ($o['class_id'] == 53) {
                            $class = $o['id'] === @$_GET['category_id'] ? 'class="active"' : '';

                            $out[] = '<li '.$class.'><a href="/'.$this->lang.'/'.$this->section->sectionName.'/today_sale/'.$o['id'].'/">'.$o['Название'].'</a></li>';
                        }
                    }


                }
                return implode("\n", $out);
            }

        }

        $txtPage = 1;
        $sectionId = 7;
        $linkId = 2;

        if ($list = $this->objects->getFullObjectsList($id)) {
            $out = array();

            foreach ($list as $o) {

                # ---- определяем класс пункта -----
                $class = '';
                if (@$get == $o['id'])
                    $class = 'class="active"';

                # ---- если каталог -----
                if ($o['class_id'] == $sectionId ) {

                    $progSubMenu = '';
                    if ($o['inside'] > 0 ) {
                        $progSubMenu = '<ul>'.$this->getLeftMenu($o['id'], $href, $get).'</ul>';

                        if ( $this->getChildId($o['id'], $get) )
                            $class = 'class="active"';
                    }

                    $out[] = '<li '.$class.'><a href="#" onclick="return false;">'.$o['Значение'].'</a>'.$progSubMenu.'</li>';
                    continue;
                }
                # ---- если текстовая (программа кредитования) -----
                if ( $o['class_id'] == $txtPage ) {
						$name = !empty($o['Название в левом Меню'])?$o['Название в левом Меню']:$o['Название'];
						$out[] = '<li '.$class.'><a href="'.$href.$o['id'].'/">'.$name.'</a></li>';
                }
                # ---- если ссылка на файл ----------
                if ( $o['class_id'] == $linkId) {
                    $out[] = '<li '.$class.'><a href="/'.$this->lang.'/'.$this->section->sectionName.$o['Ссылка'].'/">'.$o['Название'].'</a></li>';
                }

                if(strpos($_SERVER['REQUEST_URI'], 'today_sale') !== false) {
                    if ($o['class_id'] == 53) {
                        $class = $o['id'] === @$_GET['category_id'] ? 'class="active"' : '';

                        $out[] = '<li '.$class.'><a href="/'.$this->lang.'/'.$this->section->sectionName.'/today_sale/'.$o['id'].'/">'.$o['Название'].'</a></li>';
                    }
                }


            }
            return join("\n", $out);
        }
    }
	
	function getLeftMenuForToday($id, $class_id, $href){
		$last = $this->objects->last;
		$out = array();
		if($objects = $this->objects->getFullObjectsListByClass($id, $class_id, "AND o.active ORDER BY o.sort")){
			foreach($objects as $o){
				if($this->selected($o['id'], $last)){
					$name = !empty($o['Название в левом Меню'])?$o['Название в левом Меню']:$o['Название'];
					$out[] = '<li class="active"><a href="'.$href.'/'.$o['id'].'/">'.$name.'</a></li>';
				}else{
					$name = !empty($o['Название в левом Меню'])?$o['Название в левом Меню']:$o['Название'];
					$out[] = '<li><a href="'.$href.$o['id'].'/">'.$name.'</a></li>';
				}	
			}
		}
		
		return (!empty($out)?join("\n",$out):false);
	}

    # ---- ПРОВЕРКА НА СУЩЕСТВОВАНИЕ Дочерних ID
    function getChildId( $head, $needId ) {
        $tmp = $this->db->select("`objects`", "WHERE `head`='$head' ", "`id`");
        if ( in_array($needId, $tmp) )
            return true;
        else
            return false;
    }

    # ---- ФОРМИРОВАНИЕ html ССЫЛКИ НА ФАЙЛ С РИСУНКОМ ----
    # ---- Входящие данные:
    # ---- ID
    # ---- Двумерный массив
    # ---- Один ОБЬЕКТ
    function getHtmlLinkToFileListOrItem($var) {

        # ------ Если это массив -----
        if ( is_array($var) ) {

            # ------- Если один обьект --------
            if ( !is_array($var[0]) ) {
                return $this->getLinkToFileHtmlItem($var, '230', '60');
            }

            # ------- Если список обьектов ----
            else {
                $out = array();
                foreach ($var as $o) {
                    $out[] = $this->getLinkToFileHtmlItem($o, '230', '60');
                }
                return join("\n", $out);
            }
        }

        # ------ Если целое число --------
        elseif (!is_array($var) && is_numeric($var)) {
            $tmpObj = $this->objects->getFullObject($var);
            return $this->getLinkToFileHtmlItem($tmpObj, '230', '60');
        }
    }

    # --- Формирование Одного Обьекта ССЫЛКА НА ФАЙЛ С ОБЬЕКТОМ -------
    function getLinkToFileHtmlItem($obj, $w = false, $h = false) {

        $imgrSrc = '';
        if ($w && $h) {
            $imgrSrc = 'src="'._IMGR_.'?w='.$w.'&h='.$h.'&image='._UPLOADS_.'/'.$obj['Рисунок'].'"';
        }
        elseif (!$w && !$h)
            $imgrSrc = 'src="'._UPLOADS_.'/'.$obj['Рисунок'];
        elseif (!$w)
            $imgrSrc = 'src="'._IMG_.'?h='.$h.'&url='._UPLOADS_.'/'.$obj['Рисунок'].'" height="'.$h.'"';
        elseif (!$h)
            $imgrSrc = 'src="'._IMG_.'?w='.$w.'&url='._UPLOADS_.'/'.$obj['Рисунок'].'" width="'.$w.'"';

        $linkName = '';
        if (!empty($obj['Название ссылки']))
            $linkName = '<a href="'.$obj['Ссылка'].'" class="in2">'.$obj['Название ссылки'].'</a>';

        $anons = '';
        if (!empty($obj['Анонс']))
            $anons = '<div class="text">'.$obj['Анонс'].'</div>';

        return '
            <div class="one">
                <div class="f_link">
                    <a href="'.$obj['Ссылка'].'" class="in2">'.$obj['Название'].'</a>
                </div>
                <img '.$imgrSrc.' />
                '.$anons.'
                '.$linkName.'
                <!--smart:{ id:'.$obj['id'].', title:"", actions:["edit", "remove"] }-->
            </div>';
    }

    # --------- БЛОК НОСОВТЕЙ ПОД СЛАЙЕРОМ НА ГЛАВНОЙ СТРАНИЦЕ -------
    function typeOfNewsBlock($id) {

        if ($list = $this->objects->getFullObjectsListByCLass($id, 38)) {
            $newsSections = array();
            foreach ($list as $o) {

                $newsItem = array();
                if ($subList = $this->objects->getFullObjectsListByCLass($o['id'], 39, "AND o.active='1' ORDER BY o.sort LIMIT 3")) {
                    foreach ($subList as $so) {
                        $offersHref = 'href="/'.$this->lang.'/'.$this->section->sectionName.'/special_offers/section/'.$o['id'].'/'.$so['id'].'/"';
                        $newsItem[] = '
                            <li>
                                <div class="one">
                                    <a '.$offersHref.' class="in2"><span>'.$so['Название'].'</span></a>
                                    <div class="text">'.$so['Анонс'].'</div>
                                    <!--smart:{ id:'.$so['id'].', title:"", actions:["edit", "remove"] }-->
                                </div>
                            </li>';
                    }
                }
                $newsSections[] = '
                    <figure class="news">
                        <!--smart:{ id:'.$o['id'].', title:"раздела", actions:["edit", "add"], p: { add: ["8"] } }-->
                        <h2 class="title">
                            <span>'.$o['Название'].'</span>
                        </h2>
                        <article class="news_block">
                            <ul>'.join("\n", $newsItem).'</ul>
                        </article>
                    </figure>';
            }
            return '<div class="colums">'.join("\n", $newsSections).'</div>';
        }
    }

    # ------ ОБЩЕЕ меню кнопок соц сетей --------------
    function socIconsMenu() {
        if ($list = $this->objects->getFullObjectsListByCLass(363, 35)) {
            $out = array();
            foreach ($list as $o) {
                $out[] = '
                    <li>
                        <a href="'.$o['Ссылка'].'" target="_blank">
                            <img src="'._IMGR_.'?w=16&h=16&image='._UPLOADS_.'/'.$o['Рисунок'].'" width="16" style="height:16px;" />
                            '.$o['Название'].'
                        </a>
                    </li>';
            }
            return '
                <figure class="social_icons">
                    <ul>'.join("\n", $out).'</ul>
                    '.($this->auth()?'
                        <a href="/cms/#list/363/" class="fe-smart-menu" style="color:#ffffff;position:absolute;right:0;" target="_blank">К списку иконок соц сетей &rarr;</a>
                    ':'').'
                </figure>';
        }
    }

    # ------ маленькое мепню под слайдером "Перейти" -----------
    function  smallMenu($id) {
        if ($list = $this->objects->getFullObjectsList($id)) {
            $out = array();
            foreach ($list as $o) {
                $out[] = '<li><a '.$this->getLink($o).'>'.$o['Название'].'</a></li>';
            }
            return '
                <figure class="block_links">
                    <span>Перейти:</span>
                    <ul>'.join("\n", $out).'</ul>
                </figure>';
        }
    }

    # ----------- ОБЩЕЕ МЕНЮ РАЗДЕЛОВ -------------
    function mainSectionMenu() {
        if ($list = $this->objects->getFullObjectsListByCLass(0, 30)) {
            $out = array();
            foreach ($list as $o) {

                if ($o['id'] == $this->section->sectionId) continue;


                $link = '/'.$this->lang.'/'.$o['Ссылка'].'/';

                if (strpos($o['Ссылка'], 'http')!== false) {
                    $link = $o['Ссылка'];
                }

                $out[] = '
                    <li>
                        <a href="'.$link.'">
                            <span>'.$o['Название'].'</span>
                            <img src="'._IMGR_.'?w=186&h=75&image='._UPLOADS_.'/'.$o['Рисунок'].'" width="186"/>
                        </a>
                    </li>';
            }
            if (count($out) > 1)
                return '
                    <ul class="add_ind_menu">
                        <li><div class="tit">Другие разделы</div></li>
                        <li class="list_add_menu">
                            <ul>'.join("\n", $out).'</ul>
                        </li>
                        <li><div class="oth_menu"><img src="/source/a1.jpg"/></div></li>
                    </ul>';
        }
    }

    # ------- Меню на главнйо странице каждого раздела под слайдером ------
    # ------- У каждого раздела свое меню ---------------------
    function imageMenu($id) {
        if ($list = $this->objects->getFullObjectsListByCLass($id, 35)) {

            $out = array();
            foreach ($list as $o) {
                $out[] ='
                    <li>
                        <a href="'.$o['Ссылка'].'">
                            <img src="'._IMGR_.'?w=138&h=66&image='._UPLOADS_.'/'.$o['Рисунок'].'" width="138" style="height:66px" >
                            <span class="caption">'.$o['Анонс'].'</span>
                            <span class="in">'.$o['Название'].'</span>
                        </a>
                    </li>';
            }
            return '
                <ul class="base_ind_menu">
                    '.join("\n", $out).'
                </ul>';
        }
    }

    # ----- ГЛАВНЫЙ СЛАЙДЕР КАЖДОГО РАЗДЕЛА ------------
    # ----- У КАЖДОГО раздела свой слайдер -------------
    function mainSlider($id) {
        if ($list = $this->objects->getFullObjectsListByCLass($id, 34)) {
            $li = array();
            $a = array();
            $i = 0;
            foreach ($list as $o) {
                $a[] = '<a data-slide-index="'.$i.'" href=""><span>'.$o['Название'].'</span></a>';
                $li[] = '
                    <li>
						<a href="'.$o['Ссылка'].'">
							<img src="'._IMGR_.'?w=1000&h=370&image='._UPLOADS_.'/'.$o['Рисунок'].'" width="1000" style="height:370px;"/>
						</a>
                        <div class="bx-caption">
                            <!--- <a class="link" href="'.$o['Ссылка'].'">Смотреть подробнее</a> -->
                        </div>
                    </li>';
                $i++;
            }
            return '
                <figure class="main_slider">
                    <div class="slider_bx">
                        '.($this->auth()?'
                            <a href="/cms/#list/'.$id.'" class="fe-smart-menu" style="color:#ffffff;position:absolute;" target="_blank">К списку слайдов &rarr;</a>
                        ':'').'
                        <ul class="bxslider">
                            '.join("\n", $li).'
                        </ul>
                        <div id="bx-pager">
                            <!-- <span class="tit">Главные темы</span> -->
                            '.join("\n", $a).'
                        </div>
                    </div>
                </figure>';
        }
    }

    # --------- меню в шапке сайта на каждой странице каждого раздела
    # --------- У каждого раздела свое меню ------------------
    function getHeadMenu($id) {

        if ($list = $this->objects->getFullObjectsList($id)) {
            $out = array();

            foreach ($list as $o){
                $href = $this->getLink($o);
                $name = @$o['Название'];
                $subMenu = '';

                if ($o['inside'] > 0) {
                    if ($subList = $this->objects->getFullObjectsList($o['id'])) {
                        $subO = array();
                        foreach ($subList as $so) {
                            if (
                                $o['class_id'] == 1 ||
                                $o['class_id'] == 2 ||
                                $o['class_id'] == 35
                            ) {
                                $subO[] = '<li><a '.$this->getLink($so).'>'.$so['Название'].'</a></li>';
                            }
                        }
                        $subMenu = '<ul>'.join("\n", $subO).'</ul>';
                    }
                }
                $out[] = '
                    <li>
                        <a '.$href.'>'.$name.'</a>
                        '.$subMenu.'
                    </li>';
            }

            return '
                <figure class="head_menu">
                    <ul>'.join("\n", $out).'</ul>
                </figure>';
        }
    }

    # ------- Меню МОДЕЛЬНОГО РЯДА КАЖДОГО РАЗДЕЛА ------------
    # ------- У каждого раздела свое меню ---------------------
    function lineUpMenu() {

        $carsClasses = $this->objects->getFullObjectsList($this->section->classesListId);

        $modelsByClasses = array();

        foreach ($this->models as $model){
            @$modelsByClasses[$model['Класс']][] = $model;
        }

        $html = '';

        if (count($this->models)) {

            $html .= '
                <figure class="menu">
                    <ul>';
            if($this->section->sectionName == 'passengercars'){
                $html .= '<li><a href="">Модели</a>
                                <ul>
                                    <li><a href="/'.$this->lang.'/'.$this->section->sectionName.'/models/">Все модели</a></li>
                                    <!--<li><a href="/'.$this->lang.'/'.$this->section->sectionName.'/models/amg/">AMG</a></li>-->
                                    <!--<li><a href="#">Конфигуратор</a></li>-->
                                </ul>
                           </li>';
            }elseif ($this->section->sectionName !== 'truck'){
                $html .= '<li><a href="/'.$this->lang.'/'.$this->section->sectionName.'/models/">Модельный ряд</a></li>';
            }

             $kurs = $this->objects->getFullObject(7299); //EURO
               if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(2)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

              

            foreach($carsClasses as $carsClass){
                if ($this->section->sectionName == 'truck') {
                    $html .= '<li><a href="/'.$this->lang.'/'.$this->section->sectionName.'/model/'.urlencode($carsClass['Код']).'/">'.$carsClass['Название'].'</a><li>';
                } else {
                    $html .= '<li><a href="">'.$carsClass['Код'].'</a>';
                }


                if(isset($modelsByClasses[$carsClass['Название']]) && count($modelsByClasses[$carsClass['Название']])){
                    $html .= '
                            <div class="mod_list">
                                <div class="mod_main">
                                    <div class="mod_name">'.$carsClass['Название'].'</div>
                                    <div class="mod_tit">Тип кузова</div>
                                    <ul>';

                    foreach($modelsByClasses[$carsClass['Название']] as $model){

                    	  if($this->section->sectionId == 322 || $this->section->sectionId == 595 || $model['Класс']=='Малотоннажные' ||  $model['Тип кузова'] == 'Минивэны и кемперы') {

                 $kurs = $this->objects->getFullObject(7300); //RUB
               if(empty($kurs['Значение'])) {
                 $parse = file_get_html('http://halykbank.kz/ru');
                 $parse = $parse->find('table',2)->children(3)->children(2);
                 $kursBanks = substr($parse->outertext, 14,10);
             }

                }
					
							if($links = $this->objects->getFullObjectsListByClass($model['id'], 2, "AND o.active ORDER BY o.sort")){
								$links_out = array();
								foreach($links as $l){
									$links_out[] = '<li><a href="'.$l['Ссылка'].'">'.$l['Название'].'</a>
									<!--smart:{ id:'.$l['id'].', title:"линки", actions:["edit"],   css:{ marginBottom: -10} }-->
									</li>';
								}
								$links_out[]  = '<div><!--smart:{ id:'.$model['id'].', title:"Меню", actions:["add"], p:{add:[2]},   css:{ marginTop: -150 } }--></div>';
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
						
                        $html .= '<li class="active"><a href="/'.$this->lang.'/'.$this->section->sectionName.'/model/'.urlencode($model['Код модели']).'/">'.$model['Название по кузову'].'</a>
                                        <div class="mod_text">
                                            <div class="mod_im"><img src="'._UPLOADS_.'/'.$model['Фото'].'"/></div>
                                            <div class="mod_desc">
                                                <div class="mod_spec">'.$model['Слоган'].'</div>
                                                <div class="mod_cost">Базовая цена от: <span>'.$cost.' тг </span></div>
                                                <a href="/'.$this->lang.'/'.$this->section->sectionName.'/model/'.urlencode($model['Код модели']).'/" class="link"><span>Подробнее о модели</span></a>
                                                <ul>
                                                   '.($carsClass['скрыть конфигуратор']!=1?'
                                                    <li>
                                                  
                                                    <a href="/'.$this->lang.'/config/'.$model['id'].'/">Конфигуратор</a>
                                             
                                                    </li>':'').'
                                                        <!--smart: {id:'.$carsClass['id'].', actions: ["edit"],  css:{ marginBottom: -10} , title: "конфигуратора"}-->
                                                    '.(!empty($links)?join("\n", $links_out):'
													<!--smart:{ id:'.$model['id'].', title:"Меню", actions:["add"], p:{add:[2]},   css:{ position: "absolute", marginBottom: -20 } }-->
													').'
                                                </ul>
                                            </div>
                                        </div>
                                    </li>';
                    }

                    $html .= '</ul></div></div>';
                }

                $html .= '</li>';
            }
          $html .= '</ul></figure>';
        }

        return $html;
    }

	# ----------- ВЫЗОВ МОДАЛЬНОГО ОКНА ДЛЯ ПОДПИСКИ НА РАССЫЛКУ ---------------
    function subscribeModal() {
        return '
            <!-- КНОПКА ВЫЗОВА МОДАЛЬНОГО ОКНА ПО ID -->
            <figure class="subscribe">
                <a href="#" onclick="return subscribe()"><span>подписаться на рассылку</span></a>
            </figure>
            <!-- МОДАЛЬНОЕ ОКНО ДЛЯ ПОДПИСКИ НА РАССЫЛКУ -->
            <div id="subscribeWin" title="Подписка на рассылку" style="display:none">
                <div id="err" style="display: none;margin-bottom: 10px;"></div>
                <input type="email" placeholder="Введите email" id="subscribe-mail" style="width: 310px;margin: 0;">
            </div>';
    }

    function array_split( $array, $count ) {
        if ( is_array($array) && count( $array ) > 0) {
            $n = ceil( count( $array ) / $count ) - 1;
            for ( $i = 0; $i <= $n; $i++ ) {
                $r[] = array_slice( $array, $i * $count, $count, true );
            }
            return $r;
        }
        return $array;
    }

    function getMothers($id){
        if (is_numeric($id) && ($id != 0) && ($o = $this->objects->getObject($id, false)) && (($o['class_id'] == 1) || ($o['class_id'] == 3) || ($o['class_id'] == 19) || ($o['class_id'] == 20) || ($o['class_id'] == 21) || ($o['class_id'] == 2))){
            if ($o['class_id'] != 20){
                $this->mothers[] = $o['id'];
            }
            return $this->getMothers($o['head']);
        }
    }

    function bread($separator = ''){
        if ($_SERVER['PHP_SELF'] == '/index.php') return;
        if (@$_GET['id'])
            $id = $_GET['id'];
        elseif (@$_GET['cat'])
            $id = $_GET['cat'];
        else $id = @$_GET['bread'];
        $out = array();
        if ((!empty($id)) && (is_numeric($id))){
            if(($obj = $this->objects->getFullObject($id)) || (($obj['class_id'] == 1) && ($obj['class_id'] == 3))){
                # ВЛОЖЕННОСТЬ
                $this->mothers = array();

                $this->getMothers($obj['head']);
                $this->mothers = array_reverse($this->mothers);
                $out[] = '<a href="/'.$this->lang.'/"><span><!--o:131--></span></a>';

                if (($obj['class_id'] == 8)){
                    $out[] = '<a href="/'.$this->lang.'/news/"><span><!--o:132--></span></a>';
                }

                if (($obj['class_id'] == 24)){
                    $out[] = '<a href="/'.$this->lang.'/articles/"><span><!--o:124--></span></a>';
                }

                if (($obj['class_id'] == 19) || ($obj['class_id'] == 15)){
                    $out[] = '<a href="/'.$this->lang.'/catalog/"><span><!--o:126--></span></a>';
                }
                if($id != 131){ #ЕСЛИ НЕ ГЛАВНАЯ
                    # ХЛЕБНЫЕ КРОШКИ
                    if (sizeof($this->mothers) > 0){
                        foreach($this->mothers as $obj_id){
                            if (is_numeric($obj_id) && ($path_obj = $this->objects->getFullObject($obj_id, false))) $out[] = '<a '.$this->getLink($path_obj['id']).'><span>'.(($path_obj['Название'])?$path_obj['Название']:$path_obj['name']).'</span></a>';
                        }
                    }

                    $out[] = '<span>'.((@$obj['Название'])?$obj['Название']:$obj['Значение']).'</span>';
                }
            }
        }
        return '<figure id="breadcrumbs">'.join($separator, $out).'</figure>';
    }

    function array_random_k($input_mass, $flag_asKey_or_asValue = "asvalue"){
        $mass = array();
        $return_array = array();
        $k = 0;
        $count = count($input_mass);
        while($k != $count){
            $rand_key = mt_rand(0, $count - 1);
            if (!in_array($rand_key, $mass)){
                $mass[] = $rand_key;
                $k++;
            }
        }
        if (strtolower($flag_asKey_or_asValue) == 'askey'){
            return $mass;
        } elseif (strtolower($flag_asKey_or_asValue) == 'asvalue'){
            foreach ($mass as $v){
                $return_array[] = $input_mass[$v];
            }
            return $return_array;
        }
    }

#Первая буква заглавная
    function firstUpper($text){
        $first = mb_substr(trim($text),0,1, 'UTF-8');//первая буква
        $last = mb_substr(trim($text),1);//все кроме первой буквы
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');

        return $first.$last;
    }

    /* example: $api->substrword($o['Анонс'], 14); */
    /* this code writes in api.php file */
    function substrword($str = '', $c = 50){
        if ($mass = explode(" ", $str)){
            if (count($mass) > $c){
                $str = '';
                for($i = 0; $i < $c; $i++){
                    $str .= $mass[$i]." ";
                }
                $str = $str."...";
            }
        }
        return strip_tags($str, '<p><br><br />');
    }

    function substrstr($str = '', $c = 50, $addstr = '...'){
        if (mb_strlen($str, "UTF-8") > $c){
            return mb_substr($str, 0, $c, "UTF-8").$addstr;
        }
        return $str;
    }

	#ФОРМА ПОИСКА
    function search(){
        $out = array('
            <form method="post" action="/'.$this->lang.'/search/" onsubmit="return checkSearchForm(this)">
                <input type="text" name="what" class="textSearch" placeholder="Искать на сайте"/>
                <input type="image" src="/img/lupa.png" class="btSearch"/>
            </form>');
        return join("\n",$out);
    }

# ЯЗЫКИ
    function getLangs(){
        $out = array();
        $i=0;
        foreach($this->languages as $kk=>$vv){
            if ($this->lang == $kk){
                # ВЫБРАН
                $out[] = '<a href="#" class="active">'.$vv.'</a>'.((++$i == 1)?' / ':'');
            } else {
                $out[] = '<a href="/'.$kk.'/">'.$vv.'</a>'.((++$i == 1)?' / ':'');
            }
        }
        return join("", $out);
    }

    # PASSWORD GENERATION
    function genPass(){
        $out = array();
        $symbols = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        for($i=1; $i<=8; $i++){
            $out[]=$symbols[rand(0, count($symbols)-1)];
        }
        return join("", $out);
    }

    # AUTH USER BLOCK
    function authBlock(){
        $display = array( 'block', 'none' );
        $auth_html = array();
        if(!empty($_SESSION['auth']['u']) && is_array($u = $_SESSION['auth']['u'])){
            $display = array( 'none', 'block' );
            $auth_html = array(
                '<div><!--o:33-->, <strong>'.$u['name'].'</strong>!</div><br>',
                '<div><a href="/'.$this->lang.'/edit/"><!--o:34--></a></div>',
                '<div><a href="#выход" onclick="return exit()"><!--o:35--></a></div>'
            );
        }
        $html = array(
            '<h2><!--o:27--></h2>&nbsp;&nbsp;<a href="/'.$this->lang.'/register/"><!--o:28--></a>',
            '<div id="auth-form" style="display:'.$display[0].'">',
            '<div class="wihte_text top12"><!--o:29--></div>',
            '<input id="input-login" type="text" value="" class="input_text" />',
            '<div class="wihte_text"><!--o:30--></div>',
            '<div><input id="input-pass" type="password" value="" class="input_text" /></div>',
            '<div><input id="auth-button" type="button" value="<!--o:31-->" class="but" />&nbsp;&nbsp;<a href="/forgot.php?lang='.$this->lang.'"><!--o:32--></a></div>',
            '</div>',
            '<div id="auth-block" style="display:'.$display[1].'">',
            join("\n", $auth_html),
            '</div>'
        );
        return join("\n", $html);
    }

###ZOTTIG (c)
    #НОВОЕ МЕНЮ
    function selected($id,$last){
        $o=$this->objects->getFullObject($id);
        if ( !isset($_GET['id']) && empty($_GET['id']))
            $_GET['id'] = '';
        if ( !isset($_GET['cat']) && empty($_GET['cat']))
            $_GET['cat'] = '';
        if (
            ($_GET['id']==$id)
            ||
            ($last==$id)
            ||
            ($_GET['id'] == $o['id'])
            ||
            ($_GET['cat'] == $o['id'])
        )
            return true;
        else
            if ($o['class_id']==2)
                if (
                    $_SERVER['REQUEST_URI'] == '/'.$this->lang.$o['Ссылка']
                    ||
                    $_SERVER['SCRIPT_NAME'] == $o['Ссылка']
                    ||
                    (
                        strstr( $_SERVER['REQUEST_URI'], $o['Ссылка'] )
                        AND
                        $o['Ссылка'] != '/'
                    )
                    ||
                    (
                        ($_SERVER['PHP_SELF'] == '/index.php')
                        AND
                        ($o['Ссылка'] == '/')
                    )
                ) return true;
                else
                    return false;
    }

    function t($id){
        return $id.'-'.$this->objects->urlTranslitFormID($id);
    }

    function getLink($o){

        if (!is_array($o) && is_numeric($o))
            $o=$this->objects->getFullObject($o);

        if(($o['class_id']==2) || ($o['class_id']==35)){
            if (!empty($o['Ссылка'])){
                if(strstr($o['Ссылка'], '.php')){
                    return 'href="'.$o['Ссылка'].'?lang='.$this->lang.'" '.($o['В модальном окне'] == 1?'class="d_b fancy"':'').' '.(@$o['В новом окне']?' target="_blank"':'');
                }elseif(strstr($o['Ссылка'], 'http://')) return 'href="'.$o['Ссылка'].'"'.(@$o['В новом окне']?' target="_blank"':'');
                else return 'href="/'.$this->lang.'/'.$this->section->sectionName.$o['Ссылка'].'"'.(@$o['В новом окне']?' target="_blank"':'');
            } else return 'href="" onclick="return false;" style="cursor: default;"';
        }elseif($o['class_id']==5){
            return 'href="'._UPLOADS_.'/'.$o['Ссылка'].'"';
        }elseif($o['class_id']==8){
            return 'href="/'.$this->lang.'/'.$this->section->sectionName.'/news/'.$this->t($o['id']).'/"';
        }elseif($o['class_id']==24){
            return 'href="/'.$this->lang.'/'.$this->section->sectionName.'/articles/'.$this->t($o['id']).'/"';
        }elseif($o['class_id']==19){
            return 'href="/'.$this->lang.'/'.$this->section->sectionName.'/catalog/'.$this->t($o['id']).'/"';
        }elseif($o['class_id']==15){
            return 'href="/'.$this->lang.'/'.$this->section->sectionName.'/catalog/'.$this->t($o['head']).'/'.$this->t($o['id']).'/"';
        }elseif($o['class_id']==82){
			return 'href="/'.$this->lang.'/'.$this->section->sectionName.'/today/'.$this->t($o['head']).'/'.$this->t($o['id']).'/"';
		}elseif($o['class_id'] == 1){
            return 'href="/page.php?lang=ru&section='.$this->section->sectionName.'&pageSectionID=page&pageId='.$o['id'].'"';
        } else {
            return 'href="/'.$this->lang.'/'.$this->section->sectionName.'/'.$this->t($o['id']).'.html"';
        }
    }

    function isHasCatalog($id){
        if(!!$list = $this->objects->getFullObjectsListByClass($id, 19)){
            return true;
        }
        return false;
    }

    # МЕНЮ
    function getMenu($id, $hasSubMenu = true, $withIMG = false, $class=""){
        $out = array();
        if ( !!$list = $this->objects->getFullObjectsList($id) ) {

            $last = $this->objects->last;

            $out[] = '<ul'.(!empty($class)?' class="'.$class.'"':'').'>';

            foreach ($list as $o) {
				if($o['active'] == 1) {
                if ( @$this->selected( $o['id'], @$last ) )
                    $out[]='<li class="active"><a '.$this->getLink($o['id']).''.($o['Ссылка'] == '/feedback.php'?' class="fancy"':'').'>'.($withIMG?'<img title="'.$o['Название'].'" alt="'.$o['Название'].'" src="'._UPLOADS_.'/'.$o['Иконка'].'">':$o['Название']).'</a>'.($hasSubMenu?$this->getSubMenu((@$o['Ссылка']=='/catalog/'?15:$o['id'])):'').'</li>';
                else
                    $out[]='<li><a '.$this->getLink($o['id']).''.($o['Ссылка'] == '/feedback.php'?' class="fancy"':'').'>'.($withIMG?'<img alt="" title="'.$o['Название'].'" alt="'.$o['Название'].'" src="'._UPLOADS_.'/'.$o['Иконка'].'">':$o['Название']).'</a>'.($hasSubMenu?$this->getSubMenu((@$o['Ссылка']=='/catalog/'?15:$o['id'])):'').'</li>';
				} 
			}
            $out[]='</ul>';
        }
        $smart = '  <!--smart:{ id : '.$id.', title : "меню", actions : ["list"], p : { list : 1 }, css:{ position: "absolute" }}-->';
        return join("\n", $out);
    }
	
    function getSubMenu($id){
        $out = array();
        if(!!$list = $this->objects->getFullObjectsList($id)){
            $last=$this->objects->last;
            $i = 0;
            $mass = array();
            foreach($list as $o){
                if (($o['class_id'] == 1) || ($o['class_id'] == 2)){
                    if($this->selected($o['id'],$last))
                        $mass[]='<li class="active"><a '.$this->getLink($o['id']).' onclick="return false;">'.$o['Название'].'</a></li>';
                    else
                        $mass[]='<li><a '.$this->getLink($o['id']).'>'.$o['Название'].'</a></li>';
                }
            }
            if (count($mass) > 0)
                $out[] = '<ul>'.join("\n", $mass).'</ul>';
        }
        return join("\n", $out);
    }
###ZOTTIG (c)

    # ФУНКЦИЯ ГРАММОТНОЙ ОБРЕЗКИ СТРОК
    function maxsite_str_word($text, $counttext = 10, $sep = ' ') {
        $words = split($sep, $text);
        if ( count($words) > $counttext )
            $text = join($sep, array_slice($words, 0, $counttext));
        return $text;
    }

    # НОВОСТНАЯ ЛЕНТА, ВЫВОД НА ГЛАВНОЙ ДВУХ НОВОСТЕЙ
    function newsList($id, $cid) {
        $smart = '<!--smart:{ id : '.$id.', title : "новостей", actions : ["add"], p : { add : ['.$cid.'] } }-->';
        if (!$list = $this->objects->getFullObjectsListByClass($id, $cid, "AND o.active=1 ORDER BY c.field_19 DESC LIMIT 2")) return $smart;
        $html = array();
        foreach ($list as $o) {
            $html[] = '
				<li>
                    <div class="one">
                        <span class="date">'.$this->strings->date($o['Дата'], 'sql', 'textdateday').'</span>
                        <a '.$this->getLink($o['id']).'><span>'.$o['Название'].'</span></a>
                        <div class="text">'.$o['Анонс'].'</div>
                    </div>
                </li>';
        }
        return '
            <figure class="news">
                <h2 class="title">
                    <span>'.$this->v('Новости').'</span>
                    <a href="/'.$this->lang.'/news/" class="in">'.$this->v('все новости').'</a>
                </h2>
                <article class="news_block">
                    <ul>'.join("\n", $html).'</ul>
                </article>
                '.$smart.'
            </figure>';
    }

    # СПИСОК БАННЕРОВ
    function bannersList($id, $cid1, $cid2)
    {
        $smart_global = '
	      <!--smart:{
		      id : '.$id.',
		      title : "списка&nbsp;баннеров",
		      actions : ["add"],
		      p : {
			      add : ['.$cid1.', '.$cid2.']
		      },
		      info : {
			      "add" : "добавить&nbsp;баннер"
		      },
		      css : {
			      marginTop:0
		      }
	      }-->';

        if(!$list = $this->objects->getFullObjectsList($id)) return $smart_global;
        $out = array();
        foreach($list as $o)
        {
            if($o['class_id'] == 6)
            {
                if($this->lower($this->getFileExtension($o['Баннер'])) == 'swf')
                {
                    $html = '
				      <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0"'.($o['width']?' width="'.$o['width'].'"':'').($o['height']?' height="'.$o['height'].'"':'').'>
					      <param name="movie" value="'._UPLOADS_.'/'.$o['Баннер'].'">
					      <param name="quality" value="high">
					      <param name="wmode" value="transparent">
					      <embed src="'._UPLOADS_.'/'.$o['Баннер'].'" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash"'.($o['width']?' width="'.$o['width'].'"':'').($o['height']?' height="'.$o['height'].'"':'').'>
				      </object>';
                } else $html = '<a id="banner-'.$o['id'].'" href="'.($o['Ссылка']?$o['Ссылка']:'javascript:void(0)').'"'.($o['В новом окне']?' target="_blank"':'').'><img id="banner-'.$o['id'].'" src="'._UPLOADS_.'/'.$o['Баннер'].'" border="0"'.($o['width']?' width="'.$o['width'].'"':'').($o['height']?' height="'.$o['height'].'"':'').'></a>';
            } else $html = htmlspecialchars_decode($o['Значение']);
            $smart = '
		      <!--smart:{
			      id:'.$o['id'].',
			      title : "баннера",
			      actions : ["edit", "remove"],
			      p :{
				      remove : "#banner-'.$o['id'].'"
			      },
			      info : {
				      "remove" : "удалить&nbsp;баннер"
			      }
		      }-->';
            $out[]='<div class="banners">'.$html.'</div>';
        }
        return join("\n", $out).$smart_global;
    }

    #ФУНКЦИЯ ПОДАЧИ ГОЛОСА, АЙДИ ОПРОСА, АЙДИ ОТВЕТА
    function voteOne($voting_id, $answer_id){
        $class_id = 10;
        if(!!$field_id = $this->db->select("fields", "WHERE `name`='Голоса' AND class_id='".$class_id."' LIMIT 1", "id")){
            $this->db->mysql_query("UPDATE class_".$class_id." SET field_".$field_id."=field_".$field_id."+1 WHERE `object_id`='".$answer_id."'");
        }
        setcookie("votingStamp", time(), time()+3600*2);
    }
    #ФУНКЦИЯ КОТОРАЯ ВОЗВРАЩАЕТ ТАБЛИЦУ С РЕЗУЛЬТАТАМИ, ВХОДЯЩИЕ ДАННЫЕ - ID КОНКРЕТНОГО ОПРОСА
    function getVotingResults($id){
        if(!!$obj = $this->objects->getObject($id)){
            $total_count = 0;
            if(!!$list = $this->objects->getFullObjectsList($id)){
                foreach($list as $a){
                    //if(!isset($a['Голоса']) || !is_numeric($a['Голоса'])) continue;
                    $total_count+=$a['Голоса'];
                }
                $colors = array(
                    "red",
                    "green",
                    "blue",
                    "yellow",
                    "purple",
                    "orange",
                    "black",
                    "magenta",
                    "gray"
                );
                $html = array('<br>');
                foreach($list as $k=>$a){
                    $percentage = round($a['Голоса']/$total_count*100, 2);
                    $html[]='<div>'.$a['Ответ'].' '.$percentage.'%</div>';
                    $html[]='<div style="margin-bottom:10px;"><div style="width:'.($percentage*2).'px; background:'.$colors[$k].'; height:5px;"></div></div>';
                    //$html[]='<br>';
                }
                $html[]= '<div><b><!--o:43--> '.$this->sklon($total_count, array('человека', 'человек', 'человек')).'.</b> </div><br>';

            }
        }
        return join("\n", $html);
    }

    function votingBlock($parent_id){
        #БЕРЕТСЯ ПЕРВЫЙ АКТИВНЫЙ ОБЪЕКТ ОПРОСА ИЗ КАТАЛОГА ГОЛОСОВАНИЯ И ОТОБРАЖАЕТСЯ
        $o = $this->objects->getFullObject( $this->db->select("objects", "WHERE `head`='".$parent_id."' AND `active`='1' ORDER BY sort DESC LIMIT 1", "id") );
        $html = array();
        $html[]= '<div>'.$o['Значение'].'</div>';
        $html[]= '<div id="voting-backup" style="display:none"></div>';

        $html[]= '<div id="voting-screen">';
        #ЕСЛИ ЕСТЬ КУКИ ЗНАЧИТ ПОЛЬЗОВАТЕЛЬ УЖЕ ГОЛОСОВАЛ
        if(!!@$_COOKIE['votingStamp']){
            $html[]= $this->getVotingResults($o['id']);
        }else{
            $html[]= '<br>';
            foreach($this->objects->getFullObjectsList($o['id']) as $k=>$a){
                $html[]= '<div class="vote"><input name="votingAnswer" type="radio" value="'.$a['id'].'"'.(!$k?' checked':'').'> '.$a['Ответ'].'</div>';
            }
            $html[]= '<div><button class="vote_but" onclick="return voteIt(this, '.$o['id'].', $(\'#voting-screen\').find(\':checked\').val())">Отправить</button></div>';
            $html[]= '<div style="margin-top:10px;"><a href="#" onclick="return voteIt(this, '.$o['id'].', false)"><!--o:45--></a></div>';
        }
        $html[]= '</div>';
        $html[]= '<div id="back-to-vote" style="display:none;">&larr; <a href="#" onclick="return showVoting()"><!--o:46--></a></div>';
        return join("\n", $html);
    }

    function script_datepicker(){
        #СТИЛИ НАДО ТАК СТАВИТЬ
        #.ui-state-custom {
        #	border-bottom: red solid 2px !important;
        #	background: #F26100 !important;
        #}
        $out = array();
        $object_id 	= 62;			# ID объекта в котором лежат новости
        $class_id	= 8;

        $out[] = '<script type="text/javascript">

					      $("#datepickerid").datepicker({';
        if ($this->lang == 'ru'){
            $out[]= 'firstDay: 1,
							      dayNames: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
							      dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
							      monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],';
        }
        $out[] = 'duration: "fast",
						      changeYear: true,
						      hightlight : { // подсвечиваем
							      format:"yy-mm-dd",';
        if ($news = $this->objects->getFullObjectsListByClass($object_id, $class_id, "AND o.active='1'")){
            $out[] = 'values:[';
            foreach($news as $n)
            {
                $out[] = '"'.$n['Дата'].'",';
            }
            $out[] = '],';
        }
        if ($news = $this->objects->getFullObjectsListByClass($object_id, $class_id, "AND o.active='1'")){
            $out[] = 'titles:[';
            foreach($news as $n)
            {
                $out[] = '"'.$n['Название'].'",';
            }
            $out[] = '],';
        }
        $out[] = '
						      },onSelect: function(dateText) {
								      self.location.href = "/'.$this->lang.'/events.php?date="+dateText;
							      }
					      });
				      </script>';
        return join('', $out);
    }
#Функция получения формы
    function getForm($id,$class_id=29){
        $obj = $this->objects->getFullObject($id);
        $out = array('<form method="post" name="order-form">');
        $out[] = $obj['Описание формы'];
        if ($fields = $this->objects->getFullObjectsListByClass($id,$class_id)){

            foreach ($fields as $o){
                $smart = '
		      <!--smart:{
			      id:'.$o['id'].',
			      title:"поля",
			      actions:["edit", "remove"],
			      p:{
				      remove: "#field-'.$o['id'].'"
			      },
			      css:{}
		      }-->
		  ';
                switch ($o['Тип']){
                    case 0://Простой текст
                        $out[] = '
			    <div id="field-'.$o['id'].'">'.$smart.'
				  <label>'.$o['Название'].'</label>
				  <input type="text" class="text" name="fields['.$o['Название'].']" '.($o['Обязательное']==1?'required':'').' pattern="^[А-Яа-яЁёA-Za-z0-9\s]+$" title="Только буквы, цифры и пробел" />
			    </div>
			';
                        break;
                    case 1://Только буквы
                        $out[] = '
			    <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <input type="text" class="name" name="fields['.$o['Название'].']" '.($o['Обязательное']==1?'required':'').' pattern="^[А-Яа-яЁёA-Za-z\s]+$" title="Только буквы" />
			    </div>
			';
                        break;
                    case 2://Число
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <input type="text" class="digits" name="fields['.$o['Название'].']" '.($o['Обязательное']==1?'required':'').' pattern="^[0-9]+$" title="Только цифры" />
			    </div>
			';
                        break;
                    case 3://Пароль
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <input type="password" class="password" name="fields['.$o['Название'].']" '.($o['Обязательное']==1?'required':'').' />
			    </div>
			';
                        break;
                    case 4://Email
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <input type="email" class="email" name="fields['.$o['Название'].']" '.($o['Обязательное']==1?'required':'').' />
			    </div>
			';
                        break;
                    case 5://Дата
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <input type="text" class="date-picker" name="fields['.$o['Название'].']" '.($o['Обязательное']==1?'required':'').' />
			    </div>
			';
                        break;
                    case 6://Текстовый блок
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <textarea name="fields['.$o['Название'].']" class="textarea" '.($o['Обязательное']==1?'required':'').' ></textarea>
			    </div>
			';
                        break;
                    case 7://Галочка
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <input type="checkbox" class="checkbox" name="fields['.$o['Название'].']"  />
			    </div>
			';
                        break;
                    case 8://Список
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>
			     <select class="select" name="fields['.$o['Название'].']" />';
                        $options = explode("\n",$o['Значения']);
                        foreach ($options as $i){
                            $out[] = '<option>'.$i.'</option>';
                        }
                        $out[] = '
				</select>
			    </div>
			';
                        break;
                    case 9://Переключатели
                        $out[] = '
			     <div id="field-'.$o['id'].'">'.$smart.'
			     <label>'.$o['Название'].'</label>';
                        $btns = explode("\n",$o['Значения']);
                        foreach($btns as $b){
                            $out[] = '<label><input type="radio" name="fields['.$o['Название'].']" value="'.$b.'">'.$b.'</label>';
                        }
                        $out[] = '</div>';
                        break;
                }
            }
        }
        $out[] ='
      <!--smart:{
	    id : '.$obj['id'].',
	    title : "формы",
	    actions : ["edit","add"],
	    p : {
		    add : ['.$class_id.']
	    },
	    info : {
		    "add" : "добавить&nbsp;поле",
		    "edit" : "редактировать&nbsp;форму"
	    },
	    css : {
		    marginBottom:20,
	    }
	}-->';
        if ($obj['Captcha']==1){
            $out[] = 'Здесь будет выводиться Капча';
        }
        $out[] = '<input type="submit" value="'.$obj['Текст кнопки отправки'].'" />';
        $out[] = '</form>';
        return join("\n",$out);
    }

# ИНФОРМАЦИЯ О КОРЗИНЕ
    function basketInfo(){
        $total_count = 0;
        $total_summ  = 0;
        if (!isset($_SESSION['rycle'])) $_SESSION['rycle'] = array();
        if (is_array($_SESSION['rycle']))
        {
            foreach($_SESSION['rycle'] as $o)
            {
                if (is_array($o) && ($obj = $this->objects->getFullObject($o['id'], false)))
                {
                    // if ($this->root != $o['city']) continue; #Показ товара только из определенного города
                    $total_count++;
                    $total_summ += (intval($obj['Цена'])*$o['count']);
                }
            }
        }

        return array('count'=>$total_count, 'summ'=>$total_summ);
    }

#Подписка на рассылку
    function subscribeBlock(){
        return '
			<input id="subscribe-mail" class="text" type="e-mail" onblur="if(this.value==\'\')  this.value=this.title;" onfocus="if(this.value==this.title) this.value=\'\'" title="введите ваш e-mail" value="введите ваш e-mail">
			<input id="subscribe-add" class="button" type="submit" value="ok">
		';
    }
    #Блок для вывода модального окна авторизации
    function authBlockModal(){
        if(empty($_SESSION['auth']['u']) || !is_array($u = $_SESSION['auth']['u'])){
            return '
			<div id="login_win" title="'.$this->v('Авторизация пользователя').'" style="display:none">
					<div class="login_form_container">
					<table class="login_form" width="100%">
						<tr>
							<td width="70">E-mail</td><td><input id="login_email" type="text" value="" /></td>  
						</tr>
						<tr>
							<td>'.$this->v('Пароль').'</td><td><input id="login_pass" type="password" value="" /></td>
						</tr>
						<tr>
							<td colspan="2"><a href="/'.$this->lang.'/forgot/">'.$this->v('Забыли пароль?').'</a></td>
						</tr>
					</table>
					<div id="login_msg" style="display:none; padding-left: 3px; padding-top: 13px;"></div>
				</div>
			</div>';
        }
    }

    function resortObjects()
    {
        $objects = $this->objects->getObjectsList(32673);

        foreach ($objects as $key => $object) {
            $this->db->update('objects', array("sort"=>microtime() + $key), "WHERE `id`='".$object['id']."'");
            echo "Done \n";
        }
    }

}

$api = new api();
include_once(_PUBLIC_ABS_."/form.php");



if ( ($zaglushka=$api->objects->getFullObject(162)) && ($zaglushka['active']==1) )
    exit ($zaglushka['Значение']);
if ( isset($_REQUEST['lang']) && array_key_exists($_REQUEST['lang'], $api->languages))
    $api->lang = $_REQUEST['lang'];
if ( empty($_SESSION['auth']['u']) || !is_array($AUTH_USER = $_SESSION['auth']['u']) )
    $AUTH_USER = array();

?>