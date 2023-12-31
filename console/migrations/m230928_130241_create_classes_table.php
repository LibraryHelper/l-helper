<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%class}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%organization}}`
 */
class m230928_130241_create_classes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%classes}}', [
            'id' => $this->primaryKey(),
            'school_id' => $this->integer(),
            'name' => $this->string(),
            'status' => $this->smallInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        // creates index for column `school_id`
        $this->createIndex(
            '{{%idx-class-school_id}}',
            '{{%classes}}',
            'school_id'
        );

        // add foreign key for table `{{%organization}}`
        $this->addForeignKey(
            '{{%fk-class-school_id}}',
            '{{%classes}}',
            'school_id',
            '{{%organization}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%organization}}`
        $this->dropForeignKey(
            '{{%fk-class-school_id}}',
            '{{%classes}}'
        );

        // drops index for column `school_id`
        $this->dropIndex(
            '{{%idx-class-school_id}}',
            '{{%classes}}'
        );

        $this->dropTable('{{%classes}}');
    }
}
