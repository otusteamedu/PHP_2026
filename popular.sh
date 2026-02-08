#!/bin/bash

tail -n +2 "$1" | sort -k3 | uniq -c | sort -r -k1 | awk '{print $4}' | head -3
