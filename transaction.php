<?php  
	require 'config.php';
  	date_default_timezone_set($_GET['tz']);

	$apiURL = "http://local.hbar.watch:5551/api/v1/transactions/".$_GET['id'];
	
    $curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, $apiURL);
	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

	$jsonData = json_decode(curl_exec($curlSession), true);
	$transactionData = $jsonData['transactions'][0];
	curl_close($curlSession);

	//to get dollar value from database
	
  $resultHbar = pg_query($conn, "select hbar_value from public.hbar_usd") ;
  $rowHbar = pg_fetch_array($resultHbar);
  $dollerValue = $rowHbar['hbar_value'];
?>
 <head>
  	<title>Transaction Info</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="css/style.css">

	<script type="module">
	   

	</script>



	</head>
	<body>
	<section class="ftco-section">
		<input type="hidden" id="tz">
		<div class="container">
			<div class="row ">
				<div class="col-md-6">
					<h5 class="heading-section">Transaction Info</h5>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div>
						<table class="table table-bordered table-dark table-hover">
						  
						  <tbody>
						    <tr>
						      <td>ID</td>
						      <td><?php echo $transactionData['transaction_id']; ?></td>
						    </tr>
						    <tr>
						      <td>Consensus At</td>
						      <td><?php echo date("F l, Y h:i A", substr($transactionData['consensus_timestamp'], 0, 10)); ?></td>
						    </tr>
						    <tr>
						      <td>Hash</td>
						      <td><?php echo $transactionData['transaction_hash'] ?></td>
						    </tr>
						    <tr>
						      <td>Type</td>
						      <td>Transfer</td>
						    </tr>
						    <tr>
						      <td>Status</td>
						      <td><?php echo $transactionData['result'] ?></td>
						    </tr>
						    <tr>
						      <td>Memo</td>
						      <td><?php echo $transactionData['memo_base64'] ?></td>
						    </tr>
						    <tr>
						      <td>Operator Account</td>
						      <td><?php 
						      $tempID = explode('-', $transactionData['transaction_id']);

						      echo $tempID[0] ?></td>
						    </tr>
						    <tr>
						      <td>Node Account</td>
						      <td><?php echo $transactionData['node'] ?></td>
						    </tr>
						    <tr>
						      <td>Fee</td>
						      <td><?php echo 
						      number_format((float)$transactionData['charged_tx_fee']/ 100000000, 8, '.', ',').' ℏ    ($  ' 
						       .
						      number_format(round((($transactionData['charged_tx_fee']/ 100000000)) * $dollerValue), 2).')'
						      ?></td>
						    </tr>
						    <tr>
						      <td>Max Fee</td>
						      <td><?php echo 
						      number_format((float)$transactionData['max_fee']/ 100000000, 8, '.', ',').
						      ' ℏ     ($  ' .
						      number_format(round((($transactionData['max_fee']/ 100000000) * $dollerValue), 2)) .')' ?></td>
						    </tr>

						  
						  </tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row ">
				<div class="col-md-6">
					<h5 class="heading-section">Transfers</h5>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div>
						<table class="table table-bordered table-dark table-hover">
						  
						  <tbody>
						  	<?php foreach ($transactionData['transfers'] as $value) { 
						  		$firstWay = 
						  		number_format((float)$value['amount']/ 100000000, 8, '.', ',').' ℏ';	
						  		$secondWay = number_format(round((($value['amount']/ 100000000) * $dollerValue), 2));

						  		$combinedWay = $firstWay.'   ($ '.$secondWay.')';

						  		?>
						    <tr>
						      <td class="col-md-3"><?php echo $value['account']; ?></td>
						      <td class="col-md-9"><?php echo $combinedWay ?></td>
						    </tr>
						    <?php } ?>
						  
						  </tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
 
	</body>