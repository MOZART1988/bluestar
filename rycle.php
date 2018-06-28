<?
# Config

$user_class_id = 14;
$cities_field_id = 43;

include('cms/public/api.php');

if(($obj=$api->objects->getFullObject(16)) && (trim($obj['Значение'])!='')){
	$manager_mail=trim($obj['Значение']);
}else{
	$manager_mail='as@go-web.kz';
}

$vars = array(
	'ru' => array(
		'Корзина' => 'Корзина',
		'Адрес' => 'Адрес',
		'Фото' => 'Фото',
		'Название и описание' => 'Название и описание',
		'Размер' => 'Размер',
		'Количество' => 'Количество',
		'Цена за ед.' => 'Цена за ед.',
		'Итого' => 'Итого',
		'не выбран' => 'не выбран',
		'Итого к оплате' => 'Итого к оплате',
		'В корзине нет товара.' => 'В корзине нет товара.',
		'В корзине нет такого элемента.' => 'В корзине нет такого элемента.',
		'Ваш заказ успешно оформлен и направлен менеджеру, который свяжется с Вами в ближайшее время.' => 'Ваш заказ успешно оформлен и направлен менеджеру, который свяжется с Вами в ближайшее время.',
		'Ваше примечание' => 'Ваше примечание',
		'Оформить заказ' => 'Оформить заказ',
		'Для дальнейшего оформления заказа вам необходимо авторизоваться в системе.' => 'Для дальнейшего оформления заказа вам необходимо авторизоваться в системе.',
		'Ничего не забыли? Оформляем?' => 'Ничего не забыли? Оформляем?',
		'удалить' => 'удалить',
		'Удалить из списка' => 'Удалить из списка',
	),
	'en' => array(
		'Корзина' => 'Cart',
		'Адрес' => 'Address',
		'Фото' => 'Photo',
		'Название и описание' => 'Name and description',
		'Размер' => 'Size',
		'Количество' => 'Count',
		'Цена за ед.' => 'Price per unit',
		'Итого' => 'In total',
		'не выбран' => 'not selected',
		'Итого к оплате' => 'Total to pay',
		'В корзине нет товара.' => 'Cart is empty.',
		'В корзине нет такого элемента.' => 'In the basket there is no such element.',
		'Ваш заказ успешно оформлен и направлен менеджеру, который свяжется с Вами в ближайшее время.' => 'Your order has successfully designed and sent to the manager, who will contact you shortly.',
		'Ваше примечание' => 'Your note',
		'Оформить заказ' => 'Checkout',
		'Для дальнейшего оформления заказа вам необходимо авторизоваться в системе.' => 'To further make an order, please log on the system.',
		'Ничего не забыли? Оформляем?' => 'Nothing is forgotten? Make out?',
		'удалить' => 'remove',
		'Удалить из списка' => 'Remove from the list',
	),
	'kz' => array(
		'Фото' => 'Photo',
		'Адрес' => 'Адресіңіз',
		'Корзина' => 'Қоржын',
		'Название и описание' => 'Name and description',
		'Размер' => 'Size',
		'Количество' => 'Count',
		'Цена за ед.' => 'Price per unit',
		'Итого' => 'In total',
		'не выбран' => 'not selected',
		'Итого к оплате' => 'Total to pay',
		'В корзине нет товара.' => 'Cart is empty.',
		'В корзине нет такого элемента.' => 'In the basket there is no such element.',
		'Ваш заказ успешно оформлен и направлен менеджеру, который свяжется с Вами в ближайшее время.' => 'Your order has successfully designed and sent to the manager, who will contact you shortly.',
		'Ваше примечание' => 'Your note',
		'Оформить заказ' => 'Checkout',
		'Для дальнейшего оформления заказа вам необходимо авторизоваться в системе.' => 'To further make an order, please log on the system.',
		'Ничего не забыли? Оформляем?' => 'Nothing is forgotten? Make out?',
		'удалить' => 'remove',
		'Удалить из списка' => 'Remove from the list',
	),
);

# Функция вывода товаров
function getRycleHtml($withButtons=false, $lang = 'ru'){
	global $api, $vars;
	
	$html = array(
		'<style>', 
		'table.rycle-content{ border-collapse:collapse; }',
		'table.rycle-content tr th{ border:1px solid #666; padding:10px; background:#999; color:white; }',
		'table.rycle-content tr td{ border:1px solid #666; padding:10px; text-align:left; }',
		'table.rycle-content tr td.center{ text-align:center; }',
		'table.rycle-content tr td.w150{ width:80px; text-align:center; }',
		'</style>', 
		'<table class="rycle-content">', 
		'<tr>'
	);
		if(!!$withButtons) $html[]='<th>'.$vars[$lang]['Фото'].'</th>';
		$html[]='<th>'.$vars[$lang]['Название и описание'].'</th>';
		$html[]='<th>'.$vars[$lang]['Количество'].'</th>';
		$html[]='<th>'.$vars[$lang]['Цена за ед.'].'</th>';
		$html[]='<th>'.$vars[$lang]['Итого'].'</th>';
		if(!!$withButtons) $html[]='<th>!</th>';
	$html[]='</tr>';
	
	# Итоговая стоимость
	$total_price = 0;
	
	foreach($_SESSION['rycle'] as $item){
		$total_price+=$item['price']*$item['count'];
		$html[]='<tr>';
			if(!!$withButtons) $html[]='<td>'.($item['pic']?'<img src="'._IMG_.'?url='._UPLOADS_.'/'.$item['pic'].'&w=100" class="pic">':'').'</td>';
			$html[]='<td><div><a '.$api->getLink($item['id']).' target="_blank">'.$item['name'].'</a></div><div>'.$item['anons'].'</div></td>';
			$html[]='<td class="center">'.$item['count'].'</td>';
			$html[]='<td class="center w150">'.number_format($item['price'], 0, ''," ").'</td>';
			$html[]='<td class="center w150">'.number_format($item['price']*$item['count'], 0, ''," ").'</td>';
			if(!!$withButtons) $html[]='<td><a href="#удалить" onclick="return removeItem(\''.$item['name'].'\', '.$item['id'].')">'.$vars[$lang]['удалить'].'</a></td>';
		$html[]='</tr>';
	}
	$html[]='<tr>';
		$html[]='<td colspan="'.(!!$withButtons?4:3).'">'.$vars[$lang]['Итого к оплате'].'</td>';
		$html[]='<td class="center w150">'.number_format($total_price, 0, ''," ").'</td>';
		if(!!$withButtons) $html[]='<td>&nbsp;</td>';
	$html[]='</tr>';
	$html[]='</table>';
	if ($total_price == 0) $html = array();
	return join("\n", $html);
}

# AJAX PART

if(isset($_REQUEST['getRycleHtml'])){
	if(empty($_SESSION['rycle']) || !is_array($_SESSION['rycle'])){
		exit($vars[$_REQUEST['lang']]['В корзине нет товара.']);
	}
	exit( getRycleHtml(true, $_REQUEST['lang']) );
}else if(!empty($_REQUEST['removeItem']) && is_numeric($id = $_REQUEST['removeItem'])){
	if(!empty($_SESSION['rycle'][$id])){
		unset($_SESSION['rycle'][$id]);
		exit('ok');
	}else exit($vars[$_REQUEST['lang']]['В корзине нет такого элемента.']);
}

# PUBLIC PART

$api->header(array('page-title'=>'<!--object:[135][18]-->'));

# Если пользователь зареген, в корзине есть товары и пришла переменная оформления заказа - оформляем
if(!empty($_SESSION['rycle']) && !empty($AUTH_USER) && isset($_POST['buy']) && !!($u = $api->objects->getFullObject($AUTH_USER['id'])) && $u['class_id']==$user_class_id){
	$theme = 'Новый заказ в магазине '.$_SERVER['HTTP_HOST'].'!';
	
	$cities = explode("\n", $api->db->select("fields", "WHERE `id`='".$cities_field_id."' LIMIT 1", "p3"));
	
	$html = array(
		'<br>',
		'<div><strong>ФИО</strong></div>',
		'<div>'.$u['фио'].'</div>',
		
		'<br>',
		'<div><strong>E-mail</strong></div>',
		'<div>'.$u['name'].'</div>',
		
		'<br>',
		'<div><strong>Город</strong></div>',
		'<div>'.$cities[$u['Город']].'</div>',
		
		'<br>',
		'<div><strong>Телефон</strong></div>',
		'<div>'.$u['Телефон'].'</div>'
	);	
	
	$html[]='<br><strong>Поля введенные в корзине</strong><br>';
	
	if(!empty($_POST['adres'])){
		$html[]='<br>';
		$html[]='<div><strong>Адрес</strong></div>';
		$html[]='<div>'.$_POST['adres'].'</div>';
	}
	if(!empty($_POST['buy'])){
		$html[]='<br>';
		$html[]='<div><strong>Примечание покупателя</strong></div>';
		$html[]='<div>'.$_POST['buy'].'</div>';
	}
	
	$api->mail->from = 'info@'.str_replace('www.','', $_SERVER['HTTP_HOST']);
	$api->mail->headers = 'X-Mailer: PHP/' . phpversion();
	$api->mail->subject = $theme;
	$api->mail->body = getRycleHtml(true, $api->lang);
	$api->mail->body.= join("\n", $html);
	
	$api->mail->send($manager_mail);
	unset($_SESSION['rycle']);
	echo '<div>'.$vars[$api->lang]['Ваш заказ успешно оформлен и направлен менеджеру, который свяжется с Вами в ближайшее время.'].'</div>';
	exit( $api->footer() );
# Если в корзине пусто - сразу в сад
}else if(empty($_SESSION['rycle']) || !is_array($_SESSION['rycle'])){
	echo '<div>'.$vars[$api->lang]['В корзине нет товара.'].'</div>';
	exit( $api->footer() );
}
?>
<div id="content-screen"></div>
<?
if(!empty($AUTH_USER)){
	$html = array(
		'<br>',
			
		'<form method="post" onsubmit="return checkSendForm()">',
		'<div>'.$vars[$api->lang]['Адрес'].'</div>',
		'<div><textarea name="adres" style="width: 315px; height: 95px;">'.$_SESSION['auth']['u']['adres'].'</textarea></div>',
		'<div style="margin-top: 10px;">'.$vars[$api->lang]['Ваше примечание'].'</div>',
		'<div><textarea name="buy" style="width:400px; height:100px; margin-bottom: 10px;"></textarea></div>',
		'<div><input type="submit" value="'.$vars[$api->lang]['Оформить заказ'].'"></div>',
		'</form>'
	);
	echo join("\n", $html);
}else{
	echo '<br><div>'.$vars[$api->lang]['Для дальнейшего оформления заказа вам необходимо авторизоваться в системе.'].'</div>';
}
?>
<script type="text/javascript">
//получение корзины в див
function getRycleHtml( div ){
	div.text('Загрузка..');
	$.get('<?=$_SERVER['SCRIPT_NAME']?>?getRycleHtml&lang=<?=$api->lang?>', {}, function(html){
		div.html(html);
	});
}
//удаление элемента из корзины
function removeItem(title, id){
	var vars = {
		ru : {
			goods : 'Товаров',
			add : 'Добавление в корзину',
			count : 'Количество',
			summ : 'На сумму',
			tg: ' тг.'
		},
		en : {
			goods : 'Goods',
			add : 'Add to cart',
			count : 'Count',
			summ : 'Summ',
			tg: ' tg.'
		},
		kz : {
			goods : 'Тауарлар',
			add : 'Қоржынға салу',
			count : 'Саны',
			summ : 'Бағасы',
			tg: ' тг.'
		}
	};
	if(!confirm("Удалить из списка "+title+"?")) return false;
	$.get('<?=$_SERVER['SCRIPT_NAME']?>', {removeItem:id, lang:'<?=$api->lang?>'}, function(status){
		$.getJSON(ajaxFile, {go:'getCount'}, function(text){
			$('.count_p').html(vars['<?=$api->lang?>']['goods'] + ': ' + text);
		});
		$.getJSON(ajaxFile, {go:'getSumm'}, function(text){
			var a=' '+text.toString();
			var b='';
			while(a.length) {
				if(b) b=' '+b;
				b=a.substr(a.length-3)+b
				a=a.substr(0,a.length-3);
			}
			$('.summ_p').html(vars['<?=$api->lang?>']['summ'] + ': ' + b + vars['<?=$api->lang?>']['tg']);
		});
		//если ОК, перезагрузка корзины
		if(status=='ok') return getRycleHtml( $('#content-screen') );
		//иначе пришла ошибка, алертим
		return alert(status);
	});
	return false;
}
//оформление заказа
function checkSendForm(){
	if(!confirm('<?=$vars[$api->lang]['Ничего не забыли? Оформляем?']?>')) return false;
	return true;
}

$(function(){
	getRycleHtml( $('#content-screen') );
});
</script>
<?
$api->footer();
?>