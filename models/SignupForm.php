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

        // Ensure default roles exist
        $this->ensureDefaultRoles();

        $user = new Usuarios();
        $user->nombre = 'Admin Temp'; // Placeholder name as per request "simplemente correo y contraseña"
        $user->correo = $this->correo;
        $user->rol_id = 1; // Admin role
        $user->setPassword($this->password);
        $user->created_at = date('Y-m-d H:i:s');
        $user->is_deleted = 0;

        if (!$user->save()) {
            // Log the validation errors for debugging
            Yii::error('Usuario validation errors: ' . json_encode($user->errors), __METHOD__);

            // Transfer validation errors to this form model
            foreach ($user->errors as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->addError('correo', $error);
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Ensures that default roles exist in the database
     */
    private function ensureDefaultRoles()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Check if role 1 (Admin) exists
            $adminRole = Roles::findOne(1);
            if (!$adminRole) {
                $adminRole = new Roles();
                $adminRole->id = 1;
                $adminRole->nombre = 'Administrador';
                $adminRole->save(false); // Skip validation to force insert
            }

            // Check if role 2 (User) exists (for future use)
            $userRole = Roles::findOne(2);
            if (!$userRole) {
                $userRole = new Roles();
                $userRole->id = 2;
                $userRole->nombre = 'Usuario';
                $userRole->save(false);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Error creating default roles: ' . $e->getMessage(), __METHOD__);
        }
    }
}
