<?php
namespace BrokenPixel\CurlerBundle\Classes;

/**
 * object to make cURL functions more accesible, easier to use and wrapped up
 * in to a simple interface
 * 
 * @see Php\Classes\ServerConnection
 * 
 * @author John James
 * @copyright 2014 Broken Pixel Limited
 * @link http://www.broken-pixel.co.uk
 * @version 1 2014-08-09 JJ 
 */
class CurlHelper extends CurlConnection
{

    /**
     * Object constructor overridden from parent used to create a connection 
     * to a website
     * 
     * @see Php\Classes\ServerConnection
     * 
     * @param $url
     *      Web accessible address to connect to
     */
    public function __construct($url) 
    {
        // run parent method to create the connection
        parent::__construct($url);
        $this->openConnection();
        // make sure the the full page is retrieved
        $this->setCurlOptions(CURLOPT_HEADER, 1);
        $this->setCurlOptions(CURLOPT_NOBODY, 0);
        // do not follow links render the first page 
        $this->setCurlOptions(CURLOPT_FOLLOWLOCATION, 0); 
        // set the user agent to chrome for standard web testing
        $this->setCurlOptions(
            CURLOPT_USERAGENT, 
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 '
            .'(KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36'
        );
        $this->setCurlOptions(CURLOPT_RETURNTRANSFER, 1);
        $this->setCurlOptions(CURLOPT_VERBOSE, true);
        // setup to retrieve the apache header
        $this->setCurlOptions(CURLINFO_HEADER_OUT, true);
    }

    /**
     * method for changing the user agent of the objects connection to appear 
     * as an android mobile phone
     * 
     * @see Php\Classes\ServerConnection
     */
    public function setMobileSettingsOn() 
    {
        $this->setCurlOptions(
            CURLOPT_USERAGENT, 
            'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L '
            .'Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) '
            .'Version/4.0 Mobile Safari/534.30'
        );
    }
    
    /**
     * Method that changes the current connection object to use a proxy to get 
     * to its end destination used for testing with a different IP address
     * 
     * @see Php\Classes\ServerConnection
     * 
     * @param $countryProxy String
     *      Public IPv4 Address of the proxy you want to connect through
     * @param $proxyPassword String
     *      Password to be used for the proxy
     *      By default if not specified is null
     */
    public function useAProxy($countryProxy, $proxyPassword = null)
    {
        $this->setCurlOptions(CURLOPT_PROXY, $countryProxy);
        if (!empty($proxyPassword)) {
            $this->setCurlOptions(CURLOPT_PROXYUSERPWD, $proxyPassword);
        }
    }
    
    /**
     * Method for getting the html/http header only so that you can get
     * the status code back without any of the html
     *
     * @see Php\Classes\ServerConnection
     */
    public function setHeaderOnly()
    {
        $this->setCurlOptions(CURLOPT_HEADER, 1);
        $this->setCurlOptions(CURLOPT_NOBODY, 1);
    }

    /**
     * Method for allowing the CURL object to follow redirects to fully traverse
     * a link
     *
     * @see Php\Classes\ServerConnection
     */
    public function setFollowLinksOn()
    {
        $this->setCurlOptions(CURLOPT_FOLLOWLOCATION, 1);
    }

    /**
     * Method to dump html recieved from CURL to the browser to be rendered
     *
     * This will not work if setHeaderOnly() has been used on the current
     * connection
     */
    public function setOutputToBrowser()
    {
        $this->setCurlOptions(CURLOPT_RETURNTRANSFER, 0);
    }

    /**
     * Method to change the URL to use for CURL of the connection object this
     * will need to be done before the parent method executeCurl() is called
     *
     * @see Php\Classes\ServerConnection
     *
     * @param $urlToUse String
     *      The web accessible URL you want to change the connection object
     *      to use http://www.broken-pixel.co.uk/
     */
    public function changeUrlToCurl($urlToUse)
    {
        $this->setCurlOptions(CURLOPT_URL, $urlToUse);
    }

    /**
     * Simple method to stop cURL from verifying the peer's certificate
     *
     * @see Php\Classes\ServerConnection
     */
    public function doNotVerifySsl()
    {
        $this->setCurlOptions(CURLOPT_SSL_VERIFYPEER, false);
    }

}
