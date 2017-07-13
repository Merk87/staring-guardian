<?php
namespace BrokenPixel\CurlerBundle\Classes;

class MultiCurlConnection extends ServerConnectionAbstract
{
    public $arrayOfUrlsToCurl = array();
    public $arrayOfCurlHandles = array();

    public function __construct($arrayOfUrlsToCurl)
    {
        $this->arrayOfUrlsToCurl = $arrayOfUrlsToCurl;
    }

    public function openConnection()
    {
        $this->serverConnection = curl_multi_init();
        // check to see if an errors occurred
        if ($this->serverConnection === false) {
            error_log(
                "There was an error opening the multi curl handle\r\n"
                ."Please check the array of URL and the MultiCurlConnection class and try again\r\n"
            );
            throw new \Exception(
                "There was an error opening the multi curl handle\r\n"
                ."Please check the array of URL and the MultiCurlConnection class and try again\r\n"
            );
        }
    }

    public function addCurlHandleToMultiConnection($urlToCurl, $curlOptionsArray)
    {
        $curlHandle = curl_init($urlToCurl);
        foreach ($curlOptionsArray as $curlConstant => $constantValue) {
            $this->setCurlHandleOptions($curlHandle, $curlConstant, $constantValue);
        }
        curl_multi_add_handle($this->serverConnection, $curlHandle);
    }

    public function executeConnection()
    {
        do {
            curl_multi_exec($this->serverConnection, $executionsRemaining);
            $status = curl_multi_info_read($this->serverConnection);
            if ($status != false) {
                $this->arrayOfCurlHandles[] = $status;
            }
        } while($executionsRemaining > 0);
    }

    public function getMultiCurlStatus()
    {
        return curl_multi_info_read($this->serverConnection);
    }

    protected function closeConnection()
    {
        curl_multi_close($this->serverConnection);
    }

    public function setCurlHandleOptions($curlResource, $optionToSet, $valueToSetTo)
    {
        $optionSet = curl_setopt($curlResource, $optionToSet, $valueToSetTo);
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

    protected function setMultiCurlOptions($optionToSet, $valueToSetTo)
    {
        $optionSet = curl_multi_setopt($this->serverConnection, $optionToSet, $valueToSetTo);
        // check if the option was set or not
        if ($optionSet === false) {
            error_log(
                "There was an error setting the multi option you specified\r\n"
                ."Please check and try again\r\n"
                ."The option used was".$optionToSet."\r\n"
                ."The value to set to was ".$valueToSetTo."\r\n"
            );
            throw new \Exception(
                "There was a problem setting the multi curl options\r\n"
                ."Please see error log for details"
            );
        }
    }

    /**
     * @return array
     */
    public function getArrayOfCurlHandles()
    {
        return $this->arrayOfCurlHandles;
    }


    /**
     * @param $curlHandle
     * @param null $curlInformationIntOption
     * @return mixed
     * @throws \Exception
     */
    public function getCurlInfo($curlHandle, $curlInformationIntOption = null)
    {
        // if no $curlInformationIntOption constant has been used run with the
        // default getinfo returned array
        if (empty($curlInformationIntOption)) {
            $curlInfo = curl_getinfo($curlHandle);
        } else {
            $curlInfo = curl_getinfo($curlHandle, $curlInformationIntOption);
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

    public function getMultiCurlBody($curlHandle)
    {
        return curl_multi_getcontent($curlHandle);
    }
}