<?php

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */

use yii\helpers\Html;

$this->title = 'Usuario: ' . Html::encode($model->nombre);
?>

<style>
    .usuario-view-page {
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
        border: 1px solid #e8e8e8;
    }

    .admin-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 1rem;
        color: #2c3e50;
        font-weight: 500;
    }

    .rol-badge {
        display: inline-block;
        padding: 0.35rem 0.875rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .rol-admin {
        background: #e74c3c;
        color: white;
    }

    .rol-operador {
        background: #3498db;
        color: white;
    }

    .rol-default {
        background: #95a5a6;
        color: white;
    }

    .actions-bar {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.625rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid;
    }

    .btn-editar {
        background: #f39c12;
        color: white;
        border-color: #f39c12;
    }

    .btn-editar:hover {
        background: #d68910;
        border-color: #d68910;
        color: white;
    }

    .btn-volver {
        background: white;
        color: #7f8c8d;
        border-color: #ddd;
    }

    .btn-volver:hover {
        background: #f8f9fa;
        color: #5a6c7d;
    }

    .btn-eliminar {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
    }

    .btn-eliminar:hover {
        background: #c0392b;
        border-color: #c0392b;
        color: white;
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .actions-bar {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="usuario-view-page">
    <h1 class="page-title">
        <i class="fas fa-user me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="admin-card">
        <h2 class="admin-card-title">
            <i class="fas fa-info-circle me-2"></i>
            Información del Usuario
        </h2>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Nombre</span>
                <span class="detail-value"><?= Html::encode($model->nombre) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Correo Electrónico</span>
                <span class="detail-value"><?= Html::encode($model->correo) ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Rol</span>
                <span class="detail-value">
                    <?php
                    $rolClass = 'rol-default';
                    $rolNombre = $model->rol ? $model->rol->nombre : 'Sin rol';
                    if (stripos($rolNombre, 'admin') !== false) {
                        $rolClass = 'rol-admin';
                    } elseif (stripos($rolNombre, 'operador') !== false) {
                        $rolClass = 'rol-operador';
                    }
                    ?>
                    <span class="rol-badge <?= $rolClass ?>">
                        <?= Html::encode($rolNombre) ?>
                    </span>
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Fecha de Registro</span>
                <span class="detail-value"><?= Yii::$app->formatter->asDatetime($model->created_at, 'medium') ?></span>
            </div>
            <?php if ($model->updated_at): ?>
                <div class="detail-item">
                    <span class="detail-label">Última Actualización</span>
                    <span class="detail-value"><?= Yii::$app->formatter->asDatetime($model->updated_at, 'medium') ?></span>
                </div>
            <?php endif; ?>
            <?php if ($model->google_id): ?>
                <div class="detail-item">
                    <span class="detail-label">Cuenta Google</span>
                    <span class="detail-value"><i class="fab fa-google me-1"></i> Vinculada</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="actions-bar">
            <?= Html::a('<i class="fas fa-edit me-1"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn-action btn-editar']) ?>
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['index'], ['class' => 'btn-action btn-volver']) ?>
            <?php if ($model->id != Yii::$app->user->id): ?>
                <?= Html::a('<i class="fas fa-trash me-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn-action btn-eliminar',
                    'data' => [
                        'confirm' => '¿Estás seguro de eliminar este usuario?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estadísticas del Usuario -->
    <div class="row">
        <div class="col-md-6">
            <div class="admin-card">
                <h2 class="admin-card-title">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Rifas Creadas
                </h2>
                <?php
                $rifasCount = $model->getRifas()->count();
                ?>
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: #3498db;"><?= $rifasCount ?></div>
                    <div style="color: #7f8c8d;">rifas registradas</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-card">
                <h2 class="admin-card-title">
                    <i class="fas fa-credit-card me-2"></i>
                    Métodos de Pago
                </h2>
                <?php
                $metodosCount = $model->getMetodosPagos()->count();
                ?>
                <div style="text-align: center; padding: 1rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: #27ae60;"><?= $metodosCount ?></div>
                    <div style="color: #7f8c8d;">métodos registrados</div>
                </div>
            </div>
        </div>
    </div>
</div>