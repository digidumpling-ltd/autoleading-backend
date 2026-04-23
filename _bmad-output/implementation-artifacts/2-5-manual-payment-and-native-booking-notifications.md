# Story 2.5: Manual Payment Configuration and Native Booking Notifications

Status: done

## Story

As a product/operator team,
I want booking orders to run on native manual payment configuration and native Bagisto notifications,
so that admin and customer receive required booking communications without backend customization.

## Acceptance Criteria

1. Given admin configuration is available, when payment and email settings are configured, then manual-payment and order notification toggles can be managed from admin configuration.

2. Given a booking order is placed successfully, when order-create events fire, then native new order communication is available for customer and admin according to configured toggles.

3. Given booking data is attached to cart/order items, when order-created emails are sent, then booking attributes are visible in email item details for both customer and admin templates.

4. Given admin reviews a pending booking and takes action, when native order lifecycle actions occur (cancel, invoice, shipment, comment), then follow-up communication is sent through native Bagisto listeners/templates according to configuration.

5. Given the business requires a second customer-facing confirmation after approval, when selecting native mechanisms, then invoice/comment flows are preferred before any custom notification implementation.

6. Given selected manual payment method is enabled, when booking checkout is tested, then placement flow behaves as expected for the selected method and no online capture is performed at booking placement.

7. Given this story completes, when handoff notes are reviewed, then operations has a clear configuration matrix for which toggles and actions trigger which communication.

## Tasks / Subtasks

- [x] Task 1: Capture config matrix for manual payment and email toggles (AC: 1, 7)
  - [x] Document required admin keys for payment method enablement
  - [x] Document required admin keys for customer/admin order emails

- [x] Task 2: Validate order-created communication for booking placement (AC: 2, 3)
  - [x] Test booking placement with manual payment enabled
  - [x] Verify customer and admin new-order emails triggered by native listeners
  - [x] Verify booking attributes appear in email item lines

- [x] Task 3: Validate post-review communication path (AC: 4, 5)
  - [x] Test cancel path and resulting customer/admin notifications
  - [x] Test invoice/comment path as confirmation communication
  - [x] Record exact operational step that should be treated as "booking confirmed"

- [x] Task 4: Validate no online capture at placement for selected manual flow (AC: 6)
  - [x] Confirm behavior in checkout/order creation
  - [x] Confirm expected order/payment record artifacts

- [x] Task 5: Produce operator runbook excerpt (AC: 7)
  - [x] Add concise checklist: toggles to enable, action to take, expected message recipients

## Dev Notes

- Story is configuration validation + communication mapping, not backend feature implementation.
- Use Bagisto native listeners and templates first.
- Only open a follow-up customization story if native flows cannot satisfy the exact business message timing/content.

---

## Config Matrix (Task 1 Output)

### Payment Method Enablement

Admin path: **Configuration > Sales > Payment Methods**

| Config Key | Field | Notes |
|-----------|-------|-------|
| `sales.payment_methods.cashondelivery.active` | Status toggle | Channel-based |
| `sales.payment_methods.cashondelivery.generate_invoice` | Auto-generate invoice on order | Set `false` for booking (admin approves manually) |
| `sales.payment_methods.cashondelivery.invoice_status` | Invoice status when generated | `pending` recommended |
| `sales.payment_methods.cashondelivery.order_status` | Order status at placement | `pending` recommended for booking review |
| `sales.payment_methods.moneytransfer.active` | Status toggle | Channel-based, alternative to COD |
| `sales.payment_methods.moneytransfer.generate_invoice` | Auto-generate invoice | Same as above |

**Recommended for booking flow**: Enable `cashondelivery` with `generate_invoice = false` and `order_status = pending`. This creates orders that require admin review before invoice.

### Email Notification Toggles

Admin path: **Configuration > Emails > General > Notifications**

| Config Key | Recipient | Trigger |
|-----------|-----------|---------|
| `emails.general.notifications.new_order` | Customer | Order placed |
| `emails.general.notifications.new_order_mail_to_admin` | Admin | Order placed |
| `emails.general.notifications.new_invoice` | Customer | Invoice created |
| `emails.general.notifications.new_invoice_mail_to_admin` | Admin | Invoice created |
| `emails.general.notifications.cancel_order` | Customer | Order cancelled |
| `emails.general.notifications.cancel_order_mail_to_admin` | Admin | Order cancelled |
| `emails.general.notifications.new_shipment` | Customer | Shipment created |
| `emails.general.notifications.new_shipment_mail_to_admin` | Admin | Shipment created |

---

## Event → Listener → Email Mapping (Task 2 & 3 Output)

| Event | Listener | Config Guard | Recipient |
|-------|----------|-------------|-----------|
| `checkout.order.save.after` | `Shop\Listeners\Order::afterCreated` | `new_order` | Customer |
| `checkout.order.save.after` | `Admin\Listeners\Order::afterCreated` | `new_order_mail_to_admin` | Admin |
| `sales.order.cancel.after` | `Shop\Listeners\Order::afterCanceled` | `cancel_order` | Customer |
| `sales.order.cancel.after` | `Admin\Listeners\Order::afterCanceled` | `cancel_order_mail_to_admin` | Admin |
| `sales.invoice.save.after` | `Shop\Listeners\Invoice::afterCreated` | `new_invoice` | Customer |
| `sales.invoice.save.after` | `Admin\Listeners\Invoice::afterCreated` | `new_invoice_mail_to_admin` | Admin |
| `sales.order.comment.create.after` | `Shop\Listeners\Order::afterCommented` | `customer_notified` flag on comment | Customer |

**Booking Confirmation path**: Admin creates invoice → fires `sales.invoice.save.after` → customer gets invoice email. This is the native "booking approved" notification. If richer messaging is needed, admin can also add a comment with `customer_notified = true` which triggers `CommentedNotification`.

---

## Booking Attributes in Emails (Task 2 Output)

Booking date/slot data lives in `order_item.additional['attributes']` (set by `BookingProduct\Helpers\Booking::getCartItemOptions()`).

Both email templates (`Admin` and `Shop` `emails/orders/created.blade.php`) iterate `$item->additional['attributes']` at line 122–125. No custom template changes are needed — booking attributes (date, slot, type) render natively in the order item section of both customer and admin new-order emails.

---

## No Capture at Placement (Task 4 Output)

Both `CashOnDelivery` and `MoneyTransfer` extend `Webkul\Payment\Payment\Payment`:
- `getRedirectUrl()` returns `null` — no redirect to payment gateway
- No `capture()` / `authorize()` call at order creation
- Payment record is created in `order_payment` table with `method = cashondelivery` but no transaction

Transaction record is written only when admin creates an invoice: `Admin\Listeners\Invoice::createTransaction()` (invoked if `$invoice->can_create_transaction` is true).

Order artifacts at placement: `orders` row (status = `pending`), `order_items`, `order_payment` (method name stored, no transaction ID).

---

## Operator Runbook (Task 5 Output)

### Pre-launch Checklist

1. **Enable payment method**
   - Admin > Configuration > Sales > Payment Methods > Cash On Delivery
   - Set `active = true`, `generate_invoice = false`, `order_status = pending`

2. **Enable email notifications**
   - Admin > Configuration > Emails > General > Notifications
   - Enable: `new_order`, `new_order_mail_to_admin`, `new_invoice`, `new_invoice_mail_to_admin`, `cancel_order`, `cancel_order_mail_to_admin`

3. **Configure SMTP**
   - Admin > Configuration > Emails > Email Settings > SMTP
   - Fill host, port, credentials, `sender_email`, `admin_email`

### Booking Order Lifecycle

| Step | Admin Action | Customer Receives | Admin Receives |
|------|-------------|-------------------|----------------|
| Booking placed | — | New order email | New order email |
| Admin approves | Create invoice (Sales > Orders > {order} > Invoice) | Invoice email ("booking confirmed") | Invoice email |
| Admin rejects | Cancel order | Cancel order email | Cancel order email |
| Additional info | Add comment with "Notify Customer" checked | Comment/update email | — |

### "Booking Confirmed" Operational Step

**Creating an invoice is the booking confirmation action.** The invoice email sent to the customer serves as the booking confirmation. No custom notification implementation is required for this story.
