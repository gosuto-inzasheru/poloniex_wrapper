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
	    $uri = $this->publicUrl.'?command='.$call;
	    return json_decode(file_get_contents($uri), true);
	}

	//Public API Methods

	/**
	 * Returns the ticker for all markets.
	 * @return array
	 */
	public function returnTicker() {
	    return $this->callPublic('returnTicker');
	}

	/**
	 * Returns the 24-hour volume for all markets, plus totals for primary currencies.
	 * @return array
	 */
	public function return24hVolume() {
		return $this->callPublic('return24hVolume');
	}

	/**
	 * Returns the order book for a given market, as well as a sequence number for use with the Push API and an indicator specifying whether the market is frozen.
	 * @param string $currencyPair Set to all to get the order books of all markets. Otherwise define a currency pair such as BTC_ETH
	 * @param integer $depth Limits the market to a certain amount of orders.
	 * @return array
	 */
	public function returnOrderBook($currencyPair = 'all', $depth = null) {

	    $call = 'returnOrderBook';
	    $call .= '&currencyPair='.$currencyPair;
	    $call .= ($depth) ? '&depth='.$depth : null;

		return $this->callPublic($call);
	}

	/**
	 * Returns the past 200 trades for a given market, or up to 50,000 trades between a range specified in UNIX timestamps by the "start" and "end" GET parameters.
	 * @param string $currencyPair Example: BTC_ETH
	 * @param $start UNIX timestamp
	 * @param $end UNIX timestamp
	 * @return array
	 */
	public function returnTradeHistory($currencyPair, $start = null, $end = null) {

	    $call = 'returnTradeHistory';
	    $call .= '&currencyPair='.$currencyPair;
	    $call .= ($start) ? '&start='.$start : null;
	    $call .= ($end) ? '&end='.$end : null;

		return $this->callPublic($call);
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

	    $call = 'returnChartData';
	    $call .= '&currencyPair='.$currencyPair;
	    $call .= '&period='.$period;
	    $call .= ($start) ? '&start='.$start : null;
	    $call .= ($end) ? '&end='.$end : null;

		return $this->callPublic($call);
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
	 * @param string $currency Example: BTC_ETH
	 * @return array
	 */
	public function returnLoanOrders($currency) {
	    $call = 'returnLoanOrders';
	    $call .= '&currency='.$currency;

		return $this->callPublic($call);
	}
}
?>