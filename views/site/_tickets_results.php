<?php
use yii\helpers\Html;
use app\models\Boletos;

/** @var array $boletosPorRifa */

if (empty($boletosPorRifa)) {
    echo '<div class="alert alert-danger text-center animate__animated animate__fadeIn">
            <i class="fas fa-exclamation-circle fa-2x mb-3"></i><br>
            El jugador no posee tickets jugados.
          </div>';
    return;
}
?>

<div class="tickets-results-list animate__animated animate__fadeIn">
    <br>
    <?php foreach ($boletosPorRifa as $rifaId => $data): ?>
        <?php
        $rifa = $data['rifa'];
        $boletos = $data['boletos'];
        ?>

        <div class="rifa-section mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="rifa-name m-0" style="color: var(--primary-dark);">
                    <i class="fas fa-ticket-alt me-2"></i>
                    <?= Html::encode($rifa->titulo) ?>
                </h4>
                <span class="badge bg-primary rounded-pill px-3"><?= ucfirst($rifa->estado) ?></span>
            </div>

            <?php foreach ($boletos as $boleto): ?>
                <?php
                $isReservado = ($boleto->estado === Boletos::ESTADO_RESERVADO);
                $statusClass = $isReservado ? 'status-reservado' : 'status-pagado';
                $numeros = $boleto->boletoNumeros;
                $count = 0;
                foreach ($numeros as $n)
                    if ($n->is_deleted == 0)
                        $count++;
                ?>

                <div class="boleto-macro-card <?= $statusClass ?>">
                    <div class="boleto-card-header" onclick="this.closest('.boleto-macro-card').classList.toggle('expanded')">
                        <div class="boleto-info-main">
                            <div class="boleto-icon-box">
                                <?php if ($isReservado): ?>
                                    <i class="fas fa-clock"></i>
                                <?php else: ?>
                                    <i class="fas fa-check"></i>
                                <?php endif; ?>
                            </div>
                            <div class="boleto-details">
                                <h5>Ticket #<?= Html::encode($boleto->codigo) ?></h5>
                                <div class="boleto-meta">
                                    <span><i class="far fa-calendar-alt me-1"></i>
                                        <?= date('d M, H:i', strtotime($boleto->created_at)) ?></span>
                                    <span class="boleto-meta-divider">•</span>
                                    <span class="<?= $isReservado ? 'text-warning' : 'text-success' ?> fw-bold">
                                        <?= $isReservado ? 'Pendiente' : 'Pagado' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="boleto-info-main">
                            <div class="boleto-summary-right">
                                <span
                                    class="boleto-price"><?= Yii::$app->formatter->asCurrency($boleto->total_precio, 'Bs. ') ?></span>
                                <span class="boleto-count-badge"><?= $count ?> Números</span>
                            </div>
                            <i class="fas fa-chevron-down expand-icon"></i>
                        </div>
                    </div>

                    <div class="boleto-card-body">
                        <div class="boleto-body-content">
                            <h6 class="text-muted mb-3 text-uppercase fw-bold" style="font-size: 0.8rem; letter-spacing: 1px;">
                                <i class="fas fa-list-ol me-2"></i>Números Jugados
                            </h6>
                            <div class="macro-grid">
                                <?php foreach ($numeros as $numero): ?>
                                    <?php if ($numero->is_deleted == 0): ?>
                                        <div class="macro-number animate__animated animate__zoomIn">
                                            <?= Html::encode($numero->numero) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <hr class="rifa-separator my-5" style="opacity: 0.1;">
        </div>
    <?php endforeach; ?>
</div>