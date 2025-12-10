<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas $model */
/** @var app\models\Sorteos $sorteo */
/** @var app\models\Premios[] $premios */

use yii\bootstrap5\Html;

$this->title = 'Editar Rifa: ' . Html::encode($model->titulo);
?>

<?= $this->render('_form', [
    'model' => $model,
    'sorteo' => $sorteo,
    'premios' => $premios,
    'isUpdate' => true,
]) ?>
