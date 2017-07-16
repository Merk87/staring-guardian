<?php

namespace MerkCorp\JenkinsMonitorBundle\Controller;

use Doctrine\ORM\Query\AST\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * Function to retrieve all the defined projects specified in the
     * parameters.yml or directly get all of them.
     * @Route("/", name="wk_index")
     * @Template()
     */
    public function indexAction()
    {

        $projectTypes = $this->getParameter('api_type_projects');
        $jenkinsJobs = array();

        if (!empty($projectTypes)) {
            foreach ($projectTypes as $projectKey => $projectType) {
                $jenkinsJobs[$projectKey] = $this->getProjects($projectType);
                if (array_key_exists('error', $jenkinsJobs[$projectKey]) && $jenkinsJobs[$projectKey]['error'] === true) {
                    return $this->redirectToRoute('wk_curl_error_page', array(
                        'errorCode' => $jenkinsJobs[$projectKey]['http_code'],
                    ));
                }
            }
        } else {
            $jenkinsJobs['all'] = $this->getProjects();
            if (array_key_exists('error', $jenkinsJobs['all']) && $jenkinsJobs['all']['error'] === true) {
                return $this->redirectToRoute('wk_curl_error_page', array(
                    'errorCode' => $jenkinsJobs['all']['http_code'],
                ));
            }
        }

        return array(
            'jenkins_jobs' => $jenkinsJobs
        );
    }

    /**
     * Error page to handle curl bad responses.
     * @param $errorCode
     * @Route("/curl/error/{errorCode}", name="wk_curl_error_page")
     * @Template()
     * @return array
     */

    public function errorPageAction($errorCode)
    {
        return array(
            'error' => $errorCode
        );
    }

    /**
     * @Route(
     *     "/{projectName}/get/information/{statusOnly}",
     *     name="wk_ajax_project_status")
     * @Method({"GET"})
     * @param string $projectName
     * @return JsonResponse
     */
    public function projectExtendedInformationAction(string $projectName, bool $statusOnly = false)
    {
        $curler = $this->get('curler');
        $urlToCurl = $this->generateBaseUrl() . 'job/' . $projectName . '/lastBuild/api/json';

        if ($statusOnly) {
            $urlToCurl .= '?tree=result';
        }

        $lastBuildInfoResult = $curler->curlAUrl($urlToCurl);
        // We add a new key to store all the build information
        $result = json_decode($lastBuildInfoResult['body'], true);
        return new JsonResponse($result);
    }

    /**
     * @Route("/{projectName}/trigger/build", name="wk_ajax_build_project")
     * Function to trigger build request on Jenkins.
     * @param string $projectName
     * @return JsonResponse
     */
    public function remoteBuildJobAction(string $projectName)
    {
        $curler = $this->get('curler');
        $urlToCurl = $this->generateBaseUrl() . 'job/' . $projectName . '/build';

        $curler->curlAUrl($urlToCurl);
        return new JsonResponse("Build triggered...");
    }

    /**
     * Function to compose the base url for the Jenkins' JSON API based in
     * the information supplied in the parameters.yml
     * @return string
     */
    private function generateBaseUrl(): string
    {
        return $this->getParameter('api_schema')
            . '://'
            . $this->getParameter('api_user')
            . ':' . $this->getParameter('api_token')
            . '@'
            . $this->getParameter('api_domain');
    }

    /**
     * Function to retrieve projects based in the project type defined
     * in the parameters.yml
     *
     * @param string|null $projectType
     *
     * @return array
     */
    private function getProjects(string $projectType = null): array
    {
        $curler = $this->get('curler');
        $baseUrl = $this->generateBaseUrl();

        $urlToCurl = $baseUrl . 'api/json';
        if ($projectType) {
            $urlToCurl = $baseUrl . 'view/' . rawurlencode($projectType) . '/api/json';
        }

        $liveProjectsResult = $curler->curlAUrl($urlToCurl);

        $curlCheck = $this->manageCurlHttpResponseCodes($liveProjectsResult['curlInfo']);

        if ($curlCheck['error'] === true) {
            return $curlCheck;
        }

        $liveProjectsArr = json_decode($liveProjectsResult['body'], true);


        return $liveProjectsArr['jobs'];
    }

    /**
     * Function to add to the current array of project all the information
     * related to the last build.
     * @param $liveProjects
     *
     * @return array
     */
    private function getLastBuildInformation(array $liveProjects): array
    {
        // We iterate over the array passing the item by reference to being
        // able to manipulate it.
        array_walk($liveProjects, function (&$item) {

            $curler = $this->get('curler');
            $urlToCurl = $this->generateBaseUrl() . 'job/' . $item['name'] . '/lastBuild/api/json';

            $lastBuildInfoResult = $curler->curlAUrl($urlToCurl);
            // We add a new key to store all the build information
            $item['lastBuildInfo'] = json_decode($lastBuildInfoResult['body'], true);
        });

        return $liveProjects;
    }

    private function manageCurlHttpResponseCodes($curlInfo)
    {
        $result = array(
            'error' => null,
            'message' => null,
            'http_code' => 0,
        );

        switch ($curlInfo['http_code']) {
            case 403:
                $result['http_code'] = 403;
                $result['error'] = true;
                return $result;
                break;
            case 200:
                $result['http_code'] = 200;
                $result['error'] = false;
                break;
            default:
                $result['http_code'] = $curlInfo['http_code'];
                $result['error'] = true;

        }

        return $result;
    }

}

