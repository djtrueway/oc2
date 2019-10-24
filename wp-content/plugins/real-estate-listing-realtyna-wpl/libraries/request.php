<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Request Library
 * @author Howard <howard@realtyna.com>
 * @since WPL1.0.0
 * @date 03/10/2013
 * @package WPL
 */
class wpl_request
{
    /**
     * Returns request method
     * @author Howard <howard@realtyna.com>
     * @static
     * @return string
     */
	public static function get_method()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
	
    /**
     * get a variable
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $name
     * @param mixed $default
     * @param string $hash
     * @param boolean $clean
     * @return mixed
     */
	public static function getVar($name, $default = null, $hash = 'default', $clean = false)
	{
		// Ensure hash and type are uppercase
		$hash = strtoupper($hash);
		
		if ($hash === 'METHOD')
		{
			$hash = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		// Get the input hash
		switch ($hash)
		{
			case 'GET':
				$input = &$_GET;
				break;
			case 'POST':
				$input = &$_POST;
				break;
			case 'FILES':
				$input = &$_FILES;
				break;
			case 'COOKIE':
				$input = &$_COOKIE;
				break;
			case 'ENV':
				$input = &$_ENV;
				break;
			case 'SERVER':
				$input = &$_SERVER;
				break;
			default:
				$input = &$_REQUEST;
				break;
		}

		$var = isset($input[$name]) ? $input[$name] : $default;
		
		/** clean **/
		if($clean) $var = wpl_global::clean($var);
		
		return $var;
	}

    /**
     * Gets a variable array
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $hash
     * @return array
     */
	public static function get($hash = 'default')
	{
		// Ensure hash and type are uppercase
		$hash = strtoupper($hash);

		if ($hash === 'METHOD')
		{
			$hash = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		switch ($hash)
		{
			case 'GET':
				$input = $_GET;
				break;

			case 'POST':
				$input = $_POST;
				break;

			case 'FILES':
				$input = $_FILES;
				break;

			case 'COOKIE':
				$input = $_COOKIE;
				break;

			case 'ENV':
				$input = &$_ENV;
				break;

			case 'SERVER':
				$input = &$_SERVER;
				break;

			default:
				$input = $_REQUEST;
				break;
		}

		return $input;
	}
	
    /**
     * Set a variable in one of the request variables. 
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $name
     * @param mixed $value
     * @param string $hash
     * @param boolean $overwrite
     * @return mixed
     */
	public static function setVar($name, $value = null, $hash = 'method', $overwrite = true)
	{
		// If overwrite is true, makes sure the variable hasn't been set yet
		if(!$overwrite && array_key_exists($name, $_REQUEST))
		{
			return $_REQUEST[$name];
		}

		/** Get the request hash value **/
		$hash = strtoupper($hash);
		if($hash === 'METHOD') $hash = strtoupper($_SERVER['REQUEST_METHOD']);

		$previous = array_key_exists($name, $_REQUEST) ? $_REQUEST[$name] : null;

		switch($hash)
		{
			case 'GET':
				$_GET[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'POST':
				$_POST[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'COOKIE':
				$_COOKIE[$name] = $value;
				$_REQUEST[$name] = $value;
				break;
			case 'FILES':
				$_FILES[$name] = $value;
				break;
			case 'ENV':
				$_ENV['name'] = $value;
				break;
			case 'SERVER':
				$_SERVER['name'] = $value;
				break;
		}
		
		return $previous;
	}

    /**
     * Sets array to the request
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $array
     * @param string $hash
     * @param boolean $overwrite
     */
	public static function set($array, $hash = 'default', $overwrite = true)
	{
		foreach($array as $key=>$value)
		{
			self::setVar($key, $value, $hash, $overwrite);
		}
	}
}

/**
 * Session Library
 * @author Howard <howard@realtyna.com>
 * @since WPL1.8.1
 * @date 23/09/2014
 * @package WPL
 */
class wpl_session
{
    /**
     * Set a variable to session
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $key
     * @param mixed $value
     * @param boolean $override
     * @return mixed
     */
    public static function set($key, $value = NULL, $override = true)
    {
        $apply = false;
        if(!isset($_SESSION[$key]))
        {
            $apply = true;
            $_SESSION[$key] = $value;
        }
        elseif(isset($_SESSION[$key]) and $override)
        {
            $apply = true;
            $_SESSION[$key] = $value;
        }
        
        return ($apply ? $value : NULL);
    }
    
    /**
     * Get a session variable
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $key
     * @return mixed
     */
    public static function get($key = NULL)
    {
        if($key) return (isset($_SESSION[$key]) ? $_SESSION[$key] : NULL);
        return $_SESSION;
    }
    
    /**
     * Remove a session variable
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $key
     * @return boolean
     */
    public static function remove($key = NULL)
    {
        if(!isset($_SESSION[$key])) return false;
        
        unset($_SESSION[$key]);
        return true;
    }
}

/**
 * Security Library
 * @author Howard <howard@realtyna.com>
 * @since WPL2.1.0
 * @package WPL
 */
class wpl_security
{
    /**
     * Security Salt
     * @var string
     */
    private $salt = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789';
    
    /**
     * Generates and returns a token
     * @author Howard <howard@realtyna.com>
     * @return string
     */
    public function token()
    {
        $random_key = substr(str_shuffle($this->salt), 0, 10);
        $token = md5($random_key.time());
        
        $query = "INSERT INTO `#__wpl_items` (`parent_kind`,`item_type`,`item_cat`,`item_name`,`creation_date`) VALUES ('-1','security','token','$token','".date("Y-m-d H:i:s")."')";
        wpl_db::q($query, 'INSERT');
        
        return $token;
    }
    
    /**
     * Check validity of a token
     * @param string $token
     * @param boolean $delete
     * @return boolean
     */
    public function validate_token($token, $delete = false)
    {
        $query = "SELECT COUNT(*) FROM `#__wpl_items` WHERE `item_name`='$token' AND `parent_kind`='-1'";
        $num = wpl_db::num($query);
        
        if($num and $delete)
        {
            $query = "DELETE FROM `#__wpl_items` WHERE `parent_kind`='-1' AND `item_name`='$token'";
            wpl_db::q($query, 'DELETE');
        }
        
        return $num ? true : false;
    }

    /**
     * Encrypt a string using mcrypt
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @param  string  $data     Input String
     * @param  string  $key      Encryption Key
     * @param  integer $strength Encryption Strength
     * @return string            Encrypted String
     */
    public static function encrypt($data, $key = 'WPL', $strength = 128)
    {
    	if(!extension_loaded('mcrypt')) return false;

    	if($strength == 192) $cipher = MCRYPT_RIJNDAEL_192;
    	elseif($strength == 256) $cipher = MCRYPT_RIJNDAEL_256;
    	else $cipher = MCRYPT_RIJNDAEL_128;

	    $iv_size = mcrypt_get_iv_size($cipher, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $result = $iv.@mcrypt_encrypt($cipher, $key, $data, MCRYPT_MODE_CBC, $iv);
        
	    return base64_encode($result);
    }

    /**
     * Decrypt a string using mcrypt
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @param  string  $data     Input String
     * @param  string  $key      Encryption Key
     * @param  integer $strength Encryption Strength
     * @return string            Decrypted String
     */
    public static function decrypt($data, $key = 'WPL', $strength = 128)
    {
    	if(!extension_loaded('mcrypt')) return false;

    	if($strength == 192) $cipher = MCRYPT_RIJNDAEL_192;
    	elseif($strength == 256) $cipher = MCRYPT_RIJNDAEL_256;
    	else $cipher = MCRYPT_RIJNDAEL_128;

	    $data = base64_decode($data);
    	$iv_size = mcrypt_get_iv_size($cipher, MCRYPT_MODE_CBC);
	    $iv_dec = substr($data, 0, $iv_size);
        
	    return @mcrypt_decrypt($cipher, $key, substr($data, $iv_size), MCRYPT_MODE_CBC, $iv_dec);
    }
    
    /**
     * Wrapper function for WordPress wp_verify_nonce function
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $nonce
     * @param string $action
     * @return boolean
     */
    public static function verify_nonce($nonce, $action = 'wpl_nonce')
    {
        return wp_verify_nonce($nonce, $action);
    }
    
    /**
     * Wrapper function for WordPress wp_nonce_field function
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $action
     * @param string $name
     * @param boolean $referer
     * @param boolean $echo
     * @return string
     */
    public static function nonce_field($action = 'wpl_nonce', $name = '_wpnonce', $referer = true, $echo = true)
    {
        return wp_nonce_field($action, $name, $referer, $echo);
    }
    
    /**
     * Wrapper function for WordPress wp_create_nonce function
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $action
     * @return string
     */
    public static function create_nonce($action = 'wpl_nonce')
    {
        return wp_create_nonce($action);
    }
}

/**
 * WPL Flash class for showing messages
 * @author Howard <howard@realtyna.com>
 * @since WPL2.8.0
 * @package WPL
 */
class wpl_flash
{
    /**
     * Set a message
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $message
     * @param string $HTML_class
     * @param string|int $client
     */
    public static function set($message, $HTML_class, $client = '0')
    {
        $wpl_flash = wpl_session::get('wpl_flash');
        if(!$wpl_flash or !is_array($wpl_flash)) $wpl_flash = array();
        
        array_push($wpl_flash, array($message, $HTML_class, $client));
        
        wpl_session::set('wpl_flash', $wpl_flash, true);
    }
    
    /**
     * Show the message
     * @author Howard <howard@realtyna.com>
     * @static
     * @return string
     */
    public static function get()
    {
        $wpl_flash = wpl_session::get('wpl_flash');
        if(!$wpl_flash or (is_string($wpl_flash) and trim($wpl_flash) == '')) return '';
        
        wpl_session::remove('wpl_flash');
        
        $flashes = '';
        foreach($wpl_flash as $flash)
        {
            $message = $flash[0];
            $HTML_class = $flash[1];
            $client = $flash[2];
            
            if(!in_array($client, array(2, wpl_global::get_client()))) continue;
            
            $flashes .= '<div class="'.$HTML_class.'">'.$message.'</div>';
        }
        
        return $flashes;
    }
}

/**
 * WPL Cookie Library
 * @author Steve A. <steve@realtyna.com>
 * @since WPL2.8.0
 * @package WPL
 */
class wpl_cookie
{
    /**
     * Set a variable to cookie
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @param string $key
     * @param mixed $value
     * @param boolean $override
     * @param integer $days
     * @return mixed
     */
    public static function set($key, $value = NULL, $override = true, $days = 30)
    {
        if(!is_numeric($days) or $days < 1) $days = 30;

        $apply = false;
        if((!isset($_COOKIE[$key])) or (isset($_COOKIE[$key]) and $override))
        {
            $apply = true;
            setcookie($key, $value, time() + (86400 * $days), '/');
        }
        
        return ($apply ? $value : NULL);
    }
    
    /**
     * Get a cookie variable
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @param string $key
     * @return mixed
     */
    public static function get($key = NULL)
    {
        if($key) return (isset($_COOKIE[$key]) ? $_COOKIE[$key] : NULL);
        return $_COOKIE;
    }

    /**
     * Remove a cookie
     * @author Steve A. <steve@realtyna.com>
     * @param  string $key
     * @return void
     */
    public static function remove($key)
    {
        if($key and isset($_COOKIE[$key]))
            unset($_COOKIE[$key]);
    }
}

/**
 * WPL download library to download multiple files concurrently
 * @author Howard R. <howard@realtyna.com>
 * @since WPL4.0.3
 * @package WPL
 */
class wpl_download
{
    /**
     * Multi Curl Handler
     * @var resource
     */
    private $mch;

    /**
     * Curl Handlers
     * @var array
     */
    private $chs;

    /**
     * wpl_download constructor.
     * @author Howard R. <howard@realtyna.com>
     */
    public function __construct()
    {
        // Multi Curl Handler
        $this->mch = curl_multi_init();
        $this->chs = array();
    }

    /**
     * Add a file to the queue
     * @author Howard R. <howard@realtyna.com>
     * @param string $url
     */
    public function add($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        curl_multi_add_handle($this->mch, $ch);

        // Add the Curl Handler to Available Handlers
        $this->chs[md5($url)] = $ch;
    }

    /**
     * Start the download
     * @author Howard R. <howard@realtyna.com>
     */
    public function start()
    {
        $running = NULL;

        do
        {
            curl_multi_exec($this->mch, $running);
        }
        while($running > 0);
    }

    /**
     * Get content of a specific file
     * @author Howard R. <howard@realtyna.com>
     * @param string $url
     * @return string|boolean
     */
    public function content($url)
    {
        $ch = isset($this->chs[md5($url)]) ? $this->chs[md5($url)] : NULL;

        if(!$ch) return false;
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) return false;

        $content = curl_multi_getcontent($ch);

        curl_multi_remove_handle($this->mch, $ch);
        curl_close($ch);

        return $content;
    }

    /**
     * Close the Multi Curl Handler
     * @author Howard R. <howard@realtyna.com>
     */
    public function close()
    {
        curl_multi_close($this->mch);
    }
}

class wpl_ftp
{
    private $server;
    private $username;
    private $password;
    private $passive;
    private $connection;

    /**
     * @var array
     */
    private $contents;

    /**
     * wpl_download constructor.
     * @author Howard R. <howard@realtyna.com>
     * @param string $server
     * @param string $username
     * @param string $password
     * @param boolean $passive
     */
    public function __construct($server, $username, $password, $passive = true)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->passive = $passive;
    }

    public function login()
    {
        $this->connection = ftp_connect($this->server);
        $status = ftp_login($this->connection, $this->username, $this->password);

        // Set the Passive Mode
        if($this->passive) ftp_pasv($this->connection, true);

        return $status;
    }

    public function contents($directory)
    {
        $this->contents = ftp_nlist($this->connection, $directory);
        return $this->contents;
    }

    public function files($extensions = array())
    {
        if(!$this->contents) return array();

        $files = array();
        foreach($this->contents as $content)
        {
            if(!$this->is_file($content)) continue;

            $extension = end(explode('.', $content));
            if(count($extensions) and !in_array($extension, $extensions)) continue;

            $files[] = $content;
        }

        return $files;
    }

    public function directories()
    {
        if(!$this->contents) return array();

        $directories = array();
        foreach($this->contents as $content)
        {
            if(!$this->is_dir($content)) continue;

            $directories[] = $content;
        }

        return $directories;
    }

    public function download($ftp_file, $local_file, $mode = FTP_BINARY)
    {
        return ftp_get($this->connection, $local_file, $ftp_file, $mode);
    }

    public function buffer($ftp_file, $mode = FTP_BINARY)
    {
        ob_start();
        $results = $this->download($ftp_file, 'php://output', $mode);
        $data = ob_get_contents();
        ob_end_clean();

        if($results) return $data;
        else return false;
    }

    public function close()
    {
        return ftp_close($this->connection);
    }

    public function is_file($content)
    {
        $contents = ftp_nlist($this->connection, $content);

        if(is_array($contents) and count($contents) == 1) return true;
        return false;
    }

    public function is_dir($content)
    {
        $contents = ftp_nlist($this->connection, $content);

        if(is_array($contents) and count($contents) > 1) return true;
        return false;
    }
}