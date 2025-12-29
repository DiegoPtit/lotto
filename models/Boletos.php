<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "boletos".
 *
 * @property int $id
 * @property string $codigo
 * @property int $id_rifa
 * @property int $id_jugador
 * @property int $cantidad_numeros
 * @property float $total_precio
 * @property string $estado
 * @property int $acepta_condiciones
 * @property string|null $reserved_until
 * @property string|null $reserva_token
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 * @property int $is_readed
 *
 * @property BoletoNumeros[] $boletoNumeros
 * @property EvidenciaEntrega[] $evidenciaEntregas
 * @property Jugadores $jugador
 * @property Pagos[] $pagos
 * @property Rifas $rifa
 * @property SorteosGanadores[] $sorteosGanadores
 * @property TermsAcceptances[] $termsAcceptances
 */
class Boletos extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_RESERVADO = 'reservado';
    const ESTADO_PAGADO = 'pagado';
    const ESTADO_ANULADO = 'anulado';
    const ESTADO_REEMBOLSADO = 'reembolsado';
    const ESTADO_GANADOR = 'ganador';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boletos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reserved_until', 'reserva_token', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['cantidad_numeros'], 'default', 'value' => 1],
            [['total_precio'], 'default', 'value' => 0.00],
            [['estado'], 'default', 'value' => 'reservado'],
            [['is_deleted'], 'default', 'value' => 0],
            [['is_readed'], 'default', 'value' => 0],
            [['codigo', 'id_rifa', 'id_jugador'], 'required'],
            [['id_rifa', 'id_jugador', 'cantidad_numeros', 'acepta_condiciones', 'is_deleted', 'is_readed'], 'integer'],
            [['total_precio'], 'number'],
            [['estado'], 'string'],
            [['reserved_until', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['codigo', 'reserva_token'], 'string', 'max' => 100],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
            [['id_jugador'], 'exist', 'skipOnError' => true, 'targetClass' => Jugadores::class, 'targetAttribute' => ['id_jugador' => 'id']],
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
            'codigo' => Yii::t('app', 'Codigo'),
            'id_rifa' => Yii::t('app', 'Id Rifa'),
            'id_jugador' => Yii::t('app', 'Id Jugador'),
            'cantidad_numeros' => Yii::t('app', 'Cantidad Numeros'),
            'total_precio' => Yii::t('app', 'Total Precio'),
            'estado' => Yii::t('app', 'Estado'),
            'acepta_condiciones' => Yii::t('app', 'Acepta Condiciones'),
            'reserved_until' => Yii::t('app', 'Reserved Until'),
            'reserva_token' => Yii::t('app', 'Reserva Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'is_readed' => Yii::t('app', 'Is Readed'),
        ];
    }

    /**
     * Gets query for [[BoletoNumeros]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoletoNumeros()
    {
        return $this->hasMany(BoletoNumeros::class, ['id_boleto' => 'id']);
    }

    /**
     * Gets query for [[EvidenciaEntregas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenciaEntregas()
    {
        return $this->hasMany(EvidenciaEntrega::class, ['id_boleto' => 'id']);
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
     * Gets query for [[Pagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagos()
    {
        return $this->hasMany(Pagos::class, ['id_boleto' => 'id']);
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
        return $this->hasMany(SorteosGanadores::class, ['id_boleto' => 'id']);
    }

    /**
     * Gets query for [[TermsAcceptances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTermsAcceptances()
    {
        return $this->hasMany(TermsAcceptances::class, ['id_boleto' => 'id']);
    }


    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_RESERVADO => Yii::t('app', 'reservado'),
            self::ESTADO_PAGADO => Yii::t('app', 'pagado'),
            self::ESTADO_ANULADO => Yii::t('app', 'anulado'),
            self::ESTADO_REEMBOLSADO => Yii::t('app', 'reembolsado'),
            self::ESTADO_GANADOR => Yii::t('app', 'ganador'),
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
    public function isEstadoReservado()
    {
        return $this->estado === self::ESTADO_RESERVADO;
    }

    public function setEstadoToReservado()
    {
        $this->estado = self::ESTADO_RESERVADO;
    }

    /**
     * @return bool
     */
    public function isEstadoPagado()
    {
        return $this->estado === self::ESTADO_PAGADO;
    }

    public function setEstadoToPagado()
    {
        $this->estado = self::ESTADO_PAGADO;
    }

    /**
     * @return bool
     */
    public function isEstadoAnulado()
    {
        return $this->estado === self::ESTADO_ANULADO;
    }

    public function setEstadoToAnulado()
    {
        $this->estado = self::ESTADO_ANULADO;
    }

    /**
     * @return bool
     */
    public function isEstadoReembolsado()
    {
        return $this->estado === self::ESTADO_REEMBOLSADO;
    }

    public function setEstadoToReembolsado()
    {
        $this->estado = self::ESTADO_REEMBOLSADO;
    }

    /**
     * @return bool
     */
    public function isEstadoGanador()
    {
        return $this->estado === self::ESTADO_GANADOR;
    }

    public function setEstadoToGanador()
    {
        $this->estado = self::ESTADO_GANADOR;
    }
}
