---
stepsCompleted: [1, 2, 3, 4, 5, 6]
inputDocuments: []
date: 2026-03-14
author: Developer
---

# Product Brief: bagisto

<!-- Content will be appended sequentially through collaborative workflow steps -->

## Executive Summary

This project is a Bagisto-powered backend for a car rental business where Store Credit is a core payment mechanism, not just a loyalty add-on. Customers prepay through membership plans (for example, HKD 3,000 converted into 3,000 credits at a 1:1 ratio), then consume credits for each rental booking. The system must support two credit pools (purchased and bonus), controlled expiry policies (lifetime or expiring), admin-issued credit adjustments, and strict sufficiency checks at booking time with seamless top-up for shortfalls.

The immediate objective is to validate this prepaid-credit rental model in a new market setup using mostly Bagisto out-of-the-box capabilities plus focused custom modules. Phase 1 prioritizes Store Credit as the primary differentiator and operational backbone; POS is explicitly deferred to Phase 2.

---

## Core Vision

### Problem Statement

Traditional car rental flows rely on per-transaction payment and manual adjustments, which do not support a prepaid membership-credit model effectively. For this project, the business requires a digital credit wallet that can fund rentals directly, track multiple credit types, and support controlled refund behavior at product level. Bagisto's native capabilities cover much of the commerce stack, but do not fully address this domain-specific Store Credit lifecycle.

### Problem Impact

Without a dedicated Store Credit module:
- Membership plan value cannot be operationalized as wallet balance.
- Booking flow cannot reliably validate available credit and enforce top-up.
- Bonus campaigns and admin grants become manual and error-prone.
- Refund handling becomes inconsistent across products and channels.
- Finance/support lose traceability without a full credit ledger and reason codes.

This creates launch risk, customer confusion, and operational overhead in a model where credit is central to every rental.

### Why Existing Solutions Fall Short

General e-commerce payment patterns in Bagisto are transaction-first, while this rental model is wallet-first. Out-of-the-box behavior does not natively provide:
- Membership purchase to wallet conversion as a first-class flow.
- Separation of purchased credit vs bonus credit with defined consumption priority.
- Product-level refund policy controls tied to credit restoration rules.
- Unified ledger-grade auditability for credit issuance, deductions, reversals, and admin actions.
- Booking-time hard validation for credit sufficiency with required top-up completion.

As a result, custom extension is required to align commerce behavior with rental-credit operations.

### Proposed Solution

Implement a Phase 1 Store Credit module (or set of tightly scoped extensions) on top of Bagisto that delivers:

- Membership-to-credit conversion:
	- Purchase membership plans that issue purchased credit at 1:1 value.
	- Initial deployment assumes HKD as primary currency while preserving 1:1 logic for configured currencies.

- Dual-wallet balance model:
	- Purchased credit and bonus credit stored separately.
	- Deterministic deduction strategy (bonus first, then purchased).

- Booking payment enforcement:
	- At booking calculation, check total payable against available credit.
	- If insufficient, force top-up of remaining balance before completion.
	- No negative balance allowed.

- Refund and reversal framework:
	- Per-car/product configuration: refundable or non-refundable.
	- Refund destination selectable: account transfer or store credit.
	- Cancellation and reversal entries update ledger transparently.

- Admin and control capabilities:
	- Manual credit grants/adjustments by authorized admins.
	- Strong audit trail with immutable ledger-style transactions and reason taxonomy.

### Key Differentiators

- Store Credit as primary payment rail, deeply integrated into rental booking logic.
- Membership-prepaid model aligned to customer behavior in car rental use cases.
- Two-bucket credit architecture (bonus vs purchased) with explicit policy control.
- Flexible refund rules at product level with selectable destination handling.
- Finance-ready transaction history with auditability from day one.
- Pragmatic delivery strategy: Bagisto core reuse + precise custom modules for high-impact gaps.

## Target Users

### Primary Users

**1) Member Customer (Individual Renter)**
- Context: A customer who must purchase one of three membership plans before renting.
- Goal: Access vehicles quickly while paying from prepaid store credit.
- Motivation: Predictable spending and membership-linked rental benefits.
- Current friction: Cannot rent until membership is active and funded; potential confusion about credit balance and plan benefits.
- Success looks like: They can purchase a plan, see credited balance immediately, and complete rentals with smooth automatic deductions.

**2) Returning Member (Frequent Renter)**
- Context: Existing member who rents repeatedly and manages credit over time.
- Goal: Track remaining balance, top up when needed, and maximize plan benefits.
- Motivation: Faster repeat booking and clear value from membership tier.
- Current friction: Unclear separation of bonus vs purchased credit and uncertainty about expiry/refund outcomes.
- Success looks like: Transparent wallet ledger, predictable deduction behavior, and confidence in every booking payment outcome.

### Secondary Users

**1) Platform Admin / Operations**
- Context: Internal team managing membership-credit operations.
- Goal: Configure plans, issue/manual-adjust credit, run promotions, and enforce policy.
- Motivation: Operational control with minimal manual reconciliation.
- Current friction: Without dedicated credit tooling, adjustments and audits become error-prone.
- Success looks like: Centralized controls, clear reason-based adjustments, and complete transaction traceability.

**2) Finance / Support Staff**
- Context: Team handling customer inquiries, disputes, and refund decisions.
- Goal: Verify transactions quickly and process refunds according to product-level policy.
- Motivation: Reduce dispute resolution time and maintain trust.
- Current friction: Inconsistent visibility into why balances changed.
- Success looks like: Immutable ledger with event reasons (plan purchase, deduction, promo, admin grant, reversal/refund).

### User Journey

**1) Discovery**
- Customer finds the rental service and learns that membership is required.

**2) Onboarding**
- Customer purchases one of three membership plans.
- System converts payment to store credit at 1:1 and activates member eligibility.

**3) Core Usage**
- Customer browses cars and books rentals.
- Booking checks available credit.
- If balance is insufficient, customer tops up the remaining amount.
- Upon successful booking, credit is deducted according to policy (bonus first, then purchased).

**4) Success Moment**
- Customer completes a rental with no payment friction and sees accurate remaining credit immediately.

**5) Long-term Use**
- Customer repeats rentals, receives promotional bonus credit, and manages credit lifecycle (including expiry where applicable).
- Admin/support handle exceptions through controlled adjustments and refund destination rules.

## Success Metrics

For Phase 1, success is defined primarily by technical correctness, operational reliability, and policy compliance of the Store Credit system.

### Technical Success Criteria

1. Membership-to-credit conversion works deterministically:
- On successful membership purchase, purchased credit is issued at 1:1.
- Credit issuance is atomic (no partial state where order succeeds but credit not granted).

2. Booking credit validation is enforced:
- Every booking computes payable amount and validates available credit before confirmation.
- If insufficient, user is required to top up the exact remaining balance before completion.
- No negative wallet balance is possible.

3. Dual-balance deduction behavior is correct:
- Bonus credit and purchased credit are tracked separately.
- Deduction priority is deterministic and test-covered (bonus first, then purchased).

4. Refund policy engine is respected:
- Per-car/product refundability flag is enforced.
- Refund destination (account transfer vs store credit) is executed as configured.
- Reversal/refund entries are reflected in wallet and ledger consistently.

5. Ledger and auditability are complete:
- All balance-changing events are recorded immutably with reason/type metadata.
- Admin adjustments require reason attribution and actor trace.
- Ledger can reconstruct current balance from event history.

6. Admin operations are controlled:
- Authorized admins can add credit manually.
- Unauthorized users cannot mutate balances.
- All admin mutations are auditable and searchable.

7. Expiry model is supported:
- Credits can be configured as lifetime or expiring.
- Expiry processing does not corrupt balances and is traceable in ledger.

8. Deployment readiness:
- Core credit flows are covered by automated tests (unit + feature).
- Critical paths meet performance and integrity baselines for launch.

### Business Objectives

Deferred for later planning phase.
Current phase focuses on technical readiness and correctness of Store Credit domain behavior.

### Key Performance Indicators

1. Credit Transaction Integrity:
- Target: 0 unreconciled wallet transactions in test and staging environments.

2. Booking Validation Correctness:
- Target: 100% of tested insufficient-credit scenarios block booking until top-up.

3. Deduction Policy Accuracy:
- Target: 100% pass rate for bonus-first then purchased-credit deduction test matrix.

4. Refund Policy Compliance:
- Target: 100% pass rate for refundable/non-refundable and destination routing scenarios.

5. Audit Completeness:
- Target: 100% of credit mutations include actor, reason, source event, and timestamp.

6. Security and Access Control:
- Target: 0 unauthorized credit mutation paths in permission and integration tests.

7. Critical Test Coverage:
- Target: 100% coverage of core credit domain use cases in acceptance test suite.

## MVP Scope

### Core Features

1. Membership plan purchase flow with 1:1 credit issuance (HKD-first).
2. Wallet model with separate purchased credit and bonus credit balances.
3. Credit deduction during booking with deterministic order (bonus first, then purchased).
4. Insufficient-credit handling requiring top-up of remaining balance before booking completion.
5. Per-car/product refundability setting.
6. Refund destination selection (account transfer or store credit).
7. Admin credit adjustments (grant/add) with permissions.
8. Immutable credit ledger with reason, actor, source event, and timestamp.
9. Credit expiry support (lifetime or expiring).

### Out of Scope for MVP

1. POS workflows and cashier operations (Phase 2).
2. Advanced loyalty tiers beyond current 3 membership plans.
3. Multi-currency-specific conversion logic (keep 1:1 baseline).
4. Deep analytics dashboards beyond essential operational reporting.
5. Complex partner/franchise settlement automation.

### MVP Success Criteria

1. End-to-end membership-to-credit issuance is reliable and atomic.
2. Booking cannot complete when credit is insufficient unless top-up succeeds.
3. Deduction and refund policies behave exactly as configured.
4. Ledger can fully reconstruct balance history and pass audit checks.
5. Permission boundaries prevent unauthorized balance mutation.
6. Core flow acceptance tests pass for all priority scenarios.

### Future Vision

1. POS integration using same wallet/ledger domain rules.
2. Expanded membership plan mechanics and campaign automation.
3. Rich operational analytics for credit economics and customer behavior.
4. Potential multi-currency optimization if market expands.
