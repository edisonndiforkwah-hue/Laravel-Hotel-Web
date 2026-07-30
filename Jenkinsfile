pipeline {
    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
        buildDiscarder(logRotator(
            numToKeepStr: '20',
            artifactNumToKeepStr: '10'
        ))
    }

    environment {
        COMPOSE_FILE = "compose.dev.yaml"
        COMPOSE_CI_FILE = "compose.ci.yaml"
        COMPOSE_PROJECT_NAME = "hotel-ci"
        WORKSPACE_SERVICE = "workspace"
        DB_SERVICE = "postgres"
    }

    stages {

        stage('Checkout Source') {
            steps {
                checkout scm
            }
        }

        stage('Verify Workspace') {
            steps {
                sh '''
                set -e

                echo "===== SYSTEM INFO ====="
                whoami
                pwd

                docker --version
                docker compose version

                echo ""
                echo "===== PROJECT FILES ====="
                ls -la

                if [ ! -f "${COMPOSE_FILE}" ]; then
                    echo "ERROR: ${COMPOSE_FILE} not found."
                    exit 1
                fi

                if [ ! -f "${COMPOSE_CI_FILE}" ]; then
                    echo "ERROR: ${COMPOSE_CI_FILE} not found."
                    exit 1
                fi
                '''
            }
        }

        stage('Prepare Environment') {
            steps {
                sh '''
                set -e

                if [ ! -f .env ]; then
                    echo "Creating .env..."
                    cp .env.example .env
                fi

                # Ensure database settings match the Docker Compose PostgreSQL service.
                sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
                sed -i 's/^DB_HOST=.*/DB_HOST=postgres/' .env
                sed -i 's/^DB_PORT=.*/DB_PORT=5432/' .env
                sed -i 's/^DB_DATABASE=.*/DB_DATABASE=app/' .env
                sed -i 's/^DB_USERNAME=.*/DB_USERNAME=laravel/' .env
                sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=secret/' .env
                '''
            }
        }

        stage('Build Images') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} build --no-cache
                '''
            }
        }

        stage('Start Containers') {
            steps {
                sh '''
                set -e

                # Remove leftover containers from previous runs (default and CI project names).
                docker compose -f ${COMPOSE_FILE} down --remove-orphans || true
                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} \
                    -p ${COMPOSE_PROJECT_NAME} down --remove-orphans || true

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} \
                    -p ${COMPOSE_PROJECT_NAME} up -d
                '''
            }
        }

        stage('Wait For PostgreSQL') {
            steps {
                sh '''
                set -e

                echo "Waiting for PostgreSQL..."

                for i in $(seq 1 60)
                do
                    if docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${DB_SERVICE} \
                        pg_isready -U laravel >/dev/null 2>&1
                    then
                        echo "PostgreSQL is ready."
                        exit 0
                    fi

                    sleep 2
                done

                echo "ERROR: PostgreSQL failed to start."
                exit 1
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    git config --global --add safe.directory /var/www

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                sh -c '
                    if [ ! -d vendor ]; then
                        composer install \
                            --no-interaction \
                            --prefer-dist \
                            --optimize-autoloader
                    else
                        echo "vendor already exists."
                    fi
                '

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                sh -c '
                    if [ ! -d node_modules ]; then
                        npm install
                    else
                        echo "node_modules already exists."
                    fi
                '
                '''
            }
        }

        stage('Laravel Setup') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan key:generate --force

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan storage:link || true
                '''
            }
        }

        stage('Build Frontend') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    npm run build
                '''
            }
        }

        stage('Database Migration') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan migrate --force
                '''
            }
        }

        stage('Optimize Laravel') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan optimize

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan config:cache

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan route:cache

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan view:cache
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh '''
                set -e

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} exec -T ${WORKSPACE_SERVICE} \
                    php artisan test
                '''
            }
        }

        stage('Smoke Test') {
            steps {
                sh '''
                set -e

                echo "Waiting for Nginx..."

                sleep 10

                docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} \
                    exec -T web curl -f http://127.0.0.1
                '''
            }
        }
    }

    post {

        success {
            echo "======================================"
            echo "Deployment completed successfully."
            echo "======================================"
        }

        failure {
            script {
                echo "======================================"
                echo "Deployment failed."
                echo "Container status:"
                echo "======================================"

                sh '''
                if [ -f "${COMPOSE_FILE}" ] && [ -f "${COMPOSE_CI_FILE}" ]; then

                    docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} ps || true

                    echo ""
                    echo "========== LAST 100 LOGS =========="

                    docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} -p ${COMPOSE_PROJECT_NAME} logs --tail=100 || true

                else

                    echo "Compose files not found."

                fi
                '''
            }
        }

        always {
            sh '''
            docker image prune -f
            '''
        }

        cleanup {
            sh '''
            docker compose -f ${COMPOSE_FILE} down --remove-orphans || true
            docker compose -f ${COMPOSE_FILE} -f ${COMPOSE_CI_FILE} \
                -p ${COMPOSE_PROJECT_NAME} down --remove-orphans || true
            '''

            cleanWs(
                deleteDirs: true,
                disableDeferredWipeout: true
            )
        }
    }
}