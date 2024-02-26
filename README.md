# Rest-BookStore
<p align="center">
  <img src="https://i.imgur.com/L8JoB88.png" width="400px"/>
</p>

# ✏️⚠️ Instrucciones Importantes
## Configuración
1. Cambiar el nombre del archivo .env.example a .env
2. DEVELOP_MODE=true para activar el modo de desarrollo, DEVELOP_MODE=false para desactivar el modo de desarrollo. Esto es importante, pues en modo desarrollo la API está activada para poder ser testeada con Postman, pero en modo producción, la API está desactivada para evitar que se realicen cambios en la base de datos. Es importante que en modo de producción se deshabilite, o cualquiera podrá hacer cambios.

## Instrucciones
📁❗ Primero deberás de crear el enlace a storage (EN EL CONTENEDOR DE DOCKER laravel-rest-funkos-laravel.test-1):

    php artisan storage:link

1. Ejecutar Docker: docker-compose up -d
2. Ejecutar las migraciones: docker exec laravel-rest-funkos-laravel.test-1 php artisan migrate
3. Ejecutar los seeders: docker exec laravel-rest-funkos-laravel.test-1 php artisan db:seed
4. Ejecutar el comando npm run dev para compilar los archivos de JavaScript y CSS.

Deberás reemplazar el nombre del contenedor de Docker laravel-rest-funkos-laravel.test-1 por el nombre que haya asignado Docker.
Si lo prefieres, puedes realizar todos los pasos anteriores en un solo comando:

    CONTAINER_NAME="laravel-rest-funkos-laravel.test-1" && docker-compose up -d && sleep 1 && while ! docker exec $CONTAINER_NAME php artisan migrate; do sleep 2; done && docker exec $CONTAINER_NAME php artisan db:seed && echo "Migración exitosa. El servicio está iniciado." && npm run dev

Solo deberás cambiar el CONTAINER_NAME por el nombre del contenedor que te genera Docker y ya podrás ejecutar de una pasada todos los comandos anteriores.

Para eliminar el contenedor, junto con sus datos, puedes ejecutar el siguiente comando:

    docker-compose down -v --remove-orphans

Ambos comandos comentados pueden ser utilizados para iniciar y detener el contenedor de Docker. Es importante entender que cada vez que ejecutes el comando de docker-compose down -v  --remove-orphans se van a eliminar todos los datos de la base de datos. Solo se debería de utilizar en modo desarrollo, nunca en modo producción. ⚠️

## Arquitectura
<p align="center">
  <img src="https://i.imgur.com/TPseMiK.png"/>
</p>

## Diagrama UML
<p align="center">
  <img src="https://i.imgur.com/HbmhQMF.png"/>
</p>

## Diagrama de casos de uso
<p align="center">
  <img src="https://i.imgur.com/EaeNSxz.png"/>
</p>

# NULLERS BOOKS API
## Descripción

Bienvenido a la API REST de NULLERS BOOKS, una tienda de libros en línea que te permite realizar diversas operaciones, como consultar libros, gestionar usuarios, administrar tiendas y realizar orders. Nuestra API está diseñada para ser segura, eficiente y escalable, proporcionando una interfaz robusta para interactuar con la plataforma de comercio de libros.

### Estructura del Proyecto

- **Controllers:** Manejan las solicitudes HTTP y devuelven las respuestas correspondientes.
- **app:** Contiene la lógica de negocio, como modelos y controladores.
- **bootstrap:** Archivos de inicio de la aplicación.
- **config:** Configuración de la aplicación.
- **database:** Archivos de base de datos, como migraciones y seeders.
- **public:** Punto de entrada de la aplicación y archivos estáticos.
- **resources:** Recursos de la aplicación, como vistas y assets.
- **routes:** Definiciones de rutas de la aplicación.
- **storage:** Archivos generados por la aplicación.
- **tests:** Archivos de pruebas de la aplicación.
- **vendor:** Dependencias del proyecto gestionadas por Composer.

## Autores
- [Madirex](https://github.com/Madirex/)
- [Jaimesalcedo1](https://github.com/jaimesalcedo1/)
- [Danniellgm03](https://github.com/Danniellgm03)
- [Binweiwang](https://github.com/Binweiwang)
- [Alexdor11](https://github.com/alexdor11)
