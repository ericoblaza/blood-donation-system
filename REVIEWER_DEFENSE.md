# Custom PHP MVC — Reviewer / panel defense (SP ELEC 2A)

Blood donation MVP on a custom PHP MVC framework. Paste **Part B** into Word if a short written defense is required.

---

## PART A — SOLID evidence (short notes per core class)

### How to say SOLID in one defense sentence

Our framework separates routing from HTTP details from business logic. Controllers orchestrate. Models own persistence. Auth guards enforce policy. Each piece has one job (SRP). We can add routes without rewriting dispatch (OCP at the routing layer). Controllers use models and, for blood requests, a repository interface instead of raw SQL in the controller (DIP).

### `public/index.php` (front controller)

**Responsibility:** Bootstrapping only — session, constants (`BASE_PATH`, `APP_BASE_URL`), load DB helper, Composer autoload, `Application::configure()`, load `routes/web.php`, dispatch.

**SOLID:** SRP for wiring only. DIP toward `Application`, `Request`, and `Router`, not specific controllers.

### `routes/web.php`

**Responsibility:** Declarative mapping — which URL + method calls which controller action.

**SOLID:** SRP for route definitions only. OCP when new features add routes without editing router internals.

### `Core\Http\Router`

**Responsibility:** Match HTTP method + normalized path to a handler (including `/requests/{id}`); invoke controller action or return 404/500.

**SOLID:** SRP for routing/dispatch only. OCP for new routes. ISP: handlers are callable or `[Class, method]`.

### `Core\Http\Request`

**Responsibility:** Read-only view of incoming HTTP data — method, URI/path, input, route parameters.

**SOLID:** SRP so controllers do not read `$_SERVER` / `$_POST` everywhere.

### `Core\Http\Response`

**Responsibility:** Outgoing HTTP — status, headers, body, redirects.

**SOLID:** SRP for response shape and navigation.

### `Core\Auth`

**Responsibility:** Session auth helper — logged-in check and redirect guests to login.

**SOLID:** SRP for access policy only; no SQL.

### `Core\Database\EloquentBootstrap` + `config/database.php`

**Responsibility:** Boot **Eloquent ORM** (Illuminate Database via Capsule) from `config/database.php` on each request (`public/index.php` calls `boot()`).

**SOLID:** SRP for database bootstrap. Models use ORM, not hardcoded DSN strings in application code. PDO still runs under Eloquent.

### `Core\View\Engine`

**Responsibility:** Load templates under `app/Views` and pass controller data into the view.

**SOLID:** SRP for presentation loading; controllers choose view + data.

### `Core\Application` and `Core\Container\Container`

**Responsibility:** Register `BloodRequestRepositoryInterface` singleton and resolve controllers from the container.

**SOLID:** DIP for blood-request persistence behind an interface.

### `App\Contracts\BloodRequestRepositoryInterface` and `App\Repositories\BloodRequestRepository`

**Responsibility:** Contract for blood-request persistence; implementation delegates to `BloodRequest` model.

**SOLID:** DIP and ISP — controller depends on the interface, not concrete SQL details.

### Controllers (`App\Controllers\*`)

**Examples:** `AuthController`, `HomeController`, `BloodRequestController`

**Responsibility:** One HTTP use case per action — read `Request`, call models (or repository), set session or redirect, render view through `Engine`.

**SOLID:** SRP for orchestration; SQL and HTML stay out of the same file.

### Models (`App\Models\*`)

**User, BloodRequest, BloodRequestResponse**

**Responsibility:** Eloquent ORM models — table mapping, query builder, relationships (`hasMany`, `belongsTo`, `updateOrCreate`).

**SOLID:** SRP per entity. Base `App\Models\Model` extends `Illuminate\Database\Eloquent\Model`.

### Views (`app/Views/*.php`)

**Responsibility:** Presentation — HTML and `htmlspecialchars` on user data.

**SOLID:** SRP for display only.

---

## PART B — Defense artifact (about 1–2 pages for Word)

**Title:** Custom PHP MVC — Architecture and security

**1. Problem and goal**  
Small blood donation web app on a custom MVC-style PHP framework: routing, HTTP abstraction, controllers, models, and views — structured software, not one large script.

**2. Request lifecycle**  
Every request hits `public/index.php`. Session and paths are set, Composer autoload runs, **EloquentBootstrap** boots the ORM, `Application` + routes load, `Request` is built, `Router` dispatch matches method + path, the controller validates and coordinates models/repository/session, then `Engine` renders HTML or `Response` redirects.

**3. MVC responsibilities**  
Model: MySQL through **Eloquent ORM** (parameterized queries). View: HTML with escaped output. Controller: validation, redirects, repository/models + session.

**4. Authentication**  
Login uses `password_verify`; session stores id and email. `Core\Auth::requireUser()` blocks protected pages on the server.

**5. Domain features (MVP)**  
Register and login; blood request create, list, detail, edit, delete, and history; contact name and phone on each request; donor accept/decline with server checks; requester response list.

**6. Engineering trade-offs**  
Pros: clear request path, PSR-4, parameterized routes, view engine, **Eloquent ORM**, session auth, repository interface for blood requests. Cons / future work: middleware pipeline, CSRF, more repository interfaces, environment-based production config.

---

## PART C — Q&A script (oral defense)

**MVC?** Model = database; View = HTML; Controller = HTTP input, validation, models, view or redirect.

**Front controller?** All requests through `public/index.php`.

**Router vs many PHP files?** Central map in `routes/web.php`.

**Request and Response?** Request = incoming data; Response = status, headers, redirects.

**Route parameters?** Patterns like `/requests/{id}`; controller reads id with `$request->route('id')`.

**SQL injection?** Eloquent uses parameterized queries; no user input concatenated into SQL strings.

**Passwords?** `password_hash` / `password_verify`.

**Authentication?** Session after login; protected actions call `requireUser()`.

**Middleware?** `app/Middleware/AuthMiddleware` runs on protected routes (declared in `routes/web.php`) before the controller. It calls `Core\Auth::requireUser()`. Public routes: `/`, `/login`, `/register`.

**ORM?** **Eloquent** (`illuminate/database` ^11) bootstrapped in `EloquentBootstrap`. Models extend `App\Models\Model`. Repository still wraps blood-request access for DIP.

**CRUD in this MVP?** Users: register/login. Blood requests: create, read (list/detail/history), update (edit open requests), delete (own open requests). Status changes on accept.

**Why blood donation instead of the sample blog?** The handout lists sample MVPs; this project is a custom domain with the same MVC, routes, forms, validation, and database-through-models pattern.

**Repository interface?** `BloodRequestController` depends on `BloodRequestRepositoryInterface`; `Application` binds the implementation in the container (DIP).

**PSR-4?** Composer autoload maps `Core\` and `App\`; class files are not manually required outside the front controller bootstrap.

**SOLID?** SRP across router, HTTP, controller, model, view; OCP via new routes; DIP via repository interface (not direct SQL in controllers).

**Improve next?** Middleware, CSRF, more repository interfaces, migrations/seeders, environment-based config for production.

**PHP requirements for ORM?** Enable `ext-mbstring` in `php.ini` (Eloquent needs `mb_split`).
