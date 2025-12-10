<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "premios".
 *
 * @property int $id
 * @property int $id_rifa
 * @property string $titulo
 * @property string|null $descripcion
 * @property float|null $valor_estimado
 * @property int $orden
 * @property int $entregado
 * @property string|null $entregado_en
 * @property string $created_at
 *
 * @property EvidenciaEntrega[] $evidenciaEntregas
 * @property Rifas $rifa
 * @property SorteosGanadores[] $sorteosGanadores
 */
class Premios extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'premios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'valor_estimado', 'entregado_en'], 'default', 'value' => null],
            [['orden'], 'default', 'value' => 1],
            [['entregado'], 'default', 'value' => 0],
            [['id_rifa', 'titulo'], 'required'],
            [['id_rifa', 'orden', 'entregado'], 'integer'],
            [['descripcion'], 'string'],
            [['valor_estimado'], 'number'],
            [['entregado_en', 'created_at'], 'safe'],
            [['titulo'], 'string', 'max' => 255],
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
            'titulo' => Yii::t('app', 'Titulo'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'valor_estimado' => Yii::t('app', 'Valor Estimado'),
            'orden' => Yii::t('app', 'Orden'),
            'entregado' => Yii::t('app', 'Entregado'),
            'entregado_en' => Yii::t('app', 'Entregado En'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[EvidenciaEntregas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenciaEntregas()
    {
        return $this->hasMany(EvidenciaEntrega::class, ['id_premio' => 'id']);
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
        return $this->hasMany(SorteosGanadores::class, ['id_premio' => 'id']);
    }

}
