# Customer Controllers

This directory contains controllers for customer-facing verification features.

## RegistrationController

Extended with automatic reference number generation during customer registration.

### Features

#### Automatic Reference Number Generation
When a customer registers, a unique reference number is automatically generated:

**Format**: `CV + YYYYMMDD + 6 Random Characters`  
**Example**: `CV20260418ABC123`

#### Document Upload Support
Customers can upload verification documents during registration:
- ID Document
- Driver License
- Address Proof

#### Verification Status Tracking
Automatically sets verification status based on document upload:
- `incomplete` - No documents uploaded
- `pending` - Documents uploaded, awaiting review
- `approved` - Customer verified by admin
- `rejected` - Verification rejected

### Methods

#### `index()`
Displays the customer registration form.

#### `store(RegistrationRequest $registrationRequest)`
Processes the registration form submission:
1. Validates input data
2. Creates customer account
3. Generates unique reference number
4. Processes uploaded documents
5. Sets initial verification status
6. Triggers registration events
7. Sends verification email if enabled

### Events

The controller fires the following event after successful registration:
- `customer.registration.after` - Triggered after successful registration

This event fires the `HandleCustomerRegistration` listener which:
- Generates the reference number
- Processes uploaded documents
- Updates verification status

### Routes

```
GET  /customer/register                    - Show registration form
POST /customer/register                    - Store registration data
```

### Example Usage

#### Registration with Documents
```php
POST /customer/register
Parameters:
- first_name: John
- last_name: Doe
- email: john@example.com
- password: secret123
- password_confirmation: secret123
- id_document: [file]
- driver_license: [file]
- address_proof: [file]
```

Response:
- Customer created with reference number: `CV20260418XYZ789`
- Verification status set to: `pending`
- Email sent with verification link

### Related Controllers

- `VerificationDashboardController` - Customer dashboard for viewing verification status
- `Admin/VerificationManagementController` - Admin interface for managing verifications

### Localization Keys

```php
'shop::app.customers.signup-form.success-verify'
'shop::app.customers.signup-form.verification-documents-uploaded'
```

### Notes

- Reference numbers are guaranteed unique in the database
- Documents are stored in `storage/app/public/customer-documents/`
- All files are scanned for security before storage
- Customers can view their verification status in their account dashboard
This directory intentionally left blank.