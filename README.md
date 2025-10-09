 Ads Tracker

A full-featured ads tracking and attribution system — tracks sessions, clicks, signups, and sales across multiple traffic sources. Supports partner postbacks, conversion uploads to Google Ads, Bing, and other ad networks for accurate campaign optimization.

---

## Table of Contents


---

## Features

- Track **sessions**, **clicks**, **signups**, and **sales / conversions**
- Support **partner postbacks** (notify external partners of events)
- Upload conversions to ad networks (Google Ads, Bing, etc.)
- Attribution logic linking clicks → sessions → conversions
- Validation and deduplication of events
- Reporting-ready data schema
- Extensible for new ad networks and partners

---

## Architecture & Data Model

[User / Traffic] → Click → Session → Conversion (signup / sale)
↘ ↘
→ Attribution → Postback / Upload

- **Click:** Records user click on an ad with parameters.  
- **Session:** Connects user visits and page interactions.  
- **Conversion:** Records signup or sale event.  
- **Attribution:** Determines credited click/source.  
- **Postback / Upload:** Sends conversion data to networks or partners.  

---

## Getting Started

### Prerequisites


### Installation

```bash
git clone https://github.com/allimist/ads-tracker.git

update env.php

// DB config
$host = 'localhost';
$user = 'user';
$pass = 'pass';
$dbname = 'dbname';

add code from folder 'js code for site' to your website
