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
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->addConstraint('HostingSiteDatabaseExists')
  ;

    return $fields;
  }
}
