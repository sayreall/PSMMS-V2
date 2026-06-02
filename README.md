# PSMMS Dashboard

Modern PHP 8 dashboard with MVC architecture, Tailwind UI, and MySQL storage. Includes authentication, role-based access, CRUD, AJAX forms, charts, and API-ready routes.

## Features
- Responsive admin dashboard with sidebar layout
- Authentication with roles (viewer, editor, admin, super_admin)
- CRUD for users with search, filter, pagination
- Real-time form validation and AJAX submissions
- Toast notifications and confirmation dialogs
- Chart.js analytics and DataTables table styling
- Secure prepared statements and session hardening
- API endpoints with JWT authentication
- Super Admin Address management for Regions, Provinces, and Municipalities
- Local Tailwind CSS build pipeline for compiled application styles

## Requirements
- PHP 8.0+ with PDO MySQL, JSON, mbstring, openssl
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js and npm for rebuilding Tailwind CSS

## PHP ini setup (Windows)
1. Find the active `php.ini`:
   ```bash
   php --ini
   ```
2. Open the `Loaded Configuration File` and enable MySQL extensions (remove the leading `;`):
   ```ini
   extension=pdo_mysql
   extension=mysqli
   ```
3. Ensure `extension_dir` points to your PHP `ext` folder, for example:
   ```ini
   extension_dir="ext"
   ```
4. Restart your web server or terminal.
5. Verify:
   ```bash
   php -m | findstr pdo_mysql
   ```

## Setup
1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Copy environment file:
   ```bash
   cp .env.example .env
   ```
3. Configure `.env` database and mail settings.
4. Run installer (creates DB, runs migrations, seeds admin user):
   ```bash
   php install.sh
   ```
5. Start the development server:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Rebuild CSS after changing `public/css/input.css` or Tailwind classes:
   ```bash
   npm run build:css
   ```
7. Open http://localhost:8000

## Frontend CSS build
Tailwind CSS is compiled locally from `public/css/input.css` into `public/css/app.css`.

Available npm scripts:
```bash
npm run build:css
npm run watch:css
```

The main layout loads the compiled `public/css/app.css` file directly, so the Tailwind CDN script is no longer required for dashboard pages.

## Default admin account
- Email: `admin@psmms.local`
- Password: `Admin@123`

Change this immediately after first login.

## Access
- Open http://localhost:8000/login
- Use the default admin account above, or register a new account.
- New registrations are created as `pending` and must be approved by a Super Admin before login.

## Project structure
```
app/
  Config/
  Controllers/
  Helpers/
  Middleware/
  Models/
  Views/
database/
  migrations/
  seeds/
public/
  css/
  js/
  images/
routes/
```

## API endpoints
- `POST /api/auth/login`
- `GET /api/stats`
- `GET /api/users`
- `POST /api/users`
- `PUT /api/users/{id}`
- `DELETE /api/users/{id}`

Send `Authorization: Bearer <jwt>` for protected endpoints.

## Route map
### Web routes
- `GET /` redirect to dashboard
- `GET /login` guest login page
- `POST /login` login submit
- `GET /register` guest register page
- `POST /register` register submit
- `GET /forgot-password` forgot password page
- `POST /forgot-password` forgot password submit
- `POST /logout` authenticated logout
- `GET /dashboard` dashboard page
- `GET /dashboard/profile` profile page
- `POST /dashboard/profile` profile update
- `POST /dashboard/password` password update
- `GET /dashboard/stats` dashboard stats (AJAX/JSON)
- `GET /users` users index (admin)
- `GET /users/create` users create form (admin)
- `POST /users` users create submit (admin)
- `GET /users/{id}/edit` users edit form (admin)
- `PUT /users/{id}` users update (admin)
- `DELETE /users/{id}` users delete (admin)
- `GET /address/region` region management page (super admin)
- `POST /address/region` create region (super admin)
- `GET /address/province` province management page (super admin)
- `GET /address/municipalities` municipality management page (super admin)
- `POST /address/municipalities` create municipality with region/province lookup (super admin)

### API routes
- `POST /api/auth/login` JWT login
- `GET /api/stats` dashboard stats (JWT protected)
- `GET /api/users` list users (JWT protected)
- `POST /api/users` create user (JWT protected)
- `PUT /api/users/{id}` update user (JWT protected)
- `DELETE /api/users/{id}` delete user (JWT protected)

## Views and assets
### Views
- Auth: `app/Views/auth/*` (login/register/forgot password + errors)
- Dashboard: `app/Views/dashboard/*`
- Users: `app/Views/users/*`
- Super Admin Address: `app/Views/super_admin/address/*`
- Layouts and partials: `app/Views/layouts/*`, `app/Views/partials/*`

### Assets
- CSS: `public/css/input.css` (Tailwind source), `public/css/app.css` (compiled output), `public/css/auth.css`, `public/css/toast.css`
- JS: `public/js/app.js`, `public/js/auth.js`, `public/js/modal.js`, `public/js/search.js`, `public/js/toast.js`
- Image: `public/images/favicon.svg`

## Database schema
Schema SQL lives in `database/migrations/` and is auto-applied by `install.sh` in filename order.

Reference:
- `001_create_users_and_activity_logs.sql`
- `002_create_password_resets.sql`
- Detailed table/column docs: `database/SCHEMA.md`

## Notes
- Update `.env` `APP_URL` to match your host.
- For production, use HTTPS and set `APP_DEBUG=false`.
