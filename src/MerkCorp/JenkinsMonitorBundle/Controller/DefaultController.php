<?php

namespace MerkCorp\JenkinsMonitorBundle\Controller;

use Doctrine\ORM\Query\AST\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * Function to retrieve all the defined projects specified in the
     * parameters.yml or directly get all of them.
     * @Route("/", name="wk_index")
     * @Template()
     */
    public function indexAction() : array
    {

        $projectTypes = $this->getParameter('api_type_projects');
        $jenkinsJobs = array();

        if(!empty($projectTypes)) {
            foreach($projectTypes as $projectKey => $projectType) {
                $jenkinsJobs[$projectKey] = $this->getProjects($projectType);
            }
        }else{
            $jenkinsJobs['all'] = $this->getProjects();
        }

        return array(
            'jenkins_jobs' => $jenkinsJobs
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
        $urlToCurl = $this->generateBaseUrl().'job/'.$projectName.'/lastBuild/api/json';

        if($statusOnly) {
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
    public function remoteBuildJobAction(string $projectName){
        $curler = $this->get('curler');
        $urlToCurl = $this->generateBaseUrl().'job/'.$projectName.'/build';

        $curler->curlAUrl($urlToCurl);
        return new JsonResponse("Build triggered...");
    }

    /**
     * Function to compose the base url for the Jenkins' JSON API based in
     * the information supplied in the parameters.yml
     * @return string
     */
    private function generateBaseUrl() : string
    {
        return $this->getParameter('api_schema')
            . '://'
            . $this->getParameter('api_user')
            .':' . $this->getParameter('api_token')
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
    private function getProjects(string $projectType = null) : array
    {
        $curler = $this->get('curler');
        $baseUrl = $this->generateBaseUrl();

        $urlToCurl = $baseUrl.'api/json';
        if($projectType) {
            $urlToCurl = $baseUrl.'view/'.rawurlencode($projectType).'/api/json';
        }

        $liveProjectsResult = $curler->curlAUrl($urlToCurl);

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
    private function getLastBuildInformation(array $liveProjects) : array
    {
        // We iterate over the array passing the item by reference to being
        // able to manipulate it.
        array_walk($liveProjects, function(&$item) {

            $curler = $this->get('curler');
            $urlToCurl = $this->generateBaseUrl().'job/'.$item['name'].'/lastBuild/api/json';

            $lastBuildInfoResult = $curler->curlAUrl($urlToCurl);
            // We add a new key to store all the build information
            $item['lastBuildInfo'] = json_decode($lastBuildInfoResult['body'], true);
        });

        return $liveProjects;
    }

}

