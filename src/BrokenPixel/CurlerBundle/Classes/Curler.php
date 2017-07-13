<?php
namespace BrokenPixel\CurlerBundle\Classes;


class Curler extends CurlHelper
{
    public function executeCurl()
    {
        $arrayOfCurlInformation = array();
        curl_setopt($this->serverConnection, CURLOPT_FOLLOWLOCATION, true);
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
        // get details about the header to strip it out of the body response
        $headerSize = $curlInfo['header_size'];
        $header = substr($executeSuccessful, 0, $headerSize);
        $body = substr($executeSuccessful, $headerSize);

        $headerArray = array();
        $headers = explode("\n",$header);

        $headerArray['HTTP-Status'] = $headers[0];

        array_shift($headers);
        foreach($headers as $individualHeader){
            $headerSplit = explode(": ",$individualHeader,2);
            if (!empty($headerSplit[0]) && $headerSplit[0] != ''
                && $headerSplit[0] != "\n"
                && $headerSplit[0] != "\r\n"
                && $headerSplit[0] != "\r"
            ) {
                if(count($headerSplit) > 1){
                    $headerArray[trim($headerSplit[0])] = trim($headerSplit[1]);
                }
            }
        }

        $arrayOfCurlInformation['curlInfo'] = $curlInfo;
        $arrayOfCurlInformation['header'] = $headerArray;
        $arrayOfCurlInformation['body'] = $body;
        return $arrayOfCurlInformation;
    }
}