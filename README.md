בטח — רק ה-README, מוכן להעתקה ל־`README.md`.

# Ads Tracker

A lightweight advertising tracking and attribution system built with **PHP, MySQL, JavaScript, and Chart.js**.

The system collects landing-page sessions and advertising events, preserves campaign attribution data, and provides reporting tools for analyzing traffic quality, user interactions, and conversion-related activity.

It was designed as a practical analytics solution for tracking advertising performance without relying entirely on third-party dashboards.

---

## Overview

Ads Tracker provides an end-to-end flow for collecting and analyzing advertising events:

```text
Ad / Traffic Source
        |
        v
   Landing Page
        |
        +----> Session Tracking
        |
        +----> Brand / CTA Click Tracking
        |
        v
 Event Collector
      (PHP)
        |
        v
      MySQL
        |
        +----> Reports
        +----> CTR Analysis
        +----> Data Export
```

The system preserves advertising metadata such as click identifiers, campaign IDs, ad-group IDs, keywords, traffic source, device, page URL, event information, brand, and revenue.

---

## Key Features

* Session tracking for landing-page visits
* Event tracking for clicks and conversion-related events
* Advertising attribution using identifiers such as `gclid`
* Campaign and ad-group metadata collection
* Keyword, traffic-source, and device tracking
* Dynamic PHP event ingestion
* MySQL-based event storage
* Prepared statements for database inserts
* Landing-page CTR reporting
* Brand click-share analysis
* 31-day reporting views
* Interactive charts powered by **Chart.js**
* Dark-mode reporting interface
* Data export endpoints
* Access-code protection for internal reports
* JavaScript tracking snippets for integration with external landing pages

---

## Technology Stack

| Layer           | Technology            |
| --------------- | --------------------- |
| Backend         | PHP                   |
| Database        | MySQL / MariaDB       |
| Client Tracking | Vanilla JavaScript    |
| Reporting UI    | HTML, CSS, JavaScript |
| Charts          | Chart.js              |
| Event Transport | HTTP                  |

---

## Data Model

The application separates incoming traffic sessions from tracked user events.

### Sessions

Session records contain advertising and landing-page information such as:

* Google Click ID (`gclid`)
* Campaign ID
* Ad Group ID
* Keyword
* Device
* Traffic Source
* Page URL
* Timestamp

### Events

Tracked events can contain:

* Event source
* Event type
* Event name
* Event ID
* Advertising identifiers
* Brand
* Revenue
* Campaign metadata
* Page URL
* Device
* Timestamp

This structure makes it possible to correlate a visitor's original advertising session with later interactions.

---

## Tracking Flow

### 1. Traffic arrives

A visitor reaches a landing page from an advertising source.

```text
Traffic Source
     |
     v
Landing Page
     |
     v
Session Tracking
```

Available advertising parameters are captured and stored.

### 2. Session is recorded

Client-side JavaScript sends session information to the PHP tracking endpoint.

The backend validates the incoming fields and stores the event in MySQL.

### 3. User interactions are tracked

Important landing-page interactions can be recorded independently.

```text
Session
  |
  +----> Brand Click
  |
  +----> CTA Click
  |
  `----> Conversion Event
```

### 4. Attribution

Session and event data can be correlated using advertising identifiers such as `gclid`.

This enables reporting metrics such as:

```text
Landing Page CTR = Tracked Clicks / Sessions × 100
```

---

## Reporting

The project includes browser-based analytics reports built directly on the collected tracking data.

### Landing Page CTR

The CTR report compares landing-page sessions with tracked click events.

It provides:

* Daily sessions
* Daily clicks
* Landing-page CTR
* 31-day trends
* Interactive charts

### Click Share Analysis

Additional reporting can analyze brand-click activity and calculate the percentage of tracked clicks associated with a specific brand or segment.

### Visualization

Reports use **Chart.js** to visualize performance over time.

The reporting interface also includes persistent dark-mode support for easier monitoring.

---

## Project Structure

```text
ads-tracker/
|
|-- db.sql
|-- env.php
|-- README.md
|
|-- js code for site/
|   |-- session.html
|   |-- brand_click.html
|   `-- link_replacement.html
|
`-- public/
    |
    |-- pc/
    |   |-- index.php
    |   |-- export/
    |   |-- table2/
    |   `-- up/
    |
    |-- se/
    |   |-- index.php
    |   |-- export/
    |   `-- table2/
    |
    `-- report/
        |-- lpctr.php
        `-- ics.php
```

---

## Installation

### Requirements

* PHP
* PHP `mysqli` extension
* MySQL or MariaDB
* Apache, Nginx, or another PHP-compatible web server

### Clone

```bash
git clone https://github.com/allimist/ads-tracker.git
cd ads-tracker
```

### Database

Create a database and import the included schema:

```bash
mysql -u your_user -p your_database < db.sql
```

### Configuration

Configure the database connection in `env.php`:

```php
<?php

$host = 'localhost';
$user = 'your_user';
$pass = 'your_password';
$dbname = 'your_database';
```

For production environments, credentials should be stored using environment variables or a secrets-management solution.

---

## Website Integration

Client-side tracking examples are available under:

```text
js code for site/
```

These scripts demonstrate how tracking can be integrated into landing pages to capture sessions and user interactions.

The tracking flow is intentionally lightweight, allowing it to be integrated into existing websites without requiring a frontend framework.

---

## Example Event Pipeline

```text
Google Ads / Traffic Source
             |
             v
      Landing Page Visit
             |
             v
        Session Event
             |
       +-----+-----+
       |           |
       v           v
 Brand Click    Other Event
       |           |
       +-----+-----+
             |
             v
           MySQL
             |
             v
      Analytics Reports
```

---

## Engineering Focus

The project demonstrates several common backend and advertising-technology engineering concepts:

* Event ingestion
* Advertising attribution
* Client-side tracking
* Server-side event processing
* Dynamic data handling
* Prepared SQL statements
* Relational analytics queries
* Reporting dashboards
* Data visualization
* Data exports
* Integration between browser-side and backend tracking components

The implementation intentionally stays lightweight and focuses on the tracking and analytics pipeline rather than framework overhead.

---

## Future Improvements

Potential improvements include:

* REST/JSON ingestion API
* API authentication
* Environment-based configuration
* Automated tests
* Docker development environment
* Database migrations
* Background event processing
* Additional advertising-network integrations
* Aggregated reporting tables for high-volume datasets
* More advanced attribution models

---

## Author

Senior Backend / Full-Stack Software Engineer focused on backend systems, advertising technology, automation, analytics, and cloud infrastructure.

זה הנוסח שהייתי שם כרגע ב-GitHub.
