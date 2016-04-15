<?php $CURRENT_PAGE = "index"; require_once("inc/header.php"); ?>


<style>
.product_features li    { font-size:15px; }
.product_accessories li { font-size:15px; }
.product_description p  { font-size:15px; }
.product_description h3 { font-size:22px; }
.additional_info_table { border:1px solid #dddddd; }
.additional_info_table tr:nth-child(odd)  td { background-color:#FFFFFF; }
.additional_info_table tr:nth-child(even) td { background-color:#e5e5e5; }
.additional_info_table tr td:nth-child(2)    { font-size:14px; }

.dropdown-menu { min-width:225px; }

#to_top { cursor:pointer; position:fixed; top:20px; right:20px; color:#000; font-size:20px; line-height:14px; padding:10px; border:10px solid black; border-radius:10px; display:none; background-color:#F5FFFF; }
</style>


<div class="container">
   <div class="row">
      <div class="col-md-12">
		
      
<h2>Matches</h2>
<div id="search_results"></div>


<h2 id="product_data_header">Product Data</h2>
<div id="product_data"></div>

<div id="to_top">&#x25B2;</div>



		</div>
	</div>
</div>


<script>
jQuery(document.body).on('click', '.searchterm' ,function(){
	jQuery(".searchterm").each(function() {
		jQuery(this).removeClass("activesearch");
		});
	jQuery(this).addClass("activesearch");
	loadProductData(jQuery(this).attr('id'));
	});

jQuery(function(){
	jQuery('#prod_name_search')
		.data('timeout', null)
		.keyup(function(){
			clearTimeout(jQuery(this).data('timeout'));
			jQuery(this).data('timeout', setTimeout(doSearch, 400));
			});
	
	jQuery('.nav .dropdown ul.dropdown-menu li a').on('click', function() {
		showCatProducts(jQuery(this).data('category_id'));
		});
	});

function doSearch() {
	jQuery.ajax({
		type: 'POST',
		dataType: "html",
		url: '<?=Mage::getBaseUrl();?>vnprodcheck/dev/search',
		data: { 'searchterm': jQuery("#prod_name_search").val() },
		beforeSend : function (data) {
			jQuery("#search_results").html("<i>loading...</i>");
			},
		success: function (data) {
			jQuery("#search_results").html(data);
			}
		});
	}

function showCatProducts(category_id) {
	jQuery.ajax({
		type: 'POST',
		dataType: "html",
		url: '<?=Mage::getBaseUrl();?>vnprodcheck/dev/searchCat',
		data: { 'category_id': category_id },
		beforeSend : function (data) {
			jQuery("#search_results").html("<i>loading...</i>");
			},
		success: function (data) {
			jQuery("#search_results").html(data);
			}
		});
	}

function loadProductData(prod_id) {
	jQuery.ajax({
		type: 'POST',
		dataType: "html",
		url: '<?=Mage::getBaseUrl();?>vnprodcheck/dev/loadData',
		data: {'prod_id': prod_id},
		beforeSend : function (data) {
			jQuery("#product_data").html("<i>loading...</i>");
			},
		success: function (data) {
			jQuery("#product_data").html(data);
			window.scrollTo(0, jQuery("#product_data_header").offset().top);
			/*
			jQuery('html, body').animate({
				scrollTop: jQuery("#product_data_header").offset().top
				}, 2000);
			*/
			}
		});
	}

jQuery("#to_top").on('click', function() {
	jQuery('html, body').animate({
		scrollTop: 0
		}, 1000);
	});

function showHideToTop() {
	if((window.pageYOffset > 100) && (document.getElementById('to_top').style.display == "" || document.getElementById('to_top').style.display == "none")) {
		jQuery("#to_top").show(200);
		//document.getElementById('to_top').style.display = "block";
		}
	else if((window.pageYOffset <= 100) && document.getElementById('to_top').style.display == "block") {
		jQuery("#to_top").hide(200);
		//document.getElementById('to_top').style.display = "none";
		}
	}

window.onscroll = showHideToTop;
jQuery(document).ready(showHideToTop);

</script>

<?php require_once("inc/footer.php"); ?>