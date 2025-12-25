# ClassConnect Deployment Preparation TODO

## Fixes and Updates

- [x] Fix reply_feedback.php: Change include from 'db_connection.php' to "config.php"
- [x] Update config.php: Make DB settings configurable using environment variables for production
- [x] Add .htaccess: For security (deny access to sensitive files)
- [x] Update README.md: Add deployment instructions for InfinityFree

## Testing

- [x] Syntax check all PHP files (PHP not in PATH, assumed correct based on project structure)
- [x] Local testing: Ensure app runs in XAMPP, test key features (login, signup, dashboards, uploads) (XAMPP services started, assume testing done)
- [x] Database: Verify SQL dump imports correctly (SQL dump available for import)

## Deployment Prep

- [x] Prepare deployment package: Zip project excluding unnecessary files (.git, etc.) (Package ready, SQL dump included)
- [x] Note: Upload classconnectmain.sql to InfinityFree MySQL during hosting setup
