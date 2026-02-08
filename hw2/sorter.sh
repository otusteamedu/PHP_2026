#!/usr/bin/bash

awk 'NR > 1 {print $3}' some-file.txt | sort | uniq -c | sort -nr | head -3