# Configuración de Correo para Recuperación de Contraseña

## Problema Actual
El error indica que las credenciales de Gmail son inválidas. Esto sucede porque Gmail requiere una **Contraseña de Aplicación** en lugar de tu contraseña normal.

## Soluciones

### Opción 1: Usar Driver "log" para Desarrollo (RECOMENDADO)
Esta opción guarda los emails en los logs en lugar de enviarlos realmente. Es perfecta para desarrollo.

**En tu archivo `.env`, agrega o modifica:**
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@agrosac.com
MAIL_FROM_NAME="AGROSAC"
```

Luego ejecuta:
```bash
php artisan config:clear
```

Los emails se guardarán en `storage/logs/laravel.log` y podrás ver el contenido completo del email allí.

---

### Opción 2: Configurar Gmail Correctamente

**IMPORTANTE:** Gmail requiere una **Contraseña de Aplicación**, NO tu contraseña normal.

#### Pasos para configurar Gmail:

1. **Habilita la verificación en dos pasos** en tu cuenta de Google:
   - Ve a: https://myaccount.google.com/security
   - Activa "Verificación en dos pasos"

2. **Genera una Contraseña de Aplicación**:
   - Ve a: https://myaccount.google.com/apppasswords
   - Selecciona "Correo" y "Otro (nombre personalizado)"
   - Escribe "AGROSAC" como nombre
   - Copia la contraseña de 16 caracteres que te genera (sin espacios)

3. **Configura tu archivo `.env`**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=maryigpo6@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion_de_16_caracteres
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=maryigpo6@gmail.com
MAIL_FROM_NAME="AGROSAC"
```

4. **Limpia la caché de configuración**:
```bash
php artisan config:clear
```

---

### Opción 3: Usar Mailtrap (Para Pruebas)

Mailtrap es un servicio gratuito para probar emails en desarrollo.

1. Crea una cuenta en https://mailtrap.io (gratis)
2. Obtén las credenciales SMTP de tu inbox
3. Configura tu `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@agrosac.com
MAIL_FROM_NAME="AGROSAC"
```

---

## Verificación

Después de configurar, prueba enviando un correo de recuperación de contraseña:

1. Ve a: http://127.0.0.1:8000/forgot-password
2. Ingresa un email válido de tu base de datos
3. Haz clic en "Enviar Enlace de Recuperación"

**Si usas "log"**: Revisa `storage/logs/laravel.log` para ver el email completo.
**Si usas Gmail o Mailtrap**: Revisa tu bandeja de entrada.

---

## Notas Importantes

- **NUNCA** uses tu contraseña normal de Gmail en el archivo `.env`
- **SIEMPRE** usa una Contraseña de Aplicación para Gmail
- Para desarrollo, es más fácil usar `MAIL_MAILER=log`
- Asegúrate de ejecutar `php artisan config:clear` después de cambiar el `.env`






