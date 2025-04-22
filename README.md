# 🎟️ WordPress Ticketing System Plugin

A lightweight, self-hosted ticketing system for WordPress that allows **registered users** to submit, view, and manage support tickets directly from the frontend. This plugin integrates seamlessly with [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) for user registration and login, providing a clean and secure support workflow for member-based websites, clubs, and internal teams.

---

## ✨ Features

- ✅ Frontend ticket submission form for logged-in users
- 📜 Ticket history view for users to track their submissions
- 🔐 Access control powered by Ultimate Member (or native WordPress)
- 🛠️ Admin panel to view and manage all tickets
- 🚫 Prevents guest access to ticket functionality
- 🧩 Easy to customize and extend

---

## 📦 Installation

1. Download the plugin:

2. Copy the folder into your WordPress plugins directory:

wp-content/plugins/custom-ticket-system/

3. Activate the plugin from the WordPress Dashboard → Plugins

4. Ensure you have Ultimate Member installed and activated for user registration/login

🛠️ Usage
🧑‍💻 Frontend

    Logged-in users can:

        Submit new tickets via the /submit-ticket/ page (or your custom shortcode placement)

        View their ticket history at /my-tickets/

    Guests will be redirected to the login/register page

🔒 Backend (Admin)

    Go to Tickets → All Tickets to:

        View all submitted tickets

        Change ticket statuses (e.g., Open, In Progress, Closed)

        Respond (optional feature depending on customization)

🧩 Shortcodes

You can place ticket forms or lists using shortcodes:
Shortcode	Description
[ticket_form]	Displays the ticket submission form
[ticket_list]	Displays the logged-in user's ticket list
🎨 Customization

    Tweak styles via your theme’s CSS

    Template overrides supported (coming soon)

    You can extend functionality with custom fields, email notifications, etc.


📂 File Structure

wp-ticketing-system/
├── includes/
│   ├── ticket-functions.php
│   ├── admin-dashboard.php
│   └── ...
├── templates/
│   ├── form-ticket.php
│   ├── list-tickets.php
│   └── ...
├── wp-ticketing-system.php


🛡️ License
This plugin is open-source and released under the MIT License.
