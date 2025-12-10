<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jugadores".
 *
 * @property int $id
 * @property string|null $cedula
 * @property string $nombre
 * @property string|null $pais
 * @property string|null $telefono
 * @property string|null $correo
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property Boletos[] $boletos
 * @property Pagos[] $pagos
 * @property TermsAcceptances[] $termsAcceptances
 */
class Jugadores extends \yii\db\ActiveRecord
{
    /**
     * @var int Count of numbers played (calculated field)
     */
    public $total_numeros;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jugadores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cedula', 'telefono', 'correo', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['pais'], 'default', 'value' => 'VE'],
            [['is_deleted'], 'default', 'value' => 0],
            [['nombre'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['is_deleted'], 'integer'],
            [['cedula', 'pais', 'telefono'], 'string', 'max' => 50],
            [['nombre', 'correo'], 'string', 'max' => 200],
            [['correo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cedula' => Yii::t('app', 'Cedula'),
            'nombre' => Yii::t('app', 'Nombre'),
            'pais' => Yii::t('app', 'Pais'),
            'telefono' => Yii::t('app', 'Telefono'),
            'correo' => Yii::t('app', 'Correo'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * Gets query for [[Boletos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoletos()
    {
        return $this->hasMany(Boletos::class, ['id_jugador' => 'id']);
    }

    /**
     * Gets query for [[Pagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagos()
    {
        return $this->hasMany(Pagos::class, ['id_jugador' => 'id']);
    }

    /**
     * Gets query for [[TermsAcceptances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTermsAcceptances()
    {
        return $this->hasMany(TermsAcceptances::class, ['id_jugador' => 'id']);
    }

}
