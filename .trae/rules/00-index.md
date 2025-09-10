# Laravel Blog Project Rules Index

> **Master Reference**: This index provides navigation to all project rules and guidelines for the Laravel Blog application with FluxUI integration.

## Quick Navigation

### 🎨 FluxUI & Frontend
- **[01-fluxui-comprehensive-guide.md](01-fluxui-comprehensive-guide.md)** - Complete FluxUI component usage, priorities, and implementation patterns
- **[02-fluxui-performance-accessibility.md](02-fluxui-performance-accessibility.md)** - Performance optimization, accessibility standards, and responsive design
- **[03-fluxui-design-patterns.md](03-fluxui-design-patterns.md)** - Layout patterns, navigation, forms, and interactive components
- **[07-flux-ui-integration.md](07-flux-ui-integration.md)** - IDE integration rules and component replacement priorities
- **[09-fluxui-demo-patterns.md](09-fluxui-demo-patterns.md)** - Proven implementation patterns from demo examples
- **[10-project-improvement-suggestions.md](10-project-improvement-suggestions.md)** - Project improvement suggestions and enhancement recommendations
- **[11-posts-refactoring-plan.md](11-posts-refactoring-plan.md)** - Posts component refactoring plan and implementation strategy

### 🔧 Development Standards
- **[04-coding-standards.md](04-coding-standards.md)** - PHP coding standards, best practices, and code quality guidelines
- **[05-qa-testing-pipeline.md](05-qa-testing-pipeline.md)** - Mandatory QA pipeline with Laravel Pint, PHPStan, and Pest testing
- **[06-laravel-project-guidelines.md](06-laravel-project-guidelines.md)** - Laravel Boost guidelines, package versions, and application structure
- **[08-route-parameter-guidelines.md](08-route-parameter-guidelines.md)** - Laravel route parameter best practices and common error prevention

---

## File Organization

### Naming Convention
All rule files follow the pattern: `{number}-{category}-{description}.md`
- **00-09**: Core project rules and guidelines
- **01-03, 09**: FluxUI-specific documentation
- **04-08**: Development standards and Laravel guidelines

### Priority Levels
1. **Critical (01-03)**: FluxUI implementation - must be followed for UI consistency
2. **High (04-06)**: Development standards - essential for code quality
3. **Medium (07-08)**: Specialized guidelines - important for specific scenarios

---

## Rule Categories Overview

### 🎨 FluxUI Implementation
**Purpose**: Ensure consistent, modern UI using FluxUI components

**Key Files**:
- Component hierarchy and replacement rules
- Performance and accessibility standards
- Design patterns and layout templates
- IDE integration and development workflow

**Essential Rules**:
- Always use FluxUI components over raw HTML
- Follow component priority: Pro > Free > Custom > HTML
- Implement proper accessibility and responsive design
- Use established design patterns for consistency

### 🔧 Development Standards
**Purpose**: Maintain high code quality and consistent development practices

**Key Files**:
- PHP coding standards and best practices
- Mandatory QA pipeline requirements
- Laravel project structure and guidelines
- Route parameter handling and validation

**Essential Rules**:
- Follow PSR-12 coding standards
- Run Laravel Pint, PHPStan, and Pest before commits
- Use proper Laravel conventions and patterns
- Implement secure route parameter handling

---

## Quick Reference

### Package Versions (Current)
- **Laravel Framework**: v11.31.0
- **FluxUI**: v1.0.10
- **Livewire**: v3.5.6
- **Livewire Volt**: v1.6.8
- **Pest**: v3.5.1
- **Laravel Pint**: v1.18.1
- **Tailwind CSS**: v4.0.0-alpha.25

### Essential Commands
```bash
# Quality Assurance
vendor/bin/pint --dirty          # Code formatting
vendor/bin/phpstan analyse       # Static analysis
php artisan test                 # Run tests

# Development
npm run dev                      # Frontend development
php artisan about                # Verify setup
php artisan make:volt component  # Create Volt component
```

### Critical FluxUI Replacements
| HTML Element | FluxUI Component | Priority |
|--------------|------------------|----------|
| `<form>` | `<flux:form>` | Critical |
| `<input>` | `<flux:input>` | Critical |
| `<button>` | `<flux:button>` | Critical |
| `<table>` | `<flux:table>` | High |
| `<select>` | `<flux:select>` | High |
| `<nav>` | `<flux:navigation>` | Medium |

---

## Usage Guidelines

### For New Team Members
1. **Start Here**: Read this index to understand the project structure
2. **FluxUI First**: Review files 01-03 for UI development
3. **Standards**: Study files 04-06 for coding practices
4. **Specialization**: Reference files 07-08 as needed

### For Development Tasks
1. **UI Components**: Check FluxUI guides (01-03) for component usage
2. **Code Quality**: Follow coding standards (04) and QA pipeline (05)
3. **Laravel Features**: Reference project guidelines (06) for Laravel patterns
4. **Routing**: Use route parameter guidelines (08) for URL handling

### For Code Reviews
1. Verify FluxUI component usage follows priority rules
2. Ensure code passes all QA pipeline checks
3. Confirm Laravel conventions are followed
4. Check route parameter security and validation

---

## Maintenance

### Updating Rules
- Rules should be updated when package versions change
- New patterns should be documented in appropriate files
- Breaking changes require team notification

### File Structure
- Keep numbered prefixes for logical ordering
- Maintain clear separation between FluxUI and Laravel rules
- Update this index when adding new rule files

---

## Support

### Getting Help
- **FluxUI Issues**: Check comprehensive guide (01) and design patterns (03)
- **Code Quality**: Review coding standards (04) and QA pipeline (05)
- **Laravel Questions**: Reference project guidelines (06)
- **Performance**: See performance guide (02)

### Contributing
- Follow the established file naming convention
- Update this index when adding new rules
- Ensure all examples are tested and working
- Maintain consistency with existing documentation style

---

*Last Updated: January 2025 | Laravel Blog Project v1.0*