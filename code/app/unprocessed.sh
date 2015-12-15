#!/bin/bash
echo 'status' | nc 127.0.0.1 4730 | grep smtt_register_mo | awk -F $'\t' '{print $2}'
