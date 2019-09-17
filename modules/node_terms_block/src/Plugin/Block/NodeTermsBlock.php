<?php

namespace Drupal\node_terms_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'NodeTermsBlock' block.
 *
 * @Block(
 *  id = "node_terms_block",
 *  admin_label = @Translation("Node terms block"),
 * )
 */
class NodeTermsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  private $entityTypeManager;

  private $requestStack;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entityTypeManager,
    RequestStack $requestStack
  ) {

    $this->entityTypeManager = $entityTypeManager;

    $this->requestStack = $requestStack;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    /* @var EntityTypeManagerInterface $entityTypeManager */
    $entityTypeManager = $container->get('entity_type.manager');

    /* @var RequestStack $requestStack */
    $requestStack = $container->get('request_stack');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entityTypeManager,
      $requestStack
    );
  }

  public function blockForm($form, FormStateInterface $form_state) {
    return array_merge($form, [
      'taxonomy_vocabulary' => [
        '#type' => 'select',
        '#title' => $this->t('Taxonomy vocabulary'),
        '#options' => $this->getVocabularyOptions(),
        '#required' => true,
        '#default_value' => $this->configuration['taxonomy_vocabulary'] ?? null,
      ],
    ]);
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('taxonomy_vocabulary', $form_state->getValue('taxonomy_vocabulary'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];

    /* @var \Drupal\node\Entity\Node $node */
    $node = $this->requestStack->getCurrentRequest()->get('node');

    if(!($node instanceof Node)) {
      return $build;
    }

    $referencedEntities = $node->referencedEntities();

    $terms = [];

    /* @var $reference Term */
    foreach ($referencedEntities as $reference) {
      if($reference instanceof Term) {
        foreach ($reference->get('vid')->getValue() as $referenceValue) {
          if($referenceValue['target_id'] === $this->configuration['taxonomy_vocabulary']) {
            $terms[] = $reference;
          }
        }
      }
    }

    if(empty($terms)) {
      return $build;
    }

    $build['#theme'] = 'node_terms_block';
    $build['#content'] = $terms;
    $build['#label'] = $this->configuration['label'];

    return $build;
  }

  private function getVocabularyOptions():array
  {
    $vocabularies = $this->entityTypeManager
      ->getStorage('taxonomy_vocabulary')
      ->loadMultiple();

    $options = [];

    /* @var Vocabulary $vocabularyItem */
    foreach ($vocabularies as $vocabularyItem) {
      $options[$vocabularyItem->get('vid')] = $vocabularyItem->get('name');
    }

    return $options;
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
