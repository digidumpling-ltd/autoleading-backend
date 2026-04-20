# Console Commands

This directory contains Artisan console commands for the Customer Verification package.

## GenerateCustomerReferenceNumbers

### Description
Generates unique reference numbers for customers that do not have one. This is useful for existing customers created before the reference number feature was implemented.

### Usage
```bash
php artisan customer:generate-reference-numbers
```

### Reference Number Format
- **Prefix**: `CV` (Customer Verification)
- **Date**: `YYYYMMDD` (Current date when generated)
- **Random**: 6 uppercase alphanumeric characters

**Example**: `CV20260418ABC123`

### How It Works
1. Queries all customers without a reference number
2. Generates a unique reference number for each customer
3. Checks for uniqueness in the database
4. Saves the reference number to the customer record
5. Displays progress bar during execution

### Options
```bash
# Run without any output
php artisan customer:generate-reference-numbers --silent

# Only show errors
php artisan customer:generate-reference-numbers --quiet

# Increase verbosity
php artisan customer:generate-reference-numbers -v
```

### Output Examples

#### When customers need reference numbers:
```
Generating reference numbers for 15 customers...
████████████████████████████████ 15/15 [============================] 100%
Reference numbers generated successfully!
```

#### When all customers have reference numbers:
```
All customers already have reference numbers.
```

### Why Use This Command?

1. **Bulk Update**: Efficiently update multiple customers at once
2. **Data Integrity**: Ensures all customers have unique reference numbers
3. **Search Capability**: Enables searching by reference number in admin dashboard
4. **Tracking**: Facilitates verification tracking with unique identifiers

### Scheduling

To run this command periodically, add it to your scheduler in `app/Console/Kernel.php`:

```php
$schedule->command('customer:generate-reference-numbers')
    ->monthly()
    ->onSuccess(function () {
        \Log::info('Customer reference numbers generated successfully');
    });
```

### Related Commands

```bash
# Check help information
php artisan help customer:generate-reference-numbers

# View all available console commands
php artisan list
```

### Notes

- The command is idempotent - running it multiple times on the same customers will not create duplicates
- Reference numbers are generated sequentially but with randomization to ensure uniqueness
- The command respects the database connection configuration
- No customer data is modified other than the reference number assignment
