<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Boletos $boleto */
/** @var app\models\Rifas $rifa */
/** @var app\models\Jugadores $jugador */
/** @var array $numeros */

$this->context->layout = false;
$this->title = 'Subir Nuevo Comprobante - Boleto #' . Html::encode($boleto->codigo);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .resubmit-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .page-icon {
            font-size: 3rem;
            color: #ef4444;
            margin-bottom: 15px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .page-subtitle {
            font-size: 1rem;
            color: #718096;
        }

        .card-custom {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.95rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #718096;
            font-weight: 500;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
        }

        .numbers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .number-chip {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .upload-zone {
            border: 3px dashed #cbd5e0;
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            background: #f7fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .upload-zone:hover {
            border-color: #667eea;
            background: #eef2ff;
        }

        .upload-zone.has-file {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .upload-icon {
            font-size: 3rem;
            color: #a0aec0;
            margin-bottom: 15px;
        }

        .upload-zone.has-file .upload-icon {
            color: #10b981;
        }

        .upload-text {
            color: #718096;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .upload-hint {
            color: #a0aec0;
            font-size: 0.85rem;
        }

        .preview-container {
            margin-top: 20px;
            display: none;
        }

        .preview-container.active {
            display: block;
        }

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #718096;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-cancel:hover {
            background: #cbd5e0;
            color: #4a5568;
        }

        .alert-info-custom {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-title {
            color: #92400e;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .alert-text {
            color: #78350f;
            margin: 0;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }

            .numbers-grid {
                grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
                gap: 8px;
            }

            .number-chip {
                font-size: 1rem;
                padding: 10px;
            }

            .card-custom {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="resubmit-container">
        <!-- Header -->
        <div class="page-header">
            <div class="page-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <h1 class="page-title">Subir Nuevo Comprobante</h1>
            <p class="page-subtitle">Boleto #<?= Html::encode($boleto->codigo) ?></p>
        </div>

        <!-- Alert Info -->
        <div class="alert-info-custom">
            <div class="alert-title">
                <i class="fas fa-info-circle me-2"></i>¿Por qué necesito subir un nuevo comprobante?
            </div>
            <p class="alert-text">
                Tu pago anterior no pudo ser verificado correctamente. Por favor, asegúrate de que el
                comprobante sea válido, el monto coincida con el precio del boleto (Bs.
                <?= number_format($boleto->total_precio, 2, ',', '.') ?>),
                y la transferencia haya sido realizada a la cuenta correcta. Si tienes dudas, contáctanos.
            </p>
        </div>

        <!-- Resumen del Boleto -->
        <div class="card-custom">
            <div class="card-title">
                <i class="fas fa-ticket-alt me-2"></i>Resumen del Boleto
            </div>

            <div class="info-row">
                <span class="info-label">Código:</span>
                <span class="info-value">#<?= Html::encode($boleto->codigo) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Rifa:</span>
                <span class="info-value"><?= Html::encode($rifa->titulo) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Jugador:</span>
                <span class="info-value"><?= Html::encode($jugador->nombre) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Cédula:</span>
                <span class="info-value"><?= Html::encode($jugador->cedula) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Cantidad de Números:</span>
                <span class="info-value"><?= count($numeros) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Monto a Pagar:</span>
                <span class="info-value" style="color: #10b981; font-size: 1.25rem;">
                    Bs. <?= number_format($boleto->total_precio, 2, ',', '.') ?>
                </span>
            </div>
        </div>

        <!-- Números Jugados -->
        <div class="card-custom">
            <div class="card-title">
                <i class="fas fa-hashtag me-2"></i>Números Jugados
            </div>
            <div class="numbers-grid">
                <?php foreach ($numeros as $numero): ?>
                    <div class="number-chip"><?= Html::encode($numero) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Formulario de Subida -->
        <div class="card-custom">
            <div class="card-title">
                <i class="fas fa-cloud-upload-alt me-2"></i>Nuevo Comprobante de Pago
            </div>

            <form id="resubmit-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                    value="<?= Yii::$app->request->csrfToken; ?>" />

                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('file-input').click()">
                    <i class="fas fa-cloud-upload-alt upload-icon" id="upload-icon"></i>
                    <div class="upload-text" id="upload-text">
                        Haz clic aquí para seleccionar tu comprobante
                    </div>
                    <div class="upload-hint">
                        Formatos aceptados: JPG, PNG, PDF (Máx. 5MB)
                    </div>
                    <input type="file" id="file-input" name="comprobante" accept="image/*,application/pdf"
                        style="display: none;" required>
                </div>

                <div class="preview-container" id="preview-container">
                    <img id="preview-image" class="preview-image" alt="Vista previa">
                </div>

                <button type="submit" class="btn-submit mt-4" id="submit-btn" disabled>
                    <i class="fas fa-paper-plane me-2"></i>Enviar Nuevo Comprobante
                </button>

                <a href="<?= Yii::$app->homeUrl ?>" class="btn-cancel">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const fileInput = document.getElementById('file-input');
        const uploadZone = document.getElementById('upload-zone');
        const uploadText = document.getElementById('upload-text');
        const uploadIcon = document.getElementById('upload-icon');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const submitBtn = document.getElementById('submit-btn');
        const form = document.getElementById('resubmit-form');

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                uploadZone.classList.add('has-file');
                uploadText.textContent = file.name;
                uploadIcon.className = 'fas fa-check-circle upload-icon';
                submitBtn.disabled = false;

                // Preview para imágenes
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        previewContainer.classList.add('active');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.classList.remove('active');
                }
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirigir a página de estado
                        window.location.href = data.redirect;
                    } else {
                        alert('Error: ' + data.message);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Nuevo Comprobante';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al enviar el comprobante. Por favor, inténtalo de nuevo.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Nuevo Comprobante';
                });
        });
    </script>
</body>

</html>