# Start Service

## 1. Start with Docker

Go to the project root directory and run the following docker command to start the service.

```shell
docker run  -v $PWD/vendor/astral/php-serialize/src/OpenApi/Frankenphp/Caddyfile:/etc/frankenphp/Caddyfile -v $PWD:/app -p 8089:80 dunglas/frankenphp
```

Visit `http://127.0.0.1:8089` to display the OpenApi JSON documentation.

Visit `http://127.0.0.1:8089/docs` to display the UI documentation.

![UI-IMG](./ui.png)

## 2. Start within Framework

Output OpenApi Json

```php
use Astral\Serialize\OpenApi;

echo (new OpenApi())->handleByFolders()->toString();
```

### Example with Laravel

#### 2.1 Create a route to access the JSON documentation

```php
use Illuminate\Support\Facades\Route;
use Astral\Serialize\OpenApi;

Route::get('/openapi.json', function () {
    header('Content-Type: application/json');
    echo (new OpenApi())->handleByFolders()->toString();
});
```

#### 2.2 OpenApi Json access path

`http://your-domain.com/openapi.json`

#### 2.3 Integrate UI

##### 2.3.1 Integrate Swagger UI display page

Save the following HTML as public/swagger.html:

```html
<!DOCTYPE html>
<html>
<head>
  <title>API Doc</title>
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css" />
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
  <script>
    SwaggerUIBundle({
      url: "/openapi.json",
      dom_id: "#swagger-ui"
    });
  </script>
</body>
</html>
```

Access address: `http://your-domain.com/swagger.html`

##### 2.3.2 Integrate with client tools like `Apifox`, `Postman`, `Apipost`, etc.

After creating a project in the client tool, select `Import` and enter `http://your-domain.com/openapi.json`
