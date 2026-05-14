# MoonShine Components AI Generation Guide

This document is designed for AI assistants that generate interfaces based on MoonShine components. All components use the `x-moonshine::` prefix in Blade templates.

## ⚠️ CRITICAL RULES

**1. NEVER duplicate HTML tags**
- MoonShine components automatically generate `<!DOCTYPE html>`, `<html>`, `<head>`, and `<body>` tags
- Your Blade file must start with `<x-moonshine::layout>`, NOT with `<!DOCTYPE html>`
- See [Basic Template Structure](#basic-template-structure) for correct usage

**2. ALWAYS maintain the correct layout structure**
- **CRITICAL:** Always preserve the `layout-main` and `layout-page` wrapper structure
- This structure is essential for proper layout functionality and styling
- See [Layout Structure](#layout-structure-critical) for details

**3. ALWAYS use required CSS wrapper classes**
- Logo must be wrapped in `<x-moonshine::layout.div class="menu-logo">` and must have `logo` attribute with path to image
- Menu must be wrapped in `<x-moonshine::layout.div class="menu menu--vertical">` (Sidebar) or `<x-moonshine::layout.div class="menu menu--horizontal">` (TopBar/MobileBar)
- Burger must be wrapped in `<x-moonshine::layout.div class="menu-burger">` and have location attribute (`sidebar`, `topbar`, or `mobile-bar`)
- Actions must be wrapped in `<x-moonshine::layout.div class="menu-actions">`

**4. ALWAYS include MoonShine assets**
- Must include: `@vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')`
- Place inside `<x-moonshine::layout.assets>` component
- See [Assets Configuration](#critical-moonshine-assets-configuration) for details

**5. ALWAYS add spacing between stacked components**
- MoonShine components have NO margins by default
- When placing multiple components vertically (one after another), they will appear merged together
- **ALWAYS** use `<x-moonshine::line-break />` or `<x-moonshine::layout.divider />` between stacked components
- See [Component Spacing](#component-spacing-critical) for examples

## Table of Contents

### Layout Components
- [Sidebar](#sidebar-side-panel) - `<x-moonshine::layout.sidebar>`
- [Header](#header-top-header) - `<x-moonshine::layout.header>`
- [Wrapper](#wrapper-layout-wrapper) - `<x-moonshine::layout.wrapper>`
- [Content](#content-main-content) - `<x-moonshine::layout.content>`
- [Grid](#grid-grid-layout) - `<x-moonshine::layout.grid>`
- [Flex](#flex-flex-container) - `<x-moonshine::layout.flex>`
- [TopBar](#topbar-top-navigation) - `<x-moonshine::layout.top-bar>`
- [MobileBar](#mobilebar-mobile-navigation) - `<x-moonshine::layout.mobile-bar>`
- [Footer](#footer-page-footer) - `<x-moonshine::layout.footer>`
- [Div](#div-layout-div) - `<x-moonshine::layout.div>`
- [Body](#body-layout-body) - `<x-moonshine::layout.body>`
- [Head](#head-html-head) - `<x-moonshine::layout.head>`
- [Html](#html-html-document) - `<x-moonshine::layout.html>`
- [Layout](#layout-root-layout) - `<x-moonshine::layout>`

### Interface Components
- [Box](#box-container-block) - `<x-moonshine::layout.box>`
- [Card](#card-content-card) - `<x-moonshine::card>`
- [Alert](#alert-notification) - `<x-moonshine::alert>`
- [Modal](#modal-modal-window) - `<x-moonshine::modal>`
- [Table](#table-data-table) - `<x-moonshine::table>`
- [Form](#form-form) - `<x-moonshine::form>`
- [Dropdown](#dropdown-dropdown-menu) - `<x-moonshine::dropdown>`
- [Collapse](#collapse-collapsible-content) - `<x-moonshine::collapse>`
- [Breadcrumbs](#breadcrumbs-navigation-breadcrumbs) - `<x-moonshine::breadcrumbs>`
- [Badge](#badge-labelbadge) - `<x-moonshine::badge>`
- [Divider](#divider-content-divider) - `<x-moonshine::layout.divider>`
- [Progress Bar](#progress-bar-progress-indicator) - `<x-moonshine::progress-bar>`
- [Tabs](#tabs-tab-navigation) - `<x-moonshine::tabs>`
- [Spinner](#spinner-loading-indicator) - `<x-moonshine::spinner>`
- [Carousel](#carousel-image-carousel) - `<x-moonshine::carousel>`
- [Popover](#popover-hover-tooltip) - `<x-moonshine::popover>`
- [Rating](#rating-star-rating) - `<x-moonshine::rating>`
- [OffCanvas](#offcanvas-side-panel) - `<x-moonshine::off-canvas>`

### Action Components
- [Link](#link-styled-links) - `<x-moonshine::link-button>` / `<x-moonshine::link-native>`

### Content & Display
- [Icon](#icon-standalone-icon) - `<x-moonshine::icon>`
- [Logo](#logo-brand-logo) - `<x-moonshine::layout.logo>`
- [Heading](#heading-text-headings) - `<x-moonshine::heading>`
- [Boolean](#boolean-boolean-display) - `<x-moonshine::boolean>`
- [Color](#color-color-displaypicker) - `<x-moonshine::color>`
- [Thumbnails](#thumbnails-image-gallery) - `<x-moonshine::thumbnails>`
- [Files](#files-file-uploaddisplay) - `<x-moonshine::files>`
- [Metrics](#metrics-metrics-display) - `<x-moonshine::metric>`
- [Flash](#flash-flash-messages) - `<x-moonshine::flash>`

### Navigation & User
- [Menu](#menu-navigation-menu) - `<x-moonshine::layout.menu>`
- [ThemeSwitcher](#themeswitcher-theme-toggle) - `<x-moonshine::layout.theme-switcher>`
- [Burger](#burger-mobile-menu-button) - `<x-moonshine::layout.burger>`

### Form & Field Components
- [FieldsGroup](#fieldsgroup-field-grouping) - `<x-moonshine::fields-group>`

### Utility & Special
- [When](#when-conditional-rendering) - `<x-moonshine::when>`
- [Title](#title-page-title) - `<x-moonshine::title>`
- [Loader](#loader-loading-indicator) - `<x-moonshine::loader>`
- [FlexibleRender](#flexiblerender-dynamic-content) - `<x-moonshine::flexible-render>`
- [LineBreak](#linebreak-line-break) - `<x-moonshine::line-break>`
- [Components](#components-component-container) - `<x-moonshine::components>`

### HTML & Meta
- [Meta](#meta-meta-tags) - `<x-moonshine::layout.meta>`
- [Assets](#assets-asset-management) - `<x-moonshine::layout.assets>`
- [Attributes](#attributes-dynamic-attributes) - `<x-moonshine::attributes>`
- [Favicon](#favicon-favicon-management) - `<x-moonshine::layout.favicon>`

## Icons in MoonShine

MoonShine uses **Heroicons** for all icon displays. All icons are available at: https://heroicons.com/

**Icon naming conventions:**
- **Default (Outline)**: Use icon name as-is, e.g., `icon="users"`
- **Solid**: Add `s.` prefix, e.g., `icon="s.users"`
- **Mini**: Add `m.` prefix, e.g., `icon="m.users"`
- **Micro**: Add `c.` prefix, e.g., `icon="c.users"`

**Examples:**
- `icon="home"` - outline home icon
- `icon="s.home"` - solid home icon
- `icon="m.home"` - mini home icon
- `icon="c.home"` - micro home icon

## Layout Structure (CRITICAL)

**⚠️ EXTREMELY IMPORTANT:** MoonShine requires a specific wrapper structure with `layout-main` and `layout-page` classes. **NEVER skip or modify this structure** - it is essential for proper layout functionality, styling, and responsive behavior.

### Required Structure

```blade
<x-moonshine::layout.wrapper>
    <!-- Sidebar (optional, for sidebar layouts) -->
    <x-moonshine::layout.sidebar :collapsed="true">
        <!-- Sidebar content: logo, menu, theme switcher, etc. -->
    </x-moonshine::layout.sidebar>

    <!-- CRITICAL: layout-main wrapper -->
    <x-moonshine::layout.div class="layout-main">
        <!-- CRITICAL: layout-page wrapper -->
        <x-moonshine::layout.div class="layout-page">
            <!-- Header section -->
            <x-moonshine::layout.header>
                <!-- Header content: breadcrumbs, page title, etc. -->
            </x-moonshine::layout.header>

            <!-- Main content section -->
            <x-moonshine::layout.content>
                <!-- Your page content goes here -->
            </x-moonshine::layout.content>
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

### Why This Structure Is Critical

1. **CSS Dependencies** - MoonShine's CSS relies on these classes for proper spacing, positioning, and responsive behavior
2. **Sidebar Functionality** - The sidebar collapse/expand mechanism depends on this structure
3. **Mobile Responsiveness** - Mobile layouts and transitions require these wrappers
4. **Content Alignment** - Proper content width and centering depend on this hierarchy

### Common Patterns

**With Sidebar (Most Common):**
```blade
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.sidebar>
        <!-- Sidebar navigation -->
    </x-moonshine::layout.sidebar>

    <x-moonshine::layout.div class="layout-main">
        <x-moonshine::layout.div class="layout-page">
            <x-moonshine::layout.header>
                <x-moonshine::breadcrumbs :items="['/' => 'Home']" />
            </x-moonshine::layout.header>
            <x-moonshine::layout.content>
                <!-- Content here -->
            </x-moonshine::layout.content>
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

**With TopBar (No Sidebar):**
```blade
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.top-bar>
        <!-- Top navigation -->
    </x-moonshine::layout.top-bar>

    <x-moonshine::layout.div class="layout-main">
        <x-moonshine::layout.div class="layout-page">
            <x-moonshine::layout.header>
                <!-- Page header -->
            </x-moonshine::layout.header>
            <x-moonshine::layout.content>
                <!-- Content here -->
            </x-moonshine::layout.content>
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

### ❌ WRONG: Missing or Incorrect Structure

```blade
<!-- ❌ WRONG: Missing layout-main and layout-page -->
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.sidebar>...</x-moonshine::layout.sidebar>
    <x-moonshine::layout.content>
        <!-- This will break styling and responsiveness -->
    </x-moonshine::layout.content>
</x-moonshine::layout.wrapper>

<!-- ❌ WRONG: Incorrect class names -->
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.div class="main-layout">  <!-- Wrong class -->
        <x-moonshine::layout.div class="page-layout">  <!-- Wrong class -->
            <x-moonshine::layout.content>...</x-moonshine::layout.content>
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

### Key Rules

1. **ALWAYS use `class="layout-main"`** for the outer content wrapper
2. **ALWAYS use `class="layout-page"`** for the inner content wrapper
3. **ALWAYS maintain this hierarchy**: wrapper → layout-main → layout-page → header/content
4. **NEVER skip these wrappers** even if it seems they do nothing
5. **NEVER change the class names** - MoonShine CSS depends on these exact names

## Component Spacing (CRITICAL)

**⚠️ IMPORTANT:** MoonShine components have **NO margins by default**. When stacking components vertically, they will appear merged together without spacing.

**Solution:** Use spacing components between stacked elements:
- `<x-moonshine::line-break />` - Simple vertical spacing
- `<x-moonshine::layout.divider />` - Visual divider line with spacing

### ❌ WRONG: Components without spacing
```blade
<x-moonshine::layout.content>
    <x-moonshine::card>
        <h2>Card 1</h2>
        <p>First card content</p>
    </x-moonshine::card>

    <x-moonshine::card>
        <h2>Card 2</h2>
        <p>Second card content</p>
    </x-moonshine::card>

    <x-moonshine::table>
        <!-- Table content -->
    </x-moonshine::table>
</x-moonshine::layout.content>
```
**Result:** All components appear merged together with no visual separation.

### ✅ CORRECT: Components with spacing
```blade
<x-moonshine::layout.content>
    <x-moonshine::card>
        <h2>Card 1</h2>
        <p>First card content</p>
    </x-moonshine::card>

    <x-moonshine::line-break />

    <x-moonshine::card>
        <h2>Card 2</h2>
        <p>Second card content</p>
    </x-moonshine::card>

    <x-moonshine::line-break />

    <x-moonshine::table>
        <!-- Table content -->
    </x-moonshine::table>
</x-moonshine::layout.content>
```

### Using Divider for Visual Separation
```blade
<x-moonshine::layout.content>
    <x-moonshine::card>
        <h2>User Statistics</h2>
        <p>Overview of user data</p>
    </x-moonshine::card>

    <x-moonshine::layout.divider />

    <x-moonshine::card>
        <h2>Recent Activity</h2>
        <p>Latest user actions</p>
    </x-moonshine::card>
</x-moonshine::layout.content>
```

### When to Use Spacing

**ALWAYS use spacing when:**
- Placing multiple cards vertically
- Stacking tables
- Adding forms after other components
- Combining alerts with other content
- Placing multiple modal triggers
- Any time you have 2+ block-level components in sequence

**NO spacing needed when:**
- Components are inside Grid or Flex layouts (they handle spacing)
- Components are in different sections/wrappers
- Using a single component

### Common Patterns

**Multiple Cards:**
```blade
<x-moonshine::card>Card 1</x-moonshine::card>
<x-moonshine::line-break />
<x-moonshine::card>Card 2</x-moonshine::card>
<x-moonshine::line-break />
<x-moonshine::card>Card 3</x-moonshine::card>
```

**Alert + Content:**
```blade
<x-moonshine::alert type="info">Important notice</x-moonshine::alert>
<x-moonshine::line-break />
<x-moonshine::table>
    <!-- Table content -->
</x-moonshine::table>
```

**Form + Table:**
```blade
<x-moonshine::form name="search">
    <!-- Form fields -->
</x-moonshine::form>
<x-moonshine::line-break />
<x-moonshine::table>
    <!-- Results table -->
</x-moonshine::table>
```

## Basic Template Structure

```blade
<x-moonshine::layout>
    <x-moonshine::layout.html :with-alpine-js="true" :with-themes="true">
        <x-moonshine::layout.head>
            <x-moonshine::layout.meta name="csrf-token" :content="csrf_token()"/>
            <x-moonshine::layout.favicon />
            <x-moonshine::layout.assets>
                @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
            </x-moonshine::layout.assets>
        </x-moonshine::layout.head>

        <x-moonshine::layout.body>
            <x-moonshine::layout.wrapper>
                <x-moonshine::layout.div class="layout-main">
                    <x-moonshine::layout.div class="layout-page">
                        <x-moonshine::layout.header>
                            <x-moonshine::breadcrumbs :items="['#' => 'Home']"/>
                        </x-moonshine::layout.header>
                        <x-moonshine::layout.content>
                            <!-- All main page content here (recommended) -->
                        </x-moonshine::layout.content>
                    </x-moonshine::layout.div>
                </x-moonshine::layout.div>
            </x-moonshine::layout.wrapper>
        </x-moonshine::layout.body>
    </x-moonshine::layout.html>
</x-moonshine::layout>
```

**⚠️ CRITICAL: Do NOT Duplicate HTML Tags**

The MoonShine layout components **automatically generate** HTML document structure:

- `<x-moonshine::layout.html>` generates `<!DOCTYPE html>` and `<html>` tags
- `<x-moonshine::layout.head>` generates `<head>` tags
- `<x-moonshine::layout.body>` generates `<body>` tags

**❌ WRONG - Do NOT do this:**
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Title</title>
    <!-- This creates nested HTML tags! -->
    <x-moonshine::layout.html :with-alpine-js="true">
        <x-moonshine::layout.head>
            <!-- ... -->
        </x-moonshine::layout.head>
    </x-moonshine::layout.html>
</head>
</html>
```

**✅ CORRECT - Start directly with MoonShine components:**
```blade
<x-moonshine::layout>
    <x-moonshine::layout.html :with-alpine-js="true" :with-themes="true">
        <x-moonshine::layout.head>
            <x-moonshine::layout.meta name="csrf-token" :content="csrf_token()"/>
            <x-moonshine::layout.favicon />
            <x-moonshine::layout.assets>
                @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
            </x-moonshine::layout.assets>
        </x-moonshine::layout.head>
        <x-moonshine::layout.body>
            <!-- Your content -->
        </x-moonshine::layout.body>
    </x-moonshine::layout.html>
</x-moonshine::layout>
```

## ⚠️ Critical: MoonShine Assets Configuration

### Understanding MoonShine Assets
**Important:** MoonShine has pre-compiled assets that are **already built** and ready to use. These assets include:
- MoonShine CSS framework (`main.css`) with Tailwind configuration
- Alpine.js integration
- Core JavaScript functionality
- Default themes and styling system

### MoonShine Default Assets (Always Required)
**These assets are mandatory and pre-compiled:**

```blade
<x-moonshine::layout.assets>
    @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
</x-moonshine::layout.assets>
```

**Critical Notes:**
- **Pre-compiled and ready** - MoonShine assets are already built, no compilation needed
- **Must use `'vendor/moonshine'` namespace** - tells Vite to use MoonShine's configuration
- **Never remove these assets** - required for proper component styling and functionality

### Adding Your Own Assets (Optional - Only When Needed)
You can add your own application assets for additional functionality:

```blade
<x-moonshine::layout.assets>
    @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
    @vite(['resources/css/custom.css', 'resources/js/custom.js'])
</x-moonshine::layout.assets>
```

**Use cases for custom assets:**
- Override specific MoonShine styles
- Add custom JavaScript functionality
- Include additional CSS frameworks
- Add custom fonts or icons

### Troubleshooting: Missing Tailwind Classes

**Problem:** User complains that some Tailwind classes are not applying or working.

**Cause:** MoonShine's pre-compiled CSS includes only the Tailwind classes that are actually used by MoonShine components. Not all Tailwind classes are included in the build.

**Solution:** When you need additional Tailwind classes, use MoonShine's custom build feature.

### Custom Build Setup

When you need additional TailwindCSS classes beyond what MoonShine provides, you can create a custom build that includes both MoonShine and your custom styles.

#### Automatic Publishing (Recommended)

**Requirements:** TailwindCSS 4+ and Laravel 12+

1. **Run the publish command:**
   ```shell
   php artisan moonshine:publish
   ```

2. **Select `Assets Template`**

3. **Files that will be published/replaced:**
   - `vite.config.js`
   - `postcss.config.js`
   - `resources/css/app.css`

4. **Or add assets via Blade:**
   ```blade
   <x-moonshine::layout.head>
       <x-moonshine::layout.assets>
           @vite(['resources/js/app.js'], 'vendor/moonshine')
           @vite(['resources/css/app.css', 'resources/js/app.js'])
       </x-moonshine::layout.assets>
   </x-moonshine::layout.head>
   ```

#### Manual Custom Build Setup

If you can't use automatic publishing, configure manually:

1. **Update vite.config.js:**
   ```js
   import { defineConfig } from 'vite';
   import laravel from 'laravel-vite-plugin';

   export default defineConfig({
       plugins: [
           laravel({
               input: ['resources/css/app.css', 'resources/js/app.js'],
               refresh: true,
           }),
       ],
       resolve: {
           alias: {
               '@moonshine-resources': '/vendor/moonshine/moonshine/src/UI/resources',
           }
       },
   });
   ```

   **Note:** Remove the `tailwindcss()` plugin if it exists.

2. **Install and configure PostCSS:**
   ```shell
   npm install @tailwindcss/postcss
   ```

   Create `postcss.config.js`:
   ```js
   export default {
     plugins: {
       '@tailwindcss/postcss': {},
     },
   };
   ```

3. **Create resources/css/app.css:**
   ```css
   @import '../../vendor/moonshine/moonshine/src/UI/resources/css/main.css';

   @source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
   @source '../../storage/framework/views/*.php';
   @source '../**/*.blade.php';
   @source '../**/*.js';
   ```

**Result:** After setting up the custom build, any Tailwind classes you use in your templates will be available.

**Example workflow:**
- User wants to use `bg-gradient-to-r` class
- Class doesn't work because it's not in MoonShine's pre-compiled CSS
- Run `php artisan moonshine:publish` and select `Assets Template`
- Configure your layout to use the custom build
- Use the class in your templates: `<div class="bg-gradient-to-r from-blue-500 to-purple-600">`
- Run `npm run build` and your custom classes will be included

### Key Points:
- **MoonShine assets are pre-compiled** - ready to use without build process
- **Always include MoonShine defaults** - `@vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')`
- **Custom assets are optional** - add only when extending functionality
- **Missing Tailwind classes?** - Add your own Tailwind compilation with MoonShine's config
- **Respect MoonShine's Tailwind system** - avoid conflicts with component styling
- **Never modify vendor files** - they'll be overwritten on updates
- **Theme consistency matters** - align custom styles with MoonShine's design system

## Layout Components

### Sidebar (Side Panel)
**Purpose:** Creating side navigation panel

```blade
<!-- Full sidebar structure with all components -->
<x-moonshine::layout.sidebar :collapsed="true">
    <x-moonshine::layout.div class="menu-header">
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo
                href="/"
                logo="/logo.png"
                logo-small="/logo-small.png"
                :minimized="true"
            />
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu-actions">
            <x-moonshine::layout.theme-switcher/>
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger sidebar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>

    <x-moonshine::layout.div class="menu menu--vertical">
        <x-moonshine::layout.menu :elements="[
            ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'home'],
            ['label' => 'Orders', 'url' => '/orders', 'icon' => 's.shopping-bag'],
            ['label' => 'Products', 'url' => '/products', 'icon' => 'cube'],
            ['label' => 'Customers', 'url' => '/customers', 'icon' => 'users'],
            ['label' => 'Settings', 'url' => '/settings', 'icon' => 'cog-6-tooth']
        ]"/>
    </x-moonshine::layout.div>
</x-moonshine::layout.sidebar>

<!-- Minimal sidebar structure (logo and burger only) -->
<x-moonshine::layout.sidebar :collapsed="true">
    <x-moonshine::layout.div class="menu-header">
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo
                href="/"
                logo="/logo.png"
                logo-small="/logo-small.png"
                :minimized="true"
            />
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger sidebar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>

    <x-moonshine::layout.div class="menu menu--vertical">
        <x-moonshine::layout.menu :elements="[
            ['label' => 'Dashboard', 'url' => '/'],
            ['label' => 'Settings', 'url' => '/settings']
        ]"/>
    </x-moonshine::layout.div>
</x-moonshine::layout.sidebar>
```

**Parameters:**
- `collapsed` (bool) - adds a toggle button to collapse/expand the sidebar

**Important Wrapper Structure:**

Sidebar uses specific CSS class wrappers for proper styling and functionality:

1. **`menu-header`** - Container for the top section of sidebar (logo, actions, burger)
2. **`menu-logo`** - Wrapper for the logo component (logo must have `logo="/path/to/logo.svg"` attribute)
3. **`menu-actions`** - Wrapper for theme switcher, notifications, or other action components
4. **`menu-burger`** - Wrapper for the burger button (burger must have `sidebar` attribute)
5. **`menu menu--vertical`** - Wrapper for the navigation menu (vertical orientation)

These wrappers are **required** for proper alignment, spacing, and responsive behavior.

**⚠️ CRITICAL:** Logo component **must** have the `logo` attribute with a path to the image file, otherwise it will cause an error.

### Header (Top Header)
**Purpose:** Creating top page header with navigation
```blade
<x-moonshine::layout.header>
    <x-moonshine::layout.div class="menu-burger">
        <x-moonshine::layout.burger sidebar />
    </x-moonshine::layout.div>
    <x-moonshine::breadcrumbs :items="['#' => 'Home']"/>
    <x-moonshine::layout.search placeholder="Search" />
    <x-moonshine::layout.locales :locales="collect()"/>
</x-moonshine::layout.header>
```

**Responsive Burger Menu:**
- `menu-burger` with `burger` component in header is required for responsive design
- **Mobile devices:** sidebar is hidden, burger button is visible and opens menu on click
- **Large screens:** burger button is automatically hidden, sidebar is always visible
- Essential for proper mobile navigation experience
- **Important:** Burger in header should typically have the `sidebar` or `mobile-bar` attribute depending on your layout

### Wrapper (Layout Wrapper)
**Purpose:** Wrapper component to ensure proper display of layout elements
```blade
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.sidebar :collapsed="true">
        <!-- Sidebar content -->
    </x-moonshine::layout.sidebar>

    <x-moonshine::layout.div class="layout-main">
        <x-moonshine::layout.div class="layout-page">
            <x-moonshine::layout.header>
                <!-- Main content header -->
            </x-moonshine::layout.header>
            <x-moonshine::layout.content>
                <!-- Main content -->
            </x-moonshine::layout.content>
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

**Important Structure Notes:**
- **Required wrappers:** Content area must be wrapped in `<x-moonshine::layout.div class="layout-main">` → `<x-moonshine::layout.div class="layout-page">`
- **Optional header:** The `<x-moonshine::layout.header>` inside layout-page is optional
- These wrappers ensure proper layout positioning, spacing, and responsive behavior

**Single content (recommended):**
```blade
<x-moonshine::layout.body>
    <x-moonshine::layout.wrapper>
        <x-moonshine::layout.div class="layout-main">
            <x-moonshine::layout.div class="layout-page">
                <x-moonshine::layout.header>
                    <!-- Main content header -->
                </x-moonshine::layout.header>
                <x-moonshine::layout.content>
                    <!-- All main page content here -->
                    <section id="hero">...</section>
                    <section id="features">...</section>
                    <section id="footer">...</section>
                </x-moonshine::layout.content>
            </x-moonshine::layout.div>
        </x-moonshine::layout.div>
    </x-moonshine::layout.wrapper>
</x-moonshine::layout.body>
```

**Layout Customization:**
- **Centered content:** Add `layout-main-centered` class to `layout-main` to center content in a container instead of full width
  ```blade
  <x-moonshine::layout.div class="layout-main layout-main-centered">
  ```
  This works both with and without sidebar
- **Remove border:** Add `layout-page-simple` class to `layout-page` to remove default border
  ```blade
  <x-moonshine::layout.div class="layout-page layout-page-simple">
  ```

### Grid (Grid Layout)
**Purpose:** Creating grid layout (12 columns)
```blade
<x-moonshine::layout.grid :gap="6">
    <x-moonshine::layout.column :colSpan="8" :adaptiveColSpan="12">
        <!-- Main content -->
    </x-moonshine::layout.column>
    <x-moonshine::layout.column :colSpan="4" :adaptiveColSpan="12">
        <!-- Side content -->
    </x-moonshine::layout.column>
</x-moonshine::layout.grid>
```
**Grid Parameters:**
- `gap` (int) - spacing between elements (default 6)

**Column Parameters:**
- `colSpan` (int) - number of columns for screens ≥1280px (1-12)
- `adaptiveColSpan` (int) - number of columns for screens <1280px (1-12)

### Flex (Flex Container)
**Purpose:** Creating flexible layout
```blade
<x-moonshine::layout.flex
    :itemsAlign="'center'"
    :justifyAlign="'between'"
    :colSpan="12"
    :adaptiveColSpan="12"
    :withoutSpace="false"
>
    <div>Element 1</div>
    <div>Element 2</div>
</x-moonshine::layout.flex>
```
**Parameters:**
- `itemsAlign` (string) - vertical alignment: 'start', 'center', 'end', 'stretch'
- `justifyAlign` (string) - horizontal alignment: 'start', 'center', 'end', 'between', 'around'
- `colSpan` (int) - columns for large screens
- `adaptiveColSpan` (int) - columns for small screens
- `withoutSpace` (bool) - remove spacing

## Interface Components

### Box (Container Block)
**Purpose:** Highlighting content in separate block
```blade
<x-moonshine::layout.box title="Users Management" :dark="false">
    <x-moonshine::icon icon="users"></x-moonshine::icon>
    Block content
</x-moonshine::layout.box>

<!-- With solid icon -->
<x-moonshine::layout.box title="Statistics" :dark="true">
    <x-moonshine::icon icon="s.chart-bar"></x-moonshine::icon>
    Dashboard stats
</x-moonshine::layout.box>
```
**Parameters:**
- `title` (string) - block title
- `dark` (bool) - dark theme for block

**Icon Usage in Box:**
- Use `<x-moonshine::icon icon="icon-name">` inside the box
- All Heroicons available: outline (default), solid (s.), mini (m.), micro (c.)

### Card (Content Card)
**Purpose:** Creating content cards
```blade
<x-moonshine::card
    :title="'Card Title'"
    :thumbnail="'/path/to/image.jpg'"
    :url="'https://example.com'"
    :subtitle="'Subtitle'"
    :values="['ID' => 1, 'Author' => 'Name']"
>
    Card content
</x-moonshine::card>
```
**Parameters:**
- `title` (string) - card title
- `thumbnail` (string|array) - image or array of images
- `url` (string) - link
- `subtitle` (string) - subtitle
- `values` (array) - list of values in key-value format

### Alert (Notification)
**Purpose:** Displaying notifications and messages
```blade
<x-moonshine::alert
    type="success"
    icon="check-circle"
    :removable="true"
>
    Success message
</x-moonshine::alert>

<!-- With solid icon -->
<x-moonshine::alert
    type="warning"
    icon="s.exclamation-triangle"
    :removable="true"
>
    Warning message
</x-moonshine::alert>

<!-- Simple alert without custom icon (uses default type icon) -->
<x-moonshine::alert type="info">
    Information message
</x-moonshine::alert>
```
**Parameters:**
- `type` (string) - type: 'primary', 'secondary', 'success', 'warning', 'error', 'info'
- `icon` (string) - Heroicons name (outline by default, add s./m./c. prefixes for other styles)
- `removable` (bool) - closeable

**Important Alert Behavior:**
- **Built-in icon**: Alert component already includes an icon based on the `type` parameter
- **Don't add extra icons**: No need to add `<x-moonshine::icon>` inside alert content
- **Content centering**: Text content inside alert is automatically centered with the icon
- **Icon override**: Use `icon` parameter only when you want a different icon than the default

**❌ Don't do this:**
```blade
<x-moonshine::alert type="success">
    <x-moonshine::icon icon="check"></x-moonshine::icon> <!-- Unnecessary -->
    Success message
</x-moonshine::alert>
```

**✅ Do this:**
```blade
<x-moonshine::alert type="success">
    Success message
</x-moonshine::alert>

<!-- Or with custom icon -->
<x-moonshine::alert type="success" icon="s.heart">
    Success message
</x-moonshine::alert>
```

### Modal (Modal Window)
**Purpose:** Creating modal windows
```blade
<x-moonshine::modal
    title="Title"
    :wide="false"
    :auto="false"
    :closeOutside="true"
    :async="false"
    :asyncUrl="null"
>
    <div>Modal content</div>

    <x-slot name="outerHtml">
        <x-moonshine::link-button @click.prevent="toggleModal">
            Open Modal
        </x-moonshine::link-button>
    </x-slot>
</x-moonshine::modal>
```
**Parameters:**
- `title` (string) - modal title
- `wide` (bool) - wide modal window
- `auto` (bool) - automatic width based on content
- `closeOutside` (bool) - close on outside click
- `async` (bool) - asynchronous content loading
- `asyncUrl` (string) - URL for async loading

### Table (Data Table)
**Purpose:** Displaying tabular data

There are two ways to create tables in MoonShine: using arrays (simple data) or using slots (HTML content and components).

#### Method 1: Array-based Tables (Simple Data)

Use this method for simple text data without HTML or components:

```blade
<x-moonshine::table
    :columns="['#', 'Name', 'Email', 'Role', 'Date']"
    :values="[
        [1, 'John Doe', 'john@example.com', 'Admin', '01.01.2024'],
        [2, 'Jane Smith', 'jane@example.com', 'Editor', '05.01.2024'],
        [3, 'Peter Jones', 'peter@example.com', 'User', '10.01.2024']
    ]"
    :simple="false"
    :notfound="true"
>
</x-moonshine::table>
```

**Parameters:**
- `columns` (array) - column headers
- `values` (array) - table data (plain text only)
- `simple` (bool) - simplified view
- `notfound` (bool) - enable "no data found" alert when values array is empty

**⚠️ Limitations of Array-based Tables:**
- Cannot use HTML tags in values
- Cannot use MoonShine components (badges, buttons, icons)
- Cannot use complex layouts
- Limited styling options

#### Method 2: Slot-based Tables (HTML & Components)

**⚠️ RECOMMENDED:** Use this method when you need:
- HTML content in cells
- MoonShine components (badges, buttons, icons)
- Action buttons
- Complex cell layouts
- Custom styling

```blade
<x-moonshine::table>
    <x-slot:thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </x-slot:thead>

    <x-slot:tbody>
        <tr>
            <td>1</td>
            <td>Ivan Ivanov</td>
            <td>ivan@example.com</td>
            <td>Admin</td>
            <td>01.01.2024</td>
            <td>
                <x-moonshine::badge color="success">Active</x-moonshine::badge>
            </td>
            <td>
                <x-moonshine::layout.flex justify-align="end" without-space class="gap-2">
                    <x-moonshine::link-button href="/users/1" class="btn-square">
                        <x-slot:icon>
                            <x-moonshine::icon icon="eye" />
                        </x-slot:icon>
                    </x-moonshine::link-button>

                    <x-moonshine::link-button href="/users/1/edit" class="btn-square btn-secondary">
                        <x-slot:icon>
                            <x-moonshine::icon icon="pencil" />
                        </x-slot:icon>
                    </x-moonshine::link-button>

                    <x-moonshine::link-button href="/users/1/delete" class="btn-square btn-error">
                        <x-slot:icon>
                            <x-moonshine::icon icon="trash" />
                        </x-slot:icon>
                    </x-moonshine::link-button>
                </x-moonshine::layout.flex>
            </td>
        </tr>

        <tr>
            <td>2</td>
            <td>Maria Petrova</td>
            <td>maria@example.com</td>
            <td>Editor</td>
            <td>05.01.2024</td>
            <td>
                <x-moonshine::badge color="warning">Pending</x-moonshine::badge>
            </td>
            <td>
                <x-moonshine::layout.flex justify-align="end" without-space class="gap-2">
                    <x-moonshine::link-button href="/users/2" class="btn-square">
                        <x-slot:icon>
                            <x-moonshine::icon icon="eye" />
                        </x-slot:icon>
                    </x-moonshine::link-button>

                    <x-moonshine::link-button href="/users/2/edit" class="btn-square btn-secondary">
                        <x-slot:icon>
                            <x-moonshine::icon icon="pencil" />
                        </x-slot:icon>
                    </x-moonshine::link-button>

                    <x-moonshine::link-button href="/users/2/delete" class="btn-square btn-error">
                        <x-slot:icon>
                            <x-moonshine::icon icon="trash" />
                        </x-slot:icon>
                    </x-moonshine::link-button>
                </x-moonshine::layout.flex>
            </td>
        </tr>
    </x-slot:tbody>

    <x-slot:tfoot>
        <!-- Optional footer content -->
    </x-slot:tfoot>
</x-moonshine::table>
```

**Slot-based Table Structure:**
- `thead` slot - table header with `<tr>` and `<th>` tags
- `tbody` slot - table body with `<tr>` and `<td>` tags
- `tfoot` slot - optional table footer

**Common Components in Slot-based Tables:**

**Status Badges:**
```blade
<td>
    <x-moonshine::badge color="success">Active</x-moonshine::badge>
    <x-moonshine::badge color="error">Blocked</x-moonshine::badge>
    <x-moonshine::badge color="warning">Pending</x-moonshine::badge>
    <x-moonshine::badge color="info">Review</x-moonshine::badge>
</td>
```

**Action Buttons (Icon Only):**

**IMPORTANT:** When button contains ONLY an icon (no text):
- ALWAYS add `btn-square` class - makes button square-shaped
- Add `btn-secondary` for edit actions
- Add `btn-error` for delete/destructive actions

```blade
<td>
    <x-moonshine::layout.flex justify-align="end" without-space class="gap-2">
        <!-- View button - icon only, use btn-square -->
        <x-moonshine::link-button href="/view" class="btn-square">
            <x-slot:icon>
                <x-moonshine::icon icon="eye" />
            </x-slot:icon>
        </x-moonshine::link-button>

        <!-- Edit button - icon only, use btn-square + btn-secondary -->
        <x-moonshine::link-button href="/edit" class="btn-square btn-secondary">
            <x-slot:icon>
                <x-moonshine::icon icon="pencil" />
            </x-slot:icon>
        </x-moonshine::link-button>

        <!-- Delete button - icon only, use btn-square + btn-error -->
        <x-moonshine::link-button href="/delete" class="btn-square btn-error">
            <x-slot:icon>
                <x-moonshine::icon icon="trash" />
            </x-slot:icon>
        </x-moonshine::link-button>
    </x-moonshine::layout.flex>
</td>
```

**Action Buttons (With Text):**
```blade
<td>
    <x-moonshine::layout.flex justify-align="end" without-space class="gap-2">
        <x-moonshine::link-button href="/edit" class="btn-sm btn-secondary">
            <x-slot:icon>
                <x-moonshine::icon icon="pencil" />
            </x-slot:icon>
            Edit
        </x-moonshine::link-button>

        <x-moonshine::link-button href="/delete" class="btn-sm btn-error">
            <x-slot:icon>
                <x-moonshine::icon icon="trash" />
            </x-slot:icon>
            Delete
        </x-moonshine::link-button>
    </x-moonshine::layout.flex>
</td>
```

**Boolean Status:**
```blade
<td>
    <x-moonshine::boolean :value="true" />
</td>
```

**Images/Avatars:**
```blade
<td>
    <img src="/avatar.jpg" alt="User" class="w-10 h-10 rounded-full">
</td>
```

**Dynamic Tables with Blade Loop:**
```blade
<x-moonshine::table>
    <x-slot:thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </x-slot:thead>

    <x-slot:tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <x-moonshine::badge :color="$user->is_active ? 'success' : 'error'">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </x-moonshine::badge>
            </td>
            <td>
                <x-moonshine::layout.flex justify-align="end" without-space class="gap-2">
                    <x-moonshine::link-button href="/users/{{ $user->id }}" class="btn-square">
                        <x-slot:icon>
                            <x-moonshine::icon icon="eye" />
                        </x-slot:icon>
                    </x-moonshine::link-button>

                    <x-moonshine::link-button href="/users/{{ $user->id }}/edit" class="btn-square btn-secondary">
                        <x-slot:icon>
                            <x-moonshine::icon icon="pencil" />
                        </x-slot:icon>
                    </x-moonshine::link-button>
                </x-moonshine::layout.flex>
            </td>
        </tr>
        @endforeach
    </x-slot:tbody>
</x-moonshine::table>
```

**Empty State Handling:**
```blade
<x-moonshine::table>
    <x-slot:thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </x-slot:thead>

    <x-slot:tbody>
        @forelse($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">
                <x-moonshine::alert type="info">
                    No users found
                </x-moonshine::alert>
            </td>
        </tr>
        @endforelse
    </x-slot:tbody>
</x-moonshine::table>
```

**Best Practices:**

1. **Use slots when you need:**
   - Badges, buttons, icons, or any MoonShine components
   - HTML formatting in cells
   - Action buttons with icons
   - Complex cell layouts
   - Dynamic content with Blade directives

2. **Use arrays when you have:**
   - Simple text data only
   - No need for styling or components
   - Static data that doesn't change

3. **Action buttons styling:**
   - Use `btn-square` class for icon-only buttons
   - Use `btn-sm` for buttons with text
   - Wrap multiple buttons in `<x-moonshine::layout.flex>` with `gap-2` class
   - Use semantic colors: `btn-secondary` (edit), `btn-error` (delete), default (view)

4. **Always wrap action buttons in flex container:**
   ```blade
   <x-moonshine::layout.flex justify-align="end" without-space class="gap-2">
       <!-- buttons here -->
   </x-moonshine::layout.flex>
   ```

### Form (Form)
**Purpose:** Creating forms
```blade
<x-moonshine::form
    name="contact-form"
    :errors="$errors"
    :precognitive="false"
    action="/submit"
    method="POST"
>
    <x-moonshine::form.input
        name="name"
        placeholder="Your name"
        value=""
        required
    />

    <x-moonshine::form.input
        name="email"
        type="email"
        placeholder="Email"
        value=""
        required
    />

    <x-moonshine::form.textarea
        name="message"
        placeholder="Message"
        rows="5"
    ></x-moonshine::form.textarea>

    <x-slot:buttons>
        <x-moonshine::form.button type="reset">Cancel</x-moonshine::form.button>
        <x-moonshine::form.button class="btn-primary">Submit</x-moonshine::form.button>
    </x-slot:buttons>
</x-moonshine::form>
```
**Form Parameters:**
- `name` (string) - form name
- `errors` (object) - validation errors object
- `precognitive` (bool) - pre-validation
- `action` (string) - form processing URL
- `method` (string) - HTTP method

**Input Parameters:**
- `name` (string) - field name
- `type` (string) - field type: 'text', 'email', 'password', 'number'
- `placeholder` (string) - placeholder text
- `value` (string) - field value
- `required` (bool) - required field

### Dropdown (Dropdown Menu)
**Purpose:** Creating dropdown menus
```blade
<x-moonshine::dropdown
    title="Dropdown title"
    placement="bottom-start"
    :searchable="false"
    searchPlaceholder="Search..."
>
    <div class="m-4">Dropdown content</div>

    <x-slot:toggler>Click me</x-slot:toggler>
    <x-slot:footer>Footer content</x-slot:footer>
</x-moonshine::dropdown>
```
**Parameters:**
- `title` (string) - dropdown title
- `placement` (string) - position: 'top', 'bottom', 'left', 'right', 'bottom-start', etc.
- `searchable` (bool) - enable search
- `searchPlaceholder` (string) - search placeholder

### Collapse (Collapsible Content)
**Purpose:** Creating collapsible content blocks
```blade
<x-moonshine::collapse
    :label="'Collapsible Section'"
    :open="false"
    :persist="true"
>
    <div>Collapsible content here</div>
</x-moonshine::collapse>
```
**Parameters:**
- `label` (string) - collapse title
- `open` (bool) - expanded by default
- `persist` (bool) - save state

### Breadcrumbs (Navigation Breadcrumbs)
**Purpose:** Creating navigation breadcrumbs
```blade
<x-moonshine::breadcrumbs
    :items="[
        '/' => 'Home',
        '/articles' => 'Articles',
        '#' => 'Current Page'
    ]"
/>
```
**Parameters:**
- `items` (array) - breadcrumb items (URL => Label)

### Badge (Label/Badge)
**Purpose:** Creating labels and badges
```blade
<x-moonshine::badge color="success" size="sm">Active</x-moonshine::badge>
```
**Parameters:**
- `color` (string) - color: 'primary', 'secondary', 'success', 'warning', 'error', 'info'
- `size` (string) - size: 'sm', 'md', 'lg'

### Divider (Content Divider)
**Purpose:** Dividing content
```blade
<x-moonshine::layout.divider>Divider text</x-moonshine::layout.divider>
```

### Progress Bar (Progress Indicator)
**Purpose:** Displaying progress
```blade
<x-moonshine::progress-bar
    :value="75"
    :max="100"
    color="success"
    :radial="false"
    size="sm"
>
    75%
</x-moonshine::progress-bar>
```
**Parameters:**
- `value` (int) - current value
- `max` (int) - maximum value
- `color` (string) - color: 'primary', 'secondary', 'success', 'warning', 'error'
- `radial` (bool) - circular progress bar
- `size` (string) - size: 'sm', 'md', 'lg'

**⚠️ Important Slot Usage:**
- **Plain text only**: Add simple text content to the slot without any wrappers or HTML tags
- **No extra classes**: Text is automatically centered and styled by the component
- **Content examples**: Percentage values like "75%", status text like "Loading...", or completion info like "3 of 10"

**Examples:**
```blade
<!-- With percentage text -->
<x-moonshine::progress-bar :value="75" :max="100" color="success">
    75%
</x-moonshine::progress-bar>

<!-- With status text -->
<x-moonshine::progress-bar :value="50" :max="100" color="primary">
    Processing...
</x-moonshine::progress-bar>

<!-- With completion info -->
<x-moonshine::progress-bar :value="3" :max="10" color="warning">
    3 of 10 completed
</x-moonshine::progress-bar>

<!-- Empty slot is also valid -->
<x-moonshine::progress-bar :value="80" :max="100" color="success">
</x-moonshine::progress-bar>
```

**❌ Don't do this:**
```blade
<!-- Don't wrap text in divs or spans -->
<x-moonshine::progress-bar :value="75" :max="100">
    <div class="text-center">75%</div> <!-- Unnecessary wrapper -->
</x-moonshine::progress-bar>

<!-- Don't add extra styling classes -->
<x-moonshine::progress-bar :value="75" :max="100">
    <span class="font-bold">75%</span> <!-- Unnecessary styling -->
</x-moonshine::progress-bar>
```

### Tabs (Tab Navigation)
**Purpose:** Creating tabbed interfaces
```blade
<x-moonshine::tabs
    :tabs="[
        ['id' => 'tab1', 'label' => 'Tab 1'],
        ['id' => 'tab2', 'label' => 'Tab 2']
    ]"
>
    <x-slot name="tab1">First tab content</x-slot>
    <x-slot name="tab2">Second tab content</x-slot>
</x-moonshine::tabs>
```
**Parameters:**
- `tabs` (array) - array of tabs with id and label

### Spinner (Loading Indicator)
**Purpose:** Loading indicator
```blade
<x-moonshine::spinner
    size="md"
    color="primary"
    :absolute="false"
>
</x-moonshine::spinner>
```
**Parameters:**
- `size` (string) - size: 'sm', 'md', 'lg', 'xl'
- `color` (string) - color: 'primary', 'secondary', 'success', 'warning', 'error'
- `absolute` (bool) - absolute positioning

### Carousel (Image Carousel)
**Purpose:** Creating image carousels
```blade
<x-moonshine::carousel
    :items="['/images/image1.jpg', '/images/image2.jpg']"
    :portrait="false"
    alt="Image description"
/>
```
**Parameters:**
- `items` (array) - array of image URLs
- `portrait` (bool) - portrait orientation
- `alt` (string) - alternative text

### Popover (Hover Tooltip)
**Purpose:** Creating hover tooltips
```blade
<x-moonshine::popover title="Popover title" placement="right">
    <x-slot:trigger>
        <button class="btn">Hover me</button>
    </x-slot:trigger>
    <p>Popover content here</p>
</x-moonshine::popover>
```
**Parameters:**
- `title` (string) - popover title
- `placement` (string) - position relative to trigger

### Rating (Star Rating)
**Purpose:** Displaying star ratings
```blade
<x-moonshine::rating value="4" min="1" max="5" />
```
**Parameters:**
- `value` (int) - current rating value
- `min` (int) - minimum value
- `max` (int) - maximum value

### Icon (Standalone Icon)
**Purpose:** Displaying individual icons
```blade
<!-- Outline icons (default) -->
<x-moonshine::icon icon="home" />
<x-moonshine::icon icon="users" />
<x-moonshine::icon icon="cog-6-tooth" />

<!-- Solid icons -->
<x-moonshine::icon icon="s.home" />
<x-moonshine::icon icon="s.users" />
<x-moonshine::icon icon="s.heart" />

<!-- Mini icons -->
<x-moonshine::icon icon="m.star" />
<x-moonshine::icon icon="m.bell" />

<!-- Micro icons -->
<x-moonshine::icon icon="c.check" />
<x-moonshine::icon icon="c.x-mark" />

<!-- With custom size and color -->
<x-moonshine::icon icon="academic-cap" size="8" color="primary" />
```
**Parameters:**
- `icon` (string) - Heroicons name with optional prefix (s./m./c.)
- `size` (int) - icon size (default 5)
- `color` (string) - icon color: 'primary', 'secondary', 'success', 'warning', 'error'

**Icon Reference:**
- Full icon list: https://heroicons.com/
- **Outline (default)**: `icon="icon-name"`
- **Solid**: `icon="s.icon-name"`
- **Mini**: `icon="m.icon-name"`
- **Micro**: `icon="c.icon-name"`

**Common Icons:**
- Navigation: `home`, `users`, `cog-6-tooth`, `chart-bar`, `document-text`
- Actions: `plus`, `pencil`, `trash`, `eye`, `arrow-right`
- Status: `check-circle`, `x-circle`, `exclamation-triangle`, `information-circle`
- UI: `bars-3`, `x-mark`, `magnifying-glass`, `bell`, `heart`

### Logo (Brand Logo)
**Purpose:** Displaying brand logo with automatic responsive behavior

**⚠️ IMPORTANT:** The `logo` parameter is **REQUIRED**. You must provide a path to the logo image file.

```blade
<!-- Basic logo -->
<x-moonshine::layout.logo
    href="/"
    logo="/images/logo.svg"
/>

<!-- Logo with small version for mobile/minimized sidebar -->
<x-moonshine::layout.logo
    href="/"
    logo="/images/logo.svg"
    logo-small="/images/logo-small.svg"
    :minimized="false"
/>

<!-- Logo with title tooltip -->
<x-moonshine::layout.logo
    href="/"
    logo="/images/logo.svg"
    logo-small="/images/logo-small.svg"
    title="Company Name"
/>

<!-- Logo that adapts to sidebar state -->
<x-moonshine::layout.logo
    href="/dashboard"
    logo="/images/logo-full.svg"
    logo-small="/images/logo-icon.svg"
    :minimized="true"
/>
```

**Parameters:**
- `href` (string, required) - URL where logo links when clicked
- `logo` (string, **REQUIRED**) - path to main logo image (relative path like `/images/logo.svg` or absolute URL)
- `logo-small` (string, optional) - path to small/icon version of logo
- `title` (string, optional) - tooltip text on hover
- `minimized` (bool) - whether to show small logo (interacts with Sidebar state)

**Logo Path Examples:**
```blade
<!-- Relative path (recommended) -->
logo="/images/logo.svg"
logo="/vendor/moonshine/logo.png"
logo="/storage/logo.jpg"

<!-- Absolute URL -->
logo="https://example.com/logo.svg"
```

**Logo Behavior:**
- **Responsive**: Automatically switches between full and small logo based on available space
- **Sidebar integration**: When sidebar is collapsed, shows small logo if provided
- **Minimized mode**: When `minimized="true"`, shows small logo by default
- **Fallback**: If no small logo provided, scales down the main logo

**Best Practices:**
- **Provide both versions**: Always include both full and small logo for best UX
- **SVG format recommended**: Vector graphics scale better across all screen sizes
- **Consistent branding**: Ensure small logo maintains brand recognition
- **Appropriate sizing**: Small logo should work well at ~32px dimensions

**Common Use Cases:**
```blade
<!-- In sidebar header -->
<x-moonshine::layout.sidebar>
    <x-moonshine::layout.div class="menu-header">
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo
                href="/"
                logo="/images/company-logo.svg"
                logo-small="/images/company-icon.svg"
                title="Company Dashboard"
            />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.sidebar>

<!-- In top navigation -->
<x-moonshine::layout.header>
    <x-moonshine::layout.div class="menu-logo">
        <x-moonshine::layout.logo
            href="/"
            logo="/images/horizontal-logo.svg"
        />
    </x-moonshine::layout.div>
</x-moonshine::layout.header>
```

### Link (Styled Links)
**Purpose:** Creating styled links with various appearances

**IMPORTANT:** The `icon` is a **slot**, not an attribute. Use `<x-slot:icon>` with `<x-moonshine::icon>` component inside.

```blade
<!-- Basic link button -->
<x-moonshine::link-button href="/dashboard">
    Go to Dashboard
</x-moonshine::link-button>

<!-- Native link style -->
<x-moonshine::link-native href="/profile">
    View Profile
</x-moonshine::link-native>

<!-- Filled link button -->
<x-moonshine::link-button href="/settings" :filled="true">
    Settings
</x-moonshine::link-button>

<!-- Link button with icon - CORRECT WAY -->
<x-moonshine::link-button href="/create">
    <x-slot:icon>
        <x-moonshine::icon icon="plus" />
    </x-slot:icon>
    Create User
</x-moonshine::link-button>

<!-- Link button with @click event and icon -->
<x-moonshine::link-button @click.prevent="toggleModal">
    <x-slot:icon>
        <x-moonshine::icon icon="s.plus" />
    </x-slot:icon>
    Create User
</x-moonshine::link-button>

<!-- ❌ WRONG: Don't use icon as attribute -->
<!-- <x-moonshine::link-button icon="plus" href="/create"> -->
```

**Parameters:**
- `href` (string) - link URL (optional if using @click)
- `filled` (bool) - filled button style
- Standard link attributes: `target`, `title`, etc.
- Standard Vue attributes: `@click`, `@submit`, etc.

**Slots:**
- `icon` - for adding an icon using `<x-moonshine::icon>` component

**Link Types:**
- **link-button**: Styled as button (supports icon slot)
- **link-native**: Natural link appearance

**Usage Notes:**
- Icons must be added via `<x-slot:icon>` slot, NOT as an attribute
- Can be used with Vue event handlers like `@click.prevent`
- Use `@click.prevent` to prevent default link behavior when opening modals

**Styling Classes:**
- **`btn-square`** - Use when button contains ONLY an icon (no text). Makes the button square-shaped for better appearance
- **`btn-secondary`** - Secondary action styling (e.g., edit buttons)
- **`btn-error`** - Error/danger styling. Use for delete/destructive actions
- **`btn-sm`** - Small button size

**Examples with classes:**
```blade
<!-- Icon-only button - ALWAYS use btn-square -->
<x-moonshine::link-button href="/view" class="btn-square">
    <x-slot:icon>
        <x-moonshine::icon icon="eye" />
    </x-slot:icon>
</x-moonshine::link-button>

<!-- Edit button - use btn-square + btn-secondary -->
<x-moonshine::link-button href="/edit" class="btn-square btn-secondary">
    <x-slot:icon>
        <x-moonshine::icon icon="pencil" />
    </x-slot:icon>
</x-moonshine::link-button>

<!-- Delete button - use btn-square + btn-error -->
<x-moonshine::link-button href="/delete" class="btn-square btn-error">
    <x-slot:icon>
        <x-moonshine::icon icon="trash" />
    </x-slot:icon>
</x-moonshine::link-button>

<!-- Button with icon AND text - NO btn-square needed -->
<x-moonshine::link-button href="/create" class="btn-primary">
    <x-slot:icon>
        <x-moonshine::icon icon="plus" />
    </x-slot:icon>
    Create New
</x-moonshine::link-button>
```

### OffCanvas (Side Panel)
**Purpose:** Creating slide-out side panels. **Perfect for filters, forms, settings, and navigation.**

**Common Use Cases:**
- 🔍 **Filters** - Ideal for search filters and data filtering forms
- ⚙️ **Settings** - Configuration panels
- 📋 **Forms** - Data entry forms
- 🧭 **Navigation** - Mobile menus and navigation

```blade
<!-- Basic panel -->
<x-moonshine::off-canvas
    title="Settings Panel"
    :left="false"
    :wide="false"
    :full="false"
    :open="false"
>
    <x-slot:toggler>
        Open Settings
    </x-slot:toggler>

    <div>
        <p>Panel content here</p>
        <button class="btn btn-primary">Save</button>
    </div>
</x-moonshine::off-canvas>

<!-- Filters panel - PERFECT USE CASE -->
<x-moonshine::off-canvas title="Filters" :wide="true">
    <x-slot:toggler>
        Filters
    </x-slot:toggler>

    <x-moonshine::form name="filters-form">
        <x-moonshine::form.input name="search" placeholder="Search..." />
        <x-moonshine::form.select name="status" placeholder="Status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </x-moonshine::form.select>
        <x-moonshine::form.select name="role" placeholder="Role">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </x-moonshine::form.select>
        <button type="submit" class="btn btn-primary">Apply Filters</button>
    </x-moonshine::form>
</x-moonshine::off-canvas>

<!-- Left-positioned navigation panel -->
<x-moonshine::off-canvas title="Navigation" :left="true">
    <x-slot:toggler>
        <x-moonshine::icon icon="bars-3" />
        Menu
    </x-slot:toggler>

    <nav>
        <a href="/home">Home</a>
        <a href="/about">About</a>
        <a href="/contact">Contact</a>
    </nav>
</x-moonshine::off-canvas>

<!-- Wide form panel -->
<x-moonshine::off-canvas title="Edit User" :wide="true">
    <x-slot:toggler>Edit User</x-slot:toggler>

    <x-moonshine::form name="edit-form">
        <!-- Form fields here -->
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </x-moonshine::form>
</x-moonshine::off-canvas>
```
**Parameters:**
- `title` (string) - panel title
- `left` (bool) - position on left side (default: right)
- `wide` (bool) - wider panel (recommended for filters and forms)
- `full` (bool) - full width panel
- `open` (bool) - open by default

**Best Practices:**
- Use `:wide="true"` for filters and forms - provides more space for multiple fields
- Use `:left="true"` for navigation menus - follows common UX patterns
- Keep the default right position for settings and general panels
- Add appropriate icons to toggler for better UX (e.g., `funnel` for filters, `bars-3` for menu)

**Important Toggler Slot Note:**
⚠️ The `toggler` slot content is automatically wrapped in a `<button>` element by MoonShine. **Do not add an additional button element inside the toggler slot** - just provide the text or icon content directly.

```blade
<!-- ✅ Correct - text/icon only in toggler slot -->
<x-slot:toggler>
    <x-moonshine::icon icon="bars-3" />
    Menu
</x-slot:toggler>

<!-- ❌ Incorrect - don't wrap in button -->
<x-slot:toggler>
    <button>Menu</button> <!-- This creates nested buttons -->
</x-slot:toggler>
```

**OffCanvas Features:**
- **Positioning**: Left or right side of screen
- **Width options**: Normal, wide, or full width
- **Async loading**: Load content dynamically
- **Event system**: Toggle via JavaScript events
- **Auto-close**: Configurable closing behavior

**Event Integration:**
```blade
<!-- Trigger via ActionButton -->
ActionButton::make('Open Panel')->toggleOffCanvas('my-panel')

<!-- Manual event triggering -->
<button @click="$dispatch('off_canvas_toggled:my-panel')">
    Toggle Panel
</button>
```


### Heading (Text Headings)
**Purpose:** Creating styled headings with customizable levels
```blade
<!-- Basic heading -->
<x-moonshine::heading h="1">
    Main Page Title
</x-moonshine::heading>

<!-- Different heading levels -->
<x-moonshine::heading h="2">Section Title</x-moonshine::heading>
<x-moonshine::heading h="3">Subsection Title</x-moonshine::heading>
<x-moonshine::heading h="4">Small Heading</x-moonshine::heading>

<!-- Heading with semantic HTML tag -->
<x-moonshine::heading h="1" :asClass="false">
    Real H1 Tag
</x-moonshine::heading>

<!-- Custom tag with heading class -->
<x-moonshine::heading h="2" tag="p">
    Paragraph with H2 Style
</x-moonshine::heading>
```
**Parameters:**
- `h` (int) - heading level (1-6)
- `asClass` (bool) - use CSS class instead of HTML heading tag (default: true)
- `tag` (string) - custom HTML tag to use

**Heading Behavior:**
- **Default**: Uses `<div>` with heading classes (e.g., `class="h1"`)
- **Semantic**: Set `asClass="false"` to use actual `<h1>`, `<h2>`, etc. tags
- **Custom tag**: Override HTML tag while keeping heading styles
- **Responsive**: Heading styles adapt to screen size

### Burger (Mobile Menu Button)
**Purpose:** Mobile menu toggle button (hamburger icon) that controls different menu locations

```blade
<!-- Burger for sidebar (default behavior) -->
<x-moonshine::layout.burger sidebar />

<!-- Burger for topbar -->
<x-moonshine::layout.burger topbar />

<!-- Burger for mobile-bar -->
<x-moonshine::layout.burger mobile-bar />
```

**Location Attributes:**
- **`sidebar`** - Controls sidebar menu (default if no attribute specified)
- **`topbar`** - Controls top navigation bar menu
- **`mobile-bar`** - Controls mobile-specific dropdown menu

**Important:**
The burger button must specify which menu it controls by adding the appropriate location attribute. This determines what menu will open when the burger is clicked.

**Common Integration Examples:**

```blade
<!-- In Sidebar -->
<x-moonshine::layout.sidebar :collapsed="true">
    <x-moonshine::layout.div class="menu-header">
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo href="/" :logo="'/logo.png'"/>
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger sidebar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.sidebar>

<!-- In TopBar -->
<x-moonshine::layout.top-bar>
    <x-moonshine::layout.div class="menu-actions">
        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger topbar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.top-bar>

<!-- In MobileBar -->
<x-moonshine::layout.mobile-bar>
    <x-moonshine::layout.div class="menu-burger">
        <x-moonshine::layout.burger mobile-bar />
    </x-moonshine::layout.div>
</x-moonshine::layout.mobile-bar>
```

### TopBar (Top Navigation)
**Purpose:** Creating top navigation panels

```blade
<!-- Full TopBar structure with all sections -->
<x-moonshine::layout.top-bar>
    <x-moonshine::layout.div class="menu-logo">
        <x-moonshine::layout.logo
            href="/"
            logo="/logo.svg"
            logo-small="/logo-small.svg"
            :minimized="true"
        />
    </x-moonshine::layout.div>

    <x-moonshine::layout.div class="menu menu--horizontal">
        <x-moonshine::layout.menu
            :top="true"
            :elements="[
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Users', 'url' => '/users'],
                ['label' => 'Settings', 'url' => '/settings']
            ]"
        />
    </x-moonshine::layout.div>

    <x-moonshine::layout.div class="menu-actions">
        <div class="menu-divider menu-divider--vertical"></div>
        <x-moonshine::layout.theme-switcher/>
        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger topbar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.top-bar>

<!-- TopBar with Sidebar (must be inside wrapper) -->
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.top-bar>
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo href="/" logo="/logo.svg"/>
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu menu--horizontal">
            <x-moonshine::layout.menu
                :top="true"
                :elements="[
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'About', 'url' => '/about']
                ]"
            />
        </x-moonshine::layout.div>
    </x-moonshine::layout.top-bar>

    <x-moonshine::layout.sidebar>
        <!-- Sidebar content -->
    </x-moonshine::layout.sidebar>

    <x-moonshine::layout.div class="layout-main">
        <x-moonshine::layout.div class="layout-page">
            <x-moonshine::layout.content>
                <!-- Main content -->
            </x-moonshine::layout.content>
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

**Important Wrapper Structure:**

TopBar uses specific CSS class wrappers for proper styling and functionality:

1. **`menu-logo`** - Wrapper for the logo component (logo must have `logo="/path/to/logo.svg"` attribute)
2. **`menu menu--horizontal`** - Wrapper for the navigation menu (horizontal orientation)
3. **`menu-actions`** - Wrapper for theme switcher, profile, and other action components
4. **`menu-burger`** - Wrapper for the burger button (burger must have `topbar` attribute)
5. **`menu-divider menu-divider--vertical`** - Optional vertical divider between action elements

**⚠️ CRITICAL:** Logo component **must** have the `logo` attribute with a path to the image file, otherwise it will cause an error.

**⚠️ Important TopBar + Sidebar Layout:**
When using TopBar together with Sidebar, the TopBar must be placed **inside the wrapper** as the **first child element**, positioned above the sidebar in the wrapper structure.

**TopBar Features:**
- **Menu integration**: Support for navigation menus
- **Flexible layout**: Can contain various navigation components
- **Responsive design**: Adapts to different screen sizes
- **Sidebar compatibility**: Works with sidebar layouts when properly structured


### Thumbnails (Image Gallery)
**Purpose:** Displaying image thumbnails and galleries
```blade
<!-- Basic thumbnails -->
<x-moonshine::thumbnails
    :items="[
        '/images/thumb1.jpg',
        '/images/thumb2.jpg',
        '/images/thumb3.jpg'
    ]"
/>

<!-- Thumbnails with click action -->
<x-moonshine::thumbnails
    :items="[
        ['src' => '/images/thumb1.jpg', 'alt' => 'Image 1'],
        ['src' => '/images/thumb2.jpg', 'alt' => 'Image 2']
    ]"
    :clickable="true"
    :gallery="true"
/>
```
**Parameters:**
- `items` (array) - array of image URLs or objects with src/alt
- `clickable` (bool) - enable click to enlarge
- `gallery` (bool) - enable gallery mode for navigation between images

### When (Conditional Rendering)
**Purpose:** Conditionally rendering content based on conditions
```blade
<!-- Simple condition -->
<x-moonshine::when :condition="$user->isAdmin()">
    <x-moonshine::alert type="info">
        Admin panel content
    </x-moonshine::alert>
</x-moonshine::when>

<!-- With fallback content -->
<x-moonshine::when :condition="$user->hasPermission('edit')">
    <x-slot:then>
        <x-moonshine::action-button>Edit</x-moonshine::action-button>
    </x-slot:then>
    <x-slot:else>
        <span class="text-gray-500">No permission to edit</span>
    </x-slot:else>
</x-moonshine::when>
```
**Parameters:**
- `condition` (bool) - condition to evaluate for rendering

### ThemeSwitcher (Theme Toggle)
**Purpose:** Allowing users to switch between light and dark themes
```blade
<!-- Basic theme switcher -->
<x-moonshine::layout.theme-switcher />

<!-- Theme switcher with custom styling -->
<x-moonshine::layout.theme-switcher class="custom-theme-switcher" />
```

**ThemeSwitcher Features:**
- **Automatic detection**: Detects system theme preference
- **Persistent**: Remembers user's theme choice
- **Smooth transition**: Animated theme switching
- **Icon display**: Shows appropriate sun/moon icons



### MobileBar (Mobile Navigation)
**Purpose:** Optional mobile-specific dropdown menu panel that allows separate control over mobile navigation

The `MobileBar` component is used when you want to customize the mobile dropdown panel independently from your desktop navigation (TopBar or Sidebar). By default, mobile menus duplicate the content from TopBar or Sidebar, but MobileBar gives you full control over what appears in the mobile dropdown.

```blade
<!-- Full MobileBar structure with all sections -->
<x-moonshine::layout.mobile-bar>
    <x-moonshine::layout.div class="menu-logo">
        <x-moonshine::layout.logo
            href="/"
            logo="/logo.svg"
            logo-small="/logo-small.svg"
            :minimized="true"
        />
    </x-moonshine::layout.div>

    <x-moonshine::layout.div class="menu menu--horizontal">
        <x-moonshine::layout.divider label="Mobile bar" />

        <x-moonshine::layout.menu
            :top="true"
            :elements="[
                ['label' => 'Dashboard', 'url' => '/'],
                ['label' => 'Section', 'url' => '/section']
            ]"
        />
    </x-moonshine::layout.div>

    <x-moonshine::layout.div class="menu-actions">
        <div class="menu-divider menu-divider--vertical"></div>
        <x-moonshine::layout.theme-switcher/>
        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger mobile-bar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.mobile-bar>
```

**Important Wrapper Structure:**

MobileBar uses the same wrapper structure as TopBar:

1. **`menu-logo`** - Wrapper for the logo component (logo must have `logo="/path/to/logo.svg"` attribute)
2. **`menu menu--horizontal`** - Wrapper for the navigation menu (horizontal orientation)
3. **`menu-actions`** - Wrapper for theme switcher, profile, and other action components
4. **`menu-burger`** - Wrapper for the burger button (burger must have `mobile-bar` attribute)
5. **`menu-divider menu-divider--vertical`** - Optional vertical divider between action elements

**⚠️ CRITICAL:** Logo component **must** have the `logo` attribute with a path to the image file, otherwise it will cause an error.

**Important Notes:**

- **Optional component**: MobileBar is not required. If omitted, mobile menu will duplicate TopBar or Sidebar content
- **Placement**: MobileBar must be placed **above** Sidebar and TopBar in the layout structure
- **Use case**: Useful when desktop navigation (TopBar/Sidebar) differs from what should appear in mobile dropdown
- **Burger attribute**: Burger inside MobileBar must have `mobile-bar` attribute

**Example Scenario:**
```blade
<!-- Desktop shows TopBar menu, mobile shows different MobileBar menu -->
<x-moonshine::layout.wrapper>
    <!-- MobileBar for mobile devices (appears first) -->
    <x-moonshine::layout.mobile-bar>
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo href="/" logo="/logo.svg"/>
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu menu--horizontal">
            <x-moonshine::layout.menu
                :elements="[
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Mobile-Only Menu', 'url' => '/mobile']
                ]"
            />
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger mobile-bar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.mobile-bar>

    <!-- TopBar for desktop -->
    <x-moonshine::layout.top-bar>
        <x-moonshine::layout.div class="menu-logo">
            <x-moonshine::layout.logo href="/" logo="/logo.svg"/>
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu menu--horizontal">
            <x-moonshine::layout.menu
                :elements="[
                    ['label' => 'Dashboard', 'url' => '/dashboard'],
                    ['label' => 'Analytics', 'url' => '/analytics']
                ]"
            />
        </x-moonshine::layout.div>

        <x-moonshine::layout.div class="menu-burger">
            <x-moonshine::layout.burger topbar />
        </x-moonshine::layout.div>
    </x-moonshine::layout.top-bar>

    <!-- Main content -->
    <x-moonshine::layout.div class="layout-main">
        <x-moonshine::layout.div class="layout-page">
            <!-- Page content -->
        </x-moonshine::layout.div>
    </x-moonshine::layout.div>
</x-moonshine::layout.wrapper>
```

### Metrics (Metrics Display)
**Purpose:** Displaying metrics and statistics
```blade
<!-- Basic metric -->
<x-moonshine::metric
    label="Total Users"
    :value="1250"
    :change="+12%"
    color="success"
/>

<!-- Metric with icon -->
<x-moonshine::metric
    label="Revenue"
    :value="'$45,230'"
    :change="-3%"
    color="error"
    icon="currency-dollar"
/>

<!-- Metric grid -->
<x-moonshine::layout.grid>
    <x-moonshine::layout.column :colSpan="3">
        <x-moonshine::metric label="Orders" :value="156" />
    </x-moonshine::layout.column>
    <x-moonshine::layout.column :colSpan="3">
        <x-moonshine::metric label="Revenue" :value="'$12,450'" />
    </x-moonshine::layout.column>
</x-moonshine::layout.grid>
```
**Parameters:**
- `label` (string) - metric label
- `value` (string|int) - metric value
- `change` (string) - change percentage (optional)
- `color` (string) - color theme: 'success', 'error', 'warning', 'info'
- `icon` (string) - Heroicons icon name

### Flash (Flash Messages)
**Purpose:** Displaying flash messages and temporary notifications
```blade
<!-- Success flash message -->
<x-moonshine::flash type="success">
    Your changes have been saved successfully!
</x-moonshine::flash>

<!-- Error flash message -->
<x-moonshine::flash type="error">
    An error occurred while processing your request.
</x-moonshine::flash>

<!-- Flash with automatic dismiss -->
<x-moonshine::flash
    type="info"
    :removable="true"
    :timeout="5000"
>
    This message will disappear in 5 seconds.
</x-moonshine::flash>
```
**Parameters:**
- `type` (string) - message type: 'success', 'error', 'warning', 'info'
- `removable` (bool) - show close button
- `timeout` (int) - auto-dismiss timeout in milliseconds

### Files (File Upload/Display)
**Purpose:** File upload and file display components
```blade
<!-- File upload -->
<x-moonshine::files
    name="documents"
    :multiple="true"
    :accept="['pdf', 'doc', 'docx']"
    :max-size="10"
/>

<!-- File display -->
<x-moonshine::files
    :files="[
        ['name' => 'document.pdf', 'url' => '/files/document.pdf', 'size' => '2.5 MB'],
        ['name' => 'image.jpg', 'url' => '/files/image.jpg', 'size' => '1.2 MB']
    ]"
    :download="true"
    :preview="true"
/>
```
**Parameters:**
- `name` (string) - input name for uploads
- `multiple` (bool) - allow multiple file selection
- `accept` (array) - accepted file types
- `max-size` (int) - maximum file size in MB
- `files` (array) - array of file objects for display
- `download` (bool) - enable download links
- `preview` (bool) - enable file preview

### FieldsGroup (Field Grouping)
**Purpose:** Grouping form fields together
```blade
<x-moonshine::fields-group
    :label="'Personal Information'"
    :collapsible="true"
    :collapsed="false"
>
    <x-moonshine::form.input name="first_name" placeholder="First Name" />
    <x-moonshine::form.input name="last_name" placeholder="Last Name" />
    <x-moonshine::form.input name="email" type="email" placeholder="Email" />
</x-moonshine::fields-group>

<x-moonshine::fields-group
    :label="'Address Information'"
    :collapsible="true"
>
    <x-moonshine::form.input name="address" placeholder="Address" />
    <x-moonshine::form.input name="city" placeholder="City" />
    <x-moonshine::form.input name="postal_code" placeholder="Postal Code" />
</x-moonshine::fields-group>
```
**Parameters:**
- `label` (string) - group label
- `collapsible` (bool) - enable expand/collapse functionality
- `collapsed` (bool) - start in collapsed state

### Title (Page Title)
**Purpose:** Renders an `<h1>` heading for the page. Must be used inside `<x-moonshine::layout.content>`.

**IMPORTANT:** This component generates an `<h1>` tag, NOT a `<title>` meta tag.

```blade
<!-- Basic title (inside content component) -->
<x-moonshine::layout.content>
    <x-moonshine::title>
        Dashboard Overview
    </x-moonshine::title>

    <!-- Your page content here -->
</x-moonshine::layout.content>

<!-- Title with subtitle -->
<x-moonshine::layout.content>
    <x-moonshine::title subtitle="Manage your application data">
        Admin Panel
    </x-moonshine::title>
</x-moonshine::layout.content>

<!-- Title with icon in slot -->
<x-moonshine::layout.content>
    <x-moonshine::title>
        User Management
        <x-slot:slot>
            <x-moonshine::icon icon="users" />
        </x-slot:slot>
    </x-moonshine::title>
</x-moonshine::layout.content>
```

**Parameters:**
- `subtitle` (string) - optional subtitle text below the main title

**Slots:**
- `slot` - for adding small HTML elements like icons next to the title (NOT for breadcrumbs)

**Usage Notes:**
- Always use inside `<x-moonshine::layout.content>`
- Generates semantic `<h1>` heading
- The default slot is for adding visual elements (icons, badges, etc.)
- DO NOT use the slot for breadcrumbs - breadcrumbs are separate components

### Loader (Loading Indicator)
**Purpose:** Displaying loading states
```blade
<!-- Basic loader -->
<x-moonshine::loader />

<!-- Loader with custom text -->
<x-moonshine::loader text="Loading data..." />

<!-- Loader with overlay -->
<x-moonshine::loader
    :overlay="true"
    text="Processing..."
/>

<!-- Inline loader -->
<x-moonshine::loader
    size="sm"
    :inline="true"
/>
```
**Parameters:**
- `text` (string) - loading text message
- `overlay` (bool) - show overlay background
- `inline` (bool) - display inline instead of block
- `size` (string) - size: 'sm', 'md', 'lg'


### Menu (Navigation Menu)
**Purpose:** Creating navigation menus

```blade
<!-- Sidebar menu (vertical orientation) -->
<x-moonshine::layout.div class="menu menu--vertical">
    <x-moonshine::layout.menu
        :elements="[
            ['label' => 'Dashboard', 'url' => '/', 'icon' => 'home'],
            ['label' => 'Users', 'url' => '/users', 'icon' => 'users'],
            ['label' => 'Settings', 'url' => '/settings', 'icon' => 'cog-6-tooth']
        ]"
    />
</x-moonshine::layout.div>

<!-- Top menu (horizontal orientation) -->
<x-moonshine::layout.div class="menu menu--horizontal">
    <x-moonshine::layout.menu
        :top="true"
        :elements="[
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'About', 'url' => '/about'],
            ['label' => 'Contact', 'url' => '/contact']
        ]"
    />
</x-moonshine::layout.div>
```

**Parameters:**
- `elements` (array) - menu items with label, url, and optional icon
- `top` (bool) - indicates this is a top menu (for TopBar or MobileBar)

**⚠️ Important Menu Wrapper:**
Menu component must always be wrapped in a div with appropriate CSS classes:
- **Sidebar menu**: `<x-moonshine::layout.div class="menu menu--vertical">` - vertical orientation
- **TopBar/MobileBar menu**: `<x-moonshine::layout.div class="menu menu--horizontal">` - horizontal orientation

These wrappers are required for proper styling, collapse functionality, and responsive behavior.

### FlexibleRender (Dynamic Content)
**Purpose:** Rendering dynamic content based on data
```blade
<x-moonshine::flexible-render
    :data="$dynamicData"
    :template="$templateName"
    :fallback="'No content available'"
/>
```
**Parameters:**
- `data` (mixed) - dynamic data to render
- `template` (string) - template name to use
- `fallback` (string) - fallback content when no data


### LineBreak (Line Break)
**Purpose:** Adding line breaks and spacing
```blade
<!-- Simple line break -->
<x-moonshine::line-break />

<!-- Line break with custom spacing -->
<x-moonshine::line-break :height="20" />

<!-- Multiple line breaks -->
<x-moonshine::line-break :count="3" />
```
**Parameters:**
- `height` (int) - custom height in pixels
- `count` (int) - number of line breaks

### Meta (Meta Tags)
**Purpose:** Adding HTML meta tags
```blade
<x-moonshine::layout.meta
    name="description"
    content="Dashboard for managing your application"
/>

<x-moonshine::layout.meta
    property="og:title"
    content="Admin Dashboard"
/>

<x-moonshine::layout.meta
    name="viewport"
    content="width=device-width, initial-scale=1"
/>
```
**Parameters:**
- `name` (string) - meta name attribute
- `property` (string) - meta property attribute
- `content` (string) - meta content value

### Boolean (Boolean Display)
**Purpose:** Displaying boolean values with visual indicators
```blade
<!-- Basic boolean display -->
<x-moonshine::boolean :value="true" />
<x-moonshine::boolean :value="false" />

<!-- Boolean with custom labels -->
<x-moonshine::boolean
    :value="$user->is_active"
    true-label="Active"
    false-label="Inactive"
/>

<!-- Boolean with custom colors -->
<x-moonshine::boolean
    :value="$status"
    true-color="success"
    false-color="error"
/>
```
**Parameters:**
- `value` (bool) - boolean value to display
- `true-label` (string) - label for true state
- `false-label` (string) - label for false state
- `true-color` (string) - color for true state
- `false-color` (string) - color for false state

### Color (Color Display/Picker)
**Purpose:** Displaying colors and color picking
```blade
<!-- Color display -->
<x-moonshine::color :value="'#3B82F6'" />

<!-- Color picker -->
<x-moonshine::color
    name="theme_color"
    :value="'#3B82F6'"
    :picker="true"
/>

<!-- Color with label -->
<x-moonshine::color
    :value="'#10B981'"
    label="Primary Color"
/>
```
**Parameters:**
- `value` (string) - color hex value
- `name` (string) - input name for picker mode
- `picker` (bool) - enable color picker functionality
- `label` (string) - color label

### Div (Layout Div)
**Purpose:** Creating layout containers with MoonShine styling
```blade
<!-- Basic div container -->
<x-moonshine::layout.div>
    Content here
</x-moonshine::layout.div>

<!-- Div with custom classes -->
<x-moonshine::layout.div class="custom-container">
    <p>Custom styled content</p>
</x-moonshine::layout.div>

<!-- Div with flex layout -->
<x-moonshine::layout.div class="flex items-center justify-between">
    <span>Left content</span>
    <span>Right content</span>
</x-moonshine::layout.div>
```

### Assets (Asset Management)
**Purpose:** Managing CSS and JavaScript assets
```blade
<!-- MoonShine default assets -->
<x-moonshine::layout.assets>
    @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
</x-moonshine::layout.assets>

<!-- Custom assets -->
<x-moonshine::layout.assets>
    @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
    @vite(['resources/css/custom.css', 'resources/js/custom.js'])
</x-moonshine::layout.assets>
```

### Attributes (Dynamic Attributes)
**Purpose:** Adding dynamic HTML attributes to components
```blade
<x-moonshine::attributes
    :attributes="[
        'data-turbo' => 'false',
        'class' => 'custom-class',
        'id' => 'unique-id'
    ]"
/>
```
**Parameters:**
- `attributes` (array) - key-value pairs of HTML attributes

### Body (Layout Body)
**Purpose:** Main body container for layouts

**⚠️ IMPORTANT:** This component automatically generates `<body>` tags. Do NOT add `<body>` tags manually.

```blade
<x-moonshine::layout.body>
    <x-moonshine::layout.wrapper>
        <!-- Page content -->
    </x-moonshine::layout.wrapper>
</x-moonshine::layout.body>

<!-- Body with custom attributes -->
<x-moonshine::layout.body class="custom-body">
    <!-- Layout content -->
</x-moonshine::layout.body>
```

**What this component generates:**
- `<body>` opening and closing tags
- Alpine.js and theme integration attributes
- Container for all page layout components

### Components (Component Container)
**Purpose:** Container for grouping multiple components
```blade
<x-moonshine::components>
    <x-moonshine::alert type="info">Information message</x-moonshine::alert>
    <x-moonshine::button>Action Button</x-moonshine::button>
    <x-moonshine::badge color="success">Status</x-moonshine::badge>
</x-moonshine::components>
```

### Favicon (Favicon Management)
**Purpose:** Setting page favicon
```blade
<!-- Default favicon -->
<x-moonshine::layout.favicon />

<!-- Custom favicon -->
<x-moonshine::layout.favicon href="/custom-favicon.ico" />

<!-- Multiple favicon sizes -->
<x-moonshine::layout.favicon
    href="/favicon.ico"
    :sizes="['16x16', '32x32', '48x48']"
/>
```
**Parameters:**
- `href` (string) - favicon file path
- `sizes` (array) - array of icon sizes

### Footer (Page Footer)
**Purpose:** Creating page footers
```blade
<!-- Basic footer -->
<x-moonshine::layout.footer>
    <p>&copy; 2024 Your Company. All rights reserved.</p>
</x-moonshine::layout.footer>

<!-- Footer with links -->
<x-moonshine::layout.footer>
    <x-moonshine::layout.flex :justifyAlign="'between'">
        <div>
            <p>&copy; 2024 Your Company</p>
        </div>
        <div>
            <x-moonshine::link-native href="/privacy">Privacy</x-moonshine::link-native>
            <x-moonshine::link-native href="/terms">Terms</x-moonshine::link-native>
        </div>
    </x-moonshine::layout.flex>
</x-moonshine::layout.footer>
```

### Head (HTML Head)
**Purpose:** Managing HTML head section

**⚠️ IMPORTANT:** This component automatically generates `<head>` tags. Do NOT add `<head>` tags manually.

```blade
<x-moonshine::layout.head>
    <x-moonshine::layout.meta name="csrf-token" :content="csrf_token()" />
    <x-moonshine::layout.meta name="description" content="Page description" />
    <x-moonshine::layout.favicon />
    <x-moonshine::layout.assets>
        @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
    </x-moonshine::layout.assets>
</x-moonshine::layout.head>
```

**What this component generates:**
- `<head>` opening and closing tags
- Base meta tags (charset, viewport)
- Title tag if specified
- Container for custom meta tags, assets, and favicons

### Html (HTML Document)
**Purpose:** HTML document wrapper with MoonShine features

**⚠️ IMPORTANT:** This component automatically generates `<!DOCTYPE html>` and `<html>` tags. Do NOT add these tags manually.

```blade
<x-moonshine::layout.html
    :with-alpine-js="true"
    :with-themes="true"
    lang="en"
>
    <x-moonshine::layout.head>
        <!-- Head content -->
    </x-moonshine::layout.head>
    <x-moonshine::layout.body>
        <!-- Body content -->
    </x-moonshine::layout.body>
</x-moonshine::layout.html>
```
**Parameters:**
- `with-alpine-js` (bool) - include Alpine.js integration
- `with-themes` (bool) - enable theme system
- `lang` (string) - document language

**What this component generates:**
- `<!DOCTYPE html>` declaration
- `<html>` tag with Alpine.js and theme attributes
- Proper charset and viewport meta tags
- All necessary HTML document structure

### Layout (Root Layout)
**Purpose:** Root layout component for MoonShine applications

**⚠️ IMPORTANT:** This is the root wrapper component. Your Blade file should start with this component, not with HTML tags.

```blade
<x-moonshine::layout>
    <x-moonshine::layout.html :with-alpine-js="true" :with-themes="true">
        <x-moonshine::layout.head>
            <!-- Head content -->
        </x-moonshine::layout.head>
        <x-moonshine::layout.body>
            <!-- Body content -->
        </x-moonshine::layout.body>
    </x-moonshine::layout.html>
</x-moonshine::layout>
```

**Key Points:**
- This is always the outermost component
- Do NOT add `<!DOCTYPE html>`, `<html>`, `<head>`, or `<body>` tags manually
- All HTML document structure is generated automatically by child components

## Common Layout Examples

### Dashboard with Sidebar and Grid
```blade
<x-moonshine::layout>
    <x-moonshine::layout.html :with-alpine-js="true" :with-themes="true">
        <x-moonshine::layout.head>
            <x-moonshine::layout.meta name="csrf-token" :content="csrf_token()"/>
            <x-moonshine::layout.favicon />
        </x-moonshine::layout.head>

        <x-moonshine::layout.body>
            <x-moonshine::layout.wrapper>
                <x-moonshine::layout.sidebar>
                    <x-moonshine::layout.div class="menu">
                        <x-moonshine::layout.menu :elements="[
                            ['label' => 'Dashboard', 'url' => '/'],
                            ['label' => 'Users', 'url' => '/users'],
                            ['label' => 'Orders', 'url' => '/orders']
                        ]"/>
                    </x-moonshine::layout.div>
                </x-moonshine::layout.sidebar>

                <x-moonshine::layout.div class="layout-page">
                    <x-moonshine::layout.header>
                        <x-moonshine::breadcrumbs :items="['#' => 'Home']"/>
                    </x-moonshine::layout.header>

                    <x-moonshine::layout.content>
                        <x-moonshine::layout.grid>
                            <x-moonshine::layout.column :colSpan="8">
                                <!-- Main area -->
                                <x-moonshine::layout.box title="Statistics">
                                    <x-moonshine::table
                                        :columns="['Date', 'Sales', 'Profit']"
                                        :values="[
                                            ['01.01.2024', '$1000', '$200'],
                                            ['02.01.2024', '$1200', '$240']
                                        ]"
                                    />
                                </x-moonshine::layout.box>
                            </x-moonshine::layout.column>

                            <x-moonshine::layout.column :colSpan="4">
                                <!-- Sidebar -->
                                <x-moonshine::layout.box title="Quick Actions">
                                    <x-moonshine::alert type="info">
                                        You have 5 new orders
                                    </x-moonshine::alert>
                                </x-moonshine::layout.box>
                            </x-moonshine::layout.column>
                        </x-moonshine::layout.grid>
                    </x-moonshine::layout.content>
                </x-moonshine::layout.div>
            </x-moonshine::layout.wrapper>
        </x-moonshine::layout.body>
    </x-moonshine::layout.html>
</x-moonshine::layout>
```

### Card List Page
```blade
<x-moonshine::layout.wrapper>
    <x-moonshine::layout.content>
        <x-moonshine::layout.grid>
            @foreach($items as $item)
            <x-moonshine::layout.column :colSpan="4" :adaptiveColSpan="12">
                <x-moonshine::card
                    :title="$item['title']"
                    :thumbnail="$item['image']"
                    :url="$item['url']"
                    :values="['Price' => $item['price'], 'Category' => $item['category']]"
                >
                    {{ $item['description'] }}
                </x-moonshine::card>
            </x-moonshine::layout.column>
            @endforeach
        </x-moonshine::layout.grid>
    </x-moonshine::layout.content>
</x-moonshine::layout.wrapper>
```

### Form with Modal
```blade
<x-moonshine::modal title="Contact Form">
    <x-moonshine::form name="contact-form" action="/contact" method="POST">
        <x-moonshine::form.input name="name" placeholder="Your Name" required />
        <x-moonshine::form.input name="email" type="email" placeholder="Email" required />
        <x-moonshine::form.textarea name="message" placeholder="Message" rows="4"></x-moonshine::form.textarea>

        <x-slot:buttons>
            <x-moonshine::form.button type="reset">Cancel</x-moonshine::form.button>
            <x-moonshine::form.button class="btn-primary">Send</x-moonshine::form.button>
        </x-slot:buttons>
    </x-moonshine::form>

    <x-slot name="outerHtml">
        <button class="btn btn-primary" @click.prevent="toggleModal">Contact Us</button>
    </x-slot>
</x-moonshine::modal>
```

## Generation Guidelines

### Critical Requirements

1. **NEVER add HTML tags manually** - Components generate `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` automatically
2. **ALWAYS start with** `<x-moonshine::layout>` as the first line of your Blade file
3. **ALWAYS use required CSS wrappers and attributes**:
   - Logo: `<x-moonshine::layout.div class="menu-logo">` + **REQUIRED** `logo="/path/to/logo.svg"` attribute
   - Sidebar menu: `<x-moonshine::layout.div class="menu menu--vertical">`
   - TopBar/MobileBar menu: `<x-moonshine::layout.div class="menu menu--horizontal">`
   - Burger: `<x-moonshine::layout.div class="menu-burger">` + location attribute
   - Actions: `<x-moonshine::layout.div class="menu-actions">`
4. **ALWAYS include MoonShine assets** in `<x-moonshine::layout.assets>`:
   ```blade
   @vite(['resources/css/main.css', 'resources/js/app.js'], 'vendor/moonshine')
   ```

### Best Practices

5. **For responsiveness** use `Grid` and `Column` with different `colSpan` and `adaptiveColSpan`
6. **For notifications** use `Alert` with appropriate type
7. **For forms** always add CSRF token and error handling
8. **For interactivity** use modal windows and Alpine.js events
9. **Parameters with colon** (`:parameter`) accept PHP expressions
10. **Parameters without colon** accept string values
11. **Boolean parameters** can be passed as `:parameter="true"` or just `parameter`

## ⚠️ Important: Component Styling and Spacing

### Pre-configured Component Styling
**Most MoonShine components come with built-in styling and spacing:**

- **Box, Card, Modal, Alert, etc.** - Already have proper padding, margins, and styling
- **Grid and Flex components** - Have built-in spacing systems
- **Form components** - Pre-styled with appropriate spacing
- **Layout components** - Designed with proper spacing relationships

### When NOT to Add Extra Classes
**Avoid adding these classes to MoonShine components:**
```blade
<!-- ❌ DON'T DO THIS - Box already has padding -->
<x-moonshine::layout.box class="p-4 m-4" title="Title">
    Content
</x-moonshine::layout.box>

<!-- ✅ DO THIS - Use component as-is -->
<x-moonshine::layout.box title="Title">
    Content
</x-moonshine::layout.box>
```

### When TO Add Spacing
**Add spacing between elements INSIDE components based on user requirements:**

```blade
<!-- ✅ Good - Spacing between internal elements -->
<x-moonshine::layout.box title="User Profile">
    <div class="space-y-4">
        <p>User information paragraph</p>
        <button class="btn btn-primary">Edit Profile</button>
        <button class="btn btn-secondary">Delete Account</button>
    </div>
</x-moonshine::layout.box>

<!-- ✅ Good - Spacing between cards in grid -->
<x-moonshine::layout.grid>
    <x-moonshine::layout.column :colSpan="6">
        <div class="space-y-6">
            <x-moonshine::card title="Card 1">Content</x-moonshine::card>
            <x-moonshine::card title="Card 2">Content</x-moonshine::card>
        </div>
    </x-moonshine::layout.column>
</x-moonshine::layout.grid>
```

### User-Driven Spacing Configuration
**Only add custom spacing when user specifically requests it:**

- **User says**: "Add more space between sections" → Add `class="space-y-8"`
- **User says**: "Make cards closer together" → Add `class="space-y-2"`
- **User says**: "Add padding to the box" → Add `class="p-6"` (override default)
- **User doesn't mention spacing** → Use component defaults

### Recommended Spacing Classes
**When user requests spacing, use these Tailwind classes:**

```blade
<!-- Vertical spacing between elements -->
<div class="space-y-2">   <!-- tight spacing -->
<div class="space-y-4">   <!-- normal spacing -->
<div class="space-y-6">   <!-- loose spacing -->
<div class="space-y-8">   <!-- extra loose spacing -->

<!-- Horizontal spacing between elements -->
<div class="space-x-2">   <!-- tight spacing -->
<div class="space-x-4">   <!-- normal spacing -->

<!-- Individual margins when needed -->
<div class="mb-4">        <!-- margin bottom -->
<div class="mt-6">        <!-- margin top -->
<div class="mx-auto">     <!-- center horizontally -->

<!-- Padding overrides (only when user requests) -->
<div class="p-4">         <!-- all sides padding -->
<div class="px-6 py-4">   <!-- horizontal and vertical -->
```

### Key Principles:
1. **MoonShine components are pre-styled** - don't add redundant classes
2. **Space internal content** - based on user needs and layout requirements
3. **User-driven customization** - only add styling when specifically requested
4. **Semantic spacing** - use logical spacing that improves readability

This guide enables creation of full-featured interfaces using all MoonShine component capabilities.
