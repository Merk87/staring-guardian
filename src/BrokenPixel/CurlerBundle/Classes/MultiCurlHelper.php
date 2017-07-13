<?php
namespace BrokenPixel\CurlerBundle\Classes;
use BrokenPixel\CurlerBundle\Classes\MultiCurlConnection;

class MultiCurlHelper extends MultiCurlConnection
{
    protected $curlConstantsForEachConnection = array();
    protected $multiCurlConstantsForEachConnection = array();

    public function __construct($arrayOfUrlsToCurl, $curlHandleOptions = null)
    {
        // check what version of php is begin used to see if multi curl options
        // can be set

        parent::__construct($arrayOfUrlsToCurl);
        $this->defineCurlOptions();
        if (!empty($curlHandleOptions)) {
            foreach ($curlHandleOptions as $option => $optionValue) {
                $this->setCurlOptions($option, $optionValue);
            }

        }
        $this->defineMultiCurlOptions();
    }

    public function addUrlsToMultiCurl($urlArray)
    {
        foreach ($urlArray as $urlToUseForCurl)
        {
            $this->addCurlHandleToMultiConnection($urlToUseForCurl, $this->curlConstantsForEachConnection);
        }
    }

    protected function defineCurlOptions()
    {
        // make sure the the full page is retrieved
        $this->setCurlOptions(CURLOPT_HEADER, 1);
        $this->setCurlOptions(CURLOPT_NOBODY, 0);
        // do not follow links render the first page
        $this->setCurlOptions(CURLOPT_FOLLOWLOCATION, 1);
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
    
    protected function defineMultiCurlOptions()
    {
        $this->setMultiCurlOptions(CURLMOPT_MAXCONNECTS, 20);
    }


    /**
     * Method to dump html received from CURL to the browser to be rendered
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

    public function setCurlOptions($curlConstant, $curlConstantValue)
    {
        $this->curlConstantsForEachConnection[$curlConstant] = $curlConstantValue;
    }

    public function setMultiCurlOptions($multiCurlConstant, $multiCurlConstantValue)
    {
        $this->multiCurlConstantsForEachConnection[$multiCurlConstant] = $multiCurlConstantValue;
    }
}