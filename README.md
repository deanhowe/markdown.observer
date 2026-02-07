# Markdown.Observer

Transform your Markdown files into a collaborative, version-controlled content management system.
Edit in Markdown or rich text, preview in real-time, and maintain your files as the single source of truth.

## 🚀 MVP Features

The current MVP (Minimum Viable Product) focuses on showcasing the core functionality of Markdown.Observer:

- **Composer Package Slideshow**
  - Displays all installed composer packages on the homepage
  - Shows package information (name, description, version)
  - Renders README.md files with proper formatting and syntax highlighting
  - Features optimized image loading for package logos
  - Provides intuitive navigation controls

![Composer Package Slideshow](docs/images/slideshow-screenshot.png)
*Note: Replace with actual screenshot*

## 🌟 Key Features

- **Dual Editing Modes**
  - Native Markdown editing
  - Rich text editing with TipTap
  - Real-time preview
  - Seamless switching between modes

- **Version Control**
  - Full history of all changes
  - Track who made what changes and when
  - Easily revert to previous versions
  - Compare different versions

- **File System as Source of Truth**
  - Your Markdown files remain the canonical source
  - No vendor lock-in
  - Works with your existing Markdown files
  - Perfect for documentation, blogs, and content sites

- **API-First Architecture**
  - RESTful API for all operations
  - Markdown to HTML conversion using Spatie's powerful tools
  - TipTap JSON transformation for rich editing
  - Webhook support for integration with your workflows

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- SQLite or MySQL

### Installation

1. **Clone the repository**

```bash
git clone https://github.com/deanhowe/markdown.observer.git
cd markdown.observer
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install JavaScript dependencies**

```bash
npm install
```

4. **Configure your environment**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Set up the database**

```bash
touch database/database.sqlite
php artisan migrate
```

6. **Start the development server**

```bash
# Run the development server with hot reloading
composer dev

# Or run with server-side rendering
composer dev:ssr
```

### Viewing the Composer Package Slideshow

Once the development server is running, you can view the composer package slideshow on the homepage:

1. Open your browser and navigate to `http://localhost:8000`
2. The slideshow will appear on the homepage, displaying your installed composer packages
3. Use the navigation controls to browse through the packages
4. Each package will display its logo, name, description, version, and README content

# 📖 How It Works

1. **Load**: Your Markdown files are loaded through our API
2. **Convert**: Files are converted to both HTML and TipTap JSON
3. **Edit**: Make changes in either Markdown or rich text mode
4. **Preview**: See changes in real-time
5. **Save**: Changes are version-controlled in the database
6. **Publish**: When ready, your Markdown files are updated

## 💰 Pricing

- **Personal**: $9/month
    - Unlimited files
    - Single user
    - Basic version control

- **Team**: $29/month
    - 5 team members
    - Advanced version control
    - Team collaboration features

- **Enterprise**: Contact us
    - Custom user limits
    - Priority support
    - Custom integration support

## 🔧 Tech Stack

- Laravel 12
- React 19
- TipTap Editor
- Spatie Markdown
- SQLite/MySQL

## 📝 License

[MIT License](LICENSE.md)

---

## 🤝 Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## 💬 Support

- Documentation: [docs.markdown.observer](https://docs.markdown.observer)
- Issues: [GitHub Issues](https://github.com/deanhowe/markdown.observer/issues)
~~- Discord: [Join our community](https://discord.gg/markdown.observer)~~

## ⭐ Star Us!

If you find Markdown.Observer useful, please star us on GitHub! It helps others discover the project.
