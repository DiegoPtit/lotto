<?php

/** @var yii\web\View $this */
/** @var app\models\Rifas $model */
/** @var app\models\Sorteos $sorteo */
/** @var app\models\Premios[] $premios */

use yii\bootstrap5\Html;

$this->title = 'Crear Nueva Rifa';
?>

<h1 class="text-center"><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', [
    'model' => $model,
    'sorteo' => $sorteo,
    'premios' => $premios,
    'isUpdate' => false,
]) ?>
