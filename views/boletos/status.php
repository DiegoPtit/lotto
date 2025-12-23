<?php
use yii\helpers\Html;
use app\models\Boletos;

/** @var yii\web\View $this */
/** @var app\models\Boletos $boleto */
/** @var app\models\Rifas $rifa */
/** @var array $numeros */

// Desactivar el layout principal
$this->context->layout = false;

$isReservado = $boleto->isEstadoReservado();
$isPagado = $boleto->isEstadoPagado();
$isAnulado = $boleto->isEstadoAnulado();
$isReembolsado = $boleto->isEstadoReembolsado();
$isGanador = $boleto->isEstadoGanador();

// Determinar el estado y clase de color
if ($isPagado) {
    $statusClass = 'status-pagado';
    $statusText = 'Pagado';
    $statusColor = 'success';
    $statusIcon = 'fa-check-circle';
} elseif ($isReservado) {
    $statusClass = 'status-reservado';
    $statusText = 'En Proceso - Pendiente de Aprobación';
    $statusColor = 'warning';
    $statusIcon = 'fa-clock';
} elseif ($isGanador) {
    $statusClass = 'status-ganador';
    $statusText = '¡Ganador!';
    $statusColor = 'success';
    $statusIcon = 'fa-trophy';
} elseif ($isAnulado) {
    $statusClass = 'status-anulado';
    $statusText = 'Anulado';
    $statusColor = 'danger';
    $statusIcon = 'fa-times-circle';
} elseif ($isReembolsado) {
    $statusClass = 'status-reembolsado';
    $statusText = 'Reembolsado';
    $statusColor = 'info';
    $statusIcon = 'fa-undo';
} else {
    $statusClass = 'status-unknown';
    $statusText = 'Desconocido';
    $statusColor = 'secondary';
    $statusIcon = 'fa-question-circle';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del Boleto #<?= Html::encode($boleto->codigo) ?> - <?= Html::encode(Yii::$app->name) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

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
            overflow-x: hidden;
        }

        .boleto-status-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
        }

        .status-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .status-header {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .status-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .status-subtitle {
            font-size: 1.1rem;
            color: #718096;
            margin-bottom: 20px;
        }

        .status-badge-large {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .boleto-macro-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .boleto-macro-card.expanded .boleto-card-body {
            max-height: 2000px;
            padding: 30px;
        }

        .boleto-macro-card.expanded .expand-icon {
            transform: rotate(180deg);
        }

        .boleto-card-header {
            padding: 25px 30px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .boleto-card-header:hover {
            background-color: #f7fafc;
        }

        .status-pagado .boleto-card-header {
            background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%);
        }

        .status-reservado .boleto-card-header {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        .status-anulado .boleto-card-header {
            background: linear-gradient(135deg, #fee 0%, #fecaca 100%);
        }

        .status-ganador .boleto-card-header {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .boleto-info-main {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .boleto-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .status-pagado .boleto-icon-box {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .status-reservado .boleto-icon-box {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .status-anulado .boleto-icon-box {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .status-ganador .boleto-icon-box {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
        }

        .boleto-details h5 {
            margin: 0 0 8px 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a202c;
        }

        .boleto-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #718096;
            font-size: 0.95rem;
        }

        .boleto-meta-divider {
            color: #cbd5e0;
        }

        .boleto-summary-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .boleto-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .boleto-count-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .expand-icon {
            font-size: 1.2rem;
            color: #a0aec0;
            transition: transform 0.3s ease;
            margin-left: 15px;
        }

        .boleto-card-body {
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0 30px;
        }

        .boleto-body-content {
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
        }

        .macro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }

        .macro-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .macro-number:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .info-card h6 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
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

        .btn-home {
            background: white;
            color: #667eea;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            background: #f7fafc;
            color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }

        @media (max-width: 768px) {
            .boleto-status-page {
                padding: 20px 10px;
            }

            .status-header {
                padding: 30px 20px;
            }

            .status-title {
                font-size: 1.5rem;
            }

            .status-icon {
                font-size: 3rem;
            }

            .boleto-info-main {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .boleto-card-header {
                flex-direction: column;
                gap: 15px;
            }

            .boleto-summary-right {
                width: 100%;
                align-items: flex-start;
            }

            .expand-icon {
                margin-left: 0;
            }

            .macro-grid {
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
                gap: 8px;
            }

            .macro-number {
                font-size: 1rem;
                padding: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="boleto-status-page">
        <div class="status-container">
            <!-- Header con estado -->
            <div class="status-header animate__animated animate__fadeInDown">
                <div class="status-icon text-<?= $statusColor ?>">
                    <i class="fas <?= $statusIcon ?>"></i>
                </div>
                <h1 class="status-title">Boleto #<?= Html::encode($boleto->codigo) ?></h1>
                <p class="status-subtitle"><?= Html::encode($rifa->titulo) ?></p>
                <span class="status-badge-large bg-<?= $statusColor ?>">
                    <i class="fas <?= $statusIcon ?> me-2"></i><?= $statusText ?>
                </span>
            </div>

            <!-- Información del jugador -->
            <div class="info-card animate__animated animate__fadeInUp">
                <h6><i class="fas fa-user me-2"></i>Información del Jugador</h6>
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value"><?= Html::encode($boleto->jugador->nombre) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cédula:</span>
                    <span class="info-value"><?= Html::encode($boleto->jugador->cedula) ?></span>
                </div>
                <?php if ($boleto->jugador->correo): ?>
                    <div class="info-row">
                        <span class="info-label">Correo:</span>
                        <span class="info-value"><?= Html::encode($boleto->jugador->correo) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($boleto->jugador->telefono): ?>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value"><?= Html::encode($boleto->jugador->telefono) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Detalles del boleto -->
            <div class="boleto-macro-card <?= $statusClass ?> animate__animated animate__fadeInUp">
                <div class="boleto-card-header"
                    onclick="this.closest('.boleto-macro-card').classList.toggle('expanded')">
                    <div class="boleto-info-main">
                        <div class="boleto-icon-box">
                            <i class="fas <?= $statusIcon ?>"></i>
                        </div>
                        <div class="boleto-details">
                            <h5>Ticket #<?= Html::encode($boleto->codigo) ?></h5>
                            <div class="boleto-meta">
                                <span><i class="far fa-calendar-alt me-1"></i>
                                    <?= date('d M Y, H:i', strtotime($boleto->created_at)) ?></span>
                                <span class="boleto-meta-divider">•</span>
                                <span class="text-<?= $statusColor ?> fw-bold">
                                    <?= $statusText ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="boleto-info-main">
                        <div class="boleto-summary-right">
                            <span
                                class="boleto-price"><?= Yii::$app->formatter->asCurrency($boleto->total_precio, 'Bs. ') ?></span>
                            <span class="boleto-count-badge"><?= count($numeros) ?> Números</span>
                        </div>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                <div class="boleto-card-body">
                    <div class="boleto-body-content">
                        <h6 class="text-muted mb-3 text-uppercase fw-bold"
                            style="font-size: 0.8rem; letter-spacing: 1px;">
                            <i class="fas fa-list-ol me-2"></i>Números Jugados
                        </h6>
                        <div class="macro-grid">
                            <?php foreach ($numeros as $numero): ?>
                                <div class="macro-number animate__animated animate__zoomIn">
                                    <?= Html::encode($numero) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje informativo según el estado -->
            <?php if ($isReservado): ?>
                <div class="alert alert-warning animate__animated animate__fadeInUp"
                    style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);">
                    <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>¡Tu boleto está siendo procesado!</h5>
                    <p class="mb-0">Estamos verificando tu pago. Una vez confirmado, recibirás un correo con la confirmación
                        y tu boleto pasará a estado <strong>Pagado</strong>. Este proceso puede tardar hasta 24 horas.</p>
                </div>
            <?php elseif ($isPagado): ?>
                <div class="alert alert-success animate__animated animate__fadeInUp"
                    style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);">
                    <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i>¡Tu boleto está confirmado!</h5>
                    <p class="mb-0">Tu pago ha sido verificado y tu participación en la rifa está confirmada. ¡Mucha suerte!
                    </p>
                </div>
            <?php elseif ($isGanador): ?>
                <div class="alert alert-success animate__animated animate__fadeInUp animate__pulse animate__infinite"
                    style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);">
                    <h5 class="alert-heading"><i class="fas fa-trophy me-2"></i>¡FELICITACIONES, ERES GANADOR!</h5>
                    <p class="mb-0">¡Has ganado! Nos pondremos en contacto contigo pronto para coordinar la entrega de tu
                        premio.</p>
                </div>
            <?php elseif ($isAnulado): ?>
                <div class="alert alert-danger animate__animated animate__fadeInUp"
                    style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);">
                    <h5 class="alert-heading"><i class="fas fa-times-circle me-2"></i>Boleto Anulado</h5>
                    <p class="mb-0">Este boleto ha sido anulado. Si crees que esto es un error, por favor contacta con
                        soporte.</p>
                </div>
            <?php elseif ($isReembolsado): ?>
                <div class="alert alert-info animate__animated animate__fadeInUp"
                    style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);">
                    <h5 class="alert-heading"><i class="fas fa-undo me-2"></i>Boleto Reembolsado</h5>
                    <p class="mb-0">El pago de este boleto ha sido reembolsado.</p>
                </div>
            <?php endif; ?>

            <!-- Botón de regreso -->
            <div class="text-center mt-4 animate__animated animate__fadeInUp">
                <a href="<?= Yii::$app->homeUrl ?>" class="btn-home">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>