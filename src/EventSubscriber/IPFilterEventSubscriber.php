<?php

namespace Drupal\ip_filter\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ip_filter\Service\IPFilterService;

class IPFilterEventSubscriber implements EventSubscriberInterface, ContainerInjectionInterface {

  /**
   * @var \Drupal\ip_filter\Service\IPFilterService
   */
  protected $ipFilterService;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;


  public function __construct(ConfigFactoryInterface $configFactory, IPFilterService $ipFilterService) {
    $this->ipFilterService = $ipFilterService;
    $this->config = $configFactory->get('ip_filter.config');
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('ip_filter.service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['blockBlacklistedIp'];
    return $events;
  }

  public function blockBlacklistedIp(RequestEvent $event) {
    if ($this->ipFilterService->isBlacklistedIp()) {
      $event->setResponse(new Response('Your IP address has been blacklisted. Contact site admin to unban it.', 403));
    }
  }


}
