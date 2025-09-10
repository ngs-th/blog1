# Quality Assurance Rules - Laravel Blog Application

This document establishes mandatory quality assurance checks that must be executed after completing any development tasks.

## Mandatory QA Pipeline

After completing ANY development task, the following tools MUST be run in this exact order:

### 1. Laravel Pint (Code Formatting)
```bash
vendor/bin/pint
```

**Purpose**: Ensures PSR-12 coding standards compliance
**Requirements**: 
- Must pass with exit code 0
- All formatting issues must be automatically fixed
- No manual intervention required

### 2. PHPStan (Static Analysis)
```bash
vendor/bin/phpstan analyse app --level=5
```

**Purpose**: Static code analysis and type checking
**Requirements**:
- Must pass at minimum level 5 with zero errors
- Higher levels (6-8) are encouraged but not mandatory
- All type hints and PHPDoc annotations must be properly defined

### 3. Pest (Testing Framework)
```bash
php artisan test
```

**Purpose**: Automated testing suite execution
**Requirements**:
- All existing tests must pass
- New features require corresponding tests
- Minimum 80% code coverage for new code

## Implementation Rules

### When to Run QA Pipeline

The QA pipeline MUST be executed after:

1. **Feature Implementation**: Any new functionality added
2. **Bug Fixes**: Any code modifications to resolve issues
3. **Refactoring**: Code structure or organization changes
4. **Model Changes**: Database schema or Eloquent model updates
5. **Controller Updates**: HTTP request handling modifications
6. **Livewire Components**: Any component creation or modification
7. **Configuration Changes**: Updates to Laravel configuration files

### Failure Handling

If any tool in the QA pipeline fails:

1. **STOP** - Do not proceed to the next tool
2. **FIX** - Address all reported issues
3. **RE-RUN** - Execute the failed tool again
4. **CONTINUE** - Only proceed when the tool passes completely

### Quality Gates

#### Pint Quality Gate
- ✅ **PASS**: All files formatted according to PSR-12
- ❌ **FAIL**: Any formatting violations detected

#### PHPStan Quality Gate
- ✅ **PASS**: Zero errors at level 5
- ❌ **FAIL**: Any type errors, undefined properties, or missing return types

#### Pest Quality Gate
- ✅ **PASS**: All tests green, no failures or errors
- ❌ **FAIL**: Any test failures, errors, or incomplete tests

## Automation Scripts

### Quick QA Check Script
A `qa-check.sh` script has been created to run all QA checks:

```bash
./qa-check.sh
```

### Composer Scripts
The following composer scripts are available for quality assurance:

```bash
# Run complete QA pipeline (Pint + PHPStan + Tests)
composer qa

# Run formatting and static analysis only (no tests)
composer qa-fix

# Run QA shell script
composer qa-check
```

### Script Details
All scripts:
- Run Laravel Pint for code formatting
- Execute PHPStan at level 5 for static analysis
- Run Pest for testing (except qa-fix)
- Exit on first failure
- Provide clear status messages

### Shell Script Implementation
```bash
#!/bin/bash
# File: qa-check.sh

echo "🔧 Running Laravel Pint..."
vendor/bin/pint
if [ $? -ne 0 ]; then
    echo "❌ Pint failed. Please fix formatting issues."
    exit 1
fi

echo "🔍 Running PHPStan..."
vendor/bin/phpstan analyse app --level=5
if [ $? -ne 0 ]; then
    echo "❌ PHPStan failed. Please fix static analysis issues."
    exit 1
fi

echo "🧪 Running Pest Tests..."
php artisan test
if [ $? -ne 0 ]; then
    echo "❌ Tests failed. Please fix failing tests."
    exit 1
fi

echo "✅ All QA checks passed!"
```

### Composer Script Integration
Add to `composer.json`:

```json
{
    "scripts": {
        "qa": [
            "vendor/bin/pint",
            "vendor/bin/phpstan analyse app --level=5",
            "php artisan test"
        ],
        "qa-fix": [
            "vendor/bin/pint",
            "vendor/bin/phpstan analyse app --level=5 --no-progress"
        ]
    }
}
```

Usage:
```bash
composer qa      # Run full QA pipeline
composer qa-fix  # Run formatting and analysis only
```

## Development Workflow Integration

### Pre-Commit Workflow
1. Complete development task
2. Run `composer qa` or `./qa-check.sh`
3. Fix any issues reported
4. Commit only when all QA checks pass

### CI/CD Integration
For automated deployment pipelines:

```yaml
# Example GitHub Actions workflow
name: QA Pipeline
on: [push, pull_request]

jobs:
  qa:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Pint
        run: vendor/bin/pint --test
      - name: Run PHPStan
        run: vendor/bin/phpstan analyse app --level=5
      - name: Run Tests
        run: php artisan test
```

## Exception Handling

### Temporary Bypasses
In exceptional circumstances, QA checks may be temporarily bypassed:

1. **Emergency Hotfixes**: Critical production issues requiring immediate deployment
2. **External Dependencies**: Issues caused by third-party package updates
3. **Legacy Code**: When working with inherited code that doesn't meet current standards

**Requirements for Bypasses**:
- Document the reason in commit message
- Create follow-up task to address QA issues
- Set deadline for compliance restoration

### Progressive Enhancement
For legacy codebases:

1. Start with Pint (formatting) - lowest barrier to entry
2. Gradually increase PHPStan level (5 → 6 → 7 → 8)
3. Improve test coverage incrementally

## Monitoring and Metrics

### Quality Metrics to Track
- PHPStan level progression over time
- Test coverage percentage
- Code formatting compliance rate
- QA pipeline execution frequency

### Reporting
- Weekly QA compliance reports
- Trend analysis of quality improvements
- Identification of problematic code areas

---

## Summary

These QA rules ensure:
- **Consistent Code Quality**: All code follows established standards
- **Early Issue Detection**: Problems caught before deployment
- **Maintainable Codebase**: Clean, well-tested, and documented code
- **Developer Confidence**: Reliable automated quality assurance

**Remember**: Quality is not optional. These rules are mandatory for all development work.

---

*Last Updated: January 2025*
*Version: 1.0*