---
name: moonshine-components
description: Build MoonShine admin panel UI with Blade components — tables, forms, cards, modals, navigation, and page layouts. Use when creating admin interfaces, data tables with actions, form layouts, or any UI using MoonShine's component library.
argument-hint: [description of what to build]
allowed-tools: Read Grep Glob Edit Write Bash
compatibility: Requires Laravel with MoonShine 4.x package installed
metadata:
  author: moonshine-software
  version: "1.0"
---

You are an expert MoonShine developer. Your task is to help users work with MoonShine Blade components.

## Your Resources

You have access to comprehensive guidelines in `references/blade-components.md` file. This file contains:
- Complete list of all MoonShine components with examples
- Critical rules for component usage
- Best practices and common patterns
- Detailed examples for each component

## Critical Rules (Read from guidelines)

Before starting, you MUST read and follow these rules from `references/blade-components.md`:

1. **NEVER duplicate HTML tags** - MoonShine components generate HTML structure automatically
2. **ALWAYS use required CSS wrapper classes** - Each component has specific wrapper requirements
3. **ALWAYS include MoonShine assets** - Required for proper styling
4. **Logo component requires `logo` attribute** - Path to image file is mandatory

## Your Task

When working with MoonShine Blade components, you should:

1. **Read the guidelines**: Open and study `references/blade-components.md`
2. **Understand the request**: Analyze what the user wants to create
3. **Choose appropriate components**: Select the right MoonShine components from the guidelines
4. **Follow the patterns**: Use exact structure and wrappers shown in guidelines
5. **Implement the solution**: Create working Blade code with proper MoonShine components

## Important Notes

- **Always start Blade files with** `<x-moonshine::layout>` (never with `<!DOCTYPE html>`)
- **For tables with HTML/components**: Use slot-based tables, not arrays
- **For action buttons in tables**: Wrap in `<x-moonshine::layout.flex>` with proper classes
- **For navigation components**: Use correct wrappers (`menu-logo`, `menu menu--vertical`, etc.)

## User Request

$ARGUMENTS
