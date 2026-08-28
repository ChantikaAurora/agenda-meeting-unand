<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * @property int $user_id
 * @property string $nama
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $role
 * @property bool $is_active
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{%users}}';
    }

    public function rules()
    {
        return [
            [['nama', 'email', 'username', 'password', 'role'], 'required'],
            [['nama', 'username'], 'string', 'max' => 150],
            ['email', 'email'],
            ['email', 'string', 'max' => 150],
            ['username', 'unique'],
            ['email', 'unique'],
            ['role', 'in', 'range' => ['administrasi', 'notulen']],
            ['is_active', 'boolean'],
            ['is_active', 'default', 'value' => true],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /* ================= IdentityInterface ================= */

    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'is_active' => true, 'deleted_at' => null]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Login lewat access token tidak didukung, gunakan username & password.');
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /* ================= Permission) ================= */
    private const ROLE_PERMISSIONS = [
        'administrasi' => [
            'manageAgenda',
            'manageUnitLokasi',
            'manageMember',
            'manageLampiran',
            'viewLaporan',
            'viewAgenda',
        ],
        'notulen' => [
            'manageLampiran',
            'viewAgenda',
        ],
    ];

    public function can(string $permission): bool
    {
        return in_array($permission, self::ROLE_PERMISSIONS[$this->role] ?? [], true);
    }

    /* ================= Helper untuk LoginForm ================= */

    public static function findByUsername(string $username): ?self
    {
        return static::findOne(['username' => $username, 'is_active' => true, 'deleted_at' => null]);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword(string $password): void
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert && empty($this->auth_key)) {
            $this->generateAuthKey();
        }
        return parent::beforeSave($insert);
    }
}
