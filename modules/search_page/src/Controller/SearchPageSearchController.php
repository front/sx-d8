<?php

namespace Drupal\search_page\Controller;

use Drupal\search\SearchPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\search\Controller\SearchController;

/**
 * Override the Route controller for search.
 */
class SearchPageSearchController extends SearchController {

    /**
     * {@inheritdoc}
     */
    public function view(Request $request, SearchPageInterface $entity) {
        $build = parent::view($request, $entity);
        // Unset the Result title.
        if (isset($build['search_results_title'])) {
            unset($build['search_results_title']);
        }

        return $build;
    }

}