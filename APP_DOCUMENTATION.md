
# Blood Bank Request Management System - MVC Framework Documentation

## Table of Contents
1. [File Review & Purposes](#file-review--purposes)
2. [ORM (Eloquent) — How Database Access Works](#orm-eloquent--how-database-access-works)
3. [SOLID Principles Analysis](#solid-principles-analysis)
4. [Application Flow](#application-flow)
5. [Q&A for Defense Preparation](#qa-for-defense-preparation)

---

## File Review & Purposes

### Core Framework Files

#### 1. **public/index.php** - Front Controller
**Purpose:**
- Acts as the main door of your application
- Every request from the browser comes through this one file
- Sets up everything the app needs to work (loads helpers, config, database, routes)
- Directs the request to the right controller to handle it
- Think of it like the receptionist at a hotel who greets every guest and directs them

**Key Responsibilities:**
- Start the session (remember who's logged in)
- Define where the app is located on the server
- Load Composer autoloader (`vendor/autoload.php`)
- **Boot Eloquent ORM** via `EloquentBootstrap::boot()` (database layer)
- Create Application + load routes
- Send the request to the router to find the right handler

---

#### 2. **core/Application.php** - Application Bootstrap
**Purpose:**
- Sets up everything the app needs to run
- Creates the "container" (the smart factory we discussed earlier)
- Tells the container how to build objects (database, repositories, controllers)
- Manages the router that directs requests
- Think of it as the app's "setup script" that prepares all the tools

**Key Responsibilities:**
- Create and configure the container
- Register services (tell the container how to build repositories, etc.)
- Set up the router with knowledge of all routes
- Get the app ready to handle requests

---

#### 3. **core/Http/Router.php** - Request Routing
**Purpose:**
- Receives a URL request like `/requests/5`
- Looks at all registered routes to find a match
- Pulls out the parameters (like the `5` from `/requests/{id}`)
- Calls the right controller method to handle it
- Like a mail sorter who reads an address and delivers it to the right person

**Key Methods:**
- `get()`, `post()` - Register a URL pattern (optional 3rd argument: middleware classes)
- `dispatch()` - Match route, run **middleware pipeline**, then controller
- `runPipeline()` - Chain middleware before the handler runs
- `resolve()` - Extract the values from the URL (like {id})
- `normalizePath()` - Clean up the path so `/requests` and `/requests/` are treated the same

---

#### 4. **core/Http/Request.php** - HTTP Request Abstraction
**Purpose:**
- Wraps up all the incoming request data in a neat package
- Provides clean methods to access what the user sent (method, path, form data)
- Handles tricky situations like when the app is in a subdirectory
- Makes it easy for controllers to ask "what did the user send?"
- Like a receipt that shows what the customer sent to the server

**Key Methods:**
- `method()` - What type of request? (GET, POST, etc.)
- `path()` - What URL path did they request?
- `input()` - What form data did they send? (from POST or GET)
- `route()` - What are the values from the URL? (like {id} = 5)

---

#### 5. **core/Http/Response.php** - HTTP Response Abstraction
**Purpose:**
- Packages up what the server is sending back to the browser
- Sets status codes (200 = success, 404 = not found, etc.)
- Adds headers (extra information the browser needs)
- Redirects users to different pages
- Like a sealed envelope with the response written inside

**Key Methods:**
- `setStatusCode()` - Tell the browser what happened (200 = OK, 404 = Not Found, etc.)
- `setHeader()` - Add extra information for the browser
- `send()` - Actually send the response to the browser
- `redirect()` - Send user to a different page

**Difference between Request and Response**
**Request represents the incoming HTTP message from the client.**

It reads data from $_SERVER, $_GET, $_POST
It provides the request method, URI/path, route params, input values
It is used by controllers and router logic to decide what to do

**Response represents the outgoing HTTP message back to the client.**

It usually sets headers, status codes, redirects, and body content
It is used to send the result of controller actions to the browser
In short

Request = what the client sent
Response = what the server sends back

---

#### 6. **core/View/Engine.php** - View Rendering Engine
**Purpose:**
- Takes a view name and data, then generates HTML to show the user
- Finds the HTML template file that matches the view name
- Makes the data available inside the template as variables
- Renders the final HTML page
- Like a printing press that takes a template and ink (data) and produces a printed page

**Key Methods:**
- `render()` - Take a view name and data, load the template file, and output the HTML

---

#### 7. **core/Auth.php** - Authentication Helper
**Purpose:**
- Session helpers used by **AuthMiddleware** and login logic
- `check()` — is `$_SESSION['user']` set?
- `requireUser()` — redirect guests to `/login` (called from middleware, not controllers)

**Key Methods:**
- `check()` - Is a user currently logged in?
- `requireUser()` - Redirect to login if guest (used by `AuthMiddleware`)

---

#### 8. **core/Container/Container.php** - Dependency Injection Container
**Purpose:**
- Acts as a smart factory for creating objects and wiring their dependencies
- Keeps object creation code in one place instead of spread across the app
- Reuses shared services when needed and creates new objects when requested
- Automatically inspects constructor arguments to provide required dependencies

**Why it matters:**
- Controllers and services do not need to build their own dependencies manually
- The container makes code easier to read, test, and change
- If a class needs a repository or database connection, the container supplies it automatically

**Key Methods:**
- `bind()` - Register a factory binding that returns a new instance each time
- `singleton()` - Register a single shared instance that is reused throughout the app
- `resolve()` - Create an object and inject its constructor dependencies
- `resolveParameter()` - Decide how to fill one constructor argument when building a class

**Simple analogy:**
- `bind()` is like ordering a new item from a menu each time
- `singleton()` is like using the same water bottle again and again
- `resolve()` is like the chef preparing the full meal using the recipe
- `resolveParameter()` is like the chef choosing each ingredient needed for the recipe

---

#### 9. **core/Database/EloquentBootstrap.php** - ORM Bootstrap
**Purpose:**
- Starts **Eloquent ORM** (from `illuminate/database` package) once per request
- Reads `config/database.php` and configures MySQL connection
- Uses Laravel's **Capsule** manager so Eloquent works outside full Laravel
- Like turning on the database "engine" before any model can run

**Key Methods:**
- `boot()` - Load config, create Capsule, `setAsGlobal()`, `bootEloquent()` (runs only once)

**Note:** Eloquent still uses **PDO underneath** — you don't write raw SQL in models anymore; the ORM generates safe queries.

---

#### 10. **app/Models/Model.php** - Base Eloquent Model
**Purpose:**
- All app models (`User`, `BloodRequest`, …) extend this class
- Extends `Illuminate\Database\Eloquent\Model`
- Shared starting point for table mapping, fillable fields, relationships

---

### Application Controllers

#### 11. **app/Controllers/AuthController.php** - Authentication Controller
**Purpose:**
- Handles user registration, login, and logout
- Validates registration and login input
- Manages user sessions
- Prevents duplicate email registrations

**Methods:**
- `showRegister()` - Display registration form
- `register()` - Process registration with validation
- `showLogin()` - Display login form
- `login()` - Process login with password verification
- `logout()` - Clear session and destroy cookies

**Validations:**
- Name, email, password required
- Email format validation
- Password length (min 6 characters)
- Password confirmation matching
- Unique email checking
- Secure password hashing

**ORM usage:**
- `User::findByEmail($email)` — Eloquent query instead of manual SQL
- `User::query()->create([...])` — insert new user via ORM

---

#### 12. **app/Controllers/HomeController.php** - Public Pages Controller
**Purpose:**
- Handles public and authenticated home pages
- Index redirects to login for unauthenticated users
- Dashboard displays for logged-in users

**Methods:**
- `index()` - Redirect to login
- `dashboard()` - Show user dashboard (protected)

---

#### 13. **app/Controllers/BloodRequestController.php** - Blood Request Management
**Purpose:**
- Manages CRUD operations for blood requests
- Handles donor acceptance/decline of requests
- Provides request history and response tracking
- Validates blood request input

**Core Methods:**
- `index()` - List all open blood requests
- `showCreate()` - Display request creation form
- `store()` - Create new blood request with validation
- `show()` - Display single request details
- `showEdit()` - Display edit form for own requests
- `update()` - Update request (owner only, open status only)
- `destroy()` - Delete request (owner only, open status only)

**Donor Response Methods:**
- `accept()` - Accept blood request (changes status to fulfilled)
- `decline()` - Decline blood request
- `requesterResponses()` - View donor responses to own requests
- `history()` - View own blood requests

**Helper Methods:**
- `findOwnedOpenRequest()` - Security check for ownership and status
- `validatedRequestInput()` - Validates blood type, city, units, contact info

**Note:** Login protection is handled by **middleware** on routes, not `Auth::requireUser()` inside these controllers.

---

### Application Middleware

#### 14. **app/Middleware/MiddlewareInterface.php** - Middleware Contract
**Purpose:**
- Defines the rule every middleware must follow
- One method: `handle(Request $request, callable $next)`

**Why it exists:**
- Router can run any class that implements this interface (Open/Closed Principle)
- Same pattern as Laravel-style middleware

---

#### 15. **app/Middleware/AuthMiddleware.php** - Auth Middleware
**Purpose:**
- Runs **before** protected controllers
- Calls `Auth::requireUser()` — guest → redirect login; logged in → `$next($request)` continues to controller

**Used on routes in `routes/web.php`:**
```php
$auth = [AuthMiddleware::class];
$router->get('/dashboard', [HomeController::class, 'dashboard'], $auth);
```

**Public routes (no middleware):** `/`, `/login`, `/register`

---

### Application Models

#### 16. **app/Models/User.php** - User Model (Eloquent ORM)
**Purpose:**
- Maps to `users` table
- User lookup and registration via ORM (no hand-written SQL)

**Eloquent features used:**
- `$table`, `$fillable`, `$hidden` (hide `password_hash` in JSON/array output)
- `UPDATED_AT = null` (table has `created_at` only)

**Methods:**
- `findByEmail()` — `static::query()->where('email', $email)->first()`

---

#### 17. **app/Models/BloodRequest.php** - Blood Request Model (Eloquent ORM)
**Purpose:**
- Maps to `blood_requests` table
- CRUD + status updates via query builder / Eloquent

**Relationships:**
- `requester()` — `belongsTo(User::class)`
- `responses()` — `hasMany(BloodRequestResponse::class)`

**Static methods (used by repository):**
- `createRequest()`, `findAllOpen()`, `findRequestById()`, `findAllByRequester()`
- `updateRequestStatus()`, `updateRequest()`, `deleteRequestById()` (deletes related responses first)

---

#### 18. **app/Models/BloodRequestResponse.php** - Response Model (Eloquent ORM)
**Purpose:**
- Maps to `blood_request_responses` (composite key: `request_id` + `donor_user_id`)
- Donor accept/decline tracking

**Relationships:**
- `request()` — `belongsTo(BloodRequest::class)`
- `donor()` — `belongsTo(User::class)`

**Methods:**
- `upsertDecision()` — `updateOrCreate([...], ['decision' => ...])`
- `findAllByDonor()` — returns `request_id => decision` map
- `findForRequester()` — join query for requester dashboard (blood request + donor user info)

---

### Application Contracts/Interfaces

#### 19. **app/Contracts/BloodRequestRepositoryInterface.php** - Repository Interface
**Purpose:**
- Defines contract for blood request data access
- Enables loose coupling between controller and repository
- Supports dependency injection and testing

**Methods:**
- `create()`, `findAllOpen()`, `findById()`, `findAllByRequester()`
- `updateStatus()`, `update()`, `deleteById()`

---

### Application Repositories

#### 20. **app/Repositories/BloodRequestRepository.php** - Blood Request Repository
**Purpose:**
- Implements BloodRequestRepositoryInterface
- Wraps **Eloquent** `BloodRequest` model static methods
- Decouples controller from how data is stored

**Implementation:**
- No PDO in constructor — calls `BloodRequest::createRequest()`, `BloodRequest::findAllOpen()`, etc.
- Controller still depends on **interface**, not concrete SQL or model details

---

### Configuration Files

#### 21. **routes/web.php** - Route Definitions
**Purpose:**
- Defines all application routes (URL → Controller@Action mappings)
- Registers GET and POST routes with optional **middleware** (3rd argument)
- Supports dynamic routes with parameters

**Routes:**
- **Public:** register, login, `/` (no middleware)
- **Protected (`AuthMiddleware`):** dashboard, all `/requests*`, logout

---

#### 22. **config/database.php** - Database Credentials
**Purpose:**
- Stores database connection configuration (host, port, database, username, password, charset)
- Read by `EloquentBootstrap::boot()` to configure the ORM

---

#### 23. **composer.json** - Dependencies
**Purpose:**
- Declares **`illuminate/database`** (^11) — Eloquent ORM without full Laravel
- PSR-4 autoload for `App\` and `Core\`

**Install:** `composer install` (requires PHP **mbstring** extension for Eloquent)

---

## ORM (Eloquent) — How Database Access Works

### What changed from raw PDO

| Before (PDO) | After (Eloquent ORM) |
|--------------|----------------------|
| `$pdo->prepare('SELECT ...')` | `User::query()->where(...)->first()` |
| `$stmt->execute(['email' => $email])` | Bound parameters handled by ORM |
| Arrays from `fetch(PDO::FETCH_ASSOC)` | Model objects (`$user->email`) or `toArray()` |
| Manual JOIN SQL strings | Query builder + `join()` or relationships |
| `new User(db())` in controllers | `User::findByEmail()` static calls |

### Layer stack

```
Controller / Repository
        ↓
Eloquent Model (User, BloodRequest, …)
        ↓
Illuminate Query Builder
        ↓
PDO (inside Capsule)
        ↓
MySQL
```

### Boot sequence (every request)

1. `public/index.php` loads `vendor/autoload.php`
2. `EloquentBootstrap::boot()` runs
3. Reads `config/database.php`
4. Creates `Capsule`, adds MySQL connection, `bootEloquent()`
5. Models can now run: `User::query()`, `BloodRequest::create([...])`, etc.

### Example: login (AuthController)

```php
$user = User::findByEmail($email);

if ($user === null || !password_verify($password, (string) $user->password_hash)) {
    // invalid
}

$_SESSION['user'] = ['id' => (int) $user->id, 'email' => $user->email];
```

Eloquent loads the row; you use object properties instead of `$row['email']`.

### Example: create blood request

```php
BloodRequest::query()->create([
    'requester_user_id' => $requesterUserId,
    'blood_type' => $bloodType,
    'status' => 'open',
    // ...
]);
```

ORM builds parameterized `INSERT` — still safe from SQL injection.

### Example: relationships

```php
// On BloodRequest model
public function responses(): HasMany {
    return $this->hasMany(BloodRequestResponse::class, 'request_id');
}

// Delete request + child responses
$request->responses()->delete();
$request->delete();
```

### Repository + ORM together

- **Controller** → `BloodRequestRepositoryInterface` (abstraction)
- **Repository** → `BloodRequest::findAllOpen()` etc. (Eloquent)
- **Model** → talks to database

You get **ORM convenience** and **Repository/DI** for defense (SOLID).

### PHP requirements for ORM

- **`ext-mbstring`** — required by Illuminate Support (`mb_split`); enable in `php.ini` if login crashes
- **`pdo_mysql`** — MySQL driver (usually enabled with XAMPP/erico PHP)

---

## SOLID Principles Analysis

### 1. **S - Single Responsibility Principle**

✅ **Applied Throughout:**

**What it means:** Each class should have ONE job and do it well. Don't mix different responsibilities.

- **Router**: Only routes requests (finds the right handler) - nothing else
- **Request**: Only holds incoming data - doesn't route or respond
- **Response**: Only sends data back - doesn't route or render views
- **AuthController**: Only handles login/logout logic - not blood requests
- **BloodRequestController**: Only handles blood request logic - not authentication
- **Models**: Each model handles ONE type of data (User, BloodRequest, etc.)

**Why it helps:**
- If something breaks, it's easier to find and fix 
- Each class is simpler to understand
- Each class is easier to test

**Example:**
```php
// GOOD - Router only routes
class Router {
    public function dispatch(Request $request): void { /* routing logic */ }
}

// GOOD - Request only holds data
class Request {
    public function method(): string { /* get HTTP method */ }
}

// BAD - Router does too many things
class Router {
    public function dispatch() { /* routing */ }
    public function renderView() { /* rendering */ }  // Wrong!
    public function saveToDatabase() { /* database */ } // Wrong!
}
```

---

### 2. **O - Open/Closed Principle**

✅ **Applied:**

**What it means:** Classes should be OPEN for extension (add new features) but CLOSED for modification (don't change existing code).

**In this app:**
- Can create new repository types without changing the controller
- Can add new routes without modifying the router's core logic
- Can create new controllers without touching the Application class
- Use inheritance and interfaces instead of changing existing code

**Why it helps:**
- New features don't break existing code
- Less risk of introducing bugs
- Other developers can add features without touching your code

**Example:**
```php
// CLOSED - This interface doesn't change
interface BloodRequestRepositoryInterface {
    public function create(...): void;
    public function findAllOpen(): array;
}

// OPEN - We can add new implementations without changing the interface
class BloodRequestRepository implements BloodRequestRepositoryInterface { 
    // Original implementation
}

class CachedBloodRequestRepository implements BloodRequestRepositoryInterface { 
    // New cached version - no changes needed to the interface!
}

// The controller uses the interface, so it works with BOTH implementations
public function __construct(BloodRequestRepositoryInterface $repo) {
    // Works with BloodRequestRepository OR CachedBloodRequestRepository
    $this->repo = $repo;
}
```

---

### 3. **L - Liskov Substitution Principle**

✅ **Applied:**

**What it means:** If class B is a type of class A, you should be able to replace A with B without breaking the code.

**In this app:**
- `BloodRequestRepository` implements `BloodRequestRepositoryInterface` correctly
- A controller accepts the interface, so it can use ANY implementation
- Swapping implementations doesn't break anything

**Why it helps:**
- Code that uses an interface doesn't need to care which implementation it gets
- Easy to test by swapping with fake/mock implementations
- Easy to add new implementations later

**Example:**
```php
// Controller depends on interface
public function __construct(
    private readonly BloodRequestRepositoryInterface $bloodRequests
) {}

// Both work identically - no code changes needed
$repo1 = new BloodRequestRepository();
$repo2 = new CachedBloodRequestRepository($repo1, $cache);

$controller = new BloodRequestController($repo1);  // Works!
$controller = new BloodRequestController($repo2);  // Also works!
```

---

### 4. **I - Interface Segregation Principle**

✅ **Applied:**

**What it means:** Don't force classes to depend on methods they don't use. Keep interfaces small and focused.

**In this app:**
- `BloodRequestRepositoryInterface` only has blood request methods
- It doesn't include user methods or unrelated operations
- Classes only depend on the methods they actually need

**Why it helps:**
- Interfaces are simpler to understand
- Classes don't have to implement unnecessary methods
- Code is cleaner and more focused

**Example:**
```php
// GOOD - Only blood request operations
interface BloodRequestRepositoryInterface {
    public function create(...): void;
    public function findAllOpen(): array;
    public function updateStatus(...): void;
    // Nothing else - focused and simple
}

// BAD - Too many unrelated methods
interface RepositoryInterface {
    // Blood request methods
    public function createBloodRequest(...): void;
    
    // User methods (not needed here!)
    public function createUser(...): void;
    public function findUser(...): void;
    
    // Payment methods (not needed here!)
    public function processPayment(...): void;
}

// Controller only needs what it uses
public function __construct(BloodRequestRepositoryInterface $repo) {
    // Only sees blood request methods
    $repo->findAllOpen();
}
```

---

### 5. **D - Dependency Inversion Principle**

✅ **Applied Throughout:**

**What it means:** High-level code should NOT depend on low-level code. Both should depend on abstractions (interfaces).

**In other words:** Controllers shouldn't directly use Models. They should use interfaces/repositories instead.

**In this app:**
- Controllers depend on interfaces (like `BloodRequestRepositoryInterface`)
- Repositories (low-level code) also implement those interfaces
- The Application container wires them together
- If you need to change how data is stored, only change the repository - controller stays the same!

**Why it helps:**
- Easy to swap implementations without changing controllers
- Testing is easier (use fake repositories)
- Code is flexible for future changes

**Example:**
```php
// GOOD - Controller depends on interface
public function __construct(
    private readonly BloodRequestRepositoryInterface $bloodRequests
) {}

// GOOD - Application configures the wiring
$container->bind(
    BloodRequestRepositoryInterface::class,
    fn($container) => new BloodRequestRepository(...)
);

// BAD - Controller depends on concrete class
public function __construct(
    private readonly BloodRequestRepository $bloodRequests
) {}
// Now controller is tightly coupled - hard to change!
```

---

## Application Flow

### Request Lifecycle

```
Browser Request
    ↓
public/index.php (Front Controller)
    ↓
├─ session_start()
├─ Load Composer autoloader
├─ EloquentBootstrap::boot()  ← ORM + DB connection
├─ Create Application & Container
├─ Register service bindings
│  └─ BloodRequestRepository singleton (interface → implementation)
├─ Load routes/web.php
└─ Dispatch Request
    ↓
Router::dispatch(Request)
    ↓
├─ Parse HTTP method & path
├─ Resolve route pattern match
├─ Extract route parameters
└─ Handle execution
    ↓
    ├─ [IF closure] → Execute closure
    │
    └─ [IF controller@action] → 
        ├─ Run middleware pipeline (e.g. AuthMiddleware on protected routes)
        │  └─ Guest? redirect /login and stop
        │  └─ OK? call $next($request)
        ├─ Resolve controller via Container
        │  └─ Inject BloodRequestRepository
        └─ Call controller action method
            ↓
            └─ [Controller Action]
                ├─ Validate input
                ├─ Call repository methods
                ├─ Query/update database
                └─ Render view or redirect
                    ↓
                    └─ Response sent to browser
```

### Authentication Flow

```
User submits login form
    ↓
POST /login → AuthController::login()
    ↓
├─ Validate email format
├─ Validate password not empty
├─ User::findByEmail()  (Eloquent ORM)
│  └─ SELECT users WHERE email = ? (parameterized, via ORM)
├─ password_verify() against hash
├─ [IF valid] → Set $_SESSION['user']
│  └─ Redirect to /dashboard
└─ [IF invalid] → Re-render login with errors
```

### Blood Request Management Flow

```
Authenticated user creates request
    ↓
POST /requests → BloodRequestController::store()
    ↓
├─ AuthMiddleware [verify login via route]
├─ Validate input
│  ├─ Blood type in allowed list
│  ├─ City not empty
│  ├─ Units >= 1
│  └─ Contact info provided
├─ Repository::create()
│  └─ BloodRequest::createRequest()  (Eloquent)
│     └─ INSERT into blood_requests (ORM-generated SQL)
└─ Redirect to /requests
```

### Donor Response Flow

```
Donor views open requests
    ↓
GET /requests → BloodRequestController::index()
    ↓
├─ Get all open blood requests
├─ Get current donor's responses
└─ Render with request list and response status

Donor accepts request
    ↓
POST /requests/accept → BloodRequestController::accept()
    ↓
├─ Validate request exists
├─ Verify not own request
├─ Save decision (accept)
├─ Update request status → fulfilled
└─ Redirect to /requests
```

### Request Lookup/Edit Flow

```
GET /requests/{id}
    ↓
Router extracts {id} parameter
    ↓
BloodRequestController::show()
    ↓
├─ Repository::findById($id)
│  └─ BloodRequest::findRequestById()  (Eloquent)
└─ Render show view with request details

POST /requests/{id}/update [owner only]
    ↓
├─ findOwnedOpenRequest() [security check]
│  ├─ Verify user owns request
│  └─ Verify status is 'open'
├─ Validate input
├─ Repository::update()
│  └─ BloodRequest::updateRequest()  (Eloquent UPDATE)
└─ Redirect to /requests/history
```

---

## Q&A for Defense Preparation

### Architecture & Design Patterns

**Q1: What design pattern is this application built on?**
A: This is an MVC (Model-View-Controller) architecture with:
- **Model**: Database models (User, BloodRequest, BloodRequestResponse) and repositories
- **View**: PHP templates in app/Views rendered by View Engine
- **Controller**: Three controllers (Auth, Home, BloodRequest) handling business logic
- **Additional Patterns**: Dependency Injection, Repository Pattern, **ORM (Eloquent)**, **Middleware**
- **Data access**: Eloquent models + repository interface (not raw PDO in application code)
- **Auth on protected routes**: `AuthMiddleware` in `app/Middleware/` (declared in `routes/web.php`)

---

**Q2: Explain the Dependency Injection Container. Why is it important?**

A: Think of the container as a smart factory that builds objects for you.

**What it does:**
- You tell it: "Here's how to build a repository" → it builds it  
- You ask for a controller → it automatically injects `BloodRequestRepositoryInterface`
- Database connection is handled globally by **EloquentBootstrap** (not injected as PDO anymore)

**In this app:**
- `singleton(BloodRequestRepositoryInterface::class, ...)` → one repository instance per request lifecycle
- Eloquent manages its own connection via Capsule after `boot()`

**Why it's important:**
- Controllers don't need to know HOW to build dependencies, the container does it
- Easy to swap implementations for testing
- All object creation is in one place, easy to manage
- Loose coupling between classes

**Real example from your app:**
```php
// Without container, you'd write:
$repo = new BloodRequestRepository();
$controller = new BloodRequestController($repo);

// With container, the controller just asks:
public function __construct(BloodRequestRepositoryInterface $repo) {
    // Container automatically provides BloodRequestRepository!
}
```

---

**Q3: What is the Repository Pattern and why is it used here?**

A: The Repository Pattern is a middleman between your controller and database.

**Without Repository (BAD):**
```
Controller talks directly to Model
Controller knows HOW to query the database
Controller handles SQL
If database changes, controller code changes too!
```

**With Repository (GOOD):**
```
Controller → Repository → Model → Database
Controller just asks for data
Repository knows HOW to get it
Model talks to the database
Easy to swap repositories without changing controllers
```

**In your app:**
- `BloodRequestController` asks `BloodRequestRepositoryInterface` for data
- `BloodRequestRepository` implements the interface and talks to the `BloodRequest` model
- If you wanted to use caching or a different database, just create a new repository class
- Controllers don't care - they still use the same interface

**Benefits:**
- Controllers stay simple (less code to read)
- Testing is easier (use fake repositories)
- Can swap implementations (database → cache → API)
- All data access code is in one place

---

**Q4: Explain the Front Controller pattern in public/index.php**
A: Single entry point for all requests:
- All HTTP requests → public/index.php (via web server rewrite)
- One place to initialize app, load config, setup services
- All requests flow through same bootstrapping logic
- Router then directs to appropriate handler

---

### SOLID Principles

**Q5: Which SOLID principle does BloodRequestRepositoryInterface demonstrate best?**
A: Dependency Inversion Principle (D):
- Controller depends on interface, not concrete class
- Interface defines contract (what it does)
- Concrete implementation handles how it does it
- Can swap BloodRequestRepository with CachedRepository without changing controller

```php
// Controller depends on abstraction
public function __construct(BloodRequestRepositoryInterface $repo) { ... }
```

---

**Q6: How does Single Responsibility Principle appear in Router vs Request vs Response?**
A: Each class has ONE job:
- **Router**: Only routing (matching URL to handler) - doesn't handle responses or parsing
- **Request**: Only encapsulating incoming HTTP data - doesn't route or respond
- **Response**: Only outgoing HTTP (status, headers, body) - doesn't route or render
- If Router breaks, it's routing issue; if Response breaks, it's response issue
- Easy to fix, maintain, and test each piece independently

---

**Q7: How does the code follow Open/Closed Principle?**
A: Open for extension, closed for modification:
- Can create new Repository implementation without touching BloodRequestController
- Can add new routes to Router without modifying dispatch logic
- Can create new Controller without changing Application
- Inheritance/interface-based extension, not modification

```php
// Closed for modification
class BloodRequestController { /* unchanged */ }

// Open for extension
class CachedBloodRequestRepository implements BloodRequestRepositoryInterface { 
    // New implementation, controller unchanged
}
```

---

### Request Flow & Routing

**Q8: Walk through what happens when user visits /requests/5**
A:
1. Browser sends `GET /requests/5`
2. Web server routes to public/index.php
3. index.php loads Application and routes
4. Router tries to match GET /requests/5
5. Finds matching pattern `/requests/{id}` with handler `[BloodRequestController::class, 'show']`
6. Extracts `{id}` = "5" from URL using regex
7. Container resolves BloodRequestController with dependencies
8. Calls `$controller->show($request)` where request has route param id=5
9. Controller calls `$bloodRequests->findById(5)`
10. Repository queries database
11. Controller renders 'requests/show' view with result
12. Browser displays page

---

**Q9: How does Route parameter extraction work with {id}?**
A: In Router::resolve():
1. Route pattern: `/requests/{id}` 
2. Converts to regex: `/requests/([^/]+)`
3. Matches incoming path: `/requests/5`
4. Extracts captured group: `5`
5. Maps to parameter name: `['id' => '5']`
6. Request object stores these via `setRouteParams()`
7. Controller accesses via `$request->route('id')`

---

**Q10: What happens if user tries to access /requests/xyz (non-numeric)?**
A: 
1. Router regex `/requests/([^/]+)` matches (accepts any non-slash characters)
2. Controller receives `$id = (int) $request->route('id')` → converts "xyz" to 0
3. Code checks: `if ($id < 1)` → true
4. Redirects to /requests list
5. Returns 404-like behavior without full 404 page

---

### Authentication & Security

**Q11: Explain the authentication flow when user logs in**

A: Here's what happens step-by-step:

1. User fills out login form (email and password)
2. User clicks "Log In"
3. Form sends POST request to `/login`
4. `AuthController::login()` method is called
5. Check if email is valid format
6. Check if password is not empty
7. Look up user in database by email
8. Use `password_verify()` to check if password is correct
9. **If password matches:**
   - Store user info in `$_SESSION['user']` (this remembers the login)
   - Redirect user to dashboard
10. **If password doesn't match:**
    - Show error message
    - Show login form again
11. On protected routes, **AuthMiddleware** runs before the controller
    - Calls `Auth::requireUser()` → checks `$_SESSION['user']`
    - If yes → controller runs (dashboard, requests, …)
    - If no → redirect to login

**In simple terms:**
- App checks username and password
- If correct, remembers who you are
- On future requests, checks if it remembers you
- If yes, shows protected content
- If no, shows login page

---

**Q12: Why is password_hash() and password_verify() used instead of md5()?**

A: Imagine two ways to lock a box:

**❌ md5() - Old, broken lock:**
- Very fast to lock and unlock
- Someone can hack it quickly
- If they get the lock pattern, they can break every lock that uses it
- Used to be good, but people found ways to break it

**✅ password_hash() and password_verify() - Modern, strong lock:**
- Slower on purpose (harder to hack)
- Adds random "salt" to each password (like randomizing the lock pattern)
- Uses bcrypt algorithm (mathematically strong)
- Even if hacker gets one lock's pattern, other locks are different

**What happens:**
```php
// Registering
$password = "myPassword123";
$hash = password_hash($password, PASSWORD_BCRYPT);
// Stored in database: some-long-cryptic-string

// Logging in
$inputPassword = "myPassword123";
$correct = password_verify($inputPassword, $storedHash);
// Returns true if matches, false if doesn't
```

**Why password_hash and password_verify:**
- Bcrypt is slow = hackers can't try thousands of passwords per second
- Each password has salt = harder to break multiple passwords
- It's the modern PHP standard
- If a hacker breaks your database, passwords are still safe

---

**Q13: How is SQL injection prevented?**

A: Imagine building a letter. Two ways:

**❌ BAD - String concatenation (Vulnerable):**
```php
$email = $_POST['email']; // User enters: test@test.com'; DROP TABLE users;--
$sql = "SELECT * FROM users WHERE email = '$email'";
// Actual query sent: SELECT * FROM users WHERE email = 'test@test.com'; DROP TABLE users;--'
// This runs DROP TABLE and deletes the entire users table!!!
```

**✅ GOOD - Prepared statements (Safe):**
```php
$email = $_POST['email']; // User enters: test@test.com'; DROP TABLE users;--
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);
// The :email is a placeholder
// PDO sends the SQL structure and data separately
// User input is NEVER interpreted as SQL code
// Actual result: Just looks for that email, no danger
```

**Why prepared statements are safe:**
- SQL code is already "locked in"
- User input is just DATA, not code
- PDO handles escaping automatically
- No matter what the user enters, it can't execute SQL

**In your app (Eloquent ORM):**
```php
User::query()->where('email', $email)->first();
BloodRequest::query()->create(['blood_type' => $bloodType, ...]);
```
Eloquent binds values as **prepared statement parameters** — user input is still not executed as SQL.

**Defense line:** "We use an ORM; it parameterizes queries automatically. We don't concatenate user input into SQL strings."

---

### Business Logic & Validations

**Q14: What validations happen when creating a blood request?**
A: In BloodRequestController::validatedRequestInput():
- Blood type must be in `['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']`
- City required (not empty)
- Units >= 1
- Contact name required
- Contact phone required

If any fail, returns array of errors, re-renders form with old input for user correction

---

**Q15: How does the app prevent users from responding to their own blood requests?**
A: In BloodRequestController::saveDecision():
```php
if ((int) $requestRow['requester_user_id'] === $donorUserId) {
    // User is owner, redirect without saving
    redirect('/requests');
    exit;
}
```
Uses security check to prevent self-response

---

**Q16: What happens when a donor accepts a blood request?**
A:
1. Donor clicks accept on request
2. POST /requests/accept with request_id
3. BloodRequestController::accept() calls saveDecision('accept')
4. Validates request exists and user doesn't own it
5. BloodRequestResponse model upserts decision record
6. Updates blood_request status from 'open' → 'fulfilled'
7. Requester can see responses on /requests/responses page
8. Status 'fulfilled' removes request from open list

---

**Q17: Can requester modify a blood request after it's been fulfilled?**
A: No. In showEdit():
```php
$bloodRequest = $this->findOwnedOpenRequest($request);
```
This checks: ownership AND status === 'open'
If status is 'fulfilled' or 'cancelled', user gets redirected to history
Only open requests can be edited/deleted

---

### Database & Models

**Q18: Explain the relationship between BloodRequest model and BloodRequestRepository**
A:
- **Model (Eloquent)**: Table mapping, queries, relationships (`hasMany`, `belongsTo`)
- **Repository**: Wraps model static methods, implements interface for the controller
- **Benefit**: Controller doesn't know if data comes from Eloquent, cache, or API — only the interface
- **Example**: `BloodRequestRepository::findAllOpen()` calls `BloodRequest::findAllOpen()` internally

---

**Q19: When deleting a blood request, what happens to associated responses?**
A: In `BloodRequest::deleteRequestById()`:
```php
$request = static::query()->find($id);
$request->responses()->delete();  // Eloquent: delete child rows
$request->delete();               // then delete parent
```
Uses the **`responses()` relationship** instead of two hand-written DELETE queries.

---

**Q20: How does upsert work for donor decisions?**
A: In `BloodRequestResponse::upsertDecision()`:
```php
static::query()->updateOrCreate(
    ['request_id' => $requestId, 'donor_user_id' => $donorUserId],
    ['decision' => $decision]
);
```
Eloquent **inserts** if no row exists, **updates** if composite key already exists — same idea as `ON DUPLICATE KEY UPDATE`, but written in ORM syntax.

---

### Views & Rendering

**Q21: How does View Engine rendering work?**
A: In Engine::render('requests/show', $data):
1. Maps dot notation to file: `requests.show` → `app/Views/requests/show.php`
2. Extracts data array into variables: `extract($data)`
3. Requires view file in isolated scope
4. Variables available directly in view: `<?= $bloodRequest ?>`

---

**Q22: What's the difference between app_url() function and relative URLs?**
A: 
- **app_url()**: Handles subdirectory installs
  - If app at /subfolder/public → app_url('/requests') → /subfolder/requests
  - If app at document root → app_url('/requests') → /requests
  - Used for all form actions and redirects
- **Relative URLs**: Don't work well with subdirectories, fragile across page moves

---

### Error Handling

**Q23: How are 404 errors handled?**
A: In Router::dispatch():
```php
[$handler, $params] = $this->resolve($method, $path);

if ($handler === null) {
    http_response_code(404);
    echo '404 Not Found';
    return;
}
```
If no route matches, returns 404 status and simple message

---

**Q24: What happens if a controller class doesn't exist?**
A: In Router::dispatch():
```php
if (!class_exists($class)) {
    http_response_code(500);
    echo "Controller class not found: {$class}";
    return;
}
```
Returns 500 error with explanation

---

### Testing & Maintenance

**Q25: How does the Repository Interface make testing easier?**
A: 
```php
// Create mock for testing
class MockBloodRequestRepository implements BloodRequestRepositoryInterface {
    public function findAllOpen(): array {
        return [/* test data */];
    }
}

// Controller works with mock identical to real
$controller = new BloodRequestController(new MockBloodRequestRepository());
```
No need to touch database during tests

---

**Q26: What would you change if app needed caching?**
A:
```php
// Original
class BloodRequestRepository implements BloodRequestRepositoryInterface { ... }

// Add cached version
class CachedBloodRequestRepository implements BloodRequestRepositoryInterface {
    public function __construct(
        private BloodRequestRepository $repo,
        private Cache $cache
    ) {}
    
    public function findAllOpen(): array {
        return $this->cache->get('open_requests', 
            fn() => $this->repo->findAllOpen()
        );
    }
}

// In Application.php, swap binding
$container->singleton(BloodRequestRepositoryInterface::class,
    fn($c) => new CachedBloodRequestRepository(
        new BloodRequestRepository(),
        $c->get(Cache::class)
    )
);

// Controllers unchanged!
```

---

**Q27: How would you add a new controller feature?**
A:
1. Add route in routes/web.php: `$router->get('/new-feature', [NewController::class, 'show']);`
2. Create NewController with method: `public function show(Request $request)`
3. If needs database, inject repository: `public function __construct(BloodRequestRepositoryInterface $repo)`
4. Create view in app/Views/new-feature.php
5. No changes needed elsewhere

---

### Deployment & Configuration

**Q28: Why is APP_BASE_URL needed?**
A:
- App might be at:
  - `example.com/` (root) → base URL = ''
  - `example.com/blood-bank/public/` (subdirectory) → base URL = '/blood-bank/public'
- All form actions, redirects use app_url() to prefix base URL
- Ensures links work regardless of installation location

---

**Q29: How is database connection configured for different environments?**
A: 
- `config/database.php` returns array with credentials
- `EloquentBootstrap::boot()` reads it once and configures Capsule/Eloquent
- To use a different database: edit `config/database.php` only — no model/controller changes

---

### Performance & Scaling

**Q30: What's the performance bottleneck as app scales?**
A:
- Database queries increase (findAllOpen() queries all open requests)
- Solutions:
  1. Add pagination: limit 20 per page, offset by page
  2. Add caching: cache open requests, invalidate on new request
  3. Add indexes: index on `status`, `requester_user_id`, `created_at`
  4. Archive old requests: move fulfilled/cancelled to archive table

---

**Q31: Why use an ORM (Eloquent) instead of writing all SQL by hand?**
A:
- Less repetitive code (`where`, `create`, `updateOrCreate`)
- **Relationships** express table links clearly (`hasMany`, `belongsTo`)
- Still uses **parameterized queries** (SQL injection safe)
- Matches industry practice (Laravel ecosystem)
- Repository + interface keeps controllers testable

**Under the hood:** Eloquent still uses PDO — ORM is a layer above it, not a replacement for MySQL.

---

### Advanced Questions

**Q32: How does the router handle edge cases in path normalization?**
A: In Request::path():
- Strips base path: `/blood-bank/requests` → `/requests`
- Handles missing leading slash: `requests` → `/requests`
- Handles trailing slash: `/requests/` → `/requests/` (normalizePath removes it)
- Handles query strings: `/requests?sort=date` → `/requests`

---

**Q33: What happens if two routes have conflicting patterns?**
A: Routes registered first take priority:
```php
$router->get('/requests/{id}', ...);  // Matches first
$router->get('/requests/create', ...); // Never reached for /requests/create!
```
**Solution**: Register specific routes before generic ones:
```php
$router->get('/requests/create', ...);  // Specific first
$router->get('/requests/{id}', ...);    // Generic second
```

---

**Q34: How does method_exists() prevent calling non-existent methods?**
A: In Router::dispatch():
```php
if (!method_exists($controller, (string) $action)) {
    http_response_code(500);
    echo "Method not found: {$action}";
    return;
}
```
Prevents calling methods that don't exist on controller

---

**Q35: What's the difference between singleton() and bind() in Container?**

A: They control HOW MANY instances are created:

**singleton() - Create once, reuse forever:**
```php
$container->singleton(
    BloodRequestRepositoryInterface::class,
    fn () => new BloodRequestRepository()
);

$repo1 = $container->get(BloodRequestRepositoryInterface::class);
$repo2 = $container->get(BloodRequestRepositoryInterface::class);
var_dump($repo1 === $repo2); // true - same instance
```

**bind() - Create new one each time:**
```php
// Some service
$container->bind(UserValidator::class, function($container) {
    return new UserValidator();
});

// First request
$validator1 = $container->get(UserValidator::class); // Creates new one

// Second request
$validator2 = $container->get(UserValidator::class); // Creates another new one

// Are they the same object?
var_dump($validator1 === $validator2); // false - different objects!
```

**When to use each:**
- **singleton()** for expensive things (database connection, cache, config)
- **bind()** for regular services that don't need to persist

**In your app:**
- `BloodRequestRepositoryInterface` is registered as **singleton** in `Application::configure()`
- Eloquent Capsule holds **one DB connection** per request after `EloquentBootstrap::boot()`

---

### Best Practices & Design Decisions

**Q36: Why is strict_types=1 declared in every file?**

A: It controls whether PHP is "strict" about data types.

**Without strict_types (lenient):**
```php
function add(int $a, int $b): int {
    return $a + $b;
}

add(5, 3);        // Works - both are ints
add("5", "3");    // Also works! PHP converts strings to ints
// Result: 8
// PHP is being "helpful" but sometimes this causes bugs!
```

**With strict_types=1 (strict):**
```php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

add(5, 3);        // Works - both are ints
add("5", "3");    // ERROR! PHP rejects it
// You MUST pass actual integers
```

**Why declare(strict_types=1) is good:**
- Prevents bugs from unexpected type conversions
- Makes code intention clear ("this must be an int")
- Type errors are caught immediately
- Matches modern PHP best practices

**In your app:**
Every file starts with:
```php
declare(strict_types=1);
```
This means: "PHP, be strict about types in this file"

---

**Q37: Why use `readonly` property modifier?**

A: It prevents a property from being changed after it's set.

**Without readonly (changeable):**
```php
class BloodRequestController {
    public function __construct(private BloodRequestRepositoryInterface $bloodRequests) {}
    // Someone could replace $this->bloodRequests later — surprising bugs
}
```

**With readonly (immutable):**
```php
class BloodRequestController {
    public function __construct(
        private readonly BloodRequestRepositoryInterface $bloodRequests
    ) {}
    // $this->bloodRequests is set once in constructor and never changes
}
```

**Why readonly is good:**
- Property is set once in constructor and never changes
- No surprises - you know the value won't change
- Prevents bugs from accidental modifications
- Makes code intention clear ("this doesn't change")

**In your app:**
```php
public function __construct(
    private readonly BloodRequestRepositoryInterface $bloodRequests
) {}
// This repository will always be the same one given in the constructor
```

---

**Q38: What's the benefit of using interfaces in parameters?**

A: Interfaces let you swap different implementations without changing code.

**Without interface (tightly coupled):**
```php
class BloodRequestController {
    public function __construct(BloodRequestRepository $repo) {}
}

// Stuck with BloodRequestRepository
// If you want to use CachedRepository, change the constructor
// Tests need real database access
```

**With interface (loosely coupled):**
```php
class BloodRequestController {
    public function __construct(BloodRequestRepositoryInterface $repo) {}
}

// Works with ANY implementation!
$controller = new BloodRequestController(new BloodRequestRepository());
$controller = new BloodRequestController(new CachedBloodRequestRepository(...));
$controller = new BloodRequestController(new MockBloodRequestRepository()); // for testing
```

**Benefits of using interfaces:**
- Easy to swap implementations (for testing, caching, different databases)
- Controller doesn't care HOW data is retrieved, just WHAT it gets
- Add new implementations without touching the controller
- Tests can use fake/mock repositories instead of real database

**In your app:**
Controllers depend on `BloodRequestRepositoryInterface`, not concrete `BloodRequestRepository`. This means:
- Easy to add caching layer later
- Easy to test with mock data
- Easy to swap databases
- Controller code never changes

---

**Q39: Why extract() is used in View rendering?**
A:
```php
extract($data, EXTR_SKIP);
require $file;
```
- Converts array to variables: `['email' => 'test@test.com']` → `$email`
- EXTR_SKIP prevents overwriting existing variables
- Cleaner templates than `$data['email']`

---

**Q40: What security consideration for input validation?**
A:
1. **Whitelist validation**: Check against allowed values (blood types)
2. **Type casting**: `(int) $units` ensures integer
3. **Trim**: Remove leading/trailing spaces
4. **Required fields**: Check if not empty
5. **ORM / parameterized queries**: Eloquent binds values — no raw SQL concatenation
6. **Password hashing**: Never store plaintext passwords
7. **Session validation**: Check user ownership before update/delete

---

### ORM-Specific Defense Questions

**Q41: What ORM does this project use?**
A: **Eloquent** from the `illuminate/database` package (Laravel's database layer used standalone via **Capsule**). Not full Laravel — only the ORM inside our custom MVC.

---

**Q42: Where is the ORM bootstrapped?**
A: `public/index.php` calls `Core\Database\EloquentBootstrap::boot()` before `Application::configure()`. That loads `config/database.php` and starts Eloquent globally.

---

**Q43: What is the difference between PDO and ORM in this project?**
A:
- **PDO** = low-level driver; sends SQL to MySQL
- **ORM** = PHP models and query builder; generates parameterized SQL for you
- We **removed** hand-written PDO in models; controllers/repositories use **Eloquent**
- PDO still runs **under** Eloquent — professors may ask "you still use PDO?" → yes, indirectly

---

**Q44: What is `app/Models/Model.php` for?**
A: Base class extending `Illuminate\Database\Eloquent\Model`. All entities (`User`, `BloodRequest`, …) extend it so they share one ORM entry point.

---

**Q45: Why did we remove `config/Connection.php` and `db()`?**
A: After ORM adoption, nothing called `db()` anymore. Database access goes through Eloquent only. One less duplicate connection helper — config lives in `database.php` + `EloquentBootstrap`.

---

**Q46: What PHP extension does Eloquent need that caused a login error?**
A: **`mbstring`** (`extension=mbstring` in `php.ini`). Illuminate's `Str` class uses `mb_split()`. If disabled, login crashes with `Call to undefined function mb_split()`. Enable mbstring in the PHP used by Apache (erico stack: `C:\Users\Erico\Downloads\erico\php\php.ini`), then restart Apache.

---

**Q47: How does middleware work in this project?**
A:
1. Routes in `web.php` pass `[AuthMiddleware::class]` as the 3rd argument on protected URLs.
2. `Router::dispatch()` runs `runPipeline()` before the controller.
3. `AuthMiddleware::handle()` calls `Auth::requireUser()` then `$next($request)`.
4. Controllers no longer call `Auth::requireUser()` — auth is centralized on the route.

**Defense line:** "Middleware is the filter between the router and the controller; our auth middleware matches the course folder structure under `app/Middleware/`."

---

## Conclusion

This Blood Bank Request Management System demonstrates proper MVC architecture with:
- ✅ Clear separation of concerns
- ✅ SOLID principles throughout
- ✅ Dependency injection for flexibility
- ✅ Repository pattern for data abstraction
- ✅ **Eloquent ORM** for database access (course ORM requirement)
- ✅ **Middleware** (`MiddlewareInterface`, `AuthMiddleware`) on protected routes
- ✅ Security best practices (parameterized ORM queries, password hashing)
- ✅ Type safety with strict types
- ✅ Session-based authentication
- ✅ Input validation and error handling

The application is maintainable, testable, and extensible for future features.
