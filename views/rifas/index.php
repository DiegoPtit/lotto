<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas[] $rifas */
/** @var yii\data\Pagination $pagination */
/** @var array $rankingIds */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\models\Rifas;

$this->title = 'Gestionar Rifas - ' . Yii::$app->name;
?>

<style>
    /* ==================== GLOBAL RESET ==================== */
    .rifas-index-page * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ==================== PAGE LAYOUT ==================== */
    .rifas-index-page {
        background: #ffffff;
        min-height: 100vh;
        padding: 40px 0 60px;
    }

    .container-custom {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* ==================== SECTION HEADER ==================== */
    .section-header {
        margin-bottom: 40px;
        text-align: center;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
    }

    .section-description {
        font-size: 1rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }

    /* ==================== RANKING & GLOW STYLES ==================== */
    @keyframes breatheGlow {
        0% {
            box-shadow: 0 0 5px rgba(0, 102, 204, 0.1);
            border-color: rgba(0, 102, 204, 0.3);
        }

        50% {
            box-shadow: 0 0 20px rgba(0, 102, 204, 0.4);
            border-color: rgba(0, 102, 204, 0.8);
        }

        100% {
            box-shadow: 0 0 5px rgba(0, 102, 204, 0.1);
            border-color: rgba(0, 102, 204, 0.3);
        }
    }

    .rifa-card-index.top-seller-glow {
        animation: fadeInUp 0.8s ease-out forwards, breatheGlow 4s infinite ease-in-out;
        border: 1px solid rgba(0, 102, 204, 0.5);
    }

    .ranking-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #ffffff;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 5;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
    }

    .text-muted-blue {
        color: #6c8caf;
    }

    /* ==================== RIFAS GRID ==================== */
    .rifas-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        padding-top: 20px;
    }

    @media (min-width: 640px) {
        .rifas-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .rifas-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* ==================== ANIMATIONS ==================== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 40px, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    /* ==================== RIFA CARD ==================== */
    .rifa-card-index {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        position: relative;
        overflow: visible;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        opacity: 0;
        animation: fadeInUp 0.8s ease-out forwards;
        z-index: 1;
        cursor: pointer;
    }

    .rifa-card-index:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        border-color: #0066cc;
        z-index: 10;
    }

    /* ==================== CARD IMAGE CONTAINER ==================== */
    .rifa-card-image-container {
        position: relative;
        width: 100%;
        height: 280px;
        overflow: hidden;
        border-radius: 12px;
        background: #f5f5f5;
    }

    .rifa-card-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease, filter 0.3s ease;
    }

    .rifa-card-index:hover .rifa-card-image-container img {
        transform: scale(1.08);
        filter: brightness(0.5);
    }

    .rifa-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
        transition: all 0.3s ease;
    }

    .rifa-card-index:hover .rifa-placeholder {
        background: linear-gradient(135deg, #d0d0d0 0%, #c0c0c0 100%);
    }

    .rifa-placeholder i {
        font-size: 4rem;
        color: #cccccc;
    }

    /* ==================== HOVER OVERLAY ==================== */
    .rifa-hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        padding: 20px;
        text-align: center;
        z-index: 2;
    }

    .rifa-card-index:hover .rifa-hover-overlay {
        opacity: 1;
    }

    .rifa-hover-title {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 16px;
        line-height: 1.4;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-ver-rifa {
        display: inline-block;
        padding: 12px 28px;
        background: #0066cc;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
    }

    .btn-ver-rifa:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.5);
        color: #ffffff;
    }

    /* ==================== MARQUEE PRECIO (LED EFFECT) ==================== */
    @keyframes scrollText {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    .rifa-precio-badge {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 102, 204, 0.95);
        color: #ffffff;
        height: 36px;
        overflow: hidden;
        display: flex;
        align-items: center;
        z-index: 5;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        letter-spacing: 1px;
        backdrop-filter: blur(2px);
        border-radius: 0 0 12px 12px;
    }

    .marquee-track {
        display: flex;
        align-items: center;
        white-space: nowrap;
        animation: scrollText 5s linear infinite;
    }

    .marquee-track span {
        padding-right: 40px;
    }

    /* ==================== PAGINATION ==================== */
    .pagination-wrapper {
        margin-top: 50px;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pagination-wrapper .pagination li a,
    .pagination-wrapper .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 0.9375rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #e0e0e0;
        background: #ffffff;
        color: #1a1a1a;
    }

    .pagination-wrapper .pagination li a:hover {
        background: #f5f5f5;
        border-color: #0066cc;
        color: #0066cc;
    }

    .pagination-wrapper .pagination li.active span {
        background: #0066cc;
        border-color: #0066cc;
        color: #ffffff;
    }

    .pagination-wrapper .pagination li.disabled span {
        background: #f9f9f9;
        color: #ccc;
        cursor: not-allowed;
    }

    /* ==================== MOBILE TOUCH BEHAVIOR ==================== */
    @media (hover: none) and (pointer: coarse) {
        /* En móviles, la tarjeta es un link directo */
        .rifa-card-index {
            cursor: pointer;
        }

        .rifa-hover-overlay {
            display: none;
        }

        .rifa-card-index:hover .rifa-card-image-container img {
            transform: none;
            filter: none;
        }

        .rifa-card-index:active {
            transform: scale(0.98);
        }
    }

    /* ==================== EMPTY STATE ==================== */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #666;
    }

    .empty-state i {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 1rem;
        color: #666;
        margin-bottom: 20px;
    }

    .btn-crear-rifa-empty {
        display: inline-block;
        padding: 14px 28px;
        background: #0066cc;
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .btn-crear-rifa-empty:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.5);
        color: #ffffff;
    }

    /* ==================== CREAR RIFA CARD ==================== */
    .crear-rifa-card {
        background: #ffffff;
        border: 2px dashed #0066cc;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        opacity: 0;
        animation: fadeInUp 0.8s ease-out forwards;
        z-index: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 280px;
    }

    .crear-rifa-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(0, 102, 204, 0.2);
        border-color: #0052a3;
        background: rgba(0, 102, 204, 0.03);
    }

    .crear-rifa-content {
        text-align: center;
        padding: 30px;
    }

    .crear-rifa-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(0, 102, 204, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        transition: all 0.3s ease;
    }

    .crear-rifa-card:hover .crear-rifa-icon {
        background: #0066cc;
        transform: scale(1.1);
    }

    .crear-rifa-icon i {
        font-size: 2.5rem;
        color: #0066cc;
        transition: all 0.3s ease;
    }

    .crear-rifa-card:hover .crear-rifa-icon i {
        color: #ffffff;
    }

    .crear-rifa-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0066cc;
        margin: 0;
    }

    /* ==================== FILTER DROPDOWN ==================== */
    .filter-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 30px;
    }

    .filter-label {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .filter-select {
        padding: 10px 40px 10px 16px;
        font-size: 0.95rem;
        font-weight: 500;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 12px center;
        background-size: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        min-width: 180px;
    }

    .filter-select:hover {
        border-color: #0066cc;
    }

    .filter-select:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
    }

    /* ==================== ESTADO BADGES ==================== */
    .rifa-estado-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 10;
    }

    .badge-activa {
        background: rgba(40, 167, 69, 0.9);
        color: #ffffff;
    }

    .badge-borrador {
        background: rgba(108, 117, 125, 0.9);
        color: #ffffff;
    }

    .badge-sorteada {
        background: rgba(40, 167, 69, 0.9);
        color: #ffffff;
    }

    .badge-cancelada {
        background: rgba(220, 53, 69, 0.9);
        color: #ffffff;
    }

    /* ==================== OVERLAYS ==================== */
    .rifa-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 5;
        pointer-events: none;
        text-align: center;
        padding: 20px;
    }

    /* Overlay gris (borrador, cancelada) */
    .overlay-gris {
        background: rgba(128, 128, 128, 0.7);
    }

    /* Overlay verde (sorteada) */
    .overlay-verde {
        background: rgba(40, 167, 69, 0.75);
    }

    .overlay-text-main {
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        margin-bottom: 8px;
    }

    .overlay-text-sub {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .overlay-text-cancelada {
        color: #dc3545;
        font-size: 1.75rem;
        font-weight: 800;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        text-transform: uppercase;
    }

    /* Card no cliqueable */
    .rifa-card-index.no-click {
        cursor: not-allowed;
        pointer-events: none;
    }

    /* ==================== COUNTDOWN TIMER ==================== */
    .rifa-countdown-container {
        position: absolute;
        bottom: 40px;
        left: 0;
        width: 100%;
        padding: 8px 12px;
        background: rgba(0, 0, 0, 0.75);
        z-index: 6;
    }

    .countdown-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: 4px;
        font-size: 0.7rem;
        color: #ffffff;
    }

    .countdown-row:last-child {
        margin-bottom: 0;
    }

    .countdown-label {
        font-weight: 400;
        opacity: 0.9;
    }

    .countdown-timer {
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
        color: #ffdd57;
    }

    .countdown-expired {
        color: #ff6b6b;
    }
</style>

<div class="rifas-index-page">
    <div class="container-custom">
        <!-- Header -->
        <div class="section-header">
            <h1 class="section-title">Administrar Rifas</h1>
            <p class="section-description">Gestiona todas tus rifas y edita sus detalles</p>
        </div>

        <?php if (empty($rifas)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3>No hay rifas disponibles</h3>
                <p>Comienza creando tu primera rifa</p>
                <?= Html::a('<i class="fas fa-plus me-2"></i>Crear una rifa', ['rifas/create'], ['class' => 'btn-crear-rifa-empty']) ?>
            </div>
        <?php else: ?>
            <!-- Rifas Grid -->
            <div class="rifas-grid">
                <!-- Crear Nueva Rifa Card (Siempre primero) -->
                <a href="<?= Url::to(['rifas/create']) ?>" class="crear-rifa-card" style="text-decoration: none;">
                    <div class="crear-rifa-content">
                        <div class="crear-rifa-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3 class="crear-rifa-title">Crear Nueva Rifa</h3>
                    </div>
                </a>

                <?php
                $delay = 0.15;
                foreach ($rifas as $rifa):
                    // Determinar ranking (solo para activas)
                    $rankIndex = array_search($rifa->id, $rankingIds);
                    $isTop1 = ($rankIndex === 0);
                    $rankNumber = $rankIndex + 1;
                    $isTop3 = ($rankIndex !== false && $rankIndex < 3);

                    // Clase para animación extra si es el #1
                    $extraClass = $isTop1 ? 'top-seller-glow' : '';
                    
                    // Determinar si es cliqueable
                    $esCancelada = $rifa->estado === Rifas::ESTADO_CANCELADA;
                    if ($esCancelada) {
                        $extraClass .= ' no-click';
                    }
                    
                    // URL para ver la rifa
                    $rifaUrl = Url::to(['rifas/view', 'id' => $rifa->id]);
                    
                    // Obtener datos para contadores (solo para activas)
                    $segundosRecaudacion = $rifa->getSegundosHastaFinRecaudacion();
                    $segundosSorteo = $rifa->getSegundosHastaSorteo();
                    
                    // Verificar si las fechas coinciden (mostrar solo un contador)
                    $fechasCoinciden = ($segundosRecaudacion !== null && $segundosSorteo !== null && abs($segundosRecaudacion - $segundosSorteo) < 60);
                    
                    // Obtener ganador (para sorteadas)
                    $ganador = $rifa->estado === Rifas::ESTADO_SORTEADA ? $rifa->getGanador() : null;
                    ?>
                    <div class="rifa-card-index <?= $extraClass ?>" 
                         style="animation-delay: <?= $delay ?>s"
                         <?php if (!$esCancelada): ?>onclick="window.location.href='<?= $rifaUrl ?>'"<?php endif; ?>
                         data-rifa-id="<?= $rifa->id ?>">
                        
                        <?php if ($isTop3 && $rifa->estado === Rifas::ESTADO_ACTIVA): ?>
                            <div class="ranking-badge text-muted-blue">
                                <i class="fas fa-crown"></i> #<?= $rankNumber ?> MÁS VENDIDA
                            </div>
                        <?php endif; ?>

                        <!-- Badge de Estado -->
                        <div class="rifa-estado-badge badge-<?= $rifa->estado ?>">
                            <?= strtoupper($rifa->estado) ?>
                        </div>

                        <!-- Imagen Container -->
                        <div class="rifa-card-image-container">
                            <?php if ($rifa->img): ?>
                                <?= Html::img(Yii::getAlias('@web') . $rifa->img, [
                                    'alt' => Html::encode($rifa->titulo),
                                ]) ?>
                            <?php else: ?>
                                <div class="rifa-placeholder">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Overlays según estado -->
                            <?php if ($rifa->estado === Rifas::ESTADO_BORRADOR): ?>
                                <div class="rifa-overlay overlay-gris">
                                    <span class="overlay-text-main">BORRADOR</span>
                                </div>
                            <?php elseif ($rifa->estado === Rifas::ESTADO_SORTEADA): ?>
                                <div class="rifa-overlay overlay-verde">
                                    <span class="overlay-text-main">Sorteada</span>
                                    <?php if ($ganador): ?>
                                        <span class="overlay-text-sub">Ganador: <?= Html::encode($ganador->nombre) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($rifa->estado === Rifas::ESTADO_CANCELADA): ?>
                                <div class="rifa-overlay overlay-gris">
                                    <span class="overlay-text-cancelada">CANCELADA</span>
                                </div>
                            <?php endif; ?>

                            <!-- Hover Overlay (Solo Desktop, solo si no tiene overlay de estado) -->
                            <?php if ($rifa->estado === Rifas::ESTADO_ACTIVA): ?>
                                <div class="rifa-hover-overlay">
                                    <h3 class="rifa-hover-title"><?= Html::encode($rifa->titulo) ?></h3>
                                    <?= Html::a('Ver Rifa', ['rifas/view', 'id' => $rifa->id], [
                                        'class' => 'btn-ver-rifa',
                                        'onclick' => 'event.stopPropagation();'
                                    ]) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Countdown Timers (solo para activas) -->
                            <?php if ($rifa->estado === Rifas::ESTADO_ACTIVA && ($segundosRecaudacion !== null || $segundosSorteo !== null)): ?>
                                <div class="rifa-countdown-container">
                                    <?php if ($fechasCoinciden): ?>
                                        <!-- Una sola fecha: mostrar un contador unificado -->
                                        <div class="countdown-row">
                                            <span class="countdown-label">Fin y sorteo:</span>
                                            <span class="countdown-timer" data-countdown="<?= $segundosRecaudacion ?>">
                                                <?= $segundosRecaudacion > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($segundosRecaudacion !== null): ?>
                                            <div class="countdown-row">
                                                <span class="countdown-label">Fin recaudación:</span>
                                                <span class="countdown-timer" data-countdown="<?= $segundosRecaudacion ?>">
                                                    <?= $segundosRecaudacion > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($segundosSorteo !== null): ?>
                                            <div class="countdown-row">
                                                <span class="countdown-label">Día del sorteo:</span>
                                                <span class="countdown-timer" data-countdown="<?= $segundosSorteo ?>">
                                                    <?= $segundosSorteo > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Marquee Precio -->
                            <?php
                            $simbolo = ($rifa->moneda === 'USD') ? '$' : 'Bs.';
                            $textoPrecio = "PRECIO POR BOLETO " . $simbolo . " " . number_format($rifa->precio_boleto, 2);
                            ?>
                            <div class="rifa-precio-badge">
                                <div class="marquee-track">
                                    <span><?= $textoPrecio ?></span>
                                    <span><?= $textoPrecio ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $delay += 0.15;
                endforeach;
                ?>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination'],
                    'linkContainerOptions' => ['class' => ''],
                    'disabledListItemSubTagOptions' => ['class' => 'disabled'],
                    'maxButtonCount' => 5,
                    'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
                    'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
                    'prevPageLabel' => '<i class="fas fa-angle-left"></i>',
                    'nextPageLabel' => '<i class="fas fa-angle-right"></i>',
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Countdown Timer Script
document.addEventListener('DOMContentLoaded', function() {
    const countdownElements = document.querySelectorAll('.countdown-timer[data-countdown]');
    
    function formatTime(totalSeconds) {
        if (totalSeconds <= 0) {
            return 'Finalizado';
        }
        
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        return String(days).padStart(2, '0') + ':' +
               String(hours).padStart(2, '0') + ':' +
               String(minutes).padStart(2, '0') + ':' +
               String(seconds).padStart(2, '0');
    }
    
    function updateCountdowns() {
        countdownElements.forEach(function(el) {
            let seconds = parseInt(el.getAttribute('data-countdown'), 10);
            
            if (seconds > 0) {
                seconds--;
                el.setAttribute('data-countdown', seconds);
                el.textContent = formatTime(seconds);
            } else {
                el.textContent = 'Finalizado';
                el.classList.add('countdown-expired');
            }
        });
    }
    
    // Inicializar valores
    countdownElements.forEach(function(el) {
        const seconds = parseInt(el.getAttribute('data-countdown'), 10);
        el.textContent = formatTime(seconds);
    });
    
    // Actualizar cada segundo
    setInterval(updateCountdowns, 1000);
});
</script>
