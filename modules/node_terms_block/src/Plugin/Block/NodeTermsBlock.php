<?php

namespace Drupal\node_terms_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager) {

    $this->entityTypeManager = $entityTypeManager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    /* @var EntityTypeManagerInterface $entityTypeManager */
    $entityTypeManager = $container->get('entity_type.manager');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $entityTypeManager
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

    $terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadTree($this->configuration['taxonomy_vocabulary']);

    $build = [];
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

}
