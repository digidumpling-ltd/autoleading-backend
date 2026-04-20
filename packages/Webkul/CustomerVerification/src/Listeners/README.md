# Listeners

This directory contains event listeners for the Customer Verification package.

## HandleCustomerRegistration

Handles customer registration workflow including reference number generation and document verification.

### Purpose
Processes customer registration events and:
1. Generates unique reference number for customer tracking
2. Stores uploaded verification documents
3. Updates customer verification status

### Reference Number Generation

**Triggered**: On `customer.registration.after` event  
**Format**: `CV + YYYYMMDD + 6 Random Alphanumeric Characters`  
**Example**: `CV20260418ABC123`

### How It Works

#### 1. Generate Reference Number
When a customer registers:
- A unique reference number is generated with current date
- Uniqueness is verified against existing customer records
- Reference number is assigned to the customer
- Used for customer identification in admin verification dashboard

#### 2. Store Documents
If customers upload verification documents during registration:
- ID Document (`id_document`) - Government issued ID
- Driver License (`driver_license`) - Driver's license  
- Address Proof (`address_proof`) - Proof of address

Documents are stored with:
- Timestamp in filename for uniqueness
- File metadata (MIME type, size, original name)
- Organized storage in `storage/app/public/customer-documents/`

#### 3. Update Verification Status
Initial verification status is set based on document upload:
- **incomplete** - No documents uploaded
- **pending** - Documents uploaded, awaiting admin review
- **approved** - Customer approved by admin (updated later)
- **rejected** - Customer rejected by admin (updated later)

### Key Methods

#### `handle($customer): void`
Main entry point triggered by `customer.registration.after` event.

Parameters:
- `$customer` - Customer model instance

Process:
```php
1. Generate unique reference number
2. Store uploaded documents
3. Update verification status
4. Save customer record
```

#### `generateReferenceNumber($customer): void`
Generates and assigns unique reference number.

#### `createUniqueReferenceNumber(): string`
Creates a unique reference number with current date and random suffix.

#### `storeDocuments($customerId, $request): bool`
Processes and stores uploaded verification documents.

Returns `true` if any documents were uploaded.

### Events Listened To

- `customer.registration.after` - Fired after successful customer registration

### Database Operations

#### customers table
```sql
UPDATE customers 
SET reference_number = 'CV20260418XYZ789'
WHERE id = 1;
```

#### customer_verification_documents table
```sql
INSERT INTO customer_verification_documents 
  (customer_id, type, path, file_name, original_name, mime, size)
VALUES 
  (1, 'id_document', 'customer-documents/..., '...', 'id.pdf', 'application/pdf', 2048);
```

### Storage Location

Documents are stored in: `storage/app/public/customer-documents/`

Filename format: `{timestamp}_{type}_{customer_id}.{extension}`

Example: `1718641200_id_document_1.pdf`

### Error Handling

The listener safely handles:
- Missing request files
- Invalid file types
- Storage failures
- Duplicate document types
- Database conflicts

### Configuration

Configuration can be extended through:
- `Config/system.php` - System settings
- Environment variables
- Configuration cache

### Related Listeners

- `HandleCustomerLogin` - Handles login verification checks
- `PreventUnverifiedRentalAddToCartListener` - Prevents unverified users from renting

### Example Event Flow

```
User Registration Form Submitted
    ↓
Customer Model Created
    ↓
customer.registration.after Event Triggered
    ↓
HandleCustomerRegistration Listener
    ├─ Generate Reference Number (e.g., CV20260418ABC123)
    ├─ Store ID Document
    ├─ Store Driver License
    ├─ Store Address Proof
    └─ Update Verification Status
    ↓
Verification Email Sent (if enabled)
    ↓
Admin Notified (optional)
```

### Notes

- Reference numbers are globally unique across the system
- Document validation is performed for security
- All operations are database-transactional for data integrity
- Failed document uploads do not prevent registration
- Verification status drives visibility in admin dashboard
