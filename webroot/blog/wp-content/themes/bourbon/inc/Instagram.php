<?php

namespace Bourbon\Instagram;

/**
 * Instagram API class
 *
 * API Documentation: http://instagram.com/developer/
 * Class Documentation: https://github.com/cosenary/Instagram-PHP-API
 *
 * @author Christian Metz
 * @since 30.10.2011
 * @copyright Christian Metz - MetzWeb Networks 2011-2014
 * @version 2.2
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */
class Instagram
{
    /**
     * The API base URL.
     */
    const API_URL = 'https://api.instagram.com/v1/';
    /**
     * The API OAuth URL.
     */
    const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';
    /**
     * The OAuth token URL.
     */
    const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
    /**
     * The Instagram API Key.
     *
     * @var string
     */
    private $_apikey;
    /**
     * The Instagram OAuth API secret.
     *
     * @var string
     */
    private $_apisecret;
    /**
     * The callback URL.
     *
     * @var string
     */
    private $_callbackurl;
    /**
     * The user access token.
     *
     * @var string
     */
    private $_accesstoken;
    /**
     * Whether a signed header should be used.
     *
     * @var bool
     */
    private $_signedheader = false;
    /**
     * Available scopes.
     *
     * @var string[]
     */
    private $_scopes = array('basic', 'likes', 'comments', 'relationships');
    /**
     * Available actions.
     *
     * @var string[]
     */
    private $_actions = array('follow', 'unfollow', 'block', 'unblock', 'approve', 'deny');
    /**
     * Rate limit.
     *
     * @var int
     */
    private $_xRateLimitRemaining;
    /**
     * Default constructor.
     *
     * @param array|string $config Instagram configuration data
     *
     * @return void
     *
     * @throws \MetzWeb\Instagram\InstagramException
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            // if you want to access user data
            $this->setApiKey($config['apiKey']);
            $this->setApiSecret($config['apiSecret']);
            $this->setApiCallback($config['apiCallback']);
        } elseif (is_string($config)) {
            // if you only want to access public data
            $this->setApiKey($config);
        } else {
            throw new InstagramException('Error: __construct() - Configuration data is missing.');
        }
    }
    /**
     * Generates the OAuth login URL.
     *
     * @param string[] $scopes Requesting additional permissions
     *
     * @return string Instagram OAuth login URL
     *
     * @throws \MetzWeb\Instagram\InstagramException
     */

    /**
     * Get user recent media.
     *
     * @param int|string $id Instagram user ID
     * @param int $limit Limit of returned results
     *
     * @return mixed
     */
    public function getUserMedia($id = 'self', $limit = 0)
    {
        $params = array();
        if ($limit > 0) {
            $params['count'] = $limit;
        }
        return $this->_makeCall('users/' . esc_attr( $id ) . '/media/recent', strlen($this->getAccessToken()), $params);
    }



    /**
     * The call operator.
     *
     * @param string $function API resource path
     * @param bool $auth Whether the function requires an access token
     * @param array $params Additional request parameters
     * @param string $method Request type GET|POST
     *
     * @return mixed
     *
     * @throws \MetzWeb\Instagram\InstagramException
     */
    protected function _makeCall($function, $auth = false, $params = null, $method = 'GET')
    {
        if (!$auth) {
            // if the call doesn't requires authentication
            $authMethod = '?client_id=' . $this->getApiKey();
        } else {
            // if the call needs an authenticated user
            if (!isset($this->_accesstoken)) {
                throw new InstagramException("Error: _makeCall() | $function - This method requires an authenticated users access token.");
            }
            $authMethod = '?access_token=' . $this->getAccessToken();
        }
        $paramString = null;
        if (isset($params) && is_array($params)) {
            $paramString = '&' . http_build_query($params);
        }
        $apiCall = self::API_URL . $function . $authMethod . (('GET' === $method) ? $paramString : null);
        // signed header of POST/DELETE requests
        $headerData = array('Accept: application/json');
        if ($this->_signedheader && 'GET' !== $method) {
            $headerData[] = 'X-Insta-Forwarded-For: ' . $this->_signHeader();
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, esc_url_raw( $apiCall ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, count($params));
                curl_setopt($ch, CURLOPT_POSTFIELDS, ltrim($paramString, '&'));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        $jsonData = curl_exec($ch);
        // split header from JSON data
        // and assign each to a variable
        list($headerContent, $jsonData) = explode("\r\n\r\n", $jsonData, 2);
        // convert header content into an array
        $headers = $this->processHeaders($headerContent);
        // get the 'X-Ratelimit-Remaining' header value
        $this->_xRateLimitRemaining = $headers['X-Ratelimit-Remaining'];
        if (!$jsonData) {
            throw new InstagramException('Error: _makeCall() - cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return json_decode($jsonData);
    }

    /**
     * Sign header by using the app's IP and its API secret.
     *
     * @return string The signed header
     */
    private function _signHeader()
    {
        $ipAddress = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : gethostbyname(gethostname());
        $signature = hash_hmac('sha256', $ipAddress, $this->_apisecret, false);
        return join('|', array($ipAddress, $signature));
    }
    /**
     * Read and process response header content.
     *
     * @param array
     *
     * @return array
     */
    private function processHeaders($headerContent)
    {
        $headers = array();
        foreach (explode("\r\n", $headerContent) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
                continue;
            }
            list($key, $value) = explode(':', $line);
            $headers[$key] = $value;
        }
        return $headers;
    }
    /**
     * Access Token Setter.
     *
     * @param object|string $data
     *
     * @return void
     */
    public function setAccessToken($data)
    {
        $token = is_object($data) ? $data->access_token : $data;
        $this->_accesstoken = $token;
    }
    /**
     * Access Token Getter.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accesstoken;
    }
    /**
     * API-key Setter
     *
     * @param string $apiKey
     *
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->_apikey = $apiKey;
    }
    /**
     * API Key Getter
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apikey;
    }
    /**
     * API Secret Setter
     *
     * @param string $apiSecret
     *
     * @return void
     */
    public function setApiSecret($apiSecret)
    {
        $this->_apisecret = $apiSecret;
    }
    /**
     * API Secret Getter.
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->_apisecret;
    }
    /**
     * API Callback URL Setter.
     *
     * @param string $apiCallback
     *
     * @return void
     */
    public function setApiCallback($apiCallback)
    {
        $this->_callbackurl = $apiCallback;
    }
    /**
     * API Callback URL Getter.
     *
     * @return string
     */
    public function getApiCallback()
    {
        return $this->_callbackurl;
    }
    /**
     * Enforce Signed Header.
     *
     * @param bool $signedHeader
     *
     * @return void
     */
    public function setSignedHeader($signedHeader)
    {
        $this->_signedheader = $signedHeader;
    }
}
