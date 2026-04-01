# CoIFish Longitudinal Profile

A Moodle local plugin that builds cross-course student profiles and lecturer performance analytics, extending the [CoIFish grade report](https://github.com/mkpelletier/gradereport_coifish) with institutional-level insights.

## Features

### Student Risk Overview
- Institution-wide dashboard of at-risk students based on longitudinal data
- Risk classification (low, moderate, high) derived from historical course performance
- Risk factors: grade decline, engagement decline, social isolation, feedback neglect, intervention unresponsiveness
- Engagement pattern detection: consistent, declining, growing, irregular
- Filterable by programme (via cohort/course pattern) or course category
- Drill-down into individual student profiles with full course history and prescriptive recommendations

### Lecturer Performance Profiles
- Aggregated cross-course performance for each lecturer
- Key performance indicators: feedback quality, grading turnaround, intervention effectiveness, student outcomes
- Dimension breakdown with progress bars: coverage, depth, personalisation, turnaround, forum engagement
- Automated strengths (top 3) and focus areas (bottom 3) with constructive recommendations
- Activity time estimation: marking, student communication, and live sessions
- Course and date range filters for scoped analysis
- "How is this determined?" methodology modal with data sources, benchmarks, and research citations
- CSV export of time commitments per lecturer per course

### Early Warning Integration
- CoIFish grade report displays longitudinal profiles at the top of student insights
- Teachers see historical risk indicators and prescriptive recommendations from day one
- Cohort insights tab shows early warning table for at-risk students in the current course

### External API
- Web service endpoints for SIS integration
- `local_coifish_get_student_profile` — Single student longitudinal profile
- `local_coifish_get_early_warnings` — At-risk students in a course
- `local_coifish_get_course_history` — Student course-by-course snapshot history
- `local_coifish_get_lecturer_time_report` — Lecturer time commitments per course for a date range
- All endpoints return JSON via `moodlewsrestformat=json`

## Requirements

- Moodle 5.0+ (version 2024110400 or later)
- [CoIFish grade report](https://github.com/mkpelletier/gradereport_coifish) (gradereport_coifish)

## Installation

1. Copy the plugin folder to `local/coifish` in your Moodle installation.
2. Log in as admin and visit **Site administration > Notifications** to complete the install.
3. Configure settings at **Site administration > Plugins > Local plugins > CoIFish Longitudinal Profile**.
4. Run the scheduled task to build initial profiles, or wait for the daily 3:00 AM run.

## Configuration Guide

### Step 1: Choose an organisation mode

Go to **Site administration > Plugins > Local plugins > CoIFish Longitudinal Profile**.

Under **Organisation mode**, choose how reports are filtered:

- **Course categories** (default): Reports are filtered by Moodle's course category tree. Use this if your categories map to programmes or departments.
- **Site cohorts**: Reports are filtered by site-level cohorts with course shortname patterns. Use this if your categories are structured by term/semester rather than programme, or if you need to pair specific programme coordinators with specific lecturers.

### Step 2: Configure programme cohorts (cohort mode only)

If using cohort mode:

1. **Create cohorts** at **Site administration > Users > Cohorts**. Create one cohort per programme (e.g. "Theology Programme", "Biblical Studies Programme"). Add the programme coordinator as a member of their programme's cohort.

2. **Map cohorts to course patterns** in the plugin settings under **Programme cohorts and course patterns**. This widget shows a table of all system cohorts. For each programme:
   - Tick the checkbox to include the cohort
   - Enter a regex pattern matching that programme's course shortnames

   Examples:
   | Cohort | Pattern | Matches |
   |---|---|---|
   | Theology Programme | `^THE` | THE1120, THE2201, THE3305 |
   | Biblical Studies | `^(BIB\|BHB)` | BIB3129, BHB1121 |
   | Counselling | `^(BCC\|HCC\|CCC)` | BCC1101, HCC2201, CCC3301 |

   The pattern uses PHP regex syntax. Common patterns:
   - `^THE` — starts with "THE"
   - `^(BIB|BHB)` — starts with "BIB" or "BHB"
   - `^(BCC|HCC|CCC)` — starts with any of three prefixes
   - `THE` — contains "THE" anywhere (no `^` anchor)

### Step 3: Create a programme coordinator role

1. Go to **Site administration > Users > Permissions > Define roles** and create a custom role (e.g. "Programme Coordinator").

2. Set the role context to **System**.

3. Assign these capabilities:

   | Capability | Purpose |
   |---|---|
   | `moodle/site:configview` | See the Site Administration menu |
   | `local/coifish:viewfullprofile` | Access student risk overview and full profiles |
   | `local/coifish:viewlecturerprofile` | Access lecturer performance profiles |
   | `gradereport/coifish:view` | View the CoIFish grade report in courses |
   | `gradereport/coifish:viewcoordinator` | View the coordinator tab in CoIFish |
   | `gradereport/coifish:intervene` | Log interventions for students |
   | `local/coifish:viewprofile` | View longitudinal profiles within courses |

4. Assign the role to the programme coordinator at **Site administration > Users > Permissions > Assign system roles**.

5. Add the coordinator to their programme's cohort (Step 2 above).

### Step 4: Configure privacy level

Under **Privacy controls**, set the detail level for course teachers:

- **Patterns only**: Teachers see risk level, engagement pattern, and risk factors. No grades from other courses.
- **Summary**: Above plus trend directions and intervention response classification.
- **Full**: All metrics including average grades, self-regulation scores, and course history.

Programme coordinators with `local/coifish:viewfullprofile` always see full detail regardless of this setting.

### Step 5: Configure live session preparation multiplier

Under **Live session preparation multiplier**, set the estimated preparation time per hour of live session delivery:

- **No preparation time**: Delivery hours only
- **1:1**: 1 hour prep per 1 hour delivery
- **2:1** (recommended): 2 hours prep per 1 hour delivery
- **3:1**: 3 hours prep per 1 hour delivery

### Step 6: Enable the external API (optional)

If your SIS needs to query student profiles or lecturer time data:

1. Enable **External API** in the plugin settings.
2. Enable web services at **Site administration > Server > Web services > Overview**.
3. Enable the REST protocol at **Site administration > Server > Web services > Manage protocols**.
4. Create a user with the `local/coifish:apiaccess` capability.
5. Create a token for that user linked to the "CoIFish Longitudinal Profile API" service.

API endpoints:

```
# Student profile
GET /webservice/rest/server.php?wstoken=TOKEN&wsfunction=local_coifish_get_student_profile&userid=123&moodlewsrestformat=json

# Early warnings for a course
GET /webservice/rest/server.php?wstoken=TOKEN&wsfunction=local_coifish_get_early_warnings&courseid=45&moodlewsrestformat=json

# Student course history
GET /webservice/rest/server.php?wstoken=TOKEN&wsfunction=local_coifish_get_course_history&userid=123&moodlewsrestformat=json

# Lecturer time report (all lecturers, date range as Unix timestamps)
GET /webservice/rest/server.php?wstoken=TOKEN&wsfunction=local_coifish_get_lecturer_time_report&userid=0&timefrom=1709251200&timeto=1743465600&moodlewsrestformat=json

# Lecturer time report (specific lecturer)
GET /webservice/rest/server.php?wstoken=TOKEN&wsfunction=local_coifish_get_lecturer_time_report&userid=214&timefrom=1709251200&timeto=1743465600&moodlewsrestformat=json
```

### Step 7: Run the scheduled task

Profiles are built daily at 3:00 AM by the "Build longitudinal student profiles" scheduled task. To run it manually:

```bash
php admin/cli/scheduled_task.php --execute='\local_coifish\task\build_profiles'
```

This task:
1. Creates course snapshots for completed courses (students' final metrics)
2. Aggregates snapshots into longitudinal student profiles with risk classification
3. Builds lecturer performance profiles from feedback, grading, intervention, and activity data

## How Scoping Works

### Cohort mode

The programme coordinator (PC) sees only the lecturers and students that belong to their programme:

- **Lecturers**: Users with a teacher/editingteacher role in courses whose shortname matches the PC's programme pattern.
- **Students**: Users enrolled in courses whose shortname matches the PC's programme pattern.
- **Cohort**: Identifies the PC. The PC is a member of the cohort; lecturers and students are NOT members. The cohort simply pairs the coordinator with a course pattern.

This means a lecturer teaching across multiple programmes appears in each relevant PC's view, but each PC only sees courses matching their own programme pattern.

### Category mode

Reports are filtered by the Moodle course category tree. All lecturers and students in courses within the selected category (and its children) are visible.

## Time Estimation Methodology

The plugin estimates lecturer activity time using **session gap analysis** on LMS event logs.

### How sessions are detected

Events from the Moodle logstore are ordered chronologically per user and grouped into sessions:

1. Events within **30 minutes** of each other are considered part of the same session
2. A gap exceeding 30 minutes starts a new session
3. Each session's duration = last event timestamp minus first event timestamp
4. Single events (no subsequent event within the gap) count as **1 minute minimum**

### Activity categories

**Marking and feedback** includes:
- Assignment grading events (`submission_graded`)
- Quiz grading events (`attempt_graded`, `question_manually_graded`)
- Grading interface views (grader report, grading table, grading form)
- Unified Grader annotations, comments, and notes (if installed)
- Feedback comment creation

**Student communication** includes:
- Moodle core messages sent
- SATS Mail messages sent (if installed)
- Forum posts and discussion creation by the teacher
- Other messaging plugin events detected via logstore

**Live sessions** includes:
- BigBlueButton recording durations (preferred, from `bigbluebuttonbn_recordings`)
- BBB log-based session estimation (fallback if no recordings)
- Configurable preparation multiplier applied on top of delivery time

### Preparation multiplier

Research on online teaching workload suggests significant preparation time for synchronous sessions. Worley & Tesdell (2009) found that online instruction required substantially more time than face-to-face delivery, with preparation being the largest component. The commonly reported ratio is **2:1 to 3:1** (preparation to delivery):

- Worley, W. L., & Tesdell, L. S. (2009). Instructor time and effort in online and face-to-face teaching: Lessons learned. *IEEE Transactions on Professional Communication*, 52(2), 138-151.

### Limitations

- Time estimates approximate LMS-based activity only. Offline preparation, reading, and research are not captured.
- The 30-minute session gap threshold is a balance between over-counting idle time and under-counting focused work with natural pauses.
- BigBlueButton session duration relies on recording metadata or log events. If neither is available, live session time may be underestimated.
- Forum reading time is not directly measurable; only post creation events are counted.

## Capabilities

| Capability | Description | Default roles |
|---|---|---|
| `local/coifish:viewprofile` | View student longitudinal profiles within a course | Teacher, Editing teacher, Manager |
| `local/coifish:viewfullprofile` | View full profiles and risk overview at system level | Manager |
| `local/coifish:viewlecturerprofile` | View lecturer performance profiles | Manager |
| `local/coifish:apiaccess` | Access external API endpoints | None (assign manually) |

## Research Foundations

- Anderson, T., Rourke, L., Garrison, D. R., & Archer, W. (2001). Assessing teaching presence in a computer conferencing context. *Journal of Asynchronous Learning Networks*, 5(2), 1-17.
- Boud, D., & Molloy, E. (2013). Rethinking models of feedback for learning. *Assessment & Evaluation in Higher Education*, 38(6), 698-712.
- Clow, D. (2012). The learning analytics cycle: Closing the loop effectively. *Proceedings of the 2nd International Conference on Learning Analytics and Knowledge*, 134-138.
- Garrison, D. R., Anderson, T., & Archer, W. (2000). Critical inquiry in a text-based environment. *The Internet and Higher Education*, 2(2-3), 87-105.
- Gibbs, G., & Simpson, C. (2004). Conditions under which assessment supports student learning. *Learning and Teaching in Higher Education*, 1(1), 3-31.
- Hattie, J., & Timperley, H. (2007). The power of feedback. *Review of Educational Research*, 77(1), 81-112.
- Nicol, D. J., & Macfarlane-Dick, D. (2006). Formative assessment and self-regulated learning. *Studies in Higher Education*, 31(2), 199-218.
- Wise, A. F. (2014). Designing pedagogical interventions to support student use of learning analytics. *Proceedings of LAK 2014*, 203-211.
- Worley, W. L., & Tesdell, L. S. (2009). Instructor time and effort in online and face-to-face teaching. *IEEE Transactions on Professional Communication*, 52(2), 138-151.

## License

This plugin is licensed under the [GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html).

## Copyright

2026 [South African Theological Seminary](https://www.sats.ac.za)
