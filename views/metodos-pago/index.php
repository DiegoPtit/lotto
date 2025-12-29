<?php

/** @var yii\web\View $this */
/** @var app\models\MetodosPago[] $metodosPago */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\MetodosPago;

$this->title = 'Métodos de Pago - ' . Yii::$app->name;
?>

<style>
    /* ==================== GLOBAL RESET ==================== */
    .metodos-pago-page * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ==================== PAGE LAYOUT ==================== */
    .metodos-pago-page {
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

    /* ==================== METODOS GRID ==================== */
    .metodos-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        padding-top: 20px;
    }

    @media (min-width: 640px) {
        .metodos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .metodos-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* ==================== METODO CARD ==================== */
    .metodo-card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        opacity: 0;
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .metodo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #0066cc;
    }

    .metodo-card-header {
        background: linear-gradient(135deg, #0066cc 0%, #004999 100%);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .metodo-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .metodo-icon i {
        font-size: 1.5rem;
        color: #ffffff;
    }

    .metodo-header-info {
        flex: 1;
        min-width: 0;
    }

    .metodo-tipo {
        font-size: 1.125rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .metodo-id {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
    }

    /* ==================== CARD BODY ==================== */
    .metodo-card-body {
        padding: 20px;
    }

    .metodo-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .metodo-info-row:last-child {
        border-bottom: none;
    }

    .metodo-info-label {
        font-size: 0.875rem;
        color: #666;
        font-weight: 500;
    }

    .metodo-info-value {
        font-size: 0.875rem;
        color: #1a1a1a;
        font-weight: 600;
        text-align: right;
        max-width: 60%;
        word-break: break-word;
    }

    /* ==================== BADGES ==================== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-publica {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .badge-privada {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .badge-publica i,
    .badge-privada i {
        font-size: 0.7rem;
    }

    /* ==================== CARD FOOTER ==================== */
    .metodo-card-footer {
        padding: 15px 20px;
        background: #f9f9f9;
        border-top: 1px solid #e0e0e0;
    }

    .btn-ver-metodo {
        display: block;
        width: 100%;
        padding: 12px 20px;
        background: #0066cc;
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .btn-ver-metodo:hover {
        background: #0052a3;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-ver-metodo i {
        margin-right: 8px;
    }

    /* ==================== CREAR CARD ==================== */
    .crear-metodo-card {
        background: #ffffff;
        border: 2px dashed #0066cc;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        opacity: 0;
        animation: fadeInUp 0.6s ease-out forwards;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 280px;
        text-decoration: none;
    }

    .crear-metodo-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(0, 102, 204, 0.2);
        border-color: #0052a3;
        background: rgba(0, 102, 204, 0.03);
    }

    .crear-metodo-content {
        text-align: center;
        padding: 30px;
    }

    .crear-metodo-icon {
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

    .crear-metodo-card:hover .crear-metodo-icon {
        background: #0066cc;
        transform: scale(1.1);
    }

    .crear-metodo-icon i {
        font-size: 2.5rem;
        color: #0066cc;
        transition: all 0.3s ease;
    }

    .crear-metodo-card:hover .crear-metodo-icon i {
        color: #ffffff;
    }

    .crear-metodo-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0066cc;
        margin: 0;
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

    .btn-crear-empty {
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

    .btn-crear-empty:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.5);
        color: #ffffff;
    }

    /* ==================== CAMPOS DISPONIBLES ==================== */
    .campos-disponibles {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
    }

    .campo-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: rgba(0, 102, 204, 0.08);
        color: #0066cc;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .campo-tag i {
        font-size: 0.65rem;
    }

    /* ==================== HAS FIELDS INDICATORS ==================== */
    .has-fields-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .field-indicator {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
        background: #f5f5f5;
        color: #666;
    }

    .field-indicator.active {
        background: rgba(0, 102, 204, 0.1);
        color: #0066cc;
    }
</style>

<div class="metodos-pago-page">
    <div class="container-custom">
        <!-- Header -->
        <div class="section-header">
            <h1 class="section-title">Métodos de Pago</h1>
            <p class="section-description">Administra los métodos de pago disponibles para tus rifas</p>
        </div>

        <?php if (empty($metodosPago)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-credit-card"></i>
                <h3>No hay métodos de pago</h3>
                <p>Comienza creando tu primer método de pago</p>
                <?= Html::a('<i class="fas fa-plus me-2"></i>Crear Método de Pago', ['metodos-pago/create'], ['class' => 'btn-crear-empty']) ?>
            </div>
        <?php else: ?>
            <!-- Grid de Métodos -->
            <div class="metodos-grid">
                <!-- Crear Nuevo Método Card -->
                <a href="<?= Url::to(['metodos-pago/create']) ?>" class="crear-metodo-card">
                    <div class="crear-metodo-content">
                        <div class="crear-metodo-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3 class="crear-metodo-title">Nuevo Método de Pago</h3>
                    </div>
                </a>

                <?php
                $delay = 0.1;
                foreach ($metodosPago as $metodo):
                    $tipo = $metodo->tipo;

                    // Determinar ícono según el tipo
                    $icono = 'fa-credit-card';
                    $descripcionLower = strtolower($tipo->descripcion ?? '');
                    if (strpos($descripcionLower, 'transfer') !== false || strpos($descripcionLower, 'banco') !== false) {
                        $icono = 'fa-university';
                    } elseif (strpos($descripcionLower, 'pago móvil') !== false || strpos($descripcionLower, 'movil') !== false) {
                        $icono = 'fa-mobile-alt';
                    } elseif (strpos($descripcionLower, 'paypal') !== false) {
                        $icono = 'fa-paypal';
                    } elseif (strpos($descripcionLower, 'binance') !== false || strpos($descripcionLower, 'crypto') !== false) {
                        $icono = 'fa-bitcoin';
                    } elseif (strpos($descripcionLower, 'efectivo') !== false) {
                        $icono = 'fa-money-bill-wave';
                    } elseif (strpos($descripcionLower, 'zelle') !== false) {
                        $icono = 'fa-dollar-sign';
                    }
                    ?>
                    <div class="metodo-card" style="animation-delay: <?= $delay ?>s">
                        <!-- Header -->
                        <div class="metodo-card-header">
                            <div class="metodo-icon">
                                <i class="fas <?= $icono ?>"></i>
                            </div>
                            <div class="metodo-header-info">
                                <div class="metodo-tipo"><?= Html::encode($tipo ? $tipo->descripcion : 'Sin tipo') ?></div>
                                <div class="metodo-id">ID: #<?= $metodo->id ?></div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="metodo-card-body">
                            <!-- Visibilidad -->
                            <div class="metodo-info-row">
                                <span class="metodo-info-label">Estado</span>
                                <span class="status-badge badge-<?= $metodo->visibilidad ?>">
                                    <i class="fas fa-<?= $metodo->isVisibilidadPublica() ? 'eye' : 'eye-slash' ?>"></i>
                                    <?= $metodo->displayVisibilidad() ?>
                                </span>
                            </div>

                            <!-- Mostrar campos con valor -->
                            <?php if ($tipo && $tipo->has_banco && $metodo->banco): ?>
                                <div class="metodo-info-row">
                                    <span class="metodo-info-label">Banco</span>
                                    <span class="metodo-info-value"><?= Html::encode($metodo->banco) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($tipo && $tipo->has_titular && $metodo->titular): ?>
                                <div class="metodo-info-row">
                                    <span class="metodo-info-label">Titular</span>
                                    <span class="metodo-info-value"><?= Html::encode($metodo->titular) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($tipo && $tipo->has_telefono && $metodo->telefono): ?>
                                <div class="metodo-info-row">
                                    <span class="metodo-info-label">Teléfono</span>
                                    <span class="metodo-info-value"><?= Html::encode($metodo->telefono) ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Campos disponibles en el tipo -->
                            <?php if ($tipo): ?>
                                <div class="campos-disponibles">
                                    <?php if ($tipo->has_banco): ?>
                                        <span class="campo-tag"><i class="fas fa-check"></i> Banco</span>
                                    <?php endif; ?>
                                    <?php if ($tipo->has_titular): ?>
                                        <span class="campo-tag"><i class="fas fa-check"></i> Titular</span>
                                    <?php endif; ?>
                                    <?php if ($tipo->has_cedula): ?>
                                        <span class="campo-tag"><i class="fas fa-check"></i> Cédula</span>
                                    <?php endif; ?>
                                    <?php if ($tipo->has_telefono): ?>
                                        <span class="campo-tag"><i class="fas fa-check"></i> Teléfono</span>
                                    <?php endif; ?>
                                    <?php if ($tipo->has_nro_cuenta): ?>
                                        <span class="campo-tag"><i class="fas fa-check"></i> Nro. Cuenta</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div class="metodo-card-footer">
                            <?= Html::a('<i class="fas fa-eye"></i>Ver Detalles', ['metodos-pago/view', 'id' => $metodo->id], ['class' => 'btn-ver-metodo']) ?>
                        </div>
                    </div>
                    <?php
                    $delay += 0.1;
                endforeach;
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>