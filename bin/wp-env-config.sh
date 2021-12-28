#!/bin/bash

## set permalinks for easier wp-json
wp rewrite structure '/%postname%/'
wp rewrite flush
wp core version --extra
wp plugin list

exit $EXIT_CODE
