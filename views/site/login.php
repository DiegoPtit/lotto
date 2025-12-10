<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */
/** @var app\models\SignupForm $signupModel */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Iniciar Sesión';
$this->context->layout = 'login-main';

// Ensure signupModel is available
if (!isset($signupModel)) {
    $signupModel = new \app\models\SignupForm();
}
?>
<div class="site-login d-flex align-items-center justify-content-center min-vh-100 w-100">
    <div class="card shadow-lg border-0 overflow-hidden" style="max-width: 450px; width: 100%; min-height: 600px;">

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success m-3 mb-0">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <div class="d-flex" id="forms-wrapper"
            style="width: 200%; transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);">

            <!-- Login Form -->
            <div class="w-50 p-5">
                <div class="text-center mb-4">
                    <div class="display-4 text-primary mb-2">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h2 class="h4 text-gray-900 mb-4"><?= Html::encode(Yii::$app->name) ?> Acceso</h2>
                    <p class="text-muted small">Por favor ingresa tus credenciales administrativas.</p>
                </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'user'],
                    'fieldConfig' => [
                        'template' => "<div class=\"form-floating mb-3\">\n{input}\n{label}\n{error}</div>",
                        'inputOptions' => ['class' => 'form-control form-control-user', 'placeholder' => 'name@example.com'],
                        'labelOptions' => ['class' => 'text-muted'],
                    ],
                ]); ?>

                <?= $form->field($model, 'correo')->textInput(['autofocus' => true, 'placeholder' => 'Correo Electrónico'])->label('Correo Electrónico') ?>

                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Contraseña'])->label('Contraseña') ?>

                <div class="mb-3">
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"custom-control custom-checkbox small\">{input} {label}</div>\n<div class=\"invalid-feedback\">{error}</div>",
                    ])->label('Recordarme') ?>
                </div>

                <div class="d-grid gap-2">
                    <?= Html::submitButton('Iniciar Sesión', ['class' => 'btn btn-primary btn-user btn-block py-2', 'name' => 'login-button']) ?>
                </div>

                <hr>

                <div class="text-center">
                    <p class="small text-muted mb-0">¿Necesitas acceso administrativo?</p>
                    <a class="small font-weight-bold" href="#" onclick="showRegister(event)">Regístrate</a>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

            <!-- Register Form -->
            <div class="w-50 p-5 bg-light">
                <div class="text-center mb-4">
                    <div class="display-4 text-success mb-2">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h2 class="h4 text-gray-900 mb-4">Registro Admin</h2>
                    <p class="text-muted small">Crea una cuenta administrativa temporal.</p>
                </div>

                <?php $formSignup = ActiveForm::begin([
                    'id' => 'signup-form',
                    'action' => ['site/signup'],
                    'options' => ['class' => 'user'],
                    'fieldConfig' => [
                        'template' => "<div class=\"form-floating mb-3\">\n{input}\n{label}\n{error}</div>",
                        'inputOptions' => ['class' => 'form-control form-control-user'],
                        'labelOptions' => ['class' => 'text-muted'],
                    ],
                ]); ?>

                <?= $formSignup->field($signupModel, 'correo')->textInput(['placeholder' => 'Correo Electrónico'])->label('Correo Electrónico') ?>

                <div class="position-relative">
                    <?= $formSignup->field($signupModel, 'password')->passwordInput(['placeholder' => 'Contraseña', 'id' => 'signup-password'])->label('Contraseña') ?>
                    <button type="button"
                        class="btn btn-link position-absolute top-0 end-0 mt-2 me-2 text-decoration-none text-muted"
                        onclick="togglePasswordVisibility()" style="z-index: 5;">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <?= Html::submitButton('Registrarse', ['class' => 'btn btn-success btn-user btn-block py-2', 'name' => 'signup-button']) ?>
                </div>

                <hr>

                <div class="text-center">
                    <p class="small text-muted mb-0">¿Ya tienes cuenta?</p>
                    <a class="small font-weight-bold" href="#" onclick="showLogin(event)">Inicia Sesión</a>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>
</div>

<script>
    function showRegister(e) {
        e.preventDefault();
        document.getElementById('forms-wrapper').style.transform = 'translateX(-50%)';
    }

    function showLogin(e) {
        e.preventDefault();
        document.getElementById('forms-wrapper').style.transform = 'translateX(0)';
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('signup-password');
        const icon = document.getElementById('togglePasswordIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Check if there are errors in signup model to show the form automatically
    <?php if ($signupModel->hasErrors()): ?>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('forms-wrapper').style.transform = 'translateX(-50%)';
        });
    <?php endif; ?>
</script>