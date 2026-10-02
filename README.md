<div align="center">

# Secure Lead Intake

**Reusable lead ingestion with defensive controls built in.**

`PHP` · `JavaScript` · `REST` · `Validation` · `Rate Limiting`

</div>

---

A reusable full-stack lead capture module for marketing landing pages and CMS-based websites.

The goal is simple: accept user input, validate it server-side, reduce abuse, forward clean data to downstream systems and keep integrations isolated from the frontend.

## Security-oriented flow

```text
Browser
   ↓
Client-side submission
   ↓
Server-side validation
   ↓
Anti-spam / rate limiting
   ↓
CRM / marketing sync
   ↓
Internal notification
```

## Included controls

- asynchronous client submission
- server-side validation
- layered anti-spam protection
- rate limiting
- configurable campaign/source handling
- marketing-platform synchronization
- internal email notifications

## Structure

```text
backend/
  config/
  controllers/
  services/
  utils/
  plugin.php

frontend/
  lead-form.js

examples/
```

## Why this repo matters

This project sits at the intersection of application engineering and security: input handling, trust boundaries, third-party integrations and abuse prevention.

It reflects the kind of application-layer work that led me deeper into AppSec and DevSecOps.
