---
title: 'Project Invoice'
summary: 'A project management and invoicing platform for service businesses.'
description: 'Enables organisations to manage clients, track projects through a defined workflow, generate professional PDF documents (quotes, invoices, receipts, delivery notes), and monitor payments and profitability — all scoped per organisation with role-based access control.'
tech:
    - PHP 8.4
    - Laravel 12
    - Laravel Breeze
    - Blade Templates
    - MySQL
    - TCPDF
    - Bootstrap 5
    - Feather Icons
    - JavaScript
    - jQuery
    - 'HTML & CSS'
live_url: 'https://your-live-url.com'
github_url: 'https://github.com/jmunyira1/project_invoice'
featured: false
active: true
---

Project Invoice

A web-based platform for service businesses to manage clients, track projects, generate professional documents, and
monitor payments — all in one place.

Built with multi-tenancy at its core, every organisation operates in a fully isolated environment with role-based access
for owners and team members.

✨ Key Features

    Client Management
    Maintain a directory of clients with contact details and address. Each client profile shows full project history, total work value across all projects, and a quick-create button for new projects.

    Project Workflow
    Projects move through a defined status pipeline: Draft → Quoted → Active → Completed → Cancelled. Each project tracks deliverables, internal costs, documents and payments in one place, with real-time financial summaries showing project value, total costs, gross profit and outstanding balance.

    Deliverables & Internal Costs
    Add client-facing line items (deliverables) with quantity and unit price — these feed directly into generated documents. Record internal project costs separately; these are private and never shown to clients, giving you accurate margin visibility on every project.

    Document Generation (PDF via TCPDF)
    Generate professional A4 PDF documents directly from a project — Quotes, Invoices, Receipts, Delivery Notes and Statements. Documents are auto-numbered per organisation (e.g. INV-2501-001), viewed inline in the browser, downloadable, and can be marked as sent.

    Document Templates
    Multiple styled templates available for PDF generation, each with a distinct look. Templates can be previewed with live sample data before being set as the organisation default. The organisation name, logo, contact details and currency are applied automatically to every document.

    Payment Tracking
    Record payments against a project or link them to a specific invoice. Supports M-Pesa (with transaction code), cash, bank transfer, cheque and card. The system always shows the outstanding balance on every project and document.

    Financial Dashboard
    A live overview of business performance — total revenue collected, outstanding balance on sent invoices, active project count, current month income, recent payment activity, and a list of unpaid invoices with overdue highlighting.

    Organisation Settings
    Owners can configure the organisation profile — name, email, phone, address, currency and logo. The uploaded logo appears automatically on all generated PDFs.

    Team Management
    Invite team members and assign them as Owner or Member. Owners have full system access including settings. Members can manage clients, projects, documents and payments but cannot access organisation configuration.

    Multi-tenancy
    Every record is scoped to an organisation. Middleware enforces tenant isolation on every request, so no data leaks between organisations.
