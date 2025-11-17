# Configuración de Correo para Recuperación de Contraseña

## Problema Actual
El sistema está configurado con `MAIL_MAILER=log`, lo que significa que los emails se guardan en los logs en lugar de enviarse realmente.

## Soluciones

### Opción 1: Usar Mailtrap (Recomendado para Desarrollo)
1. Crea una cuenta en https://mailtrap.io (gratis)
2. Obtén las credenciales SMTP de tu inbox
3. Agrega estas variables a tu archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@agrosac.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Opción 2: Usar Gmail (Para Producción)
1. Habilita "Contraseñas de aplicaciones" en tu cuenta de Google
2. Genera una contraseña de aplicación
3. Agrega estas variables a tu archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Opción 3: Usar Sendmail (Si tienes servidor de correo configurado)
```env
MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS=noreply@agrosac.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Después de Configurar
1. Ejecuta: `php artisan config:clear`
2. Prueba enviando un correo de recuperación de contraseña
3. Verifica que el correo llegue a tu bandeja de entrada (o Mailtrap)






