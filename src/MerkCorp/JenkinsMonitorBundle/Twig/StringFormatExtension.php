<?php
/**
 * Created by PhpStorm.
 * User: merkury
 * Date: 15/07/2017
 * Time: 02:22
 */

namespace MerkCorp\JenkinsMonitorBundle\Twig;


class StringFormatExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('unCamelCase', array($this, 'unCamelCaseFilter')),
        );
    }

    /**
     * Function to convert camelCase en camel case
     * @param $cameledCaseString
     */
    public function unCamelCaseFilter($cameledCaseString)
    {

        $words = preg_split('/(?=[A-Z])/',$cameledCaseString);
        return implode(' ', $words);



    }

    public function getName()
    {
        return 'format_extension';
    }


}