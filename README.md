# ATS Dashboard

ATS Dashboard is a WordPress admin customization plugin by [Arcane Tech](https://arcanetechct.com/). Build a branded workspace for site owners with custom dashboard widgets, admin pages, navigation, login screens, and notification emails.

Configure the plugin from the **Arcane Tech** menu in WordPress. Optional modules can be enabled or disabled independently under **Arcane Tech → Modules**.

## Features

### Dashboard widgets and layouts

- Create text, HTML, RSS, video, icon, and contact form widgets.
- Control widget placement, priority, and visibility by user role or individual user.
- Remove all default dashboard widgets or select individual widgets to hide.
- Customize dashboard columns, widget ordering, the welcome panel, and the dashboard headline.
- Style widget backgrounds, headings, text, links, borders, rounded corners, and shadows.
- Use saved builder templates for dashboard layouts, with assignments by user role.
- Add custom CSS for the dashboard and the wider admin area.

The built-in **Site Overview** widget replaces WordPress's **At a Glance** widget. It shows published post and page counts, users, comments awaiting moderation, upload storage usage, HTTPS status, maintenance status, and the latest content change. Site Owner counts appear when that role is enabled and has users. The widget can be disabled in settings.

The overview uses local WordPress data: its security indicator reflects HTTPS on the current request, and its storage figure covers the uploads directory.

### Branding and appearance

- Choose default or modern admin layouts.
- Set toolbar and block editor logos, logo links, navigation colors, and an accent color.
- Customize admin footer and version text.
- Enable dark styling for the admin area and block editor.
- Optionally enforce the configured admin theme for all users and remove the profile color scheme picker.
- Customize or remove the “Howdy” greeting and hide Help and Screen Options tabs.

### Login customization and redirects

Use the WordPress Customizer to adjust login templates, backgrounds, overlays, logos, form layout, fields, labels, buttons, footer links, and custom CSS with a preview.

The separate **Login Redirect** module supports a custom login URL, post-login destinations by role, and a redirect for logged-out visitors requesting `/wp-admin/`.

### Admin pages and navigation

- Create custom admin pages using the default editor or HTML editor.
- Configure page menu placement, icons, role visibility, and custom JavaScript.
- Rearrange, hide, or add admin menu items and submenus for roles and individual users.
- Customize toolbar items and submenus, or hide the toolbar for selected roles.
- Use dynamic placeholders in supported content and navigation fields.

Saving or importing custom JavaScript and HTML script embeds requires WordPress's `unfiltered_html` capability. On Multisite this normally means a Super Admin. Other page editors can still edit permitted content; their saves and imports leave existing custom JavaScript unchanged.

### Site Owner role

Create an optional **Site Owner** role with a configurable display name. Its capabilities start from the Administrator role, then exclude unchecked capabilities from the plugin's configurable groups: plugins, themes, settings and tools, core and files, and users.

Among those configurable capabilities, only viewing the user list is granted by default. Enable and configure the role under **Arcane Tech → Settings**, then assign it to users through WordPress user management.

### Notice Bell

Collect WordPress and plugin admin notices into a toolbar notification bell. This module operates on site admin screens when the toolbar is visible; it does not run in Network Admin.

### Branded email notifications

Configure a shared logo, header and accent colors, support details, and footer, then customize supported notification content with merge tags and browser previews.

| Notification | Customization |
| --- | --- |
| Welcome / new user notification | Branded HTML, subject, heading, body, and button text |
| Password reset request | Branded HTML, subject, heading, body, and button text |
| Email address change confirmation | Plain-text body only; WordPress controls the subject |
| New user registered, admin copy | Branded HTML and editable content |
| Password changed, admin notification | Branded HTML and editable content |
| Comment awaiting moderation | Branded HTML and editable content |
| New comment, author notification | Branded HTML and editable content |

Each email customization is **off by default**, independently of the module toggle. Enabling a customization replaces the content of that WordPress notification; disabling it leaves the standard notification in place. Keep the `{action_url}` tag in the email address change confirmation so recipients can complete the change.

Email delivery uses the site's existing WordPress mail setup. ATS Dashboard provides templates; Postmark delivery is configured separately.

## Installation

1. Download the repository ZIP, or clone the repository into your WordPress plugins directory:

   ```sh
   git clone https://github.com/ArcaneTechCT/ats-dashboard.git wp-content/plugins/ats-dashboard
   ```

2. If using a ZIP, extract it and name the plugin folder `ats-dashboard`. Place it in `wp-content/plugins/`, or upload an installable ZIP containing that folder through **Plugins → Add New Plugin → Upload Plugin**.
3. Activate **ATS Dashboard** in WordPress.
4. Open **Arcane Tech → Settings**.

Runtime assets are included in the repository. Node.js and npm are only needed when developing the login customizer's TypeScript assets.

The release workflow declares WordPress **5.8+** and PHP **7.4+** in its update manifest. These are declared requirements, not a verified compatibility matrix for every module or third-party builder.

## Quick start

1. Open **Arcane Tech → Modules** and choose the optional features to use. All optional modules default to enabled on a fresh configuration.
2. Open **Customization**, enable branding, and configure the logo, layout, and colors.
3. Open **Dashboard Widgets** to configure the dashboard and manage custom widgets. Add a welcome or support widget, select its visibility, and publish it.
4. Adjust login styling and redirects if needed, keeping the new login URL available for future access.
5. Configure and enable individual templates in **Email Notifications**, using previews to review their content.
6. Check the resulting dashboard with an account that has the intended client role.

## Builder support

The code includes content rendering and template support for the WordPress block editor, Elementor, Beaver Builder, Divi, Brizy, Bricks, Oxygen, and Breakdance. Builder-specific content requires the corresponding plugin or theme to be installed and active.

Block templates are managed by the plugin's `ats_block_template` post type. Custom widgets and admin pages use `ats_widgets` and `ats_admin_page`, respectively.


## WordPress Multisite

Network settings are available under **Network Admin → Settings → ATS Dashboard**.

- Select a blueprint site to supply shared dashboard configuration.
- Exclude specific subsites by ID.
- Configure widget ordering across the network.
- Choose whether plugin management is available to Super Admins or Administrators.

The multisite module includes output handlers for widgets, settings, branding, login customization and redirects, admin pages, menus, and the toolbar. Builder content inherited from a blueprint still requires the corresponding builder on the destination site.

## Import and export

Use **Arcane Tech → Tools** to export selected configuration as JSON and import it on another installation.

Supported export sections include module toggles, general settings, widgets, branding, login customization, login redirects, admin pages, admin menu and toolbar configuration, and multisite settings where applicable.

Imports replace included option groups and update existing widgets or admin pages with matching slugs. Export the destination configuration before importing if you need to preserve it. The export does not include integration overrides, email notification templates, media files, or separate builder template libraries.

Imports accept JSON uploads up to 5 MB and validate recognized section and post structures before writing. Legacy serialized role/user lists are supported with PHP object creation disabled. Imported widgets and admin pages are restricted to their corresponding post types. On Multisite, Tools requires network administration permission; network imports only update the four supported ATS network options and require `manage_network_options`.

The setting labeled **Remove Data on Uninstall** is currently checked by the plugin's **deactivation** handler, which deletes listed plugin options when enabled. Leave it disabled to retain those settings through deactivation; it is not a complete removal of all widget and admin-page content.

## Development

The plugin uses PHP, WordPress hooks, JavaScript, and CSS. Parcel compiles the login customizer's TypeScript sources.

```sh
npm ci
npm run watch-login-customizer
```

To build the login customizer assets for distribution:

```sh
npm run build-login-customizer
```

Both commands read `modules/login-customizer/src/js/controls.ts` and `preview.ts`, and write to `modules/login-customizer/assets/js/`. Other module scripts and styles are maintained in their respective asset directories.

### Repository structure

```text
ats-dashboard.php       Plugin entry point and updater configuration
class-setup.php         Bootstrap, module loading, and lifecycle handling
helpers/                Shared content, widget, builder, and multisite helpers
includes/               Self-hosted update client
modules/                Feature modules, templates, scripts, and styles
assets/                 Shared admin assets
.github/workflows/       Release packaging and R2 publishing
```

### Validation and contributions

Run the isolated PHP regression checks without a database or mail service:

```sh
php tests/security.php
php tests/bootstrap-config.php default
php tests/bootstrap-config.php disabled
php tests/bootstrap-config.php custom
```

These tests use WordPress test doubles to check import validation, object rejection, permission boundaries, editor escaping, and updater configuration. They do not replace testing in WordPress. For PHP changes, also run a syntax check on each changed file, for example:

```sh
php -l ats-dashboard.php
```

Verify affected behavior in a WordPress installation, including the relevant user roles and any builder or multisite dependencies. For login customizer changes, rebuild the assets and check both the preview and actual login page. A successful build or PHP syntax check alone does not verify browser behavior or email delivery.

Keep pull requests focused, describe the behavior changed, and include the WordPress/PHP versions and validation performed. Include screenshots for visual changes and update this README when configuration or features change.

## Updates and releases

The included updater checks an Arcane-hosted JSON manifest at `https://files.arcanetechct.com/ats-dashboard/info.json` and exposes available updates through WordPress's normal plugin update interface. Successful manifest responses are cached for six hours; forcing a WordPress update check clears that cache.

Arcane's update service and default logo CDN remain enabled by default. To opt out of the updater, add this to `wp-config.php` before WordPress loads plugins:

```php
define( 'ATS_DASHBOARD_UPDATES_ENABLED', false );
```

Forks can instead use their own manifest and logo URLs:

```php
define( 'ATS_DASHBOARD_UPDATE_MANIFEST_URL', 'https://example.com/ats-dashboard/info.json' );
define( 'ATS_DASHBOARD_DEFAULT_LOGO_URL', 'https://example.com/logo.png' );
```

The manifest controls which ZIP WordPress offers to install. Use an HTTPS endpoint you control and trust. Disabling the updater stops ATS Dashboard from registering its update hooks; it does not disable other WordPress update mechanisms. The logo override supplies the default for branding fields and does not replace already saved logos.

The [release workflow](.github/workflows/release.yml) packages the plugin and uploads the ZIP and update manifest to Cloudflare R2. It supports version tags in the form `vX.Y.Z` or a manual patch, minor, or major bump from GitHub Actions. The workflow requires `R2_ACCOUNT_ID`, `R2_ACCESS_KEY_ID`, and `R2_SECRET_ACCESS_KEY` repository secrets, and publishes to the `plugins` bucket.

Before a tag-based release, make the plugin header's `Version` and `ATS_DASHBOARD_PLUGIN_VERSION` match the tag. The workflow rejects mismatches. 

The release workflow packages the checked-in assets without running npm, so rebuild and include generated login customizer assets before releasing changes to their TypeScript sources.

## License

ATS Dashboard is a modified derivative of **Ultimate Dashboard by David Vongries**, whose published source is GPLv2-or-later. This fork is distributed under **GPL-3.0-only**; see [LICENSE](LICENSE) and [NOTICE.md](NOTICE.md). Bundled libraries and fonts retain their respective licenses, documented in [THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md).
