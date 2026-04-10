# Gallery Web App (PHP + PostgreSQL/Oracle)

Bu proje galerici senaryosu icin hazir MVP sistemidir.

## Ozellikler

- Guest: arac listeleme + detay
- User: kayit/giris, favori ekleme, iletisim talebi olusturma
- Admin: arac ekleme, satildi isaretleme, satilanlari gorme, talepleri yonetme
- Satilan araclar guest/user listesinde gorunmez

## Kurulum (PostgreSQL Onerilen)

1. PostgreSQL kurulu olsun.
2. Bir veritabani olustur (ornek: `gallery_db`).
3. Migration calistir (onerilen):
   - `C:\xampp\php\php.exe scripts\migrate.php`
   - veya PATH varsa: `php scripts/migrate.php`
4. `app/config/config.php` icinde DB ayarlarini duzenle veya env degiskenlerini ayarla:
   - DB_DRIVER=pgsql
   - PG_HOST=127.0.0.1
   - PG_PORT=5432
   - PG_DATABASE=gallery_db
   - PG_USER=gallery_user
   - PG_PASSWORD=gallery_pass
5. XAMPP Apache ile projeyi `http://localhost/gallery` adresinden ac.

### Migration Nedir? (Kisa)

- Migration, SQL dosyalarinin versiyonlu hali demektir.
- Bu projede migration dosyalari: `database/postgresql/migrations/*.up.sql`
- Runner: `scripts/migrate.php`
- Runner, `schema_migrations` tablosunda hangi dosyanin calistigini tutar.
- Ayni migration tekrar calismaz; yeni dosya ekledikce sadece yenileri uygulanir.

### Manuel SQL Isteyenler Icin

- Tek seferlik script hala var: `database/postgresql/001_init.sql`
- Ama migration disiplini icin onerilen yol her zaman `scripts/migrate.php`.

### PostgreSQL Baglanti Formati (DSN)

Bu projede "link" mantigi su formatta kullanilir:

`pgsql:host=127.0.0.1;port=5432;dbname=gallery_db`

Bu deger kodda otomatik olusur. Sen sadece host/port/db/user/password girersin.

## Oracle ile Devam Etmek Istersen

1. DB_DRIVER=oracle yap.
2. `database/schema.sql` dosyasini calistir.
3. ORACLE_USER, ORACLE_PASSWORD, ORACLE_DSN degerlerini ayarla.

## Admin Hesabi Olusturma

1. Uygulama uzerinden normal kullanici kaydi yap.
2. Veritabaninda su komutu calistir:

UPDATE users SET role = 'ADMIN' WHERE LOWER(email) = LOWER('senin-emailin@example.com');
COMMIT;

## API Ozet

- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout
- GET /api/auth/me
- GET /api/cars
- GET /api/cars/{id}
- GET /api/favorites
- POST /api/favorites/{carId}
- DELETE /api/favorites/{carId}
- POST /api/inquiries
- GET /api/admin/cars
- POST /api/admin/cars
- POST /api/admin/cars/{id}/mark-sold
- GET /api/admin/inquiries
- PUT /api/admin/inquiries/{id}

## Frontend Rotalari

- / veya /showroom : Ana vitrin
- /access : Giris / kayit sayfasi
- /vehicle/{id} : Arac detay sayfasi
- /admin : Admin paneli (sadece ADMIN rolu)
- /playground : Swagger benzeri API test sayfasi (CRUD endpoint test)

## Notlar

- Dosya yukleme bu MVP'de endpoint seviyesinde yok. Simdilik `car_images` tablosuna URL veya path ekleyebilirsiniz.
- Uretim icin CSRF, rate limit, upload guvenligi ve audit log eklenmelidir.
