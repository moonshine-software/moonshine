# Mutation testing

Run from the MoonShine repository root, using PHP 8.4+ with PCOV or Xdebug coverage enabled:

```bash
composer test:mutation
```

When resolving dependencies on a feature branch, set `COMPOSER_ROOT_VERSION=5.x-dev` so the monorepo's `self.version` replacements satisfy import/export's MoonShine 5 requirement. The mutation workflow sets this automatically.

The command uses existing Pest tests marked with `mutates()` or `covers()`. The initial scope includes CRUD authorization, safe URLs, Text escaping, and BelongsToMany persistence. Add `mutates(Target::class)` to the feature test exercising a new target; use `mutates()` when coverage-report filtering is unnecessary.

Only executed lines are mutated (`--covered-only`), so this score measures assertion strength within covered code, not overall code coverage. Inspect surviving mutations and strengthen observable assertions, including database state after denied writes. Some mutations preserve behavior; review them before adding tests or exclusions solely to increase the score.

The minimum score is 85%. A lower score or a run producing no mutations fails the command. Increase this threshold as the selected tests improve. The command reports all surviving mutations instead of stopping at the first one.

For a focused investigation:

```bash
vendor/bin/pest tests/Feature/Components/LinkSecurityTest.php --mutate --covered-only
```

Tests use MySQL configured in `phpunit.xml.dist`. To avoid another test session's database, create a dedicated database and pass its name:

```bash
DB_DATABASE=moonshine_tests_mutation composer test:mutation
```

The command runs sequentially. Do not run another suite against the same database at the same time. The GitHub Actions mutation workflow also runs on same-repository pull requests, daily schedules, and manual dispatches.
