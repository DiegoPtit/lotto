<?php

/** @var yii\web\View $this */
/** @var string $type */

use yii\helpers\Html;

$titles = [
    'terms' => 'Términos y Condiciones',
    'privacy' => 'Política de Privacidad',
    'cookies' => 'Política de Cookies',
];

$this->title = $titles[$type] ?? 'Políticas';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-politics">
    <div class="politics-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="politics-updated">Última actualización: <?= date('d/m/Y') ?></p>
    </div>

    <div class="politics-content">
        <?php if ($type === 'terms'): ?>
            <!-- Términos y Condiciones -->
            <section class="politics-section">
                <h2>1. Aceptación de los Términos</h2>
                <p>
                    Al acceder y utilizar <?= Html::encode(Yii::$app->name) ?>, usted acepta cumplir con estos 
                    términos y condiciones. Si no está de acuerdo con alguna parte de estos términos, no debe 
                    utilizar nuestros servicios.
                </p>
            </section>

            <section class="politics-section">
                <h2>2. Descripción del Servicio</h2>
                <p>
                    <?= Html::encode(Yii::$app->name) ?> es una plataforma de sorteos y rifas en línea que permite 
                    a los usuarios participar en diversos concursos de manera segura y transparente. Nos reservamos 
                    el derecho de modificar, suspender o descontinuar cualquier aspecto del servicio en cualquier momento.
                </p>
            </section>

            <section class="politics-section">
                <h2>3. Elegibilidad y Registro</h2>
                <p>
                    Para participar en nuestros sorteos, debes ser mayor de edad según las leyes de tu jurisdicción. 
                    Al registrarte, garantizas que toda la información proporcionada es verdadera, precisa y completa.
                </p>
            </section>

            <section class="politics-section">
                <h2>4. Participación en Sorteos</h2>
                <p>
                    Cada sorteo tiene sus propias reglas específicas que deben ser consultadas antes de participar. 
                    La participación está sujeta a disponibilidad y puede estar limitada geográficamente. Los resultados 
                    de todos los sorteos son finales y vinculantes.
                </p>
            </section>

            <section class="politics-section">
                <h2>5. Premios</h2>
                <p>
                    Los premios se entregarán según las condiciones específicas de cada sorteo. Los ganadores serán 
                    notificados a través de los medios de contacto proporcionados durante el registro. Los premios 
                    no son transferibles ni canjeables por dinero en efectivo, salvo que se especifique lo contrario.
                </p>
            </section>

            <section class="politics-section">
                <h2>6. Limitación de Responsabilidad</h2>
                <p>
                    <?= Html::encode(Yii::$app->name) ?> no será responsable por daños indirectos, incidentales o 
                    consecuentes que surjan del uso de nuestros servicios. Nos esforzamos por mantener la plataforma 
                    operativa, pero no garantizamos que esté libre de interrupciones o errores.
                </p>
            </section>

        <?php elseif ($type === 'privacy'): ?>
            <!-- Política de Privacidad -->
            <section class="politics-section">
                <h2>1. Información que Recopilamos</h2>
                <p>
                    Recopilamos información personal cuando te registras en <?= Html::encode(Yii::$app->name) ?>, 
                    incluyendo tu nombre, correo electrónico, y otros datos necesarios para la participación en sorteos. 
                    También podemos recopilar información sobre tu uso de la plataforma para mejorar nuestros servicios.
                </p>
            </section>

            <section class="politics-section">
                <h2>2. Uso de la Información</h2>
                <p>
                    Utilizamos tu información personal para:
                </p>
                <ul>
                    <li>Gestionar tu cuenta y participación en sorteos</li>
                    <li>Comunicarnos contigo sobre resultados y actualizaciones</li>
                    <li>Mejorar nuestros servicios y experiencia de usuario</li>
                    <li>Cumplir con obligaciones legales</li>
                </ul>
            </section>

            <section class="politics-section">
                <h2>3. Compartir Información</h2>
                <p>
                    No vendemos ni alquilamos tu información personal a terceros. Podemos compartir información con 
                    proveedores de servicios que nos ayudan a operar la plataforma, siempre bajo estrictos acuerdos 
                    de confidencialidad.
                </p>
            </section>

            <section class="politics-section">
                <h2>4. Seguridad de Datos</h2>
                <p>
                    Implementamos medidas de seguridad técnicas y organizativas para proteger tu información personal 
                    contra acceso no autorizado, alteración, divulgación o destrucción. Sin embargo, ningún método de 
                    transmisión por Internet es 100% seguro.
                </p>
            </section>

            <section class="politics-section">
                <h2>5. Tus Derechos</h2>
                <p>
                    Tienes derecho a acceder, corregir, actualizar o eliminar tu información personal en cualquier momento. 
                    Puedes ejercer estos derechos contactándonos a través de los canales proporcionados en nuestra plataforma.
                </p>
            </section>

            <section class="politics-section">
                <h2>6. Cambios a esta Política</h2>
                <p>
                    Nos reservamos el derecho de actualizar esta política de privacidad periódicamente. Te notificaremos 
                    sobre cambios significativos y la fecha de la última actualización siempre será visible en esta página.
                </p>
            </section>

        <?php elseif ($type === 'cookies'): ?>
            <!-- Política de Cookies -->
            <section class="politics-section">
                <h2>1. ¿Qué son las Cookies?</h2>
                <p>
                    Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo cuando visitas 
                    <?= Html::encode(Yii::$app->name) ?>. Estas cookies nos ayudan a mejorar tu experiencia en 
                    nuestra plataforma y a proporcionar funcionalidades esenciales.
                </p>
            </section>

            <section class="politics-section">
                <h2>2. Tipos de Cookies que Utilizamos</h2>
                <h3>Cookies Esenciales</h3>
                <p>
                    Estas cookies son necesarias para el funcionamiento básico del sitio web, incluyendo la gestión 
                    de sesiones y la autenticación de usuarios. No se pueden desactivar.
                </p>
                
                <h3>Cookies de Rendimiento</h3>
                <p>
                    Nos ayudan a entender cómo los visitantes interactúan con nuestro sitio, recopilando información 
                    anónima sobre las páginas visitadas y los errores encontrados.
                </p>
                
                <h3>Cookies de Funcionalidad</h3>
                <p>
                    Permiten que el sitio recuerde tus preferencias (como idioma o región) para proporcionar una 
                    experiencia más personalizada.
                </p>
            </section>

            <section class="politics-section">
                <h2>3. Cookies de Terceros</h2>
                <p>
                    Podemos utilizar cookies de terceros para análisis y publicidad. Estos terceros tienen sus propias 
                    políticas de privacidad que debes revisar.
                </p>
            </section>

            <section class="politics-section">
                <h2>4. Gestión de Cookies</h2>
                <p>
                    Puedes configurar tu navegador para que rechace todas las cookies o para que te notifique cuando 
                    se envía una cookie. Sin embargo, si desactivas las cookies, algunas funciones del sitio pueden 
                    no funcionar correctamente.
                </p>
            </section>

            <section class="politics-section">
                <h2>5. Control de Cookies</h2>
                <p>
                    La mayoría de los navegadores web permiten controlar las cookies a través de la configuración. 
                    Para obtener más información sobre cómo administrar las cookies en los navegadores más populares, 
                    consulta la sección de ayuda de tu navegador.
                </p>
            </section>

            <section class="politics-section">
                <h2>6. Actualizaciones</h2>
                <p>
                    Podemos actualizar esta política de cookies de vez en cuando para reflejar cambios en nuestras 
                    prácticas o por razones operativas, legales o regulatorias.
                </p>
            </section>

        <?php endif; ?>

        <section class="politics-section politics-contact">
            <h2>Contacto</h2>
            <p>
                Si tienes alguna pregunta sobre esta política, puedes contactarnos a través de nuestra 
                <?= Html::a('página de contacto', ['/site/contact']) ?>.
            </p>
        </section>
    </div>
</div>

<style>
.site-politics {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.politics-header {
    text-align: center;
    margin-bottom: 48px;
    padding-bottom: 24px;
    border-bottom: 3px solid #6366f1;
}

.politics-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 12px;
}

.politics-updated {
    color: #6b7280;
    font-size: 0.95rem;
    font-style: italic;
}

.politics-content {
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.politics-section {
    margin-bottom: 40px;
}

.politics-section:last-child {
    margin-bottom: 0;
}

.politics-section h2 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
}

.politics-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #4b5563;
    margin-top: 20px;
    margin-bottom: 12px;
}

.politics-section p {
    color: #4b5563;
    line-height: 1.8;
    margin-bottom: 16px;
}

.politics-section ul {
    color: #4b5563;
    line-height: 1.8;
    margin-left: 24px;
    margin-bottom: 16px;
}

.politics-section ul li {
    margin-bottom: 8px;
}

.politics-contact {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 24px;
    border-radius: 8px;
    border-left: 4px solid #6366f1;
}

.politics-contact h2 {
    color: #1f2937;
    font-size: 1.5rem;
}

.politics-contact p {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .politics-header h1 {
        font-size: 2rem;
    }

    .politics-content {
        padding: 24px;
    }

    .politics-section h2 {
        font-size: 1.5rem;
    }
}
</style>
