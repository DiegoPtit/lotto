<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "terms_acceptances".
 *
 * @property int $id
 * @property int $id_terms_version
 * @property int $id_boleto
 * @property int $id_jugador
 * @property string $aceptado_en
 *
 * @property Boletos $boleto
 * @property Jugadores $jugador
 * @property TermsVersions $termsVersion
 */
class TermsAcceptances extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terms_acceptances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_terms_version', 'id_boleto', 'id_jugador'], 'required'],
            [['id_terms_version', 'id_boleto', 'id_jugador'], 'integer'],
            [['aceptado_en'], 'safe'],
            [['id_terms_version'], 'exist', 'skipOnError' => true, 'targetClass' => TermsVersions::class, 'targetAttribute' => ['id_terms_version' => 'id']],
            [['id_boleto'], 'exist', 'skipOnError' => true, 'targetClass' => Boletos::class, 'targetAttribute' => ['id_boleto' => 'id']],
            [['id_jugador'], 'exist', 'skipOnError' => true, 'targetClass' => Jugadores::class, 'targetAttribute' => ['id_jugador' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_terms_version' => Yii::t('app', 'Id Terms Version'),
            'id_boleto' => Yii::t('app', 'Id Boleto'),
            'id_jugador' => Yii::t('app', 'Id Jugador'),
            'aceptado_en' => Yii::t('app', 'Aceptado En'),
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
     * Gets query for [[Jugador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJugador()
    {
        return $this->hasOne(Jugadores::class, ['id' => 'id_jugador']);
    }

    /**
     * Gets query for [[TermsVersion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTermsVersion()
    {
        return $this->hasOne(TermsVersions::class, ['id' => 'id_terms_version']);
    }

}
