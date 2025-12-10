<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "terms_versions".
 *
 * @property int $id
 * @property string $titulo
 * @property string $contenido
 * @property string $version
 * @property string $created_at
 *
 * @property TermsAcceptances[] $termsAcceptances
 */
class TermsVersions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terms_versions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'contenido', 'version'], 'required'],
            [['contenido'], 'string'],
            [['created_at'], 'safe'],
            [['titulo'], 'string', 'max' => 255],
            [['version'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'titulo' => Yii::t('app', 'Titulo'),
            'contenido' => Yii::t('app', 'Contenido'),
            'version' => Yii::t('app', 'Version'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[TermsAcceptances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTermsAcceptances()
    {
        return $this->hasMany(TermsAcceptances::class, ['id_terms_version' => 'id']);
    }

    /**
     * Obtiene la versión más reciente de los términos y condiciones
     *
     * @return TermsVersions|null
     */
    public static function getLatestTerms()
    {
        return self::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }

}
