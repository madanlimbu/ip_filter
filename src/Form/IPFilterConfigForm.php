<?php

namespace Drupal\ip_filter\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class IPFilterConfigForm extends ConfigFormBase {

  /**
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ip_filter_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ip_filter.config');

    $form['ip_filter_blacklist'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Blacklist'),
      '#default_value' => $config->get('ip_filter_blacklist'),
      '#description' => $this->t('Enter a list of IP addresses, Add one IP per line.'),
      '#rows' => 10,
      '#cols' => 30,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->configFactory->getEditable('ip_filter.config')
      ->set('ip_filter_blacklist', $form_state->getValue('ip_filter_blacklist'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  protected function getEditableConfigNames() {
    return [
      'ip_filter.config',
    ];
  }

}
