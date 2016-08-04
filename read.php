<?php 
 // Delete function Start
if(isset($_GET['act']) && $_GET['act'] == "del"){ 
	  extract($_GET);
	  // Assigning values to variables
	  $prname = base64_decode($prname); 
	  $premail = base64_decode($premail);
	  $prvname = base64_decode($prvname);
	  $prvurl = base64_decode($prvurl);
	  $prdesc = base64_decode($prdesc);
	  $prloc = base64_decode($prloc);
	
	 // Reading our xml file and assign those to variables
	$xml=simplexml_load_file("data.xml");
	 
	// getting howmany feeds are there in xml page
	$len=$xml->prdata->count();
	// loop start and check if deleted feed data exists then remove those from xml feed page
	for($i=0;$i<$len;$i++){		
		 if($xml->prdata[$i]->pruploader_name == $prname && $xml->prdata[$i]->pruploader_email == $premail && $xml->prdata[$i]->prvideoname == $prvname && $xml->prdata[$i]->prvideourl == $prvurl && $xml->prdata[$i]->prdesc == $prdesc && $xml->prdata[$i]->prloc == $prloc){
			  $prvurl = explode("http://uploads.prospervr.com/",$xml->prdata[$i]->prvideourl);
			  unlink($prvurl[1]);  // removing file from our server
			  unset($xml->prdata[$i]); // removing feed data			 
		   } 
	}

		$doc = new DOMDocument('1.0');
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = true;
		$doc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
		$doc->save('data.xml');  // saving data
		 ?>
         <script type="text/javascript">
			location.href="read.php"; // redirect to same page
		 </script>
         <?php
}
 // Delete function End
 // Start reading for displaying data to this page
$curl = curl_init();        
curl_setopt ($curl, CURLOPT_URL, 'http://uploads.prospervr.com/data.xml');   
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   
$result = curl_exec ($curl);   

if ($result === false) {
    die('Error fetching data: ' . curl_error($curl));   
}
curl_close ($curl);    
 
//parse xml string into SimpleXML objects
$xml = simplexml_load_string($result);
 
if ($xml === false) {
    die('Error parsing XML');   
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Managing Data</title>
<!-- stylesheet path-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<!-- stylesheet path-->
<!-- Start validation code-->
<script type="text/javascript">
function deleted(prname,premail,prvname,prvurl,prdesc,prloc){
	location.href="read.php?act=del&prname="+prname+"&premail="+premail+"&prvname="+prvname+"&prvurl="+prvurl+"&prdesc="+prdesc+"&prloc="+prloc;
}
</script>
<!-- Inline Style -->
<style>
td, th {
    padding: 5px 15px;
    text-align: left;
}
</style>
<!-- Inline Style -->
</head>

<body>
<div class="contaner_outer">
  <div class="conatiner">
  <h1>All Records</h1>
<table border="0" width="100%">
<tr>
	<th>Name</th>
    <th>Email</th>
    <th>Video Name</th>
    
    <th>Description</th>
    <th>Location</th>
    <th>Action</th>
</tr>
<?php
//now we can loop through the xml structure
if(count($xml->prdata) > 0){ // if feeds are not exists dislay no records data
foreach ($xml->prdata as $item) { // if feeds are exists display all values one by one line
?>
   <tr>
	<td><?php if($item->pruploader_name != "--"){ echo $item->pruploader_name; } else { echo '---';} ?></td>
    <td><?php if($item->pruploader_email != "--"){ echo $item->pruploader_email; } else { echo '---';} ?> </td>
    <td><?php if($item->prvideoname != "--"){ echo $item->prvideoname; } else { echo '---';} ?></td> 
    <td><?php if($item->prdesc != "--"){ echo substr($item->prdesc,0,50).'...'; } else { echo '---';} ?></td>
    <td><?php if($item->prloc != "--"){ echo $item->prloc; } else { echo '---';} ?> </td>
    <td><input type="button" name="delete" value="Delete" onClick="deleted('<?php echo base64_encode($item->pruploader_name); ?>','<?php echo base64_encode($item->pruploader_email); ?>','<?php echo base64_encode($item->prvideoname); ?>','<?php echo base64_encode($item->prvideourl); ?>','<?php echo base64_encode($item->prdesc); ?>','<?php echo base64_encode($item->prloc); ?>');"></td>
</tr> 
<?php  
	} 
} else {
	echo '<tr><td colspan="7">-- No Records are there --</td></tr>';
}
?>
</table>
 <!-- End HTML Form code-->
  </div>
</div>
</body>
</html>