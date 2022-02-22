<?php  
require 'config.php';
$resultCount = pg_query($conn, "select count(*) as total from crypto_transfer") ;
while($row = pg_fetch_array($resultCount)){
	$totalReacords = $row["total"];
}
$total_pages = ceil($totalReacords / TRANSECTIONSNO);
//print_r($total_pages);die;
// $result = pg_query($conn, "select * from get_results_test(".AMOUNT.",".TRANSECTIONSNO.")") ;
// if (!$result) {  
//  echo "An error occurred.\n";  
//  exit;  
// } 

//to get dollar value
	$curlSession1 =  curl_init();
	curl_setopt($curlSession1, CURLOPT_URL, 'https://api.coingecko.com/api/v3/simple/price?ids=hedera-hashgraph&vs_currencies=USD');
	curl_setopt($curlSession1, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession1, CURLOPT_RETURNTRANSFER, true);

	$dollerInfo = json_decode(curl_exec($curlSession1), true);
	curl_close($curlSession1);
	$dollerValue = $dollerInfo['hedera-hashgraph']['usd']; 
?>
 <head>
    <img src="/images/logo1.png" alt="HBAR Watch Logo" width="50%">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="css/style.css">
	<style type="text/css">
		.pagination a {
		  color: black;
		  float: left;
		  padding: 8px 16px;
		  text-decoration: none;
		}

		.pagination a.active {
		  background-color: black;
		  color: black;
		  border-radius: 5px;
		}

		.pagination a:hover:not(.active) {
		  background-color: black;
		  border-radius: 5px;
		}
	</style>

	</head>
	<body>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
					<h2 class="heading-section">Latest Transactions</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" >
					<!-- <div >
						<table class="table table-bordered table-dark table-hover">
						  <thead>
						    <tr>
						      
						      <th>valid_start_ns</th>
						      <th>consensus_timestamp</th>
						      <th>sender</th>
						      <th>amount</th>
						    </tr>
						  </thead>
						  <tbody>
						  <?php 
						  	
						  	while ($row = pg_fetch_row($result)) {
						  		//print_r($row);die;
						  		$hypen = '-';
								$position = '-9';
								  
								$nsHypen = substr_replace( $row[3], $hypen, $position, 0 );
						  		$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"."transaction/0.0.".$row[0]
						  		."-".$nsHypen;

						  		$firstWay = number_format($row[1]/ 100000000).' ℏ';	
						  		$secondWay = number_format(round((($row[1]/ 100000000) * $dollerValue), 2));

						  		$combinedWay = $firstWay.'   ($ '.$secondWay.')';
						  		
						  ?>
						    <tr>
						      
						      <th scope="row">
						      	  <a href="<?php echo $url;?>" target="_blank">
								    <div style="height:100%;width:100%">
								      <?php echo $row[3]; ?>
								    </div>
								  </a>

						      </th>
						      
						      <td><?php echo date("Y-m-d H:i:s", substr($row[2], 0, 10)); ?></td>
						      <td><?php echo '0.0.'.$row[0]; ?></td>
						      <td><?php echo $combinedWay; ?></td>
						    </tr>
						  <?php } ?>
						  </tbody>
						</table>
					</div> -->
					<table id="dtBasicExample" class="table table-bordered table-dark table-hover">
					  <thead>
					    <tr>
					      
					      <th>valid_start_ns</th>
					      <th>consensus_timestamp</th>
					      <th>sender</th>
					      <th>amount</th>
					    </tr>
					  </thead>
					  <tbody id="target-content">
					  </tbody>
					</table> 
				</div>
				
			</div>
		</div>
	</section>

	<script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
  <script type="text/javascript">

   	var refreshTime = <?php echo REFRESH; ?>;
   	var data = [];

   	function fetchTableData(pageID)
   	{
   		var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
   		var currentLocation = window.location;
   		$.ajax({
				url: "pagination.php",
				type: "GET",
				data: {
					page : pageID,
					tz : tz
				},
				cache: false,
				success: function(dataResult){
					var html ="";
					const newArr = jQuery.parseJSON(dataResult); // for new data
					  const oldArr = data; // for old data that is currently being shown

					  const isSameRecord = (a, b) =>
					    a.valid_start_ns === b.valid_start_ns &&
					    a.consensus_timestamp === b.consensus_timestamp &&
					    a.sender === b.sender &&
					    a.amount === b.amount;

					  const checkNewRecords = (left, right, compareFunction) =>
					    left.map((leftValue) => {
					      if (!right.some((rightValue) => compareFunction(leftValue, rightValue))) {
					        return { ...leftValue, isNew: true };
					      }
					      return { ...leftValue, isNew: false };
					    });

					  const result = checkNewRecords(newArr, oldArr, isSameRecord);
					  data = result;

					  result.forEach((item) => {
					  	let htmlRow = "";
					  	var url = currentLocation + "transaction.php?id=0.0." + item.sender 
					  	+ "-" + item.formatted_vsn
					  	+ "&tz="+ tz;
					  	//alert(url);
					  	if(item.isNew)
					  	{
					  		htmlRow += '<tr style="border-left: 2px solid green;">';
					  	}
					  	else
					  	{
					  		htmlRow += '<tr style="border-left: 2px solid red;">';
					  	}
					  	
					  	htmlRow += '<td>';
					  	htmlRow += '<a href='+ url + ' target="_blank">';
					 	htmlRow += item.valid_start_ns; 
					 	htmlRow += '</a>';         
					  	htmlRow += '</td>';
					  	htmlRow += '<td>';
					 	htmlRow += item.consensus_timestamp;          
					  	htmlRow += '</td>';
					  	htmlRow += '<td>';
					 	htmlRow += '0.0.'+item.sender;          
					  	htmlRow += '</td>';
					  	htmlRow += '<td>';
					 	htmlRow += item.amount;          
					  	htmlRow += '</td>';
 	            	  	htmlRow += '</tr>';
					   
					    html += htmlRow;
					  });
					  $("#target-content").html(html);

					  setTimeout(function() {
				  		fetchTableData(1);
					 },  refreshTime * 1000);
					
				},
				error: function() { 
			      setTimeout(function() {
			  		fetchTableData(1);
				 },  refreshTime * 1000);  
			    }

			});
   	}

  </script>
  <script>
	$(document).ready(function() {
		fetchTableData(1);
		$(".page-link").click(function(){
			var id = $(this).attr("data-id");
			var tolat_pages = <?php echo $total_pages;?>;
			if(id > 9 && id < tolat_pages)
			{
				tempid = parseInt(id) + 1;
				$(this).attr("data-id",tempid); 
			}
			var select_id = $(this).parent().attr("id");
			fetchTableData(id);
		});
    });
</script>
	</body>