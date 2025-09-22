<?php

namespace Greenkey\Component\Geocontact\Site\Service;

use Joomla\CMS\Categories\Categories;

\defined('_JEXEC') or die;

class Category extends Categories
{

    public function __construct($options = array())
    {
        $options['table']     = '#__geocontact_geocontacts';
        $options['extension'] = 'com_geocontact';

        parent::__construct($options);
    }
}