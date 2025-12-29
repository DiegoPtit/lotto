<?php

/** @var yii\web\View $this */
/** @var app\models\Politicas $model */

use yii\helpers\Html;

$this->title = $model->titulo;

// Colores por tipo
$tipoColores = [
    'PRIVACIDAD' => '#9b59b6',
    'RESPONSABILIDAD' => '#3498db',
    'DESARROLLADOR' => '#1abc9c',
    'LEGAL' => '#e74c3c',
    'OTRO' => '#95a5a6',
];
$tipoColor = $tipoColores[$model->tipo] ?? '#95a5a6';
?>

<style>
    .politica-view-page {
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
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .tipo-badge {
        display: inline-block;
        padding: 0.35rem 0.875rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
    }

    .politica-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .politica-contenido {
        line-height: 1.8;
        color: #2c3e50;
    }

    .politica-contenido h1,
    .politica-contenido h2,
    .politica-contenido h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #34495e;
    }

    .politica-contenido p {
        margin-bottom: 1rem;
    }

    .politica-contenido ul,
    .politica-contenido ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
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

<div class="politica-view-page">
    <h1 class="page-title">
        <i class="fas fa-file-contract me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="admin-card">
        <h2 class="admin-card-title">
            <span class="tipo-badge" style="background: <?= $tipoColor ?>;">
                <?= Html::encode($model->tipo) ?>
            </span>
            Contenido de la Política
        </h2>

        <div class="politica-meta">
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                Creado: <?= Yii::$app->formatter->asDatetime($model->created_at, 'medium') ?>
            </div>
            <?php if ($model->updated_at): ?>
                <div class="meta-item">
                    <i class="fas fa-edit"></i>
                    Actualizado: <?= Yii::$app->formatter->asDatetime($model->updated_at, 'medium') ?>
                </div>
            <?php endif; ?>
            <?php if ($model->operadorRegistro): ?>
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    Por: <?= Html::encode($model->operadorRegistro->nombre) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="politica-contenido">
            <?= $model->descripcion ?>
        </div>

        <div class="actions-bar">
            <?= Html::a('<i class="fas fa-edit me-1"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn-action btn-editar']) ?>
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['index'], ['class' => 'btn-action btn-volver']) ?>
            <?= Html::a('<i class="fas fa-trash me-1"></i> Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn-action btn-eliminar',
                'data' => [
                    'confirm' => '¿Estás seguro de eliminar esta política?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
</div>