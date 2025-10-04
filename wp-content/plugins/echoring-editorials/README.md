# EchoRing Editorials Plugin

This plugin creates a custom post type for managing editorial interviews on the EchoRing website.

## Features

- **Custom Post Type**: Creates a new post type called "Editorials"
- **Custom Fields**: Adds interviewer, editorial type, and download link fields
- **Templates**: Includes custom single and archive templates
- **Shortcode**: Provides [editorials_table] shortcode for displaying editorials in table format

## Usage

### Creating Editorials

1. After activating the plugin, go to **Editorials > Add New**
2. Add a title (this will be the subject of the editorial)
3. Add the interview content in the main editor
4. Fill in the "Editorial Details" meta box:
   - **Interviewer**: Name of the person conducting the interview
   - **Editorial Type**: Type of editorial (e.g., "Interview", "Review")
   - **Download Link**: Optional download link for additional materials

### Displaying Editorials

#### Method 1: Using the Archive Page
The plugin automatically creates an archive page at `/editorial/` that displays all editorials in a table format.

#### Method 2: Using the Shortcode
Add the `[editorials_table]` shortcode to any page or post to display a table of editorials.

**Shortcode Parameters:**
- `count`: Number of editorials to show (-1 for all, default: -1)
- `orderby`: How to sort the editorials (date, title, etc., default: date)
- `order`: Sort order (ASC or DESC, default: DESC)

**Examples:**
```
[editorials_table]
[editorials_table count="5"]
[editorials_table orderby="title" order="ASC"]
```

### Template Structure

The plugin includes custom templates:
- `single-editorial.php`: Template for individual editorial pages
- `archive-editorial.php`: Template for the editorial archive page

These templates maintain the EchoRing site's design and styling.

## Installation

1. Upload the `echoring-editorials` folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress admin
3. The custom post type will be available immediately
4. Flush rewrite rules by visiting **Settings > Permalinks** (just visit the page to flush)

## Styling

The plugin uses the existing EchoRing theme styling and maintains the site's table-based layout format with Verdana font as requested.