# Invoice Recorder Challenge Sample (v1.0) [ES]

API REST que expone endpoints que permite registrar comprobantes en formato xml.
De estos comprobantes se obteniene la información como el emisor y receptor, sus documentos (dni, ruc, etc), los artículos o líneas, y los montos totales y por cada artículo.
Un comprobante es un documento que respalda una transacción financiera o comercial, y en su versión XML es un archivo estructurado que contiene todos los datos necesarios para cumplir con los requisitos legales y fiscales.
Utilizando el lenguaje XML, se generan comprobantes digitales, que contienen información del emisor, receptor, conceptos, impuestos y el monto total de la transacción.
La API utiliza Json Web Token para la autenticación.

## Detalles de la API

-   Usa PHP 8.1
-   Usa una base de datos en MySQL
-   Puede enviar correos

## Inicia el proyecto con docker

-   Clona el archivo `.env.example` a `.env`
-   Reemplaza las credenciales de correo por las tuyas (puedes obtener unas con gmail siguiendo [esta guía](https://programacionymas.com/blog/como-enviar-mails-correos-desde-laravel#:~:text=Para%20dar%20la%20orden%20a,su%20orden%20ha%20sido%20enviada.))
-   En una terminal ejecuta:

```
docker-compose up
```

-   En otra terminal, ingresa al contenedor web y ejecuta:

```
composer install --ignore-platform-reqs
php artisan migrate
```

-   Consulta la API en http://localhost:8090/api/v1

## Información inicial

Puedes encontrar información inicial para popular la DB en el siguiente enlace:

[Datos iniciales](https://drive.google.com/drive/folders/103WGuWMLSkuHCD9142ubzyXPbJn77ZVO?usp=sharing)

## Nuevas funcionalidades

### 1. Registro de serie, número, tipo del comprobante y moneda

Se desea poder registrar la serie, número, tipo de comprobante y moneda. Para comprobantes existentes, debería extraerse esa información a regularizar desde el campo xml_content de vouchers.

<b>Descripción</b>: Implementación de la funcionalidad para registrar y actualizar los datos de serie, número, tipo de comprobante y moneda en los comprobantes existentes.

<b>Cómo Funciona</b>: Se extrajo la información del campo xml_content de los vouchers para actualizar la base de datos con los datos requeridos.

### 3. Endpoint de montos totales

Se necesita un nuevo endpoint que devuelva la información total acumulada en soles y dólares.

<b>Descripción</b>: Creación de un endpoint para obtener los montos totales acumulados en soles y dólares.

<b>Cómo Funciona</b>: Calcula la suma del total_amount agrupados por la moneda correspondiente.

<b>Endpoint</b>: 

GET /api/v1/vouchers/total-amounts: Devuelve los montos totales en PEN y USD.

### 4. Eliminación de comprobantes

Se necesita poder eliminar comprobantes por su id.

<b>Descripción</b>: Facilita la eliminación de comprobantes a través de su ID.

<b>Cómo Funciona</b>: Elimina el comprobante especificado por su ID de la base de datos.

<b>Endpoint</b>: 

DELETE /api/v1/vouchers/{id}: Elimina el comprobante por ID.

### 5. Filtro en listado de comprobantes

Se necesita poder filtrar en el endpoint de listado por serie, número y por un rango de fechas (que actuarán sobre las fechas de creación).

<b>Descripción</b>: Permite filtrar los comprobantes por serie, número y rango de fechas.

<b>Cómo Funciona</b>: Utiliza parámetros de consulta para filtrar los comprobantes en la base de datos y devuelve una lista filtrada.

<b>Endpoint</b>: 

GET /api/v1/vouchers/filter: Lista los comprobantes filtrados según los parámetros proporcionados.

