# 🚀 Laravel CI/CD Pipeline with GitHub Actions & AWS EC2

![GitHub Actions](https://img.shields.io/badge/GitHub%20Actions-CI%2FCD-blue?logo=githubactions)
![AWS EC2](https://img.shields.io/badge/AWS-EC2-orange?logo=amazonaws)
![Laravel](https://img.shields.io/badge/Laravel-PHP-red?logo=laravel)
![Nginx](https://img.shields.io/badge/Nginx-Web%20Server-green?logo=nginx)
![Linux](https://img.shields.io/badge/Linux-Ubuntu-orange?logo=ubuntu)

## 📌 Project Overview

This project demonstrates the implementation of a **CI/CD pipeline for a Laravel web application** using **GitHub Actions** and **AWS EC2**.

The pipeline automates the process of deploying application changes from a GitHub repository to a production server hosted on AWS EC2.

Instead of manually connecting to the server and updating the application after every change, the CI/CD pipeline automatically performs the deployment whenever new changes are pushed to the configured branch.

---

## 🏗️ Architecture

```text
                   Developer
                       │
                       │ git push
                       ▼
                ┌─────────────┐
                │   GitHub    │
                │ Repository  │
                └──────┬──────┘
                       │
                       ▼
              ┌─────────────────┐
              │ GitHub Actions   │
              │   CI/CD Pipeline │
              └────────┬────────┘
                       │
                 SSH Deployment
                       │
                       ▼
              ┌─────────────────┐
              │    AWS EC2      │
              │ Ubuntu Server   │
              └────────┬────────┘
                       │
              ┌────────▼────────┐
              │      Nginx      │
              │  Web Server     │
              └────────┬────────┘
                       │
              ┌────────▼────────┐
              │ Laravel App     │
              │ PHP / Composer  │
              └────────┬────────┘
                       │
                       ▼
                  MySQL Database
```

---

## 🛠️ Technologies Used

| Technology         | Purpose                           |
| ------------------ | --------------------------------- |
| **Laravel**        | Backend web application framework |
| **PHP**            | Application runtime               |
| **MySQL**          | Relational database               |
| **Git**            | Version control                   |
| **GitHub**         | Source code repository            |
| **GitHub Actions** | CI/CD automation                  |
| **AWS EC2**        | Cloud hosting                     |
| **Ubuntu Linux**   | Server operating system           |
| **Nginx**          | Web server / reverse proxy        |
| **Composer**       | PHP dependency management         |
| **npm**            | Frontend dependency management    |
| **SSH**            | Secure server deployment          |

---

# ⚙️ CI/CD Pipeline

The deployment pipeline follows this workflow:

```text
1. Developer makes changes
           ↓
2. Changes are committed
           ↓
3. Changes are pushed to GitHub
           ↓
4. GitHub Actions workflow starts
           ↓
5. Application dependencies are installed
           ↓
6. Application is deployed through SSH
           ↓
7. Laravel application is updated on EC2
           ↓
8. Application is available on the server
```

---

# 🔄 GitHub Actions Workflow

The workflow is configured inside:

```text
.github/
└── workflows/
    └── deploy.yml
```

The workflow is triggered when changes are pushed to the configured branch.

Example:

```yaml
name: Deploy Laravel Application

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout Repository
        uses: actions/checkout@v4

      - name: Deploy to AWS EC2
        uses: appleboy/ssh-action@v1.2.0
        with:
          host: ${{ secrets.SERVER_IP }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/html/project_dir
            git pull origin main
            composer install --no-interaction --prefer-dist --optimize-autoloader
            php artisan migrate --force
            php artisan config:clear
            php artisan cache:clear
```

> **Note:** The exact workflow commands may differ depending on your Laravel application and server configuration.

---

# 🔐 GitHub Secrets

Sensitive server credentials are stored using **GitHub Actions Secrets** rather than hard-coding them inside the workflow.

The pipeline uses secrets such as:

```text
SERVER_IP
SSH_USER
SSH_KEY
```

### Example

```yaml
host: ${{ secrets.SERVER_IP }}
username: ${{ secrets.SSH_USER }}
key: ${{ secrets.SSH_KEY }}
```

This prevents sensitive server credentials from being exposed in the repository.

---

# ☁️ AWS EC2 Configuration

The application is hosted on an **AWS EC2 Ubuntu server**.

The EC2 server is configured with:

* Ubuntu Linux
* Nginx
* PHP
* PHP-FPM
* Composer
* Git
* MySQL
* Laravel
* SSH access

The Laravel project is located on the server under:

```text
/var/www/html/project_dir
```

Nginx is configured to serve the Laravel application's `public` directory.

---

# 🌐 Deployment Process

When a developer pushes new code:

```bash
git add .
git commit -m "Update application"
git push origin main
```

GitHub automatically triggers the workflow.

GitHub Actions then connects to the EC2 server using SSH and executes the deployment commands.

The server retrieves the latest version of the application and performs the required Laravel deployment tasks.

---

# 🧪 Testing the Deployment

After deployment, the application can be tested by accessing the EC2 public IP address or configured domain:

```text
http://YOUR_SERVER_IP
```

You can also verify the GitHub Actions workflow from:

```text
GitHub → Repository → Actions
```

A successful workflow should show a green checkmark.

---

# 📊 Pipeline Benefits

This CI/CD implementation provides several benefits:

* ⚡ Faster deployments
* 🤖 Automated deployment process
* 🔐 Secure SSH authentication
* 📦 Consistent application updates
* 🔄 Reduced manual server administration
* 🧪 Automated deployment verification
* ☁️ Cloud-based hosting with AWS
* 📈 Foundation for future DevOps automation

---

# 🔒 Security Considerations

The project follows several basic security practices:

* SSH keys are stored as GitHub Secrets.
* Server credentials are not committed to Git.
* `.env` files are excluded from version control.
* Sensitive configuration values are managed on the server.
* Deployment is performed through SSH.
* Database credentials are not stored directly in the workflow.

### Never commit your `.env` file

Make sure your `.gitignore` contains:

```text
.env
/vendor/
/node_modules/
```

---

# 📁 Project Structure

```text
.
├── .github/
│   └── workflows/
│       └── deploy.yml
│
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
│
├── artisan
├── composer.json
├── package.json
└── README.md
```

---

# 🚀 Future Improvements

The pipeline can be extended with additional DevOps practices, including:

* [ ] Automated unit and feature tests
* [ ] Laravel code quality checks
* [ ] PHP static analysis
* [ ] Docker containerization
* [ ] Docker Compose deployment
* [ ] AWS RDS database
* [ ] AWS S3 storage
* [ ] HTTPS with SSL/TLS
* [ ] Domain name configuration
* [ ] Monitoring and logging
* [ ] Rollback strategy
* [ ] Blue/green deployments
* [ ] Infrastructure as Code using Terraform
* [ ] Deployment notifications
* [ ] Staging and production environments

---

# 🎯 Learning Objectives

This project was created to gain practical experience with:

* CI/CD concepts
* GitHub Actions
* AWS EC2
* Linux server administration
* SSH authentication
* Nginx configuration
* Laravel production deployment
* Database configuration
* Git version control
* Cloud deployment
* DevOps automation

---

# 👨‍💻 Author

**Ndiforkwah Edison**

DevOps Engineer | Web Developer

This project demonstrates my practical experience in **Laravel development, cloud deployment, Linux administration, AWS, GitHub Actions, and CI/CD automation**.

---

# ⭐ Project

If you find this project useful or interesting, consider giving the repository a ⭐ on GitHub.

**Built with Laravel, AWS EC2, GitHub Actions, and a passion for DevOps.**
