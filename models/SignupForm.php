<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Usuarios;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $correo;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['correo', 'trim'],
            ['correo', 'required'],
            ['correo', 'email'],
            ['correo', 'string', 'max' => 200],
            ['correo', 'unique', 'targetClass' => '\app\models\Usuarios', 'message' => 'Este correo ya ha sido registrado.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful
     */
    public function signup()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new Usuarios();
        $user->nombre = 'Admin Temp'; // Placeholder name as per request "simplemente correo y contraseña"
        $user->correo = $this->correo;
        $user->rol_id = 1;
        $user->setPassword($this->password);
        $user->created_at = date('Y-m-d H:i:s');
        $user->is_deleted = 0;

        return $user->save();
    }
}
