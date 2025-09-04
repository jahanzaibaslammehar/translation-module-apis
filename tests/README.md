# Translation API Test Suite

This document provides a comprehensive overview of the test suite for the Translation API, including unit tests, feature tests, and performance tests.

## Test Structure

The test suite is organized into the following categories:

### Unit Tests (`tests/Unit/`)

Unit tests focus on testing individual components in isolation, ensuring each piece of functionality works correctly without external dependencies.

#### Models (`tests/Unit/Models/`)
- **UserTest.php** - Tests User model functionality including attributes, casts, and traits
- **TranslationTest.php** - Tests Translation model functionality including JSON casting and validation

#### Services (`tests/Unit/Services/`)
- **TranslationServiceTest.php** - Tests TranslationService business logic and methods
- **AuthServiceTest.php** - Tests AuthService authentication and token generation

#### Repositories (`tests/Unit/Repositories/`)
- **TranslationRepositoryTest.php** - Tests TranslationRepository database operations and search functionality

#### Requests (`tests/Unit/Requests/`)
- **CreateTranslationRequestTest.php** - Tests CreateTranslationRequest validation rules
- **UpdateTranslationRequestTest.php** - Tests UpdateTranslationRequest validation rules
- **LoginRequestTest.php** - Tests LoginRequest validation rules

### Feature Tests (`tests/Feature/`)

Feature tests test the complete functionality of the API endpoints, including authentication, validation, and database operations.

#### Authentication (`tests/Feature/Auth/`)
- **AuthenticationTest.php** - Tests complete authentication flow including login, token generation, and protected routes

#### Translation (`tests/Feature/Translation/`)
- **TranslationManagementTest.php** - Tests complete translation CRUD operations, search, and validation

### Performance Tests (`tests/Feature/Performance/`)

Performance tests ensure the API meets performance requirements under various load conditions.

- **TranslationPerformanceTest.php** - Tests translation operations performance including creation, retrieval, search, and concurrent operations
- **AuthenticationPerformanceTest.php** - Tests authentication performance including login speed, token generation, and concurrent requests

## Test Coverage

The test suite covers:

### Core Functionality
- ✅ User authentication and authorization
- ✅ Translation CRUD operations
- ✅ Translation search and filtering
- ✅ Request validation
- ✅ Database operations
- ✅ API response formatting

### Edge Cases
- ✅ Invalid credentials
- ✅ Missing required fields
- ✅ Malformed data
- ✅ Large datasets
- ✅ Concurrent requests
- ✅ Memory usage
- ✅ Response size limits

### Performance Requirements
- ✅ Response time thresholds
- ✅ Database query performance
- ✅ Pagination handling
- ✅ Memory efficiency
- ✅ Scalability testing

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature

# Run only performance tests
php artisan test --testsuite=Performance

# Run specific categories
php artisan test --testsuite=Models
php artisan test --testsuite=Services
php artisan test --testsuite=Repositories
php artisan test --testsuite=Requests
php artisan test --testsuite=Authentication
php artisan test --testsuite=Translation
```

### Run Individual Test Files
```bash
# Run specific test file
php artisan test tests/Unit/Models/UserTest.php

# Run specific test method
php artisan test --filter=it_can_create_user_with_valid_data
```

### Run Tests with Coverage
```bash
# Generate HTML coverage report
php artisan test --coverage-html=coverage

# Generate XML coverage report
php artisan test --coverage-clover=coverage.xml
```

### Run Tests in Parallel
```bash
# Run tests in parallel (requires parallel testing package)
php artisan test --parallel
```

## Performance Test Thresholds

The performance tests enforce the following response time thresholds:

### Authentication Operations
- **Login (valid credentials)**: < 300ms
- **Login (invalid credentials)**: < 200ms
- **Token generation**: < 300ms
- **Concurrent logins (20)**: < 3000ms

### Translation Operations
- **Creation (small data)**: < 500ms
- **Creation (large data)**: < 1000ms
- **Retrieval**: < 200ms
- **Update**: < 400ms
- **Search (small dataset)**: < 300ms
- **Search (large dataset)**: < 1000ms
- **Search with pagination**: < 1200ms

### Resource Usage Limits
- **Memory usage**: < 50MB for translation operations, < 10MB for authentication
- **Response size**: < 1MB
- **Database queries**: Optimized for large datasets

## Test Data

The test suite uses Laravel factories to generate test data:

- **UserFactory** - Creates test users with valid credentials
- **TranslationFactory** - Creates test translations with various contexts and locales

## Database Testing

Tests use SQLite in-memory database for fast execution:
- Database is refreshed between tests using `RefreshDatabase` trait
- Each test runs in isolation
- No external database dependencies

## Mocking and Stubbing

The test suite uses Laravel's built-in testing utilities:
- Database transactions for data isolation
- Factory pattern for test data generation
- Assertion methods for response validation

## Continuous Integration

The test suite is designed to run in CI/CD environments:
- Fast execution (typically completes in under 2 minutes)
- No external service dependencies
- Consistent results across environments
- Coverage reporting for quality metrics

## Best Practices

### Writing New Tests
1. Use descriptive test method names starting with "it_"
2. Test both happy path and edge cases
3. Include performance assertions where relevant
4. Use appropriate test data factories
5. Clean up test data after each test

### Test Organization
1. Group related tests in appropriate test suites
2. Use setUp() methods for common test preparation
3. Keep tests focused on single functionality
4. Use meaningful assertions with descriptive messages

### Performance Testing
1. Set realistic performance thresholds
2. Test with various data sizes
3. Include concurrent operation testing
4. Monitor memory usage and response sizes

## Troubleshooting

### Common Issues
1. **Database connection errors**: Ensure SQLite is available
2. **Memory issues**: Check for memory leaks in large dataset tests
3. **Slow tests**: Use database transactions and optimize factory usage
4. **Authentication failures**: Verify Sanctum configuration

### Debug Mode
Enable debug mode for detailed test output:
```bash
php artisan test --verbose
```

## Contributing

When adding new tests:
1. Follow the existing naming conventions
2. Include comprehensive test coverage
3. Add performance tests for new endpoints
4. Update this README with new test information
5. Ensure all tests pass before submitting

## Support

For test-related issues:
1. Check the Laravel testing documentation
2. Review existing test patterns
3. Consult the performance test thresholds
4. Run tests in isolation to identify specific issues
