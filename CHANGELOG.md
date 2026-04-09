# Changelog

## [1.2.1] - 2026-04-09

### Changed
- **Social presence snapshot** — Course snapshots now use the same group-aware composite methodology as the grade report: forum breadth (60%) + volume (40%), respecting separate group modes.
- Configurable teaching roles setting for institutions with custom roles.
- Course category filter to exclude development/test courses from reports.
- Cohort filter respects course pattern when a specific cohort is selected (admins no longer bypass filtering).
- Debug panel available on lecturer profile via `?debug=1` URL parameter.

## [1.2.0] - 2026-04-02

### Added
- **Programme cohort mapping widget** — Custom admin setting that combines cohort selection and course shortname regex patterns in a single table UI. Replaces the separate cohort checkbox and textarea settings.
- **Course pattern scoping** — Lecturers and students are now scoped to programme coordinators via course shortname regex patterns rather than cohort co-membership. A pattern like `^THE` matches all Theology courses, determining which lecturers and students a PC can see.
- **CSV export** — Programme coordinators can export a time commitment report (per lecturer per course) for a selected date range. Accessible from the lecturer list page.
- **Lecturer time report API endpoint** — `local_coifish_get_lecturer_time_report` web service function for SIS integration, returning per-lecturer per-course time breakdowns for a date range.
- **Course filter on lecturer profile** — Dropdown to filter the lecturer profile to a specific course, scoped to the PC's programme pattern.
- **Date range filter on lecturer profile** — From/to date pickers with clear button for time-scoped analysis.

### Changed
- Capabilities `viewfullprofile` and `viewlecturerprofile` changed from `CONTEXT_COURSECAT` to `CONTEXT_SYSTEM` so they work with system-level role assignments.
- Admin report links moved outside `$hassiteconfig` block so non-admin coordinators with `moodle/site:configview` can see them under Site Administration > Reports.
- Navigation hook now adds a top-level "CoIFish Longitudinal Profile" node for non-admin coordinators.
- Lecturer course dropdown filtered by PC's programme pattern to prevent cross-programme course visibility.
- `estimate_activity_hours` method made public for use by export and API.

### Fixed
- Programme coordinators with custom roles could not see report links (required `moodle/site:config` instead of `moodle/site:configview`).
- Cohort scoping returned all lecturers when PC was not in any included cohort (now returns empty).
- Admin check changed from `has_capability('moodle/cohort:manage')` to `is_siteadmin()` for consistent behaviour.
- Missing capability lang string `coifish:viewlecturerprofile`.

## [1.1.0] - 2026-04-02

### Added
- **Student risk overview** — Institution-wide dashboard of at-risk students with filtering by course category or site cohort.
- **Student drill-down** — Full longitudinal profile with course history, risk indicators, and prescriptive recommendations.
- **Lecturer performance profiles** — Aggregated cross-course analytics for each lecturer with feedback quality, grading turnaround, intervention effectiveness, forum engagement, and student outcome metrics.
- **Lecturer activity time estimation** — Session gap analysis on LMS event logs estimating hours spent on marking, student communication, and live sessions, with configurable preparation multiplier.
- **KPI detail modals** — "How is this determined?" drill-down for each key performance indicator with data sources, benchmarks, and research citations.
- **Strengths and focus areas** — Automated identification of top 3 strengths and bottom 3 focus areas with constructive recommendations.
- **Cohort-based organisation mode** — Alternative to category filtering; admins select which site cohorts to include in reports.
- **Course-level longitudinal toggle** — Enable or disable longitudinal data per course in CoIFish report settings.
- **Early warning integration** — CoIFish grade report displays at-risk students on the cohort insights tab and longitudinal profiles on student insights.
- **Prescriptive recommendations** — Risk-factor-based guidance for teachers: engagement decline, social isolation, feedback neglect, intervention unresponsiveness, grade decline, late starter, irregular patterns.
- **External API** — Web service endpoints for SIS integration: student profile, early warnings, course history.
- **Navigation hooks** — Risk overview and lecturer profiles accessible via Site Administration > Reports.
- **PHPUnit tests** — 15 tests covering API, lecturer API, and filter helper across PostgreSQL and MariaDB.
- **CI workflow** — GitHub Actions with PHPCS, phplint, phpdoc, validate, savepoints, mustache, grunt, and PHPUnit.

## [1.0.0] - 2026-04-01

### Added
- Initial release.
- Longitudinal student profiles built from historical course data via daily scheduled task (3:00 AM).
- Per-student course snapshots capturing grade, engagement, social presence, self-regulation, and feedback review metrics.
- Risk classification (low, moderate, high) based on accumulated risk factors.
- Engagement pattern detection (consistent, declining, growing, irregular).
- Intervention response tracking (positive, mixed, unresponsive) from CoIFish intervention data.
- Privacy-controlled API with three detail levels (patterns, summary, full).
- Integration with gradereport_coifish for in-course longitudinal profile display.
