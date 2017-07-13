<?php

namespace MerkCorp\JenkinsMonitorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction() : array
    {

        $projectTypes = $this->getParameter('api_type_projects');
        $jenkinsJobs = array();

        if(!empty($projectTypes)) {
            foreach($projectTypes as $projectKey => $projectType) {
                $jenkinsJobs[$projectKey] = $this->arrangeJobs($this->getProjects($projectType));
            }
        }else{
            $jenkinsJobs['all'] = $this->arrangeJobs($this->getProjects());
        }

        return array(
            'jenkins_jobs' => $jenkinsJobs
        );
    }

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
     * Function to retrieve projects the live projects
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

    private function arrangeJobs($liveProjects) : array
    {
        array_walk($liveProjects, function(&$item) {

            $item['lastBuildInfo'] = $this->getLastBuildInfo($item);

        });

        return $liveProjects;
    }

    /**
     * @param $item
     * @return array
     */
    private function getLastBuildInfo($item)
    {
        $curler = $this->get('curler');
        $urlToCurl = $this->generateBaseUrl().'job/'.$item['name'].'/lastBuild/api/json';
        $lastBuildInfoResult = $curler->curlAUrl($urlToCurl);
        $lastBuildInfoArr = json_decode($lastBuildInfoResult['body'], true);

        $changeSet = $lastBuildInfoArr;
        return $changeSet;
    }
}
