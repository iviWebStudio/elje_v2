#!/bin/bash
set -eu

declare -p WORDPRESS_HOST
wait-for-it ${WORDPRESS_HOST} -t 120

if [ -f /var/www/html/.initialized ]
then
   echo "The environment has already been initialized."
   exit 0
fi

chown xfs:xfs /var/www/html/wp-content
chown xfs:xfs /var/www/html/wp-content/plugins

if [ $UID -eq 0 ]; then
  user=xfs
  dir=/var/www/html
  cd "$dir"
  exec su -s /bin/bash "$user" "$0" -- "$@"
fi

declare -p WORDPRESS_PORT
[[ "${WORDPRESS_PORT}" == 80 ]] && \
URL="http://localhost" || \
URL="http://localhost:${WORDPRESS_PORT}"

if $(wp core is-installed);
then
    echo "Wordpress is already installed..."
else
    declare -p WORDPRESS_TITLE >/dev/null
    declare -p WORDPRESS_LOGIN >/dev/null
    declare -p WORDPRESS_PASSWORD >/dev/null
    declare -p WORDPRESS_EMAIL >/dev/null
    echo "Installing wordpress..."
    wp core install \
        --url=${URL} \
        --title="$WORDPRESS_TITLE" \
        --admin_user=${WORDPRESS_LOGIN} \
        --admin_password=${WORDPRESS_PASSWORD} \
        --admin_email=${WORDPRESS_EMAIL} \
        --skip-email
fi

wp rewrite structure "/%postname%/" --hard
wp plugin install https://github.com/WP-API/Basic-Auth/archive/master.zip --activate
wp plugin install elje --activate
wp plugin activate elje
declare -p GUTENBERG_LATEST
if [ "${GUTENBERG_LATEST-}" == 'true' ]; then
    wp plugin install gutenberg --activate
fi

wp post create --post_type=page --post_status=publish --post_title='Ready' --post_content='E2E-tests.'

declare -r CURRENT_DOMAIN=$(wp option get siteurl)

if ! [[ ${CURRENT_DOMAIN} == ${URL} ]]; then
    echo "Replacing ${CURRENT_DOMAIN} with ${URL} in database..."
    wp search-replace ${CURRENT_DOMAIN} ${URL}
fi

echo "Visit $(wp option get siteurl)"
touch /var/www/html/.initialized
