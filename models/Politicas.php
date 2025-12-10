<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "politicas".
 *
 * @property int $id
 * @property string $tipo
 * @property string $titulo
 * @property string $descripcion
 * @property int|null $id_operador_registro
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property Usuarios $operadorRegistro
 */
class Politicas extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TIPO_PRIVACIDAD = 'PRIVACIDAD';
    const TIPO_RESPONSABILIDAD = 'RESPONSABILIDAD';
    const TIPO_DESARROLLADOR = 'DESARROLLADOR';
    const TIPO_LEGAL = 'LEGAL';
    const TIPO_OTRO = 'OTRO';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'politicas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operador_registro', 'updated_at'], 'default', 'value' => null],
            [['tipo', 'titulo', 'descripcion'], 'required'],
            [['tipo', 'descripcion'], 'string'],
            [['id_operador_registro'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['titulo'], 'string', 'max' => 255],
            ['tipo', 'in', 'range' => array_keys(self::optsTipo())],
            [['id_operador_registro'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::class, 'targetAttribute' => ['id_operador_registro' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipo' => Yii::t('app', 'Tipo'),
            'titulo' => Yii::t('app', 'Titulo'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'id_operador_registro' => Yii::t('app', 'Id Operador Registro'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[OperadorRegistro]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperadorRegistro()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'id_operador_registro']);
    }


    /**
     * column tipo ENUM value labels
     * @return string[]
     */
    public static function optsTipo()
    {
        return [
            self::TIPO_PRIVACIDAD => Yii::t('app', 'PRIVACIDAD'),
            self::TIPO_RESPONSABILIDAD => Yii::t('app', 'RESPONSABILIDAD'),
            self::TIPO_DESARROLLADOR => Yii::t('app', 'DESARROLLADOR'),
            self::TIPO_LEGAL => Yii::t('app', 'LEGAL'),
            self::TIPO_OTRO => Yii::t('app', 'OTRO'),
        ];
    }

    /**
     * @return string
     */
    public function displayTipo()
    {
        return self::optsTipo()[$this->tipo];
    }

    /**
     * @return bool
     */
    public function isTipoPrivacidad()
    {
        return $this->tipo === self::TIPO_PRIVACIDAD;
    }

    public function setTipoToPrivacidad()
    {
        $this->tipo = self::TIPO_PRIVACIDAD;
    }

    /**
     * @return bool
     */
    public function isTipoResponsabilidad()
    {
        return $this->tipo === self::TIPO_RESPONSABILIDAD;
    }

    public function setTipoToResponsabilidad()
    {
        $this->tipo = self::TIPO_RESPONSABILIDAD;
    }

    /**
     * @return bool
     */
    public function isTipoDesarrollador()
    {
        return $this->tipo === self::TIPO_DESARROLLADOR;
    }

    public function setTipoToDesarrollador()
    {
        $this->tipo = self::TIPO_DESARROLLADOR;
    }

    /**
     * @return bool
     */
    public function isTipoLegal()
    {
        return $this->tipo === self::TIPO_LEGAL;
    }

    public function setTipoToLegal()
    {
        $this->tipo = self::TIPO_LEGAL;
    }

    /**
     * @return bool
     */
    public function isTipoOtro()
    {
        return $this->tipo === self::TIPO_OTRO;
    }

    public function setTipoToOtro()
    {
        $this->tipo = self::TIPO_OTRO;
    }
}
