# Order & Payment Management API

A Laravel-based RESTful API for managing orders and payments, designed for extensibility and clean code. Easily add new payment gateways using the strategy pattern.

## 🚀 Setup Instructions

1. **Clone the repository**
   ```bash
   git clone git@github.com:ahmedsayedabdelsalam/tocaan-task.git
   cd tocaan-task
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   # Edit .env for JWT, and payment gateway configs
   php artisan key:generate
   php artisan jwt:secret
   ```

4. **Database**
   ```bash
   php artisan migrate
   ```

5. **Run the application**
   - With Laravel Herd:  
     Visit [http://tocaan-task.test](http://tocaan-task.test)
   - Or use `php artisan serve`

6. **Run tests**
   ```bash
   php artisan test
   ```

---

## 🧩 Payment Gateway Extensibility

- Payment gateways use the **Strategy Pattern**.
- To add a new gateway:
  1. Create a new class in `app/Services/Payments/Gateways/` implementing `PaymentGateway`.
  2. Register the gateway in `config/payments.php`.
  3. Add any required config to `.env` and reference it in `config/payments.php`.
  4. That's it! The system will route payments to the new gateway based on the `method` field.

**Example:**
```php
// config/payments.php
return [
    'gateways' => [
        'credit_card' => \App\Services\Payments\Gateways\CreditCardGateway::class,
        'paypal' => \App\Services\Payments\Gateways\PaypalGateway::class,
        // Add new gateway here
        'stripe' => \App\Services\Payments\Gateways\StripeGateway::class,
    ],
];
```

---

## 🔐 Authentication

- All order and payment endpoints require JWT authentication.
- Register and login to receive a token.

---

## 📚 API Documentation

- Import the provided Postman collection: `https://lively-rocket-295541.postman.co/workspace/My-Workspace~225c839a-5a96-4c16-8c25-7640721d1497/collection/9379426-20e5a5d6-2158-4200-b200-ce969a42f102?action=share&creator=9379426&active-environment=9379426-1bd267df-6cca-41c4-a118-de2f1daecc09`
- Endpoints are grouped by:
  - **Auth**: Register, Login, Logout, Me
  - **Orders**: CRUD, filter by status, pagination
  - **Payments**: Process, list, view by order

---

## 🧪 Testing

- Run all tests: `php artisan test`
- Feature tests cover:
  - Order creation, update, delete (with business rules)
  - Payment processing (all gateways, business rules)
  - Authentication flows
- Unit tests cover:
  - Order total calculation

---

## 📝 Notes & Assumptions

- Only confirmed orders can be paid.
- Orders with payments cannot be deleted.
- Payment gateway configs are managed via `.env` and `config/payments.php`.

---

## 📂 Project Structure

- `app/Http/Controllers/API/` - API controllers
- `app/Services/Payments/Gateways/` - Payment gateway strategies
- `app/Data/` - Data transfer objects
- `tests/Feature/API/` - Feature tests
- `tests/Unit/` - Unit tests

---

## 🏁 How to Add a New Payment Gateway

1. Create a new gateway class in `app/Services/Payments/Gateways/`.
2. Implement the `PaymentGatewayInterface`.
3. Register the gateway in `config/payments.php`.
4. Add any required config to `.env`.
5. Done! Payments with the new method will use your gateway.

---

## 🛠️ Contact & Support

For questions, open an issue or contact the maintainer.
