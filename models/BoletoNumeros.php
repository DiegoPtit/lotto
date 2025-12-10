<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "boleto_numeros".
 *
 * @property int $id
 * @property int $id_boleto
 * @property string $numero
 * @property int $is_golden
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property Boletos $boleto
 */
class BoletoNumeros extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boleto_numeros';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at', 'deleted_at'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['id_boleto', 'numero'], 'required'],
            [['id_boleto', 'is_golden', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['numero'], 'string', 'max' => 100],
            [['id_boleto'], 'exist', 'skipOnError' => true, 'targetClass' => Boletos::class, 'targetAttribute' => ['id_boleto' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_boleto' => Yii::t('app', 'Id Boleto'),
            'numero' => Yii::t('app', 'Numero'),
            'is_golden' => Yii::t('app', 'Is Golden'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
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

}
