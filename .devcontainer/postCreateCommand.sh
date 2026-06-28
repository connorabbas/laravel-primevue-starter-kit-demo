#!/usr/bin/env bash

set -e

curl -fsSL https://opencode.ai/install | bash
PATH="/home/www-data/.opencode/bin:$PATH"

sudo chown -R 1000:1000 /var/www/html || true
