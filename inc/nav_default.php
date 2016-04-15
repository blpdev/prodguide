<?php
function getTreeCategories($parent_id, $is_child, $tabs=0) {
	$categories = Mage::getModel('catalog/category')->getCollection()
		->addAttributeToSelect('*')
		->addAttributeToFilter('is_active','1')
		->addAttributeToFilter('include_in_menu','1')
		->addAttributeToFilter('parent_id',array('eq' => $parent_id))
		->addAttributeToSort('position');
	$ul = ($is_child) ? '<ul class="dropdown-menu">' : '<ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropdownMenu">';
	$html .= str_repeat(" ", $tabs*3) . $ul . "\n";
	$tabs_orig = $tabs;
	$tabs++;
	foreach ($categories as $category) {
		$subcats = $category->getChildren();
		$li = ($subcats ? '<li class="dropdown-submenu">': '<li>');
		$html .= str_repeat(" ", $tabs*3) . $li . '<a href="#" data-category_id="'.$category->getId().'">'.$category->getName()." <span style='color:#999;'>(".$category->getProductCount().")</span></a>";
		if($subcats != '') {
			$html .= "\n" . getTreeCategories($category->getId(), true, $tabs+1) . str_repeat(" ", $tabs*3);
			}
		$html .= "</li>"."\n";
		}
	$html .= str_repeat(" ", $tabs_orig*3) . "</ul>"."\n";
	return $html;
	}

?>



<nav class="navbar navbar-default"><!-- navbar-fixed-top -->
   <div class="container-fluid">
      <div class="navbar-header">
         <!--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
         </button>-->
         <a class="navbar-brand" href="" style="padding:7px;"><img style="height:50px;" src="http://www.vapornation.com/skin/frontend/vapornation/default/images/logo_email.jpg"></a>
      </div>
      <div class="" id="bs-example-navbar-collapse-1"><!-- removed class "collapse navbar-collapse" -->
         <ul class="nav navbar-nav navbar-right">
         	
         	
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Vaporizers <span class="caret"></span></a>
				<?=getTreeCategories(3, false, 0);?>
            </li>
            
            
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Parts <span class="caret"></span></a>
					<?=getTreeCategories(4, false, 0);?>
            </li>
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Accessories <span class="caret"></span></a>
					<?=getTreeCategories(5, false, 0);?>
            </li>
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Grinders <span class="caret"></span></a>
					<?=getTreeCategories(6, false, 0);?>
            </li>
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Mods <span class="caret"></span></a>
					<?=getTreeCategories(439, false, 0);?>
            </li>
            <li>
               <a href="#" style="font-weight:bold;">E-Juice</a>
            </li>
            <li>
               <a href="/prodguide/learning-center" style="">Learning Center</a>
            </li>
            <li>  
               <form class="navbar-form navbar-right" role="search" onsubmit="return false;">
                  <div class="form-group">
                  	<input type="text" class="form-control" placeholder="Search" id="prod_name_search" style="width:360px;">
                  </div>
                  <?php /*?><button type="submit" class="btn btn-default">Submit</button><?php */?>
               </form>
            </li>
         </ul>
      </div>
   </div>
</nav>

<script>

</script>