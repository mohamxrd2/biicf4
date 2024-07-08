@echo off
cd /d C:\wamp64\www\biicf4
C:\wamp64\bin\php\php8.2.0\php.exe artisan check:countdowns > NUL 2>&1
