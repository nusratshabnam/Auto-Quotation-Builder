# Service Quote Builder

A comprehensive WordPress plugin for creating multi-step service configuration and quote builder tools, inspired by automotive paint protection and detailing services.

## Description

Service Quote Builder allows you to create interactive, multi-step quote calculators on your WordPress site. Perfect for automotive services, detailing packages, or any business that needs dynamic pricing forms.

## Features

- **Multi-Step Wizard**: Guide customers through a smooth 6-step quote building process
- **Visual Selection Cards**: Attractive card-based UI for easy selection
- **Dynamic Pricing**: Real-time price calculation as selections are made
- **Vehicle Size Categories**: Small, Mid, Full, and Large/X-Large vehicle options
- **Paint Condition Assessment**: Visual indicators for different paint conditions
- **Polishing Options**: Multiple service levels with time estimates
- **Paint Protection Products**: Product comparison with rating metrics
- **Additional Services**: Checkbox-style add-ons for extras
- **Maintenance Kits**: Optional add-on products
- **Save & Share**: Allow customers to save quotes with share codes
- **Email Notifications**: Automatic email alerts for new quote submissions
- **Admin Dashboard**: Manage quotes, change status, view details
- **Responsive Design**: Works perfectly on mobile devices
- **Localization Ready**: Fully translatable

## Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin as a ZIP file
2. Go to **Plugins > Add New > Upload Plugin**
3. Click **Choose File** and select the ZIP file
4. Click **Install Now**
5. Activate the plugin

### Method 2: Manual Upload

1. Upload the `service-quote-builder` folder to `/wp-content/plugins/`
2. Go to **Plugins** in WordPress admin
3. Find **Service Quote Builder** and click **Activate**

## Usage

### Basic Usage

Add the following shortcode to any page or post:

```
[service_quote_builder]
```

### Custom Title

You can customize the header title:

```
[service_quote_builder title="Get Your Custom Quote"]
```

## Configuration

### Admin Settings

Navigate to **Quote Builder > Settings** to configure:

- **Currency Symbol**: Set your preferred currency ($, €, £, etc.)
- **Currency Position**: Before or after the amount
- **VAT Rate**: Add percentage-based tax
- **Notification Email**: Email address for quote alerts

### Managing Quotes

Navigate to **Quote Builder > Quotes** to:

- View all submitted quotes
- Filter by status (Pending, Completed, Cancelled)
- View detailed quote breakdowns
- Change quote status
- Delete quotes
- Export quote data

## The Quote Builder Steps

1. **Vehicle Type**: Select vehicle size category
2. **Paint Condition**: Assess current paint condition
3. **Polishing**: Choose polishing service level
4. **Protection**: Select paint protection product
5. **Extras**: Add additional services (glass, rims, interior, etc.)
6. **Details**: Enter customer information and submit

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Screenshots

1. **Multi-Step Interface**: Clean progress bar showing current step
2. **Visual Cards**: Attractive selection cards for each option
3. **Protection Products**: Product comparison with rating bars
4. **Running Total**: Live sidebar showing quote total
5. **Admin Dashboard**: Manage all submitted quotes

## Frequently Asked Questions

**Q: Can I customize the pricing?**
A: Currently pricing is hardcoded in the template. For customization, you can edit the template file directly.

**Q: Does it support multiple languages?**
A: Yes! The plugin is translation-ready. You can use a translation plugin like WPML or Loco Translate.

**Q: Can I add my own products or services?**
A: Currently you would need to edit the template files to add custom products.

**Q: Where are quotes stored?**
A: Quotes are stored in a custom database table created upon plugin activation.

**Q: How do I receive email notifications?**
A: Configure the notification email in Settings. New quote submissions will trigger an email to this address.

## Changelog

### 1.0.0
- Initial release
- Multi-step quote builder
- Dynamic pricing calculations
- Admin dashboard
- Email notifications
- Save/share functionality

## Upgrade Notice

No upgrade notices at this time.

## License

This plugin is released under the GPL v2 or later license.

## Support

For support, feature requests, or bug reports, please create an issue in the repository or contact the developer.

---

**Author**: MiniMax Agent
**Version**: 1.0.0
**Last Updated**: 2026-05