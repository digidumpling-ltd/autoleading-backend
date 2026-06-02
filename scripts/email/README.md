# Email Test Scripts

Run all scripts from the project root via `ddev exec php artisan tinker <script>`.

---

## Scripts

### `payment-email.php`
Triggers the **Payment Received** email — the branded override of Bagisto's
`InvoicedNotification` (`shop::emails.orders.invoiced`). Uses the latest invoice
in the database.

```bash
# English
ddev exec php artisan tinker scripts/email/payment-email.php

# Chinese
ddev exec env TEST_LOCALE=zh_CN php artisan tinker scripts/email/payment-email.php
```

**Template**: `CustomTheme/Resources/views/emails/orders/invoiced.blade.php`
**Fired in production by**: Admin creating an invoice (`sales.invoice.save.after`)

---

### `order-confirmed-email.php`
Triggers the **Order Confirmed / Pick-up Memo** email. Uses the latest order
in the database.

```bash
# English
ddev exec php artisan tinker scripts/email/order-confirmed-email.php

# Chinese
ddev exec env TEST_LOCALE=zh_CN php artisan tinker scripts/email/order-confirmed-email.php
```

**Template**: `CustomTheme/Resources/views/emails/orders/order-confirmed.blade.php`
**Fired in production by**: Order status changing to `processing` (`sales.order.update-status.after`)
**Listener**: `CustomTheme/Listeners/Order::afterOrderConfirmed()`
**Toggle**: Admin → Configuration → Emails → General → Notifications → "Send a confirmation e-mail to the customer after placing a new order"

---

### `smtp-check.php`
Tests SMTP connectivity from inside the ddev container.

```bash
ddev exec php smtp-check.php
```

---

## Production flow

When admin creates an invoice for an order, **two emails fire in sequence**:
1. **Payment Received** — "We got your payment, reviewing your order"
2. **Order Confirmed** — Full pick-up memo with documents, notes, rental rules

Both emails respect the customer's locale (stored in `order->items->first()->additional['locale']`
at checkout time).

---

## After any changes

> **The queue worker is a long-running process. It does NOT auto-reload on code changes.**
> Any change — templates, listener logic, Mailable classes, translations, config — requires
> a worker restart to take effect.

```bash
ddev exec php artisan view:clear
ddev exec supervisorctl restart webextradaemons:queue-worker
```

---

## Mailpit

View sent emails at: **https://bagisto.ddev.site:8026**
Or run: `ddev mailpit`
