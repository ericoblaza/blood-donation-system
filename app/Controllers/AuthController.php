<?php

declare(strict_types=1);

namespace App\Controllers;

// Auth: register / login / logout — validates input, uses User model + session.
use App\Models\User;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\Engine;

class AuthController
{
    public function showRegister(Request $request): void
    {
        (new Engine())->render('register');
    }

    public function register(Request $request): void
    {
        $name = trim((string) $request->input('name', ''));
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $passwordConfirmation = (string) $request->input('password_confirmation', '');

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Please fill out your name.';
        }

        if ($email === '') {
            $errors['email'] = 'Please fill out your email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Please fill out your password.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($passwordConfirmation === '') {
            $errors['password_confirmation'] = 'Please retype your password.';
        } elseif ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        if ($errors !== []) {
            $old = ['name' => $name, 'email' => $email];
            (new Engine())->render('register', compact('errors', 'old'));
            return;
        }

        if (User::findByEmail($email) !== null) {
            $errors['email'] = 'That email is already registered.';
            $old = ['name' => $name, 'email' => $email];
            (new Engine())->render('register', compact('errors', 'old'));
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $hash,
        ]);

        (new Response())->redirect(app_url('/login'));
        exit;
    }

    public function showLogin(Request $request): void
    {
        (new Engine())->render('login');
    }

    public function login(Request $request): void
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');

        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Please fill out your email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Please fill out your password.';
        }

        if ($errors !== []) {
            $old = ['email' => $email];
            (new Engine())->render('login', compact('errors', 'old'));
            return;
        }

        $user = User::findByEmail($email);

        if ($user === null || !password_verify($password, (string) $user->password_hash)) {
            $errors['email'] = 'Invalid email or password.';
            $old = ['email' => $email];
            (new Engine())->render('login', compact('errors', 'old'));
            return;
        }

        $_SESSION['user'] = [
            'id' => (int) $user->id,
            'email' => $user->email,
        ];
        
        (new Response())->redirect(app_url('/dashboard'));
        exit;
    }

    public function logout(Request $request): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();

        (new Response())->redirect(app_url('/login'));
        exit;
    }
}