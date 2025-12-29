<?php

/** @var yii\web\View $this */
/** @var app\models\SorteosGanadores[] $ganadores */
/** @var app\models\Rifas[] $rifasSorteadas */
/** @var int|null $rifaFiltro */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Histórico de Ganadores';
?>

<style>
    .ganadores-page {
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

    .admin-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e8e8e8;
    }

    .admin-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .admin-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #34495e;
        margin: 0;
    }

    .filter-select {
        padding: 8px 36px 8px 14px;
        font-size: 0.9rem;
        font-weight: 500;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 10px center;
        background-size: 10px;
        cursor: pointer;
        appearance: none;
        min-width: 200px;
    }

    .ganador-card {
        background: linear-gradient(135deg, #fef9e7 0%, #fcf3cf 100%);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border-left: 5px solid #f39c12;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.25rem;
        align-items: center;
        transition: all 0.2s ease;
    }

    .ganador-card:hover {
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.2);
        transform: translateX(5px);
    }

    .trophy-icon {
        width: 60px;
        height: 60px;
        background: #f39c12;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .ganador-info {
        flex: 1;
    }

    .ganador-nombre {
        font-size: 1.125rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .ganador-rifa {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 0.5rem;
    }

    .ganador-details {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 0.85rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #5a6c7d;
    }

    .detail-item i {
        color: #f39c12;
    }

    .numero-ganador {
        background: #27ae60;
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.25rem;
        text-align: center;
        min-width: 80px;
    }

    .numero-label {
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        opacity: 0.9;
        display: block;
        margin-bottom: 0.25rem;
    }

    .btn-ver {
        background: #3498db;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-ver:hover {
        background: #2980b9;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .ganador-card {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .trophy-icon {
            margin: 0 auto;
        }

        .ganador-details {
            justify-content: center;
        }

        .numero-ganador {
            margin: 0 auto;
        }
    }
</style>

<div class="ganadores-page">
    <h1 class="page-title">
        <i class="fas fa-trophy me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <i class="fas fa-medal me-2"></i>
                Listado de Ganadores
            </h2>
            <select class="filter-select"
                onchange="window.location.href='<?= Url::to(['ganadores/index']) ?>&rifa=' + this.value">
                <option value="">Todas las rifas</option>
                <?php foreach ($rifasSorteadas as $rifa): ?>
                    <option value="<?= $rifa->id ?>" <?= $rifaFiltro == $rifa->id ? 'selected' : '' ?>>
                        <?= Html::encode($rifa->titulo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (empty($ganadores)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <p class="mb-0">No hay ganadores registrados</p>
            </div>
        <?php else: ?>
            <?php foreach ($ganadores as $ganador):
                $jugador = $ganador->boleto->jugador ?? null;
                $rifa = $ganador->sorteo->rifa ?? null;
                $premio = $ganador->premio ?? null;
                ?>
                <div class="ganador-card">
                    <div class="trophy-icon">
                        <i class="fas fa-trophy"></i>
                    </div>

                    <div class="ganador-info">
                        <div class="ganador-nombre">
                            <?= $jugador ? Html::encode($jugador->nombre) : 'Jugador desconocido' ?>
                        </div>
                        <div class="ganador-rifa">
                            <i class="fas fa-ticket-alt me-1"></i>
                            <?= $rifa ? Html::encode($rifa->titulo) : 'Rifa no disponible' ?>
                        </div>
                        <div class="ganador-details">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <?= Yii::$app->formatter->asDate($ganador->created_at, 'medium') ?>
                            </div>
                            <?php if ($premio): ?>
                                <div class="detail-item">
                                    <i class="fas fa-gift"></i>
                                    <?= Html::encode($premio->titulo) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($jugador && $jugador->telefono): ?>
                                <div class="detail-item">
                                    <i class="fas fa-phone"></i>
                                    <?= Html::encode($jugador->telefono) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; align-items: center;">
                        <div class="numero-ganador">
                            <span class="numero-label">Número</span>
                            <?= Html::encode($ganador->numero_ganador ?: 'N/A') ?>
                        </div>
                        <?= Html::a('<i class="fas fa-eye me-1"></i> Ver', ['view', 'id' => $ganador->id], ['class' => 'btn-ver']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>