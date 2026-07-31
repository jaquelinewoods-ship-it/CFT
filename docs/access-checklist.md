# Access checklist (store secrets in a password manager)

Do **not** put real passwords into this file or commit them to git. Use this as a shared inventory of *what exists* and where the secrets live.

## Required

| System | URL / host | Username / notes | Secret location |
|---|---|---|---|
| SiteGround login | https://my.siteground.com | | Password manager |
| WordPress admin (live) | https://crossfit-trowbridge.com/wp-admin/ | | Password manager |
| WordPress admin (staging) | https://staging2.crossfit-trowbridge.com/wp-admin/ | | Password manager |
| Staging HTTP password | Browser prompt on staging URL | User e.g. `cftstaging` | Password manager |
| GoDaddy DNS | GoDaddy domain DNS for `crossfit-trowbridge.com` | | Password manager |
| GitHub repo | https://github.com/jaquelinewoods-ship-it/CFT | | GitHub org/user access |

## Optional / developer

| System | Detail | Secret location |
|---|---|---|
| SiteGround SSH | Host `ukm5.siteground.biz`, custom port (see Site Tools) | SSH private key on maintainer machine; public key in SiteGround SSH Keys Manager |
| WP-CLI | Available over SSH in site `public_html` | Uses SSH access |
| Database | Via SiteGround Site Tools → MySQL | Password manager if shared |

## When handing over

- [ ] New developer has SiteGround access (or a collaborator seat)
- [ ] New developer has WP admin on staging + live
- [ ] Staging HTTP password rotated if previous maintainers should lose access
- [ ] GoDaddy DNS access confirmed (or process for requesting DNS changes)
- [ ] SSH key added for new developer; old keys removed if needed
- [ ] This GitHub repo access granted
- [ ] They have read `HANDOFF.md` and completed one staging → review → push dry run
