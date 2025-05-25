# Lodge Subscription Bundle for Mautic

This Mautic plugin provides a complete solution for managing lodge membership subscriptions, payments, and tracking dues.

## Features

- **Membership Management**: Track lodge members' subscription status and payment history
- **Payment Processing**: Accept payments online via Stripe or record manual payments
- **Year-End Processing**: Automate the handling of arrears and year-end subscription rollovers
- **Stripe Integration**: Process online payments securely with Stripe
- **Email Tokens**: Generate payment links to send to members via email campaigns
- **Reporting**: View comprehensive statistics on payments and outstanding dues

## Requirements

- Mautic 6.0 or later
- PHP 8.0 or later
- MySQL/MariaDB database

## Installation

1. Download the plugin from the [GitHub repository](https://github.com/yourusername/mautic-lodge-subscription-bundle).
2. Extract the downloaded file to the `plugins` directory of your Mautic installation.
3. Clear the Mautic cache:
   ```
   php bin/console cache:clear
   ```
4. Go to the Plugins page in Mautic and click "Install/Upgrade Plugins".
5. Configure the plugin settings with your Stripe credentials and subscription rates.

## Usage

### Setting Subscription Rates

1. Navigate to the Lodge Subscription → Settings page.
2. Enter the subscription rates for the current year.
3. Configure Stripe API keys for online payments.

### Recording Payments

1. Navigate to a contact's profile.
2. Click on the Lodge Subscription tab.
3. Click "Add Payment" to record a new payment.
4. Fill in the payment details and save.

### Online Payments

1. From a contact's subscription page, click "Generate Payment Link".
2. Send the payment link to the member via email.
3. The member can use the link to make a payment online using Stripe.

### Year-End Processing

1. Navigate to Lodge Subscription → Year End.
2. Ensure next year's subscription rates are configured.
3. Click "Execute Year End Process" to roll over subscriptions to the new year.

## Custom Fields

The plugin creates several custom fields for contacts:

- `craft_subscription_type`: Type of membership (Full/Reduced/Honorary)
- `craft_last_payment_date`: Date of the last payment
- `craft_last_payment_amount`: Amount of the last payment
- `craft_YYYY_paid`: Whether dues for a specific year are paid
- `craft_YYYY_due`: Whether dues for a specific year are due

## Technical Documentation

See the [Technical Documentation](docs/index.md) for details about the plugin's architecture, models, and entities.

## Troubleshooting

- **Payments not being recorded**: Ensure webhook URLs are correctly configured in Stripe.
- **Year-end process failing**: Check that all required custom fields exist and next year's subscription rates are set.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

This plugin is licensed under the GPL v3.0 License - see the [LICENSE](LICENSE) file for details. 