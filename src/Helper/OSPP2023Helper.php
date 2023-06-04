<?php


namespace Joomla\Module\OSPP2023\Site\Helper;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Content\Site\Model\ArticlesModel;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class OSPP2023Helper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Retrieve a list of article
     *
     * @param   Registry       $params  The module parameters.
     * @param   ArticlesModel  $model   The model.
     *
     * @return  mixed
     */
    public function getArticles(Registry $params, SiteApplication $app)
    {
        // Get the Dbo and User object
        $db   = $this->getDatabase();
        $user = $app->getIdentity();

        /** @var ArticlesModel $model */
        $model = $app->bootComponent('com_content')->getMVCFactory()->createModel('Articles', 'Site', ['ignore_request' => true]);

        // Set application parameters in model
        $model->setState('params', $app->getParams());

        $model->setState('list.start', 0);
        $model->setState('filter.published', 1);

        //Set the number of articles to 3
        $model->setState('list.limit', 3);
        // This module does not use tags data
        $model->setState('load_tags', false);

        // Access filter
        $access     = !ComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = Access::getAuthorisedViewLevels($user->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        $model->setState('filter.category_id', $params->get('catid', []));

        // State filter
        $model->setState('filter.condition', 1);


        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());


        $dir      = 'DESC';

        //order articles by last modified
        $model->setState('list.ordering', 'a.modified');
        $model->setState('list.direction', $dir);

        $items = $model->getItems();

        foreach ($items as &$item) {
            $item->slug    = $item->id . ':' . $item->alias;

            if ($access || \in_array($item->access, $authorised)) {
                // We know that user has the privilege to view the article
                $item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
            } else {
                $item->link = Route::_('index.php?option=com_users&view=login');
            }
        }

        return $items;
    }

    /**
     * Retrieve a list of articles
     *
     * @param   Registry       $params  The module parameters.
     * @param   ArticlesModel  $model   The model.
     *
     * @return  mixed
     */
    public static function getList(Registry $params, ArticlesModel $model)
    {
        return (new self())->getArticles($params, Factory::getApplication());
    }
}
