# CLAUDE.md

All project guidance lives in [AGENTS.md](AGENTS.md) so every coding agent shares the same rules:

@AGENTS.md

## Claude-specific notes

- Communicate with the maintainer in German; keep code identifiers, user-facing docs (README, CHANGELOG) and commit
  messages in English.
- Before larger changes (new features, refactors across several files), briefly outline the plan and wait for an OK.
- Prefer small, focused commits. Don't push, tag or create releases unless asked.
- After editing PHP files, run `php -l` on them before reporting the work as done.
- When adding UI text, update all three language files in the same change.
- When a change affects end users, add an entry to `CHANGELOG.md` under a new or the upcoming version.
