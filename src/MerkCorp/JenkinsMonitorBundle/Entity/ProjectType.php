<?php

namespace MerkCorp\JenkinsMonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectType
 *
 * @ORM\Table(name="project_type")
 * @ORM\Entity(repositoryClass="MerkCorp\JenkinsMonitorBundle\Repository\ProjectTypeRepository")
 */
class ProjectType
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="JenkinsInstance")
     * @ORM\JoinColumn(name="jenkins_instance_id", referencedColumnName="id")
     */
    private $jenkinsInstance;


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
     * @return ProjectType
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
     * Set jenkinsInstanceId
     *
     * @param JenkinsInstance $jenkinsInstance
     *
     * @return ProjectType
     */
    public function setJenkinsInstance(JenkinsInstance $jenkinsInstance = null)
    {
        $this->jenkinsInstance = $jenkinsInstance;

        return $this;
    }

    /**
     * Get jenkinsInstanceId
     *
     * @return int
     */
    public function getJenkinsInstance()
    {
        return $this->jenkinsInstance;
    }
}
