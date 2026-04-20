<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Felmanic Brandz Mannequins API",
    description: "API documentation for the Felmanic Brandz Mannequins management system",
    contact: new OA\Contact(email: "support@felmanicbrandz.com"),
    license: new OA\License(name: "MIT", url: "https://opensource.org/licenses/MIT")
)]
#[OA\Server(url: "/api", description: "Local API Server")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class OpenApi
{
    // This class exists only to hold the base OpenAPI annotations
}
