<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas $model */
/** @var app\models\Sorteos $sorteo */
/** @var app\models\Premios[] $premios */
/** @var bool $isUpdate */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

// Determinar si estamos en modo edición
$isUpdate = $isUpdate ?? !$model->isNewRecord;

// Verificar restricciones de edición
$today = date('Y-m-d');
$canEditFechaInicio = !$isUpdate || ($model->fecha_inicio && $model->fecha_inicio > $today);
$canEditMaxNumeros = !$isUpdate;
?>

<style>
    .rifa-form-container {
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
        flex-direction: column;
    }

    .rifa-image-section::before {
        content: '';
        display: block;
        padding-bottom: 100%;
    }

    .rifa-image-preview {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .rifa-image-preview.has-image {
        display: block;
    }

    .image-fallback {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 4rem;
        color: #bdc3c7;
        z-index: 1;
    }

    .image-fallback.hidden {
        display: none;
    }

    .btn-upload-image {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 2;
    }

    .btn-upload-image:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    .rifa-info-section {
        padding: 1.25rem 1.5rem;
    }

    .rifa-precio-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8f9fa;
        color: #2c3e50;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        border: 1px solid #e8e8e8;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .rifa-precio-container span {
        font-weight: 500;
        color: #7f8c8d;
    }

    .rifa-precio-container input {
        width: 100px;
        border: none;
        background: transparent;
        color: #2c3e50;
        font-size: 1.125rem;
        font-weight: 600;
        outline: none;
        text-align: right;
    }

    .rifa-precio-container input:disabled {
        color: #2c3e50;
        cursor: default;
    }

    .rifa-precio-container input::placeholder {
        color: #95a5a6;
    }

    .rifa-precio-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8f9fa;
        color: #2c3e50;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        border: 1px solid #e8e8e8;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .rifa-precio-display span:first-child {
        font-weight: 500;
        color: #7f8c8d;
    }

    .rifa-precio-display span:last-child {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2c3e50;
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

    .btn-confirmar {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }

    .btn-confirmar:hover {
        background: #219a52;
        border-color: #219a52;
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

    .card-header-custom {
        background: #f8f9fa;
        border-bottom: 2px solid #e8e8e8;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        font-weight: 600;
        color: #34495e;
    }

    .card-body-custom {
        padding: 1.25rem;
    }

    /* Form styles */
    .form-field {
        margin-bottom: 1rem;
    }

    .form-field label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #5a6c7d;
        margin-bottom: 0.5rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-field input,
    .form-field textarea,
    .form-field select {
        width: 100%;
        padding: 0.65rem 0.875rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-field input:focus,
    .form-field textarea:focus,
    .form-field select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    .form-field input:disabled,
    .form-field textarea:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .input-with-legend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .input-with-legend .legend {
        font-size: 0.85rem;
        color: #7f8c8d;
        white-space: nowrap;
    }

    .input-with-legend input {
        flex: 1;
    }

    /* Premio specific fields */
    .premio-card .form-field {
        margin-bottom: 0.875rem;
    }

    .premio-card .form-field:first-of-type {
        margin-top: 2.25rem;
    }

    .premio-card .form-field:last-of-type {
        margin-bottom: 0;
    }

    .premio-card .form-field label {
        font-size: 0.7rem;
        color: #888;
        margin-bottom: 0.375rem;
    }

    .premio-card .form-field input[type="text"] {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        border: none;
        border-bottom: 2px solid #e8e8e8;
        border-radius: 0;
        padding: 0.5rem 0;
    }

    .premio-card .form-field input[type="text"]:focus {
        border-bottom-color: #3498db;
        box-shadow: none;
    }

    .premio-card .form-field textarea {
        font-size: 0.85rem;
        color: #666;
        padding: 0.5rem 0.75rem;
        min-height: 60px;
        resize: vertical;
    }

    .premio-card .form-field:last-of-type input {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    /* Checkbox styles */
    .checkbox-field {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1rem 0;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .checkbox-field input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-field label {
        font-size: 0.9rem;
        color: #2c3e50;
        cursor: pointer;
        margin: 0;
    }

    /* Sorteo section */
    .sorteo-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #ddd;
    }

    .sorteo-section.hidden {
        display: none;
    }

    /* Premios grid */
    .premios-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .premio-card {
        background: white;
        border-radius: 8px;
        padding: 1.25rem;
        border: 1px solid #e0e0e0;
        position: relative;
        transition: all 0.2s ease;
    }

    .premio-card:hover {
        border-color: #d0d0d0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .premio-card .premio-orden {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        min-width: 24px;
        height: 24px;
        padding: 0 0.5rem;
        background: #f0f0f0;
        color: #666;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .premio-card .btn-remove-premio {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        width: 24px;
        height: 24px;
        background: transparent;
        color: #999;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.7rem;
        transition: all 0.2s ease;
        opacity: 0.6;
    }

    .premio-card:hover .btn-remove-premio {
        opacity: 1;
    }

    .premio-card .btn-remove-premio:hover {
        background: #fee;
        border-color: #e74c3c;
        color: #e74c3c;
    }

    .btn-add-premio {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: transparent;
        border: 2px dashed #3498db;
        color: #3498db;
        padding: 2rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        font-weight: 500;
        min-height: 180px;
    }

    .btn-add-premio:hover {
        background: rgba(52, 152, 219, 0.05);
        border-color: #2980b9;
        color: #2980b9;
    }

    /* Time picker styles */
    .time-picker-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .time-picker-row .time-label {
        font-size: 0.85rem;
        color: #7f8c8d;
        font-weight: normal;
    }

    .time-picker-row select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .time-picker-row select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    .time-picker-row select:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
        color: #999;
    }

    .time-picker-row .time-separator {
        font-weight: 600;
        color: #7f8c8d;
        font-size: 1rem;
    }

    .form-hint {
        font-size: 0.75rem;
        color: #95a5a6;
        margin-top: 0.25rem;
    }

    .hidden-input {
        display: none;
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

        .form-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .rifa-grid {
            gap: 1rem;
        }

        .premios-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'rifa-form',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="rifa-form-container">
    <div class="rifa-grid">
        <!-- Columna 1: Sidebar -->
        <div class="rifa-sidebar">
            <div class="rifa-card">
                <!-- Imagen -->
                <div class="rifa-image-section">
                    <img id="image-preview" 
                         class="rifa-image-preview <?= !empty($model->img) ? 'has-image' : '' ?>" 
                         src="<?= !empty($model->img) ? Html::encode($model->img) : '' ?>" 
                         alt="Preview">
                    <i id="image-fallback" class="fas fa-ticket-alt image-fallback <?= !empty($model->img) ? 'hidden' : '' ?>"></i>
                    
                    <input type="file" id="imagen-input" name="imagen" accept="image/*" class="hidden-input">
                    <input type="hidden" name="Rifas[img]" id="imagen-url" value="<?= Html::encode($model->img) ?>">
                    
                    <button type="button" class="btn-upload-image" onclick="document.getElementById('imagen-input').click()">
                        <i class="fas fa-camera me-1"></i> Cargar <?= !empty($model->img) ? 'otra ' : '' ?>imagen
                    </button>
                </div>

                <!-- Información -->
                <div class="rifa-info-section">
                    <div class="rifa-precio-container">
                        <span>Bs.</span>
                        <input type="number" 
                               name="Rifas[precio_boleto]" 
                               id="precio-boleto"
                               value="<?= $model->precio_boleto ?>" 
                               placeholder="0.00" 
                               step="0.01" 
                               min="0"
                               <?= $isUpdate ? 'disabled' : '' ?>
                               required>
                    </div>
                    <?php if ($isUpdate): ?>
                        <input type="hidden" name="Rifas[precio_boleto]" value="<?= $model->precio_boleto ?>">
                    <?php endif; ?>
                </div>

                <!-- Footer con Botones -->
                <div class="rifa-footer">
                    <?= Html::submitButton('<i class="fas fa-check me-1"></i> Confirmar', [
                        'class' => 'btn btn-confirmar',
                        'id' => 'btn-submit'
                    ]) ?>
                    <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Cancelar', ['index'], ['class' => 'btn btn-cancelar']) ?>
                </div>
            </div>
        </div>

        <!-- Columna 2: Contenido Principal -->
        <div class="rifa-main-content">
            <!-- Tarjeta 1: Información de la Rifa -->
            <div class="rifa-card">
                <div class="card-header-custom">
                    <i class="fas fa-info-circle me-2"></i>Información de la Rifa
                </div>
                <div class="card-body-custom">
                    <div class="form-field">
                        <label for="slug">Descripción Corta</label>
                        <input type="text" 
                               name="Rifas[slug]" 
                               id="slug" 
                               value="<?= Html::encode($model->slug) ?>" 
                               placeholder="Rifa benéfica navideña"
                               <?= $isUpdate ? 'disabled' : '' ?>
                               required>
                        <?php if ($isUpdate): ?>
                            <input type="hidden" name="Rifas[slug]" value="<?= Html::encode($model->slug) ?>">
                        <?php endif; ?>
                        <div class="form-hint">Breve descripción identificativa de la rifa</div>
                    </div>

                    <div class="form-field">
                        <label for="titulo">Título de la Rifa</label>
                        <input type="text" 
                               name="Rifas[titulo]" 
                               id="titulo" 
                               value="<?= Html::encode($model->titulo) ?>" 
                               placeholder="Gran Rifa de Navidad 2024"
                               required>
                    </div>

                    <div class="form-field">
                        <label for="descripcion">Descripción</label>
                        <textarea name="Rifas[descripcion]" 
                                  id="descripcion" 
                                  rows="3" 
                                  placeholder="Describe tu rifa aquí..."><?= Html::encode($model->descripcion) ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="max_numeros">Cantidad de Números</label>
                            <div class="input-with-legend">
                                <input type="number" 
                                       name="Rifas[max_numeros]" 
                                       id="max_numeros" 
                                       value="<?= $model->max_numeros ?: 100 ?>" 
                                       min="1"
                                       <?= $canEditMaxNumeros ? '' : 'disabled' ?>
                                       required>
                                <span class="legend">números</span>
                            </div>
                            <?php if (!$canEditMaxNumeros): ?>
                                <input type="hidden" name="Rifas[max_numeros]" value="<?= $model->max_numeros ?>">
                                <div class="form-hint">No se puede modificar después de crear la rifa</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="fecha_fin">Fecha de Finalización</label>
                        <?php 
                        $fechaFinDate = $model->fecha_fin ? date('Y-m-d', strtotime($model->fecha_fin)) : '';
                        $fechaFinHour = $model->fecha_fin ? date('h', strtotime($model->fecha_fin)) : '11';
                        $fechaFinMinute = $model->fecha_fin ? date('i', strtotime($model->fecha_fin)) : '59';
                        $fechaFinAmPm = $model->fecha_fin ? date('A', strtotime($model->fecha_fin)) : 'PM';
                        ?>
                        <input type="date" 
                               name="fecha_fin_date" 
                               id="fecha_fin_date" 
                               value="<?= $fechaFinDate ?>">
                        <div class="time-picker-row">
                            <span class="time-label">a las</span>
                            <select name="fecha_fin_hour" id="fecha_fin_hour">
                                <?php for ($h = 1; $h <= 12; $h++): ?>
                                    <option value="<?= sprintf('%02d', $h) ?>" <?= $fechaFinHour == sprintf('%02d', $h) ? 'selected' : '' ?>><?= sprintf('%02d', $h) ?></option>
                                <?php endfor; ?>
                            </select>
                            <span class="time-separator">:</span>
                            <select name="fecha_fin_minute" id="fecha_fin_minute">
                                <?php for ($m = 0; $m < 60; $m += 5): ?>
                                    <option value="<?= sprintf('%02d', $m) ?>" <?= $fechaFinMinute == sprintf('%02d', $m) ? 'selected' : '' ?>><?= sprintf('%02d', $m) ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="fecha_fin_ampm" id="fecha_fin_ampm">
                                <option value="AM" <?= $fechaFinAmPm == 'AM' ? 'selected' : '' ?>>AM</option>
                                <option value="PM" <?= $fechaFinAmPm == 'PM' ? 'selected' : '' ?>>PM</option>
                            </select>
                        </div>
                    </div>

                    <!-- Checkbox para fecha de sorteo -->
                    <div class="checkbox-field">
                        <input type="checkbox" 
                               id="fecha_sorteo_es_fin" 
                               name="fecha_sorteo_es_fin" 
                               value="1"
                               <?= (!$isUpdate || ($sorteo && $sorteo->fecha_sorteo == $model->fecha_fin)) ? 'checked' : '' ?>>
                        <label for="fecha_sorteo_es_fin">¿La Fecha de Finalización es la Fecha de Sorteo?</label>
                    </div>

                    <!-- Sección Sorteo -->
                    <div id="sorteo-section" class="sorteo-section">
                        <div class="form-field" id="fecha-sorteo-field">
                            <label for="fecha_sorteo">Fecha del Sorteo</label>
                            <input type="date" 
                                   name="Sorteos[fecha_sorteo]" 
                                   id="fecha_sorteo" 
                                   value="<?= $sorteo && $sorteo->fecha_sorteo ? date('Y-m-d', strtotime($sorteo->fecha_sorteo)) : '' ?>">
                            <div class="form-hint">Debe ser posterior a la fecha de finalización de la rifa</div>
                        </div>

                        <div class="form-field">
                            <label for="metodo_sorteo">Método de Sorteo</label>
                            <input type="text" 
                                   name="Sorteos[metodo_sorteo]" 
                                   id="metodo_sorteo" 
                                   value="<?= Html::encode($sorteo->metodo_sorteo ?? '') ?>" 
                                   placeholder="Lotería del Táchira, GanaYa, etc...">
                            <div class="form-hint">Indica cómo se realizará el sorteo</div>
                        </div>

                        <div class="form-field" id="sorteo-descripcion-field">
                            <label for="sorteo_descripcion">Descripción del Sorteo</label>
                            <textarea name="Sorteos[descripcion]" 
                                      id="sorteo_descripcion" 
                                      rows="2" 
                                      placeholder="Detalles adicionales del sorteo..."><?= Html::encode($sorteo->descripcion ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Premios -->
            <div class="rifa-card">
                <div class="card-header-custom">
                    <i class="fas fa-gift me-2"></i>Premios de la Rifa
                </div>
                <div class="card-body-custom">
                    <div class="premios-grid" id="premios-container">
                        <?php if (!empty($premios)): ?>
                            <?php foreach ($premios as $index => $premio): ?>
                                <div class="premio-card" data-index="<?= $index ?>">
                                    <span class="premio-orden"><?= $index + 1 ?></span>
                                    <button type="button" class="btn-remove-premio" onclick="removePremio(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <input type="hidden" name="Premios[<?= $index ?>][id]" value="<?= $premio->id ?>">
                                    
                                    <div class="form-field">
                                        <label>Título del Premio</label>
                                        <input type="text" 
                                               name="Premios[<?= $index ?>][titulo]" 
                                               value="<?= Html::encode($premio->titulo) ?>" 
                                               placeholder="Ej: Primer Premio"
                                               required>
                                    </div>
                                    
                                    <div class="form-field">
                                        <label>Descripción</label>
                                        <textarea name="Premios[<?= $index ?>][descripcion]" 
                                                  rows="2" 
                                                  placeholder="Describe el premio..."><?= Html::encode($premio->descripcion) ?></textarea>
                                    </div>
                                    
                                    <div class="form-field">
                                        <label>Valor Estimado</label>
                                        <div class="input-with-legend">
                                            <span class="legend">Bs.</span>
                                            <input type="number" 
                                                   name="Premios[<?= $index ?>][valor_estimado]" 
                                                   value="<?= $premio->valor_estimado ?>" 
                                                   step="0.01" 
                                                   min="0"
                                                   placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Botón agregar premio -->
                        <button type="button" class="btn-add-premio" onclick="addPremio()">
                            <i class="fas fa-plus"></i> Agregar Premio
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagen
    const imagenInput = document.getElementById('imagen-input');
    const imagePreview = document.getElementById('image-preview');
    const imageFallback = document.getElementById('image-fallback');
    
    imagenInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.add('has-image');
                imageFallback.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Toggle sorteo section
    const checkboxFechaSorteo = document.getElementById('fecha_sorteo_es_fin');
    const fechaSorteoField = document.getElementById('fecha-sorteo-field');
    const sorteoDescripcionField = document.getElementById('sorteo-descripcion-field');
    
    function toggleSorteoFields() {
        if (checkboxFechaSorteo.checked) {
            fechaSorteoField.style.display = 'none';
        } else {
            fechaSorteoField.style.display = 'block';
        }
    }
    
    checkboxFechaSorteo.addEventListener('change', toggleSorteoFields);
    toggleSorteoFields();

    // Sincronizar fecha_sorteo con fecha_fin cuando está marcado el checkbox
    const fechaFinDateInput = document.getElementById('fecha_fin_date');
    const fechaSorteoInput = document.getElementById('fecha_sorteo');
    
    fechaFinDateInput.addEventListener('change', function() {
        if (checkboxFechaSorteo.checked) {
            fechaSorteoInput.value = this.value;
        }
    });

    // Validar que fecha_sorteo no esté dentro del rango de la rifa
    document.getElementById('rifa-form').addEventListener('submit', function(e) {
        const fechaInicio = document.getElementById('fecha_inicio_date').value;
        const fechaFin = document.getElementById('fecha_fin_date').value;
        const fechaSorteo = fechaSorteoInput.value;
        
        if (!checkboxFechaSorteo.checked && fechaSorteo) {
            if (fechaSorteo >= fechaInicio && fechaSorteo <= fechaFin) {
                e.preventDefault();
                alert('La fecha del sorteo no puede estar dentro del rango de fechas de la rifa (debe ser posterior a la fecha de finalización).');
                return false;
            }
        }
    });
});

// Premio counter
let premioIndex = <?= !empty($premios) ? count($premios) : 0 ?>;

function addPremio() {
    const container = document.getElementById('premios-container');
    const addButton = container.querySelector('.btn-add-premio');
    
    const premioHtml = `
        <div class="premio-card" data-index="${premioIndex}">
            <span class="premio-orden">${premioIndex + 1}</span>
            <button type="button" class="btn-remove-premio" onclick="removePremio(this)">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="form-field">
                <label>Título del Premio</label>
                <input type="text" 
                       name="Premios[${premioIndex}][titulo]" 
                       placeholder="Ej: Primer Premio"
                       required>
            </div>
            
            <div class="form-field">
                <label>Descripción</label>
                <textarea name="Premios[${premioIndex}][descripcion]" 
                          rows="2" 
                          placeholder="Describe el premio..."></textarea>
            </div>
            
            <div class="form-field">
                <label>Valor Estimado</label>
                <div class="input-with-legend">
                    <span class="legend">Bs.</span>
                    <input type="number" 
                           name="Premios[${premioIndex}][valor_estimado]" 
                           step="0.01" 
                           min="0"
                           placeholder="0.00">
                </div>
            </div>
        </div>
    `;
    
    addButton.insertAdjacentHTML('beforebegin', premioHtml);
    premioIndex++;
    updatePremioOrders();
}

function removePremio(button) {
    const card = button.closest('.premio-card');
    card.remove();
    updatePremioOrders();
}

function updatePremioOrders() {
    const cards = document.querySelectorAll('.premio-card');
    cards.forEach((card, index) => {
        card.querySelector('.premio-orden').textContent = index + 1;
    });
}
</script>
