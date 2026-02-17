#!/usr/bin/env bash

awk 'NR>1 {print $3}' "$1" | sort | uniq -c | sort -rn | head -3 | awk '{print $2}'