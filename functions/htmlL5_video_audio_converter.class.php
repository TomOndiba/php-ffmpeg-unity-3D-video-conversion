<?php
class HTML5VideoAudio {

	function TypeofFile($uploaded_file) {
		$file_type = $uploaded_file['type'];

		if(substr_count($file_type,'/ogg')>0) {
			return 'ogg';
		} elseif(substr_count($file_type,'video/')>0) {
			return 'video';
		} elseif(substr_count($file_type,'audio/')>0) {
			return 'audio';
		} else {
			return 'forbidden';
		}
	}

	function Converter($uploaded_file,$max_size=5120) { // $max_size in KB, use 0 for unlimitted max. size
		$tmp_file = $uploaded_file['tmp_name'];

		if(empty($tmp_file)) {
			return 'You haven\'t upload any file. Please try again';
		} else {
			set_time_limit(0);

			$file_name = $uploaded_file['name'];
			$file_type = $uploaded_file['type'];
			$type = $this->TypeofFile($uploaded_file);

			if($type=='ogg') {
				unlink($tmp_file);
				return '<b>'.$file_name.'</b> is '.$file_type.' file. You don\'t need to convert it';
			} elseif($type=='forbidden') {
				unlink($tmp_file);
				return '<b>'.$file_name.'</b> is not an audio or a video file';
			} else {			
				$size = round($uploaded_file['size']/1024,2);
				if($size<1024) {
					$file_size = $size.' KB';
				} else {
					$file_size = round($size/1024,2).' MB';
				}

				if($max_size!=0 && $size>$max_size) {
					if($max_size<1024) {
						$max_size = $max_size.' KB';
					} else {
						$max_size = round($max_size/1024,2).' MB';
					}
					unlink($tmp_file);
					return '<b>'.$file_name.' ['.$file_size.']</b> is too large to be converted. Allowed max. size is '.$max_size;
				} else {
					if(!file_exists('ffmpeg2theora.exe')) {
						unlink($tmp_file);
						return '<b>ffmpeg2theora.exe</b> does not exist. If there is a similar file with a different name, please rename it to <b>ffmpeg2theora.exe</b>. Please check and try again';
					} else {
						$source_file = str_replace(' ','-',$file_name);
						move_uploaded_file($tmp_file,$source_file);

						$file_ext = strrchr($file_name,'.');
						$file_basename = str_replace($file_ext,'',$file_name);
						$file_basename1 = str_replace($file_ext,'',$source_file);

						if($type=='video') {
							$ext = '.ogv';
						}
						if($type=='audio') {
							$ext = '.oga';
						}
						$new_file = $file_basename.$ext;
						$new_file1 = $file_basename1.$ext;

						$execute = shell_exec('ffmpeg2theora -o '.$new_file1.' '.$source_file);
						if($execute) {
							rename($new_file1,$new_file);
							$new_size = round(filesize($new_file)/1024,2);
							if($new_size<1024) {
								$new_file_size = $new_size.' KB';
							} else {
								$new_file_size = round($new_size/1024,2).' MB';
							}

							$dir = 'output';
							if(!is_dir($dir)) {
								mkdir($dir,0777);
							}

							$output_file = $dir.'/'.$new_file;
							$output_name = $new_file;

							if(file_exists($output_file)) {
								$finished = false;
								$i = 0;
								while(!$finished) {
									$i++;
									$output_name = $file_basename.' ('.$i.')'.$ext;
									$output_file = $dir.'/'.$output_name;
									if(!file_exists($output_file)) {
										$finished = true;
									}
								}
							}

							copy($new_file,$output_file);
							unlink($source_file);
							unlink($new_file);
							return '<b>'.$file_name.' ['.$file_size.']</b> is successfull converted to <b>'.$output_name.' ['.$new_file_size.']</b><br />
							click <a href="output/'.$new_file.'" target="_blank">here</a> to download it';
						} else {
							unlink($source_file);
							return '<b>[Error Operation]</b>. Please try again';
						}
					}
				}
			}
			flush();
		}
	}

}

?>