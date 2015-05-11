<?php

namespace LinkedIn;
require 'LinkedIn.php';

session_start();

$li = new LinkedIn(
  array(
    'api_key' => '7752iv95bfdtho', 
    'api_secret' => 'gfdyHZcOQKT6oapY', 
    'callback_url' => 'http://localhost/linkdenlogin/userDetails.php'
  )
);

$token = $li->getAccessToken($_REQUEST['code']);
$token_expires = $li->getAccessTokenExpiration();

$info = $li->get('/people/~:(first-name,last-name,skills,educations,positions,public-profile-url,picture-url,courses,summary)');


$dbhost = "localhost";
$dbuser = "linked_cms";
$dbpass = "password";
$dbname = "linkedin";
$connection = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
	if (mysqli_connect_error()) {
		die("Database connection failed: " .
		mysqli_connect_error() .
			" (" . mysqli_connect_errno() . ")"
		);
	}

	$firstName = $info['firstName'];
	$lastName  = $info['lastName'];
	$summary   = $info['summary'];
	$query = "INSERT INTO user (first_name, last_name)
			  VALUES ('{$firstName}','{$lastName}')";

	 $result = mysqli_query($connection, $query);

	if (!$result) {
		die("FAILED 45");
	}

	$query = "SELECT * FROM user ";

	 $result = mysqli_query($connection, $query);

	if (!$result) {
		die("FAILED 64");
	}

	$total = $info['positions']['_total'];

	for ($x = 0; $x < $total; $x++){
		$companyName = $info['positions']['values'][$x]['company']['name'];
		$title       = $info['positions']['values'][$x]['title'];
		$query = "INSERT INTO user (company_name, title)
			  	   VALUES ('{$companyName}','{$title}')";

		$result = mysqli_query($connection, $query);

		if (!$result) {
			die("FAILED 78");
		}
	}
	

	$total = $info['skills']['_total'];

	for ($x = 0; $x < $total; $x++){
		$skills = $info['skills']['values'][$x]['skill']['name'];
		$query  = "INSERT INTO user (skills)
			  	   VALUES ('{$skills}')";

		$result = mysqli_query($connection, $query);

		if (!$result) {
			die("FAILED 93");
		}
	}


	$total = $info['educations']['_total'];

	for ($x = 0; $x < $total; $x++){
		$schoolName   = $info['educations']['values'][$x]['schoolName'];
		$degree       = $info['educations']['values'][$x]['degree'];
		$fieldOfStudy = $info['educations']['values'][$x]['fieldOfStudy'];
		$query  = "INSERT INTO user (college_name, college_degree)
			  	   VALUES ('{$schoolName}','{$fieldOfStudy}')";

		$result = mysqli_query($connection, $query);

		if (!$result) {
			die("FAILED 110");
		}
	}


	$total = $info['courses']['_total'];

	for ($x = 0; $x < $total; $x++){
		$courses   = $info['courses']['values'][$x]['name'];
		$query  = "INSERT INTO user (courses)
			  	   VALUES ('{$courses}')";

		$result = mysqli_query($connection, $query);

		if (!$result) {
			die("FAILED 125");
		}
	}

	$image = $info['pictureUrl'];
	$query = "INSERT INTO user (image)
			  VALUES ('{$image}')";

	 $result = mysqli_query($connection, $query);

	if (!$result) {
		die("FAILED 136");
	}
	
	$query = "SELECT * FROM user ";

	 $result = mysqli_query($connection, $query);

	if (!$result) {
		die("FAILED 144");
	}

	

	$query1 = "CREATE TABLE duplicate LIKE user"; 
	$dupresult = mysqli_query($connection, $query);

	if (!$dupresult) {
		die("FAILED 153");
	}
	$query1	= "INSERT duplicate SELECT * FROM user";
	$dupresult = mysqli_query($connection, $query1);

	if (!$dupresult) {
		die("FAILED 159");
	}
	 	
		
	$query = "SELECT * FROM duplicate ";

	$dupresult = mysqli_query($connection, $query);

	if (!$dupresult) {
		die("FAILED 168");
	}
?>






<!DOCTYPE html>
<html lang="en">
<HTML>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

		<style type="text/css">
		</style>
	
	</head>
	<body style="background:#4875B4;">
	<div class="container">	
		<div class="container-fluid">
		
		<?php
			while($row = mysqli_fetch_assoc($result)){
				//var_dump($row);
				$array[] = $row;
			}
			$count =  count($array);
			while($row = mysqli_fetch_assoc($dupresult)){
				//var_dump($row);
				$dupArray[] = $row;
			}
			$dupCount =  count($dupArray);
			//print_r($dupArray);
			for ($i=0; $i <$count ; $i++) { 
				$temp1 = $array[$i]['first_name']; 
				$temp2 = $array[$i]['last_name'];
				$sub = $dupCount-$i-1-$count;
				$dupTemp1 = "";
				$dupTemp2 = "";
				if ($sub>1) {

				 	$dupTemp1 = $dupArray[$sub]['first_name']; 
					$dupTemp2 = $dupArray[$sub]['last_name'];
				 } 
				if (strlen($temp1)>0 && strlen($temp2)>0) {
					$firstName = $temp1;
					$lastName = $temp2;	
				}

				if (strlen($dupTemp1)>0 && strlen($dupTemp2)>0) {
					$dupFirstName = $dupTemp1;
					$dupLastName = $dupTemp2;	
				}else{
					$dupFirstName = $firstName;
					$dupLastName = $lastName;
				}
			}


		?>


		<?php
			for ($i=0; $i <$count ; $i++) { 
				$temp1 = $array[$i]['image']; 
				if (count($dupArray)>count($array)) {
					$sub = $dupCount+$i-$count-$count;	
				}
				else{
					$sub = $i;
				}
			
				$dupTemp1 = "";
				$dupTemp2 = "";
				$arrayimage = array();
					 
				if (strlen($temp1)>0) {
					$image = $temp1;
					$arrayimage[] = $image;

				}

				if ($sub>=1) {

					 $dupTemp1 = $dupArray[$sub]['image']; 	
				}

				if (strlen($dupTemp1)>0 ) {
					$dupimage = $dupTemp1;
					$storeimage[] =  $dupimage;

				}
			}
				?>

		<div class="row" style="background-color:#4875B4; color:black; padding:20px;">
			


			
		<?php

			$arrlength = count($arrayimage);

			for($x = 0; $x < $arrlength; $x++) {
				if ($storeimage[$x]==$arrayimage[$x] ) { ?>
	<div class="col-sm-6 col-md-6 col-xs-6">
		
			<div class="col-sm-6 col-md-6 col-xs-12 image-container">
					<p>	<img src = "<?php echo $arrayimage[$x] ?>" alt="Profile Picture" style="width:200px;height:150px,float:left" /></p>
			</div>
			<?php 
				
				} 	
				else { ?>
			<div class="col-sm-12 col-md-12 col-xs-12">  
					<p>UPDATED</p>
					<p>	<img src = "<?php echo $arrayimage[$x] ?>" alt="Profile Picture" style="width:200px;height:150px,float:left" /></p>
			</div>

			<?php
				}
			}
		?>
			
		<?php if ($dupFirstName==$firstName && $dupLastName==$lastName) { ?>
	
			<h2 ><?php echo $firstName." ".$lastName ?></h2>
	<?php	
		}
		else { ?>
			<p>Updated</p>
			<h2 ><?php echo $firstName." ".$lastName ?></h2>
		
	</div>
	<?php 
		} ?>

		</div>
	</div>
		
	<div class="container-fluid">
		<div class="row" style="background-color:#DCDCDC; color:#A9A9A9; padding:20px;margin-top:5px;">
		
				<h2 style="text-align:center">Summary</h2>
				<p><?php echo $info['summary'] ?></p>
			
		</div>
		
	</div>

	<div class="container-fluid">
		<div class="row" style="background-color:#DCDCDC; color:#A9A9A9; padding:20px;margin-top:10px;">
				<h2 style="text-align:center">Experience</h2>
		<?php
			for ($i=0; $i <$count ; $i++) { 
				$temp1 = $array[$i]['company_name']; 
				$temp2 = $array[$i]['title']; 
				if (count($dupArray)>count($array)) {
					$sub = $dupCount+$i-$count-$count;	
				}
				else{
					$sub = $i;
				}			
				$dupTemp1 = "";
				$dupTemp2 = "";
				
							 
				if (strlen($temp1)>0 && strlen($temp2)>0) {
					$companyName = $temp1;
					$title = $temp2;
					$arrayCompanyName[] = $companyName;
					$arrayTitle[] = $title;

				}

				if ($sub>=1) {
					$dupTemp1 = $dupArray[$sub]['company_name']; 
					$dupTemp2 = $dupArray[$sub]['title'];	
				}

				if (strlen($dupTemp1)>0 && strlen($dupTemp2)>0) {
					$dupCompanyName = $dupTemp1;
					$dupTitle       = $dupTemp2;
					$storeCompanyName[] =  $dupCompanyName;
					$storeTitle[] =  $dupTitle;	

				}
			}
				?>
				<?php 

					$arrlength = count($arrayCompanyName);
					$arrlength1 = count($storeCompanyName);
					if ($arrlength > $arrlength1) {
						$storeCompanyName[$arrlength1] = " ";
						$storeTitle[$arrlength1] = " ";
					}

					for($x = 0; $x < $arrlength; $x++) {
						
				if ($storeCompanyName[$x]==$arrayCompanyName[$x] && $storeTitle[$x]==$arrayTitle[$x]) { ?>
				<ul class="list-group">		
				<li class="list-group-item">	
					<h3><?php echo $arrayCompanyName[$x] ?></h3>
					<p><?php echo $arrayTitle[$x] ?></p>	
				</li>
			<?php 
				
				} 	
				else { ?>
				<li class="list-group-item">
					<p>UPDATED</p>
					<h3><?php echo $arrayCompanyName[$x] ?></h3>
					<h3><?php echo $arrayTitle[$x] ?></h3>
				</li>
			
			<?php
				}
			}
		?>
				</ul>
		</div>
	</div>	


	
		<div style="background-color:#DCDCDC; color:#A9A9A9; padding:20px;margin-top:10px;">
			<h2 style="text-align:center">Skills</h2>

			<?php
			for ($i=0; $i <$count ; $i++) { 
				$temp1 = $array[$i]['skills']; 
				if (count($dupArray)>count($array)) {
					$sub = $dupCount+$i-$count-$count;	
				}
				else{
					$sub = $i;
				}
			
				$dupTemp1 = "";
				$dupTemp2 = "";
				
							 
				if (strlen($temp1)>0) {
					$skills = $temp1;
					$arrayskills[] = $skills;

				}

				if ($sub>=1) {
					$dupTemp1 = $dupArray[$sub]['skills']; 	
				}

				if (strlen($dupTemp1)>0 ) {
					$dupskills = $dupTemp1;
					$storeskills[] =  $dupskills;

				}
			}
				?>

		<?php


		$arrlength = count($arrayskills);
		$arrlength1 = count($storeskills);
		if ($arrlength > $arrlength1) {
			for ($i=0; $i < $arrlength; $i++) { 
				$storeskills[$arrlength1+$i] = " ";
			}
		}
			for ($x=0; $x <$arrlength ; $x++) { 
				if ($storeskills[$x]==$arrayskills[$x] ) { ?>
			<ul class="list-group">	
				<li class="list-group-item"><?php echo $arrayskills[$x] ?></li>		
			<?php
				}
				else{ ?>
					<p>UPDATED</p>
					<h3><?php echo $arrayskills[$x] ?></h3>
			<?php	}
			}
		?>
   			 	
			</ul>
		</div>
	



		<div style="background-color:#DCDCDC; color:#A9A9A9; padding:20px;margin-top:10px;">
			<h2 style="text-align:center">Education</h2>
			

			<?php
			for ($i=0; $i <$count ; $i++) { 
				$temp1 = $array[$i]['college_name'];
				$temp2 = $array[$i]['college_degree']; 
				if (count($dupArray)>count($array)) {
					$sub = $dupCount+$i-$count-$count;	
				}
				else{
					$sub = $i;
				}
			
				$dupTemp1 = "";
				$dupTemp2 = "";
				
							 
				if (strlen($temp1)>0) {
					$college_name = $temp1;
					$arraycollege_name[] = $college_name;

					$college_degree = $temp2;
					$arraycollege_degree[] = $college_degree;

				}

				if ($sub>=1) {
					$dupTemp1 = $dupArray[$sub]['college_name'];
					$dupTemp2 = $dupArray[$sub]['college_degree']; 	
				}

				if (strlen($dupTemp1)>0 ) {
					$dupcollege_name = $dupTemp1;
					$storecollege_name[] =  $dupcollege_name;

					$dupcollege_degree = $dupTemp2;
					$storecollege_degree[] =  $dupcollege_degree;
						
				}
			}
				?>

			<?php


		$arrlength = count($arraycollege_name);
		$arrlength1 = count($storecollege_name);
		if ($arrlength > $arrlength1) {
			for ($i=0; $i < $arrlength; $i++) { 
				$storecollege_name[$arrlength1+$i] = " ";
			}
			
		}

		 $total = $info['educations']['_total'];
			for ($x=0; $x <$arrlength ; $x++) { 
				if ($storecollege_name[$x]==$arraycollege_name[$x] &&  $storecollege_degree[$x]==$arraycollege_degree[$x] ) { ?>
			<ul class="list-group">	
			<li class="list-group-item">
   			 	<p>University: <?php echo $arraycollege_name[$x] ?></p>		
   			 	<p>Degree: <?php echo $info['educations']['values'][$x]['degree'] ?></p>
   			 	<p>Major: <?php echo $arraycollege_degree[$x] ?></p>	
   			 </li>
				
			<?php
				}
				else{ ?>
				<li class="list-group-item">
					<p>UPDATED</p>
					<h3>University: <?php echo $arraycollege_name[$x] ?></h3>
					<h3>Degree: <?php echo $info['educations']['values'][$x]['degree'] ?></h3>
   			 		<h3>Major: <?php echo $arraycollege_degree[$x] ?></h3>	
   			 	</li>
			<?php	}
			}
		?>
			</ul>



		</div>	
		<div style="background-color:#DCDCDC; color:#A9A9A9; padding:20px;margin-top:10px;">
			<h2 style="text-align:center">Courses</h2>
			
			<?php
			for ($i=0; $i <$count ; $i++) { 
				$temp1 = $array[$i]['courses']; 
				if (count($dupArray)>count($array)) {
					$sub = $dupCount+$i-$count-$count;	
				}
				else{
					$sub = $i;
				}
			
				$dupTemp1 = "";
				$dupTemp2 = "";
				
							 
				if (strlen($temp1)>0) {
					$courses = $temp1;
					$arraycourses[] = $courses;

				}

				if ($sub>=1) {
					$dupTemp1 = $dupArray[$sub]['courses']; 	
				}

				if (strlen($dupTemp1)>0 ) {
					$dupcourses = $dupTemp1;
					$storecourses[] =  $dupcourses;

				}
			}
				?>

			<?php


		$arrlength = count($arraycourses);
		$arrlength1 = count($storecourses);
		if ($arrlength > $arrlength1) {
			for ($i=0; $i < $arrlength; $i++) { 
				$storecourses[$arrlength1+$i] = " ";
			}

		}
			for ($x=0; $x <$arrlength ; $x++) { 
				if ($storecourses[$x]==$arraycourses[$x] ) { ?>
			<ul class="list-group">		
				<li class="list-group-item"><?php echo $arraycourses[$x] ?></li>		
			<?php
				}
				else{ ?>
				<li class="list-group-item">
					<p>UPDATED</p>
					<h3><?php echo $arraycourses[$x] ?></h3>
				</li>	
			<?php	}
			}
		?>
			</ul>


		</div>
	</div>	

	</body>
</HTML>

<?php
	$query = "TRUNCATE user";
	$result = mysqli_query($connection, $query);

	//$query = "TRUNCATE duplicate";
	//$result = mysqli_query($connection, $query);

	mysqli_close($connection);
?>
