<?php $CURRENT_PAGE = "learning_center"; require_once("inc/header.php"); ?>

<style>
.learning_center .section_data{
             padding: 10px 0px 10px 0px;
             }
.learning_center p {
    padding-top: 10px;
    }
.learning_center .nav-tabs > li {
    background-color: white !important;
    border: 1px solid #CCC;
    border-radius: 3px;
	 border-bottom:none;
}

.learning_center .active {
border-bottom:none;
}
 .nav-tabs > li > a{
	border-bottom: 1px solid #CCC;;
	}
.learning_center .section_data{
	border-left:none;
	}
.learning_center #subtabs{
   margin-top:10px;
	}
.learning_center .nav-tabs>li>a{
	margin-right:0px !important;
	border:none !important;
	}
.learning_center .tab-pane .row {
	border: 1px solid #CCC;
	border-top:none;
	background-color:#F5FFFF;
	}
.learning_center .tabcontainer {
	margin:0px;
	padding:0px;
	border:1px dotted #FFF;
	border-top:none;
	}
	
</style>

<div class="learning_center">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
         
      		<h1>Learning Center</h1>
         	
            
				<?php require("learning_center/chapter_1.html"); ?>
				<?php //require("learning_center/chapter_2.html"); ?>
				<?php require("learning_center/chapter_3.html"); ?>
            
         </div>
      </div>
   </div>
</div>


<style>
.section_title { color:#134fb8; text-decoration:underline; cursor:pointer; }
.section_data { padding-left:5px; margin-left:5px; border-left:1px dotted #CCC; text-align:justify; }
</style>


<script>
jQuery(".section_title").on('click', function() {
	var section_id = jQuery(this).data('sectionid');
	jQuery(".section_data[data-sectionid='"+section_id+"']").slideToggle();
	});
</script>

<?php require_once("inc/footer.php"); ?>