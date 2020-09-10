FROM postgres:12.4

#Config
COPY ./assets/postgres.conf /etc/postgresql/postgresql.conf

CMD ["postgres", "-c", "config_file=/etc/postgresql/postgresql.conf"]