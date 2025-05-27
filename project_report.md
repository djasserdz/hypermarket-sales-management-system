# Project Report: Hypermarket Sales Management System

**Table of Contents**

1.  Acknowledgments .................................................................................. 4
2.  Introduction ..................................................................................... 5
    2.1. Problem Statement
    2.2. Objectives
3.  Chapter I: Analysis ............................................................................ 6
    3.1. Presentation of the Hypermarket Context
    3.2. Existing Hardware and Current Processes
        3.2.1. Hardware Resources
        3.2.2. Current Processes
            a. Sales Process
            b. Inventory Management
            c. Reporting
4.  Chapter II: Modeling ........................................................................... 8
    4.1. Use Case Diagram
    4.2. Entity-Relation Diagram
5.  Chapter III: Proposed System ................................................................... 13
    5.1. System Architecture
        5.1.1. Administration Panel (Filament + Laravel)
        5.1.2. Sales Management (Cashier Interface – React + Next.js)
        5.1.3. Inventory Management
        5.1.4. User Management
        5.1.5. Reports and Analytics Dashboards
        5.1.6. System Communication and Flow
        5.1.7. Desktop Application (Electron Integration)
6.  Chapter IV: Implementation Details ............................................................. 17
    6.1. Sales Processing
    6.2. Inventory Updates
    6.3. Role-Based Permissions
    6.4. Report Generation
    6.5. Admin Panel Configuration
    6.6. Tracking and Transfer Optimization
7.  Chapter V: Development Environment............................................................ 19
    7.1. Backend Technologies
    7.2. Frontend Technologies
    7.3. Styling and UI Libraries
    7.4. Database
    7.5. Deployment & Version Control
8.  Conclusion ..................................................................................... 20
    8.1. Encountered Difficulties
    8.2. Future Perspectives
9.  Appendices....................................................................................... 21
    9.1. A. Screenshots
    9.2. B. API Endpoints List
10. References........................................................................................ 25

**List of Figures**
*   Figure 3.1 – Use Case Diagram
*   Figure 3.2 – Database Description Diagram (ER Diagram)
*   Figure 4.1 – MVC Flow Diagram (System Architecture Overview)

---

## 1. Acknowledgments

*(Space for acknowledgments)*
<br>
<br>

---

## 2. Introduction

The Hypermarket Sales Management System is a comprehensive application designed to streamline and manage sales, inventory, and employee operations for a chain of hypermarkets. The system supports multiple supermarket locations, offering features like sales tracking, inventory control across branches, and role-based access for employees.

### 2.1. Problem Statement

Prior to the implementation of the Hypermarket Sales Management System, the hypermarket chain faced several operational challenges that hindered efficiency, accuracy, and growth. These challenges likely included:

*   **Inefficient Sales Processing**: Manual or outdated Point of Sale (POS) systems leading to slow checkout times, errors in transaction recording, and difficulty in tracking sales per cashier or register.
*   **Poor Inventory Visibility**: Lack of real-time, centralized inventory data across multiple supermarket branches, resulting in stockouts of popular items, overstocking of slow-moving goods, and difficulties in managing inter-branch product transfers.
*   **Data Silos**: Sales, inventory, and employee data often residing in disparate systems or spreadsheets, making comprehensive analysis and reporting cumbersome and prone to inaccuracies.
*   **Inconsistent Product Information**: Difficulties in maintaining uniform product pricing, descriptions, and categories across all branches.
*   **Challenges in Multi-Location Management**: Difficulties in overseeing operations, managing staff, and consolidating financial data from multiple supermarket locations effectively.
*   **Limited Reporting Capabilities**: Inability to generate timely and actionable reports on sales trends, inventory status, and employee performance, hindering strategic decision-making.
*   **Scalability Issues**: Existing systems struggling to cope with increasing transaction volumes, product ranges, and expansion to new locations.
*   **Security Concerns**: Potential risks associated with manual cash handling, lack of audit trails, and inadequate user access controls.

These issues collectively impacted customer satisfaction, operational costs, and the ability of management to make informed, data-driven decisions.
<br>
<br>

### 2.2. Objectives

The primary objectives for developing the Hypermarket Sales Management System were to address the aforementioned problems and provide a modern, integrated solution. Key objectives include:

*   **To Centralize and Streamline Operations**: Develop a unified platform for managing sales, inventory, and employee data across all hypermarket branches.
*   **To Enhance Sales Efficiency**: Implement a modern POS interface (web-based and/or desktop) to speed up checkout processes, reduce errors, and accurately track sales data linked to cashiers and registers.
*   **To Improve Inventory Management**: Provide real-time visibility into stock levels across all locations, automate stock updates, facilitate inter-branch transfers, and enable efficient supplier order management.
*   **To Ensure Data Accuracy and Consistency**: Maintain a single source of truth for product information (pricing, categories, barcodes), sales transactions, and inventory records.
*   **To Strengthen User Management and Security**: Implement role-based access control (Admin, Manager, Cashier) to ensure users only access relevant functionalities and data. Securely manage user authentication and provide audit trails.
*   **To Provide Comprehensive Reporting and Analytics**: Offer robust reporting tools and dashboards for insights into sales performance, inventory status, customer behavior (if applicable), and overall business health.
*   **To Support Multi-Location Operations**: Enable seamless management of multiple supermarket branches, including location-specific inventory, sales tracking, and employee shifts.
*   **To Increase Scalability and Reliability**: Build a system capable of handling growing business needs, transaction volumes, and potential future expansions.
*   **To Reduce Operational Costs**: Minimize losses due to stockouts or overstocking, reduce manual effort in data management, and improve overall operational efficiency.
<br>
<br>

---

## 3. Chapter I: Analysis

### 3.1. Presentation of the Hypermarket Context

*(This section requires specific details about the hypermarket. Below is a generic placeholder.)*

The hypermarket in question (referred to as "HyperMart Chain" for this report) operates as a multi-location retail business focusing on a wide variety of consumer goods, including groceries, household items, electronics, and apparel. Prior to this system, HyperMart Chain consisted of [Number] branches, each varying in size and customer traffic. The business model relies on high sales volume and efficient stock turnover. Key operational areas include sales processing at checkout, inventory management (receiving, stocking, and inter-branch transfers), supplier relations, and employee administration across its locations. The customer base is diverse, ranging from individual shoppers to small businesses.
The existing system struggled with providing a unified view of operations, leading to the decision to develop a custom, integrated solution.
<br>
<br>

### 3.2. Existing Hardware and Current Processes

#### 3.2.1. Hardware Resources

*(This section requires specific details. Below is a generic placeholder.)*

Prior to the new system, the hardware landscape across HyperMart Chain branches likely consisted of:

*   **Point of Sale (POS) Terminals**: A mix of older electronic cash registers or basic PC-based POS systems, potentially from different vendors and with limited networking capabilities.
*   **Barcode Scanners**: Handheld or fixed scanners at checkout counters, possibly with inconsistent performance.
*   **Receipt Printers**: Standard thermal or impact printers connected to POS terminals.
*   **Servers**: Branch-level servers (if any) for local data storage, or a centralized server with limited capacity/performance.
*   **Networking Infrastructure**: Basic LAN infrastructure within branches, with varying degrees of reliability for inter-branch connectivity (e.g., VPN, manual data transfer).
*   **Staff Computers**: Desktop PCs for managers and administrative staff, used for tasks like ordering, basic reporting (often spreadsheet-based), and communication.

The lack of standardized or modern hardware often contributed to inefficiencies and data integration challenges.
<br>
<br>

#### 3.2.2. Current Processes

##### a. Sales Process

*(This section requires specific details. Below is a generic placeholder.)*

The existing sales process typically involved:

1.  **Item Entry**: Cashiers manually keyed in product codes or prices, or used barcode scanners where available. This was prone to errors and could be slow.
2.  **Payment Processing**: Cash payments were standard. Card payments might have been handled through separate, non-integrated payment terminals.
3.  **Receipt Generation**: Basic receipts were printed, often with limited transaction details.
4.  **End-of-Day Reconciliation**: Cashiers manually reconciled cash drawers. Managers compiled daily sales figures, often a time-consuming and error-prone task involving manual data entry into spreadsheets or a rudimentary backend system.
5.  **Sales Data**: Access to consolidated sales data was often delayed, making it difficult to track real-time performance or identify trends quickly.

##### b. Inventory Management

*(This section requires specific details. Below is a generic placeholder.)*

Inventory management was characterized by:

1.  **Stock Taking**: Periodic (e.g., weekly, monthly) manual stock counts, which were labor-intensive and disruptive.
2.  **Receiving Goods**: Manual checking of deliveries against invoices, with data often entered into spreadsheets or a basic inventory system days later.
3.  **Stock Tracking**: Limited real-time visibility into stock levels. Decisions on reordering were often based on estimates or outdated information, leading to stockouts or overstocking.
4.  **Inter-Branch Transfers**: If transfers occurred, they were often managed ad-hoc with paper trails, lacking systematic tracking and reconciliation between branches.
5.  **Product Information**: Maintaining consistent product codes, descriptions, and pricing across branches was a significant challenge.

##### c. Reporting

*(This section requires specific details. Below is a generic placeholder.)*

Reporting capabilities were generally limited:

1.  **Manual Compilation**: Most reports (e.g., daily sales, stock levels) were compiled manually from various sources (POS printouts, spreadsheets), a time-consuming process.
2.  **Limited Scope**: Reports often focused on basic sales figures per branch, with little ability to perform deeper analysis (e.g., sales by product category, cashier performance, inventory turnover rates).
3.  **Lack of Timeliness**: Reports were often available days or weeks after the period end, reducing their utility for timely decision-making.
4.  **Data Inaccuracies**: Manual data entry and consolidation increased the risk of errors in reports.
<br>
<br>

---

## 4. Chapter II: Modeling

### 4.1. Use Case Diagram

*(This section describes the main actors and their interactions with the system. Figure 3.1 in the final report should be a visual representation of these use cases.)*

The Hypermarket Sales Management System involves several key actors and use cases that define its functionality:

**Actors:**

*   **Cashier**: Primary user of the sales interface. Responsible for processing customer transactions.
*   **Manager**: Oversees operations at a specific supermarket branch. Responsible for staff, inventory, local reporting, and approvals.
*   **Administrator**: Has system-wide access and control. Responsible for overall system configuration, user management across all branches, and aggregated data analysis.
*   **(Implicit) System**: Represents automated processes or scheduled tasks (e.g., daily report generation, database backups - though not explicitly modeled as a user-facing actor).

**Key Use Cases (grouped by actor/functionality):**

*   **Sales Operations (Cashier, potentially Manager for overrides/returns):**
    *   `Login/Logout to POS`: Authenticate and start/end a session on a specific cash register.
    *   `Process Sale`: 
        *   Includes: `Scan/Search Product`, `Add Item to Cart`, `Modify Cart (Quantity, Remove Item)`, `Apply Discounts (if applicable)`, `Process Payment (Cash, Card)`, `Generate Receipt`.
    *   `Handle Returns/Exchanges`: Process customer returns or exchanges (may require manager approval).
    *   `View Transaction History (Own/Register)`: Check past sales for the current shift or register.
*   **Inventory Management (Manager, Administrator):**
    *   `Manage Products`: (Admin primarily, Manager might have limited view/request capability)
        *   Includes: `Add New Product`, `Edit Product Details (Price, Description, Category, Supplier)`, `Deactivate Product`.
    *   `Manage Categories & Suppliers`: (Admin primarily) CRUD operations for product categories and suppliers.
    *   `Manage Stock Levels`: 
        *   Includes: `View Stock (per product, per supermarket)`, `Receive New Stock (from supplier orders)`, `Adjust Stock Manually (with reason/audit)`, `Perform Stock Take`.
    *   `Manage Supplier Orders`: (Manager, Admin for approval)
        *   Includes: `Create Purchase Order`, `Track Order Status (Pending, Ordered, Shipped, Received)`, `Receive Order into Stock`.
    *   `Manage Product Transfers`: (Manager for initiation/receipt, Admin for oversight)
        *   Includes: `Initiate Transfer Request (to another branch)`, `Approve/Reject Transfer Request`, `Track Transfer Status (Pending, In Transit, Delivered)`, `Receive Transferred Stock`.
*   **User & System Management (Administrator, Manager for their staff):**
    *   `Manage Users`: (Admin for all, Manager for cashiers in their branch)
        *   Includes: `Add New User`, `Edit User Details`, `Assign Roles`, `Activate/Deactivate User Account`, `Reset Password`.
    *   `Manage Supermarkets & Locations`: (Admin) CRUD operations for supermarket branches and their location details.
    *   `Manage Cash Registers`: (Admin, Manager for their branch) Add/remove cash registers for a supermarket.
    *   `Manage Shifts`: (Manager) Assign cashiers to shifts and cash registers.
    *   `View Audit Logs`: (Admin) Track key system events and changes.
    *   `Configure System Settings`: (Admin) Set up global parameters, notification rules, etc.
*   **Reporting & Analytics (Manager, Administrator):**
    *   `Generate Sales Reports`: (Daily, weekly, monthly; by product, category, cashier, supermarket).
    *   `Generate Inventory Reports`: (Stock levels, stock aging, low stock, transfer history).
    *   `View Dashboards`: Visual representation of key performance indicators.
*   **Authentication & Authorization (All Users):**
    *   `Login`: Authenticate to the system (API for POS, Web for Admin Panel).
    *   `Logout`: Terminate session.
    *   (System implicitly handles role-based access control for all use cases).

*(Figure 3.1 – Use Case Diagram, would visually represent these actors and their connections to these use cases, often using ovals for use cases and stick figures for actors.)*
<br>
<br>

### 4.2. Entity-Relation Diagram

The core of the system is built around a relational database. The ERD (`project_database.png`) illustrates the relationships between the various entities in the system. The schema is defined and managed via Laravel Migrations.

*(Figure 3.2 – Database Description Diagram (ER Diagram) - which is `project_database.png` - would be embedded or referenced here, ideally updated to reflect all fields and relationships detailed below.)*

**Key Application Entities (derived from database migrations):**

*   **`users`**: Stores employee information.
    *   `id` (Primary Key)
    *   `name` (string)
    *   `email` (string, unique, nullable)
    *   `role` (enum: 'admin', 'cashier', 'manager', default: 'cashier')
    *   `email_verified_at` (timestamp, nullable)
    *   `password` (string)
    *   `rememberToken`
    *   `timestamps` (created_at, updated_at)
*   **`supermarkets`**: Stores details about each supermarket.
    *   `id` (Primary Key)
    *   `name` (string)
    *   `manager_id` (Foreign Key to `users.id`, cascade on delete)
    *   `timestamps`
*   **`locations`**: Tracks physical addresses linked to supermarkets.
    *   `id` (Primary Key)
    *   `supermarket_id` (Foreign Key to `supermarkets.id`, cascade on delete)
    *   `street_address` (string)
    *   `city` (string)
    *   `state` (string)
    *   `latitude` (double)
    *   `longitude` (double)
    *   `timestamps`
*   **`cash_registers`**: Represents individual cash registers within each supermarket.
    *   `id` (Primary Key)
    *   `supermarket_id` (Foreign Key to `supermarkets.id`, cascade on delete)
    *   `timestamps`
*   **`shifts`**: Tracks employee work schedules and cash register assignments.
    *   `id` (Primary Key)
    *   `user_id` (Foreign Key to `users.id`, cascade on delete)
    *   `cash_register_id` (Foreign Key to `cash_registers.id`, cascade on delete)
    *   `start_at` (timestamp)
    *   `end_at` (timestamp, nullable)
    *   (Note: Does not use default `timestamps`)
*   **`suppliers`**: Holds supplier contact information.
    *   `id` (Primary Key)
    *   `name` (string)
    *   `phone_number` (string)
    *   `timestamps`
*   **`categories`**: Classifies products.
    *   `id` (Primary Key)
    *   `name` (string)
    *   `timestamps`
*   **`products`**: Stores product details.
    *   `id` (Primary Key)
    *   `name` (string)
    *   `barcode` (string)
    *   `price` (decimal, e.g., 10,2)
    *   `category_id` (Foreign Key to `categories.id`, cascade on delete)
    *   `supplier_id` (Foreign Key to `suppliers.id`, cascade on delete)
    *   `timestamps`
*   **`stocks`** (Note: table name is `stocks` in migration): Keeps track of current product stock levels in each supermarket.
    *   `id` (Primary Key)
    *   `supermarket_id` (Foreign Key to `supermarkets.id`, cascade on delete)
    *   `product_id` (Foreign Key to `products.id`, cascade on delete)
    *   `quantity` (integer)
    *   `timestamps`
*   **`sales`**: Records each sales transaction.
    *   `id` (Primary Key)
    *   `cash_register_id` (Foreign Key to `cash_registers.id`, cascade on delete)
    *   `payment_method` (enum: 'cash', 'card')
    *   `timestamps` (captures date/time of sale)
*   **`sale_items`**: Details the items and quantities sold in each transaction.
    *   `id` (Primary Key)
    *   `sale_id` (Foreign Key to `sales.id`, cascade on delete)
    *   `product_id` (Foreign Key to `products.id`, cascade on delete)
    *   `quantity` (integer)
    *   `timestamps`
*   **`transfers`**: Logs product transfers between supermarkets.
    *   `id` (Primary Key)
    *   `product_id` (Foreign Key to `products.id`, cascade on delete)
    *   `from_supermarket` (Foreign Key to `supermarkets.id`, cascade on delete - consider renaming to `from_supermarket_id` for convention)
    *   `to_supermarket` (Foreign Key to `supermarkets.id`, cascade on delete - consider renaming to `to_supermarket_id` for convention)
    *   `quantity` (integer)
    *   `status` (enum: 'pending', 'in_transit', 'delevired' - 'delevired' is likely a typo for 'delivered')
    *   `timestamps`
*   **`supplier_orders`**: Manages orders placed with suppliers for specific supermarkets.
    *   `id` (Primary Key)
    *   `product_id` (Foreign Key to `products.id`, cascade on delete)
    *   `supplier_id` (Foreign Key to `suppliers.id`, cascade on delete)
    *   `supermarket_id` (Foreign Key to `supermarkets.id`, cascade on delete)
    *   `quantity_ordered` (integer)
    *   `status` (enum: 'pending_approval', 'ordered', 'shipped', 'received', 'cancelled', default: 'pending_approval')
    *   `notes` (text, nullable)
    *   `timestamps`
*   **`sale_reports`**: Stores metadata about generated sales reports.
    *   `id` (Primary Key)
    *   `file_path` (string)
    *   `report_date` (date)
    *   `timestamps`

**Laravel Standard/Internal Tables (also created by migrations):**
*   **`notifications`**: Standard Laravel table for database notifications.
    *   `id` (UUID, Primary Key)
    *   `type` (string)
    *   `notifiable_type` (string)
    *   `notifiable_id` (unsignedBigInteger)
    *   `data` (text)
    *   `read_at` (timestamp, nullable)
    *   `timestamps`
*   **`password_reset_tokens`**: Standard Laravel table for password reset functionality.
    *   `email` (Primary Key)
    *   `token` (string)
    *   `created_at` (timestamp, nullable)
*   **`sessions`**: Standard Laravel table for managing user sessions (if using database session driver).
    *   `id` (string, Primary Key)
    *   `user_id` (Foreign Key to `users.id`, nullable)
    *   `ip_address` (string, nullable)
    *   `user_agent` (text, nullable)
    *   `payload` (longText)
    *   `last_activity` (integer)
*   **`cache` / `jobs` / `personal_access_tokens`**: Other standard Laravel tables for caching, queued jobs, and API authentication tokens, respectively, if their migrations were run (migrations for `cache`, `jobs`, `personal_access_tokens` were listed previously).

<br>
<br>

---

## 5. Chapter III: Proposed System

### 5.1. System Architecture

The Hypermarket Sales Management System is designed as a modern, multi-component application primarily leveraging the Laravel framework for its backend and API, Filament for the administration panel, and a potentially separate JavaScript-based application for the cashier/POS interface.

*(Figure 4.1 – MVC Flow Diagram (System Architecture Overview) would be placed here or referenced. This diagram should illustrate the main components: Web Browser (for Admin Panel), POS Application (Cashier Interface - React/Next.js or NativePHP), Backend (Laravel API, Business Logic, Database), and how they interact, possibly showing request/response flows.)*

**Core Architectural Principles:**
*   **Modular Design**: Components are designed to be relatively independent, allowing for easier development, maintenance, and potential scaling of individual parts.
*   **API-Driven**: A central RESTful API (built with Laravel) serves as the primary communication channel between the backend and frontend components (especially the cashier interface and potentially mobile apps in the future).
*   **Centralized Database**: A single relational database (e.g., MySQL, PostgreSQL in production; SQLite for development) acts as the source of truth for all data, managed by Laravel Eloquent ORM and Migrations.
*   **Role-Based Access Control (RBAC)**: Security is enforced at multiple levels, with distinct roles (Admin, Manager, Cashier) dictating access to functionalities and data.
*   **Scalability**: While initially monolithic (Laravel backend), the API-driven approach allows for future separation of services if needed.

The system is designed with a modular approach, encompassing several key components detailed below.
<br>

#### 5.1.1. Administration Panel (Filament + Laravel)

*   The system includes a comprehensive administration panel built using Filament (^3.3) on top of the Laravel (^12.0) framework.
*   **Key Functionalities (Typical for Filament):**
    *   CRUD (Create, Read, Update, Delete) operations for all key entities (Products, Categories, Suppliers, Supermarkets, Users, Shifts, etc.).
    *   Management of system settings and configurations.
    *   User management, including role assignments (admin, manager, cashier).
    *   Viewing and managing sales data, inventory levels, and product transfers.
    *   Potentially, dashboards for displaying key metrics and reports.
    *   Filament's notification system (`filament/notifications` ^3.3) can be used for admin alerts.
*   **Technology**: Filament leverages Livewire, Alpine.js, and Tailwind CSS to create reactive interfaces directly within the Laravel ecosystem.
<br>
<br>

#### 5.1.2. Sales Management (Cashier Interface – React + Next.js)

*(The use of React + Next.js is based on the ToC; `nativephp/laravel` is an alternative for a desktop POS. This section describes the ideal features of such an interface.)*

The Cashier Interface is a critical component designed for speed, ease of use, and accuracy at the point of sale. 

*   **Technology (Assumed/Planned)**: Likely a Single Page Application (SPA) built with React and potentially Next.js for structure and SSR/SSG capabilities if parts are web-accessible, or a desktop application via NativePHP or Electron embedding web technologies.
*   **Key Functionalities**:
    *   **User Authentication**: Secure login for cashiers, associating them with a specific cash register and shift (`POST /login` endpoint).
    *   **Product Lookup/Scanning**: Fast product search by name/keyword or barcode scanning (`POST /search` endpoint).
    *   **Dynamic Cart Management**: Adding items, adjusting quantities, removing items, real-time calculation of subtotals and totals.
    *   **Payment Processing**: Support for multiple payment methods (e.g., cash, card - via `POST /ticket` endpoint). Integration with payment gateways would be a future enhancement if not already present.
    *   **Receipt Generation**: Display of transaction summary and printing of receipts (data from `/ticket` response).
    *   **Offline Capabilities (Desirable for Desktop/NativePHP)**: Ability to process sales even with intermittent network connectivity, syncing data once connection is restored.
    *   **Hardware Integration (Desktop/NativePHP)**: Direct control of receipt printers, barcode scanners, and cash drawers.
    *   **Session Management**: Tracking sales per shift, end-of-shift reconciliation prompts.
    *   **Intuitive UI/UX**: Designed for rapid adoption by cashiers, minimizing clicks and errors.
<br>
<br>

#### 5.1.3. Inventory Management

The proposed system provides robust inventory management capabilities, primarily orchestrated through the Laravel backend and accessed via the Filament Admin Panel by Managers and Administrators.

*   **Real-time Stock Tracking**: The `stocks` table is updated in real-time upon sales (`Sale_items` creation), reception of supplier orders (`supplier_orders` status to 'received'), and completion of inter-branch transfers (`transfers` status to 'delivered').
*   **Product Master Data**: Centralized management of `products`, `categories`, and `suppliers` through the Admin Panel ensures consistency.
*   **Supplier Order Management**: Interface for creating `supplier_orders`, tracking their status (`pending_approval`, `ordered`, `shipped`, `received`, `cancelled`), and receiving items into stock. This updates `stocks` and links to the `suppliers` and `products` tables.
*   **Inter-Branch Transfers**: Functionality to manage `transfers` of products between `supermarkets`. This involves status tracking (`pending`, `in_transit`, `delivered`) and corresponding updates to `stocks` at both source and destination upon completion.
*   **Stock Adjustments**: Secure interface for authorized users to perform manual stock adjustments (e.g., for damages, discrepancies), with changes logged for auditability.
*   **Low Stock Alerts**: The system can be configured (potentially using Laravel Tasks and `notifications`) to monitor stock levels and alert managers when quantities fall below reorder points, helping prevent stockouts.
*   **Reporting**: Inventory reports (stock levels, aging, valuation - if pricing data is used) are available through the Admin Panel.
<br>
<br>

#### 5.1.4. User Management

User management is handled by Laravel's authentication and authorization systems, with interfaces provided through the Filament Admin Panel.

*   **Roles**: Clearly defined roles (`admin`, `manager`, `cashier`) stored in the `users` table's `role` enum field.
*   **Authentication**: Secure password handling (hashed passwords) and session management for the Admin Panel. API token-based authentication (Laravel Sanctum) for the Cashier Interface or other external clients (`POST /login`, `POST /logout`).
*   **Authorization**: Laravel Gates and Policies are used to control access to specific models, actions, and Filament resources based on user roles.
    *   **Admins**: Full CRUD access to all system data and configurations.
    *   **Managers**: Access limited to their assigned supermarket(s) for managing staff (cashiers), inventory, local reports, and initiating/receiving transfers. They can manage `shifts` for their staff.
    *   **Cashiers**: Primarily restricted to sales processing functions on their assigned `cash_register` during their `shift`.
*   **User Administration**: Admins (and Managers for their staff) can create, view, update, and deactivate users through the Filament panel. This includes managing user details like name, email, and role.
*   **Shift Management**: Managers can create and manage `shifts`, assigning `users` (cashiers) to specific `cash_registers` for defined periods (`start_at`, `end_at`).
<br>
<br>

#### 5.1.5. Reports and Analytics Dashboards

The system provides reporting and analytics capabilities primarily through the Filament Admin Panel, with data sourced from the centralized database.

*   **Sales Analytics**: 
    *   Detailed sales reports filterable by date range, supermarket, cashier, product, category.
    *   Metrics such as total sales, number of transactions, average transaction value.
    *   The `sale_reports` table suggests a mechanism for storing generated report files (`file_path`, `report_date`).
*   **Inventory Analytics**: 
    *   Real-time stock level reports per product and supermarket.
    *   Reports on stock movement (sales, transfers, receipts).
    *   Potential for stock aging reports and identification of slow-moving vs. fast-moving items.
*   **User Performance**: Reports on cashier performance (e.g., sales volume, items per transaction).
*   **Dashboard**: Filament allows for the creation of custom dashboards with widgets (charts, stats, tables) to provide at-a-glance views of KPIs for Managers and Admins.
*   **PDF Export**: The `barryvdh/laravel-dompdf` library enables exporting reports to PDF format.
*   **Custom Reporting**: While standard reports are available, the underlying data structure allows for ad-hoc queries or custom report development if needed.
<br>
<br>

#### 5.1.6. System Communication and Flow

Communication between the system components is primarily API-driven and orchestrated by the Laravel backend.

*   **Cashier Interface (e.g., React/Next.js/NativePHP) to Backend API**:
    *   Uses HTTPS requests to the Laravel RESTful API endpoints (e.g., `/login`, `/search`, `/ticket`).
    *   Authentication is handled via tokens (Laravel Sanctum) sent in request headers.
    *   Data is exchanged in JSON format.
*   **Admin Panel (Filament/Web Browser) to Backend**:
    *   Standard web requests (HTTP/HTTPS) to Laravel routes.
    *   Authentication is session-based (Laravel web authentication).
    *   Filament leverages Livewire for dynamic UI components, making AJAX calls to the backend for reactive updates without full page reloads.
*   **Backend to Database**: 
    *   Laravel Eloquent ORM is used for all database interactions (CRUD operations).
    *   Database queries are generated by Eloquent based on model interactions.
*   **Internal Backend Communication**: 
    *   Laravel's event system can be used for decoupling actions (e.g., an event `SaleProcessed` could trigger listeners for `UpdateStock` and `GenerateSaleNotification`).
    *   Queued jobs (Laravel Queues, with `jobs` table) can be used for handling long-running tasks asynchronously (e.g., bulk report generation, sending email notifications).
*   **Notifications**: 
    *   Database notifications (Laravel's standard `notifications` table) are used for user-specific alerts (e.g., new transfer awaiting approval, low stock warning). Filament has built-in support for displaying these.
    *   Email notifications can also be triggered by backend events.
*   **(Figure 4.1 – MVC Flow Diagram would visually represent this, showing the Laravel backend as the central controller, with frontend clients and the database interacting through it.)**
<br>
<br>

#### 5.1.7. Desktop Application (Electron Integration)

*(Describe the purpose and features of the desktop application. Why Electron? What specific functionalities does it provide that a web interface might not, or what benefits does it offer? e.g., Offline capabilities for sales processing, better hardware integration.)*
<br>
<br>

---

## 6. Chapter IV: Implementation Details

### 6.1. Sales Processing

*(Detail the workflow of a sales transaction. From item selection to payment and receipt generation. Mention API endpoints involved like `/search`, `/ticket`.)*
<br>
<br>

### 6.2. Inventory Updates

*   **Sales**: Upon successful completion of a sale via the `POST /ticket` endpoint, the system automatically decrements the quantity of each sold item in the `Stock` table for the corresponding `supermarket_id` and `product_id`.
*   **Stock Transfers**: 
    *   When a product transfer is initiated (likely via the Admin Panel/Filament), a record is created in the `transfers` table with a `status` (e.g., `pending`).
    *   Upon dispatch from the source supermarket, stock may be marked as `in_transit` or deducted from the source supermarket's `Stock`.
    *   When the destination supermarket confirms receipt (transfer `status` updated to `delivered`), the `Stock` for those products at the destination supermarket is incremented.
*   **New Stock/Supplier Orders**: 
    *   When new products are received from a supplier (managed via the `supplier_orders` table, likely through the Admin Panel), the `Stock` table is updated to reflect the new quantities for the respective products and supermarket.
*   **Manual Adjustments**: The Admin Panel (Filament) should provide a secure interface for authorized personnel (e.g., managers, admins) to make manual adjustments to stock levels, with logging of such changes for auditing purposes.
*   **Real-time/Near Real-time**: Updates aim to be real-time or near real-time to ensure accurate stock information across the system, preventing overselling and informing reordering processes.
*   **Low Stock Alerts**: The system may incorporate logic (e.g., using `notifications` table or Filament notifications) to alert managers when stock for a product at a particular supermarket falls below a predefined threshold.
<br>
<br>

### 6.3. Role-Based Permissions

*   **User Roles**: The system defines distinct user roles as identified in `README.md` and implied by API structures: `admin`, `manager`, and `cashier`.
*   **Authentication**: Laravel Sanctum is used for API authentication (`POST /login` returns a token). The Filament admin panel uses Laravel's standard session-based authentication.
*   **Authorization**: 
    *   **API Routes**: API endpoints like `/user/addCacheRegister`, `/user/addCachier`, etc., are likely protected by middleware that checks for appropriate roles (e.g., `manager` or `admin`). The API documentation for `/login` indicates a `role` field is returned, which can be used by the client, but server-side authorization is critical.
    *   **Filament Resources & Actions**: Filament has its own authorization mechanisms. Model Policies and Gates in Laravel are typically used to control access to Filament resources (e.g., viewing product lists, creating new users) and specific actions (e.g., deleting a product, approving a transfer).
    *   **Cashier Permissions**: Cashiers primarily interact with sales-related functions (`/search`, `/ticket`). They have limited access, typically to a specific `cash_register` they are logged into (as per `/login` parameter `cash_register_id`).
    *   **Manager Permissions**: Managers would have broader access, potentially managing users (cashiers for their supermarket), inventory for their location, initiating transfers, and viewing reports specific to their supermarket.
    *   **Admin Permissions**: Admins have superuser access, managing all aspects of the system, including all supermarkets, system-wide settings, all users, and aggregated reports.
*   **Implementation**: Laravel's built-in Authorization features (Gates and Policies) are the standard way to implement RBAC, integrating seamlessly with Filament.
<br>
<br>

### 6.4. Report Generation

*   **API Indication**: The `api_docs.md` is titled "Daily Sales Report API Documentation," suggesting a focus on sales reports. The `sale_reports` table in the database migrations further confirms report generation capabilities.
*   **Types of Reports (Potential)**:
    *   Daily/Weekly/Monthly Sales Reports (by supermarket, by cashier, by product, by category).
    *   Inventory Reports (current stock levels, stock aging, low stock alerts).
    *   Transfer Reports (tracking products moved between supermarkets).
    *   User Activity Reports (e.g., cashier sales performance).
*   **Generation Process**:
    *   Reports are likely generated by querying and aggregating data from `sales`, `sales_items`, `stock`, `products`, `users`, and `supermarkets` tables.
    *   Filament admin panel is a likely interface for managers/admins to generate and view reports. It offers charting and table widgets for dashboards.
    *   The `barryvdh/laravel-dompdf` library can be used to export reports as PDF files.
    *   The `sale_reports` table might store metadata about generated reports or pre-calculated report data for performance.
*   **Customization**: Reports might be filterable by date ranges, supermarket, cashier, etc. Filament allows for custom filters on table views and custom dashboard widgets.
<br>
<br>

### 6.5. Admin Panel Configuration

*   **Framework**: Filament (^3.3) provides the structure for the admin panel.
*   **Resources**: Filament Resources are defined for each manageable Eloquent model (e.g., `ProductResource`, `UserResource`, `SupermarketResource`). These resources automatically provide CRUD interfaces.
    *   Table views for listing records with searching, sorting, and filtering capabilities.
    *   Forms for creating and editing records with various field types and validation.
*   **Customization**: 
    *   **Tables**: Columns, filters, and actions in Filament tables are customizable.
    *   **Forms**: Form schemas are defined with a wide array of input components. Complex layouts (tabs, wizards, sections) can be created.
    *   **Dashboards**: Custom dashboard widgets can be created to display stats, charts, and summaries.
    *   **Navigation**: The main navigation menu is configurable, grouping resources and adding custom links.
    *   **Authentication & Authorization**: Integrates with Laravel's standard authentication and uses Laravel Policies/Gates for fine-grained permission control on resources and actions.
*   **Key Configuration Areas (assumed for this project)**:
    *   Managing supermarkets, locations, cash registers.
    *   Product catalog management (products, categories, suppliers, supplier orders).
    *   Inventory management (stock levels, initiating/tracking transfers).
    *   User administration (admins, managers, cashiers) and their roles/shifts.
    *   Viewing sales data and generating reports.
*   **Filament Plugins**: The project might use additional Filament plugins for enhanced functionality (e.g., advanced charts, import/export tools), though none are explicitly listed in `composer.json` beyond core Filament packages.
<br>
<br>

### 6.6. Tracking and Transfer Optimization

*   **Transfer Tracking**: 
    *   The `transfers` table is central to tracking products moved between supermarkets. It includes `from_supermarket_id`, `to_supermarket_id`, `product_id`, `quantity`, and a `status` field (e.g., `pending`, `in_transit`, `delivered`).
    *   The Admin Panel (Filament) would be the primary interface for initiating transfers and updating their status.
    *   Notifications (via `notifications` table or Filament notifications) could be used to alert relevant supermarket managers about incoming or dispatched transfers.
*   **Optimization**: 
    *   The inclusion of `graphp/graph` and `graphp/algorithms` in `composer.json` suggests that there might be plans or an existing implementation for optimizing transfer routes or logistics if the hypermarket chain has many locations or complex transfer needs.
    *   **Potential Optimizations (if implemented using graph libraries)**:
        *   Finding the shortest path for a multi-stop transfer.
        *   Optimizing stock distribution based on demand and current stock levels across different locations.
        *   Calculating optimal transfer quantities to balance stock without overstocking.
    *   If not yet implemented, these libraries provide the tools for future enhancements in this area.
*   **Inventory Impact**: Transfers directly impact stock levels (decrementing at the source, incrementing at the destination upon confirmation) as detailed in Section 6.2.
<br>
<br>

---

## 7. Chapter V: Development Environment

### 7.1. Backend Technologies

*   **Framework**: Laravel (PHP)
*   **API**: RESTful API (as per `api_docs.md`)
*   *(Other libraries or tools used in the backend, e.g., for authentication - Sanctum/Passport, background jobs, etc.)*
<br>
<br>

### 7.2. Frontend Technologies

*   **Admin Panel**: Filament (built on Laravel Livewire, Alpine.js, Tailwind CSS)
*   **Cashier Interface**: React, Next.js
*   **Desktop Application**: Electron
*   *(Other libraries like state management (Redux, Zustand), data fetching (SWR, React Query) for the React part)*
<br>
<br>

### 7.3. Styling and UI Libraries

*   **Admin Panel**: Tailwind CSS (via Filament)
*   **Cashier Interface**: *(Specify CSS framework or methodology e.g., Material UI, Tailwind CSS, Styled Components)*
<br>
<br>

### 7.4. Database

*   *(Specify the chosen relational database, e.g., MySQL, PostgreSQL, SQLite - inferred from Laravel usage but good to state explicitly)*
<br>
<br>

### 7.5. Deployment & Version Control

*   **Version Control**: Git (as per `.git` directory and `.gitignore` file)
*   **Deployment Environment**: *(e.g., Docker, AWS, dedicated server. Mention web server like Nginx or Apache)*
*   **CI/CD**: *(If any, e.g., GitHub Actions, Jenkins)*
<br>
<br>

---

## 8. Conclusion

The Hypermarket Sales Management System, built with Laravel and Filament, provides a robust and scalable platform for managing the complex operations of a modern hypermarket chain. It successfully centralizes sales, inventory, and user management, offering real-time data insights and streamlined workflows. The system addresses key limitations of previous manual or disparate systems, aiming to improve efficiency, reduce errors, enhance security, and provide valuable analytics for data-driven decision-making. The modular architecture, leveraging a strong backend API, allows for flexible frontend implementations (including a powerful admin panel and a dedicated cashier interface) and future expansions.

### 8.1. Encountered Difficulties

*(This section requires specific project experiences. Below is a generic placeholder for common difficulties in such projects.)*

While the project aims for successful implementation, typical challenges that might be encountered during the development lifecycle of a system of this scale could include:

*   **Requirement Elicitation & Changes**: Accurately capturing all business rules and user requirements from diverse stakeholders across multiple hypermarket branches. Scope creep or changing requirements during development can pose challenges.
*   **Data Migration**: Migrating data from potentially legacy and disparate systems (if any) accurately and efficiently, ensuring data integrity.
*   **Integration Complexity**: 
    *   If a separate React/Next.js frontend for the cashier interface is developed, ensuring seamless API integration, state management, and performance can be complex.
    *   Integrating with existing or new hardware (POS terminals, printers, scanners), especially if drivers or compatibility issues arise.
    *   Potential future integrations with third-party services (e.g., payment gateways, accounting software).
*   **User Adoption and Training**: Ensuring all user types (cashiers, managers, admins) are adequately trained and comfortable with the new system to maximize its benefits.
*   **Performance Under Load**: Ensuring the system (especially the database and API) performs efficiently under peak load conditions, such as during busy sales periods.
*   **Multi-Location Synchronization**: Maintaining data consistency and near real-time updates across different supermarket branches, especially if network reliability is a concern in some locations.
*   **Security Implementation**: Thoroughly implementing and testing security measures, including role-based access, data encryption, and protection against common web vulnerabilities.
*   **Timeline and Resource Management**: Adhering to project timelines and managing development resources effectively, especially if the team is distributed or working on multiple components.
<br>
<br>

### 8.2. Future Perspectives

The current system provides a strong foundation for future growth and enhancements. Potential future perspectives and developments include:

*   **Advanced Analytics and Business Intelligence**: 
    *   Integration of more sophisticated BI tools for deeper data analysis, predictive analytics (e.g., sales forecasting, demand planning), and customizable dashboards.
    *   Customer behavior analysis by linking sales data with loyalty programs.
*   **Customer Loyalty Program**: Implementation of a points-based or tiered loyalty program to enhance customer retention and gather more detailed customer data.
*   **E-commerce Integration/Online Ordering**: Development of an online sales channel, allowing customers to order products for pickup or delivery, with real-time inventory checks against the existing system.
*   **Mobile Applications**: 
    *   Mobile app for managers: Allowing access to key reports, inventory status, and approval workflows on the go.
    *   Mobile app for staff: For tasks like stock taking, receiving goods, or internal communication.
    *   Potentially a customer-facing mobile app for browsing products, promotions, and loyalty accounts.
*   **Enhanced Payment Gateway Integration**: Direct integration with a wider range of payment gateways for seamless online and in-store card processing.
*   **Integration with Accounting Software**: Automated synchronization of sales and financial data with popular accounting platforms to streamline financial reporting and reconciliation.
*   **Supply Chain Optimization**: More advanced features for supplier management, purchase order optimization, and tracking goods from supplier to shelf, potentially using the `graphp` libraries more extensively.
*   **AI-Powered Features**: 
    *   AI-driven recommendations for product reordering.
    *   Personalized promotions for customers based on purchase history.
    *   Chatbots for customer support or internal help.
*   **Multi-Warehouse/Distribution Center Management**: If the hypermarket chain grows to include central distribution centers, extending the system to manage inventory and logistics at that level.
*   **Enhanced Hardware Integration**: Broader support for various POS hardware, self-checkout kiosks, and RFID technology for inventory tracking.
<br>
<br>

---

## 9. Appendices

### 9.1. A. Screenshots

*(Placeholder for screenshots of the application - Admin Panel, Cashier Interface, Reports, etc.)*
<br>
<br>

### 9.2. B. API Endpoints List

*(A summarized list of key API endpoints. The full details are in `api_docs.md`)*

*   `POST /login`: User login.
*   `POST /logout`: User logout.
*   `POST /search`: Search for products.
*   `POST /ticket`: Create a sales ticket.
*   `POST /user/addCacheRegister`: Add a cash register.
*   `POST /user/addCachier`: Add a cashier.
*   `POST /user/cashiers`: List cashiers.
*   `DELETE /user/cashiers/{id}`: Delete a cashier.
*   `PUT /user/cashiers/{id}`: Update a cashier.
*(Add other key endpoints if necessary, based on `api_docs.md`)*
<br>
<br>

---

## 10. References

*(List any documents, websites, books, or tools that were referenced or used during the project.)*
*   `README.md` (Project Overview and ERD description)
*   `api_docs.md` (Detailed API Documentation)
*   `project_database.png` (Entity Relationship Diagram)
*   `composer.json` (Backend dependencies and scripts)
*   `package.json` (Frontend dependencies and scripts)
*   `vite.config.js` (Frontend build configuration)
*   Laravel Documentation: [https://laravel.com/docs](https://laravel.com/docs)
*   FilamentPHP Documentation: [https://filamentphp.com/docs](https://filamentphp.com/docs)
*   NativePHP Documentation: [https://nativephp.com/docs](https://nativephp.com/docs)
*   React Documentation: [https://react.dev/](https://react.dev/) (If used for cashier interface)
*   Next.js Documentation: [https://nextjs.org/docs](https://nextjs.org/docs) (If used for cashier interface)
*   Electron Documentation: [https://www.electronjs.org/docs](https://www.electronjs.org/docs) (If used for desktop app)
*   Tailwind CSS Documentation: [https://tailwindcss.com/docs](https://tailwindcss.com/docs)
<br>
<br>