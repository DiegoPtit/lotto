<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "testimonios".
 *
 * @property int $id
 * @property int $id_sorteo
 * @property int $id_jugador_ganador
 * @property string|null $url_media
 * @property string|null $titulo
 * @property string|null $descripcion
 * @property int|null $contador_likes
 * @property int $is_deleted
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property Comentarios[] $comentarios
 * @property Jugadores $jugadorGanador
 * @property Sorteos $sorteo
 */
class Testimonios extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'testimonios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url_media', 'titulo', 'descripcion', 'deleted_at', 'updated_at'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['id_sorteo', 'id_jugador_ganador', 'titulo'], 'required'],
            [['id_sorteo', 'id_jugador_ganador', 'contador_likes', 'is_deleted'], 'integer'],
            [['descripcion'], 'string'],
            [['deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['url_media'], 'string', 'max' => 255],
            [['titulo'], 'string', 'max' => 80],
            [['id_jugador_ganador'], 'exist', 'skipOnError' => true, 'targetClass' => Jugadores::class, 'targetAttribute' => ['id_jugador_ganador' => 'id']],
            [['id_sorteo'], 'exist', 'skipOnError' => true, 'targetClass' => Sorteos::class, 'targetAttribute' => ['id_sorteo' => 'id']],
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
            'id_jugador_ganador' => Yii::t('app', 'Id Jugador Ganador'),
            'url_media' => Yii::t('app', 'Url Media'),
            'titulo' => Yii::t('app', 'Titulo'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'contador_likes' => Yii::t('app', 'Contador Likes'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::class, ['id_testimonio' => 'id']);
    }

    /**
     * Gets query for [[JugadorGanador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJugadorGanador()
    {
        return $this->hasOne(Jugadores::class, ['id' => 'id_jugador_ganador']);
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
