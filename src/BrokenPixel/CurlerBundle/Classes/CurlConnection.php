<?php
namespace BrokenPixel\CurlerBundle\Classes;
/**
 * Main class for using cURL this class has most of the main features to use 
 * cURL and wraps them in easier to use methods that make the code more
 * maintainable and readable by others
 * 
 * @author John James
 * @copyright 2014 Broken Pixel Limited
 * @link http://www.broken-pixel.co.uk
 * @version 1 2014-08-09 JJ 
 */
class CurlConnection extends ServerConnectionAbstract {

    protected $urlToCurl;

    public function __construct($url)
    {
        $this->urlToCurl = $url;
    }

    /**
     * Method to open and set a cURL connection using a url passed in to the 
     * method. This method is normally called from the constructor
     * 
     * @param $urlToCurl String
     *      Web accessible address to connect to using cURL
     * @throws \Exception
     *      Throws an exception if the connection property is 
     *      false after curl_init
     */
    protected function openConnection()
    {
        $this->serverConnection = curl_init($this->urlToCurl);
        // check to see if an errors occurred
        if ($this->serverConnection === false) {
            error_log(
                "There was an error connecting to the URL you used\r\n" 
                ."Please check and try again\r\n "
                .$this->urlToCurl
            );
            throw new \Exception(
                "There was a problem connecting to $this->urlToCurl\r\n"
                ."Please see error log for details"
            );
        }
    }
    
    /**
     * Method simple wrapper to make setting curl options for the current 
     * connection easier as you just need to pass in the option and value you 
     * want it to be set to 
     * 
     * Using the method arguments helps to write the following
     * curl_setopt($this->serverConnection, CURLOPT_FOLLOWLOCATION, true);
     * 
     * @link http://php.net/manual/en/function.curl-setopt.php
     * 
     * @param $optionToSet Constant
     *      The $optionToSet variable is to be used with the PHP manual to pass 
     *      in which constant you would like to affect for example
     *      CURLOPT_FOLLOWLOCATION
     * @param $valueToSetTo Mixed
     *      The value you want to set the $optionToSet argument to for example
     *      true
     * @throws \Exception
     *      Throws an exception if the option to set returns false 
     */ 
    public function setCurlOptions($optionToSet, $valueToSetTo)
    {
        $optionSet = curl_setopt($this->serverConnection, $optionToSet, $valueToSetTo);
        // check if the option was set or not
        if ($optionSet === false) {
            error_log(
                "There was an error setting the option you specified\r\n" 
                ."Please check and try again\r\n"
                ."The option used was".$optionToSet."\r\n"
                ."The value to set to was ".$valueToSetTo."\r\n"
            );
            throw new \Exception(
                "There was a problem setting the curl options\r\n"
                ."Please see error log for details"
            );
        }
    }   
    
    /**
     * Method for using the cURL connection with the options that have been 
     * specified prior to running this method if the option 
     * CURLOPT_RETURNTRANSFER has been set then this will return the results on
     * success else it will return true
     */
    public function executeCurl()
    {
        $executeSuccessful = curl_exec($this->serverConnection);
        $curlInfo = curl_getinfo($this->serverConnection);
        if ($executeSuccessful === false) {
            error_log(
                "There was an error using the connection with cURL\r\n" 
                ."Please check and try again\r\n"
            );
            throw new \Exception(
                "There was an error using the connection with cURL\r\n"
                ."Please see error log for details"
            );
        }
        return $executeSuccessful;
    }
    
    /**
     * Method for retrieving all the information from an executed cURL 
     * connection, this can be set to use specific information parameters
     * 
     * @link http://php.net/manual/en/function.curl-getinfo.php
     *  
     * @param $curlInformationIntOption Constant
     *      Constants that are defined for the curl_getinfo function for 
     *      example CURLINFO_EFFECTIVE_URL
     * @return Array
     *      Returns an array of information about the current cURL connection
     * @throws \Exception
     *      Throws an exception if the curl_getinfo returns false
     */

    public function getCurlInfo($curlInformationIntOption = null)
    {
        // if no $curlInformationIntOption constant has been used run with the 
        // default getinfo returned array
        if (empty($curlInformationIntOption)) {
            $curlInfo = curl_getinfo($this->serverConnection);
        } else {
            $curlInfo = curl_getinfo($this->serverConnection, $curlInformationIntOption);
        }
        // check to see if curl_getinfo was successful
        if ($curlInfo === false) {
            error_log(
                "There was an error retrieving information about the " 
                ."connection with cURL\r\n" 
                ."Please check and try again\r\n"
            );
            throw new \Exception(
                "There was an error retrieving information about the "
                ."connection with cURL\r\n"
                ."Please see error log for details"
            );
        }
        return $curlInfo;
    }


    public function getLastErrorMessage()
    {
        return curl_error($this->serverConnection);
    }
    
    /**
     * Method to close the current objects cURL connection, usually called be 
     * the destructor method 
     */
    protected function closeConnection()
    {
        curl_close($this->serverConnection);
    }
}
