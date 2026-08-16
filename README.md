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

## Clean stale cache (full wipe and reload)
docker exec -it laserphotonics_wp rm -rf /var/www/html/wp-content/uploads/elementor/css/*
docker restart laserphotonics_wp

## Full setup from scratch (if this laptop is wiped, or setting up fresh)

\`\`\`bash
# 1. Install Docker (if not already installed)
sudo apt install docker.io
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker $USER
# then fully log out and back in (group change won't apply otherwise)

# 2. Install Docker Compose plugin
sudo apt install docker-compose-v2

# 3. From the project folder (containing docker-compose.yml):
docker compose up -d

# 4. Visit http://localhost:8080 and complete the WordPress install wizard
#    (set your own admin username/password)

# 5. Re-apply the custom code:
#    - Install Elementor plugin (WP Admin > Plugins > Add New > search "Elementor")
#    - Copy functions.php and page-crud-demo.php into:
#      wp-content/themes/hello-elementor/
#    - In WP Admin > Pages, create a page, set Template to "CRUD Demo", publish
#    - In WP Admin > Settings > Reading, set homepage to your Elementor homepage
\`\`\`

---

## Common issues & fixes

### Site redirects in a loop / shows `301 Moved Permanently`
Usually means `siteurl`/`home` in the database doesn't match how you're accessing the site.

\`\`\`bash
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e \\
"UPDATE wp_options SET option_value = 'http://localhost:8080' WHERE option_name IN ('siteurl','home');"
\`\`\`

Note: `wp-config.php` has been patched to auto-detect the current domain on each request, so this should rarely be needed — but if it ever reverts, this is the fix.

### Site shows old/missing content ("my work disappeared")
Almost always a caching issue, not actual data loss. Before panicking:
\`\`\`bash
# Confirm the data is actually still there:
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e \\
"SELECT LENGTH(meta_value) FROM wp_postmeta WHERE post_id=6 AND meta_key='_elementor_data';"
# A large number = your content is safe, just not rendering. Then:
\`\`\`
1. Clear Elementor's cache: WP Admin → Elementor → **Elementor Cache** → Clear
   (or manually: `docker exec -it laserphotonics_wp rm -rf /var/www/html/wp-content/uploads/elementor/css/* && docker restart laserphotonics_wp`)
2. Hard refresh (Ctrl+Shift+R) or try a fresh Incognito window
3. Check WP Admin → Settings → Reading — "Your homepage displays" should be "A static page" with the correct page selected

### Images not loading / "mixed content" errors in browser console
Image URLs got hardcoded to an old domain (e.g. an old ngrok link) inside Elementor's saved data. Fix by replacing the old domain with the current one across all of a page's postmeta (covers both escaped `\/\/` and plain `//` URL formats):
\`\`\`bash
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e \\
"UPDATE wp_postmeta SET meta_value = REPLACE(REPLACE(REPLACE(REPLACE(meta_value, \\
'http:\\\\\\\\/\\\\\\\/OLD_DOMAIN', 'http:\\\\\\\\/\\\\\\\/NEW_DOMAIN'), \\
'http://OLD_DOMAIN', 'http://NEW_DOMAIN'), \\
'https:\\\\\\\\/\\\\\\\/OLD_DOMAIN', 'https:\\\\\\\\/\\\\\\\/NEW_DOMAIN'), \\
'https://OLD_DOMAIN', 'https://NEW_DOMAIN') WHERE post_id=6;"
\`\`\`
Then clear the Elementor cache (see above).### Images not loading / "mixed content" errors in browser console
Image URLs got hardcoded to an old domain (e.g. an old ngrok link) inside Elementor's saved data. Elementor stores URLs with escaped slashes (`\/\/`) in its JSON, so you need to fix BOTH the escaped and plain versions, and both post_content and postmeta. Run these one at a time, replacing OLD_DOMAIN and NEW_DOMAIN with the actual domains:

\`\`\`bash
# Fix escaped URLs (inside Elementor's JSON data)
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e "UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'http:\\\\/\\\\/OLD_DOMAIN', 'https:\\\\/\\\\/NEW_DOMAIN') WHERE post_id=6;"

# Fix plain (non-escaped) URLs, in case any exist
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e "UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://OLD_DOMAIN', 'https://NEW_DOMAIN') WHERE ID=6;"
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e "UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'http://OLD_DOMAIN', 'https://NEW_DOMAIN') WHERE post_id=6;"

# Also fix the image attachment records themselves
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e "UPDATE wp_posts SET guid = REPLACE(guid, 'http://OLD_DOMAIN', 'https://NEW_DOMAIN') WHERE post_type='attachment';"
\`\`\`
Then clear the Elementor cache (see above) and hard refresh.

### Global Colors / text color changes not showing on the live site
Elementor's Global Colors + CSS cache can get out of sync. If it won't reliably update:
- Elementor → **Elementor Cache** → Clear, then hard refresh
- If it's still unreliable, there's a hard override already in `functions.php` that forces white text site-wide via injected CSS (`force_white_text_css`) — bypasses Elementor's color system entirely. Edit the hex value there if a different color is needed.

### "Products" menu / custom post type not appearing after adding code
\`\`\`bash
# Confirm the code actually saved to the file:
docker exec -it laserphotonics_wp grep -c 'register_product_post_type' \\
/var/www/html/wp-content/themes/hello-elementor/functions.php
# Should return 2. If 0, the file wasn't actually saved/copied correctly.
\`\`\`
Also try going directly to `http://localhost:8080/wp-admin/edit.php?post_type=product` — the menu item can be easy to miss in the sidebar even when it's working.

### Custom Field (e.g. `price`) not saving on a Product
Field names are case-sensitive — must be typed in **lowercase** (`price`, not `Price`). If a field was accidentally saved with the wrong case:
\`\`\`bash
docker exec -it laserphotonics_db mysql -u wpuser -pwppassword wordpress -e \\
"UPDATE wp_postmeta SET meta_key = 'price' WHERE meta_key = 'Price';"
\`\`\`
If the Custom Fields panel isn't visible at all in the editor: click the **⋮ (three-dot) menu** top-right → **Preferences** → **Panels** tab → toggle **Custom Fields** on.

### `docker: command not found`
\`\`\`bash
sudo apt install docker.io
sudo systemctl start docker
sudo usermod -aG docker $USER
# log out and back in
\`\`\`

### `git: command not found` (inside the WordPress container)
\`\`\`bash
docker exec -it laserphotonics_wp bash -c "apt-get update && apt-get install -y git"
\`\`\`

### Git push fails with a password/authentication error
GitHub requires a **Personal Access Token**, not your account password, for git operations.
1. GitHub.com → profile picture → Settings → Developer settings → Personal access tokens → Tokens (classic) → Generate new token
2. Check the `repo` scope → Generate → copy the token immediately (shown once)
3. Use it in place of your password when git prompts you
4. To stop re-entering it every push: `git config --global credential.helper store`

### VS Code says Docker version is too old / can't detect Docker (Dev Containers extension)
Usually a permissions issue, not a real version problem. Launch VS Code from a terminal instead of the app launcher, so it inherits your terminal's Docker group permissions:
\`\`\`bash
code .
\`\`\`
Then retry: Ctrl+Shift+P → "Dev Containers: Attach to Running Container"

### `Author identity unknown` on git commit
\`\`\`bash
git config --global user.email "your@email.com"
git config --global user.name "Your Name"
\`\`\`

### Screen locks / laptop won't stay awake while sharing an ngrok link
Screen lock alone doesn't kill Docker or ngrok — they run in the background regardless. Only actual **sleep/suspend** kills the tunnel. If it's happening anyway, check Settings → Screen Lock and set it to Never for the duration of the demo.