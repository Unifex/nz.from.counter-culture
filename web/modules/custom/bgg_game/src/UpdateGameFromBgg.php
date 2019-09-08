<?php

namespace Drupal\bgg_game;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * UpdateGameFromBgg service.
 */
class UpdateGameFromBgg {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;
  
  /**
   * The entity type manager preloaded with node.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * The logger.factory service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Constructs an UpdateGameFromBgg object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity_type.manager service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger.factory service.
   */
  public function __construct(
    ClientInterface $http_client,
    EntityTypeManagerInterface $entity_manager,
    LoggerChannelFactoryInterface $logger
    ) {
    $this->httpClient = $http_client;
    $this->entityManager = $entity_manager;
    $this->logger = $logger->get('BGG Game');
  }

  /**
   * Method description.
   */
  public function update($nid) {
    $logger = $this->logger;
    $game = $this->entityManager->getStorage('node')->load($nid);

    if (empty($game)) {
      $logger->notice('No game found.');
      return FALSE;
    }

    // Fetch the Game from the BGG API.
    $bggid = $game->getTitle();
    $url = "https://boardgamegeek.com/xmlapi2/thing?id=" . $bggid;
    $logger->notice("BGG: " . $url);
    $client = $this->httpClient;
    $request = $client->get($url);
    
    $logger->notice("Status Code: " . print_r($request->getStatusCode(), 1));
    if ($request->getStatusCode() != 200) {
      $logger->warning('Could not fetch bggid ' . $bggid);
      return FALSE;
    }
    $body = $request->getBody()->getContents();

    $decoder = new XmlEncoder();
    $xml = $decoder->decode($body, 'xml');
    $this_game = $xml['item'];
    $name = '';
    if (count($this_game['name']) == 1) {
      $name = $this_game['name'][0]['@value'];
    }
    elseif (count($this_game['name']) > 1) {
      foreach($this_game['name'] as $item) {
        if (!empty($item['@type']) && $item['@type'] == 'primary') {
          $name = $item['@value'];
          break;
        }
      }
    }
    $game->set('field_title', $name);
    $game->set('field_year_published', $this_game['yearpublished']['@value']);
    $game->set('field_min_players',    $this_game['minplayers']['@value']);
    $game->set('field_max_players',    $this_game['maxplayers']['@value']);
    $game->set('field_play_time',      $this_game['playingtime']['@value']);
    $game->set('field_min_play_time',  $this_game['minplaytime']['@value']);
    $game->set('field_max_play_time',  $this_game['maxplaytime']['@value']);
    $this->processTerms($game, $this_game['link'], 'boardgamemechanic', 'field_mechanic');
    $this->processTerms($game, $this_game['link'], 'boardgamecategory', 'field_category');
    $game->set('field_minimum_age', $this_game['minage']['@value']);

    if (!empty($this_game['thumbnail'])) {
      $game->set('field_thumbnail_url', [
        'uri' => $this_game['thumbnail'],
        'title' => $name,
      ]);
    }
    if (!empty($this_game['image'])) {
      $game->set('field_image_url', [
        'uri' => $this_game['image'],
        'title' => $name,
      ]);
    }

    $game->setPublished();
    $save_result = $game->save();
    if ($save_result == SAVED_NEW || $save_result == SAVED_UPDATED) {
      $logger->notice("Saved: https://counter-culture.ddev.local/node/" . $nid);
      return TRUE;
    }
    else {
      $logger->notice("Not Saved: https://counter-culture.ddev.local/node/" . $nid);
      return FALSE;
    }
  }

  /**
   * Update terms on a field.
   *
   * @param object $game
   *   The game we are updating.
   * @param array $items
   *   The items our target may be on.
   * @param string $source
   *   The type of item we are after.
   * @param string $target
   *   The field our items will be added to.
   */
  private function processTerms($game, array $items, $source, $target) {
    $logger = $this->logger;
    $terms = [];
    $vocab_map = [
      'boardgamemechanic' => 'mechanic',
      'boardgamecategory' => 'category',
      'boardgamedesigner' => 'designer',
    ];

    foreach ($items as $item) {
      if ($item['@type'] == $source) {
        $properties = [
          'name' => $item['@value'],
          'vid' => $vocab_map[$source],
        ];
        $term = $this->entityManager->getStorage('taxonomy_term');
        $this_term = $term->loadByProperties($properties);
        $this_term = reset($this_term);

        if (!$this_term) {
          $term->create($properties)->save();
          $this_term = $term->loadByProperties($properties);
          $this_term = reset($this_term);
        }
        $terms[] = $this_term->id();

      }
    }
    $game->get($target)->setValue($terms);
  }

}
