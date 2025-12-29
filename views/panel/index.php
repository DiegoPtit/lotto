<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas[] $rifasFiltradas */
/** @var string $estadoFiltro */
/** @var app\models\Boletos[] $boletosReservados */
/** @var int $totalReservados */
/** @var int $totalPagados */
/** @var int $totalAnulados */
/** @var int $totalReembolsados */

use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\models\Rifas;

$this->title = 'Panel Administrativo';
$this->registerCssFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
    .admin-panel {
        padding: 2rem 0;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #3498db;
    }

    .admin-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid #e8e8e8;
    }

    .admin-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .admin-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .admin-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .admin-card-header .admin-card-title {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .filter-select {
        padding: 8px 36px 8px 14px;
        font-size: 0.9rem;
        font-weight: 500;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 10px center;
        background-size: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        min-width: 140px;
    }

    .filter-select:hover {
        border-color: #3498db;
    }

    .filter-select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
    }

    .fallback-message {
        text-align: center;
        padding: 3rem 2rem;
        color: #7f8c8d;
        font-size: 1rem;
    }

    .fallback-icon {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }

    .btn-create-new {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .btn-create-new:hover {
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        color: white;
    }

    .boleto-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        border-left: 3px solid #e74c3c;
    }

    .boleto-item:hover {
        background: #e9ecef;
    }

    .boleto-info {
        flex: 1;
    }

    .boleto-codigo {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .boleto-player {
        font-size: 0.875rem;
        color: #7f8c8d;
        margin-bottom: 0.25rem;
    }

    .boleto-countdown {
        font-size: 0.8125rem;
        color: #e74c3c;
        font-weight: 600;
    }

    .countdown-timer {
        font-family: 'Courier New', monospace;
    }

    .boleto-link {
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }

    .boleto-link:hover {
        color: #2980b9;
        text-decoration: underline;
    }

    .boleto-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .btn-view {
        background: #3498db;
        color: white;
    }

    .btn-view:hover {
        background: #2980b9;
        transform: scale(1.1);
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
        transform: scale(1.1);
    }

    .success-message {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        color: white;
        padding: 2rem;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .rifa-item {
        background: #f8f9fa;
        border-left: 4px solid #3498db;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-radius: 0 8px 8px 0;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        position: relative;
    }

    .rifa-item-link {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .rifa-item:hover {
        background: #e9ecef;
        border-left-width: 6px;
    }

    .rifa-content {
        flex: 1;
        min-width: 0;
    }

    .rifa-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .rifa-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.875rem;
        color: #7f8c8d;
    }

    .rifa-badge {
        background: #3498db;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .rifa-badge-activa {
        background: #28a745;
    }

    .rifa-badge-borrador {
        background: #6c757d;
    }

    .rifa-badge-sorteada {
        background: #28a745;
    }

    .rifa-badge-cancelada {
        background: #dc3545;
    }

    .rifa-image-container {
        width: 100px;
        height: 100px;
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
        border: 2px solid #e8e8e8;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .rifa-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rifa-image-fallback {
        font-size: 2.5rem;
        color: #bdc3c7;
    }

    .rifa-countdown-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: #7f8c8d;
        margin-top: 0.375rem;
    }

    .rifa-countdown-label {
        font-weight: 500;
    }

    .rifa-countdown-value {
        font-weight: 700;
        font-family: 'Courier New', monospace;
        color: #e67e22;
    }

    .rifa-countdown-expired {
        color: #e74c3c !important;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }

        .admin-card {
            padding: 1rem;
        }

        .boleto-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .boleto-codigo {
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .boleto-link {
            font-size: 0.8125rem;
        }

        .boleto-actions {
            width: 100%;
            justify-content: flex-end;
        }

        /* Optimización para rifas en móviles */
        .rifa-item {
            flex-direction: column;
            padding: 0.875rem;
            gap: 0.75rem;
        }

        .rifa-item:hover {
            border-left-width: 4px;
        }

        .rifa-content {
            width: 100%;
        }

        .rifa-title {
            font-size: 1rem;
            margin-bottom: 0.625rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .rifa-badge {
            align-self: flex-start;
            margin-left: 0 !important;
        }

        .rifa-meta {
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.8125rem;
        }

        .rifa-meta span {
            display: flex;
            align-items: center;
        }

        .rifa-image-container {
            width: 70px;
            height: 70px;
            align-self: center;
            order: -1;
        }

        .rifa-image-fallback {
            font-size: 2rem;
        }
    }
</style>

<div class="admin-panel">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <!-- Primera Fila: Listado de Rifas -->
    <div class="row">
        <div class="col-12">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Listado de rifas
                    </h2>
                    <select class="filter-select" id="estado-filter"
                        onchange="window.location.href='<?= Url::to(['panel/index']) ?>&estado=' + this.value">
                        <option value="<?= Rifas::ESTADO_ACTIVA ?>" <?= $estadoFiltro === Rifas::ESTADO_ACTIVA ? 'selected' : '' ?>>Activas</option>
                        <option value="<?= Rifas::ESTADO_BORRADOR ?>" <?= $estadoFiltro === Rifas::ESTADO_BORRADOR ? 'selected' : '' ?>>Borrador</option>
                        <option value="<?= Rifas::ESTADO_SORTEADA ?>" <?= $estadoFiltro === Rifas::ESTADO_SORTEADA ? 'selected' : '' ?>>Sorteadas</option>
                        <option value="<?= Rifas::ESTADO_CANCELADA ?>" <?= $estadoFiltro === Rifas::ESTADO_CANCELADA ? 'selected' : '' ?>>Canceladas</option>
                    </select>
                </div>

                <?php if (empty($rifasFiltradas)): ?>
                    <div class="fallback-message">
                        <div class="fallback-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <p class="mb-0">No hay rifas con estado "<?= ucfirst($estadoFiltro) ?>"</p>
                        <?= Html::a('<i class="fas fa-plus me-2"></i>Crear una nueva', ['/rifas/create'], ['class' => 'btn btn-create-new']) ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($rifasFiltradas as $rifa):
                        $segundosRecaudacion = $rifa->getSegundosHastaFinRecaudacion();
                        $segundosSorteo = $rifa->getSegundosHastaSorteo();
                        $fechasCoinciden = ($segundosRecaudacion !== null && $segundosSorteo !== null && abs($segundosRecaudacion - $segundosSorteo) < 60);
                        $ganador = $rifa->estado === Rifas::ESTADO_SORTEADA ? $rifa->getGanador() : null;
                        ?>
                        <div class="rifa-item">
                            <?= Html::a('', ['/panel/rifas-view', 'id' => $rifa->id], [
                                'class' => 'rifa-item-link'
                            ]) ?>
                            <div class="rifa-content">
                                <div class="rifa-title">
                                    <?= Html::encode($rifa->titulo) ?>
                                    <span
                                        class="rifa-badge rifa-badge-<?= $rifa->estado ?> ms-2"><?= strtoupper($rifa->estado) ?></span>
                                </div>
                                <div class="rifa-meta">
                                    <?php if ($rifa->fecha_fin): ?>
                                        <span><i class="fas fa-calendar me-1"></i>
                                            <?= Yii::$app->formatter->asDate($rifa->fecha_fin, 'long') ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-dollar-sign me-1"></i>
                                        <?= Yii::$app->formatter->asCurrency($rifa->precio_boleto, 'Bs. ') ?></span>
                                    <?php if ($ganador): ?>
                                        <span><i class="fas fa-trophy me-1"></i>Ganador:
                                            <?= Html::encode($ganador->nombre) ?></span>
                                    <?php endif; ?>
                                </div>
                                <!-- Contadores de tiempo (solo para activas) -->
                                <?php if ($rifa->estado === Rifas::ESTADO_ACTIVA && ($segundosRecaudacion !== null || $segundosSorteo !== null)): ?>
                                    <?php if ($fechasCoinciden): ?>
                                        <div class="rifa-countdown-row">
                                            <span class="rifa-countdown-label"><i class="fas fa-clock me-1"></i>Fin y sorteo:</span>
                                            <span class="rifa-countdown-value rifa-countdown-timer"
                                                data-countdown="<?= $segundosRecaudacion ?>">
                                                <?= $segundosRecaudacion > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($segundosRecaudacion !== null): ?>
                                            <div class="rifa-countdown-row">
                                                <span class="rifa-countdown-label"><i class="fas fa-clock me-1"></i>Fin recaudación:</span>
                                                <span class="rifa-countdown-value rifa-countdown-timer"
                                                    data-countdown="<?= $segundosRecaudacion ?>">
                                                    <?= $segundosRecaudacion > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($segundosSorteo !== null): ?>
                                            <div class="rifa-countdown-row">
                                                <span class="rifa-countdown-label"><i class="fas fa-gift me-1"></i>Día del sorteo:</span>
                                                <span class="rifa-countdown-value rifa-countdown-timer"
                                                    data-countdown="<?= $segundosSorteo ?>">
                                                    <?= $segundosSorteo > 0 ? '--:--:--:--' : 'Finalizado' ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="rifa-image-container">
                                <?php if (!empty($rifa->img)): ?>
                                    <?= Html::img(Yii::getAlias('@web') . $rifa->img, ['alt' => $rifa->titulo]) ?>
                                <?php else: ?>
                                    <i class="fas fa-ticket-alt rifa-image-fallback"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Seccion: Rifas Pendientes de Ganadores -->
    <div class="row" id="rifas-pendientes-row" style="display: none;">
        <div class="col-12">
            <div class="admin-card" style="border-left: 4px solid #e67e22;">
                <h2 class="admin-card-title" style="color: #e67e22;">
                    <i class="fas fa-hourglass-end me-2"></i>
                    Rifas Pendientes de Ganadores
                </h2>
                <div id="rifas-pendientes-list">
                    <!-- Se pobla dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda Fila: Jugadores Pendientes y Gráfico -->
    <div class="row">
        <!-- Columna 1: Boletos Reservados (Pendientes de Pago) -->
        <div class="col-lg-6">
            <div class="admin-card">
                <h2 class="admin-card-title">
                    <i class="fas fa-clock me-2"></i>
                    Boletos Reservados (Pendientes de Pago)
                </h2>

                <?php if (empty($boletosReservados)): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">¡Enhorabuena! No hay boletos pendientes</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($boletosReservados as $boleto): ?>
                        <div class="boleto-item" data-reserved-until="<?= Html::encode($boleto->reserved_until) ?>">
                            <div class="boleto-info">
                                <div class="boleto-codigo">
                                    <i class="fas fa-ticket-alt me-1"></i> <?= Html::encode($boleto->codigo) ?>
                                    <span class="mx-2">|</span>
                                    <?= Html::a('Ver Detalles del Boleto', ['/panel/boletos-view', 'id' => $boleto->id], ['class' => 'boleto-link']) ?>
                                    <span class="mx-2">|</span>
                                    <?= Html::a('Ver Rifa Relacionada', ['/panel/rifas-view', 'id' => $boleto->id_rifa], ['class' => 'boleto-link']) ?>
                                </div>
                                <div class="boleto-player">
                                    <i class="fas fa-user me-1"></i>
                                    <?= $boleto->jugador ? Html::encode($boleto->jugador->nombre) : 'No especificado' ?>
                                </div>
                                <div class="boleto-countdown">
                                    <i class="fas fa-hourglass-half me-1"></i>
                                    Se reservará hasta: <span class="countdown-timer"
                                        data-expires="<?= Html::encode($boleto->reserved_until) ?>">--:--:--</span>
                                </div>
                            </div>
                            <div class="boleto-actions">
                                <button class="btn-action btn-delete" title="Cancelar reserva">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna 2: Gráfico de Proporción -->
        <div class="col-lg-6">
            <div class="admin-card">
                <h2 class="admin-card-title">
                    <i class="fas fa-chart-pie me-2"></i>
                    Proporción de Estados de Boletos
                </h2>

                <?php if ($totalReservados == 0 && $totalPagados == 0 && $totalAnulados == 0 && $totalReembolsados == 0): ?>
                    <div class="fallback-message">
                        <div class="fallback-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <p class="mb-0">No hay data, espera a tener boletos registrados...</p>
                    </div>
                <?php else: ?>
                    <div class="chart-container">
                        <canvas id="paymentChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($totalReservados > 0 || $totalPagados > 0 || $totalAnulados > 0 || $totalReembolsados > 0): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart de estados de boletos
            const ctx = document.getElementById('paymentChart');

            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Reservados', 'Pagados', 'Anulados', 'Reembolsados'],
                        datasets: [{
                            data: [<?= $totalReservados ?>, <?= $totalPagados ?>, <?= $totalAnulados ?>, <?= $totalReembolsados ?>],
                            backgroundColor: [
                                'rgba(231, 76, 60, 0.8)',   // Rojo para reservados
                                'rgba(46, 204, 113, 0.8)',  // Verde para pagados
                                'rgba(149, 165, 166, 0.8)', // Gris para anulados
                                'rgba(52, 152, 219, 0.8)'   // Azul para reembolsados
                            ],
                            borderColor: [
                                'rgba(231, 76, 60, 1)',
                                'rgba(46, 204, 113, 1)',
                                'rgba(149, 165, 166, 1)',
                                'rgba(52, 152, 219, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Contador de tiempo para boletos reservados
            function updateCountdowns() {
                const timers = document.querySelectorAll('.countdown-timer');

                timers.forEach(timer => {
                    const expiresAt = timer.getAttribute('data-expires');
                    if (!expiresAt) return;

                    const expireDate = new Date(expiresAt);
                    const now = new Date();
                    const diff = expireDate - now;

                    if (diff <= 0) {
                        timer.textContent = 'EXPIRADO';
                        timer.style.color = '#c0392b';
                        return;
                    }

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    timer.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                });
            }

            // Actualizar cada segundo
            updateCountdowns();
            setInterval(updateCountdowns, 1000);
        });
    </script>
<?php endif; ?>

<!-- Script para contadores de rifas -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rifaCountdownElements = document.querySelectorAll('.rifa-countdown-timer[data-countdown]');

        function formatRifaTime(totalSeconds) {
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

        function updateRifaCountdowns() {
            rifaCountdownElements.forEach(function (el) {
                let seconds = parseInt(el.getAttribute('data-countdown'), 10);

                if (seconds > 0) {
                    seconds--;
                    el.setAttribute('data-countdown', seconds);
                    el.textContent = formatRifaTime(seconds);
                } else {
                    el.textContent = 'Finalizado';
                    el.classList.add('rifa-countdown-expired');
                }
            });
        }

        // Inicializar valores
        rifaCountdownElements.forEach(function (el) {
            const seconds = parseInt(el.getAttribute('data-countdown'), 10);
            el.textContent = formatRifaTime(seconds);
        });

        // Actualizar cada segundo
        if (rifaCountdownElements.length > 0) {
            setInterval(updateRifaCountdowns, 1000);
        }

        // ==================== REAL-TIME RIFAS PENDIENTES ====================
        const apiRifasPendientesUrl = '<?= \yii\helpers\Url::to(['panel/api-rifas-pendientes']) ?>';

        function checkRifasPendientes() {
            fetch(apiRifasPendientesUrl)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) return;

                    const container = document.getElementById('rifas-pendientes-row');
                    const list = document.getElementById('rifas-pendientes-list');

                    if (data.rifas && data.rifas.length > 0) {
                        container.style.display = 'block';
                        let html = '';
                        data.rifas.forEach(rifa => {
                            html += `
                                <div class="rifa-item" style="border-left-color: #e67e22;">
                                    <a href="${rifa.viewUrl}" class="rifa-item-link"></a>
                                    <div class="rifa-content">
                                        <div class="rifa-title">
                                            ${rifa.titulo}
                                            <span class="rifa-badge" style="background: #e67e22;">PENDIENTE</span>
                                        </div>
                                        <div class="rifa-meta">
                                            <span><i class="fas fa-calendar me-1"></i>Sorteo: ${rifa.fecha_sorteo}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        list.innerHTML = html;
                    } else {
                        container.style.display = 'none';
                    }
                })
                .catch(err => console.error('Error checking rifas pendientes:', err));
        }

        // Check every 10 seconds
        checkRifasPendientes();
        setInterval(checkRifasPendientes, 10000);
    });
</script>