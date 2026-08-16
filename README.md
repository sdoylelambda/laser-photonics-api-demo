# Laser Photonics — WordPress/Elementor Demo

A quick homepage rebuild + custom functionality demo, built to show fast ramp-up on WordPress and Elementor for a potential freelance/employment opportunity.

## What's in this repo

- **`docker-compose.yml`** — spins up a local WordPress + MySQL environment in Docker, no manual server setup needed.
- **`functions.php`** — registers a custom "Product" post type, giving WordPress-native CRUD (Create/Read/Update/Delete) for product data directly through the WP Admin dashboard — no custom admin UI needed.
- **`page-crud-demo.php`** — a custom WordPress page template that queries and displays Products using `WP_Query`, WordPress's native content-fetching system.

## What this demonstrates

- Recreating a real business homepage visually in **Elementor** (page builder)
- Extending WordPress with **custom post types** for structured data (the WordPress-native alternative to a raw custom database table)
- Writing custom **PHP templates** that pull and render dynamic content
- Comfort with **Docker** for local WordPress development
- Prior experience with HTML, CSS, JavaScript, and API integrations, applied to a new (to me) platform

## Running it locally

\`\`\`bash
docker compose up -d
\`\`\`

Then visit `http://localhost:8080` and complete the WordPress install screen.

## Tech stack

WordPress · Elementor · PHP · MySQL · Docker