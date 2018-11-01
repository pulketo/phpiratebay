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
		public function __construct($mirror = null, $searchTerm=null, $category="all", $orderBy="dateDesc", $page=0 ){
			if ($mirror == null){
				$tmp = $this->getProxyList()->proxies;
				$this->theMirror = $tmp[RAND(0,sizeof($tmp))]->domain;
			}else{
				$errMirror = $this->setMirror($mirror);
			}
			
		}

		public function setMirror($mirror){
			$this->theMirror = $mirror;
			return true;
		}

		private function dbg($what){
			if($this->debug)
				echo $what;
		}

		private function getProxyList(){
			$o = file_get_contents("https://thepiratebay-proxylist.se/api/v1/proxies");
			// $o = file_get_contents("../proxies.json");
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
		private function getSearchUrl(){
			$o = "http://$this->theMirror/search/$this->searchTerms/$this->searchPage/$this->order/$this->searchCategory";
			//$o = "/tmp/oo.html";
			return $o;
		}

		private function doSearchUrl(){
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

		public function getPBLinks(){
			$f = $this->doSearchUrl();
			require_once __DIR__ ."/../vendor/autoload.php";
			$dom = pQuery::parseStr($f);
			$o = $dom->query('#searchResults')->tagName('tbody');
			$dom2 = pQuery::parseStr($o->html());
			$o2 =$dom2->query('tr'); 	
			foreach ($o2 as $trcontent) {
				$dom3 = pQuery::parseStr($trcontent->html());
				$td = $dom3->query('td');
				$n=array();
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
				if(isset($n['href']))
					$out[] = $n;
			}
		return $out;
		}

		function arrLinearToPair($arr=array()){
			if (sizeOf($arr) > 0 && (sizeOf($arr)%2) == 0 ){
				
			}else{
				return false;
			}
		}

		public function getTorrentDetails($pbtorrent){
			$pbtorrent = ltrim($pbtorrent,"/ ");
			$o = "http://$this->theMirror/$pbtorrent";
			$html = file_get_contents($o);
			return json_encode((array)$this->getTorrentInfo($html));
		}
				
		public function getTorrentInfo($f){
			require_once __DIR__ ."/../vendor/autoload.php";
			$page = pQuery::parseStr($f);

			$details0['mirror'] = $this->theMirror;
//			$details0['source'] = ;
			
			$o = $page->query('#detailsframe');
			$details = pQuery::parseStr($o->html());
			$title = trim($details->query('#title')->html());
			{ //get col1 info 
				$ddtmp=array();
				$detailsCol = trim($details->query('.col1')->html());
				//i don't know how to iterate on different tags so... i will change dt to dd...
				$detailsCol = str_ireplace("dt>", "dd>", $detailsCol);
				$detailsColDOM = pQuery::parseStr($detailsCol);
				$ddQ = $detailsColDOM->query('dd'); 	
				foreach ($ddQ as $k=>$dd) {
					$ddtmp[] = trim(strip_tags($dd->html()));
				}
				$details1 = $this->serialToCombined($ddtmp);
//				echo "details1:";print_r($details1);				
			}
			{ //get col2 info 
				$ddtmp=array();
				$detailsCol = trim($details->query('.col2')->html());
				//i don't know how to iterate on different tags so... i will change dt to dd...
				$detailsCol = str_ireplace("dt>", "dd>", $detailsCol);
				$detailsColDOM = pQuery::parseStr($detailsCol);
				$ddQ = $detailsColDOM->query('dd'); 	
				foreach ($ddQ as $k=>$dd) {
					$ddtmp[] = trim(strip_tags($dd->html()));
				}
				$details2 = $this->serialToCombined($ddtmp);
//				echo "details2:";print_r($details2);
			}
			{ // get magnet info
				$magnet = trim($details->query('.download a')->attr('href'));
				$details3 ['magnet'] = $magnet;
//				echo "details3:";print_r($details3);
			}
			
			{ // get extra nfo (images)
				$nfo = $details->query('.nfo a');
				foreach($nfo as $k=>$v){
					$NFO[$v->attr('href')] = trim($v->html());
				}
				$details4['NFO'] = $NFO;
//				echo "details4:";print_r($details4);
			}
			$allDetails = array_merge((array)$details0, (array)$details1, (array)$details2, (array)$details3, (array)$details4);
//			print_r($allDetails);
			return $allDetails;
		}

		private function serialToCombined($arr=array()){
			if ( sizeOf($arr)>0 && sizeOf($arr)%2 == 0 ){
				while(sizeOf($arr)>0){
					$tmp = array_shift($arr);
					$k[] = preg_replace("/[^a-zA-Z0-9]+/", "", $tmp);
					$v[] = array_shift($arr);
				}
				return array_combine($k,$v);
			}else{
				return false;
			}
		}
	}