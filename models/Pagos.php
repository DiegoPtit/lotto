<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pagos".
 *
 * @property int $id
 * @property int $id_boleto
 * @property int|null $id_jugador
 * @property float $monto
 * @property string|null $moneda
 * @property string|null $transaction_id
 * @property int|null $id_metodo_pago
 * @property string $estado
 * @property string|null $comprobante_url
 * @property string|null $notas
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property Boletos $boleto
 * @property Jugadores $jugador
 * @property MetodosPago $metodoPago
 */
class Pagos extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_PENDING = 'pending';
    const ESTADO_CONFIRMED = 'confirmed';
    const ESTADO_FAILED = 'failed';
    const ESTADO_REFUNDED = 'refunded';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_jugador', 'transaction_id', 'id_metodo_pago', 'comprobante_url', 'notas', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['moneda'], 'default', 'value' => 'VES'],
            [['estado'], 'default', 'value' => 'pending'],
            [['is_deleted'], 'default', 'value' => 0],
            [['id_boleto', 'monto'], 'required'],
            [['id_boleto', 'id_jugador', 'id_metodo_pago', 'is_deleted'], 'integer'],
            [['monto'], 'number'],
            [['estado', 'notas'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['moneda'], 'string', 'max' => 10],
            [['transaction_id', 'comprobante_url'], 'string', 'max' => 255],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
            [['id_boleto'], 'exist', 'skipOnError' => true, 'targetClass' => Boletos::class, 'targetAttribute' => ['id_boleto' => 'id']],
            [['id_jugador'], 'exist', 'skipOnError' => true, 'targetClass' => Jugadores::class, 'targetAttribute' => ['id_jugador' => 'id']],
            [['id_metodo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => MetodosPago::class, 'targetAttribute' => ['id_metodo_pago' => 'id']],
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
            'id_jugador' => Yii::t('app', 'Id Jugador'),
            'monto' => Yii::t('app', 'Monto'),
            'moneda' => Yii::t('app', 'Moneda'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'id_metodo_pago' => Yii::t('app', 'Id Metodo Pago'),
            'estado' => Yii::t('app', 'Estado'),
            'comprobante_url' => Yii::t('app', 'Comprobante Url'),
            'notas' => Yii::t('app', 'Notas'),
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
     * Gets query for [[MetodoPago]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetodoPago()
    {
        return $this->hasOne(MetodosPago::class, ['id' => 'id_metodo_pago']);
    }


    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_PENDING => Yii::t('app', 'pending'),
            self::ESTADO_CONFIRMED => Yii::t('app', 'confirmed'),
            self::ESTADO_FAILED => Yii::t('app', 'failed'),
            self::ESTADO_REFUNDED => Yii::t('app', 'refunded'),
        ];
    }

    /**
     * @return string
     */
    public function displayEstado()
    {
        return self::optsEstado()[$this->estado];
    }

    /**
     * @return bool
     */
    public function isEstadoPending()
    {
        return $this->estado === self::ESTADO_PENDING;
    }

    public function setEstadoToPending()
    {
        $this->estado = self::ESTADO_PENDING;
    }

    /**
     * @return bool
     */
    public function isEstadoConfirmed()
    {
        return $this->estado === self::ESTADO_CONFIRMED;
    }

    public function setEstadoToConfirmed()
    {
        $this->estado = self::ESTADO_CONFIRMED;
    }

    /**
     * @return bool
     */
    public function isEstadoFailed()
    {
        return $this->estado === self::ESTADO_FAILED;
    }

    public function setEstadoToFailed()
    {
        $this->estado = self::ESTADO_FAILED;
    }

    /**
     * @return bool
     */
    public function isEstadoRefunded()
    {
        return $this->estado === self::ESTADO_REFUNDED;
    }

    public function setEstadoToRefunded()
    {
        $this->estado = self::ESTADO_REFUNDED;
    }
}
