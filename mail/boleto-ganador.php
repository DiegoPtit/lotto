<?php
use yii\helpers\Html;

/** @var app\models\Boletos $boleto */
/** @var app\models\Rifas $rifa */
/** @var array $numeros */
/** @var string $statusUrl */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Felicitaciones - Eres Ganador!</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); padding: 40px 30px; text-align: center;">
                            <div style="font-size: 64px; margin-bottom: 15px; animation: pulse 2s infinite;">🏆</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700;">
                                ¡FELICITACIONES!
                            </h1>
                            <h2 style="margin: 10px 0 0 0; color: rgba(255,255,255,0.95); font-size: 20px;">
                                ¡ERES GANADOR!
                            </h2>
                            <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">
                                Boleto #<?= Html::encode($boleto->codigo) ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; font-size: 18px; color: #2d3748; line-height: 1.6;">
                                ¡Hola <strong><?= Html::encode($boleto->jugador->nombre) ?></strong>!
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 16px; color: #2d3748; line-height: 1.6;">
                                ¡Tenemos noticias <strong style="color: #f59e0b;">INCREÍBLES</strong>! Tu boleto ha sido
                                seleccionado como <strong style="color: #f59e0b;">GANADOR</strong> en la rifa
                                <strong><?= Html::encode($rifa->titulo) ?></strong>.
                            </p>

                            <!-- Winner Badge -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="display: inline-block; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 3px solid #f59e0b; border-radius: 15px; padding: 25px 40px;">
                                            <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
                                            <div
                                                style="color: #78350f; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">
                                                BOLETO GANADOR
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Numbers -->
                            <h3
                                style="margin: 0 0 20px 0; color: #f59e0b; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                🎲 Tus Números Ganadores
                            </h3>

                            <table width="100%" cellpadding="4" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <?php
                                    $counter = 0;
                                    foreach ($numeros as $numero):
                                        if ($counter > 0 && $counter % 5 == 0): ?>
                                        </tr>
                                        <tr>
                                        <?php endif; ?>
                                        <td align="center" style="padding: 5px;">
                                            <div
                                                style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; padding: 12px; border-radius: 10px; font-size: 16px; font-weight: 700; box-shadow: 0 4px 10px rgba(251, 191, 36, 0.4);">
                                                <?= Html::encode($numero) ?>
                                            </div>
                                        </td>
                                        <?php
                                        $counter++;
                                    endforeach;

                                    $remaining = 5 - ($counter % 5);
                                    if ($remaining < 5 && $remaining > 0):
                                        for ($i = 0; $i < $remaining; $i++): ?>
                                            <td></td>
                                        <?php endfor;
                                    endif;
                                    ?>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <div
                                style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                <h4 style="margin: 0 0 10px 0; color: #78350f; font-size: 16px; font-weight: 700;">
                                    📞 ¿Qué sigue ahora?
                                </h4>
                                <ul style="margin: 0; padding-left: 20px; color: #92400e; line-height: 1.8;">
                                    <li>Nos pondremos en contacto contigo <strong>muy pronto</strong> para coordinar la
                                        entrega de tu premio.</li>
                                    <li>Mantén tu teléfono disponible durante los próximos días.</li>
                                    <li>Verifica que tus datos de contacto sean correctos.</li>
                                    <li>¡Prepárate para reclamar tu premio!</li>
                                </ul>
                            </div>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= $statusUrl ?>"
                                            style="display: inline-block; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);">
                                            Ver Mi Boleto Ganador
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Celebration -->
                            <div
                                style="background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%); padding: 25px; border-radius: 15px; text-align: center; margin-bottom: 20px;">
                                <p style="margin: 0; font-size: 24px; font-weight: 700; color: #065f46;">
                                    🎊 ¡DISFRUTA TU PREMIO! 🎊
                                </p>
                                <p style="margin: 10px 0 0 0; font-size: 14px; color: #047857;">
                                    Gracias por participar en nuestra rifa
                                </p>
                            </div>

                            <!-- Support -->
                            <p
                                style="margin: 0; font-size: 14px; color: #718096; line-height: 1.6; text-align: center;">
                                Si tienes alguna pregunta, contáctanos inmediatamente.<br>
                                Estamos aquí para ayudarte con tu premio.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f7fafc; padding: 30px; text-align: center; border-top: 2px solid #e2e8f0;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #718096;">
                                © <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. Todos los derechos reservados.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #a0aec0;">
                                Este correo fue enviado porque tu boleto resultó ganador.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>