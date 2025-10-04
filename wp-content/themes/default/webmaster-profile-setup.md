# Webmaster Profile System Setup

## Overview
The webmaster profile system has been implemented with the following features:

1. **Role-based dashboard redirection** - Admins go to WordPress dashboard, webmasters go to frontend profile
2. **Custom frontend profile page** - Webmasters can edit their profile and submit website updates
3. **Update submission system** - Webmasters can submit updates for admin approval
4. **Admin approval workflow** - Admins can review and approve/reject updates

## Files Created/Modified

### Modified Files:
- `header.php` - Updated dashboard link to use role-based redirection
- `functions.php` - Added custom post type and meta boxes for site updates

### New Files Created:
- `webmaster-profile.php` - Frontend profile page template
- `create-profile-page.php` - Helper script to create the profile page

## Setup Instructions

### 1. Create the Profile Page
The system needs a WordPress page with the slug "webmaster-profile". You have two options:

**Option A: Manual Creation**
1. Go to WordPress Admin → Pages → Add New
2. Set title: "Webmaster Profile"
3. Set slug: "webmaster-profile"
4. Select template: "Webmaster Profile"
5. Publish the page

**Option B: Run the Helper Script**
1. Navigate to: `http://your-site.com/wp-content/themes/default/create-profile-page.php`
2. This will automatically create the page and set the template
3. Delete the file after running it once

### 2. Test the System

**Test Admin Access:**
1. Log in as an admin
2. You should see "Dashboard" link in the header
3. Clicking it should take you to WordPress admin

**Test Webmaster Access:**
1. Log in as a webmaster (non-admin user)
2. You should see "My Profile" link in the header
3. Clicking it should take you to the frontend profile page

### 3. Features Available

**Webmaster Profile Page:**
- Edit profile information (name, email, bio)
- Submit new website updates
- View submitted sites and updates
- Track update approval status

**Admin Features:**
- Review all submitted updates in WordPress admin
- Approve or reject updates
- Manage site updates through custom post type

### 4. Technical Details

**Custom Post Type:**
- Name: `site_update`
- Features: title, editor, author, custom fields
- Status: pending (by default), publish, draft

**Custom Fields:**
- `site_url` - URL of the website being updated
- `update_type` - Type of update (content, design, feature, fix, other)
- `priority` - Priority level (low, medium, high, urgent)

**Template Files:**
- Uses `webmaster-profile.php` for the frontend profile
- Uses WordPress admin for backend management

### 5. Security Features
- Nonce verification for all forms
- Capability checks for admin access
- Sanitization of all user inputs
- Proper escaping of all outputs

### 6. Next Steps
After setup, you may want to:
1. Customize the styling to match your theme
2. Add additional fields to the update form
3. Set up email notifications for new updates
4. Create approval/rejection workflows
5. Add bulk actions for admin approval

## Troubleshooting

**Page Not Found:**
- Go to Settings → Permalinks and click "Save Changes" to flush rewrite rules

**Template Not Loading:**
- Ensure the page template is set to "Webmaster Profile"
- Check that the page slug is exactly "webmaster-profile"

**Access Issues:**
- Verify user roles and capabilities
- Check that non-admin users are redirected correctly

The system is now ready for use! Webmasters can access their profile and submit updates while admins retain full control over the approval process.