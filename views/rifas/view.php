<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas $rifa */
/** @var app\models\Boletos[] $boletos */
/** @var int $numerosJugados */
/** @var float $porcentajeProgreso */
/** @var app\models\Jugadores[] $topJugadores */

use yii\bootstrap5\Html;
use app\models\Rifas;

$this->title = 'Detalles de la Rifa: ' . Html::encode($rifa->titulo);
?>

<style>
    .rifa-view-container {
        min-height: 100vh;
        padding: 1rem 0;
    }

    .rifa-grid {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 1.5rem;
        min-height: calc(100vh - 2rem);
    }

    /* Columna 1: Sidebar Sticky */
    .rifa-sidebar {
        position: sticky;
        top: 1rem;
        height: fit-content;
    }

    .rifa-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e8e8e8;
        overflow: hidden;
    }

    .rifa-image-section {
        width: 100%;
        height: 45vh;
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .rifa-image-section::before {
        content: '';
        display: block;
        padding-bottom: 100%;
    }

    .rifa-image-section img,
    .rifa-image-section .image-fallback {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .rifa-image-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-fallback {
        font-size: 4rem;
        color: #bdc3c7;
    }

    .rifa-info-section {
        padding: 1.25rem 1.5rem;
    }

    .rifa-titulo {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        text-align: left;
        line-height: 1.4;
    }

    .rifa-descripcion {
        font-size: 0.875rem;
        color: #7f8c8d;
        line-height: 1.5;
        margin-bottom: 1rem;
        white-space: pre-wrap;
        text-align: left;
    }

    .rifa-precio-container {
        display: inline-block;
        background: #d4a017;
        color: white;
        padding: 0.35rem 0.875rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .rifa-footer {
        display: flex;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e8e8e8;
    }

    .rifa-footer .btn {
        flex: 1;
        border-radius: 6px;
        padding: 0.625rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: 1px solid;
    }

    .btn-editar {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .btn-editar:hover {
        background: #2980b9;
        border-color: #2980b9;
        color: white;
    }

    .btn-cancelar {
        background: white;
        color: #7f8c8d;
        border-color: #ddd;
    }

    .btn-cancelar:hover {
        background: #f8f9fa;
        color: #5a6c7d;
    }

    /* Columna 2: Contenido Principal */
    .rifa-main-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .boletos-section {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-header-custom {
        background: #f8f9fa;
        border-bottom: 2px solid #e8e8e8;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        font-weight: 600;
        color: #34495e;
    }

    .card-body-scroll {
        max-height: 55vh;
        overflow-y: auto;
        padding: 1rem 1.25rem;
    }

    .boleto-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        margin-bottom: 0.625rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .boleto-row:hover {
        background: #e9ecef;
        border-left-color: #3498db;
    }

    .boleto-info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.875rem;
        flex: 1;
        align-items: center;
    }

    .boleto-field {
        display: flex;
        flex-direction: column;
    }

    .boleto-label {
        font-size: 0.7rem;
        color: #95a5a6;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        letter-spacing: 0.3px;
    }

    .boleto-value {
        font-size: 0.875rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .estado-badge {
        padding: 0.2rem 0.625rem;
        border-radius: 3px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }

    .estado-reservado {
        background: #f8d7da;
        color: #721c24;
    }

    .estado-pagado {
        background: #d4edda;
        color: #155724;
    }

    .estado-anulado {
        background: #e2e3e5;
        color: #383d41;
    }

    .estado-reembolsado {
        background: #d1ecf1;
        color: #0c5460;
    }

    .btn-ver-detalle {
        background: transparent;
        color: #3498db;
        border: 1px solid #3498db;
        padding: 0.4rem 0.875rem;
        border-radius: 5px;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
        margin-left: 0.875rem;
        text-decoration: none;
    }

    .btn-ver-detalle:hover {
        background: #3498db;
        color: white;
    }

    .card-footer-custom {
        background: #f8f9fa;
        border-top: 1px solid #e8e8e8;
        padding: 1rem 1.25rem;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.625rem;
        font-size: 0.8125rem;
        color: #5a6c7d;
    }

    .progress-info strong {
        font-weight: 600;
        color: #2c3e50;
    }

    .progress {
        height: 20px;
        border-radius: 6px;
        background: #e9ecef;
    }

    .progress-bar {
        background: #3498db;
        font-weight: 500;
        font-size: 0.75rem;
        line-height: 20px;
    }

    /* Sección Top Jugadores */
    .top-jugadores-section {
        height: fit-content;
    }

    .jugador-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        margin-bottom: 0.625rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }

    .jugador-row:hover {
        background: #e9ecef;
    }

    .jugador-main {
        display: flex;
        align-items: center;
        flex: 1;
        gap: 0.875rem;
    }

    .jugador-rank {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: white;
        flex-shrink: 0;
    }

    .rank-1 {
        background: #d4a017;
    }

    .rank-2 {
        background: #95a5a6;
    }

    .rank-3 {
        background: #cd7f32;
    }

    .jugador-info {
        flex: 1;
    }

    .jugador-nombre {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.2rem;
        font-size: 0.9rem;
    }

    .jugador-telefono {
        font-size: 0.8125rem;
        color: #7f8c8d;
    }

    .jugador-numeros {
        background: #3498db;
        color: white;
        padding: 0.4rem 0.75rem;
        border-radius: 5px;
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .jugador-numeros small {
        font-size: 0.75rem;
        font-weight: 400;
        opacity: 0.9;
    }

    .empty-state {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 2.5rem;
        color: #bdc3c7;
        margin-bottom: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .rifa-grid {
            grid-template-columns: 1fr;
        }

        .rifa-sidebar {
            position: relative;
            top: 0;
        }

        .rifa-image-section {
            height: 60vw;
        }

        .boleto-info-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.625rem;
        }

        .btn-ver-detalle {
            margin-left: 0;
            margin-top: 0.625rem;
            width: 100%;
        }

        .boleto-row {
            flex-direction: column;
            align-items: stretch;
        }
    }

    @media (max-width: 576px) {
        .rifa-grid {
            gap: 1rem;
        }

        .rifa-titulo {
            font-size: 1.125rem;
        }

        .rifa-descripcion {
            font-size: 0.8125rem;
        }

        .boleto-info-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .boleto-label {
            font-size: 0.65rem;
        }

        .boleto-value {
            font-size: 0.8125rem;
        }

        .jugador-main {
            flex-wrap: wrap;
        }

        .jugador-info {
            flex-basis: 100%;
            order: 2;
        }

        .jugador-rank {
            order: 1;
        }

        .jugador-numeros {
            order: 3;
            margin-top: 0.5rem;
            flex-basis: 100%;
            text-align: center;
        }

        .card-header-custom {
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }

        .card-body-scroll {
            padding: 0.875rem 1rem;
        }
    }

    /* ==================== ACTION BUTTONS ==================== */
    .btn-action {
        flex: 1;
        border-radius: 6px;
        padding: 0.625rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: 1px solid;
        text-decoration: none;
        text-align: center;
    }

    .btn-activar {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .btn-activar:hover {
        background: #218838;
        border-color: #218838;
        color: white;
    }

    .btn-sortear {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .btn-sortear:hover {
        background: #218838;
        border-color: #218838;
        color: white;
    }

    /* ==================== COUNTDOWN SECTION ==================== */
    .rifa-countdown-section {
        padding: 0.875rem 1.5rem;
        border-top: 1px solid #e8e8e8;
        background: #f8f9fa;
    }

    .countdown-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.8rem;
    }

    .countdown-item:not(:last-child) {
        border-bottom: 1px dashed #e0e0e0;
    }

    .countdown-item-label {
        color: #7f8c8d;
        font-weight: 500;
    }

    .countdown-item-timer {
        font-weight: 700;
        font-family: 'Courier New', monospace;
        color: #2c3e50;
        letter-spacing: 1px;
    }

    .countdown-expired {
        color: #e74c3c !important;
    }

    /* ==================== ESTADO BADGE ==================== */
    .rifa-estado-container {
        padding: 0.5rem 1.5rem;
        border-top: 1px solid #e8e8e8;
    }

    .rifa-estado-badge-view {
        display: inline-block;
        padding: 0.35rem 0.875rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-activa-view {
        background: #d4edda;
        color: #155724;
    }

    .badge-borrador-view {
        background: #e2e3e5;
        color: #383d41;
    }

    .badge-sorteada-view {
        background: #d4edda;
        color: #155724;
    }

    .badge-cancelada-view {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<div class="rifa-view-container">
    <div class="rifa-grid">
        <!-- Columna 1: Sidebar -->
        <div class="rifa-sidebar">
            <div class="rifa-card">
                <!-- Imagen -->
                <div class="rifa-image-section">
                    <?php if (!empty($rifa->img)): ?>
                        <img src="<?= Html::encode($rifa->img) ?>" alt="<?= Html::encode($rifa->titulo) ?>">
                    <?php else: ?>
                        <i class="fas fa-ticket-alt image-fallback"></i>
                    <?php endif; ?>
                </div>

                <!-- Badge de Estado -->
                <div class="rifa-estado-container">
                    <span class="rifa-estado-badge-view badge-<?= $rifa->estado ?>-view">
                        <?= strtoupper($rifa->estado) ?>
                    </span>
                </div>

                <!-- Información -->
                <div class="rifa-info-section">
                    <h2 class="rifa-titulo"><?= Html::encode($rifa->titulo) ?></h2>
                    <p class="rifa-descripcion"><?= Html::encode($rifa->descripcion ?: 'Sin descripción disponible') ?>
                    </p>
                    <div class="rifa-precio-container">
                        Bs. <?= Yii::$app->formatter->asDecimal($rifa->precio_boleto, 2) ?>
                    </div>
                </div>

                <!-- Countdown Section (solo para activas) -->
                <?php 
                $segundosRecaudacion = $rifa->getSegundosHastaFinRecaudacion();
                $segundosSorteo = $rifa->getSegundosHastaSorteo();
                $fechasCoinciden = ($segundosRecaudacion !== null && $segundosSorteo !== null && abs($segundosRecaudacion - $segundosSorteo) < 60);
                if ($rifa->estado === Rifas::ESTADO_ACTIVA && ($segundosRecaudacion !== null || $segundosSorteo !== null)): 
                ?>
                    <div class="rifa-countdown-section">
                        <?php if ($fechasCoinciden): ?>
                            <div class="countdown-item">
                                <span class="countdown-item-label"><i class="fas fa-clock me-1"></i>Fin y sorteo:</span>
                                <span class="countdown-item-timer" data-countdown="<?= $segundosRecaudacion ?>">
                                    <?= $segundosRecaudacion > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <?php if ($segundosRecaudacion !== null): ?>
                                <div class="countdown-item">
                                    <span class="countdown-item-label"><i class="fas fa-clock me-1"></i>Fin recaudación:</span>
                                    <span class="countdown-item-timer" data-countdown="<?= $segundosRecaudacion ?>">
                                        <?= $segundosRecaudacion > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ($segundosSorteo !== null): ?>
                                <div class="countdown-item">
                                    <span class="countdown-item-label"><i class="fas fa-gift me-1"></i>Día del sorteo:</span>
                                    <span class="countdown-item-timer" data-countdown="<?= $segundosSorteo ?>">
                                        <?= $segundosSorteo > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Botón de Acción Principal (si aplica) -->
                <?php if ($rifa->estado === Rifas::ESTADO_BORRADOR || $rifa->estado === Rifas::ESTADO_ACTIVA): ?>
                    <div class="rifa-footer" style="border-top: none; padding-top: 0;">
                        <?php if ($rifa->estado === Rifas::ESTADO_BORRADOR): ?>
                            <!-- Botón Activar Rifa -->
                            <?= Html::a('<i class="fas fa-play me-1"></i> Activar Rifa', ['rifas/activar', 'id' => $rifa->id], ['class' => 'btn btn-action btn-activar', 'style' => 'width: 100%;']) ?>
                        <?php elseif ($rifa->estado === Rifas::ESTADO_ACTIVA): ?>
                            <!-- Botón Establecer Número Ganador -->
                            <?= Html::a('<i class="fas fa-trophy me-1"></i> Establecer número ganador', ['rifas/sortear', 'id' => $rifa->id], ['class' => 'btn btn-action btn-sortear', 'style' => 'width: 100%;']) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Footer con Botones de Navegación -->
                <div class="rifa-footer">
                    <?php if ($rifa->estado !== Rifas::ESTADO_SORTEADA && $rifa->estado !== Rifas::ESTADO_CANCELADA): ?>
                        <?= Html::a('<i class="fas fa-edit me-1"></i> Editar', ['rifas/update', 'id' => $rifa->id], ['class' => 'btn btn-editar']) ?>
                    <?php endif; ?>
                    <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['index'], ['class' => 'btn btn-cancelar']) ?>
                </div>
            </div>
        </div>

        <!-- Columna 2: Contenido Principal -->
        <div class="rifa-main-content">
            <!-- Fila 1: Boletos Jugados -->
            <div class="boletos-section">
                <div class="rifa-card" style="display: flex; flex-direction: column; height: 100%;">
                    <div class="card-header-custom">
                        <i class="fas fa-ticket-alt me-2"></i>Boletos Jugados
                    </div>

                    <div class="card-body-scroll">
                        <?php if (empty($boletos)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <p class="mb-0">No hay boletos registrados para esta rifa</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($boletos as $boleto): ?>
                                <div class="boleto-row">
                                    <div class="boleto-info-grid">
                                        <div class="boleto-field">
                                            <div class="boleto-label">Código</div>
                                            <div class="boleto-value"><?= Html::encode($boleto->codigo) ?></div>
                                        </div>
                                        <div class="boleto-field">
                                            <div class="boleto-label">Jugador</div>
                                            <div class="boleto-value">
                                                <?= $boleto->jugador ? Html::encode($boleto->jugador->nombre) : 'N/A' ?>
                                            </div>
                                        </div>
                                        <div class="boleto-field">
                                            <div class="boleto-label">Teléfono</div>
                                            <div class="boleto-value">
                                                <?= $boleto->jugador && $boleto->jugador->telefono ? Html::encode($boleto->jugador->telefono) : 'N/A' ?>
                                            </div>
                                        </div>
                                        <div class="boleto-field">
                                            <div class="boleto-label">Estado</div>
                                            <div class="boleto-value">
                                                <span class="estado-badge estado-<?= $boleto->estado ?>">
                                                    <?= Html::encode($boleto->displayEstado()) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?= Html::a('<i class="fas fa-eye me-1"></i> Ver', ['/panel/boletos-view', 'id' => $boleto->id], ['class' => 'btn-ver-detalle']) ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Footer con Progreso -->
                    <div class="card-footer-custom">
                        <div class="progress-info">
                            <span><strong><?= $numerosJugados ?></strong> de <strong><?= $rifa->max_numeros ?></strong>
                                números jugados <b>pagados</b></span>
                            <span><?= number_format($porcentajeProgreso, 1) ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar"
                                style="width: <?= min($porcentajeProgreso, 100) ?>%"
                                aria-valuenow="<?= $porcentajeProgreso ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= number_format($porcentajeProgreso, 1) ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fila 2: Top 3 Jugadores -->
            <div class="top-jugadores-section">
                <div class="rifa-card">
                    <div class="card-header-custom">
                        <i class="fas fa-trophy me-2"></i>Top 3 Jugadores
                    </div>

                    <div class="card-body-scroll" style="max-height: 350px;">
                        <?php if (empty($topJugadores)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <p class="mb-0">No hay jugadores registrados aún</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($topJugadores as $index => $jugador): ?>
                                <div class="jugador-row">
                                    <div class="jugador-main">
                                        <div class="jugador-rank rank-<?= $index + 1 ?>">
                                            #<?= $index + 1 ?>
                                        </div>
                                        <div class="jugador-info">
                                            <div class="jugador-nombre"><?= Html::encode($jugador->nombre) ?></div>
                                            <div class="jugador-telefono">
                                                <i class="fas fa-phone me-1"></i>
                                                <?= $jugador->telefono ? Html::encode($jugador->telefono) : 'No especificado' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="jugador-numeros">
                                        <?= $jugador->total_numeros ?> <small>números</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown Timer Script
document.addEventListener('DOMContentLoaded', function() {
    const countdownElements = document.querySelectorAll('.countdown-item-timer[data-countdown]');
    
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