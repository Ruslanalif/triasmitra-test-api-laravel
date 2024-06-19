# triasmitra-test-api-laravel
API Laravel - Sanctum Token - untuk CRUD data KTP 

API ini dibangun menggunakan laravel versi 9.52.16 dengan keamanan middleware sanctum. fitur didalam nya mencakup register user, user login, crud masterresident (+ fitur upoad gambar, di kompress maksimal 200kb), . saya sertakan postman collection untuk endpoint yang lebih jelas:



> Postman Collection json

```bash
https://github.com/Ruslanalif/triasmitra-test-api-laravel/blob/master/Triasmitra%20Laravel%20API%20Token.postman_collection.json
```
> Sql Dump database (table, data, view, store procedure)

```bash
https://github.com/Ruslanalif/triasmitra-test-api-laravel/blob/master/db_triasmitra_test.sql
```

> Menjalankan Service API

Jalankan php artisan dengen deklarasi host menggunakan IP pulic dan port yang tersedia
```bash
php artisan serve --host 192.168.43.32 --port 8001
```
