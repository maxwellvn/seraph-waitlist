# PHP Boilerplate - Minimalist Theme

A **starter template** and **architectural pattern** for building modern PHP applications without heavy frameworks. This boilerplate provides a proven structure, routing system, and component-based architecture while keeping things simple and maintainable.

## 🎯 What is a Boilerplate?

A boilerplate is a **reusable template** that provides:
- ✅ **Pre-built structure** - Organized folders and file conventions
- ✅ **Common patterns** - Routing, database, components already implemented
- ✅ **Best practices** - Security, session handling, URL rewriting configured
- ✅ **Starting point** - Copy and customize for your project needs

**This is NOT a framework** - it's a lightweight starting point that you fully control and customize.

## 🎨 Design Philosophy

This boilerplate follows strict design principles:

### Minimalist Visual Design
- **No Gradients**: Only solid colors (gray-900, white, gray-600)
- **No Shadows**: Borders (`border-2`) for visual hierarchy
- **No Rounded Corners**: Clean, sharp edges for professional look
- **Black & White Palette**: Strong contrast with gray scale
- **Border-Based Design**: `border-gray-900` and `border-gray-200` for structure

### Multi-Font Typography System
- **Playfair Display** (`font-playfair`) - Large headings, hero titles, numbers
- **Poppins** (`font-poppins`) - Subheadings, buttons, labels, navigation
- **Inter** (`font-inter`) - Body text, paragraphs, form inputs
- **Roboto** (`font-roboto`) - Descriptions, captions, subtitles

### Architectural Principles
- **No MVC Framework**: Simple, direct PHP without OOP complexity
- **Component-Based**: Reusable functions that return HTML
- **Router-Centric**: Clean URLs with `.htaccess` rewriting
- **JSON Storage**: File-based database (no SQL server needed)
- **Separation of Concerns**: Config, routing, components, pages separated

## 📁 Project Structure & Rules

This boilerplate follows a **strict folder structure** with specific purposes:

```
as-one-man/
├── config/              # Configuration & Database
│   ├── config.php       # App settings, constants, session config
│   └── database.php     # JSON database class (CRUD operations)
│
├── router/              # Routing System
│   └── Router.php       # URL router with GET/POST and dynamic params
│
├── includes/            # Helper Functions
│   └── helpers.php      # Utility functions (e(), redirect(), etc.)
│
├── components/          # Reusable Components
│   ├── header.php       # Header with navigation (nav bar)
│   ├── footer.php       # Footer (closes <main> and <body>)
│   ├── layout.php       # Layout wrapper function
│   ├── alert.php        # Alert component function
│   └── card.php         # Card component functions
│
├── pages/               # Page Content
│   ├── home.php         # Homepage content (no layout wrapper)
│   ├── about.php        # About page content
│   ├── contact.php      # Contact form page
│   ├── user.php         # User profile (dynamic route)
│   └── 404.php          # 404 error page
│
├── data/                # JSON Database Files
│   ├── .gitkeep         # Keeps folder in git
│   └── *.json           # JSON files (users.json, contacts.json, etc.)
│
├── public/              # Public Assets (accessible via URL)
│   └── assets/
│       ├── css/         # Custom stylesheets
│       ├── js/          # JavaScript files
│       └── images/      # Image files
│
├── .rules/              # Template rules & detailed guides
│   ├── README.md        # Rules overview
│   ├── architecture.md  # Architecture patterns
│   ├── design-system.md # Visual design system
│   ├── routing.md       # Routing patterns
│   ├── components.md    # Component creation
│   ├── database.md      # Database operations
│   ├── security.md      # Security best practices
│   ├── code-style.md    # Code style guide
│   └── workflow.md      # Development workflow
├── .htaccess            # Apache URL rewriting & security rules
├── .cursorrules         # Development rules & patterns
├── index.php            # Main entry point (routing happens here)
└── README.md            # This file
```

## 📋 Architectural Rules

### 1. **Routing Rules** (`index.php`)
- ✅ All routes defined in `index.php`
- ✅ Routes use callback functions, not controllers
- ✅ Use `renderLayout()` to wrap pages with header/footer
- ✅ Global `$db` for database access in routes
- ❌ Never create controller classes
- ❌ Never put business logic directly in routes (use includes)

### 2. **Component Rules** (`components/`)
- ✅ Components are PHP functions that return HTML strings
- ✅ Use heredoc syntax (`<<<HTML ... HTML;`)
- ✅ All components must include font classes
- ✅ No gradients - only solid colors
- ✅ Use `border-2` instead of shadows
- ❌ Never echo inside component functions
- ❌ Never use inline CSS

### 3. **Page Rules** (`pages/`)
- ✅ Pages contain only content HTML (no `<html>`, `<head>`, `<body>`)
- ✅ Header and footer added automatically by `renderLayout()`
- ✅ Access passed variables via `$variableName`
- ✅ Use component functions for reusable elements
- ❌ Never include header.php or footer.php manually
- ❌ Never start with `<?php session_start(); ?>`

### 4. **Database Rules** (`data/`)
- ✅ One JSON file per collection (users.json, posts.json, etc.)
- ✅ Use `$db->insert()`, `$db->find()`, `$db->update()`, `$db->delete()`
- ✅ Auto-increment IDs handled by Database class
- ✅ Pretty-print JSON for readability
- ❌ Never use SQL or external databases
- ❌ Never access JSON files directly with `file_get_contents()`

### 5. **Configuration Rules** (`config/`)
- ✅ Load `config.php` BEFORE `session_start()`
- ✅ Use constants for paths (BASE_PATH, DATA_PATH, etc.)
- ✅ Session settings in config.php before session starts
- ❌ Never hardcode paths in files
- ❌ Never call `session_start()` before loading config

### 6. **Design System Rules**
- ✅ **Colors**: gray-900, white, gray-600, gray-200, gray-100
- ✅ **Borders**: `border-2`, `border-4`, `border-l-4`, `border-b-2`
- ✅ **Fonts**: Use all 4 fonts appropriately
- ✅ **Buttons**: `bg-gray-900 text-white hover:bg-gray-800`
- ❌ **Never** use gradients (`bg-gradient-*`)
- ❌ **Never** use shadows (`shadow-*`)
- ❌ **Never** use rounded corners (`rounded-*`) except images
- ❌ **Never** use colors outside the gray scale

## 🚀 Features

- ✅ **Clean URL Routing** - RESTful routes with dynamic parameters
- ✅ **JSON Database** - File-based storage, no SQL required
- ✅ **Tailwind CSS CDN** - No build process needed
- ✅ **Multiple Fonts** - Inter, Poppins, Playfair Display, Roboto
- ✅ **Minimalist Design** - No gradients, clean borders
- ✅ **Component System** - Reusable PHP components
- ✅ **Helper Functions** - Common utilities included
- ✅ **Mobile Responsive** - Works on all devices
- ✅ **URL Rewriting** - Clean URLs with .htaccess
- ✅ **Session Management** - Built-in session handling
- ✅ **Form Handling** - Example contact form with validation

## 🛠️ Installation

### Requirements

- PHP 7.4 or higher
- Apache with mod_rewrite enabled
- Write permissions for `data/` directory

### Setup

1. **Clone or download** this repository to your web server directory:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/
   ```

2. **Configure Base URL** in `config/config.php`:
   ```php
   define('BASE_URL', '/as-one-man/');
   ```

3. **Update .htaccess** if needed (check RewriteBase):
   ```apache
   RewriteBase /as-one-man/
   ```

4. **Set Permissions** for data directory:
   ```bash
   chmod 755 data/
   ```

5. **Access your application**:
   ```
   http://localhost/as-one-man/
   ```

## 🎯 Usage

### Creating Routes

Add routes in `index.php`:

```php
// Simple GET route
$router->get('/example', function() {
    renderLayout(PAGES_PATH . 'example.php', [
        'pageTitle' => 'Example Page'
    ]);
});

// Route with parameter
$router->get('/post/:id', function($id) {
    // Your logic here
});

// POST route
$router->post('/submit', function() {
    // Handle form submission
});
```

### Using the JSON Database

```php
global $db;

// Insert data
$db->insert('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Find all records
$users = $db->find('users');

// Find with criteria
$user = $db->findOne('users', ['id' => 1]);

// Update records
$db->update('users', ['id' => 1], ['name' => 'Jane Doe']);

// Delete records
$db->delete('users', ['id' => 1]);
```

### Creating Components

Components are simple PHP functions that return HTML:

```php
// In components/mycomponent.php
function myComponent($title, $content) {
    return <<<HTML
    <div class="bg-white border-2 border-gray-200 p-6">
        <h3 class="text-xl font-bold font-poppins">{$title}</h3>
        <p class="text-gray-600 font-inter">{$content}</p>
    </div>
HTML;
}

// Load it in index.php
require_once COMPONENTS_PATH . 'mycomponent.php';

// Use it in pages
echo myComponent('Title', 'Content');
```

### Helper Functions

Available helper functions in `includes/helpers.php`:

```php
e($string)                    // Escape HTML
redirect($url)                // Redirect to URL
isActive($path)               // Check if current page
formatDate($date, $format)    // Format date
flash($key, $value)           // Session flash messages
dd($data)                     // Debug and die
isPost()                      // Check if POST request
asset($path)                  // Generate asset URL
url($path)                    // Generate URL
```

## 🎨 Font Usage

The boilerplate includes 4 Google Fonts:

- **Inter** - Body text, paragraphs (`font-inter`)
- **Poppins** - Buttons, labels, navigation (`font-poppins`)
- **Playfair Display** - Headings, titles (`font-playfair`)
- **Roboto** - Descriptions, subtitles (`font-roboto`)

Example usage:
```html
<h1 class="font-playfair">Main Heading</h1>
<p class="font-inter">Body text content</p>
<button class="font-poppins">Click Me</button>
```

## 🎨 Design System

### Colors

- **Primary Black**: `bg-gray-900` / `text-gray-900`
- **White**: `bg-white` / `text-white`
- **Gray Text**: `text-gray-600`
- **Light Gray**: `text-gray-300`
- **Borders**: `border-gray-200` / `border-gray-900`

### Components Style

- Borders: `border-2`
- Hover: `hover:border-gray-900` or `hover:bg-gray-800`
- No rounded corners (minimalist)
- No shadows (use borders)
- No gradients

## 📄 Configuration

### config/config.php

```php
define('APP_NAME', 'My PHP App');      // Application name
define('APP_VERSION', '1.0.0');        // Version
define('APP_ENV', 'development');      // Environment
define('BASE_URL', '/as-one-man/');    // Base URL path
```

### Security Features

- Session cookie httponly enabled
- Strict session mode enabled
- Protected directories (.htaccess blocks access to sensitive files)
- HTML escaping helper function
- CSRF protection ready (implement as needed)

## 🔒 Protected Directories

The `.htaccess` file prevents direct access to:
- `/data/` - JSON database files
- `/config/` - Configuration files
- `/includes/` - Helper files
- `/router/` - Router files
- `/components/` - Component files

## 🧪 Example Routes

- `/` - Homepage
- `/about` - About page
- `/contact` - Contact form
- `/user/1` - User profile (dynamic parameter)

## 📚 Detailed Documentation

For comprehensive guides on each aspect of the boilerplate:

- **[Architecture](.rules/architecture.md)** - Folder structure, patterns, request lifecycle
- **[Design System](.rules/design-system.md)** - Colors, typography, components, layouts
- **[Routing](.rules/routing.md)** - Routes, forms, authentication, APIs
- **[Components](.rules/components.md)** - Creating and using components
- **[Database](.rules/database.md)** - CRUD operations, patterns, validation
- **[Security](.rules/security.md)** - Input validation, passwords, sessions, CSRF
- **[Code Style](.rules/code-style.md)** - PHP, HTML, CSS, JavaScript standards
- **[Workflow](.rules/workflow.md)** - Development, testing, deployment

## 📝 Creating a New Page

1. **Create page file** in `pages/`:
   ```php
   <!-- pages/mypage.php -->
   <div class="max-w-7xl mx-auto px-4 py-16">
       <h1 class="text-4xl font-bold font-playfair">My Page</h1>
       <p class="text-gray-600 font-inter">Content here</p>
   </div>
   ```

2. **Add route** in `index.php`:
   ```php
   $router->get('/mypage', function() {
       renderLayout(PAGES_PATH . 'mypage.php', [
           'pageTitle' => 'My Page - ' . APP_NAME
       ]);
   });
   ```

3. **Add navigation link** (optional) in `components/header.php`

## 🤝 Contributing

Feel free to customize and extend this boilerplate for your needs!

## 📜 License

Open source - use freely for personal or commercial projects.

## 🎯 Best Practices

1. **Keep it simple** - No unnecessary complexity
2. **Use components** - Reuse code through components
3. **Type safety** - Use type hints where PHP allows
4. **Security first** - Always sanitize user input
5. **Consistent styling** - Follow the minimalist design system
6. **Mobile first** - Design for mobile, enhance for desktop

## 🐛 Troubleshooting

### Session warnings
If you see session ini settings warnings, make sure `config.php` is loaded before `session_start()`.

### .htaccess not working
Enable mod_rewrite in Apache:
```bash
a2enmod rewrite
sudo service apache2 restart
```

### Data directory permissions
```bash
chmod 755 data/
```

### Clean URLs not working
Check your Apache configuration allows `.htaccess` overrides:
```apache
AllowOverride All
```

## 📧 Support

For issues and questions, check the documentation or create an issue in your repository.

---

**Built with ❤️ using plain PHP and Tailwind CSS**

