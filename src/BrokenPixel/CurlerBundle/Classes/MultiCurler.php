<?php
namespace BrokenPixel\CurlerBundle\Classes;

class MultiCurler extends MultiCurlHelper
{
    protected $arrayOfCurls;
    protected $previousArrayOfCurls;

    public function __construct(
        $arrayOfUrlsToCurl,
        $arrayOfCurlHandleOptions = null,
        $previousArrayOfCurls = null
    ) {
        parent::__construct($arrayOfUrlsToCurl, $arrayOfCurlHandleOptions);

        // set object properties for testing detail
        $this->previousArrayOfCurls = $previousArrayOfCurls;

        $this->openConnection();

        $this->addUrlsToMultiCurl($arrayOfUrlsToCurl);
        $this->executeConnection();
    }

    public function getAllMultiCurlInformation($body = false)
    {
        $multiCurlerHandles = $this->getArrayOfCurlHandles();
        if ($body == false) {
            foreach ($multiCurlerHandles as $curlHandleIndex => $curlInfo) {
                $this->arrayOfCurls[$curlHandleIndex] = $this->getCurlInfo($curlInfo['handle']);
            }
            $multiCurlerReturn = $this->arrayOfCurls;
        } else {
            foreach ($multiCurlerHandles as $curlHandleIndex => $curlInfo) {

                $curlRequestInformation = $this->getCurlInfo($curlInfo['handle']);
                $curlResponse = $this->getMultiCurlBody($curlInfo['handle']);

                $headerSize = $curlRequestInformation['header_size'];
                $header = substr($curlResponse, 0, $headerSize);
                $body = substr($curlResponse, $headerSize);

                $headerArray = array();
                $headers = explode("\n", $header);

                $headerArray['HTTP-Status'] = $headers[0];

                array_shift($headers);
                foreach ($headers as $individualHeader) {
                    $headerSplit = explode(": ", $individualHeader, 2);
                    if (!empty($headerSplit[0]) && $headerSplit[0] != ''
                        && $headerSplit[0] != "\n"
                        && $headerSplit[0] != "\r\n"
                        && $headerSplit[0] != "\r"
                    ) {
                        if(array_key_exists(1, $headerSplit)) {
                            $headerArray[trim($headerSplit[0])] = trim($headerSplit[1]);
                        }
                    }
                }
                $this->arrayOfCurls[$curlHandleIndex]['header'] = $headerArray;
                $this->arrayOfCurls[$curlHandleIndex]['body'] = $body;
                $this->arrayOfCurls[$curlHandleIndex]['curlInfo'] = $curlRequestInformation;
            }
            $multiCurlerReturn = $this->arrayOfCurls;
        }
        return $multiCurlerReturn;
    }
}