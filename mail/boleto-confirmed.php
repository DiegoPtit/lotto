<?php
use yii\helpers\Html;
use yii\helpers\Url;

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
    <title>¡Tu Pago ha sido Confirmado!</title>
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
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 40px 30px; text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                ¡Pago Confirmado!
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255,255,255,0.9); font-size: 16px;">
                                Boleto #<?= Html::encode($boleto->codigo) ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #2d3748; line-height: 1.6;">
                                ¡Hola <strong><?= Html::encode($boleto->jugador->nombre) ?></strong>!
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 16px; color: #2d3748; line-height: 1.6;">
                                ¡Excelentes noticias! Tu pago ha sido <strong style="color: #10b981;">verificado y
                                    confirmado</strong>. Tu participación en la rifa
                                <strong><?= Html::encode($rifa->titulo) ?></strong> está oficialmente registrada.
                            </p>

                            <!-- Status Badge -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="display: inline-block; background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%); border: 2px solid #10b981; border-radius: 15px; padding: 20px 30px;">
                                            <div style="font-size: 32px; margin-bottom: 10px;">🎫</div>
                                            <div
                                                style="color: #065f46; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                                Estado: Confirmado
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Ticket Info -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background-color: #f7fafc; border-radius: 15px; padding: 20px; margin-bottom: 30px;">
                                <tr>
                                    <td>
                                        <h3
                                            style="margin: 0 0 15px 0; color: #10b981; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                            📋 Detalles del Boleto
                                        </h3>

                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Código:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #2d3748; font-size: 14px; font-weight: 600; padding: 10px 0;">
                                                    #<?= Html::encode($boleto->codigo) ?>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Rifa:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #2d3748; font-size: 14px; font-weight: 600; padding: 10px 0;">
                                                    <?= Html::encode($rifa->titulo) ?>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Cantidad de Números:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #2d3748; font-size: 14px; font-weight: 600; padding: 10px 0;">
                                                    <?= count($numeros) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #718096; font-size: 14px; padding: 10px 0;">
                                                    <strong>Total:</strong>
                                                </td>
                                                <td align="right"
                                                    style="color: #10b981; font-size: 18px; font-weight: 700; padding: 10px 0;">
                                                    Bs. <?= number_format($boleto->total_precio, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Numbers Grid -->
                            <h3
                                style="margin: 0 0 20px 0; color: #10b981; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                🎲 Tus Números de la Suerte
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
                                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 12px; border-radius: 10px; font-size: 16px; font-weight: 700; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
                                                <?= Html::encode($numero) ?>
                                            </div>
                                        </td>
                                        <?php
                                        $counter++;
                                    endforeach;

                                    // Fill remaining cells if last row is incomplete
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
                                style="background-color: #e6fffa; border-left: 4px solid #10b981; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 16px; font-weight: 700;">
                                    ✨ ¿Qué sigue ahora?
                                </h4>
                                <ul style="margin: 0; padding-left: 20px; color: #047857; line-height: 1.8;">
                                    <li>Tu boleto está <strong>confirmado</strong> y listo para participar.</li>
                                    <li>Te notificaremos por correo sobre los resultados del sorteo.</li>
                                    <li>Puedes consultar el estado de tu boleto en cualquier momento.</li>
                                    <li>Guarda este correo como comprobante de tu participación.</li>
                                </ul>
                            </div>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= $statusUrl ?>"
                                            style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                                            Ver Estado del Boleto
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Good Luck Message -->
                            <div
                                style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 20px; border-radius: 15px; text-align: center; margin-bottom: 20px;">
                                <p style="margin: 0; font-size: 20px; color: #78350f; font-weight: 700;">
                                    🍀 ¡Mucha suerte en el sorteo! 🍀
                                </p>
                            </div>

                            <!-- Support Info -->
                            <p
                                style="margin: 0; font-size: 14px; color: #718096; line-height: 1.6; text-align: center;">
                                Si tienes alguna pregunta, no dudes en contactarnos.<br>
                                ¡Gracias por participar en nuestra rifa!
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
                                Este correo fue enviado porque tu pago fue confirmado.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>