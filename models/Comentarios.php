<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comentarios".
 *
 * @property int $id
 * @property int $id_testimonio
 * @property int|null $id_comentario_padre
 * @property string $nombre
 * @property string $mensaje
 * @property int|null $contador_likes
 * @property int $is_deleted
 * @property string|null $deleted_at
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property Comentarios $comentarioPadre
 * @property Comentarios[] $comentarios
 * @property Testimonios $testimonio
 */
class Comentarios extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comentarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_comentario_padre', 'deleted_at', 'updated_at'], 'default', 'value' => null],
            [['is_deleted'], 'default', 'value' => 0],
            [['id_testimonio', 'nombre', 'mensaje'], 'required'],
            [['id_testimonio', 'id_comentario_padre', 'contador_likes', 'is_deleted'], 'integer'],
            [['mensaje'], 'string'],
            [['deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['nombre'], 'string', 'max' => 200],
            [['id_comentario_padre'], 'exist', 'skipOnError' => true, 'targetClass' => Comentarios::class, 'targetAttribute' => ['id_comentario_padre' => 'id']],
            [['id_testimonio'], 'exist', 'skipOnError' => true, 'targetClass' => Testimonios::class, 'targetAttribute' => ['id_testimonio' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_testimonio' => Yii::t('app', 'Id Testimonio'),
            'id_comentario_padre' => Yii::t('app', 'Id Comentario Padre'),
            'nombre' => Yii::t('app', 'Nombre'),
            'mensaje' => Yii::t('app', 'Mensaje'),
            'contador_likes' => Yii::t('app', 'Contador Likes'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[ComentarioPadre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarioPadre()
    {
        return $this->hasOne(Comentarios::class, ['id' => 'id_comentario_padre']);
    }

    /**
     * Gets query for [[Comentarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::class, ['id_comentario_padre' => 'id']);
    }

    /**
     * Gets query for [[Testimonio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestimonio()
    {
        return $this->hasOne(Testimonios::class, ['id' => 'id_testimonio']);
    }

}
