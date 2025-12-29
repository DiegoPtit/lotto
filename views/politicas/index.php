<?php

/** @var yii\web\View $this */
/** @var app\models\Politicas[] $politicas */
/** @var array $politicasPorTipo */
/** @var string|null $tipoFiltro */
/** @var array $tipos */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administrar Políticas';

// Colores por tipo
$tipoColores = [
    'PRIVACIDAD' => '#9b59b6',
    'RESPONSABILIDAD' => '#3498db',
    'DESARROLLADOR' => '#1abc9c',
    'LEGAL' => '#e74c3c',
    'OTRO' => '#95a5a6',
];
?>

<style>
    .politicas-page {
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

    .admin-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #34495e;
        margin: 0;
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
        appearance: none;
        min-width: 160px;
    }

    .btn-crear {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-crear:hover {
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
        color: white;
    }

    .tipo-section {
        margin-bottom: 2rem;
    }

    .tipo-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .tipo-badge {
        display: inline-block;
        padding: 0.35rem 0.875rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
    }

    .tipo-count {
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    .politica-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
        border-left: 4px solid #e0e0e0;
    }

    .politica-item:hover {
        background: #e9ecef;
    }

    .politica-info {
        flex: 1;
    }

    .politica-titulo {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .politica-fecha {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .acciones-cell {
        display: flex;
        gap: 0.5rem;
    }

    .btn-accion {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-ver {
        background: #3498db;
        color: white;
    }

    .btn-ver:hover {
        background: #2980b9;
        color: white;
    }

    .btn-editar {
        background: #f39c12;
        color: white;
    }

    .btn-editar:hover {
        background: #d68910;
        color: white;
    }

    .btn-eliminar {
        background: #e74c3c;
        color: white;
    }

    .btn-eliminar:hover {
        background: #c0392b;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .politica-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .acciones-cell {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="politicas-page">
    <h1 class="page-title">
        <i class="fas fa-file-contract me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <i class="fas fa-list me-2"></i>
                Listado de Políticas
            </h2>
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <select class="filter-select"
                    onchange="window.location.href='<?= Url::to(['politicas/index']) ?>&tipo=' + this.value">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $tipoFiltro === $key ? 'selected' : '' ?>>
                            <?= Html::encode($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= Html::a('<i class="fas fa-plus me-1"></i> Nueva Política', ['create'], ['class' => 'btn-crear']) ?>
            </div>
        </div>

        <?php if (empty($politicas)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <p class="mb-0">No hay políticas registradas</p>
            </div>
        <?php else: ?>
            <?php foreach ($politicasPorTipo as $tipo => $politicasTipo): ?>
                <div class="tipo-section">
                    <div class="tipo-header">
                        <span class="tipo-badge" style="background: <?= $tipoColores[$tipo] ?? '#95a5a6' ?>;">
                            <?= Html::encode($tipo) ?>
                        </span>
                        <span class="tipo-count"><?= count($politicasTipo) ?> política(s)</span>
                    </div>

                    <?php foreach ($politicasTipo as $politica): ?>
                        <div class="politica-item" style="border-left-color: <?= $tipoColores[$tipo] ?? '#95a5a6' ?>;">
                            <div class="politica-info">
                                <div class="politica-titulo"><?= Html::encode($politica->titulo) ?></div>
                                <div class="politica-fecha">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= Yii::$app->formatter->asDate($politica->created_at, 'medium') ?>
                                </div>
                            </div>
                            <div class="acciones-cell">
                                <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $politica->id], [
                                    'class' => 'btn-accion btn-ver',
                                    'title' => 'Ver'
                                ]) ?>
                                <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $politica->id], [
                                    'class' => 'btn-accion btn-editar',
                                    'title' => 'Editar'
                                ]) ?>
                                <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $politica->id], [
                                    'class' => 'btn-accion btn-eliminar',
                                    'title' => 'Eliminar',
                                    'data' => [
                                        'confirm' => '¿Estás seguro de eliminar esta política?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>