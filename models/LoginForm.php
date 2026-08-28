<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * LoginForm menangani validasi form login dan proses autentikasi
 * terhadap tabel `users` lewat User::findByUsername().
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private ?User $_user = null;

    /**
     * Yii2 versi terbaru meng-inject Security lewat constructor SiteController
     * (new LoginForm($this->security)), bukan lewat Yii::$app->security.
     * $config tetap diteruskan ke Model::__construct() supaya load()/populate() tetap jalan normal.
     */
    public function __construct(private readonly Security $security, $config = [])
    {
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validator kustom: cek username ada dan password cocok.
     * Dipanggil otomatis oleh Yii saat $model->validate() dijalankan.
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Username atau password salah.');
            }
        }
    }

    /**
     * Login user lewat Yii::$app->user. rememberMe = true -> cookie bertahan 30 hari.
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}
