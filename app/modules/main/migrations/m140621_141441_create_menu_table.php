<?php

use yii\db\Schema;

class m140621_141441_create_menu_table extends \app\modules\main\db\Migration
{

    public $tableName = "menu";

    public function safeUp()
    {

        $this->createTable("{{%$this->tableName}}", [
            'id' => Schema::TYPE_PK,
            'active' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT true',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT now()',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT now()',
            'root' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'lft' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'rgt' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'level' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'title' => Schema::TYPE_TEXT,
            'code' => Schema::TYPE_STRING,
            'link' => Schema::TYPE_TEXT,
            'target' => Schema::TYPE_STRING . " NOT NULL DEFAULT '_self'",
            'class' => Schema::TYPE_STRING,
        ]);

        $this->insert("{{%$this->tableName}}", [

            'author_id' => 1,
            'root' => 1,
            'lft' => 1,
            'rgt' => 6,
            'level' => 1,

        ]);

        $this->insert("{{%$this->tableName}}", [

            'author_id' => 1,
            'root' => 1,
            'lft' => 2,
            'rgt' => 5,
            'level' => 2,
            'title' => 'Главное меню',
            'code' => 'main',
        ]);

        $this->insert("{{%$this->tableName}}", [

            'author_id' => 1,
            'root' => 1,
            'lft' => 3,
            'rgt' => 4,
            'level' => 3,
            'title' => 'Главная',
            'link' => '/',
        ]);

        $this->insertPermission('\app\modules\main\models\Menu');

    }

    public function safeDown()
    {

        $this->dropTable("{{%$this->tableName}}");

        $this->deletePermission('\app\modules\main\models\Menu');

    }
}
