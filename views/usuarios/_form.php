<?php

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */
/** @var app\models\Roles[] $roles */

use yii\helpers\Html;

?>

<style>
    .form-page {
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

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-label .required {
        color: #e74c3c;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
    }

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 12px center;
        background-size: 12px;
        appearance: none;
        cursor: pointer;
    }

    .form-select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
    }

    .form-hint {
        font-size: 0.8rem;
        color: #7f8c8d;
        margin-top: 0.35rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .actions-bar {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid;
        cursor: pointer;
    }

    .btn-guardar {
        background: #27ae60;
        color: white;
        border-color: #27ae60;
    }

    .btn-guardar:hover {
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

    @media (max-width: 576px) {
        .actions-bar {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="form-group">
    <label class="form-label">Nombre <span class="required">*</span></label>
    <input type="text" name="nombre" class="form-control" value="<?= Html::encode($model->nombre) ?>"
        placeholder="Nombre completo del usuario" required>
</div>

<div class="form-group">
    <label class="form-label">Correo Electrónico <span class="required">*</span></label>
    <input type="email" name="correo" class="form-control" value="<?= Html::encode($model->correo) ?>"
        placeholder="correo@ejemplo.com" required>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Contraseña <?= $model->isNewRecord ? '<span class="required">*</span>' : '' ?></label>
        <input type="password" name="password" class="form-control"
            placeholder="<?= $model->isNewRecord ? 'Contraseña' : 'Dejar vacío para mantener actual' ?>"
            <?= $model->isNewRecord ? 'required' : '' ?>>
        <?php if (!$model->isNewRecord): ?>
            <span class="form-hint">Dejar vacío para mantener la contraseña actual</span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label class="form-label">Rol <span class="required">*</span></label>
        <select name="rol_id" class="form-select" required>
            <option value="">Seleccione un rol</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol->id ?>" <?= $model->rol_id == $rol->id ? 'selected' : '' ?>>
                    <?= Html::encode($rol->nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>