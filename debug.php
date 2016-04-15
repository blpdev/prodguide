<?php
require_once '../app/Mage.php';
umask(0);
Mage::app('default');
Mage::getSingleton('core/session', array('name' => 'frontend'));
?>
<!doctype html><html><head><meta charset="utf-8"><title>Untitled Document</title></head><body>




<?php
function getTreeCategories($parentId, $isChild, $tabs=0) {
	$allCats = Mage::getModel('catalog/category')->getCollection()
	->addAttributeToSelect('*')
	->addAttributeToFilter('is_active','1')
	->addAttributeToFilter('include_in_menu','1')
	->addAttributeToFilter('parent_id',array('eq' => $parentId));
	$class = ($isChild) ? "sub-cat-list" : "cat-list";
	$html .= str_repeat(" ", $tabs*3) . '<ul class="'.$class.'">'."\n";
	$tabs_orig = $tabs;
	$tabs++;
	foreach ($allCats as $category) {
		$html .= str_repeat(" ", $tabs*3) . '<li>'.$category->getName()."";
		$subcats = $category->getChildren();
		if($subcats != '') {
			$html .= "\n" . getTreeCategories($category->getId(), true, $tabs+1) . str_repeat(" ", $tabs*3);
			}
		$html .= "</li>"."\n";
		}
	$html .= str_repeat(" ", $tabs_orig*3) . "</ul>"."\n";
	return $html;
	}

function getCategoryProducts($category_id) {
	$collection_of_products = Mage::getModel('catalog/product')->getCollection()
		->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		->addAttributeToSelect('*')
		->addAttributeToFilter('category_id', array(
			array('in' => '23')
			))
		->setOrder('most_popular_count', 'DESC')
		->setOrder('name', 'ASC');
	$collection_of_products->getSelect()
		->reset(Zend_Db_Select::ORDER)
		->order(array('CAST(`most_popular_count` AS UNSIGNED) DESC', 'name ASC'));
	return $collection_of_products;
	}

$portable_products = getCategoryProducts(23);

$most_popular_counts = [];
foreach($portable_products as $product) { $most_popular_counts[] = $product->getMostPopularCount(); }
$median = 0;
rsort($most_popular_counts);
$middle = round(count($most_popular_counts) / 2);
$median = $most_popular_counts[$middle-1]; 
echo("<div>" . "Median: " . $median . "</div>");

foreach($portable_products as $product) {
	if($product->getMostPopularCount() <= $median) continue;
	echo("<div>" . $product->getName() . " (" . $product->getMostPopularCount() . ")</div>");
	}

/*$mpc = $most_popular_counts;
$mean = 0;
$count = count($mpc);
$sum   = array_sum($mpc);
$mean  = $sum / $count;
echo("<div>" . "Mean: " . $mean . "</div>");


$mpc = $most_popular_counts;
$mode = 0;
$v = array_count_values($mpc);
arsort($v);
foreach($v as $k => $v){$median = $k; break;} 
echo("<div>" . "Mode: " . $mode . "</div>");*/




?>









</body></html>