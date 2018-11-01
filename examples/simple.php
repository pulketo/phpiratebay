<?php
	require "../src/phpiratebay.php";
	$pb = new phpiratebay('tpbproxyone.org');
	$pb -> searchFor("android");
	$pb -> searchInCategory("video");
	// fields; name,date,size,seeds,leeches
	// order: asc,desc
	$pb -> orderBy("leeches","desc");
	$out = $pb->getPBlinks();
	$theTorrentHREF = $out[0]['href'];
	$out2 = $pb->getTorrentDetails($theTorrentHREF);
	print_r($out2);