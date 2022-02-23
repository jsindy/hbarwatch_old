<?php  
require 'config.php';
?>
 <head>

 <br><br>
    <p style="text-align:center;"><img src="/images/logo1.png" alt="HBAR Watch Logo" width="38%"></p>
	
  	<title>HBAR Watch</title>
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
					<h2 class="heading-section">Live Transactions (over 5,000 ℏ)</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" >
					<table id="dtBasicExample" class="table table-bordered table-dark table-hover">
					  <thead>
					    <tr>
					      
					      <th>Transaction</th>
					      <th>Consensus Time</th>
					      <th>Sender</th>
					      <th>Amount</th>
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
					  	
					  	if(item.isNew)
					  	{
					  		htmlRow += '<tr style="border-left: 6px solid green;">';
					  	}
					  	else
					  	{
					  		htmlRow += '<tr style="border-left: 6px solid yellow;">';
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
    });
</script>
	</body>