# CKU Scan Application - Modern UI Setup

This application has been refactored to use **Tailwind CSS** and **shadcn/ui** components for a modern, responsive design.

## 🚀 Quick Start

### Prerequisites
- Node.js (v16 or higher)
- npm or yarn
- PHP 7.4 or higher
- Web server (Apache/Nginx) or Laragon/XAMPP

### Installation

1. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

2. **Build the CSS:**
   ```bash
   # For development (with watch mode)
   npm run dev
   
   # For production (minified)
   npm run build-prod
   ```

3. **Start your web server** and navigate to the application.

## 📁 Project Structure

```
cku_scan/
├── app/                          # PHP application files
│   ├── controllers/              # PHP controllers
│   ├── common/                   # Shared components (header, footer)
│   ├── index.php                 # Login page (refactored)
│   ├── dash.php                  # Dashboard layout (refactored)
│   └── scan.php                  # Scanner page (refactored)
├── includes/
│   ├── css/
│   │   └── output.css            # Generated Tailwind CSS
│   ├── js/                       # JavaScript files
│   └── ui/                       # shadcn/ui components
├── src/
│   └── input.css                 # Tailwind source file
├── tailwind.config.js            # Tailwind configuration
├── components.json               # shadcn/ui configuration
└── package.json                  # Node.js dependencies
```

## 🎨 UI Components

The application now uses modern UI components:

- **Login Page**: Clean, centered design with gradient background
- **Dashboard**: Modern sidebar navigation with card-based layout
- **Scanner**: Professional scanner interface with responsive tables
- **Navigation**: Clean header with dropdown user menu
- **Tables**: Responsive tables with sticky headers
- **Forms**: Modern form inputs with focus states
- **Buttons**: Consistent button styling with hover effects

## 🛠️ Development

### CSS Development
- Edit `src/input.css` for global styles
- Run `npm run dev` for live CSS compilation
- Use Tailwind utility classes throughout the application

### Adding New Components
1. Create component files in `includes/ui/`
2. Follow the existing pattern for component functions
3. Import and use in PHP templates

### Customization
- Modify `tailwind.config.js` for theme customization
- Update `src/input.css` for global styles
- Customize component styles in the respective UI files

## 🔧 Key Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern UI**: Clean, professional interface
- **Accessibility**: Proper focus states and keyboard navigation
- **Performance**: Optimized CSS with Tailwind's purging
- **Maintainable**: Component-based architecture

## 📱 Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🚨 Important Notes

1. **CSS Compilation**: Always run `npm run build-prod` after making changes to `src/input.css`
2. **PHP Compatibility**: The application maintains full PHP functionality
3. **Database**: All existing database connections and queries remain unchanged
4. **JavaScript**: jQuery is still included for existing functionality

## 🔄 Migration Notes

The following changes were made during refactoring:

- **Removed**: Bootstrap CSS and JS dependencies
- **Added**: Tailwind CSS and shadcn/ui components
- **Updated**: All HTML templates with modern CSS classes
- **Maintained**: All PHP functionality and database operations
- **Improved**: Responsive design and accessibility

## 📞 Support

For issues or questions about the refactored UI:
1. Check the browser console for JavaScript errors
2. Ensure CSS is properly compiled (`npm run build-prod`)
3. Verify all PHP includes and paths are correct
4. Test database connectivity separately
