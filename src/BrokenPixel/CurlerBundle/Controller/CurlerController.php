<?php
namespace BrokenPixel\CurlerBundle\Controller;

use BrokenPixel\CurlerBundle\Classes\Curler;
use BrokenPixel\CurlerBundle\Classes\MultiCurler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CurlerController extends Controller
{
    public function curlAUrl($url, $postData = null)
    {
        $curler = new Curler($url);
        $curler->setCurlOptions(CURLOPT_POSTFIELDS, $postData);
        $curlResponse = $curler->executeCurl();
        return $curlResponse;
    }

    public function curlUrls($urls, $postData = null)
    {
        $multiCurler = new MultiCurler(
            $urls,
            null,
            null);

        $multiCurler->setCurlOptions(CURLOPT_POSTFIELDS, $postData);
        $multiCurlResponse = $multiCurler->executeConnection();
        return $multiCurler;
    }
}
