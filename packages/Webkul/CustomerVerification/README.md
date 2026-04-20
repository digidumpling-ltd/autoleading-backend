# Customer Verification Package

The Customer Verification package provides comprehensive customer verification functionality for Bagisto, including document verification and automatic reference number generation for customer tracking.

## Features

### 1. Customer Document Verification
- Support for multiple verification document types (ID, Driver License, Address Proof)
- Document upload and storage during customer registration
- Admin dashboard for reviewing and approving/rejecting verification documents
- Verification status tracking (Incomplete, Pending, Approved, Rejected)

### 2. Customer Reference Number Generation
- Automatic reference number generation on customer registration
- Unique reference number format: `CV + YYYYMMDD + 6 Random Characters`
- Example: `CV20260418ABC123`
- Searchable reference number in admin verification dashboard
- Command to generate reference numbers for existing customers

## Installation

The package is automatically registered. Run migrations to set up the database tables:

```bash
php artisan migrate
```

## Usage

### For New Customers
Reference numbers are automatically generated during the customer registration process. The format includes:
- `CV` prefix (Customer Verification)
- Current date (YYYYMMDD)
- 6 random alphanumeric characters

Example: `CV20260418XYZ789`

### For Existing Customers
To generate reference numbers for customers who don't have one:

```bash
php artisan customer:generate-reference-numbers
```

This command will:
- Identify customers without reference numbers
- Generate unique reference numbers for each
- Display a progress bar during execution
- Confirm when complete

## Admin Features

### Verification Dashboard
Access the verification dashboard at `/admin/verification` to:
- View all customers requiring verification
- Filter by verification status (Incomplete, Pending, Approved, Rejected)
- Search by:
  - Reference number
  - Email address
  - Customer name
  - Phone number

### Customer Verification Process
1. Navigate to Customer Verification in admin menu
2. Search for customer by reference number, email, or name
3. Click "View" to see uploaded documents
4. Review verification documents
5. Approve or reject with optional rejection reason

## Database Schema

### customers table additions
- `reference_number` (string, nullable, unique) - Unique reference number for verification tracking

### customer_verification_documents table
- `id` - Primary key
- `customer_id` - Foreign key to customers table
- `type` - Document type (id_document, driver_license, address_proof)
- `path` - Storage path of the document
- `file_name` - Filename with timestamp
- `original_name` - Original filename
- `mime` - MIME type of the document
- `size` - File size in bytes
- `status` - Document verification status
- `timestamps` - Created and updated timestamps

## Events

The package triggers the following events:

- `customer.registration.after` - Triggered after customer registration, generates reference number and processes documents

## Localization

Translations are provided for:
- Reference number labels
- Status descriptions
- Button labels and messages

## Console Commands

### customer:generate-reference-numbers
Generates unique reference numbers for existing customers without one.

**Usage:**
```bash
php artisan customer:generate-reference-numbers
```

**Options:**
- `--silent` - Run without output
- `--quiet` - Only show errors
- `--verbose` - Show detailed output

## Configuration

Configuration files are located in the `Config/` directory:

- `menu.php` - Customer-side menu configuration
- `admin-menu.php` - Admin menu configuration
- `system.php` - System settings (if applicable)

## Extending the Package

### Creating Custom Listeners
The package uses event listeners to handle the verification flow. Custom listeners can be added to the `Listeners/` directory and registered in the `EventServiceProvider`.

### Adding Document Types
Additional document types can be added by:
1. Creating a new listener
2. Registering it in the `EventServiceProvider`
3. Updating localization files with new type names

## Support

For issues and feature requests related to customer verification, please refer to the main Bagisto documentation.
