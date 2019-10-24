<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Render Library
 * @author Howard <howard@realtyna.com>
 * @since WPL1.0.0
 * @date 08/19/2013
 * @package WPL
 */
class wpl_render
{
    /**
     * Renders date based on global date format
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $date
     * @param int $year
     * @param int $month
     * @param int $day
     * @return string
     */
	public static function render_date($date, $year = NULL, $month = NULL, $day = NULL)
	{
		if(trim($date) == '0000-00-00' or trim($date) == '0000-00-00 00:00:00') return '';
		$date_arr = explode('-', $date);
		
		if(!$year and isset($date_arr[0])) $year = $date_arr[0];
		if(!$month and isset($date_arr[1])) $month = $date_arr[1];
		if(!$day and isset($date_arr[2])) $day = $date_arr[2];
		
		$date = $year.'-'.$month.'-'.$day;
		$date_format_arr = explode(':', wpl_global::get_setting('main_date_format'));
		$date_format = $date_format_arr[0];
		
		return date($date_format, strtotime($date));
	}

	/**
	 * Converts date
	 * @author Edward <edward@realtyna.com>
	 * @param string $date
	 * @param string $current_format
	 * @param string $new_format
	 * @return mixed (false|string)
	 */
	public static function convert_date($date, $current_format, $new_format)
	{
		if(!trim($date) or $current_format == $new_format) return $date;

		/** in PHP >= 5.3.1 **/
		if(!method_exists('DateTime','createFromFormat'))
		{
			$format_change = array('yy'=>'Y', 'mm'=>'m', 'dd'=>'d');

			$current_format = str_replace(array_keys($format_change), array_values($format_change), $current_format);
			$new_format = str_replace(array_keys($format_change), array_values($format_change), $new_format);

			$new_date = DateTime::createFromFormat($current_format, $date);
			if(!$new_date) return false;

			return $new_date->format($new_format);
		}

		/** Define variables **/
		$delimiter = '';
		$new_date = '';

		$possible_delimiters = array('.', '-', '/');
		$date_array = array();
		$current_format_array = array();
		$new_format_array = array();

		/** explode into arrays **/
		foreach ($possible_delimiters as $_delimiter)
		{
			if(strpos($date, $_delimiter) !== false and strpos($current_format, $_delimiter) !== false)
			{
				$date_array = explode($_delimiter, $date);
				$current_format_array = array_flip(explode($_delimiter, $current_format));
			}

			if(strpos($new_format, $_delimiter) !== false)
			{
				$delimiter = $_delimiter;
				$new_format_array = explode($_delimiter, $new_format);
			}
		}

		if(!$delimiter or !sizeof($date_array) or !sizeof($current_format_array) or !sizeof($new_format_array) or sizeof($date_array) !== sizeof($current_format_array)) return false;

		/**
		 * Method explain:
		 * Current format array: "mm.dd.yy" -> mm=>0  dd=>1  yy=>2
		 * Date array: "10.22.2016" -> 0=>10  1=>22  2=>2016
		 * New format array: "yy/mm/dd" -> 0=>yy  mm=>1  dd=>2
		 * Convert Sample = $date_array[$former_format[$new_format[0]]] = $date_array[$former_format[yy]] = $date_array[2] = 2016
		 */

		for($i = 0; $i < 3; $i++) $new_date .= $date_array[$current_format_array[$new_format_array[$i]]] . $delimiter;

		/** Remove last delimiter **/
		return rtrim($new_date, $delimiter);
	}
	
    /**
     * Render date time
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $datetime
     * @param mixed $year
     * @param mixed $month
     * @param mixed $day
     * @return string
     */
    public static function render_datetime($datetime, $year = '', $month = '', $day = '')
	{
		if(trim($datetime) == '0000-00-00' or trim($datetime) == '0000-00-00 00:00:00') return '';
		$tmp = explode(' ', $datetime);
        
		$date = isset($tmp[0]) ? $tmp[0] : '';
		$time = isset($tmp[1]) ? $tmp[1] : '';
        
		$output = wpl_render::render_date($date).' '.$time;
		return $output;
	}
    
    /**
     * Renders longitude
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $longitude
     * @return string
     */
	public static function render_longitude($longitude)
	{
		$degree = floor($longitude);
		$rest = ($longitude - $degree)*60;
		$minutes = floor($rest);
		$rest = $rest - $minutes;
		$seconds = $rest*60;
		
		if($degree < 0)
		{
			$degree = $degree * -1;
			$sign = 'W';
		}
		else $sign = 'E';
		
		return $sign . $degree .'&deg; '. $minutes ."' ". round($seconds, 1) ."'' ";
	}
	
    /**
     * Renders latitude
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $latitude
     * @return string
     */
	public static function render_latitude($latitude)
	{
		$degree = floor($latitude);
		$rest = ($latitude - $degree)*60;
		$minutes = floor($rest);
		$rest = $rest - $minutes;
		$seconds = $rest*60;
		
		if($degree < 0)
		{
			$degree = $degree * -1;
			$sign = 'S';
		}
		else $sign = 'N';
		
		return $sign . $degree .'&deg; '. $minutes ."' ". round($seconds, 1) ."'' ";
	}
	
    /**
     * Render file size based on Byte
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $size
     * @return string
     */
	public static function render_file_size($size)
	{
		$d = 'B';
		if($size > 1024) { $size = $size/1024; $d = 'KB'; }
		if($size > 1024) { $size = $size/1024; $d = 'MB'; }
		if($size > 1024) { $size = $size/1024; $d = 'GB'; }
		
		return round($size, 1).$d;
	}
	
    /**
     * Converts SI price (USD) to another currency
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $price_si
     * @param int $unit_id
     * @return int
     */
	public static function convert_price($price_si, $unit_id)
	{
		/** in case of empty unit just do it with default currency **/
		if(!trim($unit_id))
		{
			$all_units = wpl_units::get_units(4, 1);
			$unit_id = $all_units[0]['id'];
		}
        
		return wpl_render::convert_unit($price_si, $unit_id);
	}
    
    /**
     * Converts SI value to another unit
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $value_si
     * @param int $unit_id
     * @return int
     */
    public static function convert_unit($value_si, $unit_id)
	{
		/** get unit data **/
		$unit_data = wpl_units::get_unit($unit_id);
		
		if(!$unit_data) return 0;
		if(!$unit_data['tosi']) return 0;
		
		return ($value_si/$unit_data['tosi']);
	}
	
    /**
     * Renders price based on currency unit id
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $price
     * @param int $unit_id
     * @param string $symbol
     * @param string $price_text
     * @return string
     */
	public static function render_price($price, $unit_id = NULL, $symbol = '', $price_text = '')
	{
		/** in case of empty unit just do it with default currency **/
		if(trim($unit_id) == '')
		{
			$all_units = wpl_units::get_units(4, 1);
			$unit_id = $all_units[0]['id'];
		}
		
		/** get currency **/
		$currency = wpl_units::get_unit($unit_id);

        if(!trim($symbol)) $symbol = __($currency['name'], 'real-estate-listing-realtyna-wpl');
		$decimal = 2;
		
		$d_seperator = trim($currency['d_seperator']) != '' ? $currency['d_seperator'] : '';
		$seperator = trim($currency['seperator']) != '' ? $currency['seperator'] : '';
		
		/** set decimal **/
		if(!$d_seperator) $decimal = 0;
		
		/** set default value **/
		if(trim($price) == '') $price = 0;
        
        /** Convert price to float **/
        if(strpos($price, '.') !== false)
        {
            $price = (float) $price;
        }
        
        /** Remove decimals if the price is not float **/
		if(!is_float($price))
        {
            $price = intval($price);
            $decimal = 0;
        }
        
		$price = number_format($price, $decimal, $d_seperator, $seperator);
		
        // For minimized prices such as 100K, 1.1M etc
        if(trim($price_text) != '') $price = $price_text;
        
		if($currency['after_before'] == 1) $return = $price.$symbol; // After
		elseif($currency['after_before'] == 2) $return = $symbol.' '.$price; // Before with Space
		elseif($currency['after_before'] == 3) $return = $price.' '.$symbol; // After with Space
		else $return = $symbol.$price; // Before
		
		return $return;
	}
	
    /**
     * Derendere date based on global settings
     * @author Albert <albert@realtyna.com>
     * @static
     * @param string $date
     * @return string
     */
	public static function derender_date($date)
	{
        $time = '';
        if(strpos($date, ' ') !== false)
        {
            $ex = explode(' ', $date);
            $date = $ex[0];
            $time = $ex[1];
        }
        
		$date_format_arr = explode(':', wpl_global::get_setting('main_date_format'));
		$date_format = $date_format_arr[0];

		if(stristr($date_format, '-') != '') $delimiter = '-';
		elseif(stristr($date_format, '.') != '') $delimiter = '.';
		else $delimiter = '/';
		
		$date_format_parts = explode($delimiter, $date_format);
		$date_parts = explode($delimiter, $date);
		$standard_date = array();
        
		for($i=0; $i<3; $i++)
		{
			switch(strtolower($date_format_parts[$i]))
			{
				case 'y':
					$standard_date['y'] = $date_parts[$i];
				break;
				
				case 'm':
					$standard_date['m'] = $date_parts[$i];
				break;
				
				case 'd':
					$standard_date['d'] = $date_parts[$i];
				break;
			}
		}
		
		$dedate = trim($standard_date['y'].'-'.$standard_date['m'].'-'.$standard_date['d'].' '.$time);
		return $dedate;
	}
    
    /**
     * Renders Parent Field
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param string $parent_column
     * @param boolean $ids
     * @return string
     */
    public static function render_parent($property_id, $parent_column = 'parent', $ids = false)
    {
        $parents = array();
        
        if($ids) $parents[] = $property_id;
        else $parents[] = wpl_property::update_property_title(NULL, $property_id);
        
        $parent_id = wpl_property::get_parent($property_id);
        if($parent_id) $parents[] = self::render_parent($parent_id, $parent_column, $ids);
        
        $glue = $ids ? ',' : ' / ';
        return implode($glue, $parents);
    }
    
    /**
     * Renders numerci values
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $number
     * @return string
     */
    public static function render_number($number)
    {
        return number_format($number, 0, '.', ',');
    }
}