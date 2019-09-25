<?php

namespace Drupal\social_media_sharing\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\social_media_sharing\UseCase\MetaTagAdapterInterface;

/**
 * Provides a 'SocialSharingBlock' block.
 *
 * @Block(
 *  id = "social_sharing_block",
 *  admin_label = @Translation("Social sharing block"),
 * )
 */
class SocialSharingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  private $metaTagAdapter;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MetaTagAdapterInterface $metaTagAdapter) {

    $this->metaTagAdapter = $metaTagAdapter;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['social_networks'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Social networks'),
      '#default_value' => $this->configuration['social_networks'],
      '#required' => TRUE,
      '#options' => [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'linkedIn' => 'LinkedIn',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['social_networks'] = $form_state->getValue('social_networks');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'social_media_sharing',
      '#content' => $this->configuration['social_networks'],
      '#label' => $this->configuration['label'],
      '#uri' => $this->metaTagAdapter->getSafeTagValue('canonical_url'),
      '#pageTitle' => $this->metaTagAdapter->getSafeTagValue('title'),
      '#pageDescription' => $this->metaTagAdapter->getSafeTagValue('description'),
    ];
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    /* @var MetaTagAdapterInterface $clientMetaTagService */
    $clientMetaTagService = $container->get('social_media_sharing:client_meta_tag_service');

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $clientMetaTagService
    );
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
