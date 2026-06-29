<?php
/**
 * @package     com_geocontact
 * @version     6.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;

/**
 * Geocontact list controller
 */
class GeocontactsController extends AdminController
{
    public function uploadxml()
    {
        $this->checkToken();

        $model = $this->getModel('Geocontacts');
        $count = $model->loadItems();

        $this->setMessage(Text::sprintf('COM_GEOCONTACT_N_ITEMS_IMPORTED', $count));
        $this->setRedirect(Route::_('index.php?option=com_geocontact&view=geocontacts', false));
    }

    public function downloadxml()
    {
        $this->checkToken();

        $this->setMessage(Text::_('COM_GEOCONTACT_EXPORT_NOT_AVAILABLE'), 'warning');
        $this->setRedirect(Route::_('index.php?option=com_geocontact&view=geocontacts', false));
    }
}
