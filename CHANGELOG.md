# Changelog

All notable changes to `starter-kit-setup` will be documented in this file.

## v2.0.1 - 2026-03-20

- Update solo.php to the latest version - Upstream package dependency update
- Refactor AddMailpitCommand - Improved insertion of Mailpit line in solo config
- Update Laravel matrix in run-tests workflow - Added Laravel 13 support to CI/CD
- Update PHP matrix in run-tests workflow - Added PHP 8.5 support to CI/CD
- Update PHP- Stan workflow to PHP 8.5 - Updated linting environment and standardized quotes

Key themes:

PHP 8.5 and Laravel 13 support additions
Dependency and workflow maintenance
Minor refactoring and code quality improvements

**Full Changelog**: https://github.com/onelegstudios/starter-kit-setup/compare/v2.0.0...v2.0.1

## v2,0,0 - 2026-03-19

### What's Changed

- Bump ramsey/composer-install from 3 to 4 by @dependabot[bot] in https://github.com/onelegstudios/starter-kit-setup/pull/13
- Add Laravel 13 support

**Full Changelog**: https://github.com/onelegstudios/starter-kit-setup/compare/v1.1.0...v2.0.0

## V1.1.0 - 2026-03-16

### What's Changed

- Rename UsingHerdCommand to UsingBuiltinServerCommand by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/8
- Add SetupCommand implementation and tests by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/9
- Refactor TestCase setup and teardown for parallel mode by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/10
- Update description and default value for built-in server confirmation… by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/11
- Rename "check" script to "verify" in composer.json for clarity by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/12

**Full Changelog**: https://github.com/onelegstudios/starter-kit-setup/compare/v1.0.0...v1.1.0

## v1.0.0 - 2026-02-17

### What's Changed

- prepere for 0.1.2 by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/7

**Full Changelog**: https://github.com/onelegstudios/starter-kit-setup/compare/0.1.1...v1.0.0

## v0.1.1 - 2026-02-12

### What's Changed

- Remove Facades by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/2
- Fix tests and workbench setup to work with GitHub actions by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/4
- Remove config and database paths from PHPStan configuration by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/5
- Fixed tests, testbench and PHPstan by @oneleggedswede in https://github.com/onelegstudios/starter-kit-setup/pull/6

### New Contributors

- @oneleggedswede made their first contribution in https://github.com/onelegstudios/starter-kit-setup/pull/2

**Full Changelog**: https://github.com/onelegstudios/starter-kit-setup/compare/0.1.0...0.1.1

## v0.1.0 - 2026-02-06

### What's Changed

- Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/onelegstudios/starter-kit-setup/pull/1

### Tests

- The test is working locally. Should figure out how to set them up properly with GitHub later.
-

### New Contributors

- @dependabot[bot] made their first contribution in https://github.com/onelegstudios/starter-kit-setup/pull/1

**Full Changelog**: https://github.com/onelegstudios/starter-kit-setup/commits/0.1.0
