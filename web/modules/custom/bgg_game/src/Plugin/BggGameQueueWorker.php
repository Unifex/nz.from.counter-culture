<?php  
// /**
//  * @file
//  * Contains \Drupal\bgg_game\Plugin\QueueWorker\BggGameQueueWorker.
//  */

// namespace Drupal\bgg_game\Plugin\QueueWorker;

// #use Drupal\Core\Entity\EntityStorageInterface;
// use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
// use Drupal\Core\Queue\QueueWorkerBase;
// #use Drupal\node\NodeInterface;
// #use Symfony\Component\DependencyInjection\ContainerInterface;

// /**
//  * Processes tasks for bgg_game module.
//  *
//  * @QueueWorker(
//  *   id = "bgg_games",
//  *   title = @Translation("BGG Game: Queue worker"),
//  *   cron = {"time" = 1}
//  * )
//  */
// class BggGameQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

//   /**
//    * Constructs a new BggGameQueueWorker object.
//    *
//    * @param array $configuration
//    *   A configuration array containing information about the plugin instance.
//    * @param string $pluginId
//    *   The plugin_id for the plugin instance.
//    * @param array $pluginDefinition
//    *   The plugin implementation definition.
//    * @param \Drupal\Core\Queue\QueueInterface $bggGameQueue
//    *   The bgg_game queue object.
//    * @param \GuzzleHttp\ClientInterface
//    *   Guzzle HTTP Client.
//    * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
//    *   The logger factory object.
//    */
//   public function __construct(array $configuration, $pluginId, array $pluginDefinition, QueueInterface $bggGameQueue, ClientInterface $client, LoggerChannelFactoryInterface $loggerFactory) {
//     parent::__construct($configuration, $pluginId, $pluginDefinition);
//     $this->bggGameQueue = $bggGameQueue;
//     $this->client = $client;
//     $this->logger = $loggerFactory->get('bgg_game');
//   }

//   /**
//    * {@inheritdoc}
//    */
//   public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
//     return new static(
//       $configuration,
//       $pluginId,
//       $pluginDefinition,
//       $container->get('queue')->get('bgg_games', TRUE),
//       $container->get('http_client'),
//       $container->get('logger.factory')
//     );
//   }

//   /**
//    * {@inheritdoc}
//    */
//   public function processItem($item) {
//     $this->logger->notice("\$item = $item");

//     /** @var NodeInterface $node */
//     $node = \Drupal\node\Entity\Node::load($item->nid);
//     $this->logger->notice("Processed BGG ID " . $node->getTitle());
    
//     // Fetch the game from the API.
    
//     // XML to array.

//     // Update the node.

//     // Save the node.
//     //$node->save();
//   }

// }