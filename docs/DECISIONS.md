# Decisions log

Judgement calls made during the autonomous v2 build, one line of reasoning each. Newest at the bottom of each phase. **Review these.**

## Given by the owner (final)

- All five proposed behaviour changes from the M0 review are approved (email verification required; 404 for unknown/paused codes; only active plans purchasable; preview at error-correction H; round-style EPS fix).
- Limited free tier: yes. Location QR: keep. MP3 QR: drop. Brand: own product; hossam-x-studios references removed; new palette and typography. Arabic + RTL included.
- Nothing is pushed; everything stays local.

## M1 — Skeleton

- **Fresh Laravel 13.35 skeleton replaces the old app on `redesign/v2`** (old app stays on `master` until M9). The old code and its 128 MB of theme assets therefore leave the branch now, not at M9.
- **Skeleton's `AGENTS.md`/`CLAUDE.md` not kept:** generic "install PHP" agent boilerplate, nothing project-specific.
- **Fortify two-factor auth and passkeys disabled** (and their migrations removed): not in the approved MVP; both can be switched on later in `config/fortify.php`.
- **Email verification enabled in Fortify:** approved behaviour change #1.
- **Tests are ported per phase instead of copied in and skipped:** the old tests reference classes and tables that don't exist in v2, so skipped copies would never run unchanged. `docs/behaviour-inventory.md` maps every old test to its v2 counterpart, and the M9 gate is "every inventory row has a passing v2 test".
- **Tests run in Docker (`bin/test`, PHP 8.5.11 + imagick + gd + intl)** so PNG/EPS paths are always exercised; local PHP 8.4 lacks imagick.
- **Larastan at level 5** in CI: catches real type errors without forcing generics annotations everywhere.
- **Free tier (L5) and Location QR (L7) are built inside the MVP** (M2/M5/M7) instead of later phases: both are now decided, and adding them later would rework the quota logic and the type system just built.
