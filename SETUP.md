# Silkpanel CMS - Filament v5 Setup

This project now includes **Filament v5** admin panel with **Spatie Laravel Permission** for role-based access control and **Laravel Breeze** for standard authentication.

## Features

- ✅ **Filament v5** Admin Panel at `/admin`
- ✅ **Spatie Laravel Permission** for role-based access control
- ✅ **Laravel Breeze** for standard authentication at `/login` and `/register`
- ✅ Two roles: **Admin** and **Customer**
- ✅ Admins can access the Filament admin panel
- ✅ Customers can register and login but cannot access `/admin`

## Authentication Routes

### Standard Authentication (Laravel Breeze)
- **Login**: `/login`
- **Register**: `/register`
- **Dashboard**: `/dashboard` (accessible to all authenticated users)
- **Profile**: `/profile` (accessible to all authenticated users)

### Admin Panel (Filament)
- **Admin Dashboard**: `/admin` (accessible only to users with Admin role)
- **Admin Login**: `/admin/login` (separate login for admin panel)
- **User Management**: `/admin/users` (manage users and assign roles)

## Roles

### Admin
- Full access to Filament admin panel at `/admin`
- Can manage users and assign roles
- Can access all administrative features

### Customer
- Can register and login through `/login`
- Can access `/dashboard` and `/profile`
- **Cannot** access `/admin` (will receive 403 Forbidden error)

## Test Accounts

After running migrations with seed, the following test accounts are created:

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password | Admin |
| customer@example.com | password | Customer |

## Setup Instructions

1. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run migrations and seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Build assets:**
   ```bash
   npm run build
   ```

5. **Start the development server:**
   ```bash
   php artisan serve
   ```

## Usage

### For Customers
1. Visit `/register` to create a new account
2. Login at `/login`
3. Access your dashboard at `/dashboard`
4. You will **not** be able to access `/admin`

### For Admins
1. Login at `/admin/login` with admin credentials
2. Access the admin panel at `/admin`
3. Manage users at `/admin/users`
4. Assign Admin or Customer roles to users

## Assigning Roles

### Via Filament Admin Panel
1. Login to `/admin`
2. Navigate to **Users**
3. Edit a user
4. Select role(s) from the **Roles** dropdown
5. Save

### Programmatically
```php
use App\Models\User;

// Assign Admin role
$user = User::find(1);
$user->assignRole('Admin');

// Assign Customer role
$user = User::find(2);
$user->assignRole('Customer');

// Check if user has role
if ($user->hasRole('Admin')) {
    // User is an admin
}
```

## Security

- Admin panel is protected by `FilamentAdminMiddleware` which checks for Admin role
- Only authenticated users with the Admin role can access `/admin`
- Customers attempting to access `/admin` will receive a 403 Forbidden error
- Standard authentication is handled by Laravel Breeze with CSRF protection

## Customization

### Adding New Roles
1. Edit `database/seeders/RolesAndPermissionsSeeder.php`
2. Add new roles:
   ```php
   Role::firstOrCreate(['name' => 'NewRole']);
   ```
3. Run the seeder:
   ```php
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

### Adding Permissions
1. Edit `database/seeders/RolesAndPermissionsSeeder.php`
2. Create permissions:
   ```php
   Permission::firstOrCreate(['name' => 'edit articles']);
   Permission::firstOrCreate(['name' => 'delete articles']);
   ```
3. Assign to roles:
   ```php
   $role = Role::findByName('Admin');
   $role->givePermissionTo('edit articles');
   ```

## Documentation

- [Filament Documentation](https://filamentphp.com/docs)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze)
