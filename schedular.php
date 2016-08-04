<?php
		$fp = fopen("check.txt", "w");
		fwrite($fp, $argv[1]);

		//require_once('./functions/ffmpeg.class.php');
		//$FFmpeg = new FFmpeg;
        //$FFmpeg->input( '1.mp4' )->output( '1.3gp' )->ready();

		//echo shell_exec("ls");
		//echo shell_exec("ffmpeg -i 1.mkv -vcodec copy -acodec copy 1.mp4");
		//echo shell_exec("ffmpeg -i 1.mkv -f ogg -s 500x400 -strict -2  1.ogg");
		//echo shell_exec("ffmpeg -i 1.mkv -s 2556x1440 -vcodec libx264 -strict -2  1.mp4");
		//echo shell_exec("ffmpeg -i 1.mkv -s 1920x1080 -vcodec libx264 -strict -2  1.mp4");
		//echo shell_exec("ffmpeg -i 1.mkv -s 1334x750  -vcodec libx264 -strict -2  1.mp4");
		//echo shell_exec("ffmpeg -i 1.mp4 -b 1024k -acodec vorbis -vcodec libtheora -f ogg 1.ogv");
		//echo shell_exec("ffmpeg2theora -o 1.ogv 1.mov");
		//shell_exec('php measurePerformance.php 47 844 email@yahoo.com > /dev/null 2>/dev/null &');
		//ffmpeg2theora -1 1.ogg
		//echo shell_exec("ffmpeg -i 1.mp4");
		//ffmpeg2theora 1.mkv -o out_file.ogg
		//ffmpeg2theora 1.mkv -o 1.mp4 && php /var/www/html/uploads.prospervr.com/convert.php >/dev/null 1>/dev/null 2>/dev/null &
		//ffmpeg2theora 1.mkv -o 1.mp4 && php /var/www/html/uploads.prospervr.com/convert.php > /dev/null 2>/dev/null &

		//echo shell_exec("ffmpeg -i video.mp4 -i audio.m4a -c:v copy -c:a copy output.mp4");
        //$FFmpeg = new FFmpeg;
        //$FFmpeg->input( '/var/www/html/uploads.prospervr.com/1.mkv' )->output( '/var/www/html/uploads.prospervr.com/1.3gp' )->ready();
       //exec("ffmpeg -i video.avi -ar 22050 -ab 32 -f flv -s 320x240 video.flv");
?>