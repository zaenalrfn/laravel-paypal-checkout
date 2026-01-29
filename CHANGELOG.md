# Changelog

All notable changes to `laravel-paypal` will be documented in this file.

## [1.0.0] - 2026-01-29

### Added

- Initial release
- PayPal Checkout integration (create and capture orders)
- Payment refund functionality
- Webhook signature verification
- Webhook event handling (PAYMENT.CAPTURE.COMPLETED, DENIED, REFUNDED)
- Type-safe DTOs (CheckoutOrderData, PurchaseUnitData, AmountData)
- Custom PayPal exception with context
- Dedicated PayPal logging channel
- Comprehensive test suite with Pest PHP
- Development mode webhook bypass option
- Laravel 11 & 12 support

### Security

- Webhook signature verification enabled by default
- Environment-based credential management
- No hardcoded secrets

## [Unreleased]

### Planned

- Subscription support
- Dispute handling
- Order authorization (not just capture)
- Payout functionality
