# HeavenlyMatch — Claude Working Guide

## Project Overview
HeavenlyMatch is a premium matrimonial platform built with Laravel 11, Inertia.js, React, TypeScript, Tailwind CSS, Vite, and MySQL.

## Main Goals
- Production-ready matrimonial platform for Bangladesh users.
- Clean biodata completion flow.
- Strong admin approval and moderation system.
- Mobile-first profile browsing UI.
- Bangla/English multilingual support.
- Safe shared-hosting deployment with cPanel Git deploy.

## Environment
- PHP 8.2+
- Laravel 11
- MySQL
- Node.js + npm
- Vite
- Windows local development preferred
- No Docker/WSL required unless explicitly asked

## Common Commands

### Local setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve