<?php

declare(strict_types=1);

namespace Drupal\hosting_site\Entity\SiteEntity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * A bundle class for site entities.
 */
final class HostedSite extends HostedSiteBase {

  /**
   * @inheritDoc
   */
  static public function propertyFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions)
  {
    $fields = parent::propertyFieldDefinitions($entity_type, $bundle, $base_field_definitions);

    $fields['database_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Database Name'))
      ->setDescription(t('The database name to create for this site.'))
      ->setRequired(true)
      ->setReadOnly(true)
      ->setDisplayConfigurable('form', TRUE)
      ->addConstraint('HostingSiteDatabaseExists')
      ->setDefaultValueCallback('Drupal\hosting_site\Entity\SiteEntity\HostedSite::defaultDatabaseName')
  ;

    return $fields;
  }

  /**
   * Generate a database name for the field.
   * @return string
   * @throws \Random\RandomException
   */
  static public function defaultDatabaseName() {
    $bytes = random_bytes(16);
    return bin2hex($bytes);
  }
}
