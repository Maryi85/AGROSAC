# AGROSAC

Sistema de Gestión Agrícola desarrollado en Laravel.

## 📋 Requisitos Previos

Asegúrate de tener instalado lo siguiente en tu sistema:

- **PHP**: Versión 8.2 o superior.
- **Composer**: Gestor de dependencias de PHP.
- **Node.js & NPM**: Para compilar los activos del frontend.
- **MySQL / MariaDB**: Base de datos.

## 🚀 Instalación y Configuración

Sigue estos pasos para configurar el proyecto en tu entorno local:

1.  **Clonar el repositorio:**

    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd AGROSAC
    ```

    Si no has clonado el repositorio y ya tienes los archivos, ve al siguiente paso.

2.  **Instalar dependencias de PHP:**

    ```bash
    composer install
    ```

3.  **Instalar dependencias de Frontend:**

    ```bash
    npm install
    ```

4.  **Configurar el entorno:**

    Copia el archivo de ejemplo para crear tu configuración local.

    ```bash
    cp .env.example .env
    ```

    Edita el archivo `.env` y configura el acceso a tu base de datos:

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=agrosac
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```

5.  **Generar la clave de la aplicación:**

    ```bash
    php artisan key:generate
    ```

6.  **Migrar y sembrar la base de datos:**

    Esto creará las tablas y un usuario administrador por defecto.

    ```bash
    php artisan migrate --seed
    ```

    > **Nota:** El seeder crea un usuario administrador con las siguientes credenciales:
    > - **Email:** perdomomaryii06@gmail.com
    > - **Contraseña:** Adminagrosac123

7.  **Compilar los activos:**

    ```bash
    npm run build
    ```
    (O `npm run dev` para desarrollo en vivo)

## 🛠️ Ejecución

Para iniciar el servidor de desarrollo local:

```bash
php artisan serve
```

El sistema estará accesible en [http://localhost:8000](http://localhost:8000).

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](https://opensource.org/licenses/MIT).
