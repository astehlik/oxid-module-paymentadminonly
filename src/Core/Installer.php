<?php
declare(strict_types=1);

namespace De\Swebhosting\PaymentAdminOnly\Core;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class Installer
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    public static function getInstance(): self
    {
        return ContainerFactory::getInstance()->getContainer()->get(self::class);
    }

    public static function onActivate()
    {
        $instance = self::getInstance();
        $instance->addAlterTables();
        $instance->updateViews();
    }

    protected function addAlterTables()
    {
        $tableStructure = [
            'oxpayments' => [
                'swh_adminonly' => ['type' => 'boolean'],
            ],
        ];

        $this->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $schemaManager = $this->getSchemaManager();
        $newSchema = $schemaManager->createSchema();

        foreach ($tableStructure as $tableName => $columns) {
            $table = $newSchema->getTable($tableName);

            foreach ($columns as $columnName => $columnAttributes) {
                $columnName = strtoupper($columnName);

                if ($table->hasColumn($columnName)) {
                    continue;
                }

                $table->addColumn($columnName, $columnAttributes['type']);
            }
        }

        $currentSchema = $schemaManager->createSchema();
        $queries = $currentSchema->getMigrateToSql($newSchema, $schemaManager->getDatabasePlatform());

        $connection = $this->getConnection();
        foreach ($queries as $query) {
            $connection->executeQuery($query);
        }
    }

    protected function updateViews()
    {
        $dbMetaDataHandler = oxNew(DbMetaDataHandler::class);
        $dbMetaDataHandler->updateViews();
    }

    private function getConnection(): Connection
    {
        return $this->queryBuilderFactory->create()->getConnection();
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->queryBuilderFactory->create()->getConnection()->getSchemaManager();
    }
}
