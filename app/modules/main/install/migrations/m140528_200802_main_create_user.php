<?php

use yii\db\Schema;

class m140528_200802_main_create_user extends \yii\db\Migration
{

    public $tableName = "user";

    public function up()
    {

        $this->createTable("{{%$this->tableName}}",[
            'id'=>Schema::TYPE_PK,
            'active'=>Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT true',
            'author_id'=>Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'created_at'=>Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT now()',
            'updated_at'=>Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT now()',
            'username'=>Schema::TYPE_STRING . ' NOT NULL',
            'auth_key'=>Schema::TYPE_STRING . ' NOT NULL',
            'password_hash'=>Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token'=>Schema::TYPE_STRING,
            'email'=>Schema::TYPE_STRING . ' NOT NULL',
            'role'=>Schema::TYPE_STRING . ' NOT NULL',
            'name'=>Schema::TYPE_STRING,
            'image'=>Schema::TYPE_TEXT,
            'text'=>Schema::TYPE_TEXT,
        ]);

        $this->insert("{{%$this->tableName}}",[

            'id'=>1,
            'author_id'=>1,
            'username'=>'admin',
            'auth_key'=>'PBBpK3K8_YDofSoP1AYWcGZAbISA0O2T',
            'password_hash'=>'$2y$13$Cmws6ebdDCt3kEEjF2dfqeA16WVAqiq9Vi53VGqdz6KiSCMYFJEjq',
            'email'=>'webadmin87@gmail.com',
            'role'=>'root',
            'name'=>'Администратор',

        ]);

    }

    public function down()
    {

        $this->dropTable("{{%$this->tableName}}");

    }
}
