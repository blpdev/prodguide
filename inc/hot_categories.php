<style>
.rotate-180 {
	-moz-transform: rotate(180deg);
	-webkit-transform: rotate(180deg);
	-o-transform: rotate(180deg);
	-ms-transform: rotate(180deg);
	}
#toggle {
  position:absolute;
  width: 50px;
  height: 30px;
  bottom: -15px;
  left:50%;
  font-size: 48px;
  text-align:center;
  border-radius:5px;
  background-color:grey;
  line-height:120%;
  padding:2px 0px 2px 0px;
  cursor: pointer;
  z-index:10;
}
.navbar{
   margin-bottom:0px;
}
#menu li {
   list-style-type: none;
    color: black;  
}
#myTabContent li{
   line-height: 16px;
   font-size:14px;
   padding:10px 0px;
   }
#toggle div {
  position:aboluste;
  width: 100%;
  height: 5px;
  background-color: grey;
  margin: 4px auto;
  transition: all 0.3s; 
  backface-visibility: hidden;
}
.tab-pane li {
	text-align:left;
	}
.nav-tabs > li{
   background-color:white !important;
   }
.nav-tabs > li.active > a,
.nav-tabs > li.active > a:hover,
.nav-tabs > li.active > a:focus {
  color: #555;
  cursor: default;
  background-color: #F5FFFF !important;
  border: 1px solid #ddd;
  border-bottom-color: #F5FFFF !important;
}
/*ul.pagination.bootpag {
    position: absolute;
    bottom: 225px;
    right: 20px;
}*/
#menu {
  color: black;
  width: 100%;
  /*height: 500px;*/
  top:65px;
  border-radius: 3px;
  font-family: "Century Gothic";
  text-align: center;
  display: none;
  background-color:#F5FFFF;
}
ul.nav-tabs li {
   margin-left:20px;
   }
ul.nav-tabs li a {
   outline:0;
   }
ul.pagination {
	margin: 0 20px 20px 0;
	}
.tab_main_content li a { outline:0; }
.menu_nav_area {
	clear:both;
	background-color:#F5FFFF;
	text-align:right;
   display:none;
	}
.add_to_pdf_icon { height:17px; width:50px; opacity:0.6; cursor:pointer; }
</style>
<?php
function getCategoryProducts($category_id) {
	$collection_of_products = Mage::getModel('catalog/product')->getCollection()
		->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		->addAttributeToSelect('*')
		->addAttributeToFilter('category_id', array(
			array('in' => $category_id)
			))
		->setOrder('most_popular_count', 'DESC')
		->setOrder('name', 'ASC');
	$collection_of_products->getSelect()
		->reset(Zend_Db_Select::ORDER)
		->order(array('CAST(`most_popular_count` AS UNSIGNED) DESC', 'name ASC'));
	return $collection_of_products;
	}
function outputCategoryAreas($category_name, $category_id) {
	$products_per_column = 10;
	$columns_per_row = 4;
	$products = getCategoryProducts($category_id);
	$products_count = count($products);
	$number_of_pages = ceil($products_count / 4 / $products_per_column);
	$this_one = 0;
	$this_col = 1;
	$this_page = 0;
	foreach($products as $product) {
		if(($this_one == 0) || ($this_col == 5)) {
			if($this_one !== 0) { echo('
				   </div>
				</div>'); }
			$this_col = 1;
			$this_page++;
			echo('
				<div id="'.$category_name.'_content_'.$this_page.'" style="display:none;">
					<div class="row">');
						}
		if($this_one % $products_per_column == 0) {
			echo('
						<div class="col-sm-3">
							<ul>
');
						}
					?>
								<li style="text-indent:-18px;"><input type="checkbox" class="pdf_generator_checkselector" id="<?=$product->getId();?>" style="margin-right:3px;" data-product_id="<?=$product->getId();?>" data-product_name="<?=str_replace(array("'", '"',), '', $product->getName());?>"><?=($this_one+1);?>. <a href="#" onclick="loadProductData(<?=$product->getId();?>); return false;"><?=$product->getName();?></a></li>
<?php
		if((($this_one+1) % $products_per_column == 0) || ($this_one+1 == $products_count)) {
			echo("
							</ul>
						</div>");
			$this_col++;
			}
		if($this_one+1 == $products_count) {
			echo("
				   </div>
				</div>");
			}
		$this_one++;
		}
	?>
            <div id="<?=$category_name;?>_nav" class="menu_nav_area">
            	<div style="color:#777; float:left; padding:5px 0 5px 22px;"><input id="add_all_<?=$category_name;?>" type="checkbox" class="add-all-to-pdf" data-section="<?=$category_name;?>"> <label for="add_all_<?=$category_name;?>" style="cursor:pointer;">Add all visible <?=ucwords($category_name);?> products to PDF?</label></div>
            </div>
<script>
$('#<?=$category_name;?>_nav')
	.bootpag({
		total:<?=$number_of_pages;?>,
		page:1,
		maxVisible:10
		})
	.on('page', function(event, num){
		$("#<?=$category_name;?>_main_content").html($("#<?=$category_name;?>_content_"+num).html());
		jQuery("#myTabContent").find("input[type='checkbox']").prop('checked', false);
		});
</script>
   <?php
	}
?>
<div class="container" style="position:relative;">
   <div class="row" id="menu">
      <ul class="nav nav-tabs nav-justified">
         <li class="active" data-target="portable"><a href="#portable" data-toggle="tab" aria-expanded="true">Portable</a></li>
         <li class="" data-target="desktop"><a href="#desktop" data-toggle="tab" aria-expanded="true">Desktop</a></li>
         <li class="" data-target="herbpens"><a href="#herbpens" data-toggle="tab" aria-expanded="true">Herb Pens</a></li> 
         <li class="" data-target="waxpens"><a href="#waxpens" data-toggle="tab" aria-expanded="true">Wax Pens</a></li>
         <li class="" data-target="ejuice"><a href="#ejuice" data-toggle="tab" aria-expanded="true">E-juice Pens</a></li>
      </ul>
      <div id="myTabContent" class="tab-content" style="border:1px solid #DDDDDD; border-top:0;">
         <div class="tab-pane fade active in" id="portable">
            <div id="portable_main_content" class="tab_main_content"></div>
            <?php
				outputCategoryAreas('portable', 23);
				?>
         </div>
      	<div class="tab-pane fade active in" id="desktop">
            <div id="desktop_main_content" class="tab_main_content"></div>
            <?php
				outputCategoryAreas('desktop', 421);
				?>
         </div>
         <div class="tab-pane fade active in" id="herbpens">
            <div id="herbpens_main_content" class="tab_main_content"></div>
            <?php
				outputCategoryAreas('herbpens', 345);
				?>
         </div>
         <div class="tab-pane fade active in" id="ejuice">
            <div id="ejuice_main_content" class="tab_main_content"></div>
            <?php
				outputCategoryAreas('ejuice', 24);
				?>
         </div>  
         <div class="tab-pane fade active in" id="waxpens">
            <div id="waxpens_main_content" class="tab_main_content"></div>
            <?php
				outputCategoryAreas('waxpens', 422);
				?>
         </div>            
      </div>
   </div>
   <div id="toggle">
   &#42780;
   </div>
</div>
<script>
$("#toggle").click(function() {
	$(this).toggleClass("on");
	$("#menu").slideToggle();
	});
$(document).ready(function(){
	$("#portable_main_content").html($("#portable_content_1").html());
	$("#portable_nav").show();
   jQuery(".tab_main_content").each(function() {
      jQuery(this).html(jQuery(this).next('div').html());
      if(jQuery(this).parent().attr('id') != "portable") {
         jQuery(this).hide();
         }
      });
	var max_height = 0;
	jQuery("div[id*='_content_']").each(function(){
		var this_div = jQuery(this);
		var tempId = 'tmp-'+Math.floor(Math.random()*99999);//generating unique id just in case
		this_div.clone()
			.css('position','absolute')
			.css('height','auto').css('width','1000px')
			.appendTo(jQuery('body'))
			.css('left','-10000em')
			.addClass(tempId).show()
		max_height = Math.max(max_height, $('.'+tempId).height());
		console.log("Height of " + jQuery(this).attr('id') + " is " + $('.'+tempId).height());
		$('.'+tempId).remove();
		});
	console.log(max_height);
	jQuery(".tab_main_content").css('height', max_height+'px');
   });
$("ul.nav-tabs").find('li').on('click', function() {
   //$(this).parent().parent().find('div#myTabContent').html($("#portable_content_1").html());
   console.log(jQuery(this).data('target'));
   jQuery("#"+jQuery(this).data('target')).find('.tab_main_content').show();
   jQuery("#"+jQuery(this).data('target')).find('.menu_nav_area').show();
   });
$(function() {
	var expanded = false;
	$('#toggle').click(function() {
		$(this).toggleClass("rotate-180");
		if (!expanded) {
			//$(this).animate({'top' : '550px'}, {duration : 400});
			expanded = true;
			}
		else {
			//$(this).animate({'left' : '50%', 'top' : '50px'}, {duration: 400});
			expanded = false;
			}
		});
	});
</script>
<script>//jQuery(document).ready(function() { jQuery("#toggle").trigger('click'); });</script>