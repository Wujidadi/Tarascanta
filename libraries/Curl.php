<?php

namespace Libraries;

/**
 * cURL handle class.
 */
class Curl
{
    /**
     * cURL handle.
     *
     * @var \CurlHandle|resource|null|false
     */
    protected $_handle = null;

    /**
     * cURL response of a request.
     *
     * @var string|boolean|null
     */
    protected $_response = null;

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_instance = null;

    /**
     * Constructor.
     *
     * @return void
     */
    protected function __construct()
    {
        $this->_init();
    }

    /**
     * Get the instance of this class.
     * 
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_instance == null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Initialization method.
     *
     * @return void
     */
    private function _init()
    {
        $this->_handle = curl_init();
        curl_setopt($this->_handle, CURLOPT_CONNECTTIMEOUT, CURL_CONNECT_TIMEOUT);
        curl_setopt($this->_handle, CURLOPT_TIMEOUT,        CURL_TIMEOUT);
        curl_setopt($this->_handle, CURLOPT_SSL_VERIFYPEER, CURL_SSL_VERIFYPEER);
        curl_setopt($this->_handle, CURLOPT_FOLLOWLOCATION, CURL_FOLLOW_LOCATION);
        curl_setopt($this->_handle, CURLOPT_RETURNTRANSFER, CURL_RETURN_TRANSFER);
    }

    /**
     * Set the number of seconds of cURL connection timeout.
     *
     * @param  integer  $second  Number of seconds
     * @return void
     */
    public function setConnectTimeout($second)
    {
        if (is_numeric($second))
        {
            curl_setopt($this->_handle, CURLOPT_CONNECTTIMEOUT, (int) $second);
        }
    }

    /**
     * Set the number of seconds of cURL function execution timeout.
     *
     * @param  integer  $second  Number of seconds
     * @return void
     */
    public function setTimeout($second)
    {
        if (is_numeric($second))
        {
            curl_setopt($this->_handle, CURLOPT_TIMEOUT, (int) $second);
        }
    }

    /**
     * Set HTTP header fields of the cURL handle.
     *
     * @param  array  $header  cURL HTTP header fields
     * @return void
     */
    public function setHeader($header)
    {
        if (is_array($header))
        {
            curl_setopt($this->_handle, CURLOPT_HTTPHEADER, $header);
        }
    }

    /**
     * Set user agent of the cURL handle.
     *
     * @param  string  $userAgent  User agent
     * @return void
     */
    public function setUserAgent($userAgent)
    {
        if (gettype($userAgent) == 'string')
        {
            curl_setopt($this->_handle, CURLOPT_USERAGENT, $userAgent);
        }
    }

    /**
     * Set whether or not to verify the peer's certificate.
     *
     * @param  boolean  $verifypeer  Whether or not to verify the peer's certificate
     * @return void
     */
    public function setSslVerifypeer($verifypeer)
    {
        curl_setopt($this->_handle, CURLOPT_SSL_VERIFYPEER, (bool) $verifypeer);
    }

    /**
     * Set whether or not to follow the location by "Location: " header.
     *
     * @param  boolean  $followLocation  Whether or not to follow the location by "Location: " header
     * @return void
     */
    public function setFollowLocation($followLocation)
    {
        curl_setopt($this->_handle, CURLOPT_FOLLOWLOCATION, (bool) $followLocation);
    }

    /**
     * Set to `true` to return the response as a string or `false` to output it directly.
     *
     * @param  boolean  $returnTransfer  Return the response as a string (`true`) or output it directly (`false`)
     * @return void
     */
    public function setReturnTransfer($returnTransfer)
    {
        curl_setopt($this->_handle, CURLOPT_RETURNTRANSFER, (bool) $returnTransfer);
    }

    /**
     * Send a GET request.
     *
     * @param  string  $url   Target URL
     * @param  mixed   $data  Data to send
     * @return Curl
     */
    public function get($url, $data = null)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        if (!is_null($data))
        {
            $connector = strpos($url, '?') ? '&' : '?';
            $url .= $connector . $this->_queryBody($data);
        }
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_HTTPGET, true);
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * Send a HEAD request.
     *
     * @param  string   $url     Target URL
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return Curl
     */
    public function head($url, $data = null, $isJson = false)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $this->_queryBody($data, $isJson));
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * 發送 POST 請求
     *
     * @param  string   $url     Target URL
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return Curl
     */
    public function post($url, $data = null, $isJson = false)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_POST, true);
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $this->_queryBody($data, $isJson));
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * Send a PUT request.
     *
     * @param  string   $url     Target URL
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return Curl
     */
    public function put($url, $data = null, $isJson = false)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $this->_queryBody($data, $isJson));
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * Send a DELETE request.
     *
     * @param  string   $url     Target URL
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return Curl
     */
    public function delete($url, $data = null, $isJson = false)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $this->_queryBody($data, $isJson));
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * Send a OPTIONS request.
     *
     * @param  string   $url     Target URL
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return Curl
     */
    public function options($url, $data = null, $isJson = false)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $this->_queryBody($data, $isJson));
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * Send a PATCH request.
     *
     * @param  string   $url     Target URL
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return Curl
     */
    public function patch($url, $data = null, $isJson = false)
    {
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, NULL);
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $this->_queryBody($data, $isJson));
        $this->_response = curl_exec($this->_handle);
        return $this;
    }

    /**
     * Get cURL response of last request.
     *
     * @return string|boolean
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get cURL handle.
     *
     * @return \CurlHandle|resource|null|false
     */
    public function getHandle()
    {
        return $this->_handle;
    }

    /**
     * Build query data body.
     *
     * @param  mixed    $data    Data to send
     * @param  boolean  $isJson  Whether or not to send the data as JSON
     * @return string|array|object|null
     */
    private function _queryBody($data, $isJson = false)
    {
        $dataType = gettype($data);

        switch ($dataType)
        {
            # When the data is a string or resource, ignore the isJson option and pass it directly
            case 'string':
            case 'resource':
            case 'resource (closed)':
                return $data;

            # When the data is an array or an object, process it according to the isJson option
            case 'array':
            case 'object':
                # If the data should be sent as JSON, convert if to JSON
                if ($isJson)
                {
                    return JsonUnescaped($data);
                }
                # If the data should not be sent as JSON, convert it as a query string
                else
                {
                    return http_build_query($data);
                }

            # Data in other types is ignored
            case 'boolean':
            case 'integer':
            case 'double':
            case 'NULL':
            case 'unknown type':
            default:
                return null;
        }
    }

    /**
     * Destructor.
     *
     * @return void
     */
    public function __destruct()
    {
        curl_close($this->_handle);
    }
}
