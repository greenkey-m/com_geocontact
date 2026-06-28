<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\View\Geocontacts;

// No direct access
defined('_JEXEC') or die;

use Exception;
use Greenkey\Component\Geocontact\Site\Helper\AccessHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use RuntimeException;

/**
 * Geocontact list view
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 * @since  1.6
	 */
	protected array $items = [];

    /**
     * The access helper
     * @since  1.6
     */
    protected AccessHelper $access;

    /**
     * The authorization status
     * @since  1.6
     */
    protected array $authorised;

	/**
	 * The pagination object
	 * @since  1.6
	 */
	protected Pagination $pagination;

	/**
	 * The model state
	 * @since  1.6
	 */
	protected object $state;

	/**
	 * The component params
	 */
	protected $params;

	/**
	 * @param null $tpl
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
        $app = Factory::getApplication();
        if (!$app) {
            throw new RuntimeException('Error app');
        }
        $user = $app->getIdentity();

        $this->params = $app->getParams('com_geocontact');

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->authorised = [
            'create' => $user->authorise('core.create', 'com_geocontact'),
            'edit' => $user->authorise('core.edit', 'com_geocontact')
        ];

        if (count($errors = $this->get('Errors'))) {
            throw new RuntimeException(implode("\n", $errors));
        }

        $this->setupDocument();

        $this->towns = $this->items;

        parent::display($tpl);
	}

    /**
     * @return  void
     * @throws Exception
     */
    protected function setupDocument()
    {
        $app = Factory::getApplication();
        if ($app === null) {
            return;
        }
        if (!$document = $app->getDocument()) {
            return;
        }
	    $wa = $document->getWebAssetManager();
	    $wa->registerAndUseStyle('my-style', 'components/com_geocontact/assets/css/geocontact.css');
	    $wa->registerAndUseScript('my-script', 'components/com_geocontact/assets/js/list.js');

        $menus = $app->getMenu();
        if ($menus === null) {
            return;
        }

        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
        }

        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ((int)$app->get('sitename_pagetitles', 0) === 1) {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ((int)$app->get('sitename_pagetitles', 0) === 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->getDocument()->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->getDocument()->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->getDocument()->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->getDocument()->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
