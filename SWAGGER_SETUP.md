# Swagger / OpenAPI Setup Guide

## Overview

This project uses **L5-Swagger** (darkaonline/l5-swagger) to auto-generate OpenAPI 3.0 documentation from PHP 8 attributes in your controllers.

## Accessing the Documentation

Once the app is running, visit:

| Environment | URL |
|-------------|-----|
| Local | `http://localhost/api/documentation` |
| Production | `http://YOUR_DROPLET_IP/api/documentation` |

## Architecture

```
Controller PHP Attributes → L5-Swagger Generator → api-docs.json → Swagger UI
```

## File Structure

```
app/
├── OpenApi.php                    # Base OpenAPI info (title, version, security)
└── Http/Controllers/Api/
    ├── AuthController.php         # Auth endpoints with #[OA\...] attributes
    ├── ProductController.php      # Product CRUD with #[OA\...] attributes
    └── UserController.php         # User management with #[OA\...] attributes

config/
└── l5-swagger.php               # L5-Swagger configuration

storage/api-docs/
└── api-docs.json                # Auto-generated OpenAPI spec

resources/views/vendor/l5-swagger/
└── index.blade.php              # Swagger UI view
```

## How to Document a New Endpoint

Use PHP 8 attributes from the `OpenApi\Attributes` namespace:

```php
use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/your-endpoint",
    summary: "Short description",
    description: "Longer description of what this does",
    tags: ["YourTag"],
    security: [["bearerAuth" => []]],  // If auth required
    parameters: [
        new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
    ],
    responses: [
        new OA\Response(response: 200, description: "Success"),
        new OA\Response(response: 404, description: "Not found")
    ]
)]
public function yourMethod($id)
{
    // ...
}
```

### Common Attributes

| Attribute | Use For |
|-----------|---------|
| `#[OA\Get]` | GET requests |
| `#[OA\Post]` | POST requests |
| `#[OA\Put]` | PUT requests |
| `#[OA\Delete]` | DELETE requests |
| `#[OA\Tag]` | Group endpoints (on class) |
| `#[OA\Parameter]` | URL/path/query params |
| `#[OA\RequestBody]` | Request body schema |
| `#[OA\Response]` | Response schemas |
| `#[OA\SecurityScheme]` | Auth definition (in OpenApi.php) |

## Regenerating Documentation

### Locally:
```bash
php artisan l5-swagger:generate
```

### On the Droplet (Docker):
```bash
docker-compose exec app php artisan l5-swagger:generate
```

### During CI/CD Deploy:
The deploy workflow already runs this automatically after container restart.

## Authentication in Swagger UI

1. Click **Authorize** button in Swagger UI
2. Enter your Bearer token: `Bearer YOUR_TOKEN_HERE`
3. Click **Authorize** → **Close**
4. All secured endpoints will now include the token

## Troubleshooting

| Issue | Fix |
|-------|-----|
| `Required @OA\Info() not found` | Ensure `app/OpenApi.php` exists with `#[OA\Info]` |
| `Required @OA\PathItem() not found` | Add at least one `#[OA\Get/Post/Put/Delete]` attribute |
| Docs not updating | Run `php artisan l5-swagger:generate` |
| 404 on `/api/documentation` | Check `config/l5-swagger.php` routes config |

## Resources

- [L5-Swagger GitHub](https://github.com/DarkaOnLine/L5-Swagger)
- [Swagger PHP Attributes Docs](https://zircote.github.io/swagger-php/guide/attributes.html)
- [OpenAPI 3.0 Specification](https://swagger.io/specification/)
