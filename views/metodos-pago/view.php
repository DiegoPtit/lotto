<?php

/** @var yii\web\View $this */
/** @var app\models\MetodosPago $model */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\MetodosPago;

$tipo = $model->tipo;

$this->title = 'Detalle Método de Pago - ' . Yii::$app->name;
?>

<style>
    /* ==================== GLOBAL RESET ==================== */
    .metodo-view-page * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ==================== PAGE LAYOUT ==================== */
    .metodo-view-page {
        background: #ffffffff;
        min-height: 100vh;
        padding: 40px 0 60px;
    }

    .container-custom {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* ==================== BACK BUTTON ==================== */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #ffffff;
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.2s ease;
        margin-bottom: 30px;
    }

    .back-btn:hover {
        background: #f5f5f5;
        color: #0066cc;
        border-color: #0066cc;
    }

    /* ==================== MAIN CARD ==================== */
    .detail-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #0066cc 0%, #004999 100%);
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .header-icon i {
        font-size: 2rem;
        color: #ffffff;
    }

    .header-info {
        flex: 1;
    }

    .header-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 6px;
    }

    .header-meta {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .header-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-publica {
        background: rgba(40, 167, 69, 0.9);
        color: #ffffff;
    }

    .badge-privada {
        background: rgba(220, 53, 69, 0.9);
        color: #ffffff;
    }

    /* ==================== CARD BODY ==================== */
    .card-body-custom {
        padding: 30px;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #0066cc;
    }

    /* ==================== FORM GRID ==================== */
    .form-grid {
        display: grid;
        gap: 20px;
        margin-bottom: 30px;
    }

    .field-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        align-items: start;
        padding: 16px;
        background: #f9f9f9;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .field-row:hover {
        background: #f0f5ff;
    }

    @media (max-width: 640px) {
        .field-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .field-label-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .field-checkbox {
        width: 22px;
        height: 22px;
        cursor: not-allowed;
        accent-color: #0066cc;
    }

    .field-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #333;
    }

    .field-sublabel {
        font-size: 0.75rem;
        color: #999;
        margin-top: 2px;
    }

    .field-input {
        width: 100%;
        padding: 12px 16px;
        font-size: 0.95rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .field-input:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
    }

    .field-input:disabled {
        background: #f5f5f5;
        color: #999;
        cursor: not-allowed;
    }

    .field-hint {
        font-size: 0.75rem;
        color: #999;
        margin-top: 6px;
    }

    /* ==================== VISIBILITY TOGGLE ==================== */
    .visibility-section {
        margin-bottom: 30px;
    }

    .visibility-options {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .visibility-option {
        flex: 1;
        min-width: 200px;
    }

    .visibility-option input[type="radio"] {
        display: none;
    }

    .visibility-option label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: #f9f9f9;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .visibility-option input[type="radio"]:checked+label {
        border-color: #0066cc;
        background: rgba(0, 102, 204, 0.05);
    }

    .visibility-option label i {
        font-size: 1.25rem;
        color: #999;
        transition: color 0.2s ease;
    }

    .visibility-option input[type="radio"]:checked+label i {
        color: #0066cc;
    }

    .visibility-option .option-text {
        font-weight: 600;
        color: #333;
    }

    .visibility-option .option-desc {
        font-size: 0.75rem;
        color: #999;
    }

    /* ==================== ACTION BUTTONS ==================== */
    .action-buttons {
        display: flex;
        gap: 15px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
        flex-wrap: wrap;
    }

    .btn-primary-custom {
        flex: 1;
        min-width: 200px;
        padding: 14px 28px;
        background: #0066cc;
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-primary-custom:hover {
        background: #0052a3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
    }

    .btn-danger-custom {
        padding: 14px 28px;
        background: #ffffff;
        color: #dc3545;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid #dc3545;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-danger-custom:hover {
        background: #dc3545;
        color: #ffffff;
    }

    /* ==================== ALERTS ==================== */
    .alert-custom {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.3);
        color: #28a745;
    }

    .alert-error {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #dc3545;
    }

    .alert-custom i {
        font-size: 1.25rem;
    }

    /* ==================== NO FIELDS MESSAGE ==================== */
    .no-fields-message {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .no-fields-message i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #ddd;
    }

    .no-fields-message h4 {
        font-size: 1.125rem;
        color: #666;
        margin-bottom: 8px;
    }

    .no-fields-message p {
        font-size: 0.9rem;
    }
</style>

<div class="metodo-view-page">
    <div class="container-custom">
        <!-- Back Button -->
        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver a Métodos de Pago', ['metodos-pago/index'], ['class' => 'back-btn']) ?>

        <!-- Alerts -->
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert-custom alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= Yii::$app->session->getFlash('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert-custom alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= Yii::$app->session->getFlash('error') ?></span>
            </div>
        <?php endif; ?>

        <!-- Main Card -->
        <div class="detail-card">
            <!-- Header -->
            <div class="card-header">
                <div class="header-icon">
                    <?php
                    $icono = 'fa-credit-card';
                    if ($tipo) {
                        $descripcionLower = strtolower($tipo->descripcion);
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
                    }
                    ?>
                    <i class="fas <?= $icono ?>"></i>
                </div>
                <div class="header-info">
                    <h1 class="header-title"><?= Html::encode($tipo ? $tipo->descripcion : 'Sin tipo') ?></h1>
                    <div class="header-meta">ID: #<?= $model->id ?> • Creado:
                        <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?>
                    </div>
                </div>
                <span class="header-badge badge-<?= $model->visibilidad ?>">
                    <i class="fas fa-<?= $model->isVisibilidadPublica() ? 'eye' : 'eye-slash' ?> me-1"></i>
                    <?= $model->displayVisibilidad() ?>
                </span>
            </div>

            <!-- Body -->
            <div class="card-body-custom">
                <form method="post" action="<?= Url::to(['metodos-pago/view', 'id' => $model->id]) ?>">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">

                    <!-- Visibility Section -->
                    <div class="visibility-section">
                        <h2 class="section-title">
                            <i class="fas fa-eye"></i>
                            Visibilidad
                        </h2>
                        <div class="visibility-options">
                            <div class="visibility-option">
                                <input type="radio" name="visibilidad" id="vis-publica" value="publica"
                                    <?= $model->isVisibilidadPublica() ? 'checked' : '' ?>>
                                <label for="vis-publica">
                                    <i class="fas fa-globe"></i>
                                    <div>
                                        <div class="option-text">Pública</div>
                                        <div class="option-desc">Visible para todos los usuarios</div>
                                    </div>
                                </label>
                            </div>
                            <div class="visibility-option">
                                <input type="radio" name="visibilidad" id="vis-privada" value="privada"
                                    <?= $model->isVisibilidadPrivada() ? 'checked' : '' ?>>
                                <label for="vis-privada">
                                    <i class="fas fa-lock"></i>
                                    <div>
                                        <div class="option-text">Privada</div>
                                        <div class="option-desc">Solo visible para administradores</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Fields Section -->
                    <h2 class="section-title">
                        <i class="fas fa-edit"></i>
                        Información del Método de Pago
                    </h2>

                    <?php
                    $hasFields = $tipo && ($tipo->has_banco || $tipo->has_titular || $tipo->has_cedula || $tipo->has_telefono || $tipo->has_nro_cuenta);
                    ?>

                    <?php if ($hasFields): ?>
                        <div class="form-grid">
                            <!-- Banco -->
                            <div class="field-row">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" <?= $tipo->has_banco ? 'checked' : '' ?>
                                        disabled>
                                    <div>
                                        <div class="field-label">Banco</div>
                                        <div class="field-sublabel">Nombre de la entidad bancaria</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="banco" class="field-input"
                                        value="<?= Html::encode($model->banco) ?>" placeholder="Ej: Banesco, Mercantil..."
                                        <?= !$tipo->has_banco ? 'disabled' : '' ?>>
                                    <?php if (!$tipo->has_banco): ?>
                                        <div class="field-hint">Este campo no está habilitado para este tipo de pago</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Titular -->
                            <div class="field-row">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" <?= $tipo->has_titular ? 'checked' : '' ?>
                                        disabled>
                                    <div>
                                        <div class="field-label">Titular</div>
                                        <div class="field-sublabel">Nombre del titular de la cuenta</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="titular" class="field-input"
                                        value="<?= Html::encode($model->titular) ?>"
                                        placeholder="Nombre completo del titular" <?= !$tipo->has_titular ? 'disabled' : '' ?>>
                                    <?php if (!$tipo->has_titular): ?>
                                        <div class="field-hint">Este campo no está habilitado para este tipo de pago</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Cédula -->
                            <div class="field-row">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" <?= $tipo->has_cedula ? 'checked' : '' ?>
                                        disabled>
                                    <div>
                                        <div class="field-label">Cédula</div>
                                        <div class="field-sublabel">Documento de identidad</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="cedula" class="field-input"
                                        value="<?= Html::encode($model->cedula) ?>" placeholder="Ej: V-12345678"
                                        <?= !$tipo->has_cedula ? 'disabled' : '' ?>>
                                    <?php if (!$tipo->has_cedula): ?>
                                        <div class="field-hint">Este campo no está habilitado para este tipo de pago</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="field-row">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" <?= $tipo->has_telefono ? 'checked' : '' ?>
                                        disabled>
                                    <div>
                                        <div class="field-label">Teléfono</div>
                                        <div class="field-sublabel">Número de contacto</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="telefono" class="field-input"
                                        value="<?= Html::encode($model->telefono) ?>" placeholder="Ej: 0412-1234567"
                                        <?= !$tipo->has_telefono ? 'disabled' : '' ?>>
                                    <?php if (!$tipo->has_telefono): ?>
                                        <div class="field-hint">Este campo no está habilitado para este tipo de pago</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Número de Cuenta -->
                            <div class="field-row">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" <?= $tipo->has_nro_cuenta ? 'checked' : '' ?> disabled>
                                    <div>
                                        <div class="field-label">Nro. Cuenta</div>
                                        <div class="field-sublabel">Número de cuenta bancaria</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="nro_cuenta" class="field-input"
                                        value="<?= Html::encode($model->nro_cuenta) ?>"
                                        placeholder="Ej: 0134-1234-12-1234567890" <?= !$tipo->has_nro_cuenta ? 'disabled' : '' ?>>
                                    <?php if (!$tipo->has_nro_cuenta): ?>
                                        <div class="field-hint">Este campo no está habilitado para este tipo de pago</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-fields-message">
                            <i class="fas fa-info-circle"></i>
                            <h4>Sin campos configurados</h4>
                            <p>Este tipo de método de pago no tiene campos de información habilitados.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                        <?= Html::a('<i class="fas fa-trash"></i> Eliminar', ['metodos-pago/delete', 'id' => $model->id], [
                            'class' => 'btn-danger-custom',
                            'data' => [
                                'confirm' => '¿Estás seguro de que deseas eliminar este método de pago?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>