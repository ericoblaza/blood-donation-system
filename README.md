# Blood Donation MVP (custom PHP MVC)

Custom PHP MVC framework and blood donation MVP for SP ELEC 2A. Not Laravel/Symfony.

## Requirements

- PHP **8.3+** (`composer.json`; school **erico** bundle uses PHP 8.4)
- MySQL
- Composer 2.x
- Apache **httpd** from the school **erico** stack (port **8080** on this PC)

## Install

1. Project path: `C:\Users\Erico\Documents\my-mvc-framework` (erico `httpd` DocumentRoot: `C:\Users\Erico\Documents`).
2. In the project root:

```powershell
$env:Path = "C:\Users\Erico\Downloads\erico\php;$env:Path"
composer install
```

3. Import `database.sql` into MySQL.
4. Edit `config/database.php` if needed.

## Run Apache (erico)

```cmd
"C:\Users\Erico\Downloads\erico\httpd\Apache24\bin\httpd.exe" -d "C:/Users/Erico/Downloads/erico/httpd/Apache24"
```

If port 8080 is already in use, Apache is already running. Restart Apache after changing `httpd.conf`. Open:

`http://localhost:8080/my-mvc-framework/public/`

Stop: `taskkill /F /IM httpd.exe`

## Routes

| Method | Path | Controller | Action |
|--------|------|------------|--------|
| GET | `/` | HomeController | index → redirect login |
| GET | `/dashboard` | HomeController | dashboard |
| GET/POST | `/register`, `/login`, `/logout` | AuthController | auth |
| GET/POST | `/requests`, `/requests/create` | BloodRequestController | create / store |
| GET | `/requests/{id}`, `/requests/{id}/edit` | BloodRequestController | show / edit form |
| POST | `/requests/{id}/update`, `/requests/{id}/delete` | BloodRequestController | update / delete |
| GET | `/requests/history`, `/requests/responses` | BloodRequestController | history / responses |
| POST | `/requests/accept`, `/requests/decline` | BloodRequestController | decisions |

## MVP / CRUD (summary)

- **Users:** register, login (create + session read).
- **Blood requests:** create, list, show by id, edit, delete, history; status update on accept; contact name and phone stored on each request.

## Defense

See `REVIEWER_DEFENSE.md` (Parts A–C aligned with the course defense PDF).

## Layout

`public/index.php` (front controller), `routes/web.php`, `core/` (Http, Database, View, Container, Auth, Application), `app/Controllers`, `app/Models`, `app/Views`, `config/`, `database.sql`.
