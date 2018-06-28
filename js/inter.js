$(document).ready(function(){



// dropmenu1

    $('.head_menu > ul > li:has(ul)').hover(

        function() {

            $(this).addClass('open');

            $(this).find('ul').stop(true, true);

            $(this).find('> ul').slideDown();

        },

        function() {

            $(this).find('ul').stop(true, true);

            $(this).removeClass('open');

            $(this).find('> ul').hide();

        }

    );



// dropmenu2 class

    $('.menu > ul > li:has(ul)').hover(

        function() {

            $(this).addClass('open');

            $(this).find('ul').stop(true, true);

            $(this).find('> ul').slideDown();

        },

        function() {

            $(this).find('ul').stop(true, true);

            $(this).removeClass('open');

            $(this).find('> ul').hide();

        }

    );

// dropmenu models

    $('.menu > ul > li:has(div.mod_list)').hover(

        function() {

            $(this).addClass('open');

            $(this).find('div.mod_list').stop(true, true);

            $(this).find('> div.mod_list').slideDown();

        },

        function() {

            $(this).find('div.mod_list').stop(true, true);

            $(this).removeClass('open');

            $(this).find('> div.mod_list').hide();
			
			$('.menu > ul > li:has(div.mod_list) ul > li:first-child').addClass('open');

        }

    );

// dropmenu models2

	// iskudrat 30.07.14
	$('.mod_main > ul > li:has(div.mod_text)').removeClass('active');
	$('.mod_main > ul > li:first-child').addClass('open');

    $('.mod_main > ul > li:has(div.mod_text)').hover(

        function() {

            $(this).removeClass('active');

            $(this).addClass('open');

        },

        function() {

            $(this).removeClass('open');

        }

    );



// dropmenu addmenu

    $('.add_ind_menu').hover(

        function() {

            $(this).addClass('active');

            $('.list_add_menu').stop(true, true);

            $('.oth_menu').stop(true, true);

            $('.list_add_menu').slideDown(400);

            $('.oth_menu').slideUp(400);

        },

        function() {

            $('.list_add_menu').stop(true, true);

            $('.oth_menu').stop(true, true);

            $(this).removeClass('active');

            $('.list_add_menu').slideUp(400);

            $('.oth_menu').slideDown(400);

        }

    );



    $('.add_ind_menu .tit').click(

        function() {

            $('.list_add_menu').stop(true, true);

            $('.oth_menu').stop(true, true);

            $('.add_ind_menu').removeClass('active');

            $('.list_add_menu').slideUp(400);

            $('.oth_menu').slideDown(400);

        }

    );

// dropmenu towar page

    $('ul.sidebar_menu > li:not(.active):has(ul)').hover(

        function() {

            $(this).addClass('open');

            $(this).find('ul').stop(true, true);

            $(this).find('> ul').slideDown();

        },

        function() {

            $(this).find('ul').stop(true, true);

            $(this).removeClass('open');

            $(this).find('> ul').hide();

        }

    );



    $('ul.sidebar_menu > li:not(.active) > ul > li:has(ul)').hover(

        function() {

            $(this).addClass('open');

            $(this).find('ul').stop(true, true);

            $(this).find('> ul').slideDown();

        },

        function() {

            $(this).find('ul').stop(true, true);

            $(this).removeClass('open');

            $(this).find('> ul').hide();

        }

    );

    $('ul.sidebar_menu li:has(ul)').addClass('mhas');


    $('ul.sidebar_menu li > ul > li ul li a').click(
        function() { 
        document.location.href=$(this).attr('href');
    });

//accordeon

	
	var global_li = $('ul.sidebar_menu.accord_menu li ul li.active');
	global_li.parent().parent().addClass('active');
	

    $('ul.sidebar_menu > li > ul > li:has(ul)').toggle(

        function() {

            $(this).addClass('active');

            $(this).find('ul').stop(true, true);

            $(this).find('> ul').slideDown(200);

        },

        function() {

            $(this).removeClass('active');

            $(this).find('> ul').slideUp(200);

        }

    );
	
// iskudrat 30.07.14
	/* $('ul.sidebar_menu').hover(
		function() {
			alert('over');
		}
		function() {
			alert('out');
		}
	); */
	$('ul.sidebar_menu > li.mhas.active > ul > li.active.mhas > ul > li.active a').css('color','#00adef');
	$('ul.sidebar_menu > li.mhas.active:has(ul)').mouseout(function() {
		$(this).addClass('open');
		$(this).children('ul').css('display','block').children('.active.mhas').children('ul').css('display','block');
	});



//tabs



    $('.tabs a').click(function(){

        $('.type_list').removeClass("on").hide();

        $('.tabs a').removeClass("on");

        $(this).addClass("on");

        $($(this).attr("href")).addClass("on").show();

        return false;

    });



//tabs text



    $('.tab_row a').click(function(){

        $('.type_cont').removeClass("on").hide();

        $('.tab_row a').removeClass("on");

        $(this).addClass("on");

        $($(this).attr("href")).addClass("on").show();

        return false;

    });

// fancybox

    $('.fancy')

        .fancybox({

            padding: 	10,

            opacity: 	true

        });



// service random

    $('.service_rand > ul > li:has(div.cont_serv)').hover(

        function() {



            $('.service_rand > ul > li > a').stop(true, true);

            $(this).find('div.cont_serv').stop(true, true);

            $('.service_rand > ul > li > a').animate({'opacity':'0'}).hide();

            $(this).find('div.cont_serv').show().animate({'opacity':'1'},500);



        },

        function() {



            $('.service_rand > ul > li > a').stop(true, true);

            $(this).find('div.cont_serv').stop(true, true);

            $('.service_rand > ul > li > a').show().animate({'opacity':'1'});

            $(this).find('div.cont_serv').animate({'opacity':'0'},200).hide();

        }

    );



    /*	$('.accordeonMenu > ul > li ul li a').click(

     function() {

     document.location.href=$(this).attr('href');

     });*/







// bx_slider

    $('.bxslider').bxSlider({

        pagerCustom: '#bx-pager',

        mode: "fade", //'vertical'

        speed: 1000,

        auto: true,

        //autoControls: true,

        pause: 4000,

        pager: true,

        controls: false

    });



// bx_slider2 towar

    $('#bxslider2').bxSlider({

        mode: "fade", //'vertical'

        speed: 1000,

        auto: false,

        //autoControls: true,

        pause: 4000,

        pager: false,

        controls: false

    });



// model all

    $('#bxslider3').bxSlider({

        pagerCustom: '#bx-pager3',

        speed: 1000,

        //autoControls: true,

        pause: 4000,

        pager: true,

        controls: false,

        infiniteLoop: false,

        auto: false,

        //autoControls: true,

        slideMargin: 5,

        minSlides: 1

    });





    $('.credit_cat li').click(function() {



        index = parseInt($(this).index());



        $('.fullimg').fadeOut().remove();

        $('.bxslider4').fadeIn('slow');



        // bx_slider credit

        $('#bxslider4').bxSlider({

            pagerCustom: '#bx-pager4',

            mode: "fade", //'vertical'

            speed: 1000,

            auto: false,

            //autoControls: true,

            startSlide: index,

            pause: 4000,

            pager: true,

            controls: false

        });

    })







// bx_slider text page

    $('.bxslider5').bxSlider({

        pagerCustom: '#bx-pager5',

        mode: "fade", //'vertical'

        speed: 1000,

        auto: true,

        //autoControls: true,

        pause: 4000,

        pager: true,

        controls: false

    });

    $('.sl_mod_row a').hover(

        function() {

            $('.sl_mod_row a span.cost').animate({'opacity':'1'},1000);

            $('.sl_mod_row a span.cost').stop(true, true);



        },

        function() {

            $('.sl_mod_row a span.cost').animate({'opacity':'0'},500);

            $('.sl_mod_row a span.cost').stop(true, true);

        }

    );



});



// iskudrat 30.07.14
window.onload = function() {
	var _content = $('#content');
	var _towar = $('#page_towar .towar_menu .wrap').height();
	var _result = _towar + 50;
	
	_content.css({'min-height':_result});
}

$(document).ready(function(){

    $('.service-phone').mask("+7 999-999-99-99");

    $('body').on('click', '.modal-close', function(){
        $('body').find('.modal-message-form').css('opacity', 0);
    });

    $('body').on('click', '.calend', function(){
        $('.date-picker').datepicker('show');
    });

    $('#form-ajax-select').on('change', function(){
        $.ajax({
            url : '/ru/passengercars/page/5428/5367/?theme=' + $(this).val(),
            dataType: 'html',
            success: function (data){
                $('.ajax-part-multiform').html($(data).find('.ajax-part-multiform').html());
                $('.date-picker').datepicker();

                if ($('.ajax-form-year option:selected').val() <= 2013) {
                    $('body').find('.modal-message-form').css('opacity', 1);
                } else {
                    $('body').find('.modal-message-form').css('opacity', 0);
                }

                $('.ajax-form-year').on('change', function(){
                    if ($('.ajax-form-year option:selected').val() <= 2013) {
                        $('body').find('.modal-message-form').css('opacity', 1);
                    } else {
                        $('body').find('.modal-message-form').css('opacity', 0);
                    }
                });
            }
        });
    });
});

