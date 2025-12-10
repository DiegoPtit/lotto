<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "evidencia_entrega".
 *
 * @property int $id
 * @property int|null $id_premio
 * @property int|null $id_boleto
 * @property string|null $url_media
 * @property string|null $tipo_media
 * @property string|null $descripcion
 * @property string $created_at
 *
 * @property Boletos $boleto
 * @property Premios $premio
 */
class EvidenciaEntrega extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TIPO_MEDIA_FOTO = 'foto';
    const TIPO_MEDIA_VIDEO = 'video';
    const TIPO_MEDIA_DOCUMENTO = 'documento';
    const TIPO_MEDIA_OTRO = 'otro';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evidencia_entrega';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_premio', 'id_boleto', 'url_media', 'descripcion'], 'default', 'value' => null],
            [['tipo_media'], 'default', 'value' => 'foto'],
            [['id_premio', 'id_boleto'], 'integer'],
            [['tipo_media', 'descripcion'], 'string'],
            [['created_at'], 'safe'],
            [['url_media'], 'string', 'max' => 255],
            ['tipo_media', 'in', 'range' => array_keys(self::optsTipoMedia())],
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
            'id_premio' => Yii::t('app', 'Id Premio'),
            'id_boleto' => Yii::t('app', 'Id Boleto'),
            'url_media' => Yii::t('app', 'Url Media'),
            'tipo_media' => Yii::t('app', 'Tipo Media'),
            'descripcion' => Yii::t('app', 'Descripcion'),
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
     * column tipo_media ENUM value labels
     * @return string[]
     */
    public static function optsTipoMedia()
    {
        return [
            self::TIPO_MEDIA_FOTO => Yii::t('app', 'foto'),
            self::TIPO_MEDIA_VIDEO => Yii::t('app', 'video'),
            self::TIPO_MEDIA_DOCUMENTO => Yii::t('app', 'documento'),
            self::TIPO_MEDIA_OTRO => Yii::t('app', 'otro'),
        ];
    }

    /**
     * @return string
     */
    public function displayTipoMedia()
    {
        return self::optsTipoMedia()[$this->tipo_media];
    }

    /**
     * @return bool
     */
    public function isTipoMediaFoto()
    {
        return $this->tipo_media === self::TIPO_MEDIA_FOTO;
    }

    public function setTipoMediaToFoto()
    {
        $this->tipo_media = self::TIPO_MEDIA_FOTO;
    }

    /**
     * @return bool
     */
    public function isTipoMediaVideo()
    {
        return $this->tipo_media === self::TIPO_MEDIA_VIDEO;
    }

    public function setTipoMediaToVideo()
    {
        $this->tipo_media = self::TIPO_MEDIA_VIDEO;
    }

    /**
     * @return bool
     */
    public function isTipoMediaDocumento()
    {
        return $this->tipo_media === self::TIPO_MEDIA_DOCUMENTO;
    }

    public function setTipoMediaToDocumento()
    {
        $this->tipo_media = self::TIPO_MEDIA_DOCUMENTO;
    }

    /**
     * @return bool
     */
    public function isTipoMediaOtro()
    {
        return $this->tipo_media === self::TIPO_MEDIA_OTRO;
    }

    public function setTipoMediaToOtro()
    {
        $this->tipo_media = self::TIPO_MEDIA_OTRO;
    }
}
