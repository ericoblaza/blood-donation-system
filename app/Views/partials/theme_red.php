<?php

declare(strict_types=1);
?>
<style>
    :root {
        --ink: #111;
        --muted: #555;
        --text: #111;
        --border: #ccc;
        --surface: #fff;
        --surface-muted: #f5f5f5;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 24px;
        font-family: Arial, Helvetica, sans-serif;
        color: var(--text);
        background: var(--surface);
        line-height: 1.45;
    }

    h1, h2, h3 {
        color: var(--ink);
        margin-top: 0;
    }

    a {
        color: var(--ink);
    }

    form {
        margin: 0;
    }

    input, select, textarea, button {
        font: inherit;
        padding: 6px 8px;
        border: 1px solid var(--border);
        border-radius: 6px;
    }

    input:focus, select:focus, textarea:focus {
        outline: 2px solid #ddd;
        border-color: #999;
    }

    button {
        background: var(--surface);
        color: var(--ink);
        border-color: var(--border);
        cursor: pointer;
    }

    button.btn-plain,
    a.btn-plain {
        display: inline-block;
        background: var(--surface);
        color: var(--ink);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 6px 8px;
        text-decoration: none;
        cursor: pointer;
    }

    .actions-cell {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .actions-cell form {
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: var(--surface);
    }

    th, td {
        border: 1px solid var(--border);
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    th {
        background: var(--surface-muted);
        color: var(--ink);
    }

    .error {
        color: var(--ink);
        font-size: 14px;
        margin-left: 8px;
    }

    .auth-center {
        min-height: calc(100vh - 48px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-card {
        width: min(92vw, 460px);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
    }

    .auth-card h1 {
        margin-bottom: 14px;
    }

    .auth-field {
        margin-bottom: 12px;
    }

    .auth-field input {
        width: 100%;
    }

    .auth-submit {
        width: 100%;
        margin-top: 8px;
    }
</style>
