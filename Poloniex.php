<?php

class Poloniex {

	protected $apiKey;
	protected $apiSecret;

	protected $publicUrl = "https://poloniex.com/public";
	protected $tradingApiUrl = "https://poloniex.com/tradingApi";

	public function __construct($apiKey, $apiSecret) {
		$this->apiKey = $apiKey;
		$this->apiSecret = $apiSecret;
	}

	protected function callPublic($call) {
		$uri = $this->publicUrl.'?'.http_build_query($call);
		return json_decode(file_get_contents($uri), true);
	}

	private function callTrading(array $req = array()) {
		// API settings
		$key = $this->apiKey;
		$secret = $this->apiSecret;

		// generate a nonce to avoid problems with 32bit systems
		$mt = explode(' ', microtime());
		$req['nonce'] = $mt[1].substr($mt[0], 2, 6);

		// generate the POST data string
		$post_data = http_build_query($req, '', '&');
		$sign = hash_hmac('sha512', $post_data, $secret);

		// generate the extra headers
		$headers = array(
			'Key: '.$key,
			'Sign: '.$sign,
		);

		// curl handle (initialize if required)
		static $ch = null;
		if (is_null($ch)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT,
				'Mozilla/4.0 (compatible; Poloniex PHP bot; '.php_uname('a').'; PHP/'.phpversion().')'
			);
		}
		curl_setopt($ch, CURLOPT_URL, $this->tradingApiUrl);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		// run the query
		$res = curl_exec($ch);

		if ($res === false) throw new Exception('Curl error: '.curl_error($ch));
		//echo $res;
		$dec = json_decode($res, true);
		if (!$dec){
			//throw new Exception('Invalid data: '.$res);
			return false;
		}else{
			return $dec;
		}
	}

	//Public API Methods

	/**
	 * Returns the ticker for all markets.
	 * @return array
	 */
	public function returnTicker() {
		return $this->callPublic(
			array(
				'command' => 'returnTicker',
			)
		);
	}

	/**
	 * Returns the 24-hour volume for all markets, plus totals for primary currencies.
	 * @return array
	 */
	public function return24hVolume() {
		return $this->callPublic(
			array(
				'command' => 'return24hVolume',
			)
		);
	}

	/**
	 * Returns the order book for a given market, as well as a sequence number for use with the Push API and an indicator specifying whether the market is frozen.
	 * @param string $currencyPair Set to all to get the order books of all markets. Otherwise define a currency pair such as BTC_ETH
	 * @param integer $depth Limits the market to a certain amount of orders.
	 * @return array
	 */
	public function returnOrderBook($currencyPair = 'all', $depth = null) {
		return $this->callPublic(
			array(
				'command' => 'returnOrderBook',
				'currencyPair' => $currencyPair,
				'depth' => $depth,
			)
		);
	}

	/**
	 * Returns the past 200 trades for a given market, or up to 50,000 trades between a range specified in UNIX timestamps by the "start" and "end" GET parameters.
	 * @param string $currencyPair Example: BTC_ETH
	 * @param $start UNIX timestamp
	 * @param $end UNIX timestamp
	 * @return array
	 */
	public function returnTradeHistory($currencyPair, $start = null, $end = null) {
		return $this->callPublic(
			array(
				'command' => 'returnTradeHistory',
				'currencyPair' => $currencyPair,
				'start' => $start,
				'end' => $end,
			)
		);
	}

	/**
	 * Returns candlestick chart data.
	 * @param string $currencyPair Example: BTC_ETH
	 * @param integer $period Candlestick period in seconds; valid values are 300, 900, 1800, 7200, 14400, and 86400.
	 * @param $start UNIX timestamp
	 * @param $end UNIX timestamp
	 * @return array
	 */
	public function returnChartData($currencyPair, $period, $start, $end) {
		return $this->callPublic(
			array(
				'command' => 'returnChartData',
				'currencyPair' => $currencyPair,
				'period' => $period,
				'start' => $start,
				'end' => $end,
			)
		);
	}

	/**
	 * Returns information about currencies.
	 * @return array
	 */
	public function returnCurrencies() {
		return $this->callPublic('returnCurrencies');
	}

	/**
	 * Returns the list of loan offers and demands for a given currency, specified by the "currency" GET parameter.
	 * @param string $currency Example: BTC
	 * @return array
	 */
	public function returnLoanOrders($currency) {
		return $this->callPublic(
			array(
				'command' => 'returnLoanOrders',
				'currency' => $currency,
			)
		);
	}

	//Trading API Methods


}
?>