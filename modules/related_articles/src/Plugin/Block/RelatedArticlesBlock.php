<?php

namespace Drupal\related_articles\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'RelatedArticlesBlock' block.
 *
 * @Block(
 *  id = "related_articles_block",
 *  admin_label = @Translation("Related articles block"),
 * )
 */
class RelatedArticlesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  private $requestStack;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    return array_merge($form, [
      'limit' => [
        '#type' => 'number',
        '#title' => $this->t('Limit'),
        '#required' => false,
        '#default_value' => $this->configuration['limit'] ?? null,
      ],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('limit', $form_state->getValue('limit'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    /* @var NodeInterface $node */
    $node = $this->requestStack->getCurrentRequest()->get('node');

    if(!$this->isArticle($node)) {
      return false;
    }

    $relatedArticles = $this->getRelatedArticles($node);

    if(empty($relatedArticles)) {
      return false;
    }

    return [
      '#theme' => 'related_articles_block',
      '#articles' => $relatedArticles,
    ];
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    /* @var $requestStackService RequestStack */
    $requestStackService =  $container->get('request_stack');

    return new static($configuration, $plugin_id, $plugin_definition, $requestStackService);
  }

  private function isArticle(?NodeInterface $node):bool {
    if(!($node instanceof NodeInterface)) {
      return false;
    }

    return ($node->getType() === 'article');
  }

  private function getRelatedArticles(NodeInterface $article):iterable
  {
    /* @var $fieldList \Drupal\Core\Field\EntityReferenceFieldItemListInterface */
    $fieldList = $article->get('field_related_articles_fr_module');

    $relatedArticles = $fieldList->referencedEntities();

    if($this->needToLimitArticles($relatedArticles)) {
      shuffle($relatedArticles);

      return array_slice($relatedArticles,0,$this->configuration['limit']);
    }

    return $relatedArticles;
  }

  private function needToLimitArticles(iterable $relatedArticles):bool
  {
    if(empty($this->configuration['limit'])) {
      return false;
    }

    return (count($relatedArticles) > $this->configuration['limit']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
