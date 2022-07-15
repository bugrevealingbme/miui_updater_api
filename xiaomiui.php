<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

//security
$headers = apache_request_headers();
$request_headers = getallheaders();
if($request_headers['Authorization'] != "L2HRYaQtgU") {
		header("Location: https://play.google.com/store/apps/details?id=com.xiaomiui.downloader");
		exit();
} 

$tur = $_GET['t'];
$tel = $_GET['code'];

if ($tel == 'secret' || $tel == 'maltose') {
	$tel = 'rosemary';
} else if ($tel == 'lemon' || $tel == 'pomelo') {
	$tel = 'lime';
} else if ($tel == 'mars') {
	$tel = 'star';
} else if ($tel == 'eos') {
	$tel = 'selene';
} else if ($tel == 'karna') {
	$tel = 'surya';
} else if ($tel == 'bhima') {
	$tel = 'vayu';
} else if ($tel == 'sunny') {
	$tel = 'mojito';
} else if ($tel == 'amber') {
	$tel = 'agate';
} else if ($tel == 'merlinnfc') {
	$tel = 'merlin';
} else if ($tel == 'galahad') {
	$tel = 'lancelot';
} else if ($tel == 'gauguininpro' || $tel == 'gauguinin') {
	$tel = 'gauguin';
}

function curlknk($url) {
	$ch = curl_init($url) or die("curl issue");
	$curl_options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_HEADER 		=> false,
		CURLOPT_FOLLOWLOCATION	=> false,
		CURLOPT_ENCODING	=> "",
		CURLOPT_AUTOREFERER 	=> true,
		CURLOPT_CONNECTTIMEOUT 	=> 7,
		CURLOPT_TIMEOUT 	=> 7,
		CURLOPT_MAXREDIRS 	=> 3,
		CURLOPT_SSL_VERIFYHOST	=> false,
		CURLOPT_USERAGENT	=> "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13"
	);
	curl_setopt_array($ch, $curl_options);
	$curlcontent = curl_exec( $ch );
	curl_close( $ch );

	return $curlcontent;
}


//model ici
if ($tur == "stable") {
	include('top-cache.php');
	$results = curlknk("https://mirom.ezbox.idv.tw/en/phone/".$tel."/");
	preg_match_all('@<dt class="col-4 col-sm-2.*?>(.*?)</dt>@si',$results,$baslik);
	preg_match_all('@<dd class="col-8 col-sm-10.*?">(.*?)</dd>@si',$results,$veri);
	preg_match_all('@<h4 class=".*?">(.*?)</h4>@si',$results,$romtur);
	$j=0;
	$i=0;
	echo '{"regions": [';
	$romtur[1] = array_unique($romtur[1]);
	while ($romtur[1][$j] != "") {
		// if(strstr($romtur[1][$j], "Developer")) {$j++;}
		$romtur[1][$j] = preg_replace('/.*? &ndash; /','',$romtur[1][$j]);
		
		
		echo '{"region": "'.$romtur[1][$j].'",';
		$dur = 0;
		$yazma=false;
		$evt = "";
		while ($baslik[1][$i] != "") {
			$k = $i;
			$k++;
			$hist = "";
			$baslik[1][$i] = str_replace(" ", "_", $baslik[1][$i]);
			$baslik[1][$i] = strtolower($baslik[1][$i]);
			$baslik[1][$k] = str_replace(" ", "_", $baslik[1][$k]);
			$baslik[1][$k] = strtolower($baslik[1][$k]);			
			if ($baslik[1][$i] == "lstest" || $baslik[1][$i] == "latest") {$dur++; if ($dur == 2) {break;}}
			if ($baslik[1][$i] == "lstest" || $baslik[1][$i] == "latest") {preg_match_all('@<a class=".*?" style=".*?" href="(.*?)"@si',$veri[1][$i],$hist); $veri[1][$i] = preg_replace('/<a .*/','',$veri[1][$i]);}

			if ($hist[1][0] != "") {echo '"history": "'.rtrim($hist[1][0],"/").'",';}
			if ($baslik[1][$i] == "changelog") {$veri[1][$i] = preg_replace('/<a .*/','',trim($veri[1][$i]));$veri[1][$i] = preg_replace('/<div .*>/','',trim($veri[1][$i]));$veri[1][$i] = str_replace("</div>", "", $veri[1][$i]);$veri[1][$i] = str_replace("\n", '', trim($veri[1][$i]));$veri[1][$i] = str_replace("\t", '', trim($veri[1][$i]));$veri[1][$i] = str_replace("\r", '', trim($veri[1][$i]));$veri[1][$i] = preg_replace('/ class=".*?"/i','',trim($veri[1][$i]));$veri[1][$i] = str_replace("\"", "", $veri[1][$i]);}
			
			
			if ($baslik[1][$i] == "update_at") {
				preg_match_all('@datetime="(.*?)">@si',trim($veri[1][$i]),$tarihh); $veri[1][$i] = $tarihh[1][0];
				$veri[1][$i] = date("Y-m-d", strtotime(trim($veri[1][$i])));
			}
			
			if ($baslik[1][$i] == "link") {
				preg_match_all('@href="(.*?)">Download@si',trim($veri[1][$i]),$link);$veri[1][$i] = $link[1][0];
			}

			//ota link duzeltme
			if ($baslik[1][$i] == "ota") {$durknk = 0; $evt = "ota"; $yazma=false;}
			if ($baslik[1][$k] == "lstest" && $evt == "ota" || $baslik[1][$k] == "" && $evt == "ota") {$evt = ""; echo '"ota_'.$baslik[1][$i].'": "'.trim($veri[1][$i]).'"'; $durknk++; $yazma=true;}
			else if ($evt == "ota" && $baslik[1][$i] != "fastboot" && $durknk > 0) {echo '"ota_'.$baslik[1][$i].'": "'.trim($veri[1][$i]).'",'; $yazma=true;}
			else if ($durknk == 0 && $evt == "ota") {$durknk++;}
			
			//fastboot link duzeltme
			if ($baslik[1][$i] == "fastboot") {$durknk = 0; $evt = "fast"; $yazma=false;}
			if ($baslik[1][$k] == "lstest" && $evt == "fast" || $baslik[1][$k] == "" && $evt == "fast") {$evt = ""; echo '"fastboot_'.$baslik[1][$i].'": "'.trim($veri[1][$i]).'"'; $durknk++; $yazma=true;}
			else if ($evt == "fast" && $baslik[1][$k] != "lstest" && $durknk > 0) {echo '"fastboot_'.$baslik[1][$i].'": "'.trim($veri[1][$i]).'",'; $durknk++; $yazma=true;}
			else if ($durknk == 0 && $evt == "fast") {$durknk++;}
			
			if ($baslik[1][$k] == "lstest" && $yazma!=true || $baslik[1][$k] == "" && $yazma!=true) {echo '"'.$baslik[1][$i].'": "'.trim($veri[1][$i]).'"';}
			else if ($yazma!=true) {echo '"'.$baslik[1][$i].'": "'.trim($veri[1][$i]).'",';}
			$i++;
		}
		$j++;
		$a = $j;
		if ($romtur[1][$a++] != "") {echo "},";}
		else {echo "}";}
	}
	echo "]}";
	include('bottom-cache.php');
}
//model ici bitis

//alpha surumler
if ($tur == "alpha") {
	include('top-cache.php');
	$results = curlknk("https://t.me/fpkdgpkgojhdhhr?q=%23".$tel."");
	preg_match_all('@<div class="tgme_widget_message_bubble">(.*?)</a></span>@si',$results,$hasveri);
	echo "[";
	$i=0;
	while (true) {
		preg_match_all('@<div class="tgme_widget_message_text(.*?)</div>@si',$hasveri[1][$i],$yaziveri);
		$yaziveri[1][0] = preg_replace("#js-message_text\"#s", '', $yaziveri[1][0]);
		$yaziveri[1][0] = preg_replace("# dir=\"auto\">#s", '', $yaziveri[1][0]);
		
		preg_match_all('@<a href="(.*?)"@si',trim($yaziveri[1][0]),$link);
		$yaziveri[1][0] = preg_replace("#<a href=\".*?\" target=\"_blank\" .*?sil\sil>.*?</a>#s", '', $yaziveri[1][0]);
		$link[1][0] = str_replace("?q=%23", "",$link[1][0]);

		if ($link[1][1] != "") {
			preg_match_all('@miui.com/(.*?)/@si',trim($link[1][1]),$vers);
			preg_match_all('@weekly@s',trim($link[1][2]),$versfrom);
			echo "{";
			echo '"codename": "'.trim($link[1][0]).'",';
			echo '"link": "'.trim($link[1][1]).'",';
			echo '"version": "'.trim($vers[1][0]).'",';
			echo '"weekly": "'.trim($versfrom[0][0]).'"';
			echo "},";
		}

		if ($yaziveri[1][0] == NULL && $yaziveri[1][0] == "") {break;}
		
		
		$i++;
	}
	echo "{}]";
	include('bottom-cache.php');
}
//alphasurumlerbitis


if ($tur == "miui13-ec") {
	$phones = array('atom', 'bomb', 'umi', 'merlin', 'cmi', 'lmi', 'cas', 'tucana', 'curtana', 'excalibur', 'joyeuse', 'toco', 'vangogh', 'phoenix', 'picasso', 'willow', 'ginkgo', 'crux', 'raphaels', 'begonia', 'pyxis', 'vela', 'raphael', 'davinci', 'cepheus', 'grus', 'monet', 'picasso_48m', 'cezanne', 'apollo', 'gauguin', 'lime', 'cannon', 'lancelot', 'cattail', 'dandelion', 'angelica', 'shiva', 'citrus', 'angelicain', 'angelican', 'gram', 'venus', 'cannong', 'surya', 'haydn', 'haydnin', 'alioth', 'aliothin', 'star', 'mars', 'camellia', 'camellian', 'sweet', 'sweetin', 'rosemary', 'mojito', 'ares', 'aresin', 'chopin', 'thyme', 'renoir', 'courbet', 'cetus', 'biloba', 'selene', 'enuma', 'elish', 'nabu', 'odin', 'vili', 'agate', 'lisa', 'vayu', 'evergo', 'evergreen', 'selenes', 'mona', 'pissarro', 'pissarropro','secret','maltose','lemon','pomelo','mars','eos','karna', 'bhima','sunny','amber','spes','spesn','fleur','miel','viva','vida','veux','peux','psyche','zeus','cupid','ingres','munch','rubens','matisse','loki','thor','zizhan','taoyao','zijin','selenes', 'evergreen', 'evergo', 'merlinnfc', 'galahad');
	
	echo json_encode($phones);
}

if ($tur == "and13-ec") {
	$phones = array('venus', 'haydn', 'haydnin', 'alioth', 'aliothin', 'star', 'mars', 'camellia', 'camellian', 'sweet', 'sweetin', 'rosemary', 'mojito', 'ares', 'aresin', 'chopin', 'thyme', 'renoir', 'courbet', 'cetus', 'biloba', 'selene', 'enuma', 'elish', 'nabu', 'odin', 'vili', 'agate', 'lisa', 'vayu', 'mona', 'pissarro', 'pissarropro', 'secret','maltose','lemon','pomelo','mars','eos', 'bhima','sunny','amber','spes','spesn','fleur','miel','viva','vida','veux','peux','psyche','zeus','cupid','ingres','munch','rubens','matisse','loki','thor','zizhan','taoyao','zijin','selenes', 'evergreen', 'evergo');
	  
	echo json_encode($phones);
}

?>