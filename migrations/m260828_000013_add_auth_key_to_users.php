<?php

use yii\db\Migration;

/**
 * Menambahkan kolom auth_key ke {{%users}}, dibutuhkan oleh yii\web\IdentityInterface
 * untuk validasi cookie "remember me". Dibuat sebagai migration terpisah (ALTER TABLE),
 * bukan mengedit m260828_000001_create_table_users.php yang sudah pernah dijalankan.
 */
class m260828_000013_add_auth_key_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'auth_key', $this->string(64)->after('password'));

        // isi auth_key untuk user yang sudah ada dari seeder,
        // supaya login & remember-me langsung berfungsi tanpa perlu re-seed
        $rows = (new \yii\db\Query())->select(['user_id'])->from('{{%users}}')->all();
        foreach ($rows as $row) {
            $this->update('{{%users}}', [
                'auth_key' => Yii::$app->security->generateRandomString(),
            ], ['user_id' => $row['user_id']]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'auth_key');
    }
}
