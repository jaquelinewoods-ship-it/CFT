# CrossFit Trowbridge — developer handoff

This document is for anyone taking over site changes. Keep secrets (passwords, SSH keys, API tokens) in a password manager, not in this repo.

## Stack

| Item | Detail |
|---|---|
| CMS | WordPress |
| Theme | Avada (parent: `cfbath`) + child theme `cfbath-Child-Theme` |
| Builder | Fusion Builder / Fusion Core (required — do not deactivate) |
| Hosting | SiteGround |
| Live domain | `crossfit-trowbridge.com` |
| Staging domain | `staging2.crossfit-trowbridge.com` |
| DNS | GoDaddy (`ns31/ns32.domaincontrol.com`) — not SiteGround nameservers |
| Custom content dir | `cfbathmedia/` (replaces default `wp-content/`) |
| Live web root | `~/www/crossfit-trowbridge.com/public_html` |
| Staging web root | `~/www/staging2.crossfit-trowbridge.com/public_html` |

## Environments

### Live

- Public site used by members and the public
- Treat as read-only for day-to-day work
- Only update via **Push to Live** from staging (or an agreed emergency hotfix)

### Staging

- Full copy of the site for development and stakeholder review
- Password protected (HTTP Basic Auth)
- Search engines discouraged (`blog_public = 0`, `robots.txt` disallow, `noindex` headers/meta)
- Share the staging URL + HTTP credentials with reviewers only

## Standard workflow

1. Confirm you are on **staging** (orange Staging label in WP admin, or staging hostname in the URL).
2. Make content / theme / plugin changes on staging.
3. If changing child-theme code:
   - Edit locally in this repo under `themes/cfbath-Child-Theme/`
   - Deploy those files to **staging** first (SFTP/SSH/rsync)
   - Do not deploy straight to live
4. Review on staging (desktop + mobile).
5. In SiteGround Site Tools → **WordPress → Staging**, use **Push to Live** when approved.
6. Spot-check live after push.
7. Commit any child-theme changes back to this git repo so the next person has them.

## Creating or refreshing staging

SiteGround path: **Site Tools → WordPress → Staging → Create**.

Because DNS is on GoDaddy, after SiteGround creates `stagingN.crossfit-trowbridge.com` you must add DNS A records:

| Type | Name | Value |
|---|---|---|
| A | `stagingN` | SiteGround site IP (currently used by live apex) |
| A | `www.stagingN` | same IP |

Then install SSL in Site Tools → **Security → SSL Manager** for the staging hostname (Let’s Encrypt).

### Staging post-create checklist (important)

SiteGround rewrites `siteurl` / `home`, but this site uses a custom content directory. After creating staging, verify:

1. In staging `wp-config.php`, `WP_CONTENT_URL` points at the **staging** domain, e.g.  
   `https://staging2.crossfit-trowbridge.com/cfbathmedia`  
   not the live domain.
2. Homepage CSS/JS/fonts load from the staging host (not live). If icons show as empty boxes (□), `WP_CONTENT_URL` is almost certainly still pointing at live.
3. Staging is password protected.
4. Search engines are discouraged (`Settings → Reading`, plus `robots.txt` / noindex).
5. SSL is valid (no browser certificate warning).

## What to change where

| Change type | Where |
|---|---|
| Pages, menus, copy, media, most plugin settings | Staging WP admin |
| Custom CSS / child-theme PHP | This repo → deploy to staging → push live |
| Parent Avada theme files | Avoid — use child theme |
| DNS | GoDaddy |
| Hosting / staging / SSL | SiteGround Site Tools |

## Critical don’ts

- Do **not** deactivate **Fusion Core** / Fusion Builder — Avada shortcodes will render as raw text.
- Do **not** edit the parent theme `cfbath` for custom work — use `cfbath-Child-Theme`.
- Do **not** commit passwords, `.htpasswd`, DB dumps, or full site tarballs to git.
- Do **not** push staging → live until someone has reviewed staging.
- Be careful with search-replace across databases — always confirm you are on the staging database/path.

## Child theme (this repo)

Tracked files:

- `themes/cfbath-Child-Theme/style.css` — custom styles
- `themes/cfbath-Child-Theme/functions.php` — child theme functions
- `themes/cfbath-Child-Theme/footer.php` — footer override
- `themes/cfbath-Child-Theme/screenshot.png`

Server path:

```text
cfbathmedia/themes/cfbath-Child-Theme/
```

Deploy example (staging):

```bash
rsync -avz -e 'ssh -p 18765 -i ~/.ssh/id_ed25519 -o IdentitiesOnly=yes' \
  themes/cfbath-Child-Theme/ \
  USER@HOST:~/www/staging2.crossfit-trowbridge.com/public_html/cfbathmedia/themes/cfbath-Child-Theme/
```

Then hard-refresh / purge LiteSpeed cache on staging before review.

## Known site notes

- Brand Instagram: used for gym updates; feed embed may be desired later (not necessarily installed).
- Twitter/X social link was intentionally cleared (unused account).
- Homepage hero / contact / coaches / top-bar styles have been customized in the child theme.
- Site uses LiteSpeed cache — purge after CSS/content deploys.

## Access inventory

Use `docs/access-checklist.md` and fill real credentials into a password manager shared with the business owner / next developer.

Minimum access needed to maintain the site:

1. SiteGround account (Site Tools + staging)
2. WordPress admin (live + staging)
3. GoDaddy DNS
4. SSH key authorised on SiteGround (optional but useful)
5. This GitHub repo

## Emergency live hotfix

If staging is unavailable and a critical live fix is required:

1. Take a backup / note exact files changed.
2. Prefer changing only the child theme or a single WP option.
3. Mirror the same change into this git repo and onto staging as soon as possible so environments do not drift.
