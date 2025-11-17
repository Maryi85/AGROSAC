# Solución al Error de Correo

## Problema
El error indica que las credenciales de Gmail son inválidas. Esto sucede porque Gmail requiere una **Contraseña de Aplicación** especial, no tu contraseña normal.

## Solución Rápida (Recomendada para Desarrollo)

**Abre tu archivo `.env`** (está en la raíz del proyecto) y busca estas líneas:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=maryigpo6@gmail.com
MAIL_PASSWORD=tu_contraseña_aquí
MAIL_ENCRYPTION=tls
```

**Cámbialas por esto:**

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@agrosac.com
MAIL_FROM_NAME="AGROSAC"
```

**Luego ejecuta en tu terminal:**
```bash
php artisan config:clear
```

**¡Listo!** Ahora el sistema guardará los emails en los logs en lugar de intentar enviarlos. Puedes ver el contenido completo del email en:
```
storage/logs/laravel.log
```

---

## Si Necesitas Enviar Emails Reales con Gmail

### Paso 1: Habilita la Verificación en Dos Pasos
1. Ve a: https://myaccount.google.com/security
2. Activa "Verificación en dos pasos"

### Paso 2: Genera una Contraseña de Aplicación
1. Ve a: https://myaccount.google.com/apppasswords
2. Selecciona "Correo" y "Otro (nombre personalizado)"
3. Escribe "AGROSAC" como nombre
4. **Copia la contraseña de 16 caracteres** que te genera (sin espacios)

### Paso 3: Configura tu `.env`
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=maryigpo6@gmail.com
MAIL_PASSWORD=la_contraseña_de_16_caracteres_que_copiaste
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=maryigpo6@gmail.com
MAIL_FROM_NAME="AGROSAC"
```

### Paso 4: Limpia la Caché
```bash
php artisan config:clear
```

---

## Verificación

Después de configurar, prueba enviando un correo de recuperación:

1. Ve a: http://127.0.0.1:8000/forgot-password
2. Ingresa un email válido de tu base de datos
3. Haz clic en "Enviar Enlace de Recuperación"

**Si usas `MAIL_MAILER=log`**: Revisa `storage/logs/laravel.log` para ver el email completo.
**Si usas Gmail**: Revisa tu bandeja de entrada.

---

## Notas Importantes

- **NUNCA** uses tu contraseña normal de Gmail en el archivo `.env`
- **SIEMPRE** usa una Contraseña de Aplicación para Gmail
- Para desarrollo, es más fácil usar `MAIL_MAILER=log`
- Asegúrate de ejecutar `php artisan config:clear` después de cambiar el `.env`






