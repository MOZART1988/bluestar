//Подсчет результатов на изменение одного радио баттона

function getResultFromOneRadio(tab_href, radio){
	var name = radio.attr("fieture-name")||"";
	var price = radio.attr("price")||"";
	var text1 = radio.attr("text1")||"";
	var text2 = radio.attr("text2")||"";
	var text3 = radio.attr("text3")||"";
	var modelResult = '<div class="single-result row">'
							+'<div class="col-lg-3 col-md-3 col-sm-3 name">'+name+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 text1">'+text1+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 text2">'+text2+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 text3">'+text3+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 price">'+price+' тг</div>'
							+'<div style="clear:both"></div>'
							+ '</div>';					
	return modelResult;
}

function getResultFromOptions(tab){
	var all = tab.find("input[type=radio]:checked");
	var result = '';
	all.each(function(index){
		var name = $(this).attr("fieture-name")||"";
		var price = $(this).attr("price")||"";
		var text1 = $(this).attr("text1")||"";
		var text2 = $(this).attr("text2")||"";
		var text3 = $(this).attr("text3")||"";
		result += '<div class="single-result row">'
							+'<div class="col-lg-3 col-md-3 col-sm-3 name">'+name+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 text1">'+text1+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 text2">'+text2+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 text3">'+text3+'</div>'
							+'<div class="col-lg-3 col-md-3 col-sm-3 price">'+price+' тг</div>'
							+'<div style="clear:both"></div>'
							+ '</div>';				
	});
	return result;
}

function getColorOrWheel(img){
	result = '';
	var name = img.attr("title")||"";
	var price = img.attr("price")||"";
	if(price){
		price_div = '<div class="price col-lg-4 col-md-4 col-sm-4" style="float:right padding:10px;">'+price+' тг</div>';
	}else price_div = '<div class="col-lg-4 col-md-4 col-sm-4" style="float:right padding:10px;"></div>';
	result ='<div class="single-result row">'
			+'<div class="col-lg-4 col-md-4 col-sm-4" style="float:left; padding-top:10px; padding-bottom:10px;">'+name+'</div>'
			+price_div
			+'<div style="clear:both"></div>'
	return result;		
}


function getWheels(color_id){
    var content = $('div.ajax-wheel-load');
	$.ajax({
		url: ajaxFile,
		data: {
			go: 'load_wheel',
			color_id: color_id
		},
		type: "POST",
		dataType: "html",
		cashe: false,
		beforeSend: function(){
			content.html('<div class="wheels" style="width:400px; float:left;"><h3>Загружаем</h3></div>');
		},
		success: function(server){
			content.html(server);
			
			
			// Загрузка дисков
			
			$("img.wheel").bind('mouseover', function() {
				var imgAttr = $(this).attr('src');
				var title = $(this).attr('title');
				var alt = $(this).attr('alt');
				var from = imgAttr.search('image=/cms/');
				var to = imgAttr.length;
				var newImg = imgAttr.substring(from, to);
				$(".wheels .bigprew .bigpic > img").attr({src: '/cms/image.php?w=69&h=66&'+newImg});
				$('p.wheel-title').html(title).append('<span style="font-weight:normal;"> '+alt+'</span>');
			});
			
			$("img.wheel").bind('click', function() {
				var img_to_load = $(this).attr('img-to-load');
				if(img_to_load){
					$("#mainphoto").html('<img src="'+img_to_load+'"/>');
					$("#mainphoto img").hide().delay(200).fadeIn(800);
					window.global_wheels_result = getColorOrWheel($(this));
				}
			});
			
		},
		error: function(){alert('Не удалось отправить');}
	});
}


//Функции ажакс загрузки активного таба

function goAjax(id, getResult){
	getResult:getResult||"";
	id:id||"";
	$('.tab-pane').html('');
	var content = $('.tab-pane.active');
	var parent_id = $('.config-li.active').attr('data-id');
	$.ajax({
		url: ajaxFile,
		data: {
			go: "loadTabContent",
			tab_id: parent_id,
			getResult:getResult,
			model:window.global_model_result,
			lines:window.global_lines_result,
			colors:window.global_colors_result,
			wheels:window.global_wheels_result,
			decors:window.global_decors_result,
			options:window.global_add_result
		},
		type: "POST",
		dataType: "html",
		cashe: false,
		beforeSend: function(){
			content.html('<h3>Загружаем...</h3>');
		},
		success: function(server){
			content.html(server);
			
			var price = 0;
			
			/*Загрузка картинки при переключении радиобаттонов*/
			
			$("input[type=radio]").on("change", function(){
				var mainImg = $(this).attr('img-to-load');
				var global_pict = $("#mainphoto").attr('default');
				var tab_href = $(".config-li.active a").attr("href");
				var current_tab = $(".tab-content-inner")||"";
				if(mainImg && $(this).is(":checked")){
					$("#mainphoto").html('<img src="'+mainImg+'"/>');
					$("#mainphoto img").hide().delay(200).fadeIn(800);
				}else{
					$("#mainphoto").html('<img src="'+global_pict+'"/>');
					$("#mainphoto img").hide().delay(200).fadeIn(800);
				}
				if(tab_href==='#model'){
					window.global_model_result = getResultFromOneRadio(tab_href, $(this));
				}else if(tab_href==='#options'){
					window.global_add_result = getResultFromOptions(current_tab);
				}else if(tab_href === '#lines'){
					window.global_lines_result = getResultFromOneRadio(tab_href, $(this));
				}	
			});
			
			/*Первый радиобаттон у нас всегда активный*/
			
			$("input[type=radio]:first").trigger('click');
			
			
			/*Загрузка инфы при клике*/
			
			if('div.description-open'){
			
				$('a.description-info').click(function(){
					$(this).next().fadeToggle(600);
					if( $(this).next().length > 0 ) {
						createOverlay();
					} else { console.log('Нет информации'); }
				});
				$('a.description-close').click(function(){
					$(this).parent().parent().fadeToggle(600);
					closeOverlay();
				});
			}
			
			/*Переход на следующий таб при клике на кнопку далее*/
			
			$('button.next').click(function(){
				$('li.config-li.active').next().children('a').trigger('click');
			});
			
			/*Загрузка главной картинки при загрузке таба*/
			
			var mainImg = $('.color-block img:first-child, .colors-inner-block img:first-child').attr('img-to-load');
			if(mainImg){
				$("#mainphoto").html('<img src="'+mainImg+'"/>');
				$("#mainphoto img").hide().delay(200).fadeIn(800);
			}
			
			/*Подгрузка дисков в первый раз по дефолту от первого цвета*/
			
			var first_color_id = $('.color-block img:first-child').attr('color-id');
			getWheels(first_color_id);
			
			/*Загрузка цветов*/
			
			$("img.color").bind('mouseover', function() {
				var imgAttr = $(this).attr('src');
				var title = $(this).attr('title');
				var alt = $(this).attr('alt');
				var from = imgAttr.search('image=/cms/');
				var to = imgAttr.length;
				var newImg = imgAttr.substring(from, to);
				$(".colors .bigprew .bigpic > img").attr({src: '/cms/image.php?w=69&h=66&'+newImg});
				$('p.color-title').html(title).append('<span style="font-weight:normal;"> '+alt+'</span>');
			});
			
			$("img.color").bind('click', function() {
				var img_to_load = $(this).attr('img-to-load');
				getWheels($(this).attr('color-id'));
				if(img_to_load){
					$("#mainphoto").html('<img src="'+img_to_load+'"/>');
					$("#mainphoto img").hide().delay(200).fadeIn(800);
					window.global_colors_result = getColorOrWheel($(this));
				}
			});
			
			
			
			/*Загрузка цветов обивки*/
			
			
			$("img.color-inner").bind('mouseover', function() {
				var imgAttr = $(this).attr('src');
				var title = $(this).attr('title');
				var alt = $(this).attr('alt');
				var from = imgAttr.search('image=/cms/');
				var to = imgAttr.length;
				var newImg = imgAttr.substring(from, to);
				$(".colors-inner .bigprew .bigpic > img").attr({src: '/cms/image.php?w=69&h=66&'+newImg});
				$('p.color-inner-title').html(title).append('<span style="font-weight:normal;"> '+alt+'</span>');
			});
			
			$("img.color-inner").bind('click', function() {
				var img_to_load = $(this).attr('img-to-load');
				if(img_to_load){
					$("#mainphoto").html('<img src="'+img_to_load+'"/>');
					$("#mainphoto img").hide().delay(200).fadeIn(800);
					window.global_decors_result = getColorOrWheel($(this));
				}
			});
			
			/*Добавление активного класса юлу и табу в дополнительном оборудование*/
			
			$('ul.nav.nav-pills li:first-child').addClass('active');
			$('div#tb0').addClass('active');
			
			/*Подсчет общей цены*/
			
			var tab_href = $(".yours.active a").attr("href") || "";
			if(tab_href == "#finish"){
				var modal = '<div class="modal fade bs-example-modal-sm" id="price_final" tabindex="-1" role="dialog" aria-hidden="true">'
								+ '<div class="modal-dialog">'
								+ '<div class="modal-content">'
								+ '<div class="modal-header">'
								+ '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
								+ '<h4 class="modal-title" style="text-align:center">Ваш Mersedes-Benz</h4>'
								+ '</div>'
								+ '<div class="modal-body" style="texxt-align:center">'
								+ '</div>'
								+ '</div>'
								+ '</div>'
								+ '</div>';
				$("#footer").append(modal);				
				var global_price = 0;
				var pattern = '([0-9]+)';
				var all_price = $("div.price");
				if(all_price){
					all_price.each(function(index){
						var current = $(this).html();
						var price = parseInt(current.replace(/\D+/g,""));
						global_price+=price;
					});

					$('div.modal-body').html("<h1 style='text-align:center;'>Ваша цена : <span>"+global_price+" тг</span></h1>")
					$("#price_final").modal("toggle");
				}
			}
		},
		error: function(){alert('Не удалось отправить');}
	});
}

//Затемнение экрана

// iskudrat 07.08.14
function createOverlay() {
	var docHeight = $(document).height();
	$('body').append('<div id="overlay"></div>');
	$("#overlay").height(docHeight).css({
		'display': 'block',
		'opacity': 0.6,
		'position': 'absolute',
		'top': 0,
		'left': 0,
		'background-color': 'black',
		'width': '100%',
		'z-index': 5000
	});
}
function closeOverlay() {
	$("#overlay").css({
		'display': 'none'
	});
}


$(function(){
	//Инициализация
	
	
	var flo = $('.floating_object').hide();
	var tar = $('.target_obj');
	
	var global_model_result = '';
	var global_lines_result = '';
	var global_colors_result = '';
	var global_wheels_result = '';
	var global_decors_result = '';
	var global_add_result = '';
	var global_price = 0;
	
	tar.mouseenter(function(){
	    var posTop = $(this).offset().top;
	    var posLeft = $(this).offset().left;
	    var minW = $(this).width();
	    $(this).next(flo).css({
	        top: posTop,
	        left: posLeft,
	        minWidth: minW
	    }).fadeIn();
	});

	flo.mouseout(function(){
	    $(this).delay(500).fadeOut();
	});
	
	$(function () {
		$('#myTab a:first').tab('show')
	})
	
	function func(el){
		var right = el.style.right;
		d = document.getElementById('up');
		d.style.right = "20px";
		d.style.display = 'block';
	}
	
	//Выделение активного таба и активного содержимого в конфигураторе
	
	$('.config-tab-menu li:first-child').addClass('active');
	$('.config-tab-content div:first-child').addClass('active');
	
	
	//Ажакс загрузка активного таба при загрузке страницы в первый раз
	
	var id = $('.tab-pane.active').attr('data-id');
	if(id){
		goAjax(id);
		//tab pane #model Выделение первого радиобаттона
	}
	
	//Загрузка табов при событии показа таба, проверяет, если таб не последний, то грузит в нормальном режиме,
	// а иначе грузит функцию подсчета результатов
	
	$('a[data-toggle="tab"]').on('shown.bs.tab', function () {
		var href = $(this).attr("href");
		var id = $('.tab-pane.active').attr('data-id');
		if(href!="#finish"){
			if(id){
				goAjax(id);
			}
		}else{
			goAjax("", "getResult", window.global_model_result, window.global_lines_result, window.global_color_result, window.global_wheels_result, window.global_decors_result, window.global_add_result);
		}
	});
	
	//Конец жаваскрипта конфигуратора
});

