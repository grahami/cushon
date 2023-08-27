FROM mysql/mysql-server:5.7

USER root

run mkdir -p /var/lib/mysql && chown -R mysql /var/lib/mysql && chgrp -R mysql /var/lib/mysql
run mkdir -p /run/mysqld && chmod 777 /run/mysqld

COPY my.cnf /etc/my.cnf
COPY initdb/* /docker-entrypoint-initdb.d/

ARG MYSQL_ROOT_PASSWORD='T3st1ng'
ENV MYSQL_ROOT_PASSWORD='T3st1ng'