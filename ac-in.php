<?
include_once 'cms/public/api.php';
$api->header(array('page-title'=>'<!--object:[138][18]-->'));
?>
<script>
 function collapsElement(id) {
 if ( document.getElementById(id).style.display != "none" ) {
 document.getElementById(id).style.display = 'none';
 }
 else {
 document.getElementById(id).style.display = '';
 }
 }
 </script>
<div id="page_main">
    <div class="text_block">

		<section class="vkladki">
			<a href="javascript:collapsElement('id1')" title="" rel="nofollow">Текст ссылки1</a>
				<div id="id1" style="display: none; margin: 20px 20px 20px 40px; border: 1px solid #ECECEC;">
					<a href="javascript:collapsElement('id2')" title="" class="inner" rel="nofollow">Текст ссылки2</a>
						<div id="id2" style="display: none; margin: 5px; padding: 5px;">
							<p>
							В рамках программы корпоративных продаж ЗАО «Мерседес-Бенц РУС» разработало специальное предложение для транспортных компаний. Мы предлагаем Вашему вниманию специальную комплектацию флит-моделей Е200 и Е220 CDI.  </p>
						</div>

				</div>
			<a href="javascript:collapsElement('id3')" title="" rel="nofollow">Текст ссылки5</a>
				<div id="id3" style="display: none; margin: 10px 0 10px 20px;">
					<p>
							В рамках программы корпоративных продаж ЗАО «Мерседес-Бенц РУС» разработало специальное предложение для транспортных компаний. Мы предлагаем Вашему вниманию специальную комплектацию флит-моделей Е200 и Е220 CDI.  </p>
				</div>
			<a href="javascript:collapsElement('id4')" title="" rel="nofollow">Текст ссылки111</a>
				<div id="id4" style="display: none; padding: 5px;">
					<p>
							В рамках программы корпоративных продаж ЗАО «Мерседес-Бенц РУС» разработало специальное предложение для транспортных компаний. Мы предлагаем Вашему вниманию специальную комплектацию флит-моделей Е200 и Е220 CDI.  </p>
				</div>
		</section>			


    	<div class="clearfix"></div>      
	</div>
 </div>

<?
$api->footer();
?>