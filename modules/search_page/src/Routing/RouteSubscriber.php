<?php

/**
 * @file
 * Contains \Drupal\search_page\Routing\RouteSubscriber.
 */

namespace Drupal\search_page\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    public function alterRoutes(RouteCollection $collection)
    {
    // Replace dynamically created "search.view_node_search" route's Controller
    // with our own.
        if ($route = $collection->get('search.view_node_search')) {
            $route->setDefault('_controller', '\Drupal\search_page\Controller\SearchPageSearchController::view');
        }
    }
}
