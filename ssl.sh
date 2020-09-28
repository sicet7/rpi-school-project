#!/bin/sh

SCRIPT_PATH="`dirname \"$0\"`";
SCRIPT_PATH="`( cd \"$SCRIPT_PATH\" && pwd )`";
if [ -z "$SCRIPT_PATH" ]; then
    echo "Failed to acquire path information.";
    exit 1;
fi

SSL_KEY="$SCRIPT_PATH/ssl.key";
SSL_CRT="$SCRIPT_PATH/ssl.crt";
SSL_PEM="$SCRIPT_PATH/ssl.pem";

if [ -f "$SSL_KEY" ]; then
    rm "$SSL_KEY"
fi

if [ -f "$SSL_CRT" ]; then
    rm "$SSL_CRT"
fi

if [ -f "$SSL_PEM" ]; then
    rm "$SSL_PEM"
fi

openssl genrsa -out "$SSL_KEY" 4096 2>/dev/null
openssl req -x509 -new -nodes \
    -key "$SSL_KEY" \
    -sha256 \
    -days 1024 \
    -subj "/C=DK/ST=Fyn/L=Odense/O=SDE/OU=SDE/CN=localhost" \
    -out "$SSL_CRT"

openssl dhparam -out "$SSL_PEM" 2048