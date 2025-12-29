<?php

/** @var yii\web\View $this */
/** @var app\models\Usuarios[] $usuarios */
/** @var app\models\Roles[] $roles */
/** @var int|null $rolFiltro */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administrar Usuarios';
?>

<style>
    .usuarios-page {
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
        min-width: 140px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #3498db;
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
        transform: translateY(-1px);
    }

    .usuarios-table {
        width: 100%;
        border-collapse: collapse;
    }

    .usuarios-table th {
        background: #f8f9fa;
        padding: 0.875rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #34495e;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e8e8e8;
    }

    .usuarios-table td {
        padding: 1rem;
        border-bottom: 1px solid #e8e8e8;
        vertical-align: middle;
    }

    .usuarios-table tr:hover {
        background: #f8f9fa;
    }

    .usuario-nombre {
        font-weight: 600;
        color: #2c3e50;
    }

    .usuario-correo {
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .rol-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
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

    /* Responsive */
    @media (max-width: 768px) {
        .usuarios-table {
            display: block;
        }

        .usuarios-table thead {
            display: none;
        }

        .usuarios-table tbody,
        .usuarios-table tr,
        .usuarios-table td {
            display: block;
            width: 100%;
        }

        .usuarios-table tr {
            background: #f8f9fa;
            margin-bottom: 1rem;
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #e8e8e8;
        }

        .usuarios-table td {
            padding: 0.5rem 0;
            border: none;
            text-align: left;
        }

        .usuarios-table td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.75rem;
            color: #7f8c8d;
            text-transform: uppercase;
            display: block;
            margin-bottom: 0.25rem;
        }

        .acciones-cell {
            justify-content: flex-start;
            margin-top: 0.5rem;
        }
    }
</style>

<div class="usuarios-page">
    <h1 class="page-title">
        <i class="fas fa-users me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <i class="fas fa-list me-2"></i>
                Listado de Usuarios
            </h2>
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <select class="filter-select"
                    onchange="window.location.href='<?= Url::to(['usuarios/index']) ?>&rol=' + this.value">
                    <option value="">Todos los roles</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol->id ?>" <?= $rolFiltro == $rol->id ? 'selected' : '' ?>>
                            <?= Html::encode($rol->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= Html::a('<i class="fas fa-plus me-1"></i> Nuevo Usuario', ['create'], ['class' => 'btn-crear']) ?>
            </div>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <p class="mb-0">No hay usuarios registrados</p>
            </div>
        <?php else: ?>
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td data-label="Usuario">
                                <div class="usuario-nombre"><?= Html::encode($usuario->nombre) ?></div>
                                <div class="usuario-correo"><?= Html::encode($usuario->correo) ?></div>
                            </td>
                            <td data-label="Rol">
                                <?php
                                $rolClass = 'rol-default';
                                $rolNombre = $usuario->rol ? $usuario->rol->nombre : 'Sin rol';
                                if (stripos($rolNombre, 'admin') !== false) {
                                    $rolClass = 'rol-admin';
                                } elseif (stripos($rolNombre, 'operador') !== false) {
                                    $rolClass = 'rol-operador';
                                }
                                ?>
                                <span class="rol-badge <?= $rolClass ?>">
                                    <?= Html::encode($rolNombre) ?>
                                </span>
                            </td>
                            <td data-label="Fecha Registro">
                                <?= Yii::$app->formatter->asDate($usuario->created_at, 'medium') ?>
                            </td>
                            <td data-label="Acciones">
                                <div class="acciones-cell">
                                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $usuario->id], [
                                        'class' => 'btn-accion btn-ver',
                                        'title' => 'Ver'
                                    ]) ?>
                                    <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $usuario->id], [
                                        'class' => 'btn-accion btn-editar',
                                        'title' => 'Editar'
                                    ]) ?>
                                    <?php if ($usuario->id != Yii::$app->user->id): ?>
                                        <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $usuario->id], [
                                            'class' => 'btn-accion btn-eliminar',
                                            'title' => 'Eliminar',
                                            'data' => [
                                                'confirm' => '¿Estás seguro de eliminar este usuario?',
                                                'method' => 'post',
                                            ],
                                        ]) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>