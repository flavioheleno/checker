<?php

	class CHECKER {
		private $curl = null;
		private $time = 0;

		public function __construct() {
			$this->curl = curl_init();
			$options = array(
				CURLOPT_AUTOREFERER => true,
				CURLOPT_COOKIESESSION => true,
				CURLOPT_HEADER => true,
				CURLOPT_NOBODY => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_USERAGENT => 'Feed4Tweet-checker/0.1.1',
			
			);
			curl_setopt_array($this->curl, $options);
		}

		public function __destruct() {
			curl_close($this->curl);
		}

		public function url($value) {
			curl_setopt($this->curl, CURLOPT_URL, $value);
		}

		public function exec() {
			$this->time = microtime(true);
			$result = curl_exec($this->curl);
			$this->time = (microtime(true) - $this->time);
			if ($result === false)
				$response = array(
					'status' => false,
					'time' => round($this->time, 6),
					'message' => curl_error($this->curl)
				);
			else {
				if (preg_match('/^http\/1\.[01] ([0-9]+)/mi', $result, $matches))
					$code = $matches[1];
				else
					$code = -1;
				if (preg_match('/^location: (.*?)$/mi', $result, $matches))
					$url = trim($matches[1]);
				else
					$url = null;
				$response = array(
					'status' => true,
					'code' => $code,
					'url' => $url,
					'time' => round($this->time, 6),
					'header' => $result
				);
			}
			return json_encode($response);
		}

	}

?>
