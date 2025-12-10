<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rifas".
 *
 * @property int $id
 * @property string $top_message
 * @property string $titulo
 * @property string $slug
 * @property string|null $descripcion
 * @property float $precio_boleto
 * @property string|null $moneda
 * @property string|null $img
 * @property int $max_numeros
 * @property string $estado
 * @property int $id_operador_registro
 * @property string|null $fecha_inicio
 * @property string|null $fecha_fin
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $is_deleted
 * @property string|null $deleted_at
 *
 * @property Boletos[] $boletos
 * @property Usuarios $operadorRegistro
 * @property Premios[] $premios
 * @property Sorteos[] $sorteos
 */
class Rifas extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_CERRADA = 'cerrada';
    const ESTADO_SORTEADA = 'sorteada';
    const ESTADO_CANCELADA = 'cancelada';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rifas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'img', 'fecha_inicio', 'fecha_fin', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['precio_boleto'], 'default', 'value' => 0.00],
            [['moneda'], 'default', 'value' => 'VES'],
            [['max_numeros'], 'default', 'value' => 1],
            [['estado'], 'default', 'value' => 'borrador'],
            [['is_deleted'], 'default', 'value' => 0],
            [['titulo', 'slug', 'id_operador_registro'], 'required'],
            [['descripcion', 'estado'], 'string'],
            [['precio_boleto'], 'number'],
            [['max_numeros', 'id_operador_registro', 'is_deleted'], 'integer'],
            [['fecha_inicio', 'fecha_fin', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['titulo', 'slug', 'img'], 'string', 'max' => 255],
            [['moneda'], 'string', 'max' => 10],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
            [['slug'], 'unique'],
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
            'titulo' => Yii::t('app', 'Titulo'),
            'slug' => Yii::t('app', 'Slug'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'precio_boleto' => Yii::t('app', 'Precio Boleto'),
            'moneda' => Yii::t('app', 'Moneda'),
            'img' => Yii::t('app', 'Img'),
            'max_numeros' => Yii::t('app', 'Max Numeros'),
            'estado' => Yii::t('app', 'Estado'),
            'id_operador_registro' => Yii::t('app', 'Id Operador Registro'),
            'fecha_inicio' => Yii::t('app', 'Fecha Inicio'),
            'fecha_fin' => Yii::t('app', 'Fecha Fin'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * Gets query for [[Boletos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoletos()
    {
        return $this->hasMany(Boletos::class, ['id_rifa' => 'id']);
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
     * Gets query for [[Premios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPremios()
    {
        return $this->hasMany(Premios::class, ['id_rifa' => 'id']);
    }

    /**
     * Gets query for [[Sorteos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSorteos()
    {
        return $this->hasMany(Sorteos::class, ['id_rifa' => 'id']);
    }


    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_BORRADOR => Yii::t('app', 'borrador'),
            self::ESTADO_ACTIVA => Yii::t('app', 'activa'),
            self::ESTADO_CERRADA => Yii::t('app', 'cerrada'),
            self::ESTADO_SORTEADA => Yii::t('app', 'sorteada'),
            self::ESTADO_CANCELADA => Yii::t('app', 'cancelada'),
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
    public function isEstadoBorrador()
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    public function setEstadoToBorrador()
    {
        $this->estado = self::ESTADO_BORRADOR;
    }

    /**
     * @return bool
     */
    public function isEstadoActiva()
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    public function setEstadoToActiva()
    {
        $this->estado = self::ESTADO_ACTIVA;
    }

    /**
     * @return bool
     */
    public function isEstadoCerrada()
    {
        return $this->estado === self::ESTADO_CERRADA;
    }

    public function setEstadoToCerrada()
    {
        $this->estado = self::ESTADO_CERRADA;
    }

    /**
     * @return bool
     */
    public function isEstadoSorteada()
    {
        return $this->estado === self::ESTADO_SORTEADA;
    }

    public function setEstadoToSorteada()
    {
        $this->estado = self::ESTADO_SORTEADA;
    }

    /**
     * @return bool
     */
    public function isEstadoCancelada()
    {
        return $this->estado === self::ESTADO_CANCELADA;
    }

    public function setEstadoToCancelada()
    {
        $this->estado = self::ESTADO_CANCELADA;
    }

    /**
     * Obtiene las mejores rifas (más vendidas) con estado activo
     * @param int $limit Número máximo de rifas a retornar
     * @return array|Rifas[]
     */
    public static function getMejoresRifas($limit = 10)
    {
        // Subconsulta para obtener IDs ordenados por conteo de boletos (compatible con ONLY_FULL_GROUP_BY)
        $subQuery = (new \yii\db\Query())
            ->select(['rifas.id', 'COUNT(boletos.id) as boletos_count'])
            ->from('rifas')
            ->leftJoin('boletos', 'boletos.id_rifa = rifas.id AND boletos.estado = :estado_pagado AND boletos.is_deleted = 0', [
                ':estado_pagado' => Boletos::ESTADO_PAGADO
            ])
            ->where(['rifas.estado' => self::ESTADO_ACTIVA])
            ->andWhere(['rifas.is_deleted' => 0])
            ->groupBy('rifas.id')
            ->orderBy(['boletos_count' => SORT_DESC])
            ->limit($limit);

        $rifaIds = $subQuery->column();

        if (empty($rifaIds)) {
            return [];
        }

        // Obtener los modelos completos manteniendo el orden
        $rifas = self::find()
            ->where(['id' => $rifaIds])
            ->indexBy('id')
            ->all();

        // Ordenar según el orden de $rifaIds
        $result = [];
        foreach ($rifaIds as $id) {
            if (isset($rifas[$id])) {
                $result[] = $rifas[$id];
            }
        }

        return $result;
    }

    /**
     * Obtiene la cantidad de números vendidos (de boletos pagados y reservados)
     * @return int
     */
    public function getNumerosVendidos()
    {
        return (int) BoletoNumeros::find()
            ->joinWith(['boleto'])
            ->where([
                'boletos.id_rifa' => $this->id,
                'boletos.is_deleted' => 0,
                'boleto_numeros.is_deleted' => 0
            ])
            ->andWhere(['IN', 'boletos.estado', [Boletos::ESTADO_PAGADO, Boletos::ESTADO_RESERVADO]])
            ->count();
    }

    /**
     * Obtiene el porcentaje de números vendidos
     * @return float
     */
    public function getPorcentajeVendido()
    {
        if ($this->max_numeros <= 0) {
            return 0;
        }

        $vendidos = $this->getNumerosVendidos();
        return round(($vendidos / $this->max_numeros) * 100, 2);
    }

    /**
     * Obtiene la cantidad de números disponibles
     * @return int
     */
    public function getNumerosDisponibles()
    {
        return max(0, $this->max_numeros - $this->getNumerosVendidos());
    }

    /**
     * Obtiene el ganador de esta rifa (si existe)
     * @return Jugadores|null
     */
    public function getGanador()
    {
        $sorteoGanador = SorteosGanadores::find()
            ->joinWith(['sorteo'])
            ->where(['sorteos.id_rifa' => $this->id])
            ->one();

        if ($sorteoGanador && $sorteoGanador->boleto && $sorteoGanador->boleto->jugador) {
            return $sorteoGanador->boleto->jugador;
        }

        return null;
    }

    /**
     * Obtiene el número ganador de esta rifa (si existe)
     * @return string|null
     */
    public function getNumeroGanador()
    {
        $sorteoGanador = SorteosGanadores::find()
            ->joinWith(['sorteo'])
            ->where(['sorteos.id_rifa' => $this->id])
            ->one();

        return $sorteoGanador ? $sorteoGanador->numero_ganador : null;
    }

    /**
     * Obtiene la fecha del sorteo más próximo
     * @return string|null
     */
    public function getFechaSorteo()
    {
        $sorteo = Sorteos::find()
            ->where(['id_rifa' => $this->id])
            ->orderBy(['fecha_sorteo' => SORT_ASC])
            ->one();

        return $sorteo ? $sorteo->fecha_sorteo : null;
    }

    /**
     * Calcula los segundos restantes hasta fecha_fin (recaudación)
     * @return int|null
     */
    public function getSegundosHastaFinRecaudacion()
    {
        if (!$this->fecha_fin) {
            return null;
        }

        $fechaFin = strtotime($this->fecha_fin);
        $ahora = time();

        return max(0, $fechaFin - $ahora);
    }

    /**
     * Calcula los segundos restantes hasta la fecha del sorteo
     * @return int|null
     */
    public function getSegundosHastaSorteo()
    {
        $fechaSorteo = $this->getFechaSorteo();
        if (!$fechaSorteo) {
            return null;
        }

        $fechaSorteoTime = strtotime($fechaSorteo);
        $ahora = time();

        return max(0, $fechaSorteoTime - $ahora);
    }
}
