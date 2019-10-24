<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * WPL Units library
 * @author Howard <howard@realtyna.com>
 * @since WPL1.0.0
 * @date 05/01/2013
 * @package WPL
 */
class wpl_units
{
	/**
	 * Base currency
	 * @var string
	 * @static
	 */
	public static $base_currency = null;
    
    /**
     * Used for caching in get_units function
     * @static
     * @var array
     */
    public static $units = array();

    /**
     * Returns unit types [AREA,VALUME,....]
     * @author Howard <howard@realtyna.com>
     * @static
     * @return array
     */
	public static function get_unit_types()
	{
		$query = "SELECT * FROM `#__wpl_unit_types`  ORDER BY `id` ASC";
		return wpl_db::select($query, 'loadAssocList');
	}

	/**
	* Gets SI unit types statically
	* @author Edward <edward@realtyna.com>
	* @static
	* @return array
	*/
	public static function get_si_unit_types()
	{
		return array('area', 'price', 'volume', 'length');
	}
	
    /**
     * Get units
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $type
     * @param int $enabled
     * @param string $condition
     * @return array
     */
	public static function get_units($type = 4, $enabled = 1, $condition = '')
	{
        // Generate the Cache Key
        $cache_key = $type.'_'.$enabled.'_'.$condition;
        
        // Return from cache if exists
        if(isset(self::$units[$cache_key])) return self::$units[$cache_key];
        
		if(trim($condition) == '')
		{
			$condition = '';
			
			if(trim($type) != '') $condition .= " AND `type`='$type'";
			if(trim($enabled) != '') $condition .= " AND `enabled`>='$enabled'";
		}
		
		$query = "SELECT * FROM `#__wpl_units` WHERE 1 ".$condition." ORDER BY `enabled` DESC, `index` ASC";
		$results = wpl_db::select($query, 'loadAssocList');
        
        /** add to cache **/
		self::$units[$cache_key] = $results;
        
        return $results;
	}
    
    /**
     * Getsa unit data
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $id
     * @return array
     */
	public static function get_unit($id)
	{
		/** first validation **/
		if(trim($id) == '') return array();
		
		$unit = wpl_units::get_units('', '', " AND `id`='".$id."'");
		return (isset($unit[0]) ? $unit[0] : NULL);
	}
	
    /**
     * Returns unit ID by desired key=value criteria
     * @author Howard <howard@realtyna.com>
     * @static
     * @param mixed $value
     * @param string $by
     * @param int $type
     * @return int
     */
    public static function id($value, $by = 'extra', $type = 4)
    {
        $query = "SELECT `id` FROM `#__wpl_units` WHERE `$by`='$value' AND `type`='$type'";
        return wpl_db::select($query, 'loadResult');
    }
    
    /**
     * Returns default unit
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $type
     * @param int $enabled
     * @param string $condition
     * @return array
     */
    public static function get_default_unit($type = 4, $enabled = 1, $condition = '')
	{
		if(trim($condition) == '')
		{
			$condition = '';
			
			if(trim($type) != '') $condition .= " AND `type`='$type'";
			if(trim($enabled) != '') $condition .= " AND `enabled`>='$enabled'";
		}
		
		$query = "SELECT * FROM `#__wpl_units` WHERE 1 ".$condition." ORDER BY `index` ASC LIMIT 1";
		return wpl_db::select($query, 'loadAssoc');
	}
    
    /**
     * Sorts units
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $sort_ids
     */
	public static function sort_units($sort_ids)
	{
		$conter = 0;
		$ex_sort_ids = explode(',', $sort_ids);
		
		foreach($ex_sort_ids as $ex_sort_id)
		{
			self::update($ex_sort_id, 'index', ($conter+1));
			$conter++;
		}
	}
	
    /**
     * Update wpl_units table
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $unit_id
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
	public static function update($unit_id, $key, $value = '')
	{
		/** first validation **/
		if(trim($unit_id) == '' or trim($key) == '') return false;
		$result = wpl_db::set('wpl_units', $unit_id, $key, $value);

		/** Update Price SI if the key the 'tosi' and unit type is price */
		if($key == 'tosi') 
		{
			$unit = self::get_unit($unit_id);
			if($unit['type'] == 4) 
			{
				self::update_price_si($unit_id, $value);
			}
		}

		return $result;
	}	

	/**
	 * Get base currency
	 * @author Edward <edward@realtyna.com>
	 * @static
	 * @return string
	 */
	protected static function get_base_currency()
	{
		if(!self::$base_currency)
		{
			$base_currency = wpl_settings::get('base_currency');
			if(!$base_currency) $base_currency = 'USD';

			self::$base_currency = $base_currency;
		}

	 	return self::$base_currency;
	}
	
    /**
     * This is a function for updating all currencies exchange rates from yahoo server
     * @author Howard <howard@realtyna.com>
     * @author Edward <edward@realtyna.com>
     * @static
     * @return void
     */
	public static function update_exchange_rates()
	{
		$units = self::get_units(4);
		$currencies = array();

		foreach($units as $unit) $currencies[] = $unit['extra'];

		$converted_raw = self::currency_converter($currencies, self::get_base_currency());
		if(!is_array($converted_raw)) return;

		$converted = array();
		foreach($converted_raw as $pair => $rate)
		{
			$cur_from = preg_replace('/(.+)' . self::get_base_currency() . '$/', "$1", $pair);
			$converted[$cur_from] = $rate;
		}

		foreach($units as $unit)
		{
			$currency_code = $unit['extra'];
			if(!array_key_exists($currency_code, $converted)) continue;

			$exchange_rate = $converted[$currency_code];
			if($exchange_rate) self::update_exchange_rate($unit['id'], $exchange_rate);
		}
        
        /** trigger event **/
		wpl_global::event_handler('exchange_rates_updated', array());
	}
	
    /**
     * Update one currency exchange rate
     * @author Howard <howard@realtyna.com>
     * @author Edward <edward@realtyna.com>
     * @static
     * @param int $unit_id
     * @param string $currency_code
     * @return int
     */
	public static function update_a_exchange_rate($unit_id, $currency_code)
	{
		$exchange_rate = self::currency_converter($currency_code, self::get_base_currency());
        $result = false;

		if($exchange_rate)
		{
			$result = self::update_exchange_rate($unit_id, $exchange_rate);
			
	        /** trigger event **/
			wpl_global::event_handler('exchange_rate_updated', array('unit_id'=>$unit_id, 'currency_code'=>$currency_code));
        }

		if($exchange_rate && $result) return $exchange_rate;
		else return 0;
	}
    
    /**
     * Updates exchange rate of a currency
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $unit_id
     * @param mixed $value
     * @return boolean
     */
	public static function update_exchange_rate($unit_id, $value)
	{
		$results = self::update($unit_id, 'tosi', $value);
        self::update_price_si($unit_id, $value);
        return $results;
	}

	/**
	 * Update Price SI for all properties based on their unit id
	 * @author Steve A. <steve@realtyna.com>
	 * @param  int 	 $unit_id
     * @param  mixed $value
	 * @return void
	 */
	public static function update_price_si($unit_id, $value)
	{
		if(!$unit_id or !$value) return;

		wpl_db::q("UPDATE `#__wpl_properties` SET `price_si` = ROUND(`price` * $value) WHERE `price_unit` = '$unit_id'", 'update');
		if(wpl_global::check_addon('complex')) wpl_db::q("UPDATE `#__wpl_properties` SET `price_max_si` = ROUND(`price_max` * $value) WHERE `price_unit` = '$unit_id'", 'update');
	}
	
    /**
     * Convert a value from a currency to another one
     * @author Edward <edward@realtyna.com>
     * @static
     * @param array|string $cur_from
     * @param array|string $cur_to
     * @param string $type [Available: Rate, Ask, Bid]
     * @param integer $decimal_places
     * @return mixed
     */
	public static function currency_converter($cur_from, $cur_to, $type = 'Rate', $decimal_places = 4)
	{
		if(!is_array($cur_from))
		{
			if(trim($cur_from) == '') $cur_from = self::get_base_currency();
			$cur_from = array($cur_from);
		}

		if(!is_array($cur_to))
		{
			if(trim($cur_to) == '') $cur_to = self::get_base_currency();
			$cur_to = array($cur_to);
		}

		if(sizeof($cur_from) == 1 && sizeof($cur_to) == 1 && reset($cur_from) == reset($cur_to)) return number_format(1, $decimal_places, '.', '');

		$rates_pair = array();

		foreach($cur_from as $value_from)
		{
			foreach($cur_to as $value_to)
			{
				$rates_pair[] = $value_from . $value_to;
			}
		}

		$host = 'https://query.yahooapis.com/v1/public/yql';
		$parameters = array(
			'q' => 'select * from yahoo.finance.xchange where pair in ("' . implode('","', $rates_pair) . '")',
			'format' => 'json'
		);

		/** Yahoo forces to include this parameter as well. **/
		$extra_param = '&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

		$request = $host . '?' . http_build_query($parameters) . $extra_param;
		
		$result = json_decode(wpl_global::get_web_page($request), true);
		
		if(!$result or array_key_exists('error', $result) or !isset($result['query']['results']['rate'])) return false;

		$received_rates = $result['query']['results']['rate'];

		/** This yahoo devs are crazy, result for one pair is different from multi pair **/
		if(sizeof($rates_pair) == 1)
		{
			return isset($received_rates[$type]) ? number_format($received_rates[$type], $decimal_places, '.', '') : false;
		}

		$rates = array();

		foreach($received_rates as $rate)
		{
			$rates[$rate['id']] = isset($rate[$type]) ? number_format($rate[$type], $decimal_places, '.', '') : false;
		}

		return $rates;
	}
    
    /**
     * Converts a value from a currency to another one using WPL units table
     * @author Howard <howard@realtyna.com>
     * @static
     * @param double $value
     * @param int $unit_from
     * @param int $unit_to
     * @return double
     */
	public static function convert($value, $unit_from, $unit_to)
	{
        /** Returns $value when both of currencies are same **/
        if($unit_from == $unit_to) return $value;
        
		$unit_from_data = self::get_unit($unit_from);
        $unit_to_data = self::get_unit($unit_to);
        
        $value_si = $value*$unit_from_data['tosi'];
        $value_final = $value_si/$unit_to_data['tosi'];
        
        return $value_final;
	}

    /**
     * Auto-Update Method - Used by cron
     * @author Edward <edward@realtyna.com>
     * @static
     * @return void
     */
	public static function auto_update_rates()
	{
		$auto_update_enabled = (int) wpl_global::get_setting('autoupdate_exchange_rates');
		if(!$auto_update_enabled) return;
		
		self::update_exchange_rates();
	}
}
