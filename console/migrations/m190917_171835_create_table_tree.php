<?php

use yii\db\Migration;

/**
 * Class m190917_171835_create_table_tree
 */
class m190917_171835_create_table_tree extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tree', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11),
            'position' => $this->integer(11),
            'path' => $this->string(12288),
            'level' => $this->integer(11)->notNull(),
        ]);

        $this->insert('tree', [
            'level' => 0,
            'path' => '1',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tree');
    }   
}
