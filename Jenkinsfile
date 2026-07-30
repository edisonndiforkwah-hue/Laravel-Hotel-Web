pipeline {
    agent any

    options {
        timestamps()
        ansiColor('xterm')
        disableConcurrentBuilds()
        buildDiscarder(logRotator(
            numToKeepStr: '20',
            artifactNumToKeepStr: '10'
        ))
    }

    environment {
        COMPOSE_FILE = "compose.dev.yaml"
        WORKSPACE_SERVICE = "workspace"
        APP_URL = "http://localhost"
    }

    stages {

        stage('Checkout Source') {
            steps {
                checkout scm
            }
        }

        stage('Verify Environment') {
            steps {
                sh '''
                echo "========== SYSTEM INFO =========="
                whoami
                pwd

                docker --version
                docker compose version

                echo "========== WORKSPACE =========="
                ls -la
                '''
            }
        }

        stage('Build Docker Images') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} build --no-cache
                '''
            }
        }

        stage('Start Containers') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} up -d
                '''
            }
        }

        stage('Wait for Services') {
            steps {
                sh '''
                echo "Waiting for PostgreSQL..."

                for i in $(seq 1 30); do
                    docker compose -f ${COMPOSE_FILE} exec -T postgres pg_isready && exit 0
                    sleep 2
                done

                echo "Database did not become ready."
                exit 1
                '''
            }
        }

        stage('Prepare Laravel') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    git config --global --add safe.directory /var/www

                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    composer install --no-interaction --prefer-dist --optimize-autoloader

                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    cp .env.example .env || true

                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    php artisan key:generate --force
                '''
            }
        }

        stage('Build Frontend') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} npm install
                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} npm run build
                '''
            }
        }

        stage('Database Migration') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    php artisan migrate --force
                '''
            }
        }

        stage('Optimize Laravel') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    php artisan config:cache

                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    php artisan route:cache

                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    php artisan view:cache
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh '''
                docker compose -f ${COMPOSE_FILE} exec -T ${WORKSPACE_SERVICE} \
                    php artisan test
                '''
            }
        }

        stage('Smoke Test') {
            steps {
                sh '''
                sleep 10

                curl -f ${APP_URL} || exit 1
                '''
            }
        }

        stage('Cleanup') {
            steps {
                sh '''
                docker image prune -f
                '''
            }
        }
    }

    post {

        success {
            echo "====================================="
            echo "Deployment completed successfully."
            echo "====================================="
        }

        failure {
            echo "====================================="
            echo "Deployment failed."
            echo "Displaying container logs..."
            echo "====================================="

            sh '''
            docker compose -f ${COMPOSE_FILE} ps || true

            docker compose -f ${COMPOSE_FILE} logs --tail=100 || true
            '''
        }

        always {
            cleanWs()
        }
    }
}