#!/bin/sh

CWD=$(pwd)

mysql -uroot -pahzpizi -e "drop database training_crm_application_test; create database training_crm_application_test;"
sed -i -e 's/\(installed: \)\(.*\)/\1false/' ${CWD}/app/config/parameters_test.yml
rm -rf ${CWD}/app/cache/test
${CWD}/app/console oro:install --env=test --company-short-name Oro --company-name OroCRM --user-name admin --user-email test@example.com \
                              --user-firstname John --user-lastname Doe --user-password admin123 --sample-data=n
${CWD}/app/console doctrine:fixture:load --no-debug --append --no-interaction --env=test --fixtures vendor/oro/platform/src/Oro/Bundle/TestFrameworkBundle/Fixtures
${CWD}/app/console oro:test:schema:update --env=test
