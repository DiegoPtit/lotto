<?php

/** @var yii\web\View $this */
/** @var app\models\SorteosGanadores $model */

use yii\helpers\Html;

$jugador = $model->boleto->jugador ?? null;
$rifa = $model->sorteo->rifa ?? null;
$premio = $model->premio ?? null;
$boleto = $model->boleto ?? null;

$this->title = 'Detalle del Ganador';
?>

<style>
    .ganador-view-page {
        padding: 2rem 0;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #f39c12;
    }

    .winner-hero {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .winner-trophy {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2.5rem;
    }

    .winner-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .winner-number {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .admin-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e8e8e8;
    }

    .admin-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 1rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .premio-card {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
    }

    .premio-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .premio-desc {
        opacity: 0.9;
        line-height: 1.6;
    }

    .premio-valor {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 1.5rem;
        font-weight: 700;
    }

    .actions-bar {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.625rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid;
    }

    .btn-volver {
        background: white;
        color: #7f8c8d;
        border-color: #ddd;
    }

    .btn-volver:hover {
        background: #f8f9fa;
        color: #5a6c7d;
    }

    .btn-boleto {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .btn-boleto:hover {
        background: #2980b9;
        border-color: #2980b9;
        color: white;
    }

    @media (max-width: 576px) {
        .actions-bar {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="ganador-view-page">
    <h1 class="page-title">
        <i class="fas fa-trophy me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <!-- Hero Section -->
    <div class="winner-hero">
        <div class="winner-trophy">
            <i class="fas fa-trophy"></i>
        </div>
        <div class="winner-name">
            <?= $jugador ? Html::encode($jugador->nombre) : 'Jugador desconocido' ?>
        </div>
        <div class="winner-number">
            <i class="fas fa-star me-1"></i>
            Número Ganador: <?= Html::encode($model->numero_ganador ?: 'N/A') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Información del Ganador -->
            <div class="admin-card">
                <h2 class="admin-card-title">
                    <i class="fas fa-user me-2"></i>
                    Información del Ganador
                </h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nombre</span>
                        <span class="detail-value"><?= $jugador ? Html::encode($jugador->nombre) : 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Teléfono</span>
                        <span
                            class="detail-value"><?= $jugador && $jugador->telefono ? Html::encode($jugador->telefono) : 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Correo</span>
                        <span
                            class="detail-value"><?= $jugador && $jugador->correo ? Html::encode($jugador->correo) : 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Código Boleto</span>
                        <span class="detail-value"><?= $boleto ? Html::encode($boleto->codigo) : 'N/A' ?></span>
                    </div>
                </div>
            </div>

            <!-- Información de la Rifa -->
            <div class="admin-card">
                <h2 class="admin-card-title">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Información de la Rifa
                </h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Título</span>
                        <span class="detail-value"><?= $rifa ? Html::encode($rifa->titulo) : 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fecha del Sorteo</span>
                        <span class="detail-value">
                            <?= $model->sorteo ? Yii::$app->formatter->asDatetime($model->sorteo->fecha_sorteo, 'medium') : 'N/A' ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Registro</span>
                        <span
                            class="detail-value"><?= Yii::$app->formatter->asDatetime($model->created_at, 'medium') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Premio -->
            <?php if ($premio): ?>
                <div class="premio-card">
                    <div class="premio-title">
                        <i class="fas fa-gift me-2"></i>
                        <?= Html::encode($premio->titulo) ?>
                    </div>
                    <?php if ($premio->descripcion): ?>
                        <div class="premio-desc">
                            <?= Html::encode($premio->descripcion) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($premio->valor_estimado): ?>
                        <div class="premio-valor">
                            Bs. <?= Yii::$app->formatter->asDecimal($premio->valor_estimado, 2) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="admin-card">
                    <h2 class="admin-card-title">
                        <i class="fas fa-gift me-2"></i>
                        Premio
                    </h2>
                    <p style="color: #7f8c8d; text-align: center; padding: 1rem;">
                        No hay información de premio asociada
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="actions-bar">
        <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver al Histórico', ['index'], ['class' => 'btn-action btn-volver']) ?>
        <?php if ($boleto): ?>
            <?= Html::a('<i class="fas fa-ticket-alt me-1"></i> Ver Boleto', ['/panel/boletos-view', 'id' => $boleto->id], ['class' => 'btn-action btn-boleto']) ?>
        <?php endif; ?>
    </div>
</div>