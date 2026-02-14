#!/bin/bash

awk 'NR>1 {print $3}' table.txt | awk '{print $1}' | sort | uniq -c | sort -nr | head -3
