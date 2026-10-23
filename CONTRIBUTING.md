# Contributing to Online Food Ordering Web App

Thank you for your interest in contributing! 🚀

## 📌 How to Contribute

1. **Clone the repository** to your local machine (no need to fork if you’re part of the org).

2. Make sure you’re up to date with the latest changes:

   ```bash
   git pull origin dev
   ```

3. Create a **new branch** for your feature or bugfix:

   ```bash
   git checkout -b feature/task-123-short-description-frontend
   git checkout -b feature/task-123-short-description-backend
   ```

   * Replace `123` with your task ID (from GitHub Project Board/Issue).
   * Use `frontend` or `backend` depending on the scope of your work.
   * If the branch already exists on remote but not locally, you can check it out directly:

     ```bash
     git checkout branch-name
     ```

     (Git will automatically track `origin/branch-name`.)

4. Make your changes and **commit** with a clear message:

   ```bash
   git commit -m "Add: short description of changes"
   ```

5. **Push** your branch to GitHub:

   ```bash
   git push origin feature/task-123-short-description-frontend
   ```

6. Open a **Pull Request (PR)** into the `dev` branch and describe your changes.

## ✅ Guidelines

* Follow the project’s coding style.
* Keep commits small and meaningful.
* Update documentation if needed.
* Test your changes before submitting.

## 🛠 Branch Naming Convention

* `feature/task-123-short-description-frontend` → New frontend features
* `feature/task-123-short-description-backend` → New backend features
* `fix/task-123-short-description` → Bug fixes

## 💬 Communication

* Use the dedicated **#project-queries** channel in Discord for project-related questions.
* Be respectful and collaborative.

---

## 🔀 Workflow Overview

```text
 main  ←  production-ready branch
   ↑
   └── dev  ←  integration branch for features & fixes
         ↑
         └── feature/task-123-short-description-frontend
         └── feature/task-123-short-description-backend
         └── fix/task-123-short-description
```

---

Happy coding! 💻
