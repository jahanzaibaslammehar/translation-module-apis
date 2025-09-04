# Translation API Documentation

This repository contains comprehensive Swagger/OpenAPI 3.0 documentation for the Translation API service.

## 📚 Documentation Files

- **`swagger.yaml`** - Complete OpenAPI 3.0 specification
- **`README-API-Documentation.md`** - This file with usage instructions

## 🚀 Quick Start

### Option 1: Import to SwaggerHub

1. **Create a SwaggerHub Account**
   - Go to [SwaggerHub](https://swaggerhub.com/)
   - Sign up for a free account

2. **Import the Documentation**
   - Click "Create New" → "API"
   - Choose "Import API Definition"
   - Upload the `swagger.yaml` file or paste its contents
   - Set your API name (e.g., "Translation API")
   - Choose visibility (Public or Private)
   - Click "Create API"

3. **Access Your Documentation**
   - Your API documentation will be available at: `https://app.swaggerhub.com/apis/{username}/{api-name}/{version}`
   - You can share this URL with developers and stakeholders

### Option 2: Use Swagger UI Locally

1. **Install Swagger UI**
   ```bash
   # Clone Swagger UI
   git clone https://github.com/swagger-api/swagger-ui.git
   cd swagger-ui/dist
   
   # Copy the swagger.yaml file to this directory
   cp /path/to/your/swagger.yaml ./
   ```

2. **Modify index.html**
   - Open `index.html` in a text editor
   - Change the URL from the default to: `./swagger.yaml`

3. **Serve the Documentation**
   ```bash
   # Using Python
   python -m http.server 8000
   
   # Using Node.js
   npx serve .
   
   # Using PHP
   php -S localhost:8000
   ```

4. **View Documentation**
   - Open `http://localhost:8000` in your browser

### Option 3: Use Online Swagger Editor

1. Go to [Swagger Editor](https://editor.swagger.io/)
2. Copy the contents of `swagger.yaml`
3. Paste into the editor
4. View the rendered documentation on the right side

## 🔐 Authentication

The API uses **Bearer Token Authentication** with Laravel Sanctum:

1. **Login to get a token:**
   ```bash
   curl -X POST "https://api.translation-service.com/v1/login" \
     -H "Content-Type: application/json" \
     -d '{
       "email": "user@example.com",
       "password": "password123"
     }'
   ```

2. **Use the token in subsequent requests:**
   ```bash
   curl -X GET "https://api.translation-service.com/v1/translation/get/common/en" \
     -H "Authorization: Bearer YOUR_TOKEN_HERE"
   ```

## 📖 API Endpoints

### Authentication
- `POST /login` - User authentication

### Translations
- `GET /translation/get/{context}/{locale}` - Get translations by context and locale
- `POST /translation/create` - Create new translation
- `PATCH /translation/update` - Update existing translation
- `GET /translation/search/{keyword}` - Search translations

## 📝 Request/Response Examples

### Create Translation
```bash
curl -X POST "https://api.translation-service.com/v1/translation/create" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "context": "welcome_page",
    "locale": "es",
    "translations": {
      "title": "Bienvenido a nuestra aplicación",
      "subtitle": "Comience su viaje aquí",
      "button_start": "Comenzar"
    }
  }'
```

### Search Translations
```bash
curl -X GET "https://api.translation-service.com/v1/translation/search/welcome" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔧 Customization

### Update Server URLs
Modify the `servers` section in `swagger.yaml`:

```yaml
servers:
  - url: https://your-production-api.com/v1
    description: Production server
  - url: https://your-staging-api.com/v1
    description: Staging server
  - url: http://localhost:8000/api
    description: Local development server
```

### Update Contact Information
Modify the `info.contact` section:

```yaml
contact:
  name: Your Company Name
  email: api-support@yourcompany.com
  url: https://yourcompany.com/support
```

### Add Custom Examples
Enhance the documentation with more realistic examples:

```yaml
examples:
  real_world_scenario:
    summary: Real-world translation example
    value:
      context: "ecommerce_checkout"
      locale: "de"
      translations:
        cart_empty: "Ihr Warenkorb ist leer"
        checkout_button: "Zur Kasse"
        total_amount: "Gesamtbetrag"
```

## 📊 API Testing

### Using SwaggerHub
1. Navigate to your API documentation in SwaggerHub
2. Click "Try it out" on any endpoint
3. Fill in the required parameters
4. Click "Execute" to test the API

### Using Postman
1. Import the `swagger.yaml` file into Postman
2. Set up environment variables for your base URL and tokens
3. Use the generated collection to test all endpoints

### Using cURL
All examples in the documentation include cURL commands for easy testing.

## 🚨 Error Handling

The API returns consistent error responses:

```json
{
  "code": 422,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error description"]
  }
}
```

Common HTTP status codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## 📈 Rate Limiting

The API implements rate limiting to ensure fair usage:
- Implement exponential backoff for retries
- Respect `Retry-After` headers when provided
- Monitor your API usage to stay within limits

## 🔄 Versioning

The API uses URL versioning:
- Current version: `v1`
- Future versions will be available at `v2`, `v3`, etc.
- Breaking changes will only occur in major version updates

## 🤝 Contributing

To improve the API documentation:

1. Fork the repository
2. Make your changes to `swagger.yaml`
3. Test the documentation using Swagger Editor
4. Submit a pull request with a clear description of changes

## 📞 Support

For API support:
- Email: api-support@translation-service.com
- Documentation: [Your SwaggerHub URL]
- Issues: [GitHub Issues URL]

## 📄 License

This API documentation is licensed under the MIT License.

---

**Note**: This documentation is automatically generated and maintained. For the most up-to-date information, always refer to the live SwaggerHub documentation.
