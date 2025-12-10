<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vw_boletos_completo".
 *
 * @property int $id
 * @property string $codigo
 * @property int $id_rifa
 * @property string $rifa_titulo
 * @property int $id_jugador
 * @property string $jugador_nombre
 * @property int $cantidad_numeros
 * @property float $total_precio
 * @property string $estado
 * @property string $created_at
 */
class VwBoletosCompleto extends \yii\db\ActiveRecord
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
        return 'vw_boletos_completo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'default', 'value' => 0],
            [['cantidad_numeros'], 'default', 'value' => 1],
            [['total_precio'], 'default', 'value' => 0.00],
            [['estado'], 'default', 'value' => 'reservado'],
            [['id', 'id_rifa', 'id_jugador', 'cantidad_numeros'], 'integer'],
            [['codigo', 'id_rifa', 'rifa_titulo', 'id_jugador', 'jugador_nombre'], 'required'],
            [['total_precio'], 'number'],
            [['estado'], 'string'],
            [['created_at'], 'safe'],
            [['codigo'], 'string', 'max' => 100],
            [['rifa_titulo'], 'string', 'max' => 255],
            [['jugador_nombre'], 'string', 'max' => 200],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
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
            'rifa_titulo' => Yii::t('app', 'Rifa Titulo'),
            'id_jugador' => Yii::t('app', 'Id Jugador'),
            'jugador_nombre' => Yii::t('app', 'Jugador Nombre'),
            'cantidad_numeros' => Yii::t('app', 'Cantidad Numeros'),
            'total_precio' => Yii::t('app', 'Total Precio'),
            'estado' => Yii::t('app', 'Estado'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
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
