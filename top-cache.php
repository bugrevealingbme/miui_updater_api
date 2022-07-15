<?php
$url = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $url);
$file = $break[count($break) - 1];
$tur = $_GET["t"];
$hh = $_GET["h"];
$hh = str_replace('/', '', $hh);
$cache = $_GET["cache"];

$cachefile = 'cache/cached-'.$_GET["code"].$tur.$hh.'.cache';
$cachetime = 1800;

function EscapeNonASCII($string) {
		$hexbytes = strtoupper(bin2hex($string));
		$i = 0;
		while ($i < strlen($hexbytes))
		{
			$hexpair = substr($hexbytes, $i, 2);
			$decimal = hexdec($hexpair);
			if ($decimal < 32 || $decimal > 126)
			{
				$top = substr($hexbytes, 0, $i);
				$escaped = EscapeHex($hexpair);
				$bottom = substr($hexbytes, $i + 2);
				$hexbytes = $top . $escaped . $bottom;
				$i += 8;
			}
			$i += 2;
		}
		$string = hex2bin($hexbytes);
		return $string;
}

function EscapeHex($string) {
	$x = "5C5C78";
	$topnibble = bin2hex($string[0]);
	$bottomnibble = bin2hex($string[1]); 
	$escaped = $x . $topnibble . $bottomnibble; 
	return $escaped;
}

function UnescapeNonASCII($string) {
	$stringtohex = bin2hex($string);
	$stringtohex = preg_replace_callback('/5c5c78([a-fA-F0-9]{4})/', function ($m) { 
		return hex2bin($m[1]);
	}, $stringtohex);
	return hex2bin(strtoupper($stringtohex));
}

// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile) && $cache != "force") {
	$data = file_get_contents($cachefile);
	$string = EscapeNonASCII($data);
	$string = str_replace( '\\\xEF\\\xBB\\\xBF', '', $string );
	$string = UnescapeNonASCII($string);
    // readfile($cachefile);
	echo ($string);
    exit;
}
ob_start(); // Start the output buffer
?>