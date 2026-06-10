## [1.0.0] - 2026-06-10
- Polymorphic asset register (equipment, domains, software, subscriptions), scoped per owner via owner-access
- Auto-generated per-owner asset references, unique per owner
- Status lifecycle (in use, assigned, in stock, maintenance, expiring, expired, retired, disposed)
- Renewal and expiry tracking with an expiringWithin scope, plus renew and retire actions
- Category-specific fields in a details JSON column
- Attach receipts and documents to assets through the files package
