npm run wp-env run cli -- wp plugin delete akismet hello

network_hash=$(basename "$(npm run wp-env install-path)")
mailpit_host="mailpit-${network_hash:0:8}"

update_env() {
    local key=$1
    local value=$2
    local env_file=".env"

    if [ ! -f "$env_file" ]; then
        echo "$key=$value" > "$env_file"
    else
        if grep -q "^$key=" "$env_file"; then
            sed -i '' -e "s/^$key=.*/$key=$value/" "$env_file"
        else
            echo "$key=$value" >> "$env_file"
        fi
    fi
}

update_env "WP_ENV_NETWORK_HASH" "${network_hash}"

start_port=50000
end_port=65535

if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    open_smtp_port=$(while :; do PORT=$(shuf -i $start_port-$end_port -n 1); RESULT=$(lsof -i :$PORT); if [ -z "$RESULT" ]; then echo $PORT; break; fi; done)
elif [[ "$OSTYPE" == "darwin"* ]]; then
    open_smtp_port=$(while :; do PORT=$(jot -r 1 $start_port $end_port); RESULT=$(lsof -i :$PORT); if [ -z "$RESULT" ]; then echo $PORT; break; fi; done)
else
    echo "Unsupported OS"
    exit 1
fi

update_env "WP_ENV_SMTP_PORT" "${open_smtp_port}"

docker compose up -d

adminer_port=$(
	docker ps --filter "name=${network_hash}_adminer" --format '{{.Ports}}' | awk -F: '/0.0.0.0/{split($2,a,"->"); print a[1]}'
)
mailpit_web_port=$(
	docker ps --filter name=mailpit --format '{{.Ports}}' | grep -oE '[0-9]+->8025/tcp' | grep -oE '^[0-9]+'
)

update_env "ADMINER_PORT" "${adminer_port}"
update_env "MAILPIT_SMTP_HOST" "${mailpit_host}"
update_env "MAILPIT_SMTP_PORT" "${open_smtp_port}"
update_env "MAILPIT_WEB_PORT" "${mailpit_web_port}"

npm run wp-env run cli -- wp option update cxn_adminer_port "${adminer_port}"
npm run wp-env run cli -- wp option update cxn_mailpit_smtp_port "${open_smtp_port}"
npm run wp-env run cli -- wp option update cxn_mailpit_web_port "${mailpit_web_port}"

npm run wp-env run cli -- wp config set WPMS_SMTP_HOST "${mailpit_host}" --type=constant
npm run wp-env run cli -- wp config set WPMS_SMTP_PORT "${open_smtp_port}" --raw --type=constant
