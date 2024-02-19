<?php

namespace Drupal\ip_filter\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class IPFilterService implements ContainerInjectionInterface {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * @var string|null
   */
  private $currentUserIp;

  public function __construct(ConfigFactoryInterface $configFactory, RequestStack $requestStack) {
    $this->config = $configFactory->get('ip_filter.config');
    $this->currentUserIp = $requestStack->getCurrentRequest()->getClientIp();
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('request_stack'),
    );
  }

  /**
   * Currently just checks against against IP list.
   *
   * @return bool
   */
  public function isBlacklistedIp() {
    $data = [
      'current_user_ip' => $this->currentUserIp,
      'is_blocked' => FALSE,
    ];

    $this->checkLocalDb($data);
    $this->checkCustomSource($data);

    return $data['is_blocked'];
  }

  /**
   * Custom local IP list checklist.
   * 
   */
  public function checkLocalDb(&$data) {
    $blacklist = $this->config->get('ip_filter_blacklist') ?? '';
    $ip_blacklist = preg_split("/\r\n|\n|\r/", $blacklist);
    $user_ip = $this->currentUserIp;

    if (!empty($ip_blacklist) && $user_ip && in_array($user_ip, $ip_blacklist)) {
      $data['is_blocked'] = TRUE;
    }

    return $data;
  }

  /**
   * Let thrid party module hook into our check list.
   * 
   */
  public function checkCustomSource(&$data) {
    \Drupal::moduleHandler()->invokeAll('ip_filter_allowed_ip_alter', [&$data]);
  }
}
