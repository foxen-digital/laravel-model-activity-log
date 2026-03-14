# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v1.0.0 - 2026-03-14

### What's New

Initial release of Laravel Model Activity Log.

#### Features

- Automatic logging of `created`, `updated`, `deleted`, and `restored` (SoftDeletes) events on Eloquent models
- Tracks the authenticated user as the causer; attributes unauthenticated actions to `System`
- Stores old/new attribute values for `updated` events
- Per-model attribute ignoring and redacting, with global redact config
- Polymorphic `subject()` and `causer()` relationships on the `Activity` model
- Convenient query scopes: `whereSubject`, `forSubjectType`, `whereCauser`, `forCauserType`, `forEvent`
- Automatic pruning of old log entries via `model:prune`
- Configurable table name, log name, and prune settings

## [Unreleased]
