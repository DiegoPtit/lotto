<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "metodos_pago_tipo".
 *
 * @property int $id
 * @property string $descripcion
 * @property int $has_banco
 * @property int $has_titular
 * @property int $has_cedula
 * @property int $has_telefono
 * @property int $has_nro_cuenta
 *
 * @property MetodosPago[] $metodosPagos
 */
class MetodosPagoTipo extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'metodos_pago_tipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['has_nro_cuenta'], 'default', 'value' => 0],
            [['descripcion'], 'required'],
            [['has_banco', 'has_titular', 'has_cedula', 'has_telefono', 'has_nro_cuenta'], 'integer'],
            [['descripcion'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'has_banco' => Yii::t('app', 'Has Banco'),
            'has_titular' => Yii::t('app', 'Has Titular'),
            'has_cedula' => Yii::t('app', 'Has Cedula'),
            'has_telefono' => Yii::t('app', 'Has Telefono'),
            'has_nro_cuenta' => Yii::t('app', 'Has Nro Cuenta'),
        ];
    }

    /**
     * Gets query for [[MetodosPagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetodosPagos()
    {
        return $this->hasMany(MetodosPago::class, ['tipo_id' => 'id']);
    }

}
