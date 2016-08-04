<?php
ini_set('upload_max_filesize', '1024M');
ini_set('post_max_size', '1024M');
ini_set('max_input_time', 500000);
ini_set('max_execution_time', 500000);

if($_POST){
      include("functions/functions.php");
      $pr_rand = rand();  // this is for randam number generation for appending string to attached files (video,audio or images)
      $url =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];   // getting current website url for appending website url to xml tag.
      
      $video_uploaded       = false;   //Log variables to kee track of success of events
      $thum_large_uploaded  = false;   
      $thum_thumb_uploaded  = false;
      $audio_names          = array();  //Array to store mutiple audio uploads
      $audios_extension     = array();
      $audios               = array();
      $temp_uploads         = "uploads/temp/";        // Upload directories
      $video_upload_url     = "uploads/videos/";
      $image_upload_url     = "uploads/images/";
      $audio_upload_url     = "uploads/audios/";
      $xmlvideoUrl_1 = '';
      $xmlvideoUrl_2 = '';
      $xmlvideoUrl_3 = '';
      $width1        = 0;
      $height1       = 0;
      $width2        = 0;
      $height2       = 0;
      $width3        = 0;
      $height3       = 0;

      //Read and extension of video, audios and images and send for validation
      $video_extension    = substr(strrchr($_FILES['pr_video']['name'],'.'),1);  
      //pathinfo($_FILES['pr_video']['name'], PATHINFO_EXTENSION);
      $thumb1_extension   = pathinfo($_FILES['pr_thumb_img']['name'], PATHINFO_EXTENSION);
      $largeimg_extension = pathinfo($_FILES['pr_lar_img']['name'], PATHINFO_EXTENSION);

      //Initialize the name of images and video with random generated number with original extension
      $pr_thumb_img1_original = "uploads/images/".$_FILES['pr_thumb_img']['name']; // file to upload path
      $pr_thumb_img1          = "uploads/images/".$pr_rand."_1.".$thumb1_extension; // file to upload path
      $pr_lar_img1_original   = "uploads/images/".$_FILES['pr_lar_img']['name']; // file to upload path
      $pr_lar_img1            = "uploads/images/".$pr_rand."_2.".$largeimg_extension; // file to upload path
      
      $new_Video_File = $temp_uploads.$pr_rand.".".$video_extension;
      $original_Video = $temp_uploads.$_FILES['pr_video']['name'];

      // Upload the video to temporary location, so that it can be manipulated first and then moved to uploads/videos
      if(move_uploaded_file($_FILES['pr_video']['tmp_name'], $original_Video)){
                  $video_uploaded = true;
                  rename($original_Video, $new_Video_File);
      }

      // Move Image to Image directory if image is selected
      if(isset($_FILES['pr_thumb_img']['name'])){
              if(move_uploaded_file($_FILES['pr_thumb_img']['tmp_name'], $pr_thumb_img1_original)){
                      rename($pr_thumb_img1_original, $pr_thumb_img1);
                      unlink($pr_thumb_img1_original);
              }
      }
      // Move Image to image directory if second image is selected
      if(isset($_FILES['pr_lar_img']['name'])){
              if(move_uploaded_file($_FILES['pr_lar_img']['tmp_name'], $pr_lar_img1_original)){
                      rename($pr_lar_img1_original, $pr_lar_img1);
                      unlink($pr_lar_img1_original);
              }
      }

      // if number of audios is greater than 1 then make an array of audio names and move to audio directory
      if(count($_FILES["pr_aud_file"]['name'])>0 && (count($_FILES["pr_aud_file"]['name']) > 0 && $_FILES["pr_aud_file"]['name'][0] != '' )) { 
              $audios_name = array();
              for($j=0; $j < count($_FILES["pr_aud_file"]['name']); $j++) { 
                  $filen                  = $_FILES["pr_aud_file"]['name']["$j"];
                  $ext                    = pathinfo($filen, PATHINFO_EXTENSION);
                  $path                   = $audio_upload_url.$pr_rand."_".$j.".".$ext;
                  $audios[$j]             = $path;
                  $audios_extension[$j]   = $ext;
                  //Move audios to /uploads/audio/ directory by renaming with randon name with _1, _2 etc. so it wont clash with each other
                  if(move_uploaded_file($_FILES["pr_aud_file"]['tmp_name']["$j"],$audio_upload_url.$filen )) { 
                    $audio_names[$j] = $path;
                    rename($audio_upload_url.$filen, $path);
                    unlink($audio_upload_url.$filen);
                  }
              }
      }

        // Validate media type here if they are supported format, currently no more validation
        $isValid_medias = validate_Medias($video_extension, $thumb1_extension, $largeimg_extension, $audio_extensions);

        // If all medias are valid then
        if($isValid_medias == true){

            if($video_uploaded == true){
                $output            = $temp_uploads.$pr_rand."_1.mp4";
                $extract_audio_cmd = "ffmpeg -i ".$output." ".$audio_upload_url.$pr_rand.".mp3";
                $extracted_audio   = '';
                $mergeAudios       = '';
                // Convert command for original video to mp4 format, always converts to mp4 extension as mp4 is supported most on other 
                // video conversion process   
                $conversion_cmd = "ffmpeg -i ".$new_Video_File." -vcodec copy -acodec copy ".$output." 2>&1";
                $conversion_log = shell_exec($conversion_cmd);
                $audio_convert_log = shell_exec($extract_audio_cmd);
                if(file_exists($audio_upload_url.$pr_rand.".mp3")){
                  $extracted_audio = $url.$audio_upload_url.$pr_rand.".mp3";
                }
                // Get the resolution of original video from opesouce Project -> plugin named ID3
                require_once('functions/getID3-1.9.9/getid3/getid3.php'); 
                $getID3 = new getID3;
                $file = $getID3->analyze($output);
                $actual_width  = $file['video']['resolution_x']; // store original video width
                $actual_height = $file['video']['resolution_y']; // Stor original video height

                // GET Lower screen sizes(2 lower resolution Eg. if original video is 320x240, two possible resolution is calculated here say 240, 160)
                $newScreenSize_1 = lowerResolution($actual_width, $actual_height); 
                $newScreenSize_2 = lowerResolution($newScreenSize_1[0], $newScreenSize_1[1]);
                $newScreenSize_3 = lowerResolution($newScreenSize_2[0], $newScreenSize_2[1]);
                // STORE 3 Different Video resolutions
                if($newScreenSize_1[0]>0 && $newScreenSize_1[1]>0){
                    $width1         = $newScreenSize_1[0];
                    $height1        = $newScreenSize_1[1];
                }else{
                    $width1         = $actual_width;
                    $height1        = $actual_height;                 
                }

                if($newScreenSize_2[0]>0 && $newScreenSize_2[1]>0){
                    $width2         = $newScreenSize_2[0];
                    $height2        = $newScreenSize_2[1];
                }else{
                    $width2         = $width1;
                    $height2        = $height1;                 
                }

                if($newScreenSize_3[0]>0 && $newScreenSize_3[1]>0){
                    $width3         = $newScreenSize_3[0];
                    $height3        = $newScreenSize_3[1];
                }else{
                    $width3         = $actual_width2;
                    $height3        = $actual_height2;                 
                }

                if($conversion_log){
                    // Set Variables to save in db / XML
                    $uploader           = htmlentities($_POST['pr_name'], ENT_COMPAT, 'UTF-8', false);
                    $uploader_email     = htmlentities($_POST['pr_email'], ENT_COMPAT, 'UTF-8', false);
                    $video_name         = htmlentities($_POST['pr_video_name'], ENT_COMPAT, 'UTF-8', false);
                    $video_description  = htmlentities($_POST['pr_desc'], ENT_COMPAT, 'UTF-8', false);
                    $location           = htmlentities($_POST['pr_loc'], ENT_COMPAT, 'UTF-8', false);
                    $labels             = htmlentities($_POST['pr_labels'], ENT_COMPAT, 'UTF-8', false);
                    $audios             = $audio_names;
                    $video              = htmlentities($output, ENT_COMPAT, 'UTF-8', false);
                    $thumb1             = htmlentities($pr_thumb_img1, ENT_COMPAT, 'UTF-8', false);
                    $thumb2             = htmlentities($pr_lar_img1, ENT_COMPAT, 'UTF-8', false);
                    $processed          = 0;
                    $process_error      = 0;

                    //Convert command to ogg with ffmpeg software
                    $convert_cmd1 = "ffmpeg2theora ".$video." -x".$width1." -y ".$height1." -o ".$temp_uploads.$pr_rand."_1.ogg 2>&1";
                    $convert_cmd2 = "ffmpeg2theora ".$video." -x".$width2." -y ".$height2." -o ".$temp_uploads.$pr_rand."_2.ogg 2>&1";
                    $convert_cmd3 = "ffmpeg2theora ".$video." -x".$width3." -y ".$height3." -o ".$temp_uploads.$pr_rand."_3.ogg 2>&1";

                    //Convert by executing linux task with php 
                    $convert_log1 = shell_exec($convert_cmd1);
                    $convert_log2 = shell_exec($convert_cmd2);
                    $convert_log3 = shell_exec($convert_cmd3);
                    //Copy to videos directory and Delete Original old file from temp
                    if(copy($new_Video_File, $video_upload_url.$pr_rand.".".$video_extension)){
                            unlink($new_Video_File);
                    }
                    //Delete the mp4 file generated from original file
                    unlink($output);
                        // copy converted original videos to /uploads/videos/ directory, store the link location for xml and delete old one
                        if(copy($temp_uploads.$pr_rand."_1.ogg", $video_upload_url.$pr_rand."_1.ogg")){
                            unlink($temp_uploads.$pr_rand."_1.ogg");
                            $xmlvideoUrl_1 = $video_upload_url.$pr_rand."_1.ogg";
                        }
                        if(copy($temp_uploads.$pr_rand."_2.ogg", $video_upload_url.$pr_rand."_2.ogg")){
                            unlink($temp_uploads.$pr_rand."_2.ogg");
                            $xmlvideoUrl_2 = $video_upload_url.$pr_rand."_2.ogg";
                        }
                        if(copy($temp_uploads.$pr_rand."_3.ogg", $video_upload_url.$pr_rand."_3.ogg")){
                            unlink($temp_uploads.$pr_rand."_3.ogg");
                            $xmlvideoUrl_3 = $video_upload_url.$pr_rand."_3.ogg";
                        }
                }
            }
        }   
         // Assigning user data to variables
        
         $pr_name = $_POST['pr_name'];
         $pr_email = $_POST['pr_email'];
         $pr_video_name = $_POST['pr_video_name'];
         $pr_desc = $_POST['pr_desc'];
         $pr_loc = $_POST['pr_loc'];
         $pr_labels = $_POST['pr_labels'];
         $pr_video_url = $pr_video_url1;
         $pr_thumb_url = $pr_thumb_img1;
         $pr_lar_url = $pr_lar_img1;
         $pr_aud_url = $pr_aud_file1; 

         //LInks for xml for ogg files
        $pr_ogg1_videourl = $url.$xmlvideoUrl_1;
        $pr_ogg2_videourl = $url.$xmlvideoUrl_2;
        $pr_ogg3_videourl = $url.$xmlvideoUrl_3;

        $pr_name = htmlentities($pr_name, ENT_COMPAT, 'UTF-8', false);
        $pr_email = htmlentities($pr_email, ENT_COMPAT, 'UTF-8', false);
        $pr_video_name = htmlentities($pr_video_name, ENT_COMPAT, 'UTF-8', false);
        $pr_desc = htmlentities($pr_desc, ENT_COMPAT, 'UTF-8', false);
        $pr_loc = htmlentities($pr_loc, ENT_COMPAT, 'UTF-8', false);
        $pr_labels = htmlentities($pr_labels, ENT_COMPAT, 'UTF-8', false);
        $pr_video_url = htmlentities($pr_video_url, ENT_COMPAT, 'UTF-8', false);  
        $pr_thumb_url = htmlentities($pr_thumb_url, ENT_COMPAT, 'UTF-8', false);
        $pr_lar_url = htmlentities($pr_lar_url, ENT_COMPAT, 'UTF-8', false);
        $pr_aud_url = htmlentities($pr_aud_url, ENT_COMPAT, 'UTF-8', false); 
        
        //$pr_video_url = $url."uploads/temp/".$prvideoname;
        $pr_thumb_url = $url.$pr_thumb_url;
        $pr_lar_url = $url.$pr_lar_url;
        $pr_aud_url = $url.$pr_aud_url;
        
        /* Start code for, If user didnt add any contetnt, we pass empty space to xml variables*/        
        if($pr_name == ""){
           $pr_name = "--";
         }
         if($pr_email == ""){
           $pr_email = "--";
         }
         if($pr_video_name == ""){
           $pr_video_name = "--";
         }
         if($pr_video_name_2 == ""){
           $pr_video_name_2 = "--";
         }
         if($pr_video_name_3 == ""){
           $pr_video_name_3 = "--";
         }
         if($pr_desc == ""){
           $pr_desc = "--";
         }
         if($_FILES['pr_video']['name'] == ""){
           $pr_video_url = "--";
         }
        if($pr_loc == ""){
           $pr_loc = "--";
         }
         if($pr_labels == ""){
           $pr_labels = "--";
         }
         
         if($_FILES['pr_thumb_img']['name'] == ""){
           $pr_thumb_url = "--";
         }
         if($_FILES['pr_lar_img']['name'] == ""){
           $pr_lar_url = "--";
         }
         if($_FILES['pr_aud_file']['name'] == ""){
           $pr_aud_url = "--";
         }
      /* End code*/

      /* XML reading line*/
      $xml = simplexml_load_file('data.xml');

      /* appending data to xml */
      $prdata = $xml->addChild('prdata');
      $prdata->addChild('pruploader_name', $pr_name);
      $prdata->addChild('pruploader_email', $pr_email);
      $prdata->addChild('prvideoname', $pr_video_name);

      //Adding three converted Videos URL
      $prdata->addChild('pr_ogg1', $pr_ogg1_videourl);
      $prdata->addChild('pr_ogg2', $pr_ogg2_videourl);
      $prdata->addChild('pr_ogg3', $pr_ogg3_videourl);
      $prdata->addChild('prvideourl', $url.$video_upload_url.$pr_rand.".".$video_extension);
      
      $prdata->addChild('prdesc', $pr_desc);
      $prdata->addChild('prloc', $pr_loc);
      $prdata->addChild('pr_labels', $pr_labels);
      $prdata->addChild('pr_thumb_url', $pr_thumb_url);
      $prdata->addChild('pr_large_url', $pr_lar_url);
      $prdata->addChild('pr_audio_url', $extracted_audio);
      // Uploaded Multiple Audios
      if($pr_aud_url!=="--"){
          foreach($audio_names as $audio)
              $prdata->addChild('pr_audio_url', $url.$audio);
      }else{
              $prdata->addChild('pr_audio_url', $pr_aud_url);
      }

      /* saving data to xml file*/
      $doc = new DOMDocument('1.0');
      $doc->formatOutput = true;
      $doc->preserveWhiteSpace = true;
      $doc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
      $doc->save('data.xml');
?>
<script type="text/javascript">
 location.href="thankyou.php"; // redirect after success
</script>
<?php
} 

/* end code*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>From</title>
<!-- stylesheet path-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<!-- stylesheet path-->
</head>

<body>
<div class="contaner_outer">
  <div class="conatiner">
  <h1>Form</h1>
  <!-- Start HTML Form code-->
  <form name="xml_json" id="xml_json" method="post" enctype="multipart/form-data" >
  <div>
    <div class="main_form">
      <div class="l_name">Uploader Name<strong>:</strong></div>
      <div class="l_input">
        <input type="text" name="pr_name" id="pr_name" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Uploader Email<strong>:</strong></div>
      <div class="l_input"> 
        <input type="text" name="pr_email" id="pr_email" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Video Name<strong>:</strong></div>
      <div class="l_input"> 
        <input type="text" name="pr_video_name" id="pr_video_name" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Attach video to upload any size<strong>:</strong></div>
      <div class="l_input"> 
        <input type="file" value="Browse" name="pr_video" id="pr_video" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Description<strong>:</strong></div>
      <div class="l_input"> 
        <textarea name="pr_desc" id="pr_desc"></textarea>
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Location<strong>:</strong></div>
      <div class="l_input"> 
        <input type="text" name="pr_loc" id="pr_loc" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Labels<strong>:</strong></div>
      <div class="l_input"> 
        <input type="text" name="pr_labels" id="pr_labels" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Attach Thumbnail Image<strong>:</strong></div>
      <div class="l_input"> 
        <input type="file" value="Browse" name="pr_thumb_img" id="pr_thumb_img" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Attach Large Image<strong>:</strong></div>
      <div class="l_input"> 
        <input type="file" value="Browse" name="pr_lar_img" id="pr_lar_img" />
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">Attach Audio File<strong>:</strong></div>
      <div class="l_input"> 
        <div id="dvFile"><input type="file" value="Browse" name="pr_aud_file[]" id="pr_aud_file" /></div>
        <a href="javascript:_add_more();" title="Add more"><img src="plus_icon.gif" border="0"></a>
      </div>
    </div>
    <div class="main_form">
      <div class="l_name">&nbsp;</div>
      <div class="l_input">
        <input type="submit" value="Submit" name="submit" id="submit" />
      </div>
    </div>
  </div>
  </form>
  <!-- End HTML Form code-->
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script language="javascript">
<!--
  function _add_more() {
    var txt = "<input type=\"file\" name=\"pr_aud_file[]\">";
    $("#dvFile").append(txt);
  }

  function validate(f){
    var chkFlg = false;
    for(var i=0; i < f.length; i++) {
      if(f.elements[i].type=="file" && f.elements[i].value != "") {
        chkFlg = true;
      }
    }

    if(!chkFlg) {
      alert('Please browse/choose at least one file');
      return false;
    }
    f.pgaction.value='upload';
    return true;
  }
//-->
</script>

</body>
</html>