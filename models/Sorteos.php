<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sorteos".
 *
 * @property int $id
 * @property int $id_rifa
 * @property string $fecha_sorteo
 * @property string|null $metodo_sorteo
 * @property string|null $descripcion
 * @property string $created_at
 *
 * @property Rifas $rifa
 * @property SorteosGanadores[] $sorteosGanadores
 */
class Sorteos extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sorteos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['metodo_sorteo', 'descripcion'], 'default', 'value' => null],
            [['id_rifa', 'fecha_sorteo'], 'required'],
            [['id_rifa'], 'integer'],
            [['fecha_sorteo', 'created_at'], 'safe'],
            [['descripcion'], 'string'],
            [['metodo_sorteo'], 'string', 'max' => 100],
            [['id_rifa'], 'exist', 'skipOnError' => true, 'targetClass' => Rifas::class, 'targetAttribute' => ['id_rifa' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_rifa' => Yii::t('app', 'Id Rifa'),
            'fecha_sorteo' => Yii::t('app', 'Fecha Sorteo'),
            'metodo_sorteo' => Yii::t('app', 'Metodo Sorteo'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Rifa]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRifa()
    {
        return $this->hasOne(Rifas::class, ['id' => 'id_rifa']);
    }

    /**
     * Gets query for [[SorteosGanadores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSorteosGanadores()
    {
        return $this->hasMany(SorteosGanadores::class, ['id_sorteo' => 'id']);
    }

}
