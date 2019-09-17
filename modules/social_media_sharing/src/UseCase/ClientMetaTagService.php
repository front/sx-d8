<?php

namespace Drupal\social_media_sharing\UseCase;

use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\metatag\MetatagManager;
use Drupal\metatag\MetatagToken;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClientMetaTagService. Adapter for @see https://www.drupal.org/project/metatag module
 *
 * @package Drupal\social_media_sharing\UseCase
 */
class ClientMetaTagService implements MetaTagAdapterInterface {

  private $metaTagManager;

  private $metaTagToken;

  private $request;

  private $titleResolver;

  /**
   * @var Node|null
   */
  private $node;

  public function __construct(
    MetatagManager $metaTagManager,
    MetatagToken $metaTagToken,
    RequestStack $requestStack,
    TitleResolverInterface $titleResolver
  ) {

    $this->metaTagManager = $metaTagManager;

    $this->metaTagToken = $metaTagToken;

    $this->titleResolver = $titleResolver;

    $this->request = $requestStack->getCurrentRequest();

    $this->node = $this->request->get('node');
  }

  public function getTagValue(string $key):string {

    if($value = $this->metaTagToken->replace($this->getValueFromContentTags($key))) {
      return $value;
    }

    if($value = $this->metaTagToken->replace($this->getValueFromGlobalTags($key))) {
      return $value;
    }

    if($key === 'title') {
      $route = $this->request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);
      return $this->titleResolver->getTitle($this->request, $route);
    }

    throw new NotFoundHttpException();
  }

  public function getSafeTagValue(string $key):string {
    try {
      return $this->getTagValue($key);
    } catch (NotFoundHttpException $e) {
      return '';
    }
  }

  private function getValueFromContentTags(string $key):?string {
    if(!$this->hasNode()) {
      return false;
    }

    $nodeTags = $this->metaTagManager->tagsFromEntityWithDefaults($this->node);

    return $nodeTags[$key] ?? false;
  }

  private function getValueFromGlobalTags(string $key):?string {
    /* @var \Drupal\metatag\MetatagDefaultsInterface $metatagDefaults */
    $metaTagDefaults = $this->metaTagManager->getGlobalMetatags();

    $metaTagsArr = $metaTagDefaults->get('tags');

    return $metaTagsArr[$key] ?? false;
  }

  private function hasNode():bool {
    return ($this->node instanceof Node);
  }

}
