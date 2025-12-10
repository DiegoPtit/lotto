<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nombre
 * @property string $correo
 * @property string|null $password_hash
 * @property int $rol_id
 * @property string|null $google_id
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property MetodosPago[] $metodosPagos
 * @property Politicas[] $politicas
 * @property Rifas[] $rifas
 * @property Roles $rol
 */
class Usuarios extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password_hash', 'google_id', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['nombre', 'correo', 'rol_id'], 'required'],
            [['rol_id', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['nombre'], 'string', 'max' => 150],
            [['correo'], 'string', 'max' => 200],
            [['password_hash'], 'string', 'max' => 255],
            [['google_id'], 'string', 'max' => 100],
            [['correo'], 'unique'],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::class, 'targetAttribute' => ['rol_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'correo' => Yii::t('app', 'Correo'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'rol_id' => Yii::t('app', 'Rol ID'),
            'google_id' => Yii::t('app', 'Google ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * Gets query for [[MetodosPagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetodosPagos()
    {
        return $this->hasMany(MetodosPago::class, ['id_operador_registro' => 'id']);
    }

    /**
     * Gets query for [[Politicas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPoliticas()
    {
        return $this->hasMany(Politicas::class, ['id_operador_registro' => 'id']);
    }

    /**
     * Gets query for [[Rifas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRifas()
    {
        return $this->hasMany(Rifas::class, ['id_operador_registro' => 'id']);
    }

    /**
     * Gets query for [[Rol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Roles::class, ['id' => 'rol_id']);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'is_deleted' => 0]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Implement this if you use access tokens for authentication
        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     * @return string a key that is used to check the validity of a given identity ID.
     */
    public function getAuthKey()
    {
        // Implement auth key if you use the "remember me" functionality
        // You may need to add an auth_key column to your usuarios table
        return null;
    }

    /**
     * Validates the given auth key.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     */
    public function validateAuthKey($authKey)
    {
        // Implement auth key validation if you use the "remember me" functionality
        return false;
    }

    /**
     * Finds user by correo (email)
     * @param string $correo
     * @return static|null
     */
    public static function findByCorreo($correo)
    {
        return static::findOne(['correo' => $correo, 'is_deleted' => 0]);
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if ($this->password_hash === null) {
            return false;
        }
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

}
