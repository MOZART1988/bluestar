<?
include_once 'cms/public/api.php';
$api->header(array('page-title'=>'<!--object:[138][18]-->'));
?>
<script src="/js/responsivethumbnailgallery.js"></script>
<div id="page_main">
    <div class="text_block">    	
	<section id="fototabs1">	
		<div id="tabs">
			<div id="tabs-1">
				<img src="/cms/uploads/1.jpg" alt="">
			</div>
			<div id="tabs-2">
				<img src="/cms/uploads/2.jpg" alt="">
			</div>
			<div id="tabs-3">
				<img src="/cms/uploads/3.jpg" alt="">
			</div>
			<div id="tabs-4">
				<img src="/cms/uploads/4.jpg" alt="">
			</div>
			<div id="tabs-5">
				<img src="/cms/uploads/5.jpg" alt="">
			</div>
			
			<ul>
				<li><a href="#tabs-1"><div><img src="/cms/uploads/1.jpg" alt=""></div></a></li>
				<li><a href="#tabs-2"><div><img src="/cms/uploads/2.jpg" alt=""></div></a></li>
				<li><a href="#tabs-3"><div><img src="/cms/uploads/3.jpg" alt=""></div></a></li>
				<li><a href="#tabs-4"><div><img src="/cms/uploads/4.jpg" alt=""></div></a></li>
				<li><a href="#tabs-5"><div><img src="/cms/uploads/5.jpg" alt=""></div></a></li>
			</ul>
		</div>
	</section>
	<script type="text/javascript">
		$(document).ready(function() {
	    	var gallery = new $.ThumbnailGallery($('#gallery'), {	        
	        smallImages: '/cms/uploads/small',
	        largeImages: '/cms/uploads/large',
	        count: 10,
	        thumbImageType: 'jpg',
	        imageType: 'jpg',
	        breakpoint: 600,
	        shadowStrength: 1
    		});
		});
	</script>
    <div class="clearfix"></div>      
	</div>
 </div>

<?
$api->footer();
?>