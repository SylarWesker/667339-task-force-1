<?php

use yii\db\Migration;

/**
 * Class m200602_103937_create_db_structure
 */
class m200602_103937_create_db_structure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Дополняем таблицу пользователь.
        $this->addColumn('{{%user}}', 'full_name', $this->string()->notNull());
        $this->addColumn('{{%user}}', 'latitude', $this->decimal(9, 7));
        $this->addColumn('{{%user}}', 'longitude', $this->decimal(9, 7));
        // `locality_id` int - внешний ключ.

        // Создание таблицы категорий заданий.
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'icon_name' => $this->string()->notNull()
        ]);

        // Создание таблицы населенных пунктов.
        $this->createTable('locality', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'latitude' => $this->decimal(9, 7),
            'longitude' => $this->decimal(9, 7)
        ]);

        // Создание таблицы с задачами.
        // $this->createTable('task', []);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_103937_create_db_structure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_103937_create_db_structure cannot be reverted.\n";

        return false;
    }
    */
}
