# EchoRing Sites Plugin

A custom WordPress plugin for managing website reviews and details without depending on external plugins like Advanced Custom Fields.

## Features

### Custom Post Type: "Site"
- Creates a dedicated "Sites" post type for managing website reviews
- Supports title, content editor, featured images, and comments
- Custom URL structure: `/site/your-site-name/`

### Custom Fields
1. **Site Details**
   - Site URL (with validation)
   - Webmaster name
   - Screenshots (multiple uploaded images)

2. **Ratings & Categories**
   - Rating selection (configurable options)
   - Site types (multiple selection)
   - Site languages (multiple selection - English, Spanish, etc.)

3. **Site Content & Features**
   - Games count
   - Apps count
   - Site features (multiple selection)

3. **Reviews**
   - "The Good" points (repeater field)
   - "The Bad" points (repeater field)

### Settings Page
Accessible via Sites → Settings in the WordPress admin.

#### Feature Toggles
- Enable/disable screenshots
- Enable/disable ratings
- Enable/disable types
- Enable/disable languages
- Enable/disable games
- Enable/disable apps
- Enable/disable features
- Enable/disable reviews

#### Options Configuration
- Rating options (one per line)
- Type options (one per line)
- Language options (one per line - e.g., English, Spanish, French)
- Feature options (one per line)

### Custom Template
- Automatically loads a custom template for single site posts
- Maintains the original EchoRing layout and styling
- Respects feature toggle settings

## Installation

1. Upload the `echoring-sites` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings at Sites → Settings

## Usage

### Creating a New Site
1. Go to Sites → Add New
2. Enter the site title and description
3. Fill in the Site Details meta box
4. Select ratings, types, and languages
5. Add review points in "The Good" and "The Bad" sections
6. Publish the site

### Customizing Options
1. Go to Sites → Settings
2. Modify the rating, type, and language options
3. Toggle features on/off as needed
4. Save changes

## Template Functions

The plugin provides static helper functions for use in themes:

```php
// Get site URL
$site_url = EchoRingSites::get_site_url($post_id);

// Get webmaster
$webmaster = EchoRingSites::get_webmaster($post_id);

// Get screenshots
$screenshots = EchoRingSites::get_screenshots($post_id);

// Get rating
$rating = EchoRingSites::get_rating($post_id);

// Get types
$types = EchoRingSites::get_types($post_id);

// Get languages
$languages = EchoRingSites::get_languages($post_id);

// Get games count
$games = EchoRingSites::get_games($post_id);

// Get apps count
$apps = EchoRingSites::get_apps($post_id);

// Get features
$features = EchoRingSites::get_features($post_id);

// Get good points
$the_good = EchoRingSites::get_the_good($post_id);

// Get bad points
$the_bad = EchoRingSites::get_the_bad($post_id);
```

## Migration from Advanced Custom Fields

If you're migrating from ACF, the plugin uses the same field names but stores them as custom post meta:

- `screenshots` → `_screenshots` (now stores attachment IDs instead of URLs)
- `site_url` → `_site_url`
- `webmaster` → `_webmaster`
- `rating` → `_rating`
- `type` → `_types`
- `language` → `_languages`
- `games` → `_games`
- `apps` → `_apps`
- `features` → `_features`
- `the_good` → `_the_good`
- `the_bad` → `_the_bad`

## Screenshot Upload Feature

The plugin now supports WordPress media uploads for screenshots instead of URL links:

- Users can upload images directly through the WordPress media library
- Screenshots are stored as WordPress attachments
- The plugin automatically handles image resizing and optimization
- Better integration with WordPress core functionality

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Changelog

### Version 1.1.0
- **NEW**: Screenshot upload functionality using WordPress media library
- **IMPROVED**: Better image handling with automatic resizing
- **FIXED**: Migration from URL-based screenshots to attachment IDs
- **ENHANCED**: Better admin interface for screenshot management

### Version 1.0.0
- Initial release
- Custom post type registration
- Custom meta boxes
- Settings page with feature toggles
- Custom template integration
- Helper functions for theme development 