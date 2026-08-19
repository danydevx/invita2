<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirma tu cuenta</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); padding: 40px 40px 30px 40px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <div style="display: inline-block; background-color: rgba(255,255,255,0.2); border-radius: 50%; padding: 20px; margin-bottom: 20px;">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" alt="Email" width="48" height="48" style="display: block;">
                                        </div>
                                        <h1 style="color: #ffffff; font-size: 24px; font-weight: 600; margin: 0 0 10px 0;">
                                            ¡Hola, {{ $firstName }}!
                                        </h1>
                                        <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 0;">
                                            Confirma tu cuenta para comenzar
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                            Gracias por registrarte en <strong style="color: #4F46E5;">{{ $appName }}</strong>. Para activar tu cuenta y acceder a todos nuestros servicios, por favor confirma tu correo electrónico haciendo clic en el botón de abajo.
                                        </p>
                                    </td>
                                </tr>

                                <!-- CTA Button -->
                                <tr>
                                    <td align="center" style="padding: 20px 0 30px 0;">
                                        <a href="{{ $verificationUrl }}" style="display: inline-block; background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; padding: 16px 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);">
                                            Confirmar mi cuenta
                                        </a>
                                    </td>
                                </tr>

                                <!-- Alternative link -->
                                <tr>
                                    <td style="padding: 20px; background-color: #F9FAFB; border-radius: 8px; text-align: center;">
                                        <p style="color: #6B7280; font-size: 14px; margin: 0 0 10px 0;">
                                            Si el botón no funciona, copia y pega este enlace en tu navegador:
                                        </p>
                                        <a href="{{ $verificationUrl }}" style="color: #4F46E5; font-size: 12px; word-break: break-all; text-decoration: none;">
                                            {{ $verificationUrl }}
                                        </a>
                                    </td>
                                </tr>

                                <!-- Info boxes -->
                                <tr>
                                    <td style="padding-top: 30px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 15px; background-color: #EEF2FF; border-radius: 8px; border-left: 4px solid #4F46E5;">
                                                    <p style="color: #4338CA; font-size: 14px; margin: 0; line-height: 1.5;">
                                                        <strong>¿Por qué verificar tu correo?</strong><br>
                                                        La verificación protege tu cuenta y asegura que puedas recuperar tu contraseña si la olvidas.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F9FAFB; padding: 30px 40px; border-top: 1px solid #E5E7EB;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="color: #6B7280; font-size: 14px; margin: 0 0 10px 0;">
                                            Este enlace expira en <strong>60 minutos</strong>.
                                        </p>
                                        <p style="color: #9CA3AF; font-size: 12px; margin: 0 0 15px 0;">
                                            Si no creaste una cuenta en {{ $appName }}, puedes ignorar este mensaje.
                                        </p>
                                        <p style="color: #6B7280; font-size: 12px; margin: 0;">
                                            © {{ date('Y') }} {{ $appName }}. Todos los derechos reservados.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
