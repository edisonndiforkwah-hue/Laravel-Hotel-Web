pipeline {
    agent any

    environment {
        // Define your SSH deployment credentials ID configured in Jenkins
        SSH_CREDENTIALS_ID = 'laravel-server-ssh-key'
        DEPLOY_USER        = 'ubuntu'
        DEPLOY_SERVER      = '192.168.1.50'
        DEPLOY_PATH        = '/var/www/my-laravel-app'
    }

    stages {
        stage('Checkout') {
            steps {
                // Pulls code from your Git repository automatically
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                echo 'Installing Composer and NPM dependencies...'
                // Installs production-optimized PHP and Node dependencies
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                sh 'npm install && npm run build'
            }
        }

        stage('Environment Setup') {
            steps {
                echo 'Setting up environment file...'
                // Creates a local .env for running automated tests
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
            }
        }

        stage('Run Tests') {
            steps {
                echo 'Running PHPUnit tests...'
                // Executes application test suites
                sh 'vendor/bin/phpunit'
            }
        }

        stage('Deploy to Server') {
            steps {
                echo 'Deploying to production environment...'
                // Uses the SSH Agent plugin to securely transfer files and run post-deploy commands
                sshagent([env.SSH_CREDENTIALS_ID]) {
                    // Rsync application files, excluding development directories
                    sh "rsync -avz --exclude='.git' --exclude='node_modules' ./ ${env.DEPLOY_USER}@${env.DEPLOY_SERVER}:${env.DEPLOY_PATH}"
                    
                    // Execute Laravel deployment commands on the remote server
                    sh """
                        ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_SERVER} '
                            cd ${env.DEPLOY_PATH} && \
                            composer install --no-interaction --prefer-dist --optimize-autoloader && \
                            php artisan migrate --force && \
                            php artisan config:cache && \
                            php artisan route:cache && \
                            php artisan view:cache
                        '
                    """
                }
            }
        }
    }

    post {
        success {
            echo 'Pipeline successfully completed!'
        }
        failure {
            echo 'Pipeline failed. Please review the build logs.'
        }
    }
}
