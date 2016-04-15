<?php
require_once '../../app/Mage.php';
umask(0);
Mage::app('default');
Mage::getSingleton('core/session', array('name' => 'frontend'));

$product_ids = $_GET['product_ids'];
$product_ids_array = explode(",", $product_ids);
$inline_or_download = (isset($_GET['inline_or_download'])) ? ($_GET['inline_or_download']) : ('D');

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
define('INFINTY',chr(167));
require('fpdf/fpdf.php');
$pdf = new FPDF();

$y_position = 0;

$x_position_left_column  = 10;
$x_position_header_info  = 65;
$x_position_right_column = 130;

$height_header = 5;
$height_titles = 5;
$height_multicells = 5;


// Table of Contents
$pdf->AddPage();
$pdf->AddLink();
$y_position = 10;
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,5,'Table of Contents');
$pdf->Ln();
$pdf->Ln();

$products_per_contents_page = 51;

$this_page = 2;
for($x=0; $x<(floor(count($product_ids_array)/($products_per_contents_page+1))); $x++) {
	$this_page++;
	$pdf->AddLink();
	}

foreach($product_ids_array as $product_id) {
	$product = Mage::getModel('catalog/product')->load($product_id);
	$link = $pdf->AddLink();
	$pdf->SetFont('Arial','U',12);
	$pdf->setTextColor(69, 130, 236);
	$pdf->Write(5,'Page ' . $this_page . ': ' . $product->getName(),$link);
	//$pdf->Write(5,'here',$link);
	//$pdf->WriteHTML('<a href="'.$link.'">Page ' . $this_page . ': ' . $product->getName() . "</a>");
	//$link_identifiers[] = $link;
	$pdf->Ln();
	$this_page++;
	}

$this_page = $pdf->PageNo()+1;
//$pdf->SetPageNumber($this_page);
foreach($product_ids_array as $product_id) {
	
	$product = Mage::getModel('catalog/product')->load($product_id);
	$product->load('media_gallery');
	#todo Output error message if unable to load product
	
	// Load product variables
	$product_name = $product->getName();
	$product_price = '$'.number_format($product->getSpecialPrice(),2);
	//$product_variations = getProductVariations($product);
	$product_variations = getAlternateIds($product);
	$product_description = getProductDescription($product);
	$product_related_products_array = [];
	foreach($product->getRelatedProductCollection() as $related_product) { $related_product->load(); $product_related_products_array[] = $related_product->getName(); }
	$product_related_products = implode(", ", $product_related_products_array);
	$product_features = [];
	foreach(explode("\n", $product->getProductFeatures()) as $feat) { if($feat) { $product_features[] = $feat; } }
	$product_accessories = [];
	foreach(explode("\n", $product->getProductAccessories()) as $acc) { if($acc) { $product_accessories[] = $acc; } }
	$additional_informations = [];
	foreach(getAdditionalInformation($product) as $info) { $additional_informations[] = $info; }
	$product_image_gallery = [];
	$this_one = 1; 
	$max_gallery_images = 8;
	foreach($product->getMediaGalleryImages() as $image) {
		//if($this_one++ == 1) { continue; }
		if($this_one++ > $max_gallery_images) { continue; }
		$product_image_gallery[] = (string)Mage::helper('catalog/image')->init($product, 'thumbnail', $image->getFile())->resize(200);
		}
	array_shift($product_image_gallery);
	
	$pdf->AddPage();
	//$pdf->showPageNumbers();
	$pdf->SetLink($this_page);
	
	
	// Reset y_position
	$y_position = 0;
	
	
	
	
	/* Product Image */
	$y_position += 10;
	$pdf->Image($product->getImageUrl(), 10, $y_position, 50, 50);
	$pdf->SetDrawColor(7, 69, 180);
	$pdf->Rect(10, 10, 50, 50);
	$pdf->SetDrawColor(0, 0, 0);
	
	/* Product Name */
	$pdf->SetXY($x_position_header_info, $y_position);
	$pdf->SetFont('Arial', 'B', 18);
	$pdf->SetTextColor(30, 144, 255);
	$pdf->Multicell(0, $height_header, $product_name, 0);
	$pdf->SetTextColor(0, 0, 0);
	
	/* Product Variations */
	$pv_pad = 0;
	if($product_variations) {
		$y_position = $pdf->getY() + 2;
		$pdf->SetXY($x_position_header_info, $y_position);
		$pdf->SetFont('Arial', 'B', 10);
		if(strpos($product_variations, ", ")!==false) {
			$pdf->Cell(34, $height_header, "Product Variations:", 0, 0);
			$pdf->SetXY($x_position_header_info + 34, $y_position);
			}
		else {
			$pdf->Cell(24, $height_header, "Product SKU:", 0, 0);
			$pdf->SetXY($x_position_header_info + 24, $y_position);
			}
		$pdf->SetFont('Arial', '', 10); 
		$pdf->SetLeftMargin($x_position_header_info);
		$pdf->Write($height_header, $product_variations);
		$pdf->SetLeftMargin(10);
		$pdf->Ln();
		}
	else {
		$pv_pad = 2;
		}
	
	/* Retail Prices */
	$y_position = $pdf->getY() + $pv_pad;
	$pdf->SetXY($x_position_header_info, $y_position);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(22, $height_header, 'Retail Price:', 0);
	$pdf->SetXY($x_position_header_info + 22, $y_position);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(0, $height_header, $product_price, 0);
	$pdf->Ln();
	
	/* Icons */
	$y_position = $pdf->getY();
	drawHeader($pdf, $x_position_header_info, $y_position, $product);
	
	/* Related Products */
	if($product_related_products) {
		$y_position = max(62, $pdf->getY()+12); // for temp icons img height
		$pdf->SetXY($x_position_left_column, $y_position);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(31, 5, 'Related products:', 0, 'L');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Write(5, $product_related_products);
		}
	
	
	
	$main_columns_y = max(70, ($pdf->getY() + 7));
	
	
	$left_col_final_y = 0;
	
	$pdf->SetXY($x_position_left_column, $main_columns_y);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(50,5,'Product Description', 0,2);
	$y_position = $pdf->getY() + 1;
	$pdf->SetXY($x_position_left_column + 5, $y_position);
	$pdf->SetFont('Arial', '', 9);
	$line_top_x = $pdf->getX() - 1;
	$line_top_y = $pdf->getY();
	$product_description = iconv('UTF-8', 'windows-1252', $product_description);
	$pdf->MultiCell(110, 5, $product_description, 0);
	$line_bottom_x = $line_top_x;
	$line_bottom_y = $pdf->getY();
	$pdf->SetDrawColor(7, 69, 180);
	$pdf->Line($line_top_x, $line_top_y, $line_bottom_x, $line_bottom_y);
	$pdf->SetDrawColor(0, 0, 0);
	
	$left_col_final_y = $pdf->getY();
	
	
	
	
	$right_col_final_y = 0;
	
	//$y_position = max(65, $pdf->getY() + 30); // for temp icons img height
	/* Product Features */
	$y_position = $main_columns_y;
	$pf_pad = 0;
	if(count($product_features)) {
		$pdf->SetXY($x_position_right_column, $y_position);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 5,'Product Features', 0, 1);
		$y_position = $pdf->getY() + 1;
		$pdf->SetXY($x_position_right_column + 5, $y_position);
		$pdf->SetFont('Arial', '', 9);
		$line_top_x = $pdf->getX() - 1;
		$line_top_y = $pdf->getY();
		foreach($product_features as $feature) {
			$pdf->SetXY($x_position_right_column + 5, $pdf->getY());
			//$feature = strip_tags($feature);
			//$feature = iconv('UTF-8', 'windows-1252', $feature);
			$feature = cleanText($feature);
			//$pdf->MultiCell(($x_position_right_column - 20), 5, chr(127) . " " . $feature, 0, 2);
			$pdf->MultiCell(0, 5, chr(127) . " " . $feature, 0, 2);
			}
		$line_bottom_x = $line_top_x;
		$line_bottom_y = $pdf->getY();
		$pdf->SetDrawColor(7, 69, 180);
		$pdf->Line($line_top_x, $line_top_y, $line_bottom_x, $line_bottom_y);
		$pdf->SetDrawColor(0, 0, 0);
		$pf_pad = 5;
		}
	
	//$y_position = $main_columns_y;
	$y_position = $pdf->getY() + $pf_pad;
	$pa_pad = 0;
	if(count($product_accessories) && ($pdf->getY() < 200)) {
		$pdf->SetXY($x_position_right_column, $y_position);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 5,'Product Accessories', 0, 1);
		$y_position = $pdf->getY() + 1;
		$pdf->SetXY($x_position_right_column + 5, $y_position);
		$pdf->SetFont('Arial', '', 9);
		$line_top_x = $pdf->getX() - 1;
		$line_top_y = $pdf->getY();
		foreach($product_accessories as $accessory) {
			if($pdf->getY() > 200) { continue; }
			$pdf->SetXY($x_position_right_column + 5, $pdf->getY());
			$accessory = cleanText($accessory);
			//$accessory = strip_tags($accessory);
			//$accessory = iconv('UTF-8', 'windows-1252', $accessory);
			$pdf->MultiCell(0, 5, chr(127) . " " . $accessory, 0, 2);
			}
		$line_bottom_x = $line_top_x;
		$line_bottom_y = $pdf->getY();
		$pdf->SetDrawColor(7, 69, 180);
		$pdf->Line($line_top_x, $line_top_y, $line_bottom_x, $line_bottom_y);
		$pdf->SetDrawColor(0, 0, 0);
		$pa_pad = 4;
		}
	
	
	$y_position = $pdf->getY() + $pf_pad;
	if(count($additional_informations) && ($pdf->getY() < 250)) {
		$pdf->SetXY($x_position_right_column, $y_position);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 5,'Additional Information', 0, 1);
		$y_position = $pdf->getY() + 1;
		$pdf->SetXY($x_position_right_column + 5, $y_position);
		$pdf->SetFont('Arial', '', 9);
		$line_top_x = $pdf->getX() - 1;
		$line_top_y = $pdf->getY();
		foreach($additional_informations as $additional_information) {
			if($pdf->getY() > 250) { continue; }
			$pdf->SetXY($x_position_right_column + 5, $pdf->getY());
			$additional_information = cleanText($additional_information);
			//$additional_information = strip_tags($additional_information);
			//$additional_information = iconv('UTF-8', 'windows-1252', $additional_information);
			$pdf->MultiCell(0, 5, chr(127) . " " . $additional_information, 0, 2);
			}
		$line_bottom_x = $line_top_x;
		$line_bottom_y = $pdf->getY();
		$pdf->SetDrawColor(7, 69, 180);
		$pdf->Line($line_top_x, $line_top_y, $line_bottom_x, $line_bottom_y);
		$pdf->SetDrawColor(0, 0, 0);
		}
	
	$right_col_final_y = $pdf->getY();
	
	
	$y_position = max($left_col_final_y, $right_col_final_y) + 3;
	
	if($y_position < 270 && (count($product_image_gallery)>0)) {
		$pdf->SetXY($x_position_left_column, $y_position);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(0, 5, 'Additional Product Images', 0, 1);
		
		$y_position = $pdf->getY() + 2;
		$x_position = $x_position_left_column + 5;
		$image_size = 22;
		foreach($product_image_gallery as $image) {
			$pdf->SetXY($x_position, $y_position);
			$pdf->Image($image, $x_position, $y_position, $image_size, $image_size);
			$pdf->SetDrawColor(7, 69, 180);
			$pdf->Rect($x_position-1, $y_position-1, $image_size+2, $image_size+2);
			$pdf->SetDrawColor(0, 0, 0);
			$x_position += $image_size+5;
			}
		}
	
	$this_page++;
	}

//$pdf->Output('D', "VaporNation_Product_Guide.pdf"); // Force a file download
//$pdf->Output('I'); // Inline view, i.e. the browser's PDF viewer
$pdf->Output($inline_or_download); // Inline view, i.e. the browser's PDF viewer

function drawHeader($pdf, $x_position_header_info, $y_position, $prod)
{

// Variables

$show_compatibility = false;
$show_vaporizer_type = false;
$show_warranty = false;

// Compatibility
	
$vm_attr = $prod->getResource()->getAttribute('vbg_vape_material')->getFrontend()->getValue($prod);
$vms = array();
$compatibility="";
$compatibility_url="";
if($vm_attr) {
	$show_compatibility = true;
	$vms['herbs'] = (preg_match("#Herbs#", $vm_attr));
	$vms['oils']  = (preg_match("#Oils#", $vm_attr));
	$vms['waxes'] = (preg_match("#Waxes#", $vm_attr));
	}
	
if($vms['herbs'] && $vms['oils'] && $vms['waxes']) {
	$compatibility = "Multi-Function";
	$compatibility_url="../images/header_icons/multifunction.jpg";
	}
	
else if($vms['herbs'] && $vms['oils']) {
	$compatibility = "Herb + E-Juice";
	$compatibility_url="../images/header_icons/oil+herb.jpg";
	}
	
else if($vms['herbs'] && $vms['waxes']) {
	$compatibility = "Herb + Wax";
	$compatibility_url="../images/header_icons/herb+wax.jpg";
	}
	
else if($vms['oils'] && $vms['waxes']) {
	$compatibility = "Wax + E-Juice";
	$compatibility_url= "../images/header_icons/wax+oil.jpg";
	}
	
else if($vms['herbs']) {
	$compatibility = "Herbs";
	$compatibility_url="../images/header_icons/herb.jpg";
	}
	
else if($vms['oils']) {
	$compatibility = "E-Juice";
	$compatibility_url="../images/header_icons/oil.jpg";
	}
	
else if($vms['waxes']) {
	$compatibility = "Wax";
	$compatibility_url="../images/header_icons/wax.jpg";
	}
	

//Vaporizer Type
$vape_type='';
$vape_type_url='';
if(in_array(421, $prod->getCategoryIds())) { // Desktop
	$show_vaporizer_type = true;
	$vape_type = 'Desktop';
	$vape_type_url='../images/header_icons/desktop.jpg';
	}

elseif(in_array(23, $prod->getCategoryIds())) { // Portable
	$show_vaporizer_type = true;
	$vape_type = 'Portable';
	$vape_type_url='../images/header_icons/portable.jpg';
	}

elseif(count(array_intersect(array(24,345,422), $prod->getCategoryIds()))) { // Pen
	$show_vaporizer_type = true;
	$vape_type = 'Pen-Style';
	$vape_type_url='../images/header_icons/pen.jpg';
	}	
	


// Warranty-Info

$warranty_info = strip_tags($prod->getAdditionalInfo());
$warr_info_html = "";
$hi_warranty_num = "";
$hi_warranty_time = "";

if(preg_match("/lifetime/i", $warranty_info)) {
	$show_warranty = true;
	$hi_warranty_num ='infinty';
	$hi_warranty_time = "Lifetime";
	}
	
else {
	preg_match_all("/([0-9]*)(\s|\-)year/i", $warranty_info, $year_matches);
	//Mage::vdp($year_matches);
	$year_match_numbers = $year_matches[1];
	rsort($year_match_numbers);
	//Mage::vdp($year_match_numbers);
	
	if(isset($year_match_numbers[0])) {
		$show_warranty = true;
		$hi_warranty_num = $year_match_numbers[0];
		$hi_warranty_time = "year";
		}
		
	else {
		preg_match_all("/([0-9]*)(\s|\-)?month/i", $warranty_info, $month_matches);
		$month_match_numbers = $month_matches[1];
		rsort($month_match_numbers);
		//Mage::vdp($month_match_numbers);
		
		if(isset($month_match_numbers[0])) {
			$show_warranty = true;
			$hi_warranty_num = $month_match_numbers[0];
			$hi_warranty_time = "month";
		}
		
		else {
			preg_match_all("/([0-9]*)(\s|\-)?day/i", $warranty_info, $day_matches);
			$day_match_numbers = $day_matches[1];
			rsort($day_match_numbers);
			
			//Mage::vdp($month_match_numbers);
			if(isset($day_match_numbers[0])) {
				$show_warranty = true;
				$hi_warranty_num = $day_match_numbers[0];
				$hi_warranty_time = "day";
				}
			}
		}
	}
	
if($hi_warranty_num != "") {
	$show_warranty = true;
	$warranty=$hi_warranty_num.' '.$hi_warranty_time;
	}


//Average Review Calculator

$reviews = Mage::getModel('review/review')->getResourceCollection();  
$reviews->addEntityFilter('product', $prod->getId())
	->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )  
	->setDateOrder()  
	->addRateVotes();
if(count($reviews)) {
	$avg_rating = Mage::getModel('review/review')->getAverageRating($prod, $reviews);
	$avg_ranking_out_of_five = number_format(($avg_rating*.01*5), 1);
	}
	
//Average Reviews

if($avg_ranking_out_of_five) {
	$average_reviews= $avg_ranking_out_of_five;
	}
	
//Heat up Time

if($prod->getHeatupTime()) {
	$heat_up_time = $prod->getHeatupTime();
	}
	
$header_width_increment = 25;
$image_size = 20;
$header_height_increment = 10;
// Output
$icon_default_y = $y_position+5;
$icon_defulat_x = $x_position_header_info;
if($show_compatibility) {//compatibility
	$pdf->setXY($x_position_header_info, $icon_default_y);
	$pdf->Image($compatibility_url, $x_position_header_info, $icon_default_y,$image_size,$image_size);
	$y_position+=20;
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(20, 15, $compatibility , 0, 0, 'C');
	$pdf->SetFont('Arial', '', 10);

	}
if($show_vaporizer_type) {// type
	$x_position_header_info+=$header_width_increment;
	$pdf->setXY($icon_defulat_x, $icon_default_y);
	$pdf->Image($vape_type_url, $x_position_header_info, $icon_default_y,$image_size,$image_size);
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(20, 15, $vape_type , 0, 0, 'C');
	$pdf->SetFont('Arial', '', 10);
	}
	
	if($show_warranty) { // warranty type

	$x_position_header_info+=$header_width_increment;
	$pdf->setXY($icon_defulat_x, $icon_default_y);
	$pdf->Image('../images/header_icons/warranty.jpg', $x_position_header_info, $icon_default_y,$image_size,$image_size);
	if($hi_warranty_time == "Lifetime"){	
		$pdf->Image('../images/header_icons/infinity.jpg', $x_position_header_info+4, $icon_default_y+3);
		$pdf->setXY($x_position_header_info, $icon_default_y+5);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(20, 15, $hi_warranty_time , 0, 0, 'C');
		$pdf->setXY($x_position_header_info, $y_position);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(20, 15, "Warranty" , 0, 0, 'C');
		$pdf->SetFont('Arial', '', 10);
	}
	else{
		$pdf->setXY($x_position_header_info, $icon_default_y);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(20, 15, $hi_warranty_num , 0, 0, 'C');
		$pdf->WriteHTML($hi_warranty_num );
		$pdf->SetTextColor(0,0,0);
		$pdf->setXY($x_position_header_info, $icon_default_y+5);
		$pdf->SetTextColor(255,255,255);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(20, 15, $hi_warranty_time , 0, 0, 'C');
		$pdf->SetTextColor(0,0,0);
	}
		$pdf->setXY($x_position_header_info, $y_position);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(20, 15, "Warranty" , 0, 0, 'C');
		$pdf->SetFont('Arial', '', 10);
	}
	
if($avg_ranking_out_of_five) { // average ratings
	$x_position_header_info+=$header_width_increment;
	$pdf->setXY($icon_defulat_x, $icon_default_y);
	$pdf->Image('../images/header_icons/rating.jpg', $x_position_header_info, $icon_default_y,$image_size,$image_size);
	
	$pdf->setXY($x_position_header_info, $icon_default_y+3);
	$pdf->SetTextColor(95,79,206);
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(20, 15, $average_reviews , 0, 0, 'C');
	$pdf->SetTextColor(0,0,0);

	
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(20, 15, "Rating" , 0, 0, 'C');
	$pdf->SetFont('Arial', '', 10);
	}
if($prod->getHeatupTime()) { //heat-up time
	$x_position_header_info+=$header_width_increment;
	$pdf->setXY($icon_defulat_x, $icon_default_y);
	$pdf->Image('../images/header_icons/duration.jpg', $x_position_header_info, $icon_default_y,$image_size,$image_size);
	
	
	$pdf->setXY($x_position_header_info, $icon_default_y+1);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(20, 15, $heat_up_time  , 0, 0, 'C');
	$pdf->SetTextColor(0,0,0);
	$pdf->setXY($x_position_header_info, $icon_default_y+5);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(20, 15, "Secs" , 0, 0, 'C');
	$pdf->SetTextColor(0,0,0);
	
	
	
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(20, 15, "Heat-up Time" , 0, 0, 'C');
	$pdf->SetFont('Arial', '', 10);
	}

	
	
	}


function drawTextHeader($pdf, $x_position_header_info, $y_position, $prod) {
	// Dummy image
	//$pdf->Image('../images/dummy_header_icons.png', $x_position_header_info, $y_position, 120, 30);


// Variables

$show_compatibility = false;
$show_vaporizer_type = false;
$show_warranty = false;

// Compatibility
	
$vm_attr = $prod->getResource()->getAttribute('vbg_vape_material')->getFrontend()->getValue($prod);
$vms = array();

if($vm_attr) {
	$show_compatibility = true;
	$vms['herbs'] = (preg_match("#Herbs#", $vm_attr));
	$vms['oils']  = (preg_match("#Oils#", $vm_attr));
	$vms['waxes'] = (preg_match("#Waxes#", $vm_attr));
	}
	
if($vms['herbs'] && $vms['oils'] && $vms['waxes']) {
	$compatibility = "Multi-Function";
	}
	
else if($vms['herbs'] && $vms['oils']) {
	$compatibility = "Herb + E-Juice";
	}
	
else if($vms['herbs'] && $vms['waxes']) {
	$compatibility = "Herb + Wax";
	}
	
else if($vms['oils'] && $vms['waxes']) {
	$compatibility = "Wax + E-Juice";
	}
	
else if($vms['herbs']) {
	$compatibility = "Herbs";
	}
	
else if($vms['oils']) {
	$compatibility = "E-Juice";
	}
	
else if($vms['waxes']) {
	$compatibility = "Wax";
	}
	

//Vaporizer Type
	 
if(in_array(421, $prod->getCategoryIds())) { // Desktop
	$show_vaporizer_type = true;
	$vape_type = 'Desktop';
	}

elseif(in_array(23, $prod->getCategoryIds())) { // Portable
	$show_vaporizer_type = true;
	$vape_type = 'Portable';
	}

elseif(count(array_intersect(array(24,345,422), $prod->getCategoryIds()))) { // Pen
	$show_vaporizer_type = true;
	$vape_type = 'Pen-Style';
	}	
	


// Warranty-Info

$warranty_info = strip_tags($prod->getAdditionalInfo());
$warr_info_html = "";
$hi_warranty_num = "";
$hi_warranty_time = "";

if(preg_match("/lifetime/i", $warranty_info)) {
	$show_warranty = true;
	$hi_warranty_num = "Lifetime";
	}
	
else {
	preg_match_all("/([0-9]*)(\s|\-)year/i", $warranty_info, $year_matches);
	//Mage::vdp($year_matches);
	$year_match_numbers = $year_matches[1];
	rsort($year_match_numbers);
	//Mage::vdp($year_match_numbers);
	
	if(isset($year_match_numbers[0])) {
		$show_warranty = true;
		$hi_warranty_num = $year_match_numbers[0];
		$hi_warranty_time = "year";
		}
		
	else {
		preg_match_all("/([0-9]*)(\s|\-)?month/i", $warranty_info, $month_matches);
		$month_match_numbers = $month_matches[1];
		rsort($month_match_numbers);
		//Mage::vdp($month_match_numbers);
		
		if(isset($month_match_numbers[0])) {
			$show_warranty = true;
			$hi_warranty_num = $month_match_numbers[0];
			$hi_warranty_time = "month";
		}
		
		else {
			preg_match_all("/([0-9]*)(\s|\-)?day/i", $warranty_info, $day_matches);
			$day_match_numbers = $day_matches[1];
			rsort($day_match_numbers);
			
			//Mage::vdp($month_match_numbers);
			if(isset($day_match_numbers[0])) {
				$show_warranty = true;
				$hi_warranty_num = $day_match_numbers[0];
				$hi_warranty_time = "day";
				}
			}
		}
	}
	
if($hi_warranty_num != "") {
	$show_warranty = true;
	$warranty=$hi_warranty_num.' '.$hi_warranty_time;
	}


//Average Review Calculator

$reviews = Mage::getModel('review/review')->getResourceCollection();  
$reviews->addEntityFilter('product', $prod->getId())
	->addStatusFilter( Mage_Review_Model_Review::STATUS_APPROVED )  
	->setDateOrder()  
	->addRateVotes();
if(count($reviews)) {
	$avg_rating = Mage::getModel('review/review')->getAverageRating($prod, $reviews);
	$avg_ranking_out_of_five = number_format(($avg_rating*.01*5), 1);
	}
	
//Average Reviews

if($avg_ranking_out_of_five) {
	$average_reviews= $avg_ranking_out_of_five." "."Stars";
	}
	
//Heat up Time

if($prod->getHeatupTime()) {
	$heat_up_time= $prod->getHeatupTime();
	}
	
// Output

if($show_compatibility) {//compatibility
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->WriteHTML("<b>Vaporizer Compatibility:</b> " . $compatibility);
	}
if($show_vaporizer_type) {// type
	$y_position+=5;
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->WriteHTML("<b>Vaporizer Type:</b> " . $vape_type); 
	}
	if($show_warranty) { // warranty type
	$y_position+=5;
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->WriteHTML("<b>Warranty Duration:</b> " . $warranty);
	}
if($avg_ranking_out_of_five) { // average ratings
	$y_position+=5;
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->WriteHTML("<b>Average Rating:</b> " . $average_reviews);
	}
if($prod->getHeatupTime()) { //heat-up time
	$y_position+=5;
	$pdf->setXY($x_position_header_info, $y_position);
	$pdf->WriteHTML("<b>Heat-up Time:</b> " . $heat_up_time . " Seconds");
	}

	
	}






function getProductDescription($prod) {
	$product_description = "";
	$product_description = $prod->getDescription();
	$product_description = cleanText($product_description);
	$max_length = 2000;
	
	// 35 rows
	// Approx. 75 characters per row
	$rows = 35;
	$chars_per_row = 75;
	$max_length = $rows * $chars_per_row;
	
	$tmp_prod_desc = substr($product_description, 0, $max_length) . "...";
	//$tmp_prod_desc = nl2br($tmp_prod_desc);
	$num_newlines = substr_count($tmp_prod_desc, "\n");
	$max_length -= ($num_newlines*$chars_per_row);
	
	if(strlen($product_description) > $max_length) {
		$product_description = substr($product_description, 0, $max_length) . "...";
		}
	
	return $product_description;
	}

function getProductVariations($prod) {
	# get SKU from custom options
	$skip_option_names = array();
	$skip_option_names[] = "deluxe package";
	$skip_option_names[] = "extended warranty";
	$skip_option_names[] = "product upgrades";
	
	$product_variations = array();
	
	if(count($prod->getOptions())) {
		foreach ($prod->getOptions() as $option) {
			$this_options_text = "";
			foreach($skip_option_names as $son) { if(preg_match("/".$son."/i", $option->getTitle())) { continue 2; }  }
			$this_values = [];
			foreach ($option->getValues() as $value) {
				$this_values[] = $value->default_title;
				}
			$this_options_text .= str_replace(":", "", $option->getTitle()) . " (";
			$this_options_text .= implode(", ", $this_values);
			$this_options_text .= ")";
			$product_variations[] = $this_options_text;
			}
		}
	
	return implode("; ", $product_variations);
	}

function getAdditionalInformation($_product) {
	$_helper = Mage::helper('catalog/output');
	
	$warranty_info = $_helper->productAttribute($_product, $_product->getAdditionalInfo(), 'additional_info');
	$dimensions = $_product->getLength() . " x " . $_product->getWidth() . " x " . $_product->getHeight() . " in.";
	$weight = number_format($_product->getWeight(), 2) . " lbs.";
	
	$vaporizer_type = $_product->getAttributeText('vaporizer_type');
	$vaporizer_compatibility = $_product->getAttributeText('vbg_vape_material');
	if(is_array($vaporizer_compatibility)) {
		$vaporizer_compatibility = implode(", ", $vaporizer_compatibility);
		}
	$vaporizer_compatibility = str_replace("Oils", "E-Juice", $vaporizer_compatibility);
	//$vaporizer_compatibility = str_replace("Herbs", "Materials", $vaporizer_compatibility);
	$vaporizer_compatibility = Mage::helper('googlehider')->hide('prodpage_description', $vaporizer_compatibility);
	$delivery_method = $_product->getAttributeText('whip_style');
	$heat_source = $_product->getAttributeText('vbg_portable_heat_source');
	$voltage = $_product->getAttributeText('voltage');
	$thread_size = $_product->getAttributeText('thread_size');
	$screen_size = $_product->getAttributeText('screen_size');
		 if($screen_size=="Arizer Dome Screen") { $screen_size = "<a href=\"/arizer-screen-pack.html\" style=\"text-decoration:underline;\">Arizer Dome Screen</a>"; }
	elseif($screen_size=="AroMed Screens") { $screen_size = "<a href=\"/aromed-replacement-screens.html\" style=\"text-decoration:underline;\">AroMed Replacement Screens</a>"; }
	elseif($screen_size=="AtmosRX Glass Screens") { $screen_size = "<a href=\"/atmosrx-glass-screens.html\" style=\"text-decoration:underline;\">AtmosRX Glass Screens</a>"; }
	elseif($screen_size=="DaVinci Screens") { $screen_size = "<a href=\"/davinci-screen-pack.html\" style=\"text-decoration:underline;\">DaVinci Screens</a>"; }
	elseif($screen_size=="G Pro Filter Screens") { $screen_size = "<a href=\"/g-pro-filter-screens.html\" style=\"text-decoration:underline;\">G Pro Filter Screens</a>"; }
	elseif($screen_size=="Iolite Screens") { $screen_size = "<a href=\"/iolite-replacement-screens.html\" style=\"text-decoration:underline;\">Iolite Screens</a>"; }
	elseif($screen_size=="Iolite WISPR Screens") { $screen_size = "<a href=\"/iolite-wispr-mesh-screens.html\" style=\"text-decoration:underline;\">Iolite WISPR Screens</a>"; }
	elseif($screen_size=="Palm Screens") { $screen_size = "<a href=\"/palm-screens.html\" style=\"text-decoration:underline;\">Palm Screens</a>"; }
	elseif($screen_size=="Pinnacle Screens") { $screen_size = "<a href=\"/pinnacle-screens.html\" style=\"text-decoration:underline;\">Pinnacle Screens</a>"; }
	elseif($screen_size=="Plenty Screens") { $screen_size = "<a href=\"/volcano-plenty-normal-screen-set.html\" style=\"text-decoration:underline;\">Volcano Plenty Normal Screen Set</a> - <a href=\"volcano-plenty-fine-screen-set.html\" style=\"text-decoration:underline;\">Volcano Plenty Fine Screen Set</a> - <a href=\"volcano-plenty-liquid-pad-set.html\" style=\"text-decoration:underline;\">Volcano Plenty Liquid Pad Set</a>"; }
	elseif($screen_size=="PUFFiT Screens") { $screen_size = "<a href=\"/puffit-replacement-screens.html\" style=\"text-decoration:underline;\">PUFFiT Screens</a>"; }
	elseif($screen_size=="Sonic Screens") { $screen_size = "<a href=\"/sonic-screens.html\" style=\"text-decoration:underline;\">Sonic Screens</a>"; }
	elseif($screen_size=="VapeXhale Screens") { $screen_size = "<a href=\"/vapexhale-screens.html\" style=\"text-decoration:underline;\">VapeXhale Screens</a>"; }
	elseif($screen_size=="Vapir Blend Discs") { $screen_size = "<a href=\"/vapir-blend-discs.html\" style=\"text-decoration:underline;\">Vapir Blend Discs</a>"; }
	elseif($screen_size=="Vapir NO2 Screens") { $screen_size = "<a href=\"/vapir-no2-screens.html\" style=\"text-decoration:underline;\">Vapir NO2 Screens</a>"; }
	elseif($screen_size=="Vapir Oxygen Mini Screens") { $screen_size = "<a href=\"/vapir-oxygen-mini-screens.html\" style=\"text-decoration:underline;\">Vapir Oxygen Mini Screens</a>"; }
	elseif($screen_size=="Vapir Rise Screens") { $screen_size = "<a href=\"/vapir-rise-plunger-mesh-screens.html\" style=\"text-decoration:underline;\">Vapir Rise Plunger Mesh Screen</a> - <a href=\"vapir-rise-chamber-mesh-screen.html\" style=\"text-decoration:underline;\">Vapir Rise Chamber Mesh Screen</a>"; }
	elseif($screen_size=="Vaporfection ViVape Screens") { $screen_size = "<a href=\"/vaporfection-vivape-screen-pack.html\" style=\"text-decoration:underline;\">Vaporfection ViVape Screens</a>"; }
	elseif($screen_size=="Volcano Screens") { $screen_size = "<a href=\"/volcano-easy-valve-normal-screen-set.html\" style=\"text-decoration:underline;\">Volcano Normal Screen Set</a> - <a href=\"volcano-fine-screen-set-solid-valve.html\" style=\"text-decoration:underline;\">Volcano Fine Screen Set </a>"; }
	elseif($screen_size=="Zephyr Ion Screens") { $screen_size = "<a href=\"/zephyr-ion-screens.html\" style=\"text-decoration:underline;\">Zephyr Ion Screens</a>"; }
	elseif($screen_size=="No Screen") { $screen_size =  $screen_size; }
	elseif($screen_size) { $screen_size = "<a href=\"/replacement-screens.html\" style=\"text-decoration:underline;\">{$screen_size}</a>"; }
	$passthrough = $_product->getAttributeText('passthrough');
	
	$possible_attributes = array();
	$possible_attributes["Warranty Info"] = $warranty_info;
	$possible_attributes["Dimensions"] = $dimensions;
	$possible_attributes["Weight"] = $weight;
	//$possible_attributes["Vaporizer Type"] = $vaporizer_type;
	//$possible_attributes["Vaporizer Compatibility"] = $vaporizer_compatibility;
	//$possible_attributes["Delivery Method"] = $delivery_method;
	//$possible_attributes["Heat Source"] = $heat_source;
	$possible_attributes["Voltage"] = $voltage;
	$possible_attributes["Thread Size"] = $thread_size;
	$possible_attributes["Screen Size"] = $screen_size;
	$possible_attributes["Use while charging?"] = ($passthrough=="Not Sure") ? '' : $passthrough;
	
	$additional_informations = [];
	foreach($possible_attributes as $title=>$val) {
		if($val) {
			$val = strip_tags($val);
			$val = str_replace("\n", ", ", $val);
			$additional_informations[] = $title . ": " . $val;
			}
		}
	return $additional_informations;
	}

function getAlternateIds($prod) {
	# get SKU from custom options
	$skip_option_names = array();
	$skip_option_names[] = "deluxe package";
	$skip_option_names[] = "extended warranty";
	$skip_option_names[] = "product upgrades";
	
	$alternate_ids = array();
	$mult_initial_id = 0;
	$has_mult = false;
	if(count($prod->getOptions())) {
		foreach ($prod->getOptions() as $option) {
			foreach($skip_option_names as $son) { if(preg_match("/".$son."/i", $option->getTitle())) { continue 2; }  }
			foreach ($option->getValues() as $value) {
				if($value->sku) {
					if(preg_match("#,#", $value->sku)) { $has_mult = true; $mult_initial_id = $option->getSortOrder()-1; break 2; }
					$alternate_ids[] = $value->default_title . " - " . $value->sku . "";
					}
				}
			}
		}
	
	if($has_mult) {
		foreach ($prod->getOptions() as $option) {
			foreach($skip_option_names as $son) { if(preg_match("/".$son."/i", $option->getTitle())) { continue 2; }  }
			if($option->getSortOrder() == $mult_initial_id) {
				foreach ($option->getValues() as $value) {
					$pair1[] = $value->default_title;
					}
				}
			elseif($option->getSortOrder() == $mult_initial_id+1) {
				foreach ($option->getValues() as $value) {
					$sku_pair = explode(",",$value->sku);
					for($x=0; $x<count($sku_pair); $x++) {
						$alternate_ids_assoc[$pair1[$x]][$value->default_title] = $sku_pair[$x];
						}
					}
				}
			}
		foreach($alternate_ids_assoc as $option1=>$o2) {
			foreach($o2 as $option2=>$sku) {
				$alternate_ids[] = $option1 . " / " . $option2 . " - " . $sku . "";
				}
			}
		}
	
	if(count($alternate_ids) == 0) {
		$alternate_ids[] = $prod->getSku();
		}
	return implode(", ", $alternate_ids);
	}


function cleanText($text) {
	$text = strip_tags($text);
	$text = htmlentities($text);
	$text = str_ireplace("&omega;", "[Ohm]", $text);
	$text = str_ireplace("&deg;", "[deg]", $text);
	$text = str_ireplace("&quot;", '"', $text);
	$text = str_ireplace("&lsquo;", "'", $text); // single quote
	$text = str_ireplace("&rsquo;", "'", $text); // single quote
	$text = str_ireplace(array("&ldquo;", "&rdquo;"), '"', $text); // double quote
	$text = str_ireplace("&nbsp;", " ", $text);
	$text = str_ireplace("&amp;", "&", $text);
	$text = str_ireplace("&ndash;", "-", $text);
	$text = str_ireplace("&#9888;", "/!\\", $text);
	return $text;
	}

?>