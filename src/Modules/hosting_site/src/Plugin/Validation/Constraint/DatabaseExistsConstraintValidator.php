<?php

declare(strict_types=1);


namespace Drupal\hosting_site\Plugin\Validation\Constraint;

use Drupal\Core\Database\Database;
use Drupal\Core\Database\DatabaseExceptionWrapper;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Messenger\Messenger;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Database Exists constraint.
 */
final class DatabaseExistsConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $field, Constraint $constraint): void {

    $new_database_name = $field->value;

    try {
      // Check that the database exists and can be accessed.
      $database = \Drupal::database();

      $new_connection = $database->getConnectionOptions();
      $new_connection['database'] = $new_database_name;

      Database::addConnectionInfo($new_database_name, 'default', $new_connection);
      Database::setActiveConnection($new_database_name);
      $connection = Database::getConnection('default',$new_database_name);

      $tables = $connection->query('SHOW TABLES')->fetchAll();
      \Drupal::messenger()->addMessage(t("Successfully connected to database :database. There are :tables tables in the dB", [
        ':database' => $new_database_name,
        ':tables' => count($tables),
      ]));


      // @TODO: Should we set state to "not installed"?
      if (count($tables) == 0) {
        \Drupal::messenger()->addMessage(t("No data was found in the database :database. Try visiting the site to install data.", [
          ':database' => $new_database_name,
        ]));
      }
    }
    catch (\PDOException $e) {
      // Cannot access.
      $this->context->addViolation($constraint->messageCannotAccess, [
        '%value' => $new_database_name,
        '%error' => $e->getMessage(),
      ]);
    }
    catch (DatabaseExceptionWrapper $e) {
      // Cannot access. Try to create.
      $this->context->addViolation($constraint->messageCannotAccess, [
        '%value' => $new_database_name,
        '%error' => $e->getMessage(),
      ]);
    }
  }
}
