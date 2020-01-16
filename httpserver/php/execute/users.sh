#!/bin/bash

echo 'haha'

if [ -f /root/.my.cnf ]; then
    mysql -e "SELECT User FROM mysql.user"
else
    mysql -e "SELECT User FROM mysql.user"
fi