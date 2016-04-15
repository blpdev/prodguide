<?php
$show_pdf_generator = false;
$additional_js = "";
switch($CURRENT_PAGE) {
	case "index":
		$show_pdf_generator = true;
		break;
	case "learning_center":
	
		break;
	case "quiz":
		$additional_js .= '<script src="/prodguide/inc/slickquiz/js/slickQuiz-config.js"></script><script src="/prodguide/inc/slickquiz/js/slickQuiz.js"></script>';
		break;
	}
?>

<?php if($show_pdf_generator == false) { ?>

<footer class="footer" style="z-index:999;">
   <div class="container">
   	<p style="margin:20px 0; float:left;">&copy; <?=date('Y');?> VaporNation.com</p>
   </div>
</footer>

<?php } else { ?>

<style>
.pdf_generator_info { color:#666; line-height:60px; }
.pdf_generator { min-width:300px; background-color:#FFF; position:absolute; bottom:0; right:0; border:1px solid black; display:none; max-height:275px; overflow-x:hidden; overflow-y:scroll; }
.pdf_generator h3 { border-bottom:1px solid black; text-align:center; margin:0; padding:0 5px; }
.pdf_generator ol.product_list { margin:0; padding:0px 10px 0px 30px; text-indent:-17px; }
.pdf_generator ol.product_list li.item { padding:5px 0; }
.pdf_generator ol.product_list li.item .remove_from_pdf_icon { cursor:pointer; }
<?php /*?>.pdf_generator .generate_pdf { position:absolute; bottom:0; cursor:pointer; text-align:center; border-top:1px dotted black; width:100%; background-color:#FFF; }
<?php */?>
.ui-sortable {
  list-style: none;
}
.ui-sortable > li:not(.ui-sortable-placeholder) {
  counter-increment: number;
}
.ui-sortable > li:before {
  content: counter(number)'. ';
}
.ui-sortable-placeholder {
  visibility: hidden;
}
</style>

<footer class="footer" style="z-index:999;">
   <div class="container">
   	<p style="margin:20px 0; float:left;">&copy; <?=date('Y');?> VaporNation.com</p>
      
      <div style="float:right; position:relative; height:60px; width:300px;">
      	<div class="pdf_generator_info">
	         Click checkboxes to add products to PDF
         </div>
      	<div class="pdf_generator">
            
            <div style="border-bottom:1px solid #000; line-height:16px; display:none;" id="generate_pdf_link_input">
            	<input type="text" style="width:100%; font-size:11px;" value="" onclick="this.select();">
            </div>
            
            <div>
            	<h3>PDF Generator <img id="generate_pdf" src="images/save_icon.png" class="generate_pdf" style="padding-left:5px; cursor:pointer; margin-top:-4px;"> <img id="generate_pdf_link" src="images/link.png" class="generate_pdf_link" style="padding-left:5px; cursor:pointer; margin-top:-4px;"></h3>
            </div>
            
            <ol class="product_list"></ol>
            <?php /*?><div class="generate_pdf">Generate PDF</div><?php */?>
         </div>
      </div>
      
   </div>
</footer>

<script>
var PDFGenerator = function() {
	var pdf_generator = this;
	
	this.product_ids = [];
	this.pages = [];
	
	this.generating_pdf = false;
	
	jQuery(document).on('click', ".add_to_pdf_icon", function() {
		pdf_generator.addProduct(jQuery(this));
		});
      
	jQuery(document).on('click', ".remove_from_pdf", function() {
		pdf_generator.removeProduct(jQuery(this));
		});
      
	jQuery(document).on('click', ".pdf_generator_checkselector", function() {
		if(jQuery(this).prop('checked')) {
			pdf_generator.addProduct(jQuery(this));
			}
		else {
			jQuery("ol.product_list li span[data-product_id='"+jQuery(this).data('product_id')+"'] img.remove_from_pdf_icon").trigger('click');
			}
		});
	
	jQuery(document).on('click', ".ajaxprod_add_to_pdf", function() {
		pdf_generator.addProduct(jQuery(this));
		jQuery("input[type='checkbox'][id='"+jQuery(this).data('product_id')+"']").prop('checked',true);
		});
   
	jQuery(document).on('click', ".add-all-to-pdf", function() {
		var section = jQuery(this).data('section');
		var page    = jQuery(this).parent().parent().find('ul.pagination li.active').data('lp');
		//alert("div#"+section+"_main_content");
		if(jQuery(this).prop('checked')) {
			jQuery("div#"+section+"_main_content").find("input[type='checkbox']").prop('checked', false).trigger('click');
			}
		else {
			jQuery("div#"+section+"_main_content").find("input[type='checkbox']").prop('checked', true).trigger('click');
			}
		});
	
	jQuery(document).on('click', "#generate_pdf", function() {
		pdf_generator.generate();
		});
	jQuery(document).on('click', "#generate_pdf_link", function() {
		jQuery("#generate_pdf_link_input").slideDown();
		});
	
	jQuery(document).on('blur', function() {
		//if(pdf_generator.generating_pdf == true) {
			jQuery("#generate_pdf").css('cursor', 'pointer').attr('src', 'images/save_icon.png');
		//	}
		});
	
	$("ol.product_list").sortable(); 
	}

PDFGenerator.prototype.addProduct = function(jq_obj) {
   if($.inArray(jq_obj.data('product_id'), this.product_ids) == -1) {
		jQuery(".pdf_generator_info").hide();
      $('.pdf_generator').css('display','block');
      //alert("Adding product " + jq_obj.data('product_name') + ", ID " + jq_obj.data('product_id'));
      this.product_ids.push(jq_obj.data('product_id'));
      $('.pdf_generator ol').append( "<li class='item'>" + jq_obj.data('product_name') + "&nbsp;&nbsp;" + 
      "<span class='remove_from_pdf' data-product_name='" + jq_obj.data('product_name') + "'data-product_id=" +
      jq_obj.data('product_id') + "><img src='images/del.gif' class='remove_from_pdf_icon'></span></li>");
      }  
	jQuery("#generate_pdf_link_input input").val("http://www.vapornation.com/prodguide/inc/pdf_generator.php?inline_or_download=I&product_ids="+this.product_ids.join(","));
	}

PDFGenerator.prototype.removeProduct = function(jq_obj) {
	//alert("Removing " + jq_obj.data('product_id'));
	var index = this.product_ids.indexOf(jq_obj.data('product_id'));
	//alert("index: " + index);
	//alert("product_ids: " + this.product_ids);
	if(index > -1) {
		this.product_ids.splice(index, 1);
		}
	//alert("product_ids: " + this.product_ids);
	jQuery("input.pdf_generator_checkselector[data-product_id='"+jq_obj.data('product_id')+"']").prop('checked', false);
   jq_obj.parent().remove();
   if(jQuery('ol.product_list li.item').length==0){
      jQuery('.pdf_generator').hide();
		jQuery(".pdf_generator_info").show();
      }
	jQuery("#generate_pdf_link_input input").val("http://www.vapornation.com/prodguide/inc/pdf_generator.php?inline_or_download=I&product_ids="+this.product_ids.join(","));
	}
   
PDFGenerator.prototype.generate = function() {
	var pdf_generator = this;
	//alert("Generating PDF? " + this.generating_pdf)
	if(pdf_generator.generating_pdf == true) { return; }
	//alert("I guess not")
	pdf_generator.pages = [];
	//if($("ol.product_list li").length >= 40) {
		alert("Generating file, please wait.");
	//	}
   $("ol.product_list li").each(function() {
		/*pdf_generator.pages.push({
			id: $(this).find("span.remove_from_pdf").data('product_id'),
			name: $(this).find("span.remove_from_pdf").data('product_name')
			});*/
		pdf_generator.pages.push($(this).find("span.remove_from_pdf").data('product_id'));
		});
	console.log('inc/pdf_generator.php?product_ids=' + pdf_generator.pages.join());
	//jQuery("#generate_pdf").attr('src', 'images/load_icon.gif').css('cursor', 'default');
	pdf_generator.generating_pdf = true;
	jQuery("#pdf_download").attr('src', 'inc/pdf_generator.php?product_ids=' + pdf_generator.pages.join());
	pdf_generator.generating_pdf = false;
	}

jQuery(document).ready(function() {
	var pdf_generator = new PDFGenerator();
	});

</script>

<iframe id="pdf_download" style="display:none;"></iframe>

<?php } ?>

<?=$additional_js;?>
</body>
</html>