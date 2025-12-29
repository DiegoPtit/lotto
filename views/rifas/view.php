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

    .estado-ganador {
        background: #ffeaa7;
        color: #6c5ce7;
        border: 1px solid #fdcb6e;
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

    .btn-sortear:hover:not(:disabled) {
        background: #218838;
        border-color: #218838;
        color: white;
    }

    .btn-sortear:disabled {
        background: #95a5a6;
        border-color: #95a5a6;
        color: white;
        cursor: not-allowed;
        opacity: 0.7;
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
                        <img src="<?= Yii::$app->request->baseUrl . Html::encode($rifa->img) ?>"
                            alt="<?= Html::encode($rifa->titulo) ?>">
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
                            <!-- Botón Establecer Número Ganador (abre modal) -->
                            <?php $sorteoDisponible = ($segundosSorteo !== null && $segundosSorteo <= 0) || $segundosSorteo === null; ?>
                            <button type="button" id="btn-sortear" class="btn btn-action btn-sortear" style="width: 100%;"
                                data-segundos-sorteo="<?= $segundosSorteo ?? 0 ?>" <?= !$sorteoDisponible ? 'disabled title="¡No disponible hasta el momento del sorteo!"' : '' ?>
                                onclick="<?= $sorteoDisponible ? 'openWinnerModal()' : '' ?>">
                                <i class="fas fa-trophy me-1"></i> Establecer número ganador
                            </button>
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
                <div class="rifa-card" id="boletos-card"
                    style="display: flex; flex-direction: column; min-height: 400px;">
                    <div class="card-header-custom">
                        <i class="fas fa-ticket-alt me-2"></i>Boletos Jugados
                    </div>

                    <div class="card-body-scroll" id="boletos-list" style="flex: 1; overflow-y: auto;">
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
                    <div class="card-footer-custom" style="margin-top: auto;">
                        <div class="progress-info">
                            <span><strong id="numeros-jugados"><?= $numerosJugados ?></strong> de
                                <strong><?= $rifa->max_numeros ?></strong>
                                números jugados <b>pagados</b></span>
                            <span id="porcentaje-progreso"><?= number_format($porcentajeProgreso, 1) ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="progress-bar" role="progressbar"
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
    document.addEventListener('DOMContentLoaded', function () {
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
            countdownElements.forEach(function (el) {
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
        countdownElements.forEach(function (el) {
            const seconds = parseInt(el.getAttribute('data-countdown'), 10);
            el.textContent = formatTime(seconds);
        });

        // Actualizar cada segundo
        setInterval(updateCountdowns, 1000);
    });
</script>

<?php
// Obtener premios para el selector en el modal
$premios = \app\models\Premios::find()
    ->where(['id_rifa' => $rifa->id])
    ->orderBy(['orden' => SORT_ASC])
    ->all();
?>

<!-- Modal para Establecer Ganador -->
<style>
    .winner-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .winner-modal-overlay.show {
        display: flex;
    }

    .winner-modal {
        background: #ffffff;
        border-radius: 12px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    .winner-modal-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .winner-modal-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .winner-modal-close {
        color: white;
        font-size: 24px;
        cursor: pointer;
        line-height: 1;
    }

    .winner-modal-body {
        padding: 25px;
    }

    .search-group {
        margin-bottom: 20px;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 14px 20px 14px 50px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1.1rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #f39c12;
        box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.15);
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
        font-size: 1.1rem;
    }

    .search-results {
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-top: 10px;
        display: none;
    }

    .search-results.has-results {
        display: block;
    }

    .result-item {
        padding: 12px 15px;
        border-bottom: 1px solid #e8e8e8;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .result-item:last-child {
        border-bottom: none;
    }

    .result-item:hover,
    .result-item.selected {
        background: #fff8e1;
    }

    .result-item.selected {
        border-left: 4px solid #f39c12;
    }

    .result-numero {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f39c12;
    }

    .result-info {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-top: 3px;
    }

    .no-results {
        padding: 20px;
        text-align: center;
        color: #7f8c8d;
    }

    .premio-select-group {
        margin-top: 20px;
    }

    .premio-label {
        display: block;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 8px;
    }

    .premio-select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        background: #ffffff;
    }

    .selected-winner-preview {
        display: none;
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        text-align: center;
    }

    .selected-winner-preview.show {
        display: block;
    }

    .winner-modal-footer {
        padding: 15px 25px;
        background: #f8f9fa;
        border-top: 1px solid #e8e8e8;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-modal-cancel {
        padding: 10px 20px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: white;
        color: #7f8c8d;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-modal-cancel:hover {
        background: #f8f9fa;
    }

    .btn-modal-confirm {
        padding: 10px 25px;
        border-radius: 6px;
        border: none;
        background: #f39c12;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-modal-confirm:hover {
        background: #e67e22;
    }

    .btn-modal-confirm:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>

<div id="winnerModal" class="winner-modal-overlay">
    <div class="winner-modal">
        <div class="winner-modal-header">
            <h3 class="winner-modal-title">
                <i class="fas fa-trophy me-2"></i>
                Establecer Número Ganador
            </h3>
            <span class="winner-modal-close" onclick="closeWinnerModal()">&times;</span>
        </div>

        <?= Html::beginForm(['rifas/set-ganador', 'id' => $rifa->id], 'post') ?>
        <div class="winner-modal-body">
            <div class="search-group">
                <label class="premio-label">
                    <i class="fas fa-search me-1"></i>
                    Buscar por número jugado
                </label>
                <div class="search-input-wrapper">
                    <i class="fas fa-hashtag search-icon"></i>
                    <input type="text" id="numeroSearch" class="search-input"
                        placeholder="Escriba el número a buscar..." autocomplete="off">
                </div>

                <div id="searchResults" class="search-results">
                    <div class="no-results">
                        <i class="fas fa-search me-2"></i>
                        Ingrese un número para buscar
                    </div>
                </div>
            </div>

            <input type="hidden" name="boleto_numero_id" id="selectedNumeroId">

            <div id="selectedPreview" class="selected-winner-preview">
                <i class="fas fa-check-circle me-2"></i>
                <span id="selectedNumeroText">Número seleccionado</span>
            </div>


        </div>

        <div class="winner-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeWinnerModal()">
                Cancelar
            </button>
            <button type="submit" class="btn-modal-confirm" id="confirmBtn" disabled>
                <i class="fas fa-trophy me-1"></i>
                Confirmar Ganador
            </button>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<script>
    const searchUrl = '<?= \yii\helpers\Url::to(['rifas/buscar-numero', 'id' => $rifa->id]) ?>';
    let searchTimeout = null;

    function openWinnerModal() {
        document.getElementById('winnerModal').classList.add('show');
        document.getElementById('numeroSearch').focus();
    }

    function closeWinnerModal() {
        document.getElementById('winnerModal').classList.remove('show');
        document.getElementById('numeroSearch').value = '';
        document.getElementById('searchResults').classList.remove('has-results');
        document.getElementById('selectedNumeroId').value = '';
        document.getElementById('selectedPreview').classList.remove('show');
        document.getElementById('confirmBtn').disabled = true;
    }

    function selectNumero(id, numero, jugador) {
        document.getElementById('selectedNumeroId').value = id;
        document.getElementById('selectedNumeroText').innerHTML =
            '<strong>Número: ' + numero + '</strong><br><small>Jugador: ' + jugador + '</small>';
        document.getElementById('selectedPreview').classList.add('show');
        document.getElementById('confirmBtn').disabled = false;

        // Marcar como seleccionado
        document.querySelectorAll('.result-item').forEach(el => el.classList.remove('selected'));
        document.querySelector('.result-item[data-id="' + id + '"]')?.classList.add('selected');
    }

    document.getElementById('numeroSearch').addEventListener('input', function () {
        const query = this.value.trim();

        if (searchTimeout) clearTimeout(searchTimeout);

        if (query.length === 0) {
            document.getElementById('searchResults').innerHTML =
                '<div class="no-results"><i class="fas fa-search me-2"></i>Ingrese un número para buscar</div>';
            document.getElementById('searchResults').classList.add('has-results');
            return;
        }

        searchTimeout = setTimeout(function () {
            fetch(searchUrl + '&numero=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    const resultsDiv = document.getElementById('searchResults');

                    if (data.success && data.resultados && data.resultados.length > 0) {
                        let html = '';
                        data.resultados.forEach(function (r) {
                            html += '<div class="result-item" data-id="' + r.id + '" ' +
                                'onclick="selectNumero(\'' + r.id + '\', \'' + r.numero + '\', \'' + r.jugador_nombre + '\')">' +
                                '<div class="result-numero">#' + r.numero + '</div>' +
                                '<div class="result-info">' +
                                r.jugador_nombre + ' | Boleto: ' + r.boleto_codigo +
                                '</div></div>';
                        });
                        resultsDiv.innerHTML = html;
                    } else {
                        resultsDiv.innerHTML =
                            '<div class="no-results"><i class="fas fa-exclamation-circle me-2"></i>No se encontraron números</div>';
                    }
                    resultsDiv.classList.add('has-results');
                })
                .catch(err => {
                    console.error('Error:', err);
                });
        }, 300);
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById('winnerModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeWinnerModal();
        }
    });

    // ==================== AUTO-ENABLE WINNER BUTTON ====================
    (function () {
        const btnSortear = document.getElementById('btn-sortear');
        if (!btnSortear) return;

        let segundosSorteo = parseInt(btnSortear.getAttribute('data-segundos-sorteo')) || 0;

        if (segundosSorteo > 0) {
            const interval = setInterval(function () {
                segundosSorteo--;
                if (segundosSorteo <= 0) {
                    clearInterval(interval);
                    btnSortear.disabled = false;
                    btnSortear.removeAttribute('title');
                    btnSortear.setAttribute('onclick', 'openWinnerModal()');
                }
            }, 1000);
        }
    })();

    // ==================== REAL-TIME BOLETOS UPDATE ====================
    const rifaId = <?= $rifa->id ?>;
    const maxNumeros = <?= $rifa->max_numeros ?>;
    const apiBoletosUrl = '<?= \yii\helpers\Url::to(['panel/api-boletos-rifa', 'id' => $rifa->id]) ?>';

    function refreshBoletosData() {
        fetch(apiBoletosUrl)
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;

                // Update boletos list
                const boletosList = document.getElementById('boletos-list');
                if (boletosList && data.boletosHtml) {
                    boletosList.innerHTML = data.boletosHtml;
                }

                // Update progress
                const numerosJugados = document.getElementById('numeros-jugados');
                const porcentajeProgreso = document.getElementById('porcentaje-progreso');
                const progressBar = document.getElementById('progress-bar');

                if (numerosJugados && data.numerosJugados !== undefined) {
                    numerosJugados.textContent = data.numerosJugados;
                }

                if (porcentajeProgreso && data.porcentaje !== undefined) {
                    porcentajeProgreso.textContent = data.porcentaje.toFixed(1) + '%';
                }

                if (progressBar && data.porcentaje !== undefined) {
                    progressBar.style.width = Math.min(data.porcentaje, 100) + '%';
                    progressBar.textContent = data.porcentaje.toFixed(1) + '%';
                    progressBar.setAttribute('aria-valuenow', data.porcentaje);
                }
            })
            .catch(err => console.error('Error refreshing boletos:', err));
    }

    // Refresh every 10 seconds
    setInterval(refreshBoletosData, 10000);
</script>