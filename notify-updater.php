<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
 /*
//security 
$headers = apache_request_headers();
$request_headers = getallheaders();
if($request_headers['Authorization'] != "L2HRYaQtgUru") {
		header("Location: https://play.google.com/store/apps/details?id=com.xiaomiui.downloader");
		exit();
} */

//cache file create
$version = $_GET['v'];
$codename = $_GET['codename'];
$region = $_GET['region'];
$tur = $_GET['tur'];
$cache = $_GET["cache"];

if ($tur == "alpha") {
	$cachefile = 'cache/cachedU-'.$codename.$version.'.cache';
	$cachetime = 1440 * 60; // 1 day
} else {
	$cachefile = 'cache/cachedU-'.$version.$codename.'.cache';
	$cachetime = 10080 * 60; // 1 week
}


// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile) && $cache != "force") {
    exit;
}

// $model = str_replace('%20', '');
echo $codename;
echo $version;

function sendnotifi($version, $codename, $region, $tur){
	if ($tur == 'alpha') {
		$content = array(
			"en" => "$version daily beta released for your device!"
		);
	} else {
		$content = array(
			"en" => "$version released for your device!"
		);
	}

	
	$headings = array(
		"en" => "Update available!"
	);
	
	$buttons = array();
    array_push($buttons, array(
        "id" => "download",
        "text" => "Download Now"
    ));
	
	if ($tur == "alpha") {
		$fields = array(
			'app_id' => "4a05f9f8-5d5a-47d1-9af1-8de96ddc39fe",
			'filters' => array(
				array("field" => "tag", "key" => 'codename', "relation" => "=", "value" => trim($codename)),
				array("operator" => "AND"),
				array("field" => "tag", "key" => "miuiver_beta", "relation" => "!=", "value" => trim($version)),
			),
			'contents' => $content,
			'headings' => $headings,
			'buttons' => $buttons
		);
	} else {
		$fields = array(
			'app_id' => "4a05f9f8-5d5a-47d1-9af1-8de96ddc39fe",
			'filters' => array(
				array("field" => "tag", "key" => "codename", "relation" => "=", "value" => trim($codename)),
				array("operator" => "AND"),
				array("field" => "tag", "key" => "miuiver", "relation" => "!=", "value" => trim($version)),
				array("operator" => "AND"),
				array("field" => "tag", "key" => "miuiver", "relation" => "!=", "value" => null),
				array("operator" => "AND"),
				array("field" => "tag", "key" => "miuiver", "relation" => "!=", "value" => ''),
				array("field" => "tag", "key" => "region", "relation" => "=", "value" => trim($region))
			),
			'contents' => $content,
			'headings' => $headings,
			'buttons' => $buttons
		);
	}
	


	$fields = json_encode($fields);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
											   'Authorization: Basic MTcxZDQ3MDktZGViNC00ZWI3LWEyNjYtODExMmU5ZGYzOTAw'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$response = curl_exec($ch);
	curl_close($ch);
	
	return $response;
}

//stable update
if ($version != "null" && $version != null) {
	sendnotifi($version, $codename, $region, $tur);
}


//cache file create
$cached = fopen($cachefile, 'w');
fwrite($cached, '');
fclose($cached);
?>