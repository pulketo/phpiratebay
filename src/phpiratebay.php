<?php

/////////////////////////
	class phpiratebay {
		private $debug = false;
		// 	
		public $list = array();
		public $theMirror = "";
		private $searchTerms = null; // null search
		private $searchCategory = 0; // defaults to all categories
		private $order=3; // sort by date desc 
		private $searchPage = 0;
		private $categories = array(
			"audio"=>100, "audio/music"=>101, "audio/books"=>102, "audio/clips"=>103, "audio/flac"=>104, "audio/other"=>199, 
			"video"=>200, "video/movies"=>201, "video/dvdr"=>202, "video/music"=>203, "video/clips"=>204, "video/tvshows"=>205, "video/handheld"=>206, "video/movies/hd"=>207, "video/tvshows/hd"=>208,	"video/3d"=>209, "video/other"=>299, 
			"applications"=>300, "applications/windows"=>301, "applications/mac"=>302, "applications/unix"=>303, "applications/handheld"=>304, "applications/ios"=>305, "applications/android"=>306, "applications/other"=>399,
			"games"=>400, "games/pc"=>401, "games/mac"=>402, "games/psx"=>403, "games/xbox"=>404, "games/wii"=>405, "games/handheld"=>406, "games/ios"=>407, "games/android"=>408, "games/other"=>499, 
			"pr0n"=>500, "pr0n/movies"=>501, "pr0n/movies/dvdr"=>502, "pr0n/pictures"=>503, "pr0n/games"=>504, "pr0n/movies/hd"=>505, "pr0n/clips"=>506, "pr0n/other"=>599, 
			"other"=>600, "other/ebook"=>601, "other/comics"=>602, "other/pictures"=>603, "other/covers"=>604, "other/physibles"=>605, "other/other"=>699
		);
		/**
			 curl get contents
		*/
		private $curlTimeout=5;
		private $curlProxy=null;		
		/*
		Proxy List:
			https://thepiratebay-proxylist.se/api/v1/proxies	 
											  P S Cat
			https://xxxxxxxxxxx/search/empire/0/7/200
		*/
		private function curl_get_contents($url=null){
			$ch = curl_init();
			if($ch === false || $url==null)
			{
			    return "curl NOK obj";
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, is_numeric($this->curlTimeout)?$this->curlTimeout:5);
			//proxy details
			if($this->curlProxy!=null)
				curl_setopt($ch, CURLOPT_PROXY, $this->curlProxy);
			$data = curl_exec($ch);
			echo $data;
			curl_close($ch);
			return $data;
		}		
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

		public function getCategories(){
			$o = "";
			foreach ($this->categories as $k=>$v){
				$o .= "$k:$v, ";
			}
			$o = rtrim($o, ", ");
			return $o;
		}
		public function searchFor($what=null){
			$this->searchTerms = $what;
		}
		public function searchInCategory($cat){
			$cat = trim($cat);
			if($this->categories[$cat]>0){
				$this->searchCategory = $this->categories["$cat"];
				return true;
			}else{
				return false;
			}
		}
		public function orderBy($field="date", $order="desc"){
			switch(strtolower($field)){
				case "name":
					$this->order = ($order == "desc")?1:2;
					break;
				case "date":
					$this->order = ($order == "desc")?3:4;
					break;
				case "size":
					$this->order = ($order == "desc")?5:6;
					break;
				case "seeds":
					$this->order = ($order == "desc")?7:8;
					break;
				case "leeches":
					$this->order = ($order == "desc")?9:10;
					break;
				default:
					return false;
					break;
			}
			return true;
		}
		public function getSearchUrl(){
			$o = "http://$this->theMirror/search/$this->searchTerms/$this->searchPage/$this->order/$this->searchCategory";
//			$o = "/tmp/oo.html";
			return $o;
		}

		public function doSearchUrl(){
			$url = $this->getSearchUrl();
//			return $this->curl_get_contents($url);
			return file_get_contents($url);
		}

		public function setProxy($proxy){
			if($proxy==null){
				$this->curlProxy = null;
			}else{
				$this->curlProxy = $proxy;
			}	
		}

		public function getPBLinks($f){
			require __DIR__ ."../vendor/autoload.php";
			//	$dom = new Sunra\PhpSimple\HtmlDomParser();
			//	$dom = new PHPHtmlParser\Dom();
			$dom = pQuery::parseStr($f);
			$o = $dom->query('#searchResults')->tagName('tbody');
			//	echo $o->html();
			
			$dom2 = pQuery::parseStr($o->html());
			$o2 =$dom2->query('tr'); 	
			foreach ($o2 as $trcontent) {
			    // echo "---------------------".PHP_EOL.$trcontent->html().PHP_EOL;
				$dom3 = pQuery::parseStr($trcontent->html());
			//		$a = $dom3->query('.detLink');echo $a->attr('href').PHP_EOL;
				$td = $dom3->query('td');
				foreach($td as $k=>$tdc){
					switch($k){
						case 0:
							break;
						case 1:
							$n['href'] = $tdc->query('.detLink')->attr('href');
							$n['html'] = $tdc->query('.detLink')->html();
							break;
						case 2:
							$n['seeders'] = $tdc->html();
							break;
						case 3:
							$n['leechers'] = $tdc->html();
							break;
					}
				}
				print_r($n);
			}
		}


		
	}
	/////////////////////////

	// require __DIR__ ."/../vendor/autoload.php";
	//	$opts = new Commando\Command();
	//	$opts->option('m')->aka('man')->describedAs(MAN)->boolean()->defaultsTo(false);
	//	$opts->option('e')->aka('engine')->describedAs('--engine <...> or -e <google|duckgo|searxme|playstore> or  -e <g|d|s|p>')->defaultsTo("s");
	$pb = new phpiratebay();
	// $pb->theMirror;
	// echo $pb->getCategories();
	// print_r($pb->list);
//	$pb->setProxy("socks5://localhost:9999"); //not working ??
//	$pb->setProxy("http://127.0.0.1:9999"); //not working ??
//	$pb->searchFor("empire strikes back");
	$pb->searchFor("android");
	$pb->searchInCategory("video");
	$pb->orderBy("date","desc");
	$pb->getPBlinks($pb->doSearchUrl());
	
	