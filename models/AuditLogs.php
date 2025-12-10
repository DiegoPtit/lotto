<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audit_logs".
 *
 * @property int $id
 * @property string $actor_type
 * @property int|null $actor_id
 * @property string $accion
 * @property string|null $entidad
 * @property int|null $entidad_id
 * @property string|null $datos
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $created_at
 */
class AuditLogs extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ACTOR_TYPE_USUARIO = 'usuario';
    const ACTOR_TYPE_JUGADOR = 'jugador';
    const ACTOR_TYPE_SISTEMA = 'sistema';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audit_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['actor_id', 'entidad', 'entidad_id', 'datos', 'ip_address', 'user_agent'], 'default', 'value' => null],
            [['actor_type', 'accion'], 'required'],
            [['actor_type'], 'string'],
            [['actor_id', 'entidad_id'], 'integer'],
            [['datos', 'created_at'], 'safe'],
            [['accion', 'user_agent'], 'string', 'max' => 255],
            [['entidad'], 'string', 'max' => 100],
            [['ip_address'], 'string', 'max' => 45],
            ['actor_type', 'in', 'range' => array_keys(self::optsActorType())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'actor_type' => Yii::t('app', 'Actor Type'),
            'actor_id' => Yii::t('app', 'Actor ID'),
            'accion' => Yii::t('app', 'Accion'),
            'entidad' => Yii::t('app', 'Entidad'),
            'entidad_id' => Yii::t('app', 'Entidad ID'),
            'datos' => Yii::t('app', 'Datos'),
            'ip_address' => Yii::t('app', 'Ip Address'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }


    /**
     * column actor_type ENUM value labels
     * @return string[]
     */
    public static function optsActorType()
    {
        return [
            self::ACTOR_TYPE_USUARIO => Yii::t('app', 'usuario'),
            self::ACTOR_TYPE_JUGADOR => Yii::t('app', 'jugador'),
            self::ACTOR_TYPE_SISTEMA => Yii::t('app', 'sistema'),
        ];
    }

    /**
     * @return string
     */
    public function displayActorType()
    {
        return self::optsActorType()[$this->actor_type];
    }

    /**
     * @return bool
     */
    public function isActorTypeUsuario()
    {
        return $this->actor_type === self::ACTOR_TYPE_USUARIO;
    }

    public function setActorTypeToUsuario()
    {
        $this->actor_type = self::ACTOR_TYPE_USUARIO;
    }

    /**
     * @return bool
     */
    public function isActorTypeJugador()
    {
        return $this->actor_type === self::ACTOR_TYPE_JUGADOR;
    }

    public function setActorTypeToJugador()
    {
        $this->actor_type = self::ACTOR_TYPE_JUGADOR;
    }

    /**
     * @return bool
     */
    public function isActorTypeSistema()
    {
        return $this->actor_type === self::ACTOR_TYPE_SISTEMA;
    }

    public function setActorTypeToSistema()
    {
        $this->actor_type = self::ACTOR_TYPE_SISTEMA;
    }
}
