<?php

  namespace OCA\Nextbiblio\Migration;

  use Closure;
  use OCP\DB\ISchemaWrapper;
  use OCP\Migration\SimpleMigrationStep;
  use OCP\Migration\IOutput;

  class Version001Date20210612232200 extends SimpleMigrationStep {

    /**
    * @param IOutput $output
    * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
    * @param array $options
    * @return null|ISchemaWrapper
    */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('biblio')) {
            $table = $schema->createTable('biblio');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);
            $table->addColumn('emplacement', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('lu', 'boolean', [
                'notnull' => true,
                'default' => false
            ]);
            $table->addColumn('period', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('uuid', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('publisher', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('isbn', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('identifiers', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('authors', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('timestamp', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('pubdate', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('tags', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('languages', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('cover', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('comments', 'text', [
                'notnull' => false,
                'default' => ''
            ]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'nextbiblio_user_id_index');
        }
        return $schema;
    }
}
