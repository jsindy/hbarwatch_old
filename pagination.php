<?php
require 'config.php';
//$limit = 10;  
if(!empty($_GET["tz"]))
{
	date_default_timezone_set($_GET["tz"]);
}

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };  
$start_from = ($page-1) * TRANSECTIONSNO;  
  
$result = pg_query($conn, "select * from get_results_impr(".AMOUNT.",".$start_from.",".TRANSECTIONSNO.")") ;
if (!$result) {  
 echo "An error occurred.\n";  
 exit;  
} 

//to get dollar value from database
	
  $resultHbar = pg_query($conn, "select hbar_value from public.hbar_usd") ;
  $rowHbar = pg_fetch_array($resultHbar);
  $dollerValue = $rowHbar['hbar_value'];
?>

  <?php 
  	
  	$resultArray = array();
  	while ($row = pg_fetch_row($result)) {
  		$hypen = '-';
		  $position = '-9';
  		
      $nsHypen = substr_replace( $row[3], $hypen, $position, 0 );
  		

  		$firstWay = number_format($row[1]/ 100000000).' ℏ';	
      //$firstWay = number_format((float)$row[1]/ 100000000, 8, '.', ',');
  		$secondWay = number_format(round((($row[1]/ 100000000) * $dollerValue), 2));
  		
  		$combinedWay = $firstWay.'   ($ '.$secondWay.')';	
  		$consensus_timestamp_tz = date("F l, Y h:i A", substr($row[2], 0, 10));
  		$resultArray[] =  array(
  					"valid_start_ns" => $row[3],
  					"consensus_timestamp" => $consensus_timestamp_tz,
  					"sender" => $row[0],
  					"amount" => $combinedWay,
  					"formatted_vsn"	 => $nsHypen,
  					"dollerValue"  =>$dollerValue
  		);
  	}

  	echo json_encode($resultArray);
  	exit();
  ?>
 