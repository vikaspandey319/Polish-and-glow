# Polish & Glow — PHP website

Responsive single-page website built with PHP, HTML5, Bootstrap 5, JavaScript and jQuery.

## Run locally

```bash
php -S localhost:8080
```

Open `http://localhost:8080`.

Business and social details are configured in `config.php`. Enquiries are stored in MySQL using PDO prepared statements.

## MySQL setup

1. Create a MySQL database and user.
2. Import `schema.sql` through phpMyAdmin.
3. Copy `.env.example` to `.env` and add the database host, port, name, username and password. `.env` is excluded from Git.

Hostinger commonly prefixes database and user names with the hosting account identifier. Copy the exact values shown under **Websites → Manage → Databases → MySQL Databases**.

## Course enquiry notifications

Academy course enquiries are emailed to `NOTIFICATION_EMAIL`. When a customer provides an email address, they also receive a confirmation from the Gmail account configured through `SMTP_USERNAME`. Use a Google App Password for `SMTP_PASSWORD`, never the normal Gmail password.

Automatic WhatsApp alerts use Meta's WhatsApp Cloud API. Configure the optional `WHATSAPP_*` values in `.env` and use an approved template containing four body variables in this order: student name, phone, preferred date, and message. Notification failures are logged and never prevent an enquiry from being stored.

Replace gallery placeholders in `index.php` with real client images when available.
