#!/bin/bash
COUNT=$(./unprocessed.sh)
if [ $COUNT -gt 0 ]; then
gearman -c $COUNT -n -w -f smtt_register_mo > /dev/null
fi