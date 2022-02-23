<?php  
require 'config.php';

//to get dollar value
	$curlSession1 =  curl_init();
	curl_setopt($curlSession1, CURLOPT_URL, 'https://api.coingecko.com/api/v3/simple/price?ids=hedera-hashgraph&vs_currencies=USD');
	curl_setopt($curlSession1, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession1, CURLOPT_RETURNTRANSFER, true);

	$dollerInfo = json_decode(curl_exec($curlSession1), true);
	curl_close($curlSession1);
	$dollerValue = $dollerInfo['hedera-hashgraph']['usd']; 

	$resultCount = pg_query($conn, "select count(*) as total  from public.hbar_usd") ;
	$rowRecords = pg_fetch_array($resultCount);
	$rowRecords = $rowRecords['total'];
	//print_r($rowRecords);die;
	if($rowRecords > 0)
	{
		$query = "UPDATE public.hbar_usd
		SET hbar_value = ".$dollerValue."
		RETURNING hbar_id";
		$nId = pg_query($conn, $query) ;
		 if($nId > 0)
		 	echo "Dollar rate updated successfully!";
	}
	else
	{

		$query = "INSERT INTO public.hbar_usd (hbar_id,hbar_value)
		VALUES(1,".$dollerValue.") 
		RETURNING hbar_id";
		$nId = pg_query($conn, $query) ;
		
		 if($nId > 0)
		 	echo "Dollar rate inserted successfully!";
	}