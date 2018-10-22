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
			"games"=>400, 
			"xxx"=>500, 
			"other"=>600 
		);

		/*
		Proxy List:
			https://thepiratebay-proxylist.se/api/v1/proxies	 
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
				$this->theMirror = $this->getProxyList()->proxies[0]->domain;
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
	