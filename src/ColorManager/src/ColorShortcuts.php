<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

trait ColorShortcuts
{
    public function text(string $value, bool $dark = false, bool $everything = false): static
    {
        return $this
            ->set('base.text', $value, dark: $dark, everything: $everything);
    }

    public function background(string $bg, ?string $pageBg = null, bool $dark = false, bool $everything = false): static
    {
        if ($pageBg !== null) {
            $this->pageBackground($pageBg, dark: $dark, everything: $everything);
        }

        return $this
            ->set('ms-layout-body-bg-color', $bg, dark: $dark, everything: $everything);
    }

    public function pageBackground(string $bg, bool $dark = false, bool $everything = false): static
    {
        return $this
            ->set('ms-layout-page-bg-color', $bg, dark: $dark, everything: $everything);
    }

    public function borders(string $value, bool $dark = false, bool $everything = false): static
    {
        return $this
            ->set('base.stroke', $value, dark: $dark, everything: $everything);
    }

    public function dropzone(
        string $bg,
        ?string $text = null,
        ?string $icon = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($icon !== null) {
            $this->set('ms-dropzone-icon-color', $icon, dark: $dark, everything: $everything);
        }

        if ($text !== null) {
            $this->set('ms-dropzone-file-color', $text, dark: $dark, everything: $everything);
        }

        return $this
            ->set('ms-dropzone-file-bg-color', $bg, dark: $dark, everything: $everything);
    }

    public function dividers(string $value, bool $dark = false, bool $everything = false): static
    {
        return $this
            ->bulkAssign([
                'ms-hr-divider-border-color' => $value,
                'ms-divider-line-bg-color' => $value,
                'ms-divider-color' => $value,
            ], dark: $dark, everything: $everything);
    }

    /** Themes */

    public function primary(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('primary-text', $text, dark: $dark, everything: $everything);
        }

        return $this->set('primary', $bg, dark: $dark, everything: $everything);
    }

    public function secondary(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('secondary-text', $text, dark: $dark, everything: $everything);
        }

        return $this->set('secondary', $bg, dark: $dark, everything: $everything);
    }

    /** error, success, info, warning colors with transparency */

    public function error(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('error-text', $text, dark: $dark, everything: $everything);
        }

        return $this->set('error-bg', $bg, dark: $dark, everything: $everything);
    }

    public function success(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('success-text', $text, dark: $dark, everything: $everything);
        }

        return $this->set('success-bg', $bg, dark: $dark, everything: $everything);
    }

    public function warning(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('warning-text', $text, dark: $dark, everything: $everything);
        }

        return $this->set('warning-bg', $bg, dark: $dark, everything: $everything);
    }

    public function info(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('info-text', $text, dark: $dark, everything: $everything);
        }

        return $this->set('info-bg', $bg, dark: $dark, everything: $everything);
    }

    /** Scrollbar */

    public function scrollbar(string $bg, ?string $hoverBg = null, bool $dark = false, bool $everything = false): static
    {
        if ($hoverBg !== null) {
            $this
                ->set('ms-scrollbar-hover-color', $hoverBg, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-scrollbar-color', $bg, dark: $dark, everything: $everything);
    }

    /** Buttons */
    public function button(
        string $bg,
        ?string $text = null,
        ?string $hoverBg = null,
        ?string $hoverText = null,
        ?string $disabledBg = null,
        ?string $disabledText = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($text !== null) {
            $this->set('ms-btn-color', $text, dark: $dark, everything: $everything);
        }

        if ($hoverBg !== null) {
            $this->set('ms-btn-hover-bg-color', $hoverBg, dark: $dark, everything: $everything);
        }

        if ($hoverText !== null) {
            $this->set('ms-btn-hover-color', $hoverText, dark: $dark, everything: $everything);
        }

        if ($disabledBg !== null) {
            $this->set('ms-btn-disabled-border-color', $disabledBg, dark: $dark, everything: $everything);
            $this->set('ms-btn-disabled-bg-color', $disabledBg, dark: $dark, everything: $everything);
        }

        if ($disabledText !== null) {
            $this->set('ms-btn-disabled-color', $disabledText, dark: $dark, everything: $everything);
        }

        return $this->set('ms-btn-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Menu */

    public function menu(
        string $active,
        ?string $text = null,
        ?string $hoverBg = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($text !== null) {
            $this
                ->set('ms-menu-item-active-color', $text, dark: $dark, everything: $everything)
            ;
        }

        if ($hoverBg !== null) {
            $this
                ->set('ms-menu-item-hover-bg-color', $hoverBg, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-menu-item-active-bg-color', $active, dark: $dark, everything: $everything)
        ;
    }

    /** Dropdown */

    public function dropdown(
        string $bg,
        ?string $text = null,
        ?string $headerBg = null,
        ?string $footerBg = null,
        ?string $hoverBg = null,
        ?string $hoverText = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($text !== null) {
            $this
                ->set('ms-dropdown-color', $text, dark: $dark, everything: $everything)
            ;
        }

        if ($footerBg !== null) {
            $this
                ->set('ms-dropdown-footer-bg-color', $footerBg, dark: $dark, everything: $everything)
            ;
        }

        if ($headerBg !== null) {
            $this
                ->set('ms-dropdown-heading-bg-color', $headerBg, dark: $dark, everything: $everything)
            ;
        }

        if ($hoverBg !== null) {
            $this
                ->set('ms-dropdown-menu-item-hover-bg-color', $hoverBg, dark: $dark, everything: $everything)
            ;
        }

        if ($hoverText !== null) {
            $this
                ->set('ms-dropdown-menu-item-hover-color', $hoverText, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-dropdown-bg-color', $bg, dark: $dark, everything: $everything)
        ;
    }

    /** Alerts */

    public function alert(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('ms-alert-color', $text, dark: $dark, everything: $everything);
        }

        return $this
            ->set('ms-alert-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Badges (has transparency) */

    public function badge(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this->set('ms-badge-color', $text, dark: $dark, everything: $everything);
        }

        return $this
            ->set('ms-badge-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Table */

    public function table(
        string $bg,
        ?string $headBg = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($headBg !== null) {
            $this
                ->set('ms-table-thead-bg-color', $headBg, dark: $dark, everything: $everything);
        }

        return $this
            ->set('ms-table-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Collapses */

    public function collapse(
        string $bg,
        ?string $text = null,
        ?string $bgOpen = null,
        ?string $textOpen = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($text !== null) {
            $this
                ->set('ms-accordion-btn-color', $text, dark: $dark, everything: $everything)
                ->set('ms-accordion-item-color', $text, dark: $dark, everything: $everything)
                ->set('ms-accordion-btn-active-color', $textOpen ?? $text, dark: $dark, everything: $everything)
                ->set('ms-accordion-item-opened-color', $textOpen ?? $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-accordion-item-bg-color', $bg, dark: $dark, everything: $everything)
            ->set('ms-accordion-item-opened-bg-color', $bgOpen ?? $bg, dark: $dark, everything: $everything);
    }

    /** Search buttons */

    public function searchButtons(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-search-key-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-search-key-bg-color', $bg, dark: $dark, everything: $everything)
            ->set('ms-search-key-shadow', $bg, dark: $dark, everything: $everything);
    }

    /** Popovers */

    public function popover(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-popover-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-popover-border-color', $bg, dark: $dark, everything: $everything)
            ->set('ms-popover-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Progress bars */

    public function progress(
        string $bg,
        string $barBg,
        ?string $text = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($text !== null) {
            $this
                ->set('ms-progress-bar-color', $text, dark: $dark, everything: $everything)
                ->set('ms-radial-progress-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-progress-bg-color', $bg, dark: $dark, everything: $everything)
            ->set('ms-progress-bar-bg-color', $barBg, dark: $dark, everything: $everything)
            ->set('ms-radial-progress-track-color', $barBg, dark: $dark, everything: $everything)
        ;
    }

    /** Modals/OffCanvases */

    public function modal(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-modal-content-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-modal-content-bg-color', $bg, dark: $dark, everything: $everything);
    }

    public function offCanvas(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-offcanvas-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-offcanvas-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Boxes */

    public function box(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-box-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-box-bg-color', $bg, dark: $dark, everything: $everything);
    }

    public function boxDark(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-box-dark-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-box-dark-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Cards */

    public function card(string $bg, ?string $text = null, bool $dark = false, bool $everything = false): static
    {
        if ($text !== null) {
            $this
                ->set('ms-card-color', $text, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-card-bg-color', $bg, dark: $dark, everything: $everything);
    }

    /** Forms */

    public function form(
        string $bg,
        ?string $text = null,
        ?string $focus = null,
        ?string $disabled = null,
        ?string $disabledText = null,
        ?string $readonly = null,
        ?string $readonlyText = null,
        ?string $expansion = null,
        ?string $expansionText = null,
        bool $dark = false,
        bool $everything = false
    ): static {
        if ($text !== null) {
            $this
                ->set('ms-form-default-color', $text, dark: $dark, everything: $everything)
            ;
        }

        if ($focus !== null) {
            $this
                ->set('ms-form-focus-border-color', $focus, dark: $dark, everything: $everything)
                ->set('ms-form-focus-ring-color', $focus, dark: $dark, everything: $everything)
            ;
        }

        if ($disabled !== null) {
            $this
                ->set('ms-form-disabled-bg-color', $disabled, dark: $dark, everything: $everything)
            ;
        }

        if ($disabledText !== null) {
            $this
                ->set('ms-form-disabled-color', $disabledText, dark: $dark, everything: $everything)
            ;
        }

        if ($readonly !== null) {
            $this
                ->set('ms-form-readonly-bg-color', $readonly, dark: $dark, everything: $everything)
            ;
        }

        if ($readonlyText !== null) {
            $this
                ->set('ms-form-readonly-color', $readonlyText, dark: $dark, everything: $everything)
            ;
        }

        if ($expansion !== null) {
            $this
                ->set('ms-form-expansion-bg-color', $expansion, dark: $dark, everything: $everything)
            ;
        }

        if ($expansionText !== null) {
            $this
                ->set('ms-form-expansion-color', $expansionText, dark: $dark, everything: $everything)
            ;
        }

        return $this
            ->set('ms-form-default-bg-color', $bg, dark: $dark, everything: $everything);
    }
}
