<?php

use yii\db\Migration;

/**
 * Class m180322_122505_user
 */
class m180322_122505_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'firstName' => $this->string(255)->notNull(),
            'lastName' => $this->string(255)->notNull(),
            'auth_key' => $this->string(255)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull()->unique(),
            'status' => $this->integer(6)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('user');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180322_122505_user cannot be reverted.\n";

        return false;
    }
    */
}
