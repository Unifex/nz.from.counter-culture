<?php

// namespace Drupal\bgg_game;

// use Drupal\Core\DependencyInjection\ContainerBuilder;
// use Drupal\Core\DependencyInjection\ServiceProviderBase;
// use Symfony\Component\DependencyInjection\Reference;

// /**
//  * Defines a service provider for the BGG Game module.
//  */
// class BggGameServiceProvider extends ServiceProviderBase {

//   /**
//    * {@inheritdoc}
//    */
//   public function register(ContainerBuilder $container) {
//     $container->register('bgg_game.subscriber', 'Drupal\bgg_game\EventSubscriber\BggGameSubscriber')
//       ->addTag('event_subscriber')
//       ->addArgument(new Reference('entity_type.manager'));
//   }

//   /**
//    * {@inheritdoc}
//    */
//   public function alter(ContainerBuilder $container) {
//     $modules = $container->getParameter('container.modules');
//     if (isset($modules['dblog'])) {
//       // Override default DB logger to exclude some unwanted log messages.
//       $container->getDefinition('logger.dblog')
//         ->setClass('Drupal\bgg_game\Logger\BggGameLog');
//     }
//   }

// }
