# Course Archive Trigger (Lifecycle plugin)

A lifecycle trigger that targets courses for archival based on inactivity and age.

## Trigger logic

A course is selected for archival when **all** of the following are true:

- The course context is **not** already locked (not already archived)
- The course was created more than `creationdelay` ago (default: 24 months / 2 years)
- The most recent enrolled-user access is older than `lastaccessdelay` (default: 12 months / 1 year), **or** the course has never been accessed at all

## Installation

This plugin should be installed at `admin/tool/lccoursearchive`.

## Dependencies

- [tool_lifecycle](https://moodle.org/plugins/tool_lifecycle) — requires the refactored subplugins API (PR #293)

## Configuration

Settings are configured per workflow instance in the Lifecycle admin UI:

| Setting | Default | Description |
|---|---|---|
| Last access delay | 12 months | Minimum inactivity period before a course is eligible |
| Course creation delay | 24 months | Minimum course age before it is eligible |
