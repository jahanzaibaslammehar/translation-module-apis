#!/usr/bin/env node

const fs = require('fs');
const yaml = require('js-yaml');
const path = require('path');

console.log('🔍 Validating Swagger YAML file...\n');

try {
    // Read the swagger.yaml file
    const swaggerPath = path.join(__dirname, 'swagger.yaml');
    const fileContents = fs.readFileSync(swaggerPath, 'utf8');
    
    // Parse YAML
    const swagger = yaml.load(fileContents);
    
    // Basic validation
    if (!swagger.openapi) {
        throw new Error('Missing openapi version');
    }
    
    if (!swagger.info) {
        throw new Error('Missing info section');
    }
    
    if (!swagger.paths) {
        throw new Error('Missing paths section');
    }
    
    if (!swagger.components) {
        throw new Error('Missing components section');
    }
    
    // Count endpoints
    const endpointCount = Object.keys(swagger.paths).length;
    const schemaCount = Object.keys(swagger.components.schemas || {}).length;
    
    console.log('✅ Swagger YAML file is valid!');
    console.log(`📊 OpenAPI Version: ${swagger.openapi}`);
    console.log(`📝 API Title: ${swagger.info.title}`);
    console.log(`🔗 Endpoints: ${endpointCount}`);
    console.log(`📋 Schemas: ${schemaCount}`);
    console.log(`🏷️  Tags: ${swagger.tags?.length || 0}`);
    
    // List all endpoints
    console.log('\n📋 Available Endpoints:');
    Object.keys(swagger.paths).forEach(path => {
        const methods = Object.keys(swagger.paths[path]);
        methods.forEach(method => {
            const endpoint = swagger.paths[path][method];
            console.log(`  ${method.toUpperCase()} ${path} - ${endpoint.summary || 'No summary'}`);
        });
    });
    
    // List all schemas
    console.log('\n📋 Available Schemas:');
    Object.keys(swagger.components.schemas || {}).forEach(schema => {
        console.log(`  - ${schema}`);
    });
    
    console.log('\n🎉 Validation completed successfully!');
    console.log('💡 You can now import this file into SwaggerHub or use it locally.');
    
} catch (error) {
    console.error('❌ Validation failed:');
    console.error(error.message);
    
    if (error.message.includes('js-yaml')) {
        console.error('\n💡 To install js-yaml, run: npm install js-yaml');
    }
    
    process.exit(1);
}
