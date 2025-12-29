<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas[] $rifasSorteadas */
/** @var array $ganadoresPorRifa */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Rifas Sorteadas';
?>

<style>
    .sorteados-page {
        padding: 60px 0;
        min-height: 100vh;
    }

    .page-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 1.1rem;
        color: #7f8c8d;
    }

    .sorteados-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }

    .rifa-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
    }

    .rifa-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .rifa-image-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .rifa-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rifa-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
    }

    .badge-sorteada {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
    }

    .rifa-content {
        padding: 1.5rem;
    }

    .rifa-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .rifa-description {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .winner-section {
        background: linear-gradient(135deg, #fef9e7 0%, #fcf3cf 100%);
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid #f39c12;
    }

    .winner-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: #f39c12;
        font-weight: 700;
    }

    .winner-info {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .winner-name {
        font-weight: 700;
        color: #2c3e50;
    }

    .winner-number {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .number-badge {
        background: #27ae60;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .winner-prize {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .rifa-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e8e8e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rifa-date {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .btn-testimonios {
        background: #9b59b6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-testimonios:hover {
        background: #8e44ad;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .sorteados-grid {
            grid-template-columns: 1fr;
        }

        .page-title {
            font-size: 2rem;
        }
    }
</style>

<div class="sorteados-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-trophy me-2"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p class="page-subtitle">
                Conoce a los afortunados ganadores de nuestras rifas
            </p>
        </div>

        <?php if (empty($rifasSorteadas)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-dice"></i>
                </div>
                <h3>Aún no hay rifas sorteadas</h3>
                <p>Vuelve pronto para conocer a nuestros próximos ganadores</p>
                <?= Html::a('<i class="fas fa-home me-1"></i> Volver al inicio', ['site/index'], ['class' => 'btn btn-primary mt-3']) ?>
            </div>
        <?php else: ?>
            <div class="sorteados-grid">
                <?php foreach ($rifasSorteadas as $rifa):
                    $ganadores = $ganadoresPorRifa[$rifa->id] ?? [];
                    $primerGanador = !empty($ganadores) ? $ganadores[0] : null;
                    ?>
                    <div class="rifa-card">
                        <div class="rifa-image-wrapper">
                            <?php if (!empty($rifa->img)): ?>
                                <img src="<?= Yii::getAlias('@web') . '/' . Html::encode($rifa->img) ?>"
                                    alt="<?= Html::encode($rifa->titulo) ?>" class="rifa-image">
                            <?php else: ?>
                                <div class="rifa-image-placeholder">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                            <?php endif; ?>
                            <span class="badge-sorteada">
                                <i class="fas fa-check me-1"></i> SORTEADA
                            </span>
                        </div>

                        <div class="rifa-content">
                            <h3 class="rifa-title"><?= Html::encode($rifa->titulo) ?></h3>
                            <p class="rifa-description"><?= Html::encode($rifa->descripcion ?: 'Sin descripción') ?></p>

                            <?php if ($primerGanador):
                                $jugador = $primerGanador->boleto->jugador ?? null;
                                $premio = $primerGanador->premio ?? null;
                                ?>
                                <div class="winner-section">
                                    <div class="winner-header">
                                        <i class="fas fa-crown"></i>
                                        Ganador
                                    </div>
                                    <div class="winner-info">
                                        <span class="winner-name">
                                            <i class="fas fa-user me-1"></i>
                                            <?= $jugador ? Html::encode($jugador->nombre) : 'Anónimo' ?>
                                        </span>
                                        <span class="winner-number">
                                            Número:
                                            <span class="number-badge">
                                                #<?= Html::encode($primerGanador->numero_ganador ?: 'N/A') ?>
                                            </span>
                                        </span>
                                        <?php if ($premio): ?>
                                            <span class="winner-prize">
                                                <i class="fas fa-gift me-1"></i>
                                                <?= Html::encode($premio->titulo) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="winner-section">
                                    <div class="winner-header">
                                        <i class="fas fa-info-circle"></i>
                                        Información
                                    </div>
                                    <p style="margin:0; color: #7f8c8d;">Ganador pendiente de registrar</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="rifa-footer">
                            <span class="rifa-date">
                                <i class="fas fa-calendar me-1"></i>
                                <?= Yii::$app->formatter->asDate($rifa->updated_at, 'medium') ?>
                            </span>
                            <?= Html::a('<i class="fas fa-comment me-1"></i> Testimonios', ['site/testimonios'], ['class' => 'btn-testimonios']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>