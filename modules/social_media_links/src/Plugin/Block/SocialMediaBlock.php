<?php

namespace Drupal\social_media_links\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;

/**
 * Provides a 'SocialMediaBlock' block.
 *
 * @Block(
 *  id = "social_media_block",
 *  admin_label = @Translation("Social media links block"),
 * )
 */
class SocialMediaBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new SocialMediaBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The RequestStack definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RequestStack $request_stack
  ) {
    $this->requestStack = $request_stack;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')
    );
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

    //form with multiply instance
    $form['#tree'] = TRUE;

    $form['instances'] = [
      '#type' => 'container',
    ];

    if(!isset($this->configuration['countInstances'])) {
      $this->setConfigurationValue('countInstances', 1);
    }

    $form['#attributes']['novalidate'] = '1';

    for ($x = 0; $x < $this->configuration['countInstances']; $x++) {

      $queueNumber = ($x + 1);

      $form['instances'][$x] = [
        '#prefix' => sprintf(
          "<div class='block-instance-%s ui-state-default'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>",
          $queueNumber
        ),
        '#suffix' => '</div>',
        'icon' => [
          '#type' => 'select',
          '#title' => $this->t('Icon'),
          '#options' => $this->getIconOptions(),
          '#default_value' => isset($this->configuration['instances'][$x]['icon']) ?
            $this->configuration['instances'][$x]['icon'] : null,
          '#required' => true,
        ],
        'text_label_field' => [
          '#type' => 'textfield',
          '#title' => $this->t('Label'),
          '#default_value' => isset($this->configuration['instances'][$x]['text_label_field']) ?
            $this->configuration['instances'][$x]['text_label_field'] : null,
          '#maxlength' => 64,
          '#size' => 64,
          '#required' => true,
        ],
        'link_field' => [
          '#type' => 'url',
          '#title' => $this->t('Url'),
          '#default_value' => isset($this->configuration['instances'][$x]['link_field']) ?
            $this->configuration['instances'][$x]['link_field'] : null,
          '#required' => true,
        ],
        'order_number' => [
          '#type' => 'hidden',
          '#default_value' => $x,
          '#required' => true,
          '#attributes' => [
            'class' => 'order-number-input',
          ],
        ],
      ];
    }

    $form['addInstance'] = [
      '#type' => 'submit',
      '#name' => 'addInstance',
      '#value' => $this->t('Add link'),
    ];

    $form['deleteLastInstance'] = [
      '#type' => 'submit',
      '#name' => 'deleteLastInstance',
      '#value' => $this->t('Delete last link'),
    ];

    $form['#attached']['library'][] = 'social_media_links/social_media_links';

    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {

    $formSubmitted = !empty($this->requestStack->getCurrentRequest()->get('op'));
    $addInstance = !empty($this->requestStack->getCurrentRequest()->get('addInstance'));

    if($formSubmitted) {
      parent::submitConfigurationForm($form, $form_state);
    } elseif($addInstance) {
      $this->addNewFields($form, $form_state);
    } elseif ($this->deleteLastInstanceSubmitted()) {
      $this->deleteLastInstance($form, $form_state);
    }
  }

  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    if($this->deleteLastInstanceSubmitted()) {
      $form_state->clearErrors();
    }

    parent::validateConfigurationForm($form, $form_state);
  }

  /**
   * Handle adding new instance
   */
  private function addNewFields(array &$form, FormStateInterface $form_state) {
    $this->configuration['countInstances']++;

    $form_state->setRebuild();
  }

  /**
   * Handle delete last instance
   */
  private function deleteLastInstance(array &$form, FormStateInterface $form_state) {
    if($this->configuration['countInstances'] > 0) {
      $this->configuration['countInstances']--;
    }

    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    $values = $form_state->getValues();

    if(isset($values['instances'])) {
      $instances = $values['instances'];

      $this->clearEmptyFormValues($instances);

      $this->sortByOrderNumber($instances);

      foreach ($instances as $key => $instanceItem) {
        $instances[$key]['image'] = sprintf("/img/header/menu/%s.svg", $instanceItem['icon']);
      }

      $this->setConfigurationValue('instances', $instances);
    } else {
      $this->setConfigurationValue('instances', []);
    }

    $this->setConfigurationValue('countInstances', max(count($this->configuration['instances']), 1));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'social-media-links-block';

    $instances = $this->configuration['instances'];

    $build['#content'] = $instances;

    return $build;
  }

  /**
   * @param array $instances
   */
  private function clearEmptyFormValues(&$instances)
  {
    foreach ($instances as $index => $instanceItem) {
      if(empty($instanceItem['link_field'])) {
        unset($instances[$index]);
      }
    }
  }

  /**
   * @return bool
   */
  private function deleteLastInstanceSubmitted()
  {
    return !empty($this->requestStack->getCurrentRequest()->get('deleteLastInstance'));
  }

  /**
   * @param array $instances
   * @return void
   */
  private function sortByOrderNumber(array &$instances) {
    $tempArr = [];

    foreach ($instances as $instanceItem) {
      $tempArr[(int)$instanceItem['order_number']] = $instanceItem;
    }

    ksort($tempArr);

    $instances = $tempArr;
  }

  /**
   * @return array
   */
  private function getIconOptions() {
    return [
      'twitter' => $this->t('Twitter'),
      'facebook' => $this->t('Facebook'),
      'youtube' => $this->t('Youtube'),
    ];
  }

}
