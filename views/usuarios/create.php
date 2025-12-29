<?php

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */
/** @var app\models\Roles[] $roles */

use yii\helpers\Html;

$this->title = 'Crear Usuario';
?>

<div class="form-page">
    <h1 class="page-title">
        <i class="fas fa-user-plus me-2"></i>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="admin-card">
        <h2 class="admin-card-title">
            <i class="fas fa-info-circle me-2"></i>
            Información del Usuario
        </h2>

        <?= Html::beginForm() ?>
        <?= $this->render('_form', [
            'model' => $model,
            'roles' => $roles,
        ]) ?>

        <div class="actions-bar">
            <button type="submit" class="btn-action btn-guardar">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
            <?= Html::a('<i class="fas fa-times me-1"></i> Cancelar', ['index'], ['class' => 'btn-action btn-cancelar']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>