<?php  
/**
 * @file
 * Contains \Drupal\bgg_game\Plugin\QueueWorker\BggGameQueueWorker.
 */

namespace Drupal\bgg_game\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes tasks for bgg_game module.
 *
 * @QueueWorker(
 *   id = "bgg_game",
 *   title = @Translation("BGG Game: Queue worker"),
 *   cron = {"time" = 90}
 * )
 */
class BggGameQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Creates a new NodePublishBase object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   */
  public function __construct(EntityStorageInterface $node_storage) {
    $this->nodeStorage = $node_storage;
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    /** @var NodeInterface $node */
    $node = \Drupal\node\Entity\Node::load($item->nid);
    \Drupal::logger('BGG Game')->notice("Processed BGG ID " . $node->getTitle());
    // Fetch the game from the API.
    
    // XML to array.

    // Update the node.

    // Save the node.
    $node->save();
  }

}