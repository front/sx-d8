<?php

namespace Drupal\social_media_sharing\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Provides a 'SocialSharingBlock' block.
 *
 * @Block(
 *  id = "social_sharing_block",
 *  admin_label = @Translation("Social sharing block"),
 * )
 */
class SocialSharingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var Request
   */
  private $request;

  private $requestStack;

  private $titleResolver;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $requestStack, TitleResolverInterface $titleResolver) {

    $this->requestStack = $requestStack;

    $this->request = $requestStack->getCurrentRequest();

    $this->titleResolver = $titleResolver;

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
      '#weight' => '0',
      '#required' => true,
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

    $route = $this->request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);

    $build = [];
    $build['#theme'] = 'social_media_sharing';
    $build['#content'] = $this->configuration['social_networks'];
    $build['#label'] = $this->configuration['label'];
    $build['#uri'] = $this->request->getUri();
    $build['#pageTitle'] = $this->titleResolver->getTitle($this->request, $route);

    return $build;
  }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        /* @var RequestStack $requestStack */
        $requestStack = $container->get('request_stack');

        /* @var TitleResolverInterface $titleResolver */
        $titleResolver = $container->get('title_resolver');

        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $requestStack,
            $titleResolver
        );
    }
}
