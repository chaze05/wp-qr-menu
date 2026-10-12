# WP QR Menu

Turn any WordPress site into a **QR digital menu** for restaurants — menu items with
categories and prices, per-table QR codes with printable cards, and a one-tap handoff
to your ordering system (built to pair with a POS / QR ordering app).

## Features

- **Menu Items** — a dedicated post type with categories, featured images, price,
  badge ("Best seller", "Chef's pick"), and an availability toggle.
- **Digital menu page** — drop the `[qr_menu]` shortcode on any page for a mobile-first
  menu: category chips, item cards with photos and prices, accent colour and tagline
  from settings.
- **Table QR codes** — manage your tables ("1-6", or names like "Patio A") and get a
  printable card per table. Each code opens `…/menu/?table=3` with the table already
  selected.
- **Order handoff** — when a guest arrives via a table QR, an "Order at table N" button
  appears, linking to your ordering app with `?table=` appended.
- **Print & download** — print all cards from the admin (clean print stylesheet) or
  download each QR as a high-resolution PNG.

## Installation

1. Copy the `wp-qr-menu` folder into `wp-content/plugins/`.
2. Activate **WP QR Menu** in Plugins.
3. A **Menu** page with the `[qr_menu]` shortcode is created automatically.

## Usage

### Add menu items

**QR Menu → Add Item** — title, description (excerpt), featured image, price, badge,
and availability. Organise with **QR Menu → Categories**.

### Show the menu

Use the auto-created *Menu* page, or place the shortcode anywhere:

```
[qr_menu title="Cafe Uno"]
```

### Generate table QR codes

**QR Menu → Table QR Codes** — list your tables, save, then print the cards or
download the PNGs. Guests scan → menu opens with their table set.

### Ordering handoff

**QR Menu → Settings → Ordering app URL** — set the base URL of your ordering
system (e.g. a POS QR ordering app). The "Order at table N" button links there with
`?table=` appended. Leave empty to hide the button.

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Tech notes

- QR codes are generated fully client-side with
  [qrcode-generator](https://github.com/kazuhikoarase/qrcode-generator) (MIT) — no
  external API calls, works offline.
- All output escaped, nonces on every admin action, `manage_options` capability for
  settings and QR management.

## Author

Rani M. Rubillos — [ranimrubillos.vercel.app](https://ranimrubillos.vercel.app) ·
[github.com/chaze05](https://github.com/chaze05)

## License

GPL-2.0-or-later.
