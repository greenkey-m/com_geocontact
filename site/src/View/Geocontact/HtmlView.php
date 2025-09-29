<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\View\Geocontact;

// No direct access
defined('_JEXEC') or die;

use Exception;
use Greenkey\Component\Geocontact\Site\Helper\AccessHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use RuntimeException;

/**
 * Geocontact detail view
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The active item
     *
     * @var    object
     * @since  1.5
     */
    protected object $item;

    /**
     * The pagination object
     *
     * @var    ?Pagination
     * @since  1.6
     */
    protected ?Pagination $pagination;

    /**
     * The form object
     *
     * @var    object
     * @since  1.5
     */
    protected $form;

    /**
     * The model state
     *
     * @var    object
     * @since  1.5
     */
    protected object $state;

    /**
     * The component params
     *
     * @var    Registry
     * @since  1.5
     */
    protected Registry $params;

	/**
	 * @throws Exception
	 */
    public function display($tpl = null): void
    {
		$app = Factory::getApplication();

        $this->form 				= $this->get('Form');
        $this->state 				= $this->get('State');
        $this->item 				= $this->get('Item');
        $this->pagination           = $this->get('pagination');

        $this->params 				= $app->getParams('com_geocontact');

        /*
        $morph = json_decode(file_get_contents('http://morphos.tech/api/inflect-geographical-name?name='.$this->item->caption.'&_format=json'), true);
        //print_r($morph);
        $this->caption_morph1 = $morph['cases'][1];
        $this->caption_morph2 = $morph['cases'][2];
        $this->caption_morph5 = $morph['cases'][5];

        // Preparing description
        switch ($this->item->category_title) {
            case "Калужская область": $this->item->category_title = "Калужской области";
                break;
            case "Московская область": $this->item->category_title = "Московской области";
                break;
            case "Прочие регионы": $this->item->category_title = "Калужской, Московской, Тульской области";
                break;
        }
        $this->item->description = str_replace("{caption}", $this->item->caption, $this->item->description);
        $this->item->description = str_replace("{caption1}", $this->caption_morph1, $this->item->description);
        $this->item->description = str_replace("{caption2}", $this->caption_morph2, $this->item->description);
        $this->item->description = str_replace("{caption5}", $this->caption_morph5, $this->item->description);
        $this->item->description = str_replace("{phones}", $this->item->phones, $this->item->description);
        $this->item->description = str_replace("{stand}", $this->item->stand, $this->item->description);
        $this->item->description = str_replace("{name}", $this->item->name, $this->item->description);
        $this->item->description = str_replace("{address}", $this->item->address, $this->item->description);
        $this->item->description = str_replace("{category}", $this->item->category_title, $this->item->description);

        preg_match_all("/\{([^\{\}|]*)\|([^\{\}|]*)\}/", $this->item->description, $this->regs);
        $selecting = $this->regs[0];
        $os = array('a', 'b', 'k', 'l', 'e', 'x', 'y', 'w', 'z', 'i', 'p', 'm', 'w');
        foreach ($selecting as $i => $s) {
            if (in_array($this->item->category_alias[$i], $os)) {
                $x = 1;
            } else {
                $x = 2;
            };
            $this->item->description = str_replace($s, $this->regs[$x][$i], $this->item->description);
        }

         */


        if (count($errors = $this->get('Errors'))) {
            throw new RuntimeException(implode("\n", $errors));
        }

        $this->setupDocument();

        /*
        $xmlfile = JURI::base() . 'administrator/components/com_geocontact/towns.xml';
        //echo $xmlfile;
        //$this->towns = JFactory::getXML($xmlfile, true);
        $this->towns = simplexml_load_file($xmlfile);
        //$this->towns = $this->towns[2]->attributes();
         */

        // TODO: restore towns, или изменить подачу! в шаблон
        $this->towns = [];

        parent::display($tpl);
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function hasAccess(): bool
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        if($this->_layout === 'edit') {
            $isEdit = ($app->input->getInt('id', 0) || $this->params->get('id'));
            if ($isEdit) {
                $authorised = $user->authorise('core.edit', 'com_geocontact');
                $access = new AccessHelper();
                $access->preloadOwnRecords('#__geocontact_geocontacts');
                if ($access->canAccessOwnRecord()) {
                    return true;
                }
            } else {
                $authorised = $user->authorise('core.create', 'com_geocontact');
            }
            if ($authorised !== true) {
                $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
                return false;
            }
        }

        return true;
    }

    /**
     * @return  void
     *
     * @throws \Exception
     * @since   1.6
     */
    protected function setupDocument(): void
    {
        $app = Factory::getApplication();
        if ($app === null) {
            return;
        }
        $document = $app->getDocument();
        if ($document === null) {
            return;
        }
	    $wa = $document->getWebAssetManager();
	    $wa->registerAndUseStyle('my-style', 'components/com_geocontact/assets/css/geocontact.css');
	    $wa->registerAndUseScript('my-script', 'components/com_geocontact/assets/js/detail.js');

        $menus = $app->getMenu();
        if ($menus === null) {
            return;
        }
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', Text::_('COM_GEOCONTACT_DEFAULT_PAGE_TITLE'));
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
