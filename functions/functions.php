<?php 
	function validate_Medias($video_extension, $thumb1_extension, $largeimg_extension, $audio_extensions){
			return true;
	}

    function saveMedia_info($uploader, $uploader_email, $video_name, $video_description, $location, $labels, $audios, $video, $thumb1, $thumb2, $processed, $process_error){
		include("./functions/config.php");
		$temp_audios = '';
		if(count($audios)>0){
			foreach($audios as $audio){
					if($temp_audios  == '')
					$temp_audios  = $audio;
				else
					$temp_audios .= ','.$audio; 
			}
		}

		$uploader = ($uploader);
		$uploader_email =  ($uploader_email);
		$video_name = ($video_name);
		$video_description = ($video_description);
		$location = ($location);
		$labels = ($labels);
		$temp_audios = ($temp_audios);
		$video = ($video);
		$thumb1 = ($thumb1);
		$thumb2 = ($thumb2);
		$processed = ($processed);
		$process_error = ($process_error);

		$conn   = mysqli_connect($host, $user, $pass, $db);
		$sql = "INSERT INTO `medias`(uploader, uploader_email, video_name, video_description, location, labels, audios, video, thumb1, thumb2, processed, process_error)
				VALUES('$uploader', '$uploader_email', '$video_name', '$video_description', '$location', '$labels', '$temp_audios', '$video', '$thumb1', '$thumb2', $processed, $process_error)";
		mysqli_query($conn, $sql);
        mysqli_close($conn);
	}

	
	function convert2Mp4($source, $output){
		return shell_exec("ffmpeg -i ".$source." -vcodec copy -acodec copy ".$output);
	}

	function lowerResolution($width, $height){
	         $arr = array(  array('160','120'),
	                        array('240','160'),
	                        array('240','320'),
	                        array('240','400'),
	                        array('240','427'),
	                        array('267','356'),
	                        array('320','200'),
	                        array('320','240'),
	                        array('320','401'),
	                        array('320','406'),
	                        array('320','415'),
	                        array('320','427'),
	                        array('320','480'),
	                        array('320','496'),
	                        array('320','505'),
	                        array('320','533'),
	                        array('320','534'),
	                        array('320','544'),
	                        array('320','545'),
	                        array('320','568'),
	                        array('320','569'),
	                        array('342','570'),
	                        array('345','691'),
	                        array('360','480'),
	                        array('360','559'),
	                        array('360','598'),
	                        array('360','640'),
	                        array('375','667'),
	                        array('384','598'),
	                        array('384','640'),
	                        array('400','640'),
	                        array('400','683'),
	                        array('414','736'),
	                        array('425','974'),
	                        array('432','240'),
	                        array('480','320'),
	                        array('480','800'),
	                        array('480','813'),
	                        array('480','854'),
	                        array('496','1024'),
	                        array('533','801'),
	                        array('540','960'),
	                        array('600','799'),
	                        array('600','960'),
	                        array('600','961'),
	                        array('600','963'),
	                        array('600','1024'),
	                        array('601','962'),
	                        array('602','961'),
	                        array('602','962'),
	                        array('603','966'),
	                        array('640','200'),
	                        array('640','350'),
	                        array('640','400'),
	                        array('640','480'),
	                        array('640','1067'),
	                        array('648','1280'),
	                        array('720','348'),
	                        array('720','1280'),
	                        array('731','1170'),
	                        array('768','1024'),
	                        array('768','1280'),
	                        array('768','1366'),
	                        array('800','480'),
	                        array('800','600'),
	                        array('800','637'),
	                        array('800','1067'),
	                        array('800','1128'),
	                        array('800','1220'),
	                        array('800','1280'),
	                        array('854','480'),
	                        array('900','1600'),
	                        array('1024','576'),
	                        array('1024','768'),
	                        array('1152','864'),
	                        array('1280','720'),
	                        array('1280','768'),
	                        array('1280','800'),
	                        array('1280','1024'),
	                        array('1366','768'),
	                        array('1366','900'),
	                        array('1400','1050'),
	                        array('1440','900'),
	                        array('1600','900'),
	                        array('1600','1200'),
	                        array('1680','945'),
	                        array('1680','1050'),
	                        array('1920','1080'),
	                        array('1920','1200'),
	                        array('2048','1152'),
	                        array('2048','1536'),
	                        array('2560','1440'),
	                        array('2560','1600'),
	                        array('2560','2048'),
	                        array('3200','2048'),
	                        array('3200','2400'),
	                        array('3840','2160'),
	                        array('4096','3072'),
	                        array('5120','2880'),
	                        array('5120','3200'),
	                        array('5120','4096'),
	                        array('5760','3240'),
	                        array('6400','4096'),
	                        array('6400','4800'),
	                        array('7680','4320'),
	                        array('7680','4800'),
	                        array('15360','8640')
	                );
	        $storedWidth = '';
	        $storedHeight = '';
	        $lowerResolutionArr = array();
	        $width_greater = false;
	        $height_greater = false;

	        if($width > $height){
	        	$width_greater = true;
	        }
	        else if ($width < $height){
	        	$height_greater = true;
	        }else{
	        	$width_greater = true;
	        }

	        foreach ($arr as $a) {
	        	if($width_greater == true)
	              {
	              	if($a[0] >= $width && $a[1] >= $height && $a[0] >= $a[1])
	                	break;
	            	else if($a[0] >= $a[1]){
	            		$storedWidth  = $a[0];
	                	$storedHeight = $a[1];
	                	}
	               }

	        	if($height_greater == true)
	              {
	              	if($a[0] >= $width && $a[1] >= $height && $a[1] >= $a[0])
	                	break;
	            	else if($a[1] >= $a[0]){
	            		$storedWidth  = $a[0];
	                	$storedHeight = $a[1];
	                	}
	               } 
	        }

	        if($storedWidth!= '' && $storedHeight!=''){
	                $lowerResolutionArr[0] = $storedWidth;
	                $lowerResolutionArr[1] = $storedHeight;
	        }else{
	                $lowerResolutionArr[0] = $width;
	                $lowerResolutionArr[1] = $height;
	        }
	        return $lowerResolutionArr;
	}
?>