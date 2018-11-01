<?php
	// require __DIR__ ."/../vendor/autoload.php";
	//	$opts = new Commando\Command();
	//	$opts->option('m')->aka('man')->describedAs(MAN)->boolean()->defaultsTo(false);
	//	$opts->option('e')->aka('engine')->describedAs('--engine <...> or -e <google|duckgo|searxme|playstore> or  -e <g|d|s|p>')->defaultsTo("s");
	$pb = new phpiratebay('tpbproxyone.org');
	// $pb = new phpiratebay();
	// echo $pb->theMirror.PHP_EOL;
	// echo $pb->getCategories();
	// print_r($pb->list);
	//	$pb->setProxy("socks5://localhost:9999"); //not working ??
	//	$pb->setProxy("http://127.0.0.1:9999"); //not working ??
	//	$pb->searchFor("empire strikes back");
	$pb->searchFor("android");
	$pb->searchInCategory("video");
	$pb->orderBy("date","desc");
	$out = $pb->getPBlinks();
	// print_r($out);
	$out2 = $pb->getTorrentDetails($out['29']['href']);
	print_r($out2);