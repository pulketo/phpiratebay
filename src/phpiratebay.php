<?php

/////////////////////////
	class phpiratebay {
		private $debug = false;
		// 	
		public $list = array();
		public $theMirror = "";
		private $categories = array(
			"audio"=>100, 
			"video"=>200, 
			"applications"=>300,
			"applications/windows"=>301,
			"applications/android"=>306,
			"games"=>400, 
			"xxx"=>500, 
			"other"=>600 
		);
/***
<option value="0">All</option>
<optgroup label="Audio"><option value="101">Music</option><option value="102">Audio books</option><option value="103">Sound clips</option><option value="104">FLAC</option><option value="199">Other</option></optgroup><optgroup label="Video"><option value="201">Movies</option><option value="202">Movies DVDR</option><option value="203">Music videos</option><option value="204">Movie clips</option><option value="205">TV shows</option><option value="206">Handheld</option><option value="207">HD - Movies</option><option value="208">HD - TV shows</option><option value="209">3D</option><option value="299">Other</option></optgroup><optgroup label="Applications"><option value="301">Windows</option><option value="302">Mac</option><option value="303">UNIX</option><option value="304">Handheld</option><option value="305">IOS (iPad/iPhone)</option><option value="306">Android</option><option value="399">Other OS</option></optgroup><optgroup label="Games"><option value="401">PC</option><option value="402">Mac</option><option value="403">PSx</option><option value="404">XBOX360</option><option value="405">Wii</option><option value="406">Handheld</option><option value="407">IOS (iPad/iPhone)</option><option value="408">Android</option><option value="499">Other</option></optgroup><optgroup label="Porn"><option value="501">Movies</option><option value="502">Movies DVDR</option><option value="503">Pictures</option><option value="504">Games</option><option value="505">HD - Movies</option><option value="506">Movie clips</option><option value="599">Other</option></optgroup><optgroup label="Other"><option value="601">E-books</option><option value="602">Comics</option><option value="603">Pictures</option><option value="604">Covers</option><option value="605">Physibles</option><option value="699">Other</option></optgroup></select>
*/



		/*
		Proxy List:
			https://thepiratebay-proxylist.se/api/v1/proxies	 
		Query:
			https://alivbay.org/s/?q=apk&page=0&orderby=99
		opts: 
			category:
				0 : all
				101: 699
			page:
				0-99
			orderBy:
				1 name desc
				2 name asc
				3 date desc
				4 date asc
				5 size desc
				6 size asc
				7 seeds desc
				8 seeds asc
				9 leeches desc
				10 leeches asc
		*/		

		/***
		* 
		* 
		*
		*/


		public function __construct($searchTerm=array(), $category="all", $orderBy="dateDesc", $page=0, $mirror = null ){
			if ($mirror == null){
				$tmp = $this->getProxyList()->proxies;
				$this->theMirror = $tmp[RAND(0,sizeof($tmp))]->domain;
			}
		}

		private function dbg($what){
			if($this->debug)
				echo $what;
		}

		private function getProxyList(){
			// $o = file_get_contents("https://thepiratebay-proxylist.se/api/v1/proxies");
			$o = file_get_contents("../proxies.json");
			$this->list = json_decode($o);
			return $this->list;
		}
		
	}
	/////////////////////////

	// require __DIR__ ."/../vendor/autoload.php";
	//	$opts = new Commando\Command();
	//	$opts->option('m')->aka('man')->describedAs(MAN)->boolean()->defaultsTo(false);
	//	$opts->option('e')->aka('engine')->describedAs('--engine <...> or -e <google|duckgo|searxme|playstore> or  -e <g|d|s|p>')->defaultsTo("s");
	$pb = new phpiratebay();
	echo $pb->theMirror;
	// print_r($pb->list);
	