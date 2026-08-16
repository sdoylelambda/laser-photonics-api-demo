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

WordPress · Elementor · PHP · MySQL · Docker · HTML

---

## Demoing this project (quick reference)

### Local demo (on this laptop) — no internet needed
Everything runs inside Docker. As long as the containers are running, `http://localhost:8080` works completely offline — switching networks, hotspots, or losing WiFi has no effect on the local demo.

Check containers are up:
\`\`\`bash
docker ps
\`\`\`
Both `laserphotonics_wp` and `laserphotonics_db` should show `Up`. If not:
\`\`\`bash
docker compose up -d
\`\`\`
(run from the project folder containing `docker-compose.yml`)

### Sharing a public link (requires internet + ngrok)
Only needed if someone else needs to view it remotely, not for the local demo.

\`\`\`bash
ngrok http 8080
\`\`\`
Copy the `Forwarding` URL it prints (a new one is generated every time you restart ngrok on the free tier).

**Notes:**
- `wp-config.php` has been patched to auto-detect the domain (`WP_HOME`/`WP_SITEURL` are set dynamically per-request), so `siteurl`/`home` do **not** need manual updating just to load pages.
- Product images stored in `_elementor_data` and related postmeta are saved as **relative URLs**, so they load correctly regardless of domain (localhost or ngrok) — no manual URL-fixing needed unless something is later re-saved with an absolute path baked in.
- The ngrok tunnel only stays alive while that terminal window is open and the laptop is awake (not asleep — screen lock is fine, sleep/suspend is not).

### If something looks broken
1. `docker ps` — confirm both containers are `Up`.
2. `curl -I http://localhost:8080` — should return `200 OK`, not a `301` redirect loop.
3. Check WP Admin → Settings → Reading — confirm "Your homepage displays" is set to the correct static page.
4. Hard refresh / try an incognito window before assuming content is missing — most "my work disappeared" scares turned out to be stale cache, not lost data.
