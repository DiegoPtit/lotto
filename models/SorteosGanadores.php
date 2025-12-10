<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sorteos_ganadores".
 *
 * @property int $id
 * @property int $id_sorteo
 * @property int $id_boleto
 * @property int|null $id_premio
 * @property string|null $numero_ganador
 * @property string $created_at
 *
 * @property Boletos $boleto
 * @property Premios $premio
 * @property Sorteos $sorteo
 */
class SorteosGanadores extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sorteos_ganadores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_premio', 'numero_ganador'], 'default', 'value' => null],
            [['id_sorteo', 'id_boleto'], 'required'],
            [['id_sorteo', 'id_boleto', 'id_premio'], 'integer'],
            [['created_at'], 'safe'],
            [['numero_ganador'], 'string', 'max' => 100],
            [['id_sorteo'], 'exist', 'skipOnError' => true, 'targetClass' => Sorteos::class, 'targetAttribute' => ['id_sorteo' => 'id']],
            [['id_boleto'], 'exist', 'skipOnError' => true, 'targetClass' => Boletos::class, 'targetAttribute' => ['id_boleto' => 'id']],
            [['id_premio'], 'exist', 'skipOnError' => true, 'targetClass' => Premios::class, 'targetAttribute' => ['id_premio' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_sorteo' => Yii::t('app', 'Id Sorteo'),
            'id_boleto' => Yii::t('app', 'Id Boleto'),
            'id_premio' => Yii::t('app', 'Id Premio'),
            'numero_ganador' => Yii::t('app', 'Numero Ganador'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Boleto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoleto()
    {
        return $this->hasOne(Boletos::class, ['id' => 'id_boleto']);
    }

    /**
     * Gets query for [[Premio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPremio()
    {
        return $this->hasOne(Premios::class, ['id' => 'id_premio']);
    }

    /**
     * Gets query for [[Sorteo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSorteo()
    {
        return $this->hasOne(Sorteos::class, ['id' => 'id_sorteo']);
    }

}
