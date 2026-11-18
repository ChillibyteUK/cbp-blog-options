# CB Blog Options WordPress Plugin

A WordPress plugin that provides granular control over blog functionality, allowing administrators to disable blog features, comments, and gravatars through a simple admin interface.

The plugin also forces Advanced Custom Fields blocks to stay in edit mode in the block editor, including immediately after a new block is inserted, adds a branded Chillibyte dashboard widget (which can be hidden per site), and offers a set of opt-in security response headers aimed at Lighthouse/securityheaders.com-style audits.

## Features

- **Disable Blog**: Completely removes blog functionality including:
  - Hides Posts menu from admin
  - Prevents access to post-related admin pages
  - Removes blog-related dashboard widgets
  - Disables post type support
  - Automatically disables comments and gravatars

- **Disable Comments**: Removes comment functionality including:
  - Closes comments on all posts
  - Hides existing comments
  - Removes Comments menu from admin
  - Removes comment-related dashboard widgets
  - Hides discussion settings page

- **Disable Gravatars**: Disables avatar/gravatar functionality:
  - Turns off gravatar display
  - Removes avatar options from user profiles

- **Disable Tags**: Detaches post tags from the default `post` post type and hides the tags admin UI

- **Disable Emojis**: Removes WordPress emoji scripts/styles from the frontend and admin, and drops TinyMCE emoji support

- **Suppress Object Cache Warning**: Hides the Site Health "persistent object cache" warning, useful for small sites where object caching is unnecessary

- **Suppress Core Update Nag**: Hides the "WordPress x.x is available" banner and blocks WordPress from auto-updating to a new **major** version on its own — for sites deliberately pinned to an older release. Dashboard > Updates still works, and minor/security updates are unaffected. The major-update block can be lifted independently of the checkbox with `add_filter( 'cbp_block_major_core_auto_updates', '__return_false' )`.

- **Chillibyte Dashboard Widget**: Adds a branded admin dashboard widget with quick contact details; can be hidden via the "Hide Chillibyte Dashboard Widget" option

- **Security Headers**: A separate "Security Headers" tab on the settings page, each header independently opt-in and off by default (HSTS with optional `preload`, Cross-Origin-Opener-Policy, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, and experimental Trusted Types, and REST API user-listing blocking). Only affects requests WordPress itself handles — see [Security Headers and Static Files](#security-headers-and-static-files)

- **Force ACF Block Edit Mode**: Keeps all ACF blocks in edit mode in the block editor, including newly inserted blocks. Can be disabled site-wide with `add_filter( 'cbp_acf_blocks_force_edit_mode', '__return_false' )`.

- **WP 7.1 ACF/Gutenberg hardening**: WordPress 7.1 moved the block editor canvas into an iframe, which breaks several things for sites relying on ACF blocks. This plugin works around that automatically:
  - Re-enqueues ACF/editor/button styles inside the iframe canvas so ACF fields and buttons aren't left unstyled
  - Tags the iframe body with `wp-core-ui` so core admin button styles apply inside it
  - Hides ACF's duplicate inspector-sidebar panel when a block is forced into edit mode
  - Fixes a WP 7.1 QTags/`buildQuicktags` crash that could break ACF's field init and show "This block has encountered an error and cannot be previewed"
  - Prevents TinyMCE from stealing focus/scrolling the page when a WYSIWYG field is added inside an ACF repeater or flexible content layout
  - Checking **Suppress Core Update Nag** additionally stops WordPress auto-updating past its current major, so a site doesn't jump to 7.1 unattended and lose ACF's TinyMCE fields

## Installation

1. Upload the `cbp-blog-options` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to Tools > CB Blog Options to configure settings

## Usage

### Admin Interface

The plugin adds a "CB Blog Options" page under the WordPress admin Tools menu. This page contains eight checkboxes:

1. **Disable Blog** - When checked, completely disables all blog functionality and automatically enables options 2, 3, and 4
2. **Disable Comments** - When checked, disables all comment-related functionality
3. **Disable Gravatars** - When checked, disables gravatar/avatar display
4. **Disable Tags** - When checked, removes post tags from the admin UI
5. **Disable Emojis** - When checked, removes WordPress emoji assets and TinyMCE emoji support
6. **Suppress Object Cache Warning** - When checked, hides the Site Health "persistent object cache" warning
7. **Suppress Core Update Nag** - When checked, hides the "WordPress x.x is available" admin banner and blocks major core auto-updates
8. **Hide Chillibyte Dashboard Widget** - When checked, the Chillibyte dashboard widget is not shown

### Checkbox Behavior

- When "Disable Blog" is checked, "Disable Comments", "Disable Gravatars", and "Disable Tags" are automatically checked and cannot be unchecked
- Individual comment, gravatar, tag, and emoji options can be controlled independently when blog is not disabled

## What Gets Disabled

### When Blog is Disabled:

- Posts menu is removed from admin
- All post-related admin pages are inaccessible
- Post type support is disabled
- Blog-related dashboard widgets are removed
- New Post button is removed from admin bar
- All comment functionality (inherited)
- All gravatar functionality (inherited)

### When Comments are Disabled:

- Comments are closed on all posts
- Existing comments are hidden
- Comments menu is removed from admin
- Comment-related dashboard widgets are removed
- Discussion settings page is inaccessible
- Comments section is removed from admin bar

### When Gravatars are Disabled:

- Avatar display is turned off
- Gravatar images are replaced with empty content
- Avatar options are removed from user profiles

### When Tags are Disabled:

- Post tags are detached from the default `post` post type
- The tags admin screen is hidden
- The tags metabox is removed from the post editor

### When Emojis are Disabled:

- Frontend and admin emoji scripts and styles are removed
- TinyMCE emoji support is removed
- The emoji SVG CDN URL is disabled

## Technical Details

- **Version**: 1.8.1
- **Requires**: WordPress 4.0+
- **PHP**: 5.6+
- **License**: GPL v2 or later

## File Structure

```
cbp-blog-options/
├── cbp-blog-options.php    # Main plugin file
├── assets/images/cb-full.jpg # Dashboard widget image
└── README.md                # This documentation
```

## Hooks and Filters Used

The plugin uses various WordPress hooks and filters to achieve its functionality:

- `admin_menu` - For adding/removing menu items
- `admin_init` - For settings registration and redirects
- `init` - For disabling post type support
- `wp_dashboard_setup` - For removing dashboard widgets
- `admin_bar_menu` - For modifying admin bar
- `comments_open` / `pings_open` - For disabling comments
- `get_avatar` - For disabling gravatars
- `allow_major_auto_core_updates` - For blocking major core auto-updates
- `enqueue_block_assets` / `enqueue_block_editor_assets` - For ACF/Gutenberg iframe fixes
- `acf/input/admin_enqueue_scripts` - For the QTags crash fix

## Security Headers and Static Files

The Security Headers settings only affect requests that WordPress itself handles — pages, posts, admin screens, anything that loads through `index.php`. Direct static file requests (`/wp-content/uploads/*`, and any other file the web server serves straight off disk) never run WordPress or PHP, so this plugin's `send_headers` hook never fires for them and **no WordPress plugin can add headers to those responses**. That has to be configured at the web server (or CDN) instead.

For most of the headers this doesn't matter in practice — HSTS is cached per-origin by the browser after a single page load, and the framing/referrer/permissions headers are only meaningful on HTML documents. The one that's actually worth adding to static files is **`X-Content-Type-Options: nosniff`**, since it stops the browser from MIME-sniffing a response into something it wasn't declared as — exactly the risk with user-uploaded files.

**Apache** — add to a `.htaccess` in `wp-content/uploads/`, or to the vhost config:

```apache
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
</IfModule>
```

**Nginx** — add to the `location` block that serves uploads (or the server block, to cover all static assets):

```nginx
location /wp-content/uploads/ {
    add_header X-Content-Type-Options "nosniff" always;
}
```

**Behind a CDN/proxy** (Cloudflare, etc.) — set the header as a response header rule at the edge instead; it will apply before the request ever reaches the origin.

## Support

This plugin is provided as-is. For customizations or support, please contact the plugin author.

## Changelog

### 1.8.1
- Documented the existing "Disable Tags", "Disable Emojis" and "Suppress Object Cache Warning" options, and tidied code layout and comments to match the upstream plugin
- Added a "Suppress Core Update Nag" option that hides the "WordPress x.x is available" admin banner and blocks WordPress from auto-updating to a new major version on its own (minor/security updates unaffected, major-block independently filterable via `cbp_block_major_core_auto_updates`)
- Added WP 7.1 ACF/Gutenberg hardening to deal with the block editor canvas moving into an iframe:
  - Made the ACF edit-mode enforcement filterable via `cbp_acf_blocks_force_edit_mode`
  - Re-enqueue ACF/editor/button styles inside the iframe canvas and tag its body with `wp-core-ui`
  - Hide ACF's duplicate inspector-sidebar panel
  - Fix a WP 7.1 QTags/`buildQuicktags` crash that could break ACF field init
  - Prevent TinyMCE focus-steal/scroll-jump when adding a WYSIWYG field inside an ACF repeater
- Replaced the older "force edit mode on insert" JS watcher with the simpler always-on subscriber, now gated by the filter above

### 1.8.0
- Added a "REST API User Listing" Security Headers option: blocks unauthenticated requests to `/wp-json/wp/v2/users` (and the `?rest_route=/wp/v2/users` form), including single-user lookups, to stop anonymous username enumeration. Off by default; logged-in and authenticated REST requests are unaffected.

### 1.7.1
- Added a "Hide Chillibyte Dashboard Widget" option (unchecked by default, so the widget keeps showing unless explicitly hidden)

### 1.7.0
- Added three more opt-in Security Headers, and split the settings page into "Blog Options" / "Security Headers" tabs so the growing header list doesn't crowd out the blog toggles:
  - **X-Content-Type-Options** — `X-Content-Type-Options: nosniff`, safe to enable on virtually any site
  - **Referrer-Policy** — `Referrer-Policy: strict-origin-when-cross-origin`, sent as a header (not just the `<meta>` tag WordPress core already prints) so header-only scanners like securityheaders.com pick it up
  - **Permissions-Policy** — `Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()`
- ACF-specific block-editor workarounds (edit-mode enforcement, iframe styling fixes, the QTags crash fix) now only register on sites with ACF/ACF PRO active, rather than on every site regardless
- Added a direct "Open Blog Options" link to the plugin's row on the Installed Plugins page, alongside the existing action link

### 1.6.0
- Added a Security Headers section to the settings page, each toggle independently opt-in (off by default on upgrade):
  - **HSTS** — `Strict-Transport-Security: max-age=31536000; includeSubDomains`, only sent on an actual HTTPS response, with an optional `preload` sub-toggle
  - **COOP** — `Cross-Origin-Opener-Policy: same-origin-allow-popups`
  - **X-Frame-Options** — `X-Frame-Options: SAMEORIGIN` (clickjacking mitigation)
  - **Trusted Types** — `Content-Security-Policy: require-trusted-types-for 'script'`, front-end only, paired with an early inline script registering a permissive pass-through `default` Trusted Types policy so existing third-party scripts keep working. Experimental — see the in-admin field description before enabling.

### 1.1.2
- Added the Chillibyte dashboard widget to the WordPress admin dashboard
- Moved the dashboard widget image into the plugin at `assets/images/cb-full.jpg`

### 1.1.1
- Forced ACF blocks into edit mode immediately when inserted in the block editor
- Fixed the edit-mode enforcement so it also works for newly added blocks in WordPress 7.0

### 1.0.0
- Initial release
- Added blog disable functionality
- Added comments disable functionality
- Added gravatars disable functionality
- Added admin interface under Tools menu
