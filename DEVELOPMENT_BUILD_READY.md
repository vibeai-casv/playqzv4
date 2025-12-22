# Development Server Deployment Instructions

## Project rebuilt for development server at: projects/playqzvd

### Files Ready for Upload:
- All files in `dist/` directory
- React app built with base path: `/projects/playqzvd/`
- API configured for development environment
- Database configuration: `playqzv4_dev`

### Upload Structure:
```
projects/playqzvd/
├── index.html (React app entry point)
├── assets/ (CSS, JS, images)
├── api/ (PHP backend)
├── uploads/ (media files)
└── .htaccess (URL rewriting)
```

### Configuration:
- **Base URL**: `http://localhost/projects/playqzvd`
- **API URL**: `http://localhost/projects/playqzvd/api`
- **Database**: `playqzv4_dev` (localhost)
- **Environment**: Development mode enabled

### Next Steps:
1. Upload all files from `dist/` to `projects/playqzvd/` on your development server
2. Create database `playqzv4_dev` 
3. Import schema from `api/schema.sql`
4. Update database credentials in `api/config.development.php` if needed
5. Test at `http://localhost/projects/playqzvd`

### Features Enabled:
- Debug mode ON
- CORS enabled for development
- Error reporting enabled
- All media files included
- Fixed error handling in useAdmin hook
- Removed debug console.log statements

Build completed successfully!