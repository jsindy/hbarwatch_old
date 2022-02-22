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

//to get dollar value
	$curlSession1 =  curl_init();
	curl_setopt($curlSession1, CURLOPT_URL, 'https://api.coingecko.com/api/v3/simple/price?ids=hedera-hashgraph&vs_currencies=USD');
	curl_setopt($curlSession1, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession1, CURLOPT_RETURNTRANSFER, true);

	$dollerInfo = json_decode(curl_exec($curlSession1), true);
	curl_close($curlSession1);
	$dollerValue = $dollerInfo['hedera-hashgraph']['usd']; 
?>

  <?php 
  	
  	$resultArray = array();
  	while ($row = pg_fetch_row($result)) {
  		//print_r($row);die;
  		$hypen = '-';
		$position = '-9';
		  
		// $nsHypen = substr_replace( $row[3], $hypen, $position, 0 );
  // 		$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"."transaction/0.0.".$row[0]
  // 		."-".$nsHypen;

  		$nsHypen = substr_replace( $row[3], $hypen, $position, 0 );
  		// $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/monst6/"."transaction.php?id=0.0.".$row[0]
  		// ."-".$nsHypen;

  		//$firstWay = number_format($row[1]/ 100000000).' ℏ';	
  		//$secondWay = number_format(round((($row[1]/ 100000000) * $dollerValue), 2));
  		
  		//$combinedWay = $firstWay.'   ($ '.$secondWay.')';	
  		$consensus_timestamp_tz = date("F l, Y h:i A", substr($row[2], 0, 10));
  		$resultArray[] =  array(
  					"valid_start_ns" => $row[3],
  					"consensus_timestamp" => $consensus_timestamp_tz,
  					"sender" => $row[0],
  					"amount" => $row[1],
  					"formatted_vsn"	 => $nsHypen,
  					"dollerValue"  =>$dollerValue
  		);
  	}

  	echo json_encode($resultArray);
  	exit();
  ?>
 