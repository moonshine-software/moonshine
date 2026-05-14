# MoonShine Color Palettes - AI Guidelines

## Overview

This guide helps AI assistants create and modify custom color palettes for MoonShine admin panel. Palettes define the visual theme including colors for buttons, backgrounds, text, borders, and semantic states (success, error, warning, info).

## Color Format

Colors use OKLCH color space format: `L C H` where:
- **L** (Lightness): 0.0 to 1.0 (0 = black, 1 = white)
- **C** (Chroma): 0.0 to 0.4 (0 = grayscale, higher = more saturated)
- **H** (Hue): 0 to 360 degrees (color angle)

Example: `0.58 0.24 293.756` means:
- Lightness: 0.58 (medium)
- Chroma: 0.24 (moderately saturated)
- Hue: 293.756° (purple)

For transparency: add `/ opacity%` like `0.58 0.24 293.756 / 20%`

## Palette Structure

### Basic Implementation

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class MyCustomPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'My custom palette description';
    }

    public function getColors(): array
    {
        // Light theme colors
    }

    public function getDarkColors(): array
    {
        // Dark theme colors
    }
}
```

### Registration

**Method 1: In MoonShine Layout (Recommended)**

Edit `app/MoonShine/Layouts/MoonShineLayout.php`:

```php
protected ?string $palette = \App\MoonShine\Palettes\MyCustomPalette::class;
```

**Method 2: In Config (Global)**

Add to `config/moonshine.php`:

```php
'palette' => \App\MoonShine\Palettes\MyCustomPalette::class,
```

**Method 3: For Blade Component Demos**

In your route file:

```php
Route::get('/demo', function (ColorManagerContract $colorManager) {
    $colorManager->palette(new MyCustomPalette);
    return view('demo');
});
```

## Color Keys Reference

### Primary Colors

| Key | Purpose | Light Theme | Dark Theme |
|-----|---------|-------------|------------|
| `primary` | Main brand color, primary buttons background | Medium-dark shade | Medium-light shade |
| `primary-text` | Text on primary buttons | Light (for contrast) | Dark (for contrast) |
| `secondary` | Secondary actions, less prominent elements | Lighter than primary | Medium shade |
| `secondary-text` | Text on secondary buttons | Dark (for contrast) | Light (for contrast) |

**Design Rule**: Ensure sufficient contrast between `primary` and `primary-text` (same for `secondary`). If `primary` is dark, `primary-text` must be light, and vice versa.

### Semantic Colors

| Key | Purpose | Typical Hue | Light Theme | Dark Theme |
|-----|---------|-------------|-------------|------------|
| `success` | Success states, confirmations | Green (120-150°) | Medium-dark | Medium-light |
| `success-text` | Text for success messages | Green | Darker | Lighter |
| `warning` | Warnings, cautions | Orange/Yellow (60-90°) | Medium-light | Bright |
| `warning-text` | Text for warnings | Orange/Yellow | Darker | Very light |
| `error` | Errors, destructive actions | Red (0-30°) | Medium | Medium |
| `error-text` | Text for errors | Red | Dark | Light-medium |
| `info` | Information, neutral notices | Blue (240-270°) | Medium | Medium |
| `info-text` | Text for info messages | Blue | Dark | Light |

### Base Colors

| Key | Purpose | Light Theme | Dark Theme |
|-----|---------|-------------|------------|
| `body` | Background of entire page | Very light (0.95-0.99) | Very dark (0.15-0.25) |
| `base.default` | Primary background color for content | Very light (0.95-0.99) | Dark (0.20-0.30) |
| `base.text` | Main text color | Dark (0.20-0.30) | Light (0.85-0.95) |
| `base.stroke` | Borders, dividers | Medium with opacity | Medium with opacity |
| `base.50` to `base.900` | Gradient shades | Light to dark | Dark to light |

**Base Shades Scale** (50-900):
- **Light Theme**: 50 (lightest) → 900 (darkest)
  - 50-400: Lighter shades for backgrounds, highlights
  - 500-700: Medium shades for borders, subtle elements
  - 800-900: Darker shades for text, emphasis

- **Dark Theme**: 50 (darkest) → 900 (lightest) - **INVERTED**
  - 50-400: Darker shades for backgrounds
  - 500-700: Medium shades for borders
  - 800-900: Lighter shades for text, highlights

## Creating a Palette: Step-by-Step

### 1. Choose Your Primary Hue

Select a hue angle (0-360°) for your brand color:
- Red: 0-30°
- Orange: 30-60°
- Yellow: 60-90°
- Green: 90-150°
- Cyan: 150-210°
- Blue: 210-270°
- Purple: 270-330°
- Magenta: 330-360°

### 2. Light Theme Colors

```php
public function getColors(): array
{
    return [
        // Background - very light
        'body' => '0.985 0.008 [HUE]',

        // Primary - medium-dark with good saturation
        'primary' => '0.58 0.24 [HUE]',

        // Primary text - very light for contrast
        'primary-text' => '0.985 0.008 [HUE]',

        // Secondary - lighter than primary
        'secondary' => '0.92 0.06 [HUE]',

        // Secondary text - dark for contrast
        'secondary-text' => '0.22 0.02 [HUE]',

        'base' => [
            // Text - dark
            'text' => '0.22 0.02 [HUE]',

            // Borders - medium with transparency
            'stroke' => '0.58 0.24 [HUE] / 20%',

            // Default background - very light
            'default' => '0.985 0.008 [HUE]',

            // Shades from light to dark
            50  => '0.969 0.016 [HUE]',
            100 => '0.95 0.025 [HUE]',
            200 => '0.93 0.045 [HUE]',
            300 => '0.90 0.07 [HUE]',
            400 => '0.86 0.11 [HUE]',
            500 => '0.77 0.16 [HUE]',
            600 => '0.67 0.20 [HUE]',
            700 => '0.58 0.24 [HUE]',  // Matches primary
            800 => '0.48 0.19 [HUE]',
            900 => '0.38 0.14 [HUE]',
        ],

        // Semantic colors with appropriate hues
        'success' => '0.64 0.22 142.49',      // Green
        'success-text' => '0.46 0.16 142.49',

        'warning' => '0.75 0.17 75.35',       // Orange
        'warning-text' => '0.5 0.10 76.10',

        'error' => '0.58 0.21 26.855',        // Red
        'error-text' => '0.37 0.145 26.85',

        'info' => '0.60 0.219 257.63',        // Blue
        'info-text' => '0.35 0.12 257.63',
    ];
}
```

### 3. Dark Theme Colors

**Key Differences**:
- Background colors are dark (low L values)
- Text colors are light (high L values)
- Base shades are inverted: 50 is darkest, 900 is lightest
- Primary/secondary are lighter than in light theme

```php
public function getDarkColors(): array
{
    return [
        // Background - dark
        'body' => '0.18 0.04 [HUE]',

        // Primary - medium-light
        'primary' => '0.72 0.18 [HUE]',

        // Primary text - dark for contrast
        'primary-text' => '0.16 0.05 [HUE]',

        // Secondary - medium
        'secondary' => '0.48 0.14 [HUE]',

        // Secondary text - very light for contrast
        'secondary-text' => '0.94 0.04 [HUE]',

        'base' => [
            // Text - light
            'text' => '0.90 0.03 [HUE]',

            // Borders - medium with transparency
            'stroke' => '0.72 0.18 [HUE] / 20%',

            // Default background - dark
            'default' => '0.22 0.05 [HUE]',

            // Shades from dark to light (INVERTED)
            50  => '0.24 0.05 [HUE]',
            100 => '0.29 0.06 [HUE]',
            200 => '0.33 0.07 [HUE]',
            300 => '0.39 0.09 [HUE]',
            400 => '0.46 0.12 [HUE]',
            500 => '0.54 0.15 [HUE]',
            600 => '0.63 0.17 [HUE]',
            700 => '0.72 0.18 [HUE]',  // Matches primary
            800 => '0.80 0.15 [HUE]',
            900 => '0.87 0.12 [HUE]',
        ],

        // Semantic colors adjusted for dark theme
        'success' => '0.64 0.22 142.495',
        'success-text' => '0.93 0.12 144.46',

        'warning' => '0.9 0.22 92.72',
        'warning-text' => '0.99 0.072 107.64',

        'error' => '0.589 0.214 26.855',
        'error-text' => '0.71 0.24 25.96',

        'info' => '0.6 0.22 257.63',
        'info-text' => '0.88 0.065 244.38',
    ];
}
```

## Design Rules & Best Practices

### Contrast Rules

1. **Button Contrast**:
   - Dark `primary` → Light `primary-text`
   - Light `primary` → Dark `primary-text`
   - Same for `secondary`, `success`, `error`, `warning`, `info`

2. **Text Readability**:
   - Light theme: `base.text` should be dark (L: 0.20-0.35)
   - Dark theme: `base.text` should be light (L: 0.85-0.95)

3. **Background Contrast**:
   - Light theme: `body` and `base.default` very light (L: 0.95-0.99)
   - Dark theme: `body` and `base.default` dark (L: 0.15-0.25)

### Color Harmony

1. **Keep Same Hue**: For `primary`, `secondary`, and all `base` shades, use the same hue angle for visual consistency

2. **Vary Lightness & Chroma**:
   - Lightness (L): Changes perceived brightness
   - Chroma (C): Changes saturation/intensity
   - Hue (H): Keep consistent within a color family

3. **Semantic Colors**: Use conventional hues:
   - Success: Green (120-150°)
   - Warning: Orange/Yellow (60-90°)
   - Error: Red (0-30°)
   - Info: Blue (240-270°)

### Progressive Shades

Base shades (50-900) should form a smooth gradient:
- **Lightness**: Decreases gradually from 50 to 900 (light theme)
- **Chroma**: Can vary slightly, typically peaks around 500-700
- **Hue**: Keep constant for consistency

## Example: Creating a Blue Palette

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class OceanBluePalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Ocean blue theme';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.985 0.008 240',
            'primary' => '0.58 0.24 240',
            'primary-text' => '0.985 0.008 240',
            'secondary' => '0.92 0.06 240',
            'secondary-text' => '0.22 0.02 240',
            'base' => [
                'text' => '0.22 0.02 240',
                'stroke' => '0.58 0.24 240 / 20%',
                'default' => '0.985 0.008 240',
                50 => '0.969 0.016 240',
                100 => '0.95 0.025 240',
                200 => '0.93 0.045 240',
                300 => '0.90 0.07 240',
                400 => '0.86 0.11 240',
                500 => '0.77 0.16 240',
                600 => '0.67 0.20 240',
                700 => '0.58 0.24 240',
                800 => '0.48 0.19 240',
                900 => '0.38 0.14 240',
            ],
            'success' => '0.64 0.22 142.49',
            'success-text' => '0.46 0.16 142.49',
            'warning' => '0.75 0.17 75.35',
            'warning-text' => '0.5 0.10 76.10',
            'error' => '0.58 0.21 26.855',
            'error-text' => '0.37 0.145 26.85',
            'info' => '0.60 0.219 240',  // Match primary hue
            'info-text' => '0.35 0.12 240',
        ];
    }

    public function getDarkColors(): array
    {
        return [
            'body' => '0.18 0.04 240',
            'primary' => '0.72 0.18 240',
            'primary-text' => '0.16 0.05 240',
            'secondary' => '0.48 0.14 240',
            'secondary-text' => '0.94 0.04 240',
            'base' => [
                'text' => '0.90 0.03 240',
                'stroke' => '0.72 0.18 240 / 20%',
                'default' => '0.22 0.05 240',
                50 => '0.24 0.05 240',
                100 => '0.29 0.06 240',
                200 => '0.33 0.07 240',
                300 => '0.39 0.09 240',
                400 => '0.46 0.12 240',
                500 => '0.54 0.15 240',
                600 => '0.63 0.17 240',
                700 => '0.72 0.18 240',
                800 => '0.80 0.15 240',
                900 => '0.87 0.12 240',
            ],
            'success' => '0.64 0.22 142.495',
            'success-text' => '0.93 0.12 144.46',
            'warning' => '0.9 0.22 92.72',
            'warning-text' => '0.99 0.072 107.64',
            'error' => '0.589 0.214 26.855',
            'error-text' => '0.71 0.24 25.96',
            'info' => '0.6 0.22 240',
            'info-text' => '0.88 0.065 240',
        ];
    }
}
```

## Modification Guidelines

When modifying existing palettes:

1. **Changing Hue**: Replace all hue values with new angle while keeping L and C values
2. **Adjusting Brightness**: Modify L values but maintain relative relationships
3. **Increasing Saturation**: Increase C values proportionally
4. **Semantic Colors**: Can be modified independently but keep conventional hues

## Common Mistakes to Avoid

1. **Insufficient Contrast**: Dark text on dark background or light on light
2. **Inverted Themes**: Dark backgrounds in light theme or vice versa
3. **Broken Gradients**: Shades that don't progress smoothly
4. **Mismatched Primary**: `base.700` should match `primary` color
5. **Wrong Dark Theme Logic**: Forgetting to invert shade scale (50-900)
6. **Text Visibility**: Ensure `base.text` contrasts with `body`/`base.default`

## Testing Your Palette

After creating a palette, verify:

1. All buttons have readable text (check contrast)
2. Text is visible on all backgrounds
3. Base shades form smooth gradient
4. Dark theme actually looks dark (not inverted light theme)
5. Semantic colors are distinguishable
6. No harsh color combinations that clash

## File Location and Registration

### 1. Create Palette File

Store custom palettes in:
```
app/MoonShine/Palettes/YourPaletteName.php
```

### 2. Register in MoonShine Layout

Open `app/MoonShine/Layouts/MoonShineLayout.php` and set the `$palette` property:

```php
<?php

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\CompactLayout;

final class MoonShineLayout extends CompactLayout
{
    // Set your custom palette here
    protected ?string $palette = \App\MoonShine\Palettes\YourPaletteName::class;

    // ... rest of the layout code
}
```

### 3. Alternative: Register in Config (Global)

You can also register globally in `config/moonshine.php`:
```php
'palette' => \App\MoonShine\Palettes\YourPaletteName::class,
```

**Note:** Layout-level registration (`MoonShineLayout.php`) takes precedence over config-level registration.

### 4. Using Palette in Blade Component Demos

When creating standalone Blade component demos (not part of MoonShine admin), you need to enable the palette in your route:

```php
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use App\MoonShine\Palettes\SpotifyPalette;

Route::get('/spotify-demo', function (ColorManagerContract $colorManager) {
    // Enable the palette for this demo page
    $colorManager->palette(new SpotifyPalette);

    return view('spotify-demo');
});
```

This is necessary because Blade component demos exist outside the MoonShine admin panel context.
