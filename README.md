# Hypermarket Sales Management System

## 1. Project Overview
The **Hypermarket Sales Management System** is designed to manage **inventory, sales, and supplier interactions** efficiently. It includes **barcode scanning, real-time stock updates, sales reporting, and user role management**.

## Key Functionalities:
- **Sales Management:** Cashiers scan items, process payments (cash or card), and generate invoices.
- **Stock Movements:** Allows stock transfers between supermarkets and reorders from suppliers.
- **Supermarket Network Management:** Each supermarket connects to a central hypermarket server that oversees all operations.
- **Cash Register Management:** Handles cash transactions and tracks cash register activity.
- **Automated Stock Replenishment:** Checks stock availability in nearby supermarkets and places orders if necessary


# Use Case Descriptions

### (1) Sales Processing
- **Actors:** Cashier, Customer
- **Description:** The cashier scans the items, calculates the total, processes the payment, and issues an invoice.
- **Outcome:** A successful sale is recorded.

### (2) Stock Management
- **Actors:** Stock Manager, System
- **Description:** The system tracks stock levels, logs stock movements between supermarkets, and places orders if a product is out of stock.
- **Outcome:** Stock is replenished automatically or manually.

### (3) Supermarket Management
- **Actors:** Manager
- **Description:** The supermarket manager monitors sales, modifies product prices, and generates reports.
- **Outcome:** Sales trends and inventory levels are optimized.

### (4) Cash Register Management
- **Actors:** Cashier, Manager
- **Description:** The system ensures cash registers are functional, tracking cash transactions.
- **Outcome:** Secure cash handling and accountability.

### (5) Order Requests
- **Actors:** Supplier, Manager
- **Description:** When stock runs out, the system places orders to suppliers.
- **Outcome:** Products are delivered and restocked.

## 4. Database Schema

### **Tables**
1. `users`
2. `cash_registers`
3. `sales`
4. `sales_items`
5. `products`
6. `categories`
7. `suppliers`
8. `stock`
9. `stock_movements`
10. `order_requests`
11. `user_cash_register` _(Pivot Table)_

---

## 4. Database Relationships

### **One-to-Many (1:N) Relationships**
- **Users → Cash Registers** (A user can manage multiple cash registers)
- **Users → Order Requests** (A user can create multiple stock requests)
- **Cash Registers → Sales** (Each register handles multiple sales)
- **Sales → Sales Items** (A sale includes multiple products)
- **Products → Sales Items** (A product can be sold multiple times)
- **Categories → Products** (A category contains multiple products)
- **Suppliers → Products** (A supplier provides multiple products)
- **Products → Stock** (Each product has a stock entry)
- **Products → Stock Movements** (A product has multiple stock movements)
- **Suppliers → Order Requests** (A supplier receives multiple stock requests)

### **Many-to-Many (N:M) Relationships**
- **Users ↔ Cash Registers** _(via `user_cash_register` table)_  
  (A user can be assigned to multiple registers, and each register can have multiple users)
- **Products ↔ Order Requests** _(via an intermediary table)_  
  (A product can be included in multiple order requests, and each request can include multiple products)

---

## 5. Relationship Summary Table

| Table 1          | Relationship | Table 2       |
|------------------|-------------|--------------|
| Users           | 1:N         | Cash Registers  |
| Users           | 1:N         | Order Requests  |
| Cash Registers  | 1:N         | Sales           |
| Sales           | 1:N         | Sales Items     |
| Products        | 1:N         | Sales Items     |
| Products        | 1:N         | Stock Movements |
| Products        | 1:N         | Stock           |
| Products        | N:M         | Order Requests  |
| Categories      | 1:N         | Products        |
| Suppliers      | 1:N         | Products        |
| Suppliers      | 1:N         | Order Requests  |

---

## 6. Conclusion
This database structure provides a solid foundation for managing **sales, stock, and suppliers** in the hypermarket system.

- **Start with User, Supermarket, and Product models.**
- **Then build transactional models (Sales, Stock, OrderRequests).**
- **Finally, add supporting features (Stock Movements, Payments, Reports).**
