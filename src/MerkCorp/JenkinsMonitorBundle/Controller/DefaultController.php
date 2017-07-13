<?php

namespace MerkCorp\JenkinsMonitorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * Function to retrieve all the defined projects specified in the
     * parameters.yml or directly get all of them.
     * @Route("/")
     * @Template()
     */
    public function indexAction() : array
    {

        $projectTypes = $this->getParameter('api_type_projects');
        $jenkinsJobs = array();

        if(!empty($projectTypes)) {
            foreach($projectTypes as $projectKey => $projectType) {
                $jenkinsJobs[$projectKey] = $this->getLastBuildInformation($this->getProjects($projectType));
            }
        }else{
            $jenkinsJobs['all'] = $this->getLastBuildInformation($this->getProjects());
        }

        return array(
            'jenkins_jobs' => $jenkinsJobs
        );
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
