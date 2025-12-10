<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "metodos_pago".
 *
 * @property int $id
 * @property int $tipo_id
 * @property string|null $banco
 * @property string|null $titular
 * @property string|null $cedula
 * @property string|null $telefono
 * @property string|null $nro_cuenta
 * @property string $visibilidad
 * @property int|null $id_operador_registro
 * @property string $created_at
 *
 * @property Usuarios $operadorRegistro
 * @property Pagos[] $pagos
 * @property MetodosPagoTipo $tipo
 */
class MetodosPago extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const VISIBILIDAD_PUBLICA = 'publica';
    const VISIBILIDAD_PRIVADA = 'privada';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'metodos_pago';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['banco', 'titular', 'cedula', 'telefono', 'nro_cuenta', 'id_operador_registro'], 'default', 'value' => null],
            [['visibilidad'], 'default', 'value' => 'publica'],
            [['tipo_id'], 'required'],
            [['tipo_id', 'id_operador_registro'], 'integer'],
            [['visibilidad'], 'string'],
            [['created_at'], 'safe'],
            [['banco'], 'string', 'max' => 150],
            [['titular'], 'string', 'max' => 200],
            [['cedula', 'telefono'], 'string', 'max' => 50],
            [['nro_cuenta'], 'string', 'max' => 100],
            ['visibilidad', 'in', 'range' => array_keys(self::optsVisibilidad())],
            [['id_operador_registro'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::class, 'targetAttribute' => ['id_operador_registro' => 'id']],
            [['tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetodosPagoTipo::class, 'targetAttribute' => ['tipo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipo_id' => Yii::t('app', 'Tipo ID'),
            'banco' => Yii::t('app', 'Banco'),
            'titular' => Yii::t('app', 'Titular'),
            'cedula' => Yii::t('app', 'Cedula'),
            'telefono' => Yii::t('app', 'Telefono'),
            'nro_cuenta' => Yii::t('app', 'Nro Cuenta'),
            'visibilidad' => Yii::t('app', 'Visibilidad'),
            'id_operador_registro' => Yii::t('app', 'Id Operador Registro'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[OperadorRegistro]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperadorRegistro()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'id_operador_registro']);
    }

    /**
     * Gets query for [[Pagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagos()
    {
        return $this->hasMany(Pagos::class, ['id_metodo_pago' => 'id']);
    }

    /**
     * Gets query for [[Tipo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(MetodosPagoTipo::class, ['id' => 'tipo_id']);
    }


    /**
     * column visibilidad ENUM value labels
     * @return string[]
     */
    public static function optsVisibilidad()
    {
        return [
            self::VISIBILIDAD_PUBLICA => Yii::t('app', 'publica'),
            self::VISIBILIDAD_PRIVADA => Yii::t('app', 'privada'),
        ];
    }

    /**
     * @return string
     */
    public function displayVisibilidad()
    {
        return self::optsVisibilidad()[$this->visibilidad];
    }

    /**
     * @return bool
     */
    public function isVisibilidadPublica()
    {
        return $this->visibilidad === self::VISIBILIDAD_PUBLICA;
    }

    public function setVisibilidadToPublica()
    {
        $this->visibilidad = self::VISIBILIDAD_PUBLICA;
    }

    /**
     * @return bool
     */
    public function isVisibilidadPrivada()
    {
        return $this->visibilidad === self::VISIBILIDAD_PRIVADA;
    }

    public function setVisibilidadToPrivada()
    {
        $this->visibilidad = self::VISIBILIDAD_PRIVADA;
    }
}
