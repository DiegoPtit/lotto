<?php

/** @var yii\web\View $this */
/** @var app\models\Testimonios $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Sorteos;
use app\models\Jugadores;

$this->title = 'Crear Testimonio';
$this->params['breadcrumbs'][] = ['label' => 'Testimonios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Obtener sorteos activos para el dropdown
$sorteos = Sorteos::find()
    ->orderBy(['created_at' => SORT_DESC])
    ->all();
$sorteosData = [];
foreach ($sorteos as $sorteo) {
    $rifa = $sorteo->rifa;
    $sorteosData[$sorteo->id] = $rifa ? $rifa->titulo . ' - Sorteo #' . $sorteo->id : 'Sorteo #' . $sorteo->id;
}

// Obtener jugadores para el dropdown
$jugadores = Jugadores::find()
    ->where(['is_deleted' => 0])
    ->orderBy(['nombre' => SORT_ASC])
    ->all();
$jugadoresData = [];
foreach ($jugadores as $jugador) {
    $jugadoresData[$jugador->id] = $jugador->nombre . ' (' . $jugador->cedula . ')';
}
?>

<style>
    /* ==================== CREATE TESTIMONIO STYLES ==================== */
    .create-testimonio-page {
        padding: 30px 0;
        max-width: 800px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 35px;
    }

    .page-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header h1 i {
        color: #28a745;
    }

    .page-header p {
        color: #666;
        margin: 0;
    }

    /* Formulario card */
    .form-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 35px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section:last-of-type {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #0066cc;
    }

    /* Form fields */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
    }

    .form-group label .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 1.5px solid #ddd;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafbfc;
    }

    .form-control:focus {
        outline: none;
        border-color: #0066cc;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 20px;
        padding-right: 40px;
    }

    /* File upload */
    .file-upload-wrapper {
        position: relative;
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        background: #fafbfc;
        cursor: pointer;
    }

    .file-upload-wrapper:hover {
        border-color: #0066cc;
        background: #f0f7ff;
    }

    .file-upload-wrapper.dragover {
        border-color: #28a745;
        background: #e8f5e9;
    }

    .file-upload-wrapper input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload-icon {
        font-size: 2.5rem;
        color: #aaa;
        margin-bottom: 12px;
    }

    .file-upload-text {
        color: #666;
        font-size: 0.95rem;
    }

    .file-upload-text strong {
        color: #0066cc;
    }

    .file-upload-hint {
        font-size: 0.8rem;
        color: #999;
        margin-top: 8px;
    }

    /* Preview */
    .media-preview {
        margin-top: 15px;
        display: none;
    }

    .media-preview img,
    .media-preview video {
        max-width: 100%;
        max-height: 250px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .media-preview.active {
        display: block;
    }

    /* Botones */
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 35px;
        padding-top: 25px;
        border-top: 1px solid #eee;
    }

    .btn-submit {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #28a745 0%, #20833a 100%);
        color: #ffffff;
        padding: 16px 30px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #f5f5f5;
        color: #666;
        padding: 16px 25px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #e0e0e0;
        color: #333;
        text-decoration: none;
    }

    /* Validation errors */
    .has-error .form-control {
        border-color: #dc3545;
    }

    .help-block {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 6px;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .create-testimonio-page {
            padding: 20px 15px;
        }

        .form-card {
            padding: 25px 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-cancel {
            order: 2;
        }
    }
</style>

<div class="create-testimonio-page">
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-plus-circle"></i>
                Crear Nuevo Testimonio
            </h1>
            <p>Agrega un nuevo testimonio de un ganador para mostrar en el sitio</p>
        </div>

        <!-- Formulario -->
        <div class="form-card">
            <?php $form = ActiveForm::begin([
                'id' => 'create-testimonio-form',
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <!-- Sección: Información del Sorteo -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-trophy"></i>
                    Información del Sorteo
                </h3>

                <div class="form-group">
                    <label>Sorteo <span class="required">*</span></label>
                    <?= Html::activeDropDownList($model, 'id_sorteo', $sorteosData, [
                        'class' => 'form-control',
                        'prompt' => 'Seleccione un sorteo...'
                    ]) ?>
                </div>

                <div class="form-group">
                    <label>Jugador Ganador <span class="required">*</span></label>
                    <?= Html::activeDropDownList($model, 'id_jugador_ganador', $jugadoresData, [
                        'class' => 'form-control',
                        'prompt' => 'Seleccione el ganador...'
                    ]) ?>
                </div>
            </div>

            <!-- Sección: Contenido -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-edit"></i>
                    Contenido del Testimonio
                </h3>

                <div class="form-group">
                    <label>Título <span class="required">*</span></label>
                    <?= Html::activeTextInput($model, 'titulo', [
                        'class' => 'form-control',
                        'placeholder' => 'Ej: ¡Gané mi primer premio!',
                        'maxlength' => 80
                    ]) ?>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <?= Html::activeTextarea($model, 'descripcion', [
                        'class' => 'form-control',
                        'placeholder' => 'Cuéntanos la historia del ganador...',
                        'rows' => 5
                    ]) ?>
                </div>
            </div>

            <!-- Sección: Media -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-photo-video"></i>
                    Imagen o Video
                </h3>

                <div class="form-group">
                    <label>Archivo multimedia</label>
                    <div class="file-upload-wrapper" id="fileDropzone">
                        <input type="file" name="Testimonios[url_media]" id="mediaFile" accept="image/*,video/*">
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="file-upload-text">
                            <strong>Haz clic para subir</strong> o arrastra un archivo aquí
                        </div>
                        <div class="file-upload-hint">
                            Imágenes: JPG, PNG, GIF | Videos: MP4, WEBM (máx. 50MB)
                        </div>
                    </div>

                    <div class="media-preview" id="mediaPreview">
                        <img id="previewImage" src="" alt="Preview" style="display: none;">
                        <video id="previewVideo" src="" controls style="display: none;"></video>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-actions">
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'], ['class' => 'btn-cancel']) ?>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    Guardar Testimonio
                </button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('mediaFile');
        const dropzone = document.getElementById('fileDropzone');
        const previewContainer = document.getElementById('mediaPreview');
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');

        // Drag and drop
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handleFileSelect(this.files[0]);
            }
        });

        function handleFileSelect(file) {
            const isImage = file.type.startsWith('image/');
            const isVideo = file.type.startsWith('video/');

            previewContainer.classList.add('active');
            previewImage.style.display = 'none';
            previewVideo.style.display = 'none';

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (isVideo) {
                previewVideo.src = URL.createObjectURL(file);
                previewVideo.style.display = 'block';
            }
        }
    });
</script>