<?php

namespace MerkCorp\JenkinsMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JenkinsInstance
 *
 * @ORM\Table(name="jenkins_instance")
 * @ORM\Entity(repositoryClass="MerkCorp\JenkinsMonitorBundle\Repository\JenkinsInstanceRepository")
 */
class JenkinsInstance
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="api_user", type="string", length=255)
     */
    private $apiUser;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=255)
     */
    private $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(name="api_domain", type="string", length=255)
     */
    private $apiDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="api_schema", type="string", length=255)
     */
    private $apiSchema;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return JenkinsInstance
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set apiUser
     *
     * @param string $apiUser
     *
     * @return JenkinsInstance
     */
    public function setApiUser($apiUser)
    {
        $this->apiUser = $apiUser;

        return $this;
    }

    /**
     * Get apiUser
     *
     * @return string
     */
    public function getApiUser()
    {
        return $this->apiUser;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return JenkinsInstance
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set apiDomain
     *
     * @param string $apiDomain
     *
     * @return JenkinsInstance
     */
    public function setApiDomain($apiDomain)
    {
        $this->apiDomain = $apiDomain;

        return $this;
    }

    /**
     * Get apiDomain
     *
     * @return string
     */
    public function getApiDomain()
    {
        return $this->apiDomain;
    }

    /**
     * Set apiSchema
     *
     * @param string $apiSchema
     *
     * @return JenkinsInstance
     */
    public function setApiSchema($apiSchema)
    {
        $this->apiSchema = $apiSchema;

        return $this;
    }

    /**
     * Get apiSchema
     *
     * @return string
     */
    public function getApiSchema()
    {
        return $this->apiSchema;
    }
}
