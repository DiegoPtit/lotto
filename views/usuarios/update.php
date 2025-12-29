<?php

/** @var yii\web\View $this */
/** @var app\models\Usuarios $model */
/** @var app\models\Roles[] $roles */

use yii\helpers\Html;

$this->title = 'Editar Usuario: ' . Html::encode($model->nombre);
?>

<div class="form-page">
    <h1 class="page-title">
        <i class="fas fa-user-edit me-2"></i>
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
                <i class="fas fa-save me-1"></i> Guardar Cambios
            </button>
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Volver', ['view', 'id' => $model->id], ['class' => 'btn-action btn-cancelar']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>