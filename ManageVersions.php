<?php

namespace Drupal\sample_module\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Drupal\Core\Url;
use Drupal\node\NodeStorageInterface;
use Drupal\sample_module\Services\VersionHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manage versions page.
 */
class ManageVersions extends ControllerBase {

  use RedirectDestinationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public $entityTypeManager;

  /**
   * The node storage service.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected NodeStorageInterface $nodeStorage;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Version Helper class.
   *
   * @var \Drupal\sample_module\Services\VersionHelper
   */
  protected VersionHelper $versionHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ConfigFactoryInterface $config_factory,
    DateFormatterInterface $date_formatter,
    VersionHelper $versionHelper,
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->configFactory = $config_factory;
    $this->dateFormatter = $date_formatter;
    $this->versionHelper = $versionHelper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('sample_module.version_helper'),
    );
  }

  /**
   * Builds the header row for the listing table.
   *
   * @return array
   *   A render array structure of header strings.
   */
  protected function buildHeader() {
    $header['version'] = $this->t('Version');
    $header['imported'] = $this->t('Imported');
    $header['published'] = $this->t('Published');
    $header['imported_by'] = $this->t('Imported by');
    $header['operations'] = $this->t('Operations');
    return $header;
  }

  /**
   * Builds the listing table.
   *
   * @return array
   *   A render array structure of table.
   */
  public function render() {
    return [
      'table' => [
        '#theme' => 'table',
        '#header' => $this->buildHeader(),
        '#rows' => $this->getVersionRevisions(),
        '#empty' => $this->t('There are no entries.'),
      ],
      'pager' => [
        '#type' => 'pager',
      ],
      '#attached' => [
        'library' => [
          'sample_module/manage-versions',
        ],
      ],
    ];
  }

  /**
   * Retrieves node revisions using Entity Query with a pager.
   *
   * @return array
   *   An array of version revisions.
   */
  protected function getVersionRevisions() {
    $rows = [];
    $query = $this->nodeStorage->getQuery();
    $query->allRevisions();
    $query->condition('field_version_id', 0);
    $query->accessCheck(TRUE);
    $query->sort('vid', 'DESC');
    $query->pager(25);
    $allRevisions = $query->execute();
    $allRevisions = $allRevisions ? $this->nodeStorage->loadMultipleRevisions(array_keys($allRevisions)) : [];

    $currentVersion = $this->configFactory->get('sample_module.config')->get('sample_module_version');

    foreach ($allRevisions as $revision) {
      $row = [];
      $row['version'] = $revision?->field_version?->value;

      $row['imported'] = $revision?->revision_timestamp?->value ?
      $this->dateFormatter->format($revision?->revision_timestamp?->value, 'custom', 'm/d/Y') : NULL;

      $row['published'] = $revision?->field_published_time?->value ?
      $this->dateFormatter->format($revision?->field_published_time?->value, 'custom', 'm/d/Y') : NULL;

      $row['imported_by'] = $revision?->getRevisionUser()?->getDisplayName();

      $row['operations']['data'] = $this->getOperations($revision, $currentVersion);

      $rows[] = $row;
    }

    return $rows;
  }

  /**
   * Generates operations links for revisions.
   *
   * @param object $revision
   *   Revision of node.
   * @param string $currentVersion
   *   Current active version.
   *
   * @return array
   *   An array of operations.
   */
  protected function getOperations($revision, $currentVersion) {
    $version = $currentVersion == $revision?->field_version?->value ? '' : $revision?->field_version?->value;

    $op = [];
    $op['view']['title'] = $this->t('View');
    $op['view']['url'] = Url::fromUserInput($version ? '/version/available/' . $version : '/version');

    $op['needs_review']['title'] = $this->t('Needs review');
    if ($this->versionHelper->getVersionReviewCount($version)) {
      $op['needs_review']['url'] = Url::fromUserInput('/admin/version/review-content/' . $revision?->field_version?->value);
    }
    else {
      $op['needs_review']['url'] = Url::fromRoute('<none>');
      $op['needs_review']['attributes'] = [
        'class' => ['disabled-link'],
        'title' => $this->t('No content requires review.'),
      ];
    }

    $op['publish']['title'] = $this->t('Publish');
    if ($version) {
      $op['publish']['url'] = Url::fromUserInput("/admin/versions/{$version}/publish")
        ->setOption('query', $this->getRedirectDestination()->getAsArray());
    }
    else {
      $op['publish']['url'] = Url::fromRoute('<none>');
      $op['publish']['attributes'] = [
        'class' => ['disabled-link'],
        'title' => $this->t('Content already published.'),
      ];
    }

    return [
      '#type' => 'operations',
      '#dropbutton_type' => 'small',
      '#links' => $op,
    ];
  }

}
