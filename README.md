# 🍱 Gyukaku System Monorepo

## 0. Overview

This document explains how to set up the development environment for the Gyukaku System project.
It uses a pnpm workspace monorepo, where:

- The backend (Laravel 12) provides APIs only.
- The frontend (React Router v7) handles routing and rendering.

✅ Supports MacOS only. If you are using Windows and encounter setup issues, please contact me.

---

## 1. Prerequisites

### 1-1. Install pnpm

pnpm is used as the main package manager for both frontend and backend (monorepo-level).

```bash
npm install -g pnpm@latest-10
pnpm -v
```

For more installation options, see the [pnpm installation guide](https://pnpm.io/installation).

---

### 1-2. Install PHP & Composer (for Laravel)

Ensure that PHP 8.2+ (preferably 8.4) and Composer 2.x are installed.

```bash
php -v
composer -V
```

---

## 1-3. Install Tailscale (for secure database connection)

Our database is hosted on a private network, so you must connect using [Tailscale](https://tailscale.com/download).

```bash
# macOS
brew install tailscale
sudo tailscale up
```

Once connected, you’ll see a private IP (e.g., 100.x.y.z).

> 📨 After installation, please share your Tailscale account email with me
> so I can add you to the project network and grant database access.

## 1-4. (Optional) Install Gitmoji

This step is optional but highly recommended, as well-formatted commits make your history easier to read and maintain. ✨

With Gitmoji, you can add emojis to your commit messages to visually represent the purpose of each change (e.g., ✨ feature, 🐛 fix, 📝 docs, 🎨 style).

### Installation

```bash
pnpm add -g gitmoji-cli
```

Verify installation:

```bash
gitmoji -v
```

### Usage

```bash
gitmoji -c
```

Then follow the prompts:

- Select the emoji that represents your commit
- Enter a short title (commit message)
- Optionally, add a longer description

## 1-5. Enable Husky (pre-commit hook)

Husky is used to automatically format and lint your code before each commit.

This ensures that only clean, validated code is pushed to the repository.

After cloning the project and installing dependencies, run the following command once:
```bash
chmod +x .husky/pre-commit
```
This will make the pre-commit hook executable.
Then, every time you run, Husky will:

1. Run Prettier & ESLint (frontend)
2. Run Laravel Pint (backend)
3. Block commits if there are any formatting or linting errors

✅ This guarantees consistent code style across all team members.

---

## 2. Monorepo Structure

```
gyukaku-system/
├─ frontend/           # React Router 7 (Framework Mode)
├─ backend/            # Laravel 12 (API only)
├─ package.json        # Root scripts (pnpm workspace)
├─ pnpm-workspace.yaml
└─ README.md
```

### 🧱 Root package.json

```json
{
  "name": "gyukaku_system_mono",
  "private": true,
  "scripts": {
    "dev": "pnpm -r --parallel dev",
    "dev:frontend": "pnpm --filter frontend dev",
    "dev:backend": "pnpm --filter backend dev",
    "build": "pnpm --filter frontend build"
  }
}
```

---

## 3. Install Dependencies

### Root

```bash
pnpm install
```

### Backend (Laravel)

```bash
cd backend
composer install
```

### Frontend (React Router)

```bash
cd frontend
pnpm install
```

---

## 4. Configure Environment Variables

.env file was shared separately.

---

## 5. Run the Project

### Option A — Run both

```bash
pnpm dev
```

- Frontend: http://localhost:3000
- Backend: http://127.0.0.1:8080

### Option B — Run individually

```bash
pnpm dev:backend
pnpm dev:frontend
```

---

## 6. Commit Convention

```bash
<emoji> <type>(<scope>): <description>
```

Example:

```bash
✨ feat(api): add user authentication endpoints
```

---

## 7. Project Notes

| Area            | Responsibility                                |
| --------------- | --------------------------------------------- |
| Frontend        | Handles all routing and UI rendering          |
| Backend         | Provides REST API responses only              |
| Database        | PostgreSQL (local Docker or Tailscale remote) |
| Package Manager | pnpm (workspace shared)                       |
