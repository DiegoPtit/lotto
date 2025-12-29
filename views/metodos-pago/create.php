<?php

/** @var yii\web\View $this */
/** @var app\models\MetodosPago $model */
/** @var app\models\MetodosPagoTipo[] $tipos */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Crear Método de Pago - ' . Yii::$app->name;

// Preparar datos de tipos para JavaScript
$tiposData = [];
foreach ($tipos as $t) {
    $tiposData[$t->id] = [
        'descripcion' => $t->descripcion,
        'has_banco' => (bool) $t->has_banco,
        'has_titular' => (bool) $t->has_titular,
        'has_cedula' => (bool) $t->has_cedula,
        'has_telefono' => (bool) $t->has_telefono,
        'has_nro_cuenta' => (bool) $t->has_nro_cuenta,
    ];
}
?>

<style>
    /* ==================== GLOBAL RESET ==================== */
    .metodo-create-page * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ==================== PAGE LAYOUT ==================== */
    .metodo-create-page {
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
    .create-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #0066cc 0%, #004999 100%);
        padding: 30px;
        text-align: center;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }

    .header-icon i {
        font-size: 2rem;
        color: #ffffff;
    }

    .header-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 6px;
    }

    .header-subtitle {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.8);
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

    /* ==================== TYPE SELECTOR ==================== */
    .type-selector-section {
        margin-bottom: 30px;
    }

    .type-selector-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .type-select-wrapper {
        flex: 1;
        min-width: 250px;
    }

    .type-select {
        width: 100%;
        padding: 14px 18px;
        font-size: 1rem;
        font-weight: 500;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
    }

    .type-select:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
    }

    .btn-new-type {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: rgba(0, 102, 204, 0.1);
        color: #0066cc;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid #0066cc;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-new-type:hover {
        background: #0066cc;
        color: #ffffff;
    }

    /* ==================== TYPE INFO CARD ==================== */
    .type-info-card {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        display: none;
    }

    .type-info-card.visible {
        display: block;
    }

    .type-info-title {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
    }

    .type-fields-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .type-field-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .type-field-tag.active {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .type-field-tag.inactive {
        background: #f0f0f0;
        color: #999;
    }

    .type-field-tag i {
        font-size: 0.75rem;
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

    .field-row.disabled {
        opacity: 0.5;
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

    .btn-primary-custom:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-secondary-custom {
        padding: 14px 28px;
        background: #ffffff;
        color: #666;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-secondary-custom:hover {
        background: #f5f5f5;
        border-color: #0066cc;
        color: #0066cc;
    }

    /* ==================== MODAL ==================== */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 25px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-title i {
        color: #0066cc;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        border: none;
        background: #f5f5f5;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: #dc3545;
        color: #ffffff;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-field {
        margin-bottom: 20px;
    }

    .modal-field label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .modal-field input[type="text"] {
        width: 100%;
        padding: 12px 16px;
        font-size: 0.95rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .modal-field input[type="text"]:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
    }

    .modal-checkboxes {
        display: grid;
        gap: 12px;
    }

    .modal-checkbox-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #f9f9f9;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .modal-checkbox-item:hover {
        background: #f0f5ff;
    }

    .modal-checkbox-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: #0066cc;
        cursor: pointer;
    }

    .modal-checkbox-item label {
        flex: 1;
        font-size: 0.9rem;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        margin: 0;
    }

    .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-modal-cancel {
        padding: 10px 20px;
        background: #ffffff;
        color: #666;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-modal-cancel:hover {
        background: #f5f5f5;
    }

    .btn-modal-save {
        padding: 10px 20px;
        background: #0066cc;
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-modal-save:hover {
        background: #0052a3;
    }

    .btn-modal-save:disabled {
        background: #ccc;
        cursor: not-allowed;
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

    /* ==================== PLACEHOLDER SECTION ==================== */
    .placeholder-section {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .placeholder-section i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #ddd;
    }

    .placeholder-section h3 {
        font-size: 1.25rem;
        color: #666;
        margin-bottom: 8px;
    }

    .placeholder-section p {
        font-size: 0.95rem;
    }
</style>

<div class="metodo-create-page">
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
        <div class="create-card">
            <!-- Header -->
            <div class="card-header">
                <div class="header-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <h1 class="header-title">Nuevo Método de Pago</h1>
                <p class="header-subtitle">Selecciona un tipo y completa la información requerida</p>
            </div>

            <!-- Body -->
            <div class="card-body-custom">
                <form id="createForm" method="post" action="<?= Url::to(['metodos-pago/create']) ?>">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">

                    <!-- Type Selector Section -->
                    <div class="type-selector-section">
                        <h2 class="section-title">
                            <i class="fas fa-tags"></i>
                            Tipo de Método de Pago
                        </h2>
                        <div class="type-selector-header">
                            <div class="type-select-wrapper">
                                <select name="tipo_id" id="tipoSelect" class="type-select" required>
                                    <option value="">-- Selecciona un tipo --</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo->id ?>"><?= Html::encode($tipo->descripcion) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" class="btn-new-type" onclick="openCreateTipoModal()">
                                <i class="fas fa-plus-circle"></i>
                                Crear Nuevo Tipo
                            </button>
                        </div>

                        <!-- Type Info Card -->
                        <div id="typeInfoCard" class="type-info-card">
                            <div class="type-info-title">Campos habilitados para este tipo:</div>
                            <div id="typeFieldsGrid" class="type-fields-grid">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Fields Section (will be shown/hidden based on type) -->
                    <div id="fieldsSection" style="display: none;">
                        <h2 class="section-title">
                            <i class="fas fa-edit"></i>
                            Información del Método de Pago
                        </h2>

                        <div class="form-grid">
                            <!-- Banco -->
                            <div class="field-row" id="fieldBanco">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" id="checkBanco" disabled>
                                    <div>
                                        <div class="field-label">Banco</div>
                                        <div class="field-sublabel">Nombre de la entidad bancaria</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="banco" id="inputBanco" class="field-input"
                                        placeholder="Ej: Banesco, Mercantil...">
                                </div>
                            </div>

                            <!-- Titular -->
                            <div class="field-row" id="fieldTitular">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" id="checkTitular" disabled>
                                    <div>
                                        <div class="field-label">Titular</div>
                                        <div class="field-sublabel">Nombre del titular de la cuenta</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="titular" id="inputTitular" class="field-input"
                                        placeholder="Nombre completo del titular">
                                </div>
                            </div>

                            <!-- Cédula -->
                            <div class="field-row" id="fieldCedula">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" id="checkCedula" disabled>
                                    <div>
                                        <div class="field-label">Cédula</div>
                                        <div class="field-sublabel">Documento de identidad</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="cedula" id="inputCedula" class="field-input"
                                        placeholder="Ej: V-12345678">
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="field-row" id="fieldTelefono">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" id="checkTelefono" disabled>
                                    <div>
                                        <div class="field-label">Teléfono</div>
                                        <div class="field-sublabel">Número de contacto</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="telefono" id="inputTelefono" class="field-input"
                                        placeholder="Ej: 0412-1234567">
                                </div>
                            </div>

                            <!-- Número de Cuenta -->
                            <div class="field-row" id="fieldNroCuenta">
                                <div class="field-label-container">
                                    <input type="checkbox" class="field-checkbox" id="checkNroCuenta" disabled>
                                    <div>
                                        <div class="field-label">Nro. Cuenta</div>
                                        <div class="field-sublabel">Número de cuenta bancaria</div>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="nro_cuenta" id="inputNroCuenta" class="field-input"
                                        placeholder="Ej: 0134-1234-12-1234567890">
                                </div>
                            </div>
                        </div>

                        <!-- Visibility Section -->
                        <div class="visibility-section">
                            <h2 class="section-title">
                                <i class="fas fa-eye"></i>
                                Visibilidad
                            </h2>
                            <div class="visibility-options">
                                <div class="visibility-option">
                                    <input type="radio" name="visibilidad" id="vis-publica" value="publica" checked>
                                    <label for="vis-publica">
                                        <i class="fas fa-globe"></i>
                                        <div>
                                            <div class="option-text">Pública</div>
                                            <div class="option-desc">Visible para todos los usuarios</div>
                                        </div>
                                    </label>
                                </div>
                                <div class="visibility-option">
                                    <input type="radio" name="visibilidad" id="vis-privada" value="privada">
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
                    </div>

                    <!-- Placeholder when no type selected -->
                    <div id="placeholderSection" class="placeholder-section">
                        <i class="fas fa-hand-pointer"></i>
                        <h3>Selecciona un tipo de método de pago</h3>
                        <p>Los campos de información se mostrarán según el tipo seleccionado</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" id="submitBtn" class="btn-primary-custom" disabled>
                            <i class="fas fa-save"></i>
                            Crear Método de Pago
                        </button>
                        <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['metodos-pago/index'], ['class' => 'btn-secondary-custom']) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo tipo -->
<div id="createTipoModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle"></i>
                Crear Nuevo Tipo de Pago
            </h3>
            <button type="button" class="modal-close" onclick="closeCreateTipoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-field">
                <label for="tipoDescripcion">Nombre del tipo *</label>
                <input type="text" id="tipoDescripcion" placeholder="Ej: Pago Móvil, Transferencia, Zelle...">
            </div>
            <div class="modal-field">
                <label>Campos disponibles</label>
                <div class="modal-checkboxes">
                    <div class="modal-checkbox-item">
                        <input type="checkbox" id="modalHasBanco">
                        <label for="modalHasBanco">Banco</label>
                    </div>
                    <div class="modal-checkbox-item">
                        <input type="checkbox" id="modalHasTitular">
                        <label for="modalHasTitular">Titular</label>
                    </div>
                    <div class="modal-checkbox-item">
                        <input type="checkbox" id="modalHasCedula">
                        <label for="modalHasCedula">Cédula</label>
                    </div>
                    <div class="modal-checkbox-item">
                        <input type="checkbox" id="modalHasTelefono">
                        <label for="modalHasTelefono">Teléfono</label>
                    </div>
                    <div class="modal-checkbox-item">
                        <input type="checkbox" id="modalHasNroCuenta">
                        <label for="modalHasNroCuenta">Número de Cuenta</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeCreateTipoModal()">Cancelar</button>
            <button type="button" class="btn-modal-save" id="saveTipoBtn" onclick="saveNewTipo()">
                <i class="fas fa-save"></i>
                Guardar Tipo
            </button>
        </div>
    </div>
</div>

<script>
    // Datos de tipos desde PHP
    const tiposData = <?= json_encode($tiposData) ?>;
    const csrfToken = '<?= Yii::$app->request->csrfToken ?>';
    const csrfParam = '<?= Yii::$app->request->csrfParam ?>';
    const createTipoUrl = '<?= Url::to(['metodos-pago/create-tipo']) ?>';

    // Referencias a elementos
    const tipoSelect = document.getElementById('tipoSelect');
    const typeInfoCard = document.getElementById('typeInfoCard');
    const typeFieldsGrid = document.getElementById('typeFieldsGrid');
    const fieldsSection = document.getElementById('fieldsSection');
    const placeholderSection = document.getElementById('placeholderSection');
    const submitBtn = document.getElementById('submitBtn');

    // Event listener para cambio de tipo
    tipoSelect.addEventListener('change', function () {
        const tipoId = this.value;

        if (tipoId && tiposData[tipoId]) {
            const tipo = tiposData[tipoId];

            // Mostrar info card
            typeInfoCard.classList.add('visible');
            updateTypeFieldsGrid(tipo);

            // Mostrar sección de campos
            fieldsSection.style.display = 'block';
            placeholderSection.style.display = 'none';

            // Actualizar campos según el tipo
            updateFieldsBasedOnType(tipo);

            // Habilitar botón de submit
            submitBtn.disabled = false;
        } else {
            // Ocultar todo
            typeInfoCard.classList.remove('visible');
            fieldsSection.style.display = 'none';
            placeholderSection.style.display = 'block';
            submitBtn.disabled = true;
        }
    });

    function updateTypeFieldsGrid(tipo) {
        const fields = [
            { key: 'has_banco', label: 'Banco' },
            { key: 'has_titular', label: 'Titular' },
            { key: 'has_cedula', label: 'Cédula' },
            { key: 'has_telefono', label: 'Teléfono' },
            { key: 'has_nro_cuenta', label: 'Nro. Cuenta' }
        ];

        let html = '';
        fields.forEach(field => {
            const isActive = tipo[field.key];
            const iconClass = isActive ? 'fa-check' : 'fa-times';
            const tagClass = isActive ? 'active' : 'inactive';
            html += `<span class="type-field-tag ${tagClass}">
                <i class="fas ${iconClass}"></i>
                ${field.label}
            </span>`;
        });

        typeFieldsGrid.innerHTML = html;
    }

    function updateFieldsBasedOnType(tipo) {
        // Banco
        updateField('Banco', tipo.has_banco);
        // Titular
        updateField('Titular', tipo.has_titular);
        // Cedula
        updateField('Cedula', tipo.has_cedula);
        // Telefono
        updateField('Telefono', tipo.has_telefono);
        // NroCuenta
        updateField('NroCuenta', tipo.has_nro_cuenta);
    }

    function updateField(fieldName, isEnabled) {
        const fieldRow = document.getElementById('field' + fieldName);
        const checkbox = document.getElementById('check' + fieldName);
        const input = document.getElementById('input' + fieldName);

        if (isEnabled) {
            fieldRow.classList.remove('disabled');
            checkbox.checked = true;
            input.disabled = false;
        } else {
            fieldRow.classList.add('disabled');
            checkbox.checked = false;
            input.disabled = true;
            input.value = '';
        }
    }

    // Modal functions
    function openCreateTipoModal() {
        document.getElementById('createTipoModal').classList.add('active');
        document.getElementById('tipoDescripcion').focus();
    }

    function closeCreateTipoModal() {
        document.getElementById('createTipoModal').classList.remove('active');
        // Reset form
        document.getElementById('tipoDescripcion').value = '';
        document.getElementById('modalHasBanco').checked = false;
        document.getElementById('modalHasTitular').checked = false;
        document.getElementById('modalHasCedula').checked = false;
        document.getElementById('modalHasTelefono').checked = false;
        document.getElementById('modalHasNroCuenta').checked = false;
    }

    async function saveNewTipo() {
        const descripcion = document.getElementById('tipoDescripcion').value.trim();

        if (!descripcion) {
            alert('Por favor, ingresa un nombre para el tipo de pago.');
            return;
        }

        const saveBtn = document.getElementById('saveTipoBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        try {
            const formData = new FormData();
            formData.append(csrfParam, csrfToken);
            formData.append('descripcion', descripcion);
            formData.append('has_banco', document.getElementById('modalHasBanco').checked ? 1 : 0);
            formData.append('has_titular', document.getElementById('modalHasTitular').checked ? 1 : 0);
            formData.append('has_cedula', document.getElementById('modalHasCedula').checked ? 1 : 0);
            formData.append('has_telefono', document.getElementById('modalHasTelefono').checked ? 1 : 0);
            formData.append('has_nro_cuenta', document.getElementById('modalHasNroCuenta').checked ? 1 : 0);

            const response = await fetch(createTipoUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Agregar nuevo tipo al select
                const newOption = document.createElement('option');
                newOption.value = result.tipo.id;
                newOption.textContent = result.tipo.descripcion;
                tipoSelect.appendChild(newOption);

                // Agregar a tiposData
                tiposData[result.tipo.id] = {
                    descripcion: result.tipo.descripcion,
                    has_banco: result.tipo.has_banco,
                    has_titular: result.tipo.has_titular,
                    has_cedula: result.tipo.has_cedula,
                    has_telefono: result.tipo.has_telefono,
                    has_nro_cuenta: result.tipo.has_nro_cuenta
                };

                // Seleccionar el nuevo tipo
                tipoSelect.value = result.tipo.id;
                tipoSelect.dispatchEvent(new Event('change'));

                // Cerrar modal
                closeCreateTipoModal();
            } else {
                alert('Error al crear el tipo: ' + result.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al crear el tipo de pago.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Tipo';
        }
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('createTipoModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeCreateTipoModal();
        }
    });

    // Cerrar modal con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateTipoModal();
        }
    });
</script>