<?php

header("Access-Control-Allow-Origin: *");

if(!isset($_GET['name']))
{

$key=$_GET['keyword'];
$key1 = preg_split("/[\s,]+/", $key);

$keyword="";

for($i=0;$i<(int)count($key1)-1;$i++)
{
  $keyword.=$key1[$i].'+';
}

 $val1=(int)count($key1)-1;
    $keyword.= $key1[$val1];

 //echo $keyword;
$cat=$_GET['category'];
$cat1 = preg_split("/[\s,]+/", $cat);

$category="";

for($i=0;$i<(int)count($cat1)-1;$i++)
{
  $category.=$cat1[$i].'+';
}

 $val2=(int)count($cat1)-1;
    $category.= $cat1[$val2];

//echo $category;

    $dist=$_GET['distance'];

    if($dist=="")
      $dist="10";

    //echo $dist;

$distance=(int)$dist*1609.34;
$distancestring=(string)$distance;
$radio=$_GET['optionsRadios'];

  switch($radio) {
        case "option2":

           $usedopt="loc";

            $location=$_GET['location'];

            $keywords = preg_split("/[\s,]+/", $location);

$string="";

for($i=0;$i<(int)count($keywords)-1;$i++)
{
  $string.=$keywords[$i].'+';
}

 $val=(int)count($keywords)-1;
    $string.= $keywords[$val];

    //print_r($string);

$url1='https://maps.googleapis.com/maps/api/geocode/json?address='.$string.'&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw';

$contents1 = file_get_contents($url1);

if($contents1 !== false){
    //Print out the contents.

    $arrayloc=json_decode($contents1,TRUE);
     
    $locationlat=$arrayloc['results'][0]['geometry']['location']['lat'];
    $locationlon=$arrayloc['results'][0]['geometry']['location']['lng'];


   // print_r(json_decode($contents1,TRUE));


} 


         if($category=="default")
          $url2 = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$locationlat.','.$locationlon.'&radius='.$distancestring.'&keyword='.$keyword.'&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw';
         else
            $url2 = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$locationlat.','.$locationlon.'&radius='.$distancestring.'&type='.$category.'&keyword='.$keyword.'&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw';
            break;

        case "option1":

        $usedopt="here";
            $lat=$_GET['lat'];
            $lon=$_GET['lon'];

            if($category=="default")
              $url2 = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$lat.','.$lon.'&radius='.$distancestring.'&keyword='.$keyword.'&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw';
            else 
            $url2 = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$lat.','.$lon.'&radius='.$distancestring.'&type='.$category.'&keyword='.$keyword.'&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw';
            break;


        }
 

$contents2 = file_get_contents($url2);

if($contents2 !== false){
  //print_r($contents2);

  $check1=json_decode($contents2,TRUE);

  /*if($check1['next_page_token'])
  {
    echo "yasssss";
  }*/

 $contents3="";
 $contents4="";
 
  if(isset($check1['next_page_token']))
  {

    $nextpage=$check1['next_page_token'];
    $url3="https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=".$nextpage."&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw";
    $contents3 = file_get_contents($url3);

    $check2=json_decode($contents3,TRUE);

    while($check2['status']=="INVALID_REQUEST")
    {
     // $contents2="detecting";
      $contents3 = file_get_contents($url3);
      $check2=json_decode($contents3,TRUE);

     // if($ccheck2['status']=="INVALID_REQUEST")
      //  $content2="secondtime";
    }

    //else $contents2=$check2['status'];

      
       if(isset($check2['next_page_token']))
  {

    $nextpage1=$check2['next_page_token'];
    $url4="https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=".$nextpage1."&key=AIzaSyAWCDfTQCL36oKKsl5aLF98U6imeFF_0Aw";
    $contents4 = file_get_contents($url4);
    $check3=json_decode($contents4,TRUE);
    
    while($check3['status']=="INVALID_REQUEST")
    {
      $contents4 = file_get_contents($url4);
      $check3=json_decode($contents4,TRUE);
    }
}
   

  }

  $result= array('result1' =>$contents2, 'result2' =>$contents3,'result3' =>$contents4);

 
  echo json_encode($result); 
 }

}

?>

<?php
if(isset($_GET['name']))
 
{


  $name=$_GET['name'];
  $address1=$_GET['address1'];
  $address2=$_GET['address2'];
  $city=$_GET['city'];
  $state=$_GET['state'];
  $country=$_GET['country'];

 // if($address2==" ")
$cit=str_replace(" ","%20",$city);
$nam=str_replace(" ","%20",$name);
$add=str_replace(" ","%20",$address1);
$add2=str_replace(" ","%20",$address2);

if($address2==" ")
  $apiurl="https://api.yelp.com/v3/businesses/matches/best?city=".$cit."&name=".$nam."&address1=".$add."&state=".$state."&country=".$country;
  else
   $apiurl="https://api.yelp.com/v3/businesses/matches/best?name=".$nam."&address1=".$add."&address2=".$add2."&city=".$cit."&state=".$state."&country=".$country; 

 //$api=urlencode($apiurl);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $apiurl,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Bearer qVP3Ai3S6yRWK-5MaNUke2GsBK324uPPyxUaCA6SnId0pUm4Hv2XxsXb4dPUNzVkCl7IspXTaxpoYZkcmhYM3hMqz69EkiKdhZaAxUeG2ggZiu6Z2LdU3teBFO3DWnYx",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  //echo json_encode($response);
  $yelpmatch=json_decode($response,TRUE);

  $id=$yelpmatch['businesses'][0]['id'];

 //echo $id;
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.yelp.com/v3/businesses/".$id."/reviews",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Bearer qVP3Ai3S6yRWK-5MaNUke2GsBK324uPPyxUaCA6SnId0pUm4Hv2XxsXb4dPUNzVkCl7IspXTaxpoYZkcmhYM3hMqz69EkiKdhZaAxUeG2ggZiu6Z2LdU3teBFO3DWnYx",
    "cache-control: no-cache",
    "postman-token: 1e3481bb-46ee-41ac-5eea-80006915ae63"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
}

//echo $apiurl;
}



?>