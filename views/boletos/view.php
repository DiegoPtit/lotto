<?php

/** @var yii\web\View $this */
/** @var app\models\Boletos $boleto */
/** @var app\models\Jugadores $jugador */
/** @var app\models\Rifas $rifa */
/** @var app\models\BoletoNumeros[] $boletoNumeros */
/** @var app\models\Pagos[] $pagos */

use yii\bootstrap5\Html;

$this->title = 'Detalles del Boleto: ' . Html::encode($boleto->codigo);
?>

<style>
    .boleto-view-container {
        min-height: 100vh;
        padding: 1rem 0;
    }

    .boleto-grid {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 1.5rem;
    }

    .boleto-sidebar {
        position: sticky;
        top: 1rem;
        height: fit-content;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .boleto-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e8e8e8;
        overflow: hidden;
    }

    .card-header-custom {
        background: #f8f9fa;
        border-bottom: 2px solid #e8e8e8;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        font-weight: 600;
        color: #34495e;
    }

    .card-body-custom {
        padding: 1.25rem 1.5rem;
    }

    .info-row {
        margin-bottom: 1rem;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-size: 0.75rem;
        color: #95a5a6;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 0.9rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .estado-dropdown {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        font-size: 0.875rem;
        color: #2c3e50;
        background: white;
    }

    /* ==================== BADGE ESTADO HEADER ==================== */
    .card-header-custom {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header-title {
        display: flex;
        align-items: center;
    }

    .boleto-estado-badge {
        padding: 0.3rem 0.75rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-reservado {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .badge-pagado {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .badge-anulado {
        background: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    .badge-reembolsado {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .badge-ganador {
        background: linear-gradient(135deg, #ffd700 0%, #ffb800 100%);
        color: #5a4a00;
        border: 1px solid #e6c200;
    }

    /* ==================== BOTONES DE ACCIÓN ESTADO ==================== */
    .estado-actions-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .estado-action-btn {
        padding: 0.625rem 0.5rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.8rem;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    .estado-action-btn i {
        font-size: 0.75rem;
    }

    .estado-action-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .estado-action-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Botón Confirmar Pago */
    .btn-confirmar-pago {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }

    .btn-confirmar-pago:hover:not(:disabled) {
        background: #219a52;
        border-color: #219a52;
    }

    /* Botón Reembolsar */
    .btn-reembolsar {
        background: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }

    .btn-reembolsar:hover:not(:disabled) {
        background: #138496;
        border-color: #138496;
    }

    /* Botón Anular */
    .btn-anular {
        background: #6c757d;
        color: white;
        border-color: #6c757d;
    }

    .btn-anular:hover:not(:disabled) {
        background: #5a6268;
        border-color: #5a6268;
    }

    /* Botón Ganador */
    .btn-ganador {
        background: linear-gradient(135deg, #ffd700 0%, #ffb800 100%);
        color: #5a4a00;
        border-color: #e6c200;
    }

    .btn-ganador:hover:not(:disabled) {
        background: linear-gradient(135deg, #ffb800 0%, #ff9500 100%);
        border-color: #cc9e00;
    }

    /* Responsivo para botones */
    @media (max-width: 1200px) {
        .estado-actions-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .estado-actions-container {
            grid-template-columns: 1fr;
        }

        .estado-action-btn {
            font-size: 0.85rem;
            padding: 0.75rem;
        }
    }

    /* ==================== BADGE ESTADO PAGO (SOBRIO) ==================== */
    .pago-estado-badge-sobrio {
        padding: 0.3rem 0.65rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .pago-badge-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .pago-badge-confirmed {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .pago-badge-failed {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .pago-badge-refunded {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .rifa-info-section {
        padding: 1rem;
    }

    .rifa-image-container {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .rifa-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rifa-image-fallback {
        font-size: 3rem;
        color: #bdc3c7;
    }

    .rifa-titulo {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .rifa-descripcion {
        font-size: 0.85rem;
        color: #7f8c8d;
        line-height: 1.4;
        margin-bottom: 0.75rem;
    }

    .rifa-precio {
        background: #d4a017;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .card-footer-custom {
        display: flex;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e8e8e8;
    }

    .card-footer-custom .btn {
        flex: 1;
        border-radius: 6px;
        padding: 0.625rem;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .btn-editar {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .btn-editar:hover {
        background: #2980b9;
        color: white;
    }

    .btn-eliminar {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
    }

    .btn-eliminar:hover {
        background: #c0392b;
        color: white;
    }

    .btn-volver {
        background: #95a5a6;
        color: white;
        border-color: #95a5a6;
    }

    .btn-volver:hover {
        background: #7f8c8d;
        border-color: #7f8c8d;
        color: white;
    }

    .boleto-main-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .numero-chip {
        display: inline-block;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        margin: 0.25rem;
    }

    .pago-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0;
        margin-bottom: 0.75rem;
        border-left: 3px solid #3498db;
        overflow: hidden;
    }

    .pago-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .pago-header:hover {
        background: #e9ecef;
    }

    .pago-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        flex: 1;
    }

    .pago-transaction {
        font-weight: 600;
        color: #2c3e50;
    }

    .pago-monto {
        color: #27ae60;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .pago-estado-dropdown {
        padding: 0.4rem 0.75rem;
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        font-size: 0.85rem;
        background: white;
    }

    .pago-estado-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .pago-estado-badge.confirmed {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .pago-estado-badge.failed {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .pago-estado-badge.refunded {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .pago-toggle-icon {
        font-size: 0.9rem;
        color: #7f8c8d;
        transition: transform 0.3s ease;
    }

    .pago-toggle-icon.active {
        transform: rotate(180deg);
    }

    .pago-details {
        display: none;
        padding: 1rem;
        background: white;
        border-top: 1px solid #e8e8e8;
    }

    .pago-details.active {
        display: block;
    }

    .pago-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .pago-detail-column {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .pago-detail-item {
        display: flex;
        flex-direction: column;
    }

    .pago-detail-label {
        font-size: 0.7rem;
        color: #95a5a6;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .pago-detail-value {
        font-size: 0.9rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .pago-comprobante {
        max-width: 300px;
        margin-top: 0.75rem;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e8e8e8;
    }

    .pago-comprobante img {
        width: 100%;
        height: auto;
    }

    .btn-descargar-comprobante {
        margin-top: 0.75rem;
        width: 100%;
        max-width: 300px;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 0.625rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.2);
    }

    .btn-descargar-comprobante:hover {
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        transform: translateY(-1px);
    }

    .btn-descargar-comprobante:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(52, 152, 219, 0.2);
    }

    .pago-comprobante-fallback {
        max-width: 300px;
        margin-top: 0.75rem;
        padding: 2rem 1rem;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        text-align: center;
        color: #95a5a6;
    }

    .pago-comprobante-fallback i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #bdc3c7;
    }

    .pago-comprobante-fallback p {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .pago-comprobante-error {
        max-width: 300px;
        margin-top: 0.75rem;
        padding: 2rem 1rem;
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 8px;
        text-align: center;
        color: #856404;
    }

    .pago-comprobante-error i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #ffc107;
    }

    .pago-comprobante-error p {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 500;
    }


    @media (max-width: 768px) {
        .pago-details-grid {
            grid-template-columns: 1fr;
        }
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

    @media (max-width: 992px) {
        .boleto-grid {
            grid-template-columns: 1fr;
        }

        .boleto-sidebar {
            position: relative;
            top: 0;
        }
    }
</style>

<div class="boleto-view-container">
    <div class="boleto-grid">
        <!-- Columna 1: Sidebar -->
        <div class="boleto-sidebar">
            <div class="boleto-card">
                <div class="card-header-custom">
                    <span class="card-header-title">
                        <i class="fas fa-user me-2"></i>Datos del Jugador
                    </span>
                </div>
                <div class="card-body-custom">
                    <div class="info-row">
                        <div class="info-label">Cédula</div>
                        <div class="info-value"><?= Html::encode($jugador->cedula ?: 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nombre</div>
                        <div class="info-value"><?= Html::encode($jugador->nombre) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">País</div>
                        <div class="info-value"><?= Html::encode($jugador->pais ?: 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Teléfono</div>
                        <div class="info-value"><?= Html::encode($jugador->telefono ?: 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Correo</div>
                        <div class="info-value"><?= Html::encode($jugador->correo ?: 'N/A') ?></div>
                    </div>
                </div>
            </div>

            <div class="boleto-card">
                <div class="card-footer-custom" style="flex-wrap: wrap;">
                    <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['/panel/index'], ['class' => 'btn btn-volver', 'style' => 'width: 100%; flex: 0 0 100%; margin-bottom: 0.5rem;']) ?>
                    <button class="btn btn-eliminar" id="boleto-eliminar-btn">
                        <i class="fas fa-trash me-1"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna 2: Contenido Principal -->
        <div class="boleto-main-content">
            <!-- Datos del Boleto -->
            <?php
            // Obtener texto de estado para el badge
            $estadoTextos = [
                'reservado' => 'Reservado',
                'pagado' => 'Pagado',
                'anulado' => 'Anulado',
                'reembolsado' => 'Reembolsado',
                'ganador' => 'Ganador'
            ];
            $estadoActual = $boleto->estado;
            $estadoTexto = $estadoTextos[$estadoActual] ?? ucfirst($estadoActual);
            $isAnulado = $estadoActual === 'anulado';
            ?>
            <div class="boleto-card">
                <div class="card-header-custom">
                    <span class="card-header-title">
                        <i class="fas fa-ticket-alt me-2"></i>Datos del Boleto
                    </span>
                    <span class="boleto-estado-badge badge-<?= $estadoActual ?>">
                        <?= $estadoTexto ?>
                    </span>
                </div>
                <div class="card-body-custom">
                    <div class="info-row">
                        <div class="info-label">Código</div>
                        <div class="info-value"><?= Html::encode($boleto->codigo) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Cantidad de Números</div>
                        <div class="info-value"><?= $boleto->cantidad_numeros ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Monto Total</div>
                        <div class="info-value">Bs. <?= Yii::$app->formatter->asDecimal($boleto->total_precio, 2) ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Acciones de Estado</div>
                        <div class="estado-actions-container" data-estado-actual="<?= $estadoActual ?>">
                            <?php if ($estadoActual === 'reservado'): ?>
                                <!-- Estado: Reservado -->
                                <button class="estado-action-btn btn-confirmar-pago" data-nuevo-estado="pagado">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Confirmar Pago</span>
                                </button>
                                <button class="estado-action-btn btn-reembolsar" data-nuevo-estado="reembolsado">
                                    <i class="fas fa-undo"></i>
                                    <span>Reembolsar</span>
                                </button>
                                <button class="estado-action-btn btn-anular" data-nuevo-estado="anulado">
                                    <i class="fas fa-ban"></i>
                                    <span>Anular</span>
                                </button>
                            <?php elseif ($estadoActual === 'pagado'): ?>
                                <!-- Estado: Pagado/Confirmado -->
                                <button class="estado-action-btn btn-ganador" data-nuevo-estado="ganador">
                                    <i class="fas fa-trophy"></i>
                                    <span>Boleto Ganador</span>
                                </button>
                                <button class="estado-action-btn btn-anular" data-nuevo-estado="anulado">
                                    <i class="fas fa-ban"></i>
                                    <span>Anular</span>
                                </button>
                                <button class="estado-action-btn btn-reembolsar" data-nuevo-estado="reembolsado">
                                    <i class="fas fa-undo"></i>
                                    <span>Reembolsar</span>
                                </button>
                            <?php elseif ($estadoActual === 'anulado'): ?>
                                <!-- Estado: Anulado - Botones deshabilitados -->
                                <button class="estado-action-btn btn-confirmar-pago" disabled>
                                    <i class="fas fa-check-circle"></i>
                                    <span>Confirmar Pago</span>
                                </button>
                                <button class="estado-action-btn btn-ganador" disabled>
                                    <i class="fas fa-trophy"></i>
                                    <span>Boleto Ganador</span>
                                </button>
                                <button class="estado-action-btn btn-reembolsar" disabled>
                                    <i class="fas fa-undo"></i>
                                    <span>Reembolsar</span>
                                </button>
                                <button class="estado-action-btn btn-anular" disabled>
                                    <i class="fas fa-ban"></i>
                                    <span>Anular</span>
                                </button>
                            <?php elseif ($estadoActual === 'reembolsado'): ?>
                                <!-- Estado: Reembolsado - solo vista -->
                                <button class="estado-action-btn btn-confirmar-pago" disabled>
                                    <i class="fas fa-check-circle"></i>
                                    <span>Confirmar Pago</span>
                                </button>
                                <button class="estado-action-btn btn-ganador" disabled>
                                    <i class="fas fa-trophy"></i>
                                    <span>Boleto Ganador</span>
                                </button>
                                <button class="estado-action-btn btn-reembolsar" disabled>
                                    <i class="fas fa-undo"></i>
                                    <span>Reembolsar</span>
                                </button>
                                <button class="estado-action-btn btn-anular" disabled>
                                    <i class="fas fa-ban"></i>
                                    <span>Anular</span>
                                </button>
                            <?php elseif ($estadoActual === 'ganador'): ?>
                                <!-- Estado: Ganador - solo vista -->
                                <button class="estado-action-btn btn-confirmar-pago" disabled>
                                    <i class="fas fa-check-circle"></i>
                                    <span>Confirmar Pago</span>
                                </button>
                                <button class="estado-action-btn btn-ganador" disabled>
                                    <i class="fas fa-trophy"></i>
                                    <span>Boleto Ganador</span>
                                </button>
                                <button class="estado-action-btn btn-reembolsar" disabled>
                                    <i class="fas fa-undo"></i>
                                    <span>Reembolsar</span>
                                </button>
                                <button class="estado-action-btn btn-anular" disabled>
                                    <i class="fas fa-ban"></i>
                                    <span>Anular</span>
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($estadoActual === 'anulado'): ?>
                            <!-- Enlace de aprobación manual (fuera de la grilla) -->
                            <div
                                style="margin-top: 20px; padding: 15px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 6px; width: 100%;">
                                <p style="margin: 0 0 10px 0; font-size: 0.9rem; color: #92400e; line-height: 1.5;">
                                    <i class="fas fa-info-circle" style="color: #f59e0b;"></i>
                                    Si el jugador se contactó y aclaró la situación, puedes aprobar manualmente su boleto.
                                </p>
                                <a href="javascript:void(0)" id="btn-abrir-modal-aprobacion"
                                    style="color: #10b981; font-weight: 600; text-decoration: none; font-size: 0.9rem; cursor: pointer;">
                                    <i class="fas fa-user-check me-1"></i>
                                    Aprobar Boleto Manualmente
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Rifa Relacionada -->
            <div class="boleto-card">
                <div class="card-header-custom">
                    <span class="card-header-title">
                        <i class="fas fa-trophy me-2"></i>Rifa Relacionada
                    </span>
                </div>
                <div class="rifa-image-container">
                    <?php if (!empty($rifa->img)): ?>
                        <?= Html::img(Yii::getAlias('@web') . $rifa->img, ['alt' => $rifa->titulo]) ?>
                    <?php else: ?>
                        <i class="fas fa-ticket-alt rifa-image-fallback"></i>
                    <?php endif; ?>
                </div>
                <div class="rifa-info-section">
                    <div class="rifa-titulo"><?= Html::encode($rifa->titulo) ?></div>
                    <div class="rifa-descripcion"><?= Html::encode($rifa->descripcion ?: 'Sin descripción') ?></div>
                    <div class="rifa-precio">Bs. <?= Yii::$app->formatter->asDecimal($rifa->precio_boleto, 2) ?></div>
                    <div class="mt-3">
                        <?= Html::a('<i class="fas fa-eye me-1"></i> Ver detalles de rifa', ['/panel/rifas-view', 'id' => $rifa->id], ['class' => 'btn btn-outline-primary w-100']) ?>
                    </div>
                </div>
            </div>

            <!-- Números Jugados -->
            <div class="boleto-card">
                <div class="card-header-custom">
                    <span class="card-header-title">
                        <i class="fas fa-hashtag me-2"></i>Números Jugados
                    </span>
                </div>
                <div class="card-body-custom">
                    <?php if (empty($boletoNumeros)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                            <p class="mb-0">No hay números asignados</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($boletoNumeros as $numero): ?>
                            <span class="numero-chip"><?= Html::encode($numero->numero) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagos -->
            <div class="boleto-card">
                <div class="card-header-custom">
                    <span class="card-header-title">
                        <i class="fas fa-credit-card me-2"></i>Pagos Relacionados
                    </span>
                </div>
                <div class="card-body-custom">
                    <?php if (empty($pagos)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-wallet"></i></div>
                            <p class="mb-0">No hay pagos registrados</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pagos as $pago): ?>
                            <div class="pago-card" data-pago-id="<?= $pago->id ?>">
                                <div class="pago-header" onclick="togglePagoDetails(<?= $pago->id ?>)">
                                    <div class="pago-info">
                                        <span class="pago-transaction"><i
                                                class="fas fa-hashtag me-1"></i><?= Html::encode($pago->transaction_id) ?></span>
                                        <span class="pago-monto"><?= Html::encode($pago->moneda ?: 'Bs.') ?>
                                            <?= Yii::$app->formatter->asDecimal($pago->monto, 2) ?></span>
                                        <?php
                                        // Determinar la clase y texto del badge para el pago
                                        $pagoBadgeClass = '';
                                        $pagoBadgeText = '';
                                        if ($pago->isEstadoPending()) {
                                            $pagoBadgeClass = 'pending';
                                            $pagoBadgeText = 'Pendiente';
                                        } elseif ($pago->isEstadoConfirmed()) {
                                            $pagoBadgeClass = 'confirmed';
                                            $pagoBadgeText = 'Confirmado';
                                        } elseif ($pago->isEstadoFailed()) {
                                            $pagoBadgeClass = 'failed';
                                            $pagoBadgeText = 'Fallido';
                                        } elseif ($pago->isEstadoRefunded()) {
                                            $pagoBadgeClass = 'refunded';
                                            $pagoBadgeText = 'Reembolsado';
                                        }
                                        ?>
                                        <span
                                            class="pago-estado-badge-sobrio pago-badge-<?= $pagoBadgeClass ?>"><?= $pagoBadgeText ?></span>
                                    </div>
                                    <i class="fas fa-chevron-down pago-toggle-icon" id="toggle-icon-<?= $pago->id ?>"></i>
                                </div>

                                <div class="pago-details" id="pago-details-<?= $pago->id ?>">
                                    <div class="pago-details-grid">
                                        <!-- Columna 1: Detalles del Pago -->
                                        <div class="pago-detail-column">
                                            <div class="pago-detail-item">
                                                <div class="pago-detail-label">Transaction ID</div>
                                                <div class="pago-detail-value">
                                                    <?= Html::encode($pago->transaction_id ?: 'N/A') ?>
                                                </div>
                                            </div>
                                            <div class="pago-detail-item">
                                                <div class="pago-detail-label">Nombre del Jugador</div>
                                                <div class="pago-detail-value">
                                                    <?= Html::encode($pago->jugador ? $pago->jugador->nombre : 'N/A') ?>
                                                </div>
                                            </div>
                                            <div class="pago-detail-item">
                                                <div class="pago-detail-label">Monto Total</div>
                                                <div class="pago-detail-value">
                                                    <span style="color: #27ae60; font-weight: 700;">
                                                        <?= Html::encode($pago->moneda ?: 'Bs.') ?>
                                                        <?= Yii::$app->formatter->asDecimal($pago->monto, 2) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="pago-detail-item">
                                                <div class="pago-detail-label">Método de Pago</div>
                                                <div class="pago-detail-value">
                                                    <?php if ($pago->metodoPago && $pago->metodoPago->tipo): ?>
                                                        <?php
                                                        $metodoPagoText = Html::encode($pago->metodoPago->tipo->descripcion);
                                                        if (!empty($pago->metodoPago->banco)) {
                                                            $metodoPagoText .= ' - ' . Html::encode($pago->metodoPago->banco);
                                                        }
                                                        ?>
                                                        <span style="color: #3498db; font-weight: 600;">
                                                            <i class="fas fa-credit-card me-1"></i><?= $metodoPagoText ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: #95a5a6;">No especificado</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Columna 2: Nota del Pago -->
                                        <div class="pago-detail-column">
                                            <div class="pago-detail-item">
                                                <div class="pago-detail-label">Nota del Pago</div>
                                                <div class="pago-detail-value" style="white-space: pre-line; line-height: 1.6;">
                                                    <?= trim(Html::encode($pago->notas ?: 'Sin notas')) ?>
                                                </div>
                                            </div>

                                            <div class="pago-detail-item">
                                                <div class="pago-detail-label">Comprobante</div>
                                                <?php if (!empty($pago->comprobante_url)): ?>
                                                    <div class="pago-comprobante">
                                                        <?= Html::img(Yii::getAlias('@web') . $pago->comprobante_url, [
                                                            'alt' => 'Comprobante',
                                                            'onerror' => "this.parentElement.innerHTML='<div class=\'pago-comprobante-error\'><i class=\'fas fa-exclamation-triangle\'></i><p>Error al cargar la imagen</p></div>';",
                                                        ]) ?>
                                                    </div>
                                                    <button class="btn-descargar-comprobante"
                                                        onclick="descargarComprobante('<?= Yii::$app->request->baseUrl . Html::encode($pago->comprobante_url) ?>', 'comprobante-pago-<?= $pago->id ?>')">
                                                        <i class="fas fa-download me-2"></i>Descargar Comprobante de Pago
                                                    </button>
                                                <?php else: ?>
                                                    <div class="pago-comprobante-fallback">
                                                        <i class="fas fa-file-image"></i>
                                                        <p>No se ha cargado comprobante</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">¿Está seguro?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmModalBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        var boletoId = <?= $boleto->id ?>;
        var confirmModalEl = document.getElementById('confirmModal');
        var confirmModal = null;
        var pendingAction = null;

        // Inicializar modal - Bootstrap 5 native
        try {
            confirmModal = new bootstrap.Modal(confirmModalEl);
        } catch (e) {
            console.log('Bootstrap modal no disponible:', e);
        }

        function showModal() {
            if (confirmModal) {
                confirmModal.show();
            } else {
                // Fallback: mostrar manualmente
                confirmModalEl.classList.add('show');
                confirmModalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }

        function hideModal() {
            if (confirmModal) {
                confirmModal.hide();
            } else {
                confirmModalEl.classList.remove('show');
                confirmModalEl.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }

        // BOTONES DE ACCIÓN DEL BOLETO
        var estadoActionButtons = document.querySelectorAll('.estado-action-btn[data-nuevo-estado]');
        estadoActionButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var nuevoEstado = this.getAttribute('data-nuevo-estado');
                var estadoTextos = {
                    'pagado': 'Confirmar Pago',
                    'reembolsado': 'Marcar como Reembolsado',
                    'anulado': 'Anular el Boleto',
                    'ganador': 'Establecer como Boleto Ganador'
                };
                var accionTexto = estadoTextos[nuevoEstado] || 'cambiar el estado';

                document.getElementById('confirmModalBody').textContent =
                    '¿Está seguro de ' + accionTexto.toLowerCase() + '?';
                pendingAction = function () {
                    cambiarEstadoBoleto(nuevoEstado);
                };
                showModal();
            });
        });

        // ELIMINAR
        document.getElementById('boleto-eliminar-btn').onclick = function () {
            document.getElementById('confirmModalBody').textContent = '¿Está seguro de eliminar este boleto?';
            pendingAction = eliminarBoleto;
            showModal();
        };

        // CONFIRMAR MODAL
        document.getElementById('confirmModalBtn').onclick = function () {
            if (pendingAction) {
                pendingAction();
                hideModal();
                pendingAction = null;
            }
        };

        // AJAX
        function cambiarEstadoBoleto(nuevoEstado) {
            fetch('<?= \yii\helpers\Url::to(['/panel/boleto-change-estado']) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: boletoId, estado: nuevoEstado })
            }).then(function (r) { return r.json(); }).then(function (data) {
                alert(data.success ? 'Actualizado' : 'Error: ' + data.message);
                if (data.success) location.reload();
            });
        }

        function cambiarEstadoPago(pagoId, nuevoEstado) {
            fetch('<?= \yii\helpers\Url::to(['/panel/pago-change-estado']) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: pagoId, estado: nuevoEstado })
            }).then(function (r) { return r.json(); }).then(function (data) {
                alert(data.success ? 'Actualizado' : 'Error: ' + data.message);
                if (data.success) location.reload();
            });
        }

        function eliminarBoleto() {
            fetch('<?= \yii\helpers\Url::to(['/panel/boleto-delete']) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: boletoId })
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data.success) {
                    alert('Eliminado');
                    window.location.href = '<?= \yii\helpers\Url::to(['/panel/index']) ?>';
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }


        console.log('Script cargado correctamente');
    });

    // Toggle de detalles de pago
    function togglePagoDetails(pagoId) {
        var detailsElement = document.getElementById('pago-details-' + pagoId);
        var iconElement = document.getElementById('toggle-icon-' + pagoId);

        if (detailsElement && iconElement) {
            detailsElement.classList.toggle('active');
            iconElement.classList.toggle('active');
        }
    }

    // Descargar comprobante de pago
    function descargarComprobante(url, filename) {
        fetch(url)
            .then(response => response.blob())
            .then(blob => {
                // Obtener la extensión del archivo desde la URL
                var extension = url.split('.').pop().split('?')[0];
                var filenameWithExt = filename + '.' + extension;

                // Crear un enlace temporal para descargar
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filenameWithExt;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(link.href);
            })
            .catch(error => {
                console.error('Error al descargar el comprobante:', error);
                alert('No se pudo descargar el comprobante. Por favor, intente nuevamente.');
            });
    }

</script>

<?php if ($estadoActual === 'anulado'): ?>
    <?= $this->render('_modal_aprobacion', ['boleto' => $boleto, 'rifa' => $rifa]) ?>
<?php endif; ?>